<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }
    
    public function index()
    {
        $users = User::with(['roles'])->where('isActive', true)->get();

        // Check if the request is an AJAX request
        if (request()->ajax()) {
            return $this->datatable($users);
        }

        return view('user-management.index');
    }

    public function store(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['name'],
                'employee_no' => $data['employee_no'],
                'email' => $data['email'],
                'password' => Hash::make('Novulutions@12345'),
            ]);

            $user->assignRole($data['role']); 

            DB::commit();
            return response(['data' => $user, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'store failed'], 500);
        }
    }

    public function edit($id) {
        $user = User::with(['roles'])->where('isActive', true)->findOrFail($id);
        return response(['data' => $user, 'status' => 'success'], 200);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            $user = User::where('isActive', true)->findOrFail($id);

            $user->update([
                'name' => $data['name'],
                'employee_no' => $data['employee_no'],
                'email' => $data['email'],
            ]);

            // Sync roles (replace existing roles with the new one)
            $user->syncRoles([$data['role']]);

            DB::commit();
            return response(['data' => $user, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'store failed'], 500);
        }
    }

    public function destroy(string $id)
    {
        $user = User::where('isActive', true)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark the user as inactive
            $user->update(['isActive' => false]);
    
            DB::commit();
            return response(['data' => $user, 'status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage(), 'status' => 'destroy failed'], 500);
        }
    }

    public function datatable($query)
    {
        return DataTables::of($query)
        ->addColumn('profile_picture', function ($user) {
            $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1));

            return '<div class="profile_display">' . $initials . '</div>';
        })
        ->addColumn('role', function ($user) {
            return $user->roles->pluck('name')->implode(', ');
        })
        ->addColumn('actions', function ($row) {
            if($row->role  === 'Admin') {
                $data = '
                    <div class="d-flex">
                        <button data-id="' . $row->id . '" class="btn btn-warning w-100 btn-sm ms-1 text-white edit-button">Edit</button>
                    </div>
                ';
            } else {
                $data = '
                    <div class="d-flex">
                        <button data-id="' . $row->id . '" class="btn btn-warning btn-sm ms-1 text-white edit-button">Edit</button>
                        <button data-id="' . $row->id . '" class="btn btn-danger btn-sm ms-1 text-white delete-button">Delete</button>
                    </div>
                ';
            }
            return $data;
     
        })
        ->rawColumns(['role', 'profile_picture', 'actions'])
        ->make(true);
    }
}
