<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MyRidesController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;

        $bookings = Booking::where('driver_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
 
         // Check if the request is an AJAX request
         if (request()->ajax()) {
             return $this->datatable($bookings);
         }

        return view('my-rides.index');

    }

    public function done($id)
    {
        $booking = Booking::where('status', 'pending')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark the user as inactive
            $booking->update(['status' => 'done']);
    
            DB::commit();
            return response(['data' => $booking, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'destroy failed'], 500);
        }
    }

    public function update(UpdateBookingRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('image-attachment', 'public');
                $data['attachment'] = $path;
            } else {
                $data['attachment'] = 'img/default.jpg';
            }

            $booking = Booking::where('status', '!=', 'done')->findOrFail($id);
            $booking->update([
                'odo_meter' => $data['odo_meter'],
                'status' => 'done',
                'img_attachment' => 'storage/' . $data['attachment']
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return  response(['message' => $e->getMessage(), 'status' => 'store  failed'], 500);
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
            $doneButtom = '';
        
            if ($row->status === 'approved') {
                $doneButtom = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm ms-1 done-button">Finish</button>';
            }
        
            return '
                <div class="d-flex">
                    ' . $viewButton . '
                    ' . $doneButtom . '
                </div>
            ';
        })        
        ->rawColumns(['status', 'date', 'actions'])
        ->make(true);
    }
}
