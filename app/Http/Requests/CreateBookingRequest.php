<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
       public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'purpose'     => 'required|string',
            'destination' => 'required|string',
            'driver_id'   => 'nullable|exists:users,id',
            'car_id'      => 'nullable|exists:cars,id',
            'remarks'     => 'nullable|string|max:255',
            'from_date'   => 'required|date|after_or_equal:' . now()->addDays(2)->toDateString(), // 2-day advance
            'to_date'     => 'required|date|after:from_date',
            'is_approved' => 'nullable|boolean',
        ];
    }
}