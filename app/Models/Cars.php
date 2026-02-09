<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;

    protected  $fillable = [
        'name',
        'image',
        'license_plate',
        'brand',
        'seater',
        'type',
        'is_available',
        'remarks',
    ];
}
