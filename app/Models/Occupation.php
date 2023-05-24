<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    public $timestamps = false;
    protected $table = 'occupation';
    use HasFactory;
}
