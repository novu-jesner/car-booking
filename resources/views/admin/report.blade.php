@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Driver Report</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Driver Name</th>
                <th>Car</th>
                <th>Report Type</th>
                <th>Description</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->user->name }}</td>
                <td>{{ $report->car->brand }} ({{ $report->car->license_plate }})</td>
                <td>{{ ucfirst($report->type) }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
</div>
@endsection