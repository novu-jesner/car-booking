<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Mail\BookingAproveMail;
use App\Mail\BookingRejectMail;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class BookingApprovalController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::orderBy('created_at', 'desc') 
                   ->get();

        // Check if the request is an AJAX request
        if (request()->ajax()) {
            return $this->datatable($bookings);
        }

        return view('approval.index');
    }

    public function approve($id)
    {
        $booking = Booking::where('status', 'pending')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark the user as inactive
            $booking->update(['status' => 'approved']);

            DB::commit();

            $driverEmail = optional($booking->driver)->email;

            $requesterEmail = $booking->user->email;

            $recipients = collect($driverEmail)
                ->merge([$requesterEmail])
                ->filter()
                ->unique()
                ->values()
                ->all();

            Mail::to($recipients)->send(new BookingAproveMail($booking));

            return response(['data' => $booking, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'destroy failed'], 500);
        }
    }

    public function reject($id)
    {
        $booking = Booking::where('status', 'pending')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark the user as inactive
            $booking->update(['status' => 'rejected']);
    
            DB::commit();

            $driverEmail = optional($booking->driver)->email;

            $requesterEmail = $booking->user->email;

            $recipients = collect($driverEmail)
                ->merge([$requesterEmail])
                ->filter()
                ->unique()
                ->values()
                ->all();

            Mail::to($recipients)->send(new BookingRejectMail($booking));

            return response(['data' => $booking, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'destroy failed'], 500);
        }
    }

    public function datatable($query)
    {
        return DataTables::of($query)
        ->addColumn('date', function ($row) {
            $from = \Carbon\Carbon::parse($row->from_date)->format('M d, Y h:i A');
            $to = \Carbon\Carbon::parse($row->to_date)->format('M d, Y h:i A');
            return "
            <div class='d-flex flex-column align-items-start'>
                <span class='badge bg-danger text-white mb-1'>From: $from</span>
                <span class='badge bg-primary text-white mt-1'>To: $to</span>
            </div>
        ";
        })
        ->addColumn('status', function ($row) {
            $statusClass = '';
        
            switch ($row->status) {
                case 'pending':
                    $statusClass = 'badge bg-primary'; 
                    break;
                case 'approved':
                    $statusClass = 'badge bg-success';
                    break;
                case 'rejected':
                    $statusClass = 'badge bg-danger'; 
                    break;
                case 'cancelled':
                    $statusClass = 'badge bg-warning';
                    break;
                case 'done':
                    $statusClass = 'badge bg-info';
                    break;
                default:
                    $statusClass = 'badge bg-secondary'; 
            }
        
            return '<span class="' . $statusClass . '">' . ucfirst($row->status) . '</span>';
        })
        
        ->addColumn('actions', function ($row) {
            $viewButton = '<button data-id="' . $row->id . '" class="btn w-100 btn-outline-primary btn-sm ms-1 view-button">View</button>';
            $editButton = '';
            $cancelButton = '';
        
            if ($row->status === 'pending' || $row->status === 'approved') {
                $editButton = '<button data-id="' . $row->id . '" class="btn btn-outline-warning btn-sm ms-1 edit-button">Edit</button>';
                $cancelButton = '<button data-id="' . $row->id . '" class="btn btn-outline-danger btn-sm ms-1 cancel-button">Cancel</button>';
            }
        
            return '
                <div class="d-flex">
                    ' . $viewButton . '
                    ' . $editButton . '
                    ' . $cancelButton .'
                </div>
            ';
        })        
        ->rawColumns(['status', 'date', 'actions'])
        ->make(true);
    }
}
