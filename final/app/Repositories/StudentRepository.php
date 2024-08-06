<?php

namespace App\Repositories;

use App\Interfaces\StudentRepositoryInterface;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentRepository implements StudentRepositoryInterface
{
    public function all()
    {
        return Student::with('addresses')->get();
    }

    public function find($id)
    {
        return Student::with('addresses')->find($id);
    }

    public function create(array $data)
    {

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['image']->store('public/images');
            if ($imagePath) {
                $data['image_path'] = str_replace('public/', '', $imagePath);
            } else {
                throw new \Exception('Image upload failed');
            }
        }

        // Create student record
        $student = Student::create($data);

        // Handle addresses if present
        if (isset($data['addresses'])) {
            $student->addresses()->createMany($data['addresses']);
        }

        return $student;
    }

    public function update($id, array $data)
    {
        // Find the student or fail
        $student = Student::findOrFail($id);

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image file if it exists
            if ($student->image_path) {
                Storage::delete('public/images/' . $student->image_path);
            }

            // Store new image file
            $imagePath = $data['image']->store('images', 'public'); // Store in 'public/images' directory
            if ($imagePath) {
                $data['image_path'] = str_replace('public/', '', $imagePath);
            } else {
                throw new \Exception('Image upload failed');
            }
        } else {
            // Keep the existing image path if no new image is provided
            $data['image_path'] = $student->image_path;
        }

        // Update student record
        $student->update($data);

        // Handle addresses if present
        if (isset($data['addresses'])) {
            // Delete old addresses
            $student->addresses()->delete();

            // Create new addresses if provided
            $student->addresses()->createMany($data['addresses']);
        }

        return $student;
    }

    public function delete($id)
    {
        $student = Student::findOrFail($id);

        // Delete related addresses
        $student->addresses()->delete();

        // Delete student record
        $student->delete();

        return $student;
    }
}
