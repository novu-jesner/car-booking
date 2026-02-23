@extends('layouts.app')

@section('content')
@include('booking.history.modal.view')
@include('booking.modal.edit')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>Booking History</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum, nisi.</p>
        </div>
        <a href="{{ route('booking.index') }}" class="btn btn-danger text-light btn-custom d-flex align-items-center gap-2">
            <i class="fa-solid fa-left-long"></i> Back to Booking
        </a>
    </div>
    <div class="table-responsive">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>Title/Description</th>
                    <th>Date/Time Range</th>
                    <th>Status</th>
                    <th>Destination</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>                
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
     $(document).ready(function () {
        var renderWithDiv = function(data, type, full, meta) {
            return '<div>' + data + '</div>';
        };
    let dataTable = $('#myTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("booking-history.index") }}',
    columns: [
        { data: "title", name: "title", render: renderWithDiv },
        { data: "date", name: "date", render: renderWithDiv },
        { data: "status", name: "status", render: renderWithDiv }, // fixed
        { data: "destination", name: "destination", render: renderWithDiv },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ]
});


        const myViewModal = $('#viewModal');
        const myEditModal = $('#bookingModal');

        // Edit show
        let id = null;
        $(document).on('click', '.edit-button', function() {
            id = $(this).attr('data-id');
            $('.modal-title').html('Edit Booking');
            $('#submit-button').hide();
            $('#update-button').show();

             // Remove the previous error if there is
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            axios.get(`booking-history/${id}/edit`)
            .then((response) => {
                const data = response.data.data;
                console.log(data);
                $('#title').val(data.title);
                $('#purpose').val(data.purpose);
                $('#from_date').val(data.from_date);
                $('#to_date').val(data.to_date);
                $('#destination').val(data.destination);
                $('#driver_id').val(data.driver_id);
                $('#car_id').val(data.car_id);
                $('#remarks').val(data.remarks);

                myEditModal.modal('show');
            })
            .catch(error => {
                Swal.fire({
                    title: "Oops!",
                    text: error.message,
                    icon: "error"
                });
            })
        })

        $(document).on('click', '.view-button', function() {
            id = $(this).attr('data-id');
            $('.modal-title').html('View Booking');

            axios.get(`booking-history/${id}`)
            .then((response) => {
                const bookingData = response.data.data;
                $('#viewTitle').text(bookingData.title || '--');
                $('#viewUser').text(bookingData.user.name || '--');
                $('#viewPurpose').text(bookingData.purpose || '--');
                $('#viewFromDate').text(bookingData.from_date || '--');
                $('#viewToDate').text(bookingData.to_date || '--');
                $('#viewDestination').text(bookingData.destination || '--');
                $('#viewDriver').text(bookingData.driver.name || '--');
                $('#viewCar').text(bookingData.car.name || '--');
                $('#viewOdometer').text(bookingData.odo_meter || '--');
                $('#viewRemarks').text(bookingData.remarks || '--');
                myViewModal.modal('show');
            })
            .catch(error => {
                Swal.fire({
                    title: "Oops!",
                    text: error.message,
                    icon: "error"
                });
            })
        })
// Update
$('#update-button').click(e => {
    e.preventDefault();

    $('.text-danger').addClass('d-none');
    $('.form-control').removeClass('is-invalid');
    $('.form-select').removeClass('is-invalid');

    const payload = {
        title: $('#title').val(),
        purpose: $('#purpose').val(),
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
        destination: $('#destination').val(),
        driver_id: $('#driver_id').val(),
        car_id: $('#car_id').val(),
        remarks: $('#remarks').val()
    };

    axios.put(`/booking-history/${id}`, payload, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then((response) => {
        dataTable.ajax.reload();
        myEditModal.modal('hide');
        Swal.fire({
            title: "Success!",
            text: "Your changes have been saved.",
            icon: "success"
        });
    })
    .catch(error => {
        if(error.response && error.response.status === 422) {
            $('.text-danger').addClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.form-select').removeClass('is-invalid');

            $.each(error.response.data.errors, function(field, errorMessage) {
                var errorSpanId = '#' + field + '_error';
                $(`#${field}`).addClass('is-invalid');
                $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
            });

        } else {
            Swal.fire({
                title: "Oops!",
                text: error.response?.data?.message || error.message,
                icon: "error"
            });
        }
    });
});


         // Cancel
         $(document).on('click', '.cancel-button', function() {
            id = $(this).attr('data-id');

            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Cancelled it!"
            }).then((result) => {
                if(result.isConfirmed) {
                    axios.delete(`booking-history/${id}`)
                    .then(response => {
                        dataTable.ajax.reload();
                        console.log(response.data.data)
                        Swal.fire({
                            title: "Cancelled!",
                            text: "Your data has been deleted.",
                            icon: "success"
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: "Oops!",
                            text: error.message,
                            icon: "error"
                        });
                    })
                }
            })
        })
    })
</script>
@endsection