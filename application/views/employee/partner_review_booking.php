<link rel="stylesheet" href="http://localhost/247around/css/jquery.loading.css">
<script src="http://localhost/247around/js/jquery.loading.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}
    </style>
    <div class="right_col" role="main">
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
                  <b>Review Bookings - <?php echo $review_for ?></b>
               </h2>
             <form action="<?php echo base_url();?>employee/partner/checked_complete_review_booking" method="post" onsubmit="return check_limit_booking()">
                 <input type="hidden" value="<?php echo $this->session->userdata('partner_id') ?>" name="approved_by">
                  <div class="col-md-12" style="font-size:82%;">
                      <table class="table table-bordered table-hover table-striped">
                        <thead>
                           <tr>
                              <th >S.N.</th>
                              <th>Booking Id</th>
<!--                              <th class="jumbotron" >Service Center </th>-->
                              <th style="text-align: center;">Price Details</th>
                              <th>Amount Due</th>
                              <th>Amount Paid</th>
                              <th>Admin Remarks</th>
                              <th>Vendor Remarks</th>
                              <th>Vendor Cancellation Reason</th>
                              <th><input type="checkbox" id="selecctall" /></th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1; foreach ($charges as $key => $value) { ?>
                            <tr id="<?php echo  "row_".$value['booking_id'] ?>">
                              
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $count; ?></td>
                              <td  style="text-align: left;white-space: inherit;"><?php echo $value['booking_id']." <br/><br/>".$value['booking'][0]['vendor_name']; ?>
                                 
                                  <input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                              </td>

                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo  $value['booking'][0]['partner_id'];?>" >

                              <td style="text-align: left;white-space: inherit; <?php if($value['unit_details'][0]['mismatch_pincode'] == 1){ echo "background-color:red;";}?>">
                                 <table  class="table table-condensed">
                                    <thead>
                                        <th >Brand</th>
                                       <th>Category/Capacity</th>
                                       <th>Model No</th>
                                       <th>Serial Number</th>
                                       <th>Tags</th>
                                       <th>Service Charge</th>
                                       <th>Additional Service Charge</th>
                                       <th>Parts Cost</th>
                                       <th>Upcountry Charges</th>
                                       <th>IS Broken</th>
                                       <th>Vendor Status</th>
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
                              <td style="background: #26b99a;border-color: #26b99a;">
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "partner/booking_details/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              <a style="margin-top:5px;" target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil" aria-hidden="true" title="Edit"></i></a>
                              <button style="margin-top:5px;background-color: #2a3f54;border-color: #2a3f54;" type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button></td>
                           
                            </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings"  style=" background-color: #2a3f54;
                           border-color: #2a3f54;"  class="btn btn-md btn-success" /></center>
                     </div>
                     <?php } ?>
                                     </div>
               </form>
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
            <input type="hidden" value="<?php echo $this->session->userdata('partner_id'); ?>" id="partner_id_cancel">
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks_by_partner()">Send</button>
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
//   $('body').popover({ selector: '[data-popover]', trigger: 'click hover', placement: 'auto', delay: {show: 50, hide: 100}});

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
      url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due + "/"+ flat_upcountry,
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