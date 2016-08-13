<div id="page-wrapper" >
   <div class="container" >
      <div class="panel panel-info" style="margin-top:20px;">
         <div class="panel-heading">Complete Booking</div>
         <div class="panel-body">
         
            <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/service_centers/process_complete_booking/<?php echo $booking_id;?>"  method="POST" enctype="multipart/form-data">
               <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-6">
                     <div class="form-group">
                           <label for="name" class="col-md-4">Booking ID</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control" id="booking_id" name="booking_id" value = "<?php if (isset($booking_history[0]['booking_id'])) {echo $booking_history[0]['booking_id']; } ?>" readonly="readonly">
                           </div>
                        </div>
                        <div class="form-group">
                           <label for="name" class="col-md-4">User Name</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control" id="name" name="user_name" value = "<?php if (isset($booking_history[0]['name'])) {echo $booking_history[0]['name']; } ?>" readonly="readonly">
                           </div>
                        </div>
                        <div class="form-group ">
                           <label for="booking_city" class="col-md-4">Booking City *</label>
                           <div class="col-md-6">
                              <select type="text" onchange= "select_state()" class="form-control"  id="booking_city" name="city" required>
                                 <option value="<?php if (isset($booking_history[0]['city'])) {echo $booking_history[0]['city']; } ?>" selected="selected" disabled="disabled"><?php if (isset($booking_history[0]['city'])) {echo $booking_history[0]['city']; } ?></option>
                              </select>
                           </div>
                        </div>
                       
                        <!--  end col-md-6  -->
                     </div>
                     <!--  start col-md-6  -->
                     <div class="col-md-6">
                       <div class="form-group ">
                           <label for="booking_primary_contact_no" class="col-md-4">Order ID </label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="order_id" name="order_id" value = "<?php if (isset($booking_history[0]['order_id'])) {echo $booking_history[0]['order_id']; } ?>" readonly="readonly">
                           </div>
                        </div>
                        <div class="form-group ">
                           <label for="booking_primary_contact_no" class="col-md-4">Primary Contact Number *</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if (isset($booking_history[0]['booking_primary_contact_no'])) {echo $booking_history[0]['booking_primary_contact_no']; } ?>" readonly="readonly">
                           </div>
                        </div>
                        <div class="form-group">
                           <label for="service_name" class="col-md-4">Service Name *</label>
                           <div class="col-md-6">
                              <input type="hidden" name="service" id="services"/>
                              <select type="text" class="form-control"  id="service_id" name="service_id" required>
                                 <option value="<?php if (isset($booking_history[0]['service_id'])) {echo $booking_history[0]['service_id']; } ?>" selected="selected" disabled="disabled"><?php if (isset($booking_history[0]['services'])) {echo $booking_history[0]['services']; } ?></option>
                              </select>
                           </div>
                        </div>
                        <!-- end col-md-6 -->
                     </div>
                  </div>
               </div>
               <!-- row End  -->

               <?php foreach ($bookng_unit_details as $key => $unit_details) { ?>
               <div class="clonedInput panel panel-info " id="clonedInput1">
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group ">
                              <div class="col-md-8 ">
                                 <select type="text" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" required>
                                    <option selected disabled><?php echo $unit_details['brand']; ?></option>
                                 </select>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-md-8 ">
                                 <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  required>
                                    <option selected disabled><?php echo $unit_details['category']; ?></option>
                                 </select>
                              </div>
                           </div>
                           <?php  if(!empty($unit_details['capacity'])){ ?>
                           <div class="form-group">
                              <div class="col-md-8">
                                 <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]" >
                                    <option selected disabled><?php echo $unit_details['capacity']; ?></option>
                                 </select>
                              </div>
                           </div>
                           <?php } ?>
                            <div class="form-group">
                              <div class="col-md-8 ">
                                 <input type="text" id="serial_number" class="form-control" name="serial_number[]" value="<?php echo $unit_details['serial_number']; ?>" placeholder="Please Enter Serial Number" required></input>
                                
                           
                              </div>
                           </div>
                        </div>
                        <div class="col-md-8">
                           <table class="table priceList table-striped table-bordered" name="priceList" >
                              <tr>
                                 <th>Service Category</th>
                                 <th>Amount Due</th>
                                 <th>Customer Basic Charge</th>
                                 <th>Additional Charge</th>
                                 <th>Parts Cost</th> 
                                 <th style="width:265px;">Status</th>
                                
                              </tr>
                              <tbody>
                                 <?php $paid_basic_charges = 0; $paid_additional_charges = 0; $paid_parts_cost=0;foreach ($unit_details['quantity'] as $key => $price) { ?>
                                 <tr>
                                    <td><?php echo $price['price_tags'] ?></td>
                                    <td><?php echo $price['customer_net_payable']; ?></td>
                                    <td>  <input  type="text" class="form-control cost"  name="<?php echo "customer_basic_charge[". $price['unit_id'] . "]"?>"  value = "<?php $paid_basic_charges += $price['customer_paid_basic_charges']; if(!empty($price['customer_paid_basic_charges'])){ echo $price['customer_paid_basic_charges']; } else { echo "0"; } ?>">
                                    </td>
                                    <td>  <input  type="text" class="form-control cost"  name="<?php echo "additional_charge[". $price['unit_id'] . "]"?>"  value = "<?php $paid_additional_charges += $price['customer_paid_extra_charges']; if(!empty($price['customer_paid_extra_charges'])){ echo  $price['customer_paid_extra_charges']; } else { echo "0"; } ?>">

                                    </td>
                                    <td>  <input  type="text" class="form-control cost"  name="<?php echo "parts_cost[". $price['unit_id'] . "]"?>"  value = "<?php $paid_parts_cost += $price['customer_paid_extra_charges']; if(!empty($price['customer_paid_extra_charges'])) { echo $price['customer_paid_extra_charges']; } else { echo "0"; }?>"></td>
                                    <td>
                                       <div class="row">
                                          <div class="col-md-12">
                                             
                                             <div class="form-group ">
                                                <div class="col-md-10">
                                                   
                                                   <div class="radio">
                                                      <label><input type="radio" name="<?php echo "booking_status[". $price['unit_id'] . "]"?>"  value="Completed" <?php if($price['booking_status'] =="Completed"){ echo "checked"; } ?> required><?php if($price['product_or_services']=="Product"){ echo " Delivered";}else { echo " Completed"; } ?><br/>
                                                      <input type="radio" name="<?php echo "booking_status[". $price['unit_id'] . "]"?>"  value="Cancelled" <?php if($price['booking_status'] =="Cancelled"){ echo "checked"; } ?>  required><?php if($price['product_or_services']=="Product"){ echo " Not Delivered";}else { echo " Not Completed"; } ?>
                                                      </label>
                                                   </div>
                                                 
                                                </div>
                                             </div>
                                            
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                                 <?php  } ?>
                              </tbody>
                           </table>
                           <span class="error_msg" style="color: red"></span>
                        </div>
                     </div>
                  </div>
               </div>
               <?php } ?>
               <div class="row">
                  <div class ="col-md-12">
                     <div class="form-group col-md-offset-1">
                        <label for="type" class="col-sm-2">Total Customer Paid</label>
                        <div class="col-md-4">
                           <div class="input-group">
                              <div class="input-group-addon">Rs.</div>
                              <input  type="text" class="form-control" style="height: 36px;" name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost; ?>" placeholder="Total Price" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">

                   <div class="form-group">
                       
                        <label for="remark" class="col-md-2">Booking Remarks</label>
                        <div class="col-md-8" >
                           <textarea class="form-control"  rows="5" name="closing_remarks" readonly><?php if(isset($booking_history[0]['booking_remarks'])){ echo str_replace("<br/>","&#13;&#10;", $booking_history[0]['booking_remarks']); }  ?></textarea>
                        </div>
                     </div>
                    
                     <div class="form-group">
                       
                        <label for="remark" class="col-md-2">Closing Remarks</label>
                        <div class="col-md-8" >
                           <textarea class="form-control"  rows="5" name="closing_remarks"><?php if(isset($charges[0]['service_center_remarks'])){ echo str_replace("<br/>","&#13;&#10;", $charges[0]['service_center_remarks']); }  ?></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group  col-md-12" >
                  <center>
                     <input type="submit" id="submitform" class="btn btn-lg" style="background-color: #2C9D9A;
    border-color: #2C9D9A; color:#fff;" value="submit">
               </div>
               </center>
         </div>
         </form>
         <!-- end Panel Body  -->
      </div>
   </div>
</div>
</div>

<script>
   $("#service_id").select2();
   $("#booking_city").select2();
   
   
   $(document).ready(function () {
   //called when key is pressed in textbox
   $(".cost").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $(".error_msg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
   
   });
   
   
   $(document).on('keyup', '.cost', function(e) {
   
    var price = 0;
    $("input.cost").each(function(){
          price += Number($(this).val());
        
    });
    
   
    $("#grand_total_price").val(price);
   });
</script>
<style type="text/css">
.panel-info>.panel-heading {
    color: #fff;
    background-color: #2C9D9A;
    border-color: #2C9D9A;
}

.panel-info {
    border-color: #bce8f1;
}
</style>