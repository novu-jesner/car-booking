<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    
    public function index()
    {
        $approved = Booking::where('status', '=', 'approved')->count();
        $rejected = Booking::where('status', '=', 'rejected')->count();
        $pending = Booking::where('status', '=', 'pending')->count();
        $done = Booking::where('status', '=', 'done')->count();
        $cancelled = Booking::where('status', '=', 'cancelled')->count();

        return view('dashboard.index', compact('approved', 'rejected', 'done', 'pending', 'cancelled'));
    }
}
