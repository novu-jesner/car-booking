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
        $rules = [
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date',
            'destination' => 'required|string',
            'driver_id' => 'nullable|exists:users,id',
            'car_id' => 'nullable|exists:cars,id',
            'remarks' => 'nullable|string',
            'odo_meter' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
            'from_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $this->checkOverlappingBooking($value, 'from_date', $fail);
                },
            ],
            'to_date' => [
                'required',
                'date',
                'after:from_date',
                function ($attribute, $value, $fail) {
                    $this->checkOverlappingBooking($value, 'to_date', $fail);
                },
            ],
        ];

         // If is_approved is not true, require from_date and to_date
        if (!$this->boolean('is_approved')) {
            $rules['from_date'] = [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $this->checkOverlappingBooking($value, 'from_date', $fail);
                },
            ];
            
            $rules['to_date'] = [
                'required',
                'date',
                'after:from_date',
                function ($attribute, $value, $fail) {
                    $this->checkOverlappingBooking($value, 'to_date', $fail);
                },
            ];
        } else {
            // When is_approved is true, make them nullable
            $rules['from_date'] = ['nullable', 'date'];
            $rules['to_date'] = ['nullable', 'date'];
        }


        return $rules;
    }

    protected function checkOverlappingBooking($date, $type, $fail)
    {
        $from_date = Carbon::parse($this->input('from_date'));  // Parse the ISO format date
        $to_date = Carbon::parse($this->input('to_date'));      // Parse the ISO format date
        $car_id = $this->input('car_id');
        $driver_id = $this->input('driver_id');

        Log::info($from_date);
        Log::info($to_date);

        // Check for overlapping bookings that are approved
        $overlappingBooking = Booking::where('status', 'approved')
        ->where(function ($query) use ($car_id, $driver_id) {
            $query->where('car_id', $car_id)
                  ->orWhere('driver_id', $driver_id);
        })
        ->where(function ($query) use ($from_date, $to_date) {
            $query->whereBetween('from_date', [$from_date, $to_date])
                  ->orWhereBetween('to_date', [$from_date, $to_date])
                  ->orWhere(function ($query) use ($from_date, $to_date) {
                      $query->where('from_date', '<=', $from_date)
                            ->where('to_date', '>=', $to_date);
                  });
        })
        ->exists();    
   
       // If an overlapping booking is found, trigger the validation failure
       if ($overlappingBooking) {
           $fail('The selected dates and times conflict with an already approved booking.');
       }
    }
}
