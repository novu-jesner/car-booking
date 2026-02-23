@extends('layouts.app')

@section('content')
<div class="container">

    <h2>My Vehicle</h2>

 

    @if($car)
        <div class="card p-3 mb-4">
            <div class="text-center mb-3">
                
            @if($car->image) 
                <img src="{{ asset('storage/' . $car->image) }}" 
                     alt="Car Image" 
                     class="img-thumbnail" 
                     style="width: 100%; max-width: 300px; height: auto;">
            @else
                <img src="{{ asset('images/no-car.png') }}" 
                     alt="No Image" 
                     class="img-thumbnail" 
                     style="width: 100%; max-width: 300px;">
            @endif
        </div>
            <p><b>Plate Number:</b> {{ $car->license_plate }}</p>
            <p><b>Model:</b> {{ $car->brand }}</p>
            <p><b>Name:</b> {{ $car->name }}</p>
            <p><b>Seater:</b> {{ $car->seater }}</p>
            <p><b>Type:</b> {{ $car->type }}</p>
        </div>

   @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
        <h3>Submit Report</h3>

        <form method="POST" action="{{ route('driver.report.store') }}">
            @csrf
            <div class="mb-3">
                <label>Report Type</label>
                <select name="type" class="form-control" required>
                    <option value="maintenance">Maintenance Request</option>
                    <option value="accident">Accident Report</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <button class="btn btn-primary">Submit Report</button>
        </form>

    @else
        <div class="alert alert-warning">
            You have no approved booking vehicle.
        </div>
    @endif

</div>
@endsection
