<?php

use App\Http\Controllers\BookingApprovalController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MyRidesController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

    Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/booking', BookingController::class);
    Route::resource('/booking-history', HistoryController::class)->only('index', 'show', 'edit', 'update', 'destroy');
    Route::resource('/user-management', UserManagementController::class);
    Route::resource('/booking-approval', BookingApprovalController::class)->only('index')->middleware('role:admin');
    Route::post('/booking-approve/{id}', [BookingApprovalController::class, 'approve'])->middleware('role:admin');
    Route::post('/booking-reject/{id}', [BookingApprovalController::class, 'reject'])->middleware('role:admin');
    
    Route::resource('/my-rides-bookings', MyRidesController::class)->only('index', 'update')->middleware('role:driver');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/available-drivers', [BookingController::class, 'availableDrivers']);
    Route::get('/available-cars', [BookingController::class, 'availableCars']);
    Route::resource('/cars', CarsController::class);





});