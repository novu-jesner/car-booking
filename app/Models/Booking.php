<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'purpose',
        'img_attachment',
        'user_id',
        'from_date',
        'to_date',
        'destination',
        'driver_id',
        'car_id',
        'odo_meter',
        'status',
        'remarks'
    ];

    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
