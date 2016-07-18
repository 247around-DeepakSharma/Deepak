<div id="page-wrapper" >
   <div class="container" >
      <div class="panel panel-info" style="margin-top:20px;">
         <div class="panel-heading">Complete Booking</div>
         <div class="panel-body">
         <?php if($booking_history[0]['current_status'] =="Completed"){ $status = "1"; } else { $status = "0"; }?>
            <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/booking/process_complete_booking/<?php echo $booking_id;?>/<?php echo $status; ?>"  method="POST" enctype="multipart/form-data">
               <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-6">
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
                        <div class="form-group <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                           <label for="service_name" class="col-md-4">Service Name *</label>
                           <div class="col-md-6">
                              <input type="hidden" name="service" id="services"/>
                              <select type="text" class="form-control"  id="service_id" name="service_id" required>
                                 <option value="<?php if (isset($booking_history[0]['service_id'])) {echo $booking_history[0]['service_id']; } ?>" selected="selected" disabled="disabled"><?php if (isset($booking_history[0]['services'])) {echo $booking_history[0]['services']; } ?></option>
                              </select>
                           </div>
                        </div>
                        <!--  end col-md-6  -->
                     </div>
                     <!--  start col-md-6  -->
                     <div class="col-md-6">
                        <div class="form-group ">
                           <label for="booking_primary_contact_no" class="col-md-4">Primary Contact Number *</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if (isset($booking_history[0]['booking_primary_contact_no'])) {echo $booking_history[0]['booking_primary_contact_no']; } ?>" readonly="readonly">
                           </div>
                        </div>
                        <div class="form-group ">
                           <label for="source_name" class="col-md-4">Booking Source *</label>
                           <div class="col-md-6">
                              <select type="text" class="booking_source form-control"  id="source_code" name="source_code" required>
                                 <option value="<?php if (isset($booking_history[0]['source'])) {echo $booking_history[0]['source']; } ?>" selected="selected" disabled="disabled"><?php if (isset($booking_history[0]['source'])) {echo $booking_history[0]['source_name']; } ?></option>
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
                  <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                     <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
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
                                 <?php $paid_basic_charges = 0; $paid_additional_charges = 0; $paid_parts_cost=0;foreach ($unit_details['qunatity'] as $key => $price) { ?>
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
                                                      <label><input type="radio" name="<?php echo "booking_status[". $price['unit_id'] . "]"?>"  value="Completed" <?php if($price['booking_status'] =="Completed"){ echo "checked"; } ?> required>Completed<br/>
                                                      <input type="radio" name="<?php echo "booking_status[". $price['unit_id'] . "]"?>"  value="Not Completed" <?php if($price['booking_status'] =="Not Completed"){ echo "checked"; } ?>  required>Not Completed
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
                  <div class ="col-md-10">
                     <div class="form-group col-md-offset-1">
                        <label for="type" class="col-sm-2">Total Customer Paid</label>
                        <div class="col-md-4">
                           <div class="input-group">
                              <div class="input-group-addon">Rs.</div>
                              <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost; ?>" placeholder="Total Price" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="rating_star" class="col-md-2">Star Rating</label>
                        <div class="col-md-4">
                           <Select type="text" class="form-control"  name="rating_stars" value="">
                              <option>Select</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='0'){ echo "selected"; } ?>>0</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='1'){ echo "selected"; } ?>>1</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='2'){ echo "selected"; } ?>>2</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='3'){ echo "selected"; } ?>>3</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='4'){ echo "selected"; } ?>>4</option>
                              <option <?php if($booking_history[0]['rating_stars'] =='5'){ echo "selected"; } ?>>5</option>
                           </Select>
                        </div>
                        <label for="rating_star" class="col-md-2">Vendor Star Rating</label>
                        <div class="col-md-4" >
                           <Select type="text" class="form-control"  name="vendor_rating_stars" value="">
                              <option>Select</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='0'){ echo "selected"; } ?>>0</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='1'){ echo "selected"; } ?>>1</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='2'){ echo "selected"; } ?>>2</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='3'){ echo "selected"; } ?>>3</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='4'){ echo "selected"; } ?>>4</option>
                              <option <?php if($booking_history[0]['vendor_rating_stars'] =='5'){ echo "selected"; } ?>>5</option>
                           </Select>
                        </div>
                     </div>
                     <div class="form-group">
                        <label for="remark" class="col-md-2">Rating Comment</label>
                        <div class="col-md-4">
                           <textarea class="form-control" rows="5" name="rating_comments"><?php echo $booking_history[0]['rating_comments']; ?></textarea>
                        </div>
                        <label for="remark" class="col-md-2">Vendor Rating Comment</label>
                        <div class="col-md-4" >
                           <textarea class="form-control"  rows="5" name="vendor_rating_comments"><?php echo $booking_history[0]['vendor_rating_comments']; ?></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group  col-md-12" >
                  <center>
                     <input type="submit" id="submitform" class="btn btn-info" value="submit">
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
   $(".booking_source").select2();
</script>
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