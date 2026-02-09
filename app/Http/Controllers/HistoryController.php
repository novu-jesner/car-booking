<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HistoryController extends Controller
{
    //
    public function index()
    {
        $user_id = Auth::user()->id;

        $bookings = Booking::where('user_id', $user_id)->get();

        // Check if the request is an AJAX request
        if (request()->ajax()) {
            return $this->datatable($bookings);
        }

        return view('booking.history.index');
    }

    public function show($id)
    {
        $booking = Booking::with('user', 'driver', 'car', 'user')->findOrFail($id);

        return response(['data' => $booking, 'status' => 'success'], 200);
    }

    public function edit($id)
    {
        $booking = Booking::where('status', 'pending')->orWhere('status', 'approved')->findOrFail($id);

        return response(['data' => $booking, 'status' => 'success'], 200);
    }

    public function update(CreateBookingRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $booking = Booking::where('status', 'pending')->orWhere('status', 'approved')->findOrFail($id);
        
            // Ensure is_approved is properly cast as a boolean
            $isApproved = filter_var($data['is_approved'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
            // Keep existing dates if approved, otherwise use input values
            $from = $isApproved ? $booking->from_date : ($data['from_date'] ?? $booking->from_date);
            $to = $isApproved ? $booking->to_date : ($data['to_date'] ?? $booking->to_date);
        
            $booking->update([
                'title' => $data['title'],
                'purpose' => $data['purpose'],
                'from_date' => $from,
                'to_date' => $to,
                'destination' => $data['destination'],
                'driver_id' => $data['driver_id'],
                'car_id' => $data['car_id'],
                'remarks' => $data['remarks'],
            ]);
        
            DB::commit();
            
            // Return success response
            return response(['data' => $booking, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'store failed'], 500);
        }        
    }

    public function destroy(string $id)
    {
        $booking = Booking::where('status', 'pending')->orWhere('status', 'approved')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark the user as inactive
            $booking->update(['status' => 'cancelled']);
    
            DB::commit();
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
        
            if ($row->status === 'pending') {
                $editButton = '<button data-id="' . $row->id . '" class="btn btn-outline-warning btn-sm ms-1 edit-button">Edit</button>';
                $cancelButton = '<button data-id="' . $row->id . '" class="btn btn-outline-danger btn-sm ms-1 cancel-button">Cancel</button>';
            } elseif ($row->status === 'approved') {
                $cancelButton = '<button data-id="' . $row->id . '" class="btn btn-outline-danger btn-sm ms-1 cancel-button">Cancel</button>';
            }
        
            return '
                <div class="d-flex">
                    ' . $viewButton . '
                    ' . $editButton . '
                    ' . $cancelButton . '
                </div>
            ';
        })        
        ->rawColumns(['status', 'date', 'actions'])
        ->make(true);
    }
}
