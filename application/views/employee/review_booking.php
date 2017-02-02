<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>
<script type="text/javascript">
   $(document).ready(function(){
     $("#selecctall_reschedule").change(function(){
       $(".checkbox_reschedule").prop('checked', $(this).prop("checked"));
       });
   });

</script>
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}
    </style>
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
                        <td><?php echo  date('d-m-Y',strtotime($value['reschedule_date_request'])) ;  ?>
                           <input type="hidden" name="reschedule_booking_date[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_date_request'] ?>" ></input>
<!--                           <input type="hidden" name="reschedule_booking_timeslot[<?php //echo $value['booking_id']; ?>]" value="<?php //echo $value['reschedule_timeslot_request'] ?>" ></input>-->
                           <input type="hidden" name="reschedule_reason[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_reason'] ?>" ></input>
                        </td>
                        <td><?php echo $value['reschedule_reason'];  ?></td>
                        <td><input id="reschedule_checkbox" type="checkbox"  class="checkbox_reschedule" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                        </td>
                        
                        <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($partner_id)) {echo $partner_id; } ?>" >
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
               <h2 >
                  <b>Review Bookings - Complete / Cancel</b>
               </h2>
               <form action="<?php echo base_url();?>employee/booking/checked_complete_review_booking" method="post">
                  <div class="col-md-12" style="font-size:82%;margin-left:-23px;">
                      <table class="table table-bordered table-hover table-striped">
                        <thead>
                           <tr>
                              <th class="jumbotron" >S.N.</th>
                              <th class="jumbotron" >Booking Id</th>
                              <th class="jumbotron" >Service Center </th>
                              <th class="jumbotron" style="text-align: center;">Price Details</th>
                              <th class="jumbotron" >Amount Due</th>
                              <th class="jumbotron" >Amount Paid</th>
                              <th class="jumbotron" >Admin Remarks</th>
                              <th class="jumbotron" >Vendor Remarks</th>
                              <th class="jumbotron" >Vendor Cancellation Reason</th>
                              <th class="jumbotron" ><input type="checkbox" id="selecctall" /></th>
                              <th class="jumbotron" >Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1; foreach ($charges as $key => $value) { ?>
                           <tr>
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $count; ?></td>
                              <td  style="text-align: left;white-space: inherit;"><?php echo $value['booking_id']; ?><input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                              </td>
                              <td style="text-align: left;white-space: inherit;"> <?php echo $value['service_centres'][0]['name']; ?></td>
                              </td>
                              <td style="text-align: left;white-space: inherit;">
                                 <table  class="table table-condensed">
                                    <thead>
                                        <th class="jumbotron" >Brand</th>
                                       <th class="jumbotron" >Category/Capacity</th>
                                       <th class="jumbotron" >Serial Number</th>
                                       <th class="jumbotron" >Tags</th>
                                       <th class="jumbotron" >Service Charge</th>
                                       <th class="jumbotron" >Additional Service Charge</th>
                                       <th class="jumbotron" >Parts Cost</th>
                                       <th class="jumbotron" >Upcountry Charges</th>
                                       <th class="jumbotron" >Vendor Status</th>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($value['unit_details'] as $key1 => $value1) {
                                            $style = "";
                                           
                                            if($value1['customer_net_payable'] > 0 && $value1['internal_status'] == "Completed" 
                                                   && ($value1['service_charge'] + $value1['additional_service_charge'] + $value1['parts_cost']) ==0 ){
                                                $style = "background-color:#FF8080";
                                                
                                            } else if($value1['internal_status'] == "Completed" && $value1['customer_net_payable'] ==0  && 
                                                    ($value1['service_charge'] + $value1['additional_service_charge'] + $value1['parts_cost']) > 0){
                                                 $style = "background-color:#4CBA90";
                                            }
   
                                               ?>
                                       <tr style="<?php echo $style?>">
                                            <td><span class="<?php echo "brand".$count; ?>"><?php echo $value1['appliance_brand']; ?></span></td>
                                           <td><span class="<?php echo "category".$count; ?>"><?php echo $value1['appliance_category']."/". $value1['appliance_capacity']; ?></span></td>
                                          <td>
                                             <span class="<?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></span>
                                          </td>
                                          <td><span class="<?php echo "price_tags".$count; ?>"><?php echo $value1['price_tags']; ?></span></td>
                                          <td>
                                             <span id="<?php echo "service_charge".$count; ?>"><?php echo $value1['service_charge']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "additional_charge".$count; ?>"><?php echo $value1['additional_service_charge']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "parts_cost".$count;?>"><?php echo $value1['parts_cost']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "upcountry".$count;?>"><?php if($key1 ==0){ echo $value1['upcountry_charges'];} ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "internal_status".$count; ?>"><?php echo $value1['internal_status']; ?></span>
                                          </td>
                                       </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['booking'][0]['amount_due']; ?></strong></td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value1['amount_paid']; ?></strong></td>
                              <td style="text-align: left;white-space: inherit;">
                                 <p id="<?php echo "admin_remarks_".$count; ?>"><?php echo $value['admin_remarks']; ?></p>
                              </td>
                              <input type="hidden" id="<?php echo "admin_remarks".$count;?>" value="<?php echo $value['admin_remarks'];?>"></input>
                              <td style="text-align: left;white-space: inherit;font-size:90%">
                                 <p id="<?php echo "service_center_remarks".$count; ?>"><?php echo $value['service_center_remarks']; ?></p>
                              </td>
                              <td style="text-align: left;white-space: inherit;font-size:90%">
                                 <p id="<?php echo "cancellation_reason".$count; ?>"><?php echo $value['cancellation_reason']; ?></p>
                              </td>
                              <td><input id="approved_close" type="checkbox"  class="checkbox1" name="approved_booking[]" value="<?php echo $value['booking_id']; ?>"></input></td>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              <a style="margin-top:5px;" target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil" aria-hidden="true" title="Edit"></i></a>
                              <button style="margin-top:5px;" type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button></td>
                           
                            </tr>
                           <?php $count++; } ?>
                            <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($partner_id)) {echo $partner_id; } ?>" >
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
                           border-color: #2C9D9C;"  class="btn btn-md btn-success" onclick="check_limit_booking()"/></center>
                     </div>
                     <?php } ?>
               </form>
               
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
         alert("Maximum 40 bookings can be completed/cancelled in one time.");
         return false;
      } else if(count ===0){
         alert("Please select at least one booking to complete/cancel.");
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
         alert("Maximum 40 bookings can be rescheduled in one time.");
         return false;
      } else if(count ===0){
         alert("Please select at least one booking to reschedule.");
         return false;
      } else {
          return true;
      }
   }
</script>