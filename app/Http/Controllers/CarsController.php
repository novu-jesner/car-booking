<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        if (request()->ajax()) {
            $cars = Cars::select('*'); 
            return $this->datatable($cars);
        }

        return view('cars.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    
        $request->validate([
            'name' => 'required',
            'license_plate' => 'required|unique:cars',
            'brand' => 'required', 
            'type' => 'required', 
            'seater' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'is_available' => 'required|boolean'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('cars', 'public');
            }

            $car = Cars::create($data);

            DB::commit();
            return response(['data' => $car, 'status' => 'success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'store failed'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
        $car = Cars::findOrFail($id);
        return response(['data' => $car, 'status' => 'success'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $car = Cars::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'license_plate' => 'required|unique:cars,license_plate,' . $car->id,
            'brand' => 'required',
            'type' => 'required',
            'seater' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'is_available' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();

            if ($request->hasFile('image')) {
              
                if ($car->image && Storage::disk('public')->exists($car->image)) {
                    Storage::disk('public')->delete($car->image);
                }
                $data['image'] = $request->file('image')->store('cars', 'public');
            }

            $car->update($data);

            DB::commit();
            return response(['data' => $car, 'status' => 'success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'update failed'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $car = Cars::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($car->image && Storage::disk('public')->exists($car->image)) {
                Storage::disk('public')->delete($car->image);
            }
            
            $car->delete();

            DB::commit();
            return response(['data' => $car, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'destroy failed'], 500);
        }
    }

    /**
     * Handle DataTable Data
     */
    public function datatable($query)
    {
        return DataTables::of($query)
            ->addColumn('car_image', function ($row) {
                if ($row->image) {
                    $url = asset('storage/' . $row->image);
                    return '<img src="' . $url . '" width="50" class="img-thumbnail">';
                }
                return '<span class="text-muted">No Image</span>';
            })
            ->addColumn('availability', function ($row) {
                return $row->is_available 
                    ? '<span class="badge bg-success">Available</span>' 
                    : '<span class="badge bg-danger">Unavailable</span>';
            })
            ->addColumn('actions', function ($row) {
                return '
                    <div class="d-flex">
                        <button data-id="' . $row->id . '" class="btn btn-warning btn-sm ms-1 text-white edit-button">Edit</button>
                        <button data-id="' . $row->id . '" class="btn btn-danger btn-sm ms-1 text-white delete-button">Delete</button>
                    </div>
                ';
            })
            ->rawColumns(['car_image', 'availability', 'actions'])
            ->make(true);
    }
}