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

var dataTable;
var bookingId;
var id; // optional, if you track view/cancel
var myViewModal;
var myEditModal;


   $(document).ready(function () {
    myViewModal = $('#viewModal');
    myEditModal = $('#bookingModal');
    var renderWithDiv = function(data, type, full, meta) {
        return '<div>' + data + '</div>';
    };

    dataTable = $('#myTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('booking-approval.index') }}",
        "columns": [
            { "data": "title", "name": "title", "render": renderWithDiv },
            { "data": "date", "name": "date", "render": renderWithDiv },
            { "data": "status", "name": "status", "render": renderWithDiv, "searchable": true },
            { "data": "destination", "name": "destination", "render": renderWithDiv },
            { "data": "actions", "name": "actions", "orderable": false, "searchable": false },
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
        $('.btn-paginate').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
    });

    
    $('#myTable').on('draw.dt', function () {
        $('#myTable tbody tr').each(function () {
            var status = $(this).find('td:eq(2)').text().trim().toLowerCase();
            if (status === 'approved') {
                $(this).find('.edit-button').hide();
            } else {
                $(this).find('.edit-button').show();
            }
        });
    });
});



       $(document).on('click', '.edit-button', function () {
    bookingId = $(this).data('id');

    // Reset form
    $('#bookingModal form')[0].reset();
    $('.text-danger').addClass('d-none');
    $('.form-control, .form-select').removeClass('is-invalid');

    axios.get(`booking-history/${bookingId}/edit`)
        .then(response => {
            const data = response.data.data;
            $('#title').val(data.title);
            $('#purpose').val(data.purpose);
            $('#from_date').val(data.from_date);
            $('#to_date').val(data.to_date);
            $('#destination').val(data.destination);
            $('#driver_id').val(data.driver_id);
            $('#car_id').val(data.car_id);
            $('#remarks').val(data.remarks);

            // Hide submit, show update
            $('#submit-button').hide();
            $('#update-button').show();

            myEditModal.modal('show');
        })
        .catch(error => {
            Swal.fire({
                title: "Oops!",
                text: error.response?.data?.message || error.message,
                icon: "error"
            });
        });
});



 $(document).on('click', '.view-button', function() {
        const id = $(this).data('id');

        // Get booking data
        axios.get(`/booking-history/${id}`)
        .then(response => {
            const bookingData = response.data.data;
            
            // Fill modal fields
            $('#viewTitle').text(bookingData.title || '--');
            $('#viewUser').text(bookingData.user?.name || '--');
            $('#viewPurpose').text(bookingData.purpose || '--');
            $('#viewFromDate').text(bookingData.from_date || '--');
            $('#viewToDate').text(bookingData.to_date || '--');
            $('#viewDestination').text(bookingData.destination || '--');
            $('#viewDriver').text(bookingData.driver?.name || '--');
            $('#viewCar').text(bookingData.car?.name || '--');
            $('#viewOdometer').text(bookingData.odo_meter || '--');
            $('#viewRemarks').text(bookingData.remarks || '--');

            // Image attachment
            if (bookingData.img_attachment) {
                const attachment_url = `/storage/${bookingData.img_attachment}`;
                $('#viewImg').attr('src', attachment_url).removeClass('d-none');
            } else {
                $('#viewImg').addClass('d-none');
            }

            // Approve/Reject buttons
            if (bookingData.status === 'pending') {
                $('#footer').removeClass('d-none');
                $('#footer .btn-approve').data('id', bookingData.id);
                $('#footer .btn-reject').data('id', bookingData.id);
            } else {
                $('#footer').addClass('d-none');
            }

            myViewModal.modal('show');
        })
        .catch(error => {
            Swal.fire('Error', error.response?.data?.message || error.message, 'error');
        });
    });

       
   // Update Booking
    $(document).on('click', '#update-button', function (e) {
        e.preventDefault();

        // Reset previous errors
        $('.text-danger').addClass('d-none');
        $('.form-control, .form-select').removeClass('is-invalid');

        // Prepare FormData
        const formData = new FormData();
        formData.append('title', $('#title').val());
        formData.append('purpose', $('#purpose').val());
        formData.append('from_date', $('#from_date').val());
        formData.append('to_date', $('#to_date').val());
        formData.append('destination', $('#destination').val());
        formData.append('driver_id', $('#driver_id').val());
        formData.append('car_id', $('#car_id').val());
        formData.append('remarks', $('#remarks').val());
        formData.append('is_approved', '1'); // optional
        formData.append('_method', 'PUT'); // For Laravel
        

        axios.post(`booking-history/${bookingId}`, formData)
            .then(response => {
                $('#myTable').DataTable().ajax.reload(); // reload table
                myEditModal.modal('hide'); // hide modal
                Swal.fire({
                    title: "Success!",
                    text: response.data.message,
                    icon: "success"
                });
            })
          .catch(error => {

    if (error.response?.status === 422) {

        // 🔥 If it's a custom conflict response
        if (error.response.data.message && !error.response.data.errors) {
            Swal.fire({
                title: error.response.data.title || "Oops!",
                text: error.response.data.message,
                icon: "error",
                confirmButtonText: error.response.data.buttonText || "OK"
            });
            return;
        }

        // 🔹 Normal Laravel validation errors
        const errors = error.response.data.errors;
        for (const field in errors) {
            $(`#${field}`).addClass('is-invalid');
            $(`#${field}_error`)
                .removeClass('d-none')
                .text(errors[field][0]);
        }

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


// Approve button click
    $(document).on('click', '#footer .btn-approve', function() {
        const id = $(this).data('id');
        if (!id) return Swal.fire('Error', 'Booking ID not found', 'error');

        axios.post(`/booking-approve/${id}`)
        .then(response => {
            Swal.fire('Success', response.data.message, 'success');
            dataTable.ajax.reload();
            myViewModal.modal('hide');
        })
        .catch(error => {
            Swal.fire('Error', error.response?.data?.message || error.message, 'error');
        });
    });






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
   
</script>
@endsection