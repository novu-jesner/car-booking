<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\sendToAdminMail;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function index(Request $request)
    {   
        // Get the year and month from the request, defaulting to current year and month
        $year = $request->input('year', now()->year); 
        $month = $request->input('month', now()->month); 
    
        Log::info('Year: ' . $year);
        Log::info('Month: ' . $month);
    
        // Calculate the start and end date for the 12 months before and 12 months after
        $startDate = \Carbon\Carbon::create($year, $month, 1)->subMonths(13)->startOfMonth();
        $endDate = \Carbon\Carbon::create($year, $month, 1)->addMonths(13)->endOfMonth();
    
        Log::info('Start Date: ' . $startDate);
        Log::info('End Date: ' . $endDate);
    
        // Get bookings where the from_date is within the 12 months before and after
        $booking_schedule = DB::table('bookings')
            ->where('status', 'approved')
            ->whereBetween('from_date', [$startDate, $endDate])
            ->get();
    
        $schedules = [];
    
        foreach ($booking_schedule as $sched)
        {
            $schedules[] = [
                'id' => $sched->id,
                'title' => $sched->title,
                'start' => $sched->from_date, 
                'end' => $sched->to_date,
                'description' => $sched->purpose,
            ];
        }
    
        // Return response for AJAX requests
        if (request()->ajax()) {
            return response(['data' => $schedules, 'status' => 'success'], 200);
        }
    
        // Return the view
        return view('booking.index');
    }
    
    public function store(CreateBookingRequest $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $booking = Booking::create([
                'title' => $data['title'],
                'user_id' => Auth::user()->id,
                'purpose' => $data['purpose'],
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
                'destination' => $data['destination'],
                'driver_id' => $data['driver_id'],
                'car_id' => $data['car_id'],
                'odo_meter' => '',
                'remarks' => $data['remarks']
            ]);
            
            $adminEmails = User::role('admin')->pluck('email')->toArray();

            $driverEmail = optional($booking->driver)->email;

            $requesterEmail = $booking->user->email;

            $recipients = collect($adminEmails)
                ->merge([$driverEmail, $requesterEmail])
                ->filter()
                ->unique()
                ->values()
                ->all();

            DB::commit();

            Mail::to($recipients)->send(new sendToAdminMail($booking, 'Booking Request'));

            return response(['data' => $booking, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return  response(['message' => $e->getMessage(), 'status' => 'store  failed'], 500);
        }
    }
}
