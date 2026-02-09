@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>Dashboard</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum, nisi.</p>
        </div>
        <a href="{{ route('booking.index') }}" class="btn btn-primary btn-custom d-flex align-items-center gap-2" data-bs-toggle="tooltip" data-bs-title="Default tooltip">
            <i class="fa-solid fa-caravan"></i> Go to Booking Now!
        </a>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-end mb-3 update-status">
      <small>Last updated: 0 mins ago</small>
    </div>
    <div class="row">
      <!-- Approved Card -->
      <div class="col-md-4 mb-4">
        <div class="card card-custom">
          <div class="card-header bg-primary card-header-custom">
            <i class="fas fa-check-circle icon-custom"></i> Pending
          </div>
          <div class="card-body card-body-custom">
            <h3>{{ $pending }}</h3>
            <p>Total Pending Bookings</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card card-custom">
          <div class="card-header bg-success card-header-custom">
            <i class="fas fa-check-circle icon-custom"></i> Approved
          </div>
          <div class="card-body card-body-custom">
            <h3>{{ $approved }}</h3>
            <p>Total Approved Bookings</p>
          </div>
        </div>
      </div>

      <!-- Canceled Card -->
      <div class="col-md-4 mb-4">
        <div class="card card-custom">
          <div class="card-header bg-warning card-header-custom">
            <i class="fas fa-times-circle icon-custom"></i> Canceled
          </div>
          <div class="card-body card-body-custom">
            <h3>{{ $cancelled }}</h3>
            <p>Total Canceled Bookings</p>
          </div>
        </div>
      </div>

      <!-- Canceled Card -->
      <div class="col-md-4 mb-4">
        <div class="card card-custom">
          <div class="card-header bg-danger card-header-custom">
            <i class="fas fa-times-circle icon-custom"></i> Rejected
          </div>
          <div class="card-body card-body-custom">
            <h3>{{ $rejected }}</h3>
            <p>Total Rejected Bookings</p>
          </div>
        </div>
      </div>

      <!-- Total Done Card -->
      <div class="col-md-4 mb-4">
        <div class="card card-custom">
          <div class="card-header bg-info card-header-custom">
            <i class="fas fa-clipboard-check icon-custom"></i> Total Done
          </div>
          <div class="card-body card-body-custom">
            <h3>{{ $done }}</h3>
            <p>Total Completed Bookings</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function () {
      function updateTimestamps() {
          $(".update-status small").each(function () {
              let text = $(this).text().trim();
              let minutesMatch = text.match(/(\d+)\s+mins?/); 
              
              if (minutesMatch) {
                  let minutes = parseInt(minutesMatch[1]); 
                  minutes++; 
                  
                  $(this).text(`Last updated: ${minutes} mins ago`); 
              }
          });
      }

      setInterval(updateTimestamps, 60000);
  });
</script>
@endsection