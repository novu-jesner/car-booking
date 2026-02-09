<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header bg-primary text-dark bg-opacity-25">
          <h5 class="modal-title" id="viewModalLabel">Booking Receipt</h5>
          <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
  
        <!-- Modal Body -->
        <div class="modal-body">
          <div class="text-center mb-4">
            <h6 class="fw-bold">Booking</h6>
            <p class="text-muted">This document serves as proof of booking.</p>
          </div>
          <hr>
          <div class="row">
            <!-- Booking Details -->
            <div class="col-12">
              <table class="table">
                <tbody>
                  <tr>
                    <th class="text-start text-primary">Title:</th>
                    <td class="text-end" id="viewTitle">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">User:</th>
                    <td class="text-end" id="viewUser">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Purpose:</th>
                    <td class="text-end" id="viewPurpose">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">From Date:</th>
                    <td class="text-end" id="viewFromDate">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">To Date:</th>
                    <td class="text-end" id="viewToDate">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Destination:</th>
                    <td class="text-end" id="viewDestination">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Driver:</th>
                    <td class="text-end" id="viewDriver">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Car:</th>
                    <td class="text-end" id="viewCar">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Remarks:</th>
                    <td class="text-end" id="viewRemarks">--</td>
                  </tr>
                  <tr>
                    <th class="text-start text-primary">Odo Meter:</th>
                    <td class="text-end" id="viewOdometer">--</td>
                  </tr>
                </tbody>
              </table>
              
              <img id="viewImg" class="img-fluid d-none" alt="Attachment Image">
            </div>
          </div>
          <hr>
        </div>
  
        <!-- Modal Footer -->
        <div class="modal-footer" id="footer">
          @role('admin')
              @if(Route::currentRouteName() === 'booking-approval.index')
                  <button type="button" class="btn btn-success btn-approve">Approve</button>
                  <button type="button" class="btn btn-danger btn-reject">Reject</button>
              @endif
          @endrole
      </div>  
      </div>
    </div>
  </div>
  