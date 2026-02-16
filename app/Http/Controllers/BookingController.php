<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\sendToAdminMail;
use App\Models\Booking;
use App\Models\User;
use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $startDate = \Carbon\Carbon::create($year, $month, 1)
            ->subMonths(13)
            ->startOfMonth();

        $endDate = \Carbon\Carbon::create($year, $month, 1)
            ->addMonths(13)
            ->endOfMonth();

        $booking_schedule = DB::table('bookings')
            ->where('status', 'approved')
            ->whereBetween('from_date', [$startDate, $endDate])
            ->get();

        $schedules = [];

        foreach ($booking_schedule as $sched) {
            $schedules[] = [
                'id'          => $sched->id,
                'title'       => $sched->title,
                'start'       => $sched->from_date,
                'end'         => $sched->to_date,
                'description' => $sched->purpose,
            ];
        }

        if ($request->ajax()) {
            return response([
                'data'   => $schedules,
                'status' => 'success'
            ], 200);
        }

        return view('booking.index');
    }

    public function store(CreateBookingRequest $request)
    {
        $data = $request->validated();

        // 🔒 DRIVER AVAILABILITY VALIDATION
        $driverConflict = Booking::where('driver_id', $data['driver_id'])
            ->where('status', 'approved')
            ->where(function ($query) use ($data) {
                $query->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                      ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                      ->orWhere(function ($q) use ($data) {
                          $q->where('from_date', '<=', $data['from_date'])
                            ->where('to_date', '>=', $data['to_date']);
                      });
            })
            ->exists();

        if ($driverConflict) {
            return response([
                'message' => 'Selected driver is already booked for this date range.',
                'status'  => 'error'
            ], 422);
        }

        // 🔒 CAR AVAILABILITY VALIDATION
        $carConflict = Booking::where('car_id', $data['car_id'])
            ->where('status', 'approved')
            ->where(function ($query) use ($data) {
                $query->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                      ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                      ->orWhere(function ($q) use ($data) {
                          $q->where('from_date', '<=', $data['from_date'])
                            ->where('to_date', '>=', $data['to_date']);
                      });
            })
            ->exists();

        if ($carConflict) {
            return response([
                'message' => 'Selected car is already booked for this date range.',
                'status'  => 'error'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'title'       => $data['title'],
                'user_id'     => Auth::id(),
                'purpose'     => $data['purpose'],
                'from_date'   => $data['from_date'],
                'to_date'     => $data['to_date'],
                'destination' => $data['destination'],
                'driver_id'   => $data['driver_id'],
                'car_id'      => $data['car_id'],
                'odo_meter'   => '',
                'remarks'     => $data['remarks'],
            ]);

            $adminEmails    = User::role('admin')->pluck('email')->toArray();
            $driverEmail    = optional($booking->driver)->email;
            $requesterEmail = $booking->user->email;

            $recipients = collect($adminEmails)
                ->merge([$driverEmail, $requesterEmail])
                ->filter()
                ->unique()
                ->values()
                ->all();

            DB::commit();

            Mail::to($recipients)->send(
                new sendToAdminMail($booking, 'Booking Request')
            );

            return response([
                'data'   => $booking,
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking Store Error: ' . $e->getMessage());

            return response([
                'message' => $e->getMessage(),
                'status'  => 'store failed'
            ], 500);
        }
    }

public function availableDrivers(Request $request)
{
    $from = $request->from_date;
    $to   = $request->to_date;

    if (!$from || !$to) return response()->json([]);

 
    $allDrivers = User::role('driver')->get(['id', 'name']);

   
    $bookedDriverIds = Booking::where('status', 'approved')
        ->where(function ($query) use ($from, $to) {
            $query->whereBetween('from_date', [$from, $to])
                  ->orWhereBetween('to_date', [$from, $to])
                  ->orWhere(function ($q) use ($from, $to) {
                      $q->where('from_date', '<=', $from)
                        ->where('to_date', '>=', $to);
                  });
        })
        ->pluck('driver_id')
        ->toArray();

   
    $drivers = $allDrivers->map(function($driver) use ($bookedDriverIds) {
        return [
            'id'        => $driver->id,
            'name'      => $driver->name,
            'available' => !in_array($driver->id, $bookedDriverIds),
        ];
    });

    return response()->json($drivers);
}


public function availableCars(Request $request)
{
    $from = $request->query('from_date');
    $to   = $request->query('to_date');

    if (!$from || !$to) {
        return response()->json([
            'message' => 'Please provide from_date and to_date',
            'cars_with_availability' => []
        ]);
    }

    
    $bookedCarIds = Booking::where('status', 'approved')
        ->where(function ($query) use ($from, $to) {
            $query->where(function ($q) use ($from, $to) {
                $q->where('from_date', '<=', $to)
                  ->where('to_date', '>=', $from);
            });
        })
        ->pluck('car_id')
        ->toArray();

   
    $carsWithAvailability = Cars::all()->map(function($car) use ($bookedCarIds) {
        return [
            'id' => $car->id,
            'name' => $car->name,
            'brand' => $car->brand,
            'seater' => $car->seater,
            'license_plate' => $car->license_plate,
            'available' => !in_array($car->id, $bookedCarIds)
        ];
    });

    // 3️⃣ Return as JSON
    return response()->json([
        'from_date' => $from,
        'to_date' => $to,
        'booked_car_ids' => $bookedCarIds,
        'cars_with_availability' => $carsWithAvailability
    ]);
}



}
