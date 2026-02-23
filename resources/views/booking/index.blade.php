@extends('layouts.app')

@section('content')
<div class="container">
    @include('booking.modal.edit')
    @include('booking.modal.off-canvas')
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>Booking Module</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum, nisi.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('booking-history.index') }}" class="btn btn-outline-primary btn-custom d-flex align-items-center gap-2">
                <i class="fa-solid fa-table-list"></i> History
            </a>
            <button class="btn btn-primary btn-custom book-now">
                <i class="fa-solid fa-caravan"></i> Book a Ride Now!
            </button>
        </div>
    </div>
    <div id="calendar"></div>
</div>
@endsection

@section('script')
<script>
    function formatDate(date) {
        if (!date) return '--';
        const d = new Date(date);
        return d.toLocaleString('en-US', { 
            weekday: 'long',    // Full weekday name (e.g., Monday)
            year: 'numeric',    // Full year (e.g., 2025)
            month: 'long',      // Full month name (e.g., January)
            day: 'numeric',     // Day of the month (e.g., 7)
            hour: '2-digit',    // Hour in 12-hour format
            minute: '2-digit',  // Minute (e.g., 12:30 PM)
            hour12: true        // Use 12-hour format
        });
    }
    $(document).ready(function() {
        var calendarEl = $('#calendar')[0]; 

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,multiMonth' 
            },
            views: {
                multiMonth: {
                    type: 'dayGrid',
                    duration: { years: 1 },
                    buttonText: 'Grid' 
                }
            },
            events: function(info, successCallback, failureCallback) {
                $.ajax({
                    url: '/booking', 
                    data: {
                        year: info.start.getFullYear(), 
                        month: info.start.getMonth() + 1, 
                    },
                    success: function(response) {
                        var events = response.data.map(function(event) {
                            return {
                                id: event.id,
                                title: event.title,
                                start: event.start, 
                                end: event.end, 
                                description: event.description
                            };
                        });

                        successCallback(events);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching events:", error);
                        failureCallback(error);
                    }
                });
            },
            eventClick: function(info) {
                axios.get(`booking-history/${info.event.id}`)
                .then((response) => {
                    const bookingData = response.data.data;
                    console.log(bookingData);
                    $('#viewTitle').text(bookingData.title || '--');
                    $('#viewUser').text(bookingData.user.name || '--');
                    $('#viewPurpose').text(bookingData.purpose || '--');
                    $('#viewFromDate').text(formatDate(bookingData.from_date));
                    $('#viewToDate').text(formatDate(bookingData.to_date));
                    $('#viewDestination').text(bookingData.destination || '--');
                    $('#viewDriver').text(bookingData.driver.name || '--');
                    $('#viewCar').text(bookingData.car.name || '--');
                    $('#viewOdometer').text(bookingData.odo_meter || '--');
                    $('#viewRemarks').text(bookingData.remarks || '');
                    $('#viewBookBy').text(bookingData.user.name || '--');
                    
                    $('#myOffCanvas').offcanvas('show');
                })
                .catch(error => {
                    Swal.fire({
                        title: "Oops!",
                        text: error.message,
                        icon: "error"
                    });
                })
            }
        });

        // Render the calendar
        calendar.render();

        const myModal = $('#bookingModal');

        $('.book-now').click(e => {
            console.log('asdasd')
            $('.modal-title').html('Where to go?');
            $('#myForm')[0].reset();
            $('#submit-button').show();
            $('#update-button').hide();

            // Remove the previous error if there is
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');

            myModal.modal('show');
        })

       // Create
        $('#submit-button').click(function(e) {
            e.preventDefault();

            const button = $(this);

            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.form-select').removeClass('is-invalid');

            const formData = new FormData();
            formData.append('title', $('#title').val());
            formData.append('purpose', $('#purpose').val());
            formData.append('from_date', $('#from_date').val());
            formData.append('to_date', $('#to_date').val());
            formData.append('destination', $('#destination').val());
            formData.append('driver_id', $('#driver_id').val());
            formData.append('car_id', $('#car_id').val());
            formData.append('remarks', $('#remarks').val());

            button.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> Submitting...`);

            axios.post('booking', formData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                myModal.modal('hide');
                Swal.fire({
                    title: "Success!",
                    text: response.data.message,
                    icon: "success"
                });
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    $('.text-danger').addClass('d-none');
                    $('.form-control').removeClass('is-invalid');

                    $.each(error.response.data.errors, function(field, errorMessage) {
                        const errorSpanId = '#' + field + '_error';
                        $(`#${field}`).addClass('is-invalid');
                        $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
                    });
                } else {
                    Swal.fire({
                        title: "Oops!",
                        text: error.message,
                        icon: "error"
                    });
                }
            })
            .finally(() => {
                button.prop('disabled', false).html(`Submit Booking`);
            });
        });

    });




 

</script>

@endsection
