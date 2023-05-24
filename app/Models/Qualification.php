<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    public $timestamps = false;
    protected $table = 'qualification';
    use HasFactory;
}
