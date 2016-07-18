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
         <h1 align="left">
            Review Bookings - Reschedule
         </h1>
         <div class="col-md-12">
            <form action="<?php echo base_url();?>employee/booking/process_reschedule_booking" method="post">
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
                        <td><input type="checkbox"  class="checkbox_reschedule" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                        </td>
                     </tr>
                     <?php $sno++;  } ?>
                  </tbody>
               </table>
               <?php if(!empty($data)){?>
               <div class"col-md-12">
                  <center><input type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
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
               <form action="<?php echo base_url();?>employee/new_booking/complete_booking" method="post">
                  <div class="col-md-12">
                     <table class="table table-bordered table-hover table-striped">
                        <thead>
                           <tr>
                              <th>S No.</th>
                              <th>Booking Id</th>
                              <th>Service Center </th>
                              <th>User Name</th>
                              <th>Category/ Capacity</th>
                              <th>Amount Due</th>
                              <th>Service Charge</th>
                              <th>Additional Service Charge</th>
                              <th>Parts Cost</th>
                              <th>Total Charge</th>
                              <th>Admin Remarks</th>
                              <th>Vendor Status</th>
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
                              <td><?php echo $value['booking_id']; ?><input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>"></input></td>
                              <td><?php echo $value['service_centres'][0]['name']; ?></td>
                              <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?php echo $value['booking'][0]['phone_number'];?>"><?php echo $value['booking'][0]['name'];?></a></td>
                              <?php 
                                 if (isset($value['query2'])) 
                                 {
                                   $brand  ="";
                                   $category="";
                                   $capacity="";
                                   
                                   for($i=0; $i< $value['booking'][0]['quantity']; $i++)
                                   {
                                     $brand .=$value['query2'][$i]['appliance_brand'].",";
                                     $category .=$value['query2'][$i]['appliance_category'].",";
                                     $capacity .=$value['query2'][$i]['appliance_capacity'].",";
                                 
                                   }
                                 } 
                                 ?>
                              <td><?php echo $category." / ". $capacity; ?></td>
                              <td><?php echo $value['booking'][0]['amount_due']; ?></td>
                              <td>
                                 <p id="<?php echo "service_charge".$count; ?>"><?php echo $value['service_charge']; ?></p>
                              </td>
                              <td>
                                 <p id="<?php echo "additional_charge".$count; ?>"><?php echo $value['additional_service_charge']; ?></p>
                              </td>
                              <td>
                                 <p id="<?php echo "parts_cost".$count;?>"><?php echo $value['parts_cost']; ?></p>
                              </td>
                              <td><?php echo ($value['parts_cost'] + $value['additional_service_charge'] + $value['service_charge']); ?></td>
                              <td>
                                 <p id="<?php echo "admin_remarks_".$count; ?>"><?php echo $value['admin_remarks']; ?></p>
                              </td>
                              <input type="hidden" id="<?php echo "admin_remarks".$count;?>" value="<?php echo $value['admin_remarks'];?>"></input>
                              <td>
                                 <p id="<?php echo "internal_status".$count; ?>"><?php echo $value['internal_status']; ?></p>
                              </td>
                              <td>
                                 <p id="<?php echo "service_center_remarks".$count; ?>"><?php echo $value['service_center_remarks']; ?></p>
                              </td>
                              <td>
                                 <p id="<?php echo "cancellation_reason".$count; ?>"><?php echo $value['cancellation_reason']; ?></p>
                              </td>
                              <td><input type="checkbox"  class="checkbox1" name="approve[]" value="<?php echo $value['booking_id']; ?>"></input></td>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              </td>
                              <td><button type="button" id="<?php echo $count;?>" class="btn btn-info btn-sm open-AddBookingDialog" data-toggle="modal" data-target="#myModal">Edit</button></td>
                              <td><button type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Reject</button></td>
                           </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class"col-md-12">
                        <center><input type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
    border-color: #2C9D9C;"  class="btn btn-md btn-success"></input></center>
                     </div>
                     <?php } ?>
               </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Modal -->
   <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title2">Modal Header</h4>
            </div>
            <div class="modal-body" id="edit_form">
               <form class="form-horizontal"  method="post" action="#">
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Service Charge</label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" id="input_service_charge" name="service_charge" value = ""  >
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Addtional Charge</label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" id="input_additional_charge" name="additional_charge" value = ""  >
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Parts Cost</label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" id="input_parts_cost" name="parts_cost" value = ""  >
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Total Charge</label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" id="input_total_charge" name="total_charge" value = ""  readonly >
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Vendor Status</label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" id="input_internal_status" name="internal_status" value = ""   >
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Vendor Cancellation Reason</label>
                     <div class="col-md-6">
                        <textarea type="text" class="form-control" id="input_cancellation_reason" name="cancellation_reason" value = ""  ></textarea> 
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Vendor Remarks</label>
                     <div class="col-md-6">
                        <textarea type="text" class="form-control" id="input_service_center_remarks" name="service_center_remarks" value = ""  ></textarea> 
                     </div>
                  </div>
                  <div class="form-group ">
                     <label for="name" class="col-md-3">Admin Remarks</label>
                     <div class="col-md-6">
                        <textarea type="text" class="form-control" id="input_admin_remarks" name="input_admin_remarks" value = ""   ></textarea>
                     </div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="approve_booking()">Approve</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
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
</script>