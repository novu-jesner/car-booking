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
    
 public static function hasConflict($driver_id, $car_id, $from_date, $to_date, $excludeId = null)
    {
        $query = self::where('status', 'approved')
            ->where(function($q) use ($driver_id, $car_id) {
                $q->where('driver_id', $driver_id)
                  ->orWhere('car_id', $car_id);
            })
            ->where(function($q) use ($from_date, $to_date) {
                $q->whereBetween('from_date', [$from_date, $to_date])
                  ->orWhereBetween('to_date', [$from_date, $to_date]);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
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
