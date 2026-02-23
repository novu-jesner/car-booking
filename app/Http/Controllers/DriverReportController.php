<?php

namespace App\Http\Controllers;
use App\Models\VehicleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DriverReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     $booking = Booking::where('driver_id', Auth::id())
                ->where('status', 'approved')
                ->latest()
                ->first();

    $car = $booking ? $booking->car : null;

    return view('driver.dashboard', compact('car'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function submit(Request $request)
    {
      $booking = Booking::where('driver_id', Auth::id())
                ->where('status', 'approved')
                ->latest()
                ->first();

     if(!$booking){
        return back()->with('error','No active vehicle.');
    }

    VehicleReport::create([
        'user_id' => Auth::id(),
        'car_id' => $booking->car_id,
        'type' => $request->type,
        'description' => $request->description
    ]);

    return back()->with('success','Report submitted!');
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
