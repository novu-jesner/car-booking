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
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;


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


public function store(Request $request)
{
    $data = $request->validate([
        'title'       => 'required|string|max:255',
        'purpose'     => 'required|string',
        'destination' => 'required|string',
        'driver_id'   => 'required|exists:users,id',
        'car_id'      => 'required|exists:cars,id',
        'remarks'     => 'nullable|string|max:255',
        'from_date'   => 'required|date|after_or_equal:' . now()->addDays(2)->toDateString(),
        'to_date'     => 'required|date|after:from_date',
    ]);

 
    if (Booking::hasConflict(
        $data['driver_id'],
        $data['car_id'],
        $data['from_date'],
        $data['to_date']
    )) {
        return response()->json([
            'status' => 'error',
            'errors' => [
                'driver_id' => ['May nauna na yah']
            ]
        ], 422);
    }

    $booking = Booking::create([
        ...$data,
        'status' => 'pending' // Automatically pending
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Salamat sa pag Book',
        'data' => $booking
    ], 200);
}
}