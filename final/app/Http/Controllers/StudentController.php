<?php

namespace App\Http\Controllers;

use App\Interfaces\StudentRepositoryInterface;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private StudentRepositoryInterface $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->studentRepository->all());
        }
        return view('students.index');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'date' => 'required|date',
            'standard' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'addresses.*.street' => 'required|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.state' => 'required|string|max:255',
            'addresses.*.country' => 'required|string|max:255',
            'addresses.*.postal_code' => 'required|string|max:20',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        return response()->json($this->studentRepository->create($data));
    }

    public function show($id)
    {
        $student = $this->studentRepository->find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        // Debugging received data
        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // die;

        // Validate the incoming request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'date' => 'required|date',
            'standard' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'addresses.*.street' => 'required|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.state' => 'required|string|max:255',
            'addresses.*.country' => 'required|string|max:255',
            'addresses.*.postal_code' => 'required|string|max:20',
        ]);

        // If an image file is present in the request, handle it

        // Pass the validated data to the repository
        return response()->json($this->studentRepository->update($id, $data));
    }




    public function destroy($id)
    {
        $result = $this->studentRepository->delete($id);
        if ($result) {
            return response()->json(['message' => 'Student deleted successfully']);
        }
        return response()->json(['message' => 'Student not found'], 404);
    }
}
