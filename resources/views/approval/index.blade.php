@extends('layouts.app')

@section('content')
@include('approval.modal.view')
@include('booking.modal.edit')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
        <div class="d-flex justify-content-center flex-column">
            <h3>Booking Approval</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum, nisi.</p>
        </div>
        <a href="{{ route('booking.index') }}" class="btn btn-danger text-light btn-custom d-flex align-items-center gap-2">
            <i class="fa-solid fa-left-long"></i> Back to Booking
        </a>
    </div>
    <div class="btn-group mb-4" role="group">
        <button id="filterPending" class="btn btn-primary btn-paginate" btn-paginate>Pending</button>
        <button id="filterApproved" class="btn btn-outline-primary btn-paginate">Approved</button>
        <button id="filterRejected" class="btn btn-outline-primary btn-paginate">Rejected</button>
        <button id="filterCancelled" class="btn btn-outline-primary btn-paginate">Cancelled</button>
        <button id="filterDone" class="btn btn-outline-primary btn-paginate">Done</button>
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
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('booking-approval.index') }}",
            "columns": [
                { "data": "title", "name": "title", "render": renderWithDiv },
                { "data": "date", "name": "date", "render": renderWithDiv },
                { "data": "status", "name": "status", "render": renderWithDiv, "searchable": true },
                { "data": "destination", "name": "destination", "render": renderWithDiv },
                { "data": "actions", "name": "actions", "orderable": false, "searchable": false }
            ]
        });

        dataTable.column(2).search('pending').draw();

        // Filter buttons click event handlers
        $('#filterPending').on('click', function() {
            dataTable.column(2).search('pending').draw();
        });

        $('#filterApproved').on('click', function() {
            dataTable.column(2).search('approved').draw();
        });

        $('#filterRejected').on('click', function() {
            dataTable.column(2).search('rejected').draw();
        });

        $('#filterCancelled').on('click', function() {
            dataTable.column(2).search('cancelled').draw();
        });

        $('#filterDone').on('click', function() {
            dataTable.column(2).search('done').draw();
        });

        $('.btn-paginate').click(function() {
            // Reset all buttons to btn-outline-primary
            $('.btn-paginate').removeClass('btn-primary').addClass('btn-outline-primary');

            // Add btn-primary class to the clicked button
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            // You can add your filtering logic here, based on the clicked button
            var filterType = $(this).text().toLowerCase(); // Get the text of the clicked button (e.g., 'pending', 'approved')
            console.log("Filter applied: " + filterType); // You can replace this with actual filtering logic
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
                $('#from_date').closest('.mb-3').hide();
                $('#to_date').closest('.mb-3').hide();
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
                console.log(bookingData)
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
                $('#viewOdometer').text(bookingData.odo_meter || 'not yet done');

                 // Set image src
                if (bookingData.img_attachment) {
                    const attachment_url = `{{ asset('${bookingData.img_attachment}') }}`;
                    $('#viewImg').attr('src', attachment_url).removeClass('d-none'); // Show image
                } else {
                    $('#viewImg').addClass('d-none'); // Hide image if no attachment
                }

                console.log(bookingData.status)
                if(bookingData.status === 'pending')
                {
                    $('#footer').removeClass('d-none'); // show buttons
                } else {
                    $('#footer').addClass('d-none'); // hide buttons
                }

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

            const formData = new FormData();
            formData.append('title', $('#title').val());
            formData.append('purpose', $('#purpose').val());
            formData.append('from_date', $('#from_date').val());
            formData.append('to_date', $('#to_date').val());
            formData.append('destination', $('#destination').val());
            formData.append('driver_id', $('#driver_id').val());
            formData.append('car_id', $('#car_id').val());
            formData.append('remarks', $('#remarks').val());
            formData.append('is_approved', '1');
            formData.append('_method', 'PUT');

            axios.post(`booking-history/${id}`, formData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
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
                    // Clear previous error messages
                    $('.text-danger').addClass('d-none');
                    $('.form-control').removeClass('is-invalid');
                    $('.form-select').removeClass('is-invalid');

                    $.each(error.response.data.errors, function(field, errorMessage) {
                        var errorSpanId = '#' + field + '_error';
                        $(`#${field}`).addClass('is-invalid');

                        // Show the error message in the respective error span
                        $(errorSpanId).removeClass('d-none').text(errorMessage[0]);
                    });

                } else {
                    Swal.fire({
                        title: "Oops!",
                        text: error.message,
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

         // Approve
         $(document).on('click', '.btn-approve', function() {
            const button = $(this);
            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approve it!"
            }).then((result) => {
                if(result.isConfirmed) {

                    button.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> Approving...`);

                    axios.post(`booking-approve/${id}`)
                    .then(response => {
                        dataTable.ajax.reload();
                        console.log(response.data.data)
                        myViewModal.modal('hide');
                        Swal.fire({
                            title: "Approved!",
                            text: "Your data has been Approved.",
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
                    .finally(() => {
                        button.prop('disabled', false).html(`Approve`);
                    });
                }
            })
        })

         // Reject
         $(document).on('click', '.btn-reject', function() {
            const button = $(this);
            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Reject it!"
            }).then((result) => {
                if(result.isConfirmed) {

                    button.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> rejecting...`);

                    axios.post(`booking-reject/${id}`)
                    .then(response => {
                        dataTable.ajax.reload();
                        console.log(response.data.data)
                        myViewModal.modal('hide');
                        Swal.fire({
                            title: "Rejected!",
                            text: "Your data has been Rejected.",
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
                    .finally(() => {
                        button.prop('disabled', false).html(`Reject`);
                    });
                }
            })
        })
    })
</script>
@endsection