<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    public $timestamps = false;
    protected $table = "degree";
    use HasFactory;
}
