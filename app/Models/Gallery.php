<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    // function getNameAttribute($value){
    //     return "http://127.0.0.1:8000/upload/images/".$value;
    // }
}
