<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class VehicleReport extends Model
{
    use HasFactory;
    protected $table = 'vehicle_report';
    protected $fillable = [
        'car_id',
        'user_id',
        'description',
        'type',
    ];

    public function car()
    {
        return $this->belongsTo(Cars::class , 'car_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

  public function booking()
    {
        return $this->belongsTo(Booking::class , 'booking_id');
    }
}
