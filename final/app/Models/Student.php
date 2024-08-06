<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;


    protected $fillable = ['name', 'mobile', 'date', 'standard', 'image_path'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
