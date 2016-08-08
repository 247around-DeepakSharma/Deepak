<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Booking (<?php echo $count; ?>)</h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                        <tr>
                           <th>S No.</th>
                           <th>Booking Id</th>
                           <th>User Name</th>
                           <th>Phone No.</th>
                           <th>Service Name</th>
                           <th>Booking Date</th>
                           <th>Days Passed</th>
                           <th>247around Remarks</th>
                           <th>View</th>
                           <th>Reschedule</th>
                           <th>Cancel</th>
                           <th>Complete</th>
                           <th>Job Card</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $sn_no = 1; ?>
                        <?php foreach($bookings as $key =>$row){?>
                        <tr>
                           <td>
                              <?php echo $sn_no; ?>
                           </td>
                           <td >
                              <?=$row->booking_id?>
                           </td>
                           <td>
                              <?=$row->customername;?>
                           </td>
                           <td>
                              <?= $row->booking_primary_contact_no; ?>
                           </td>
                           <td>
                              <?= $row->services; ?>
                           </td>
                           <td>
                              <?= $row->booking_date; ?> /
                              <?= $row->booking_timeslot; ?>
                           </td>
                           <td> <?= $row->age_of_booking." day"; ?></td>
                          
                           <td data-popover="true" style="position: absolute; border:0px; width: 12%" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                              <div class="marquee">
                                 <div><span><?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?></span></div>
                              </div>

                           </td>
                           <td><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?=$row->booking_id?>" target='_blank' title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                           <td>
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>
                           <td><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                           </td>
                           <td>
                              <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-success' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                           </td>
                           <td><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm' download><i class="fa fa-download" aria-hidden="true"></i></a></td>
                        </tr>
                        <?php $sn_no++; } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <!-- end  col-md-12-->
      </div>
   </div>
</div>
 <div class="pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>

 <style type="text/css">


.pagination a { color:#474747; border:solid 1px #B6B6B6; padding:6px 9px 6px 9px; background:#E6E6E6; background:-moz-linear-gradient(top, #FFFFFF 1px, #F3F3F3 1px, #E6E6E6); background:-webkit-gradient(linear, 0 0, 0 100%, color-stop(0.02, #FFFFFF), color-stop(0.02, #F3F3F3), color-stop(1, #E6E6E6)); }
.pagination a:hover ,
.pagination strong { background:#2C9D9C; padding:6px 9px 6px 9px; color: #fff; border:solid 1px #2C9D9C;  }
 </style>
      
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
         <h4 class="modal-title" id="myModalLabel">Send Reschedule Request</h4>
      </div>
      <div class="modal-body">
         <form name="myForm1" id="reschedule_form" class="form-horizontal" method="POST">
            <div class="form-group">
               <label for="name" class="col-sm-3">Booking Id </label>
               <div class="col-md-6">
                  <input type="text" name="booking_id"  class="form-control "  id="booking_id" readonly></input>
               </div>
            </div>
            <div class="form-group">
               <label for="name" class="col-sm-3">Booking Date </label>
               <div class="col-md-6">
                  <div class="input-group input-append date" >
                     <input type="text" id="datepicker" class="form-control " style="z-index: 10000;" name="booking_date" required>
                     <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <label for="name" class="col-sm-3">Booking Timeslot </label>
               <div class="col-md-6">
                  <select class="form-control" id="booking_timeslot" name="booking_timeslot" required>
                     <option selected disabled>Select time slot</option>
                     <option>10AM-1PM</option>
                     <option>1PM-4PM</option>
                     <option>4PM-7PM</option>
                  </select>
               </div>
               </div>
             <div class="form-group">
               <label for="name" class="col-sm-3">Reschedule Reason</label>
                <div class="col-md-6">
                   <textarea name="remarks" rows="5" class="form-control" id="remarks" placeholer="Plese Enter Reschedule Reason" ></textarea>
                </div>
                </div>
         </form>
         
         </div>
         <div class="col-md-12" style="margin-top: 5px; margin-bottom: 5px;">
         <p id="error" style="color: red"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="sendRescheduleRequest()">Save changes</button>
         </div>
      </div>
   </div>
</div>
<style type="text/css">
   .marquee {
        height: 100%;
        width: 100%;
        color: red;
        overflow: hidden;
        position: relative;
    }
    
    .marquee div {
        display: block;
        width: 100%;
        height: 22px;
    
        position: relative;
        overflow: hidden;
        animation: marquee 5s linear infinite;
    }
    
    .marquee span {
        
        width: 50%;
    }
    
    @keyframes marquee {
        0% {
            left: 0;
        }
        100% {
            left: -100%;
        }
    }
</style>
<script type="text/javascript">
   $('body').popover({
       selector: '[data-popover]',
       trigger: 'click hover',
       placement: 'auto',
       delay: {
           show: 50,
           hide: 100
       }
   });
   
   $(function() { $( "#datepicker" ).datepicker({  minDate: new Date });});
   
   function setbooking_id(booking_id){
   
      $('#booking_id').val(booking_id);
   }

   function sendRescheduleRequest(){
        var booking_id = $('#booking_id').val();
        var booking_date = $('#datepicker').val();
        var booking_timeslot = $('#booking_timeslot').val();
        var remarks = $('#remarks').val();
        if(booking_date ==""){
          
           $("#error").text('Plese Enter Booking Date');
            return false;
        }

        if(booking_timeslot == null){
            $("#error").text('Plese Enter Booking Timeslot');
            return false;
        }

        if(remarks ==""){
           $("#error").text('Plese Enter Reschedule Reason');
            return false;
        }

         $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/save_reschedule_request',
            data: {booking_id: booking_id, booking_date: booking_date, booking_timeslot: booking_timeslot, remarks: remarks},
            success: function (result) {

                //console.log(result);
                location.reload();
               
            }
         });
   }


 

</script>