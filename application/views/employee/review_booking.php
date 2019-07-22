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
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>Service Center </th>
                        <th>User Name</th>
                        <th>Original Booking Date</th>
                        <th>Booking Date</th>
                        <th>Reschedule Booking Date</th>
                        <th>Reschedule Reason</th>
                        <th  ><input type="checkbox" id="selecctall_reschedule" />  Reschedule</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $sno = 1; foreach ($data as $key => $value) { ?>
                     <tr>
                        <td><?php echo $sno; if($value['is_upcountry'] == 1) { ?>.<i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $value['assigned_vendor_id'];?>','<?php echo $value['booking_id'];?>', '<?php echo $value['amount_due'];?>', '<?php echo $value['flat_upcountry'];?>')" class="fa fa-road" aria-hidden="true"></i><?php } ?></td>
                        <td>
                            <a href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $value['booking_id'];  ?>" target="_blank"><?php echo $value['booking_id'];  ?></a>
                        </td>
                        <td><?php echo $value['service_center_name'];  ?></td>
                        <td>
                            <?php echo $value['customername'];  ?>
                            <br/>
                            <a href="javascipt:void(0);" onclick="outbound_call(<?php echo $value['booking_primary_contact_no'] ?>)"><?php echo $value['booking_primary_contact_no'];  ?></a>
                        </td>
                       
                        <td><?php echo $value['initial_booking_date'];  ?></td>
                        <td><?php echo $value['booking_date']." / ".$value['booking_timeslot'] ;  ?></td>
                        <td><?php echo  date('d-m-Y',strtotime($value['reschedule_date_request'])) ; ?>
                        <div class="blink">
                           <div class="esclate"><?php echo '<b>' . $value['count_reschedule'] . " times</b><br>";?></div>
                                
                            </div>
                           
                            
                        
                           <input type="hidden" name="reschedule_booking_date[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_date_request'] ?>" ></input>
<!--                           <input type="hidden" name="reschedule_booking_timeslot[<?php //echo $value['booking_id']; ?>]" value="<?php //echo $value['reschedule_timeslot_request'] ?>" ></input>-->
                           <input type="hidden" name="reschedule_reason[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_reason'] ?>" ></input>
                        </td>
                        <td><?php echo $value['reschedule_reason'];  ?></td>
                        <td><input id="reschedule_checkbox" type="checkbox"  class="checkbox_reschedule" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                            <a href="#"><span style="float: right;" class="glyphicon glyphicon-remove" data-toggle="modal" data-target="#review_reject_form" onclick="create_reject_form(<?php echo "'".$value['booking_primary_contact_no']."'";  ?>,<?php echo "'".$value['booking_id']."'";  ?>)"></span> </a>
                        </td>
                        
                        <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo $value['partner_id']; ?>" >
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
              <?php
        if($this->session->flashdata('inProcessBookings')){
            ?>
        <h2 style="line-height: 31px;color: #ff6c95;font-size: 16px;text-align: center;">
            Following Bookings has been updated by someone else , Please check updated booking and then try again<br>
            <?php echo implode(",",$this->session->flashdata('inProcessBookings'))?>
        </h2>
        <?php
        }
        ?>
               <h2 >
                   <b>Review Bookings - Complete / Cancel </b>
               </h2>
             <p><span style="background: #cada71;color:#cada71;">Color</span> Partner Will Review These Bookings <span style="background: #89d4a7;color:#89d4a7;">Color</span> Booking Cancelled by you</p>
             <form action="<?php echo base_url();?>employee/booking/checked_complete_review_booking" method="post" onsubmit="return check_limit_booking()">
                  <div class="col-md-12" style="font-size:82%;margin-left:-23px;">
                      <table class="table table-bordered table-hover table-striped">
                        <thead>
                           <tr>
                              <th class="jumbotron" >S.N.</th>
                              <th class="jumbotron" >Booking Id</th>
<!--                              <th class="jumbotron" >Service Center </th>-->
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
                            <tr id="<?php echo  "row_".$value['booking_id'] ?>" <?php if(in_array($value['booking_id'], $partner_review_bookings)){ echo 'class = "partner_review"'; } ?>>
                              
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $count; ?></td>
                              <td  style="text-align: left;white-space: inherit;"><?php echo $value['booking_id']." <br/><br/>".$value['booking'][0]['vendor_name']; ?>
                                 
                                  <input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                              </td>

                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo $value['booking'][0]['partner_id'];?>" >
                                             <input type="hidden" value="247001" name="approved_by">
                            

                              <td style="text-align: left;white-space: inherit; <?php if($value['unit_details'][0]['mismatch_pincode'] == 1){ echo "background-color:red;";}?>">
                                 <table  class="table table-condensed">
                                    <thead>
                                        <th class="jumbotron" >Brand</th>
                                       <th class="jumbotron" >Category/Capacity</th>
                                       <th class="jumbotron" >Model No</th>
                                       <th class="jumbotron" >Serial Number</th>
                                       <th class="jumbotron" >Tags</th>
                                       <th class="jumbotron" >Service Charge</th>
                                       <th class="jumbotron" >Additional Service Charge</th>
                                       <th class="jumbotron" >Parts Cost</th>
                                       <th class="jumbotron" >Upcountry Charges</th>
                                       <th class="jumbotron" >IS Broken</th>
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
                                           <td><span class="<?php echo "model_number".$count; ?>"><?php echo $value1['model_number']; ?></span></td>
                                           <td>
                                              <?php if(!empty($value1['serial_number_pic'])) {?>
                                              <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $value1['serial_number_pic'];?>"> 
                                                  <span class="<?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></span></a>
                                              <?php } else {?>
                                               <span class="<?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></span>
                                              <?php } ?>
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
                                             <span id="<?php echo "broken".$count;?>"><?php if($value1['is_broken'] == 1){ echo "Yes";} else{ echo "No";} ?></span>
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
                        </tbody>
                     </table>
                    <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
                           border-color: #2C9D9C;"  class="btn btn-md btn-success" /></center>
                     </div>
                                     </div>
                     <?php } ?>
               </form>
               
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
            <input type="hidden" id="id_no">
            <input type="hidden" value="<?php echo $this->session->userdata('partner_id'); ?>" id="admin_id">
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks()">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>
    <div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="open_model1">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upcountry Call</h4>
            </div>
            <div class="modal-body" >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <div id="review_reject_form" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center">Fake Reschedule</h4>
      </div>
      <div class="modal-body">
          <form>
 <div class="form-group">
     <input type="hidden" value="" name="p_number" id="p_number">
     <input type="hidden" value="" name="b_id" id="b_id">
     <input type="hidden" value="<?php echo $this->session->userdata('employee_id'); ?>" name="employee_id" id="employee_id">
     <input type="hidden" value="<?php echo $this->session->userdata('id'); ?>"  name="id" id="id">
  <label for="comment">Remarks:</label>
  <textarea class="form-control" rows="5" id="remarks"></textarea>
</div>
              <button type="submit" align="center" class="btn btn-success" onclick="return cancel_reschedule_request()">Cancel Reschedule</button>
  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
      
      if(Number(count) > 40){
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
   
   function open_upcountry_model(sc_id, booking_id, amount_due, flat_upcountry){
      
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due +"/"+ flat_upcountry,
      success: function (data) {
       $("#open_model1").html(data); 
      
       $('#myModal1').modal('toggle');
    
      }
    });
    }
</script>
<style >
    @keyframes blink {
      50% { opacity: 0.0; }
    }
    @-webkit-keyframes blink {
      50% { opacity: 0.0; }
    }
    .blink {
      animation: blink 1s step-start 0s infinite;
      -webkit-animation: blink 1s step-start 0s infinite;
    }
    
    .esclate {
    width: auto;
    height: 17px;
   
    color: #F73006;
    /* transform: rotate(-26deg); */
    margin-left: 0px;
    font-weight: bold;
    margin-right: 0px;
    font-size: 12px;
}
</style>
<script>
    function create_reject_form(p_number,booking_id){
        $("#p_number").val(p_number);
        $("#b_id").val(booking_id);
    }
    function cancel_reschedule_request(){
        var p_number  = $("#p_number").val();
        var booking_id  = $("#b_id").val();
        var remarks  = $("#remarks").val();
        var employeeID = $("#employee_id").val();
        var id = $("#id").val();
        var url =  '<?php echo base_url();?>employee/booking/cancel_rescheduled_booking';
         $.ajax({
            type: 'POST',
            url: url,
            data: {p_number: p_number, remarks: remarks, employeeID: employeeID, id: id,booking_id:booking_id},
            success: function (response) {
                console.log(response);
                if(response == true){
                    alert("Reschedule Cancelled Successfully");
                }
                else{
                    alert("Something Went Wrong Please Try Again");
                }
                location.reload();
            }
        });
        return false;
    }
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
    
        if (confirm_call == true) {
    
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);
    
                }
            });
        } else {
            return false;
        }
    
    }
  </script>
  <style>
      .partner_review{
             background: #cada71 !important;
      }
      </style>
