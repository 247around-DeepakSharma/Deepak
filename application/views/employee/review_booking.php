<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>
<script type="text/javascript">
   $(document).ready(function(){
     $("#selecctall_reschedule").change(function(){
       $(".checkbox_reschedule").prop('checked', $(this).prop("checked"));
       });
   });

</script>
<div id="page-wrapper">
   <div class="row">
      <div style="width:100%;margin-left:10px;margin-right:5px;">
	  <h2 align="left">
	      Review Bookings - Reschedule
         </h2>
         <div class="col-md-12">
	     <form action="<?php echo base_url(); ?>employee/booking/process_review_reschedule_bookings" method="post">
		 <table class="table table-bordered table-hover table-striped">
                  <thead>
                     <tr>
                        <th>SNo.</th>
                        <th>Booking Id</th>
                        <th>Service Center </th>
                        <th>User Name</th>
                        <th>User Phone Number</th>
                        <th>Quantity</th>
                        <th>Booking Date</th>
                        <th>Reschedule Booking Date</th>
                        <th>Reschedule Reason</th>
                        <th  ><input type="checkbox" id="selecctall_reschedule" />  Reschedule</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $sno = 1; foreach ($data as $key => $value) { ?>
                     <tr>
                        <td><?php echo $sno; ?></td>
                        <td><?php echo $value['booking_id'];  ?></td>
                        <td><?php echo $value['service_center_name'];  ?></td>
                        <td><?php echo $value['customername'];  ?></td>
                        <td><?php echo $value['booking_primary_contact_no'];  ?></td>
                        <td><?php echo $value['quantity'];  ?></td>
                        <td><?php echo $value['booking_date']." / ".$value['booking_timeslot'] ;  ?></td>
                        <td><?php echo  date('d-m-Y',strtotime($value['reschedule_date_request']))." / ".$value['reschedule_timeslot_request'] ;  ?>
                           <input type="hidden" name="reschedule_booking_date[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_date_request'] ?>" ></input>
                           <input type="hidden" name="reschedule_booking_timeslot[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_timeslot_request'] ?>" ></input>
                           <input type="hidden" name="reschedule_reason[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_reason'] ?>" ></input>
                        </td>
                        <td><?php echo $value['reschedule_reason'];  ?></td>
                        <td><input id="reschedule_checkbox" type="checkbox"  class="checkbox_reschedule" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                        </td>
                     </tr>
                     <?php $sno++;  } ?>
                  </tbody>
               </table>
               <?php if(!empty($data)){?>
               <div class"col-md-12">
                  <center><input onclick="check_limit_reschedule()" type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
                     border-color: #2C9D9C;" class="btn btn-md btn-success"></input></center>
               </div>
               <?php } ?>
            </form>
         </div>
      </div>
   </div>
   <div >
      <div class="" style="margin-top: 30px;">
         <div class="row">
            <div class="col-md-3 pull-right" style="margin-top:20px;">
               <input type="search" class="form-control pull-right"  id="search" placeholder="search">
            </div>
            <div style="width:100%;margin-left:10px;margin-right:5px;">
               <h2 >
                  <b>Review Bookings - Complete / Cancel</b>
               </h2>
               <form action="<?php echo base_url();?>employee/booking/checked_complete_review_booking" method="post">
                  <div class="col-md-12">
                     <table class="table table-bordered table-hover table-striped">
                        <thead>
                           <tr>
                              <th>S No.</th>
                              <th>Booking Id</th>
                              <th>Service Center </th>
                              <th>User Name</th>
                              <th>Amount Due</th>
                              <th style="text-align: center;">Price Details</th>
                              <th>Total Charge</th>
                              <th>Admin Remarks</th>
                              <th>Vendor Remarks</th>
                              <th>Vendor Cancellation Reason</th>
                              <th><input type="checkbox" id="selecctall" />  Approve</th>
                              <th>View</th>
                              <th>Edit</th>
                              <th>Reject</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1; foreach ($charges as $key => $value) { ?>
                           <tr>
                              <td><?php echo $count; ?></td>
                              <td><?php echo $value['booking_id']; ?><input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                              </td>
                              <td><?php echo $value['service_centres'][0]['name']; ?></td>
                              <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?php echo $value['booking'][0]['phone_number'];?>"><?php echo $value['booking'][0]['name'];?></a>
                              </td>
                              <td><?php echo $value['booking'][0]['amount_due']; ?></td>
                              <td>
                                 <table  class="table table-bordered table-hover table-striped">
                                    <thead>
                                       <th>Category/Capacity</th>
                                       <th>Serial Number</th>
                                       <th>Tags</th>
                                       <th>Service Charge</th>
                                       <th>Additional Service Charge</th>
                                       <th>Parts Cost</th>
                                       <th>Vendor Status</th>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($value['unit_details'] as $key1 => $value1) { ?>
                                       <tr>
                                          <td><p class="<?php echo "category".$count; ?>"><?php echo $value1['appliance_category']."/". $value1['appliance_capacity']; ?></p></td>
                                          <td>
                                             <p class="<?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></p>
                                          </td>
                                          <td><p class="<?php echo "price_tags".$count; ?>"><?php echo $value1['price_tags']; ?></p></td>
                                          <td>
                                             <p id="<?php echo "service_charge".$count; ?>"><?php echo $value1['service_charge']; ?></p>
                                          </td>
                                          <td>
                                             <p id="<?php echo "additional_charge".$count; ?>"><?php echo $value1['additional_service_charge']; ?></p>
                                          </td>
                                          <td>
                                             <p id="<?php echo "parts_cost".$count;?>"><?php echo $value1['parts_cost']; ?></p>
                                          </td>
                                          <td>
                                             <p id="<?php echo "internal_status".$count; ?>"><?php echo $value1['internal_status']; ?></p>
                                          </td>
                                       </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </td>
                              <td><?php echo $value1['amount_paid']; ?></td>
                              <td>
                                 <p id="<?php echo "admin_remarks_".$count; ?>"><?php echo $value['admin_remarks']; ?></p>
                              </td>
                              <input type="hidden" id="<?php echo "admin_remarks".$count;?>" value="<?php echo $value['admin_remarks'];?>"></input>
                              <td>
                                 <p id="<?php echo "service_center_remarks".$count; ?>"><?php echo $value['service_center_remarks']; ?></p>
                              </td>
                              <td>
                                 <p id="<?php echo "cancellation_reason".$count; ?>"><?php echo $value['cancellation_reason']; ?></p>
                              </td>
                              <td><input id="approved_close" type="checkbox"  class="checkbox1" name="approved_booking[]" value="<?php echo $value['booking_id']; ?>"></input></td>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              </td>
                              <td><a target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm">Edit</a></td>
                              <td><button type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Reject</button></td>
                           </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class"col-md-12">
                        <center><input type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
                           border-color: #2C9D9C;"  class="btn btn-md btn-success" onclick="check_limit_booking()"/></center>
                     </div>
                     <?php } ?>
               </form>
               
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal -->
   <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
               <textarea rows="8" class="form-control" id="textarea"></textarea>
            </div>
            <input type="hidden" id="id_no"></input>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks()">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
     $("#selecctall").change(function(){
       $(".checkbox1").prop('checked', $(this).prop("checked"));
       });
   });

</script>
<script type="text/javascript">
   $("#search").keyup(function () {
   var value = this.value.toLowerCase().trim();

   $("table tr").each(function (index) {
       if (!index) return;
       $(this).find("td").each(function () {
           var id = $(this).text().toLowerCase().trim();
           var not_found = (id.indexOf(value) == -1);
           $(this).closest('tr').toggle(!not_found);
           return not_found;
       });
   });
   });
</script>
<style type="text/css">
   .marquee {
   height: 60px;
   width: 60px;
   color: red;
   overflow: hidden;
   position: relative;
   }
   .marquee div {
   display: block;
   width: 60px;
   height: 100%;
   position: absolute;
   overflow: hidden;
   animation: marquee 5s linear infinite;
   }
   .marquee span {
   float: left;
   width: 50%;
   }
   @keyframes marquee {
   0% { left: 0; }
   100% { left: -100%; }
   }
</style>
<script type="text/javascript">
   $('body').popover({ selector: '[data-popover]', trigger: 'click hover', placement: 'auto', delay: {show: 50, hide: 100}});

   function check_limit_booking(){
      
      var count = 0;
      $("#approved_close:checked").each(function(i) {
         count = count+1;
      });
      if(count >40){
         alert("We Can Approve Maximum 40 Bookings");
         return false;
      } else if(count ===0){
         alert("Please select atleast one checkbox");
         return false;
      } else {
          return true;
      }
       
   }

   function check_limit_reschedule(){
      var count = 0;
      $("#reschedule_checkbox:checked").each(function(i) {
         count = count+1;
      });
     
      if(count >40){
         alert("We Can Approve Maximum 40 Bookings");
         return false;
      } else if(count ===0){
         alert("Please select atleast one checkbox");
         return false;
      } else {
          return true;
      }
   }
</script>