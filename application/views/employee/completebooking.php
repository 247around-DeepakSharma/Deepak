
<script>
    function total_amount_check()
    {
        var servicecharge = $('#service_charge').val();
        var additionalservicecharge = $('#additional_service_charge').val();
        var partscost = $('#parts_cost').val();
        if(servicecharge == 0 && additionalservicecharge == 0 && partscost == 0)
        {
            alert("Service Charge, Additional Service Charge & Parts Cost are filled as 0. Is it ok! ");
        }
    }
</script>
<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

            <?php if (isset($data['booking_id'])) {
               foreach ($data['booking_id'] as $key => $data) 
                    $booking = $data['booking_id'];
                   
               
               }?>
               <?php if (isset($data1['booking_id'])) {
                    foreach ($data1['booking_id'] as $key => $data1) 
                    $booking1 = $data1['booking_id'];
                    }?>

                <?php 
                if (isset($query2)) 
                {
                  $brand  ="";
                  $category="";
                  $capacity="";
                  
                  for($i=0; $i<$data['quantity']; $i++)
                  {
                    $brand .=$query2[$i]['appliance_brand'].",";
                    $category .=$query2[$i]['appliance_category'].",";
                    $capacity .=$query2[$i]['appliance_capacity'].",";

                  }
                } 
                ?>
               
                <h1 class="page-header"> 
                    Complete Booking 
                </h1>
                <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_complete_booking_form/<?php echo $booking ?>" method="POST" >

                <div class="form-group <?php if( form_error('user_id') ) { echo 'has-error';} ?>">
                        <div class="col-md-6">
                            <input type="hidden" class="form-control"  name="user_id" value = "<?php if (isset($data['user_id'])) {echo $data['user_id']; }?>"  disabled>
                            <?php echo form_error('user_id'); ?>
                        </div>
                    </div>

                    
                <!--<input type="hidden" class="form-control"  name="vendor_name" value = "<?php if (isset($vendor_details[0]['name'])) {echo $vendor_details[0]['name']; }?>">
                            
                <input type="hidden" class="form-control"  name="vendor_city" value = "<?php if (isset($vendor_details[0]['district'])) {echo $vendor_details[0]['district']; }?>">-->
                    
                    
                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                        <label for="name" class="col-md-2">User Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                            <?php echo form_error('name'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                        <label for="phone_number" class="col-md-2">User Phone No:</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="phone_number" value = "<?php if (isset($data1['phone_number'])) {echo $data1['phone_number']; }?>"  disabled>
                            <?php echo form_error('phone_number'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                        <label for="appliance_brand" class="col-md-2">Brand</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_brand" value = "<?php echo $brand;?>"  disabled>
                            <?php echo form_error('appliance_brand'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                        <label for="appliance_category" class="col-md-2">Category</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_category" value = "<?php echo $category;?>"  disabled>
                            <?php echo form_error('appliance_category'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                        <label for="capacity" class="col-md-2">Capacity</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_capacity" value = "<?php echo $capacity;?>"  disabled>
                            <?php echo form_error('appliance_capacity'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('quantity') ) { echo 'has-error';} ?>">
                        <label for="quantity" class="col-md-2">Quantity</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="quantity" value = "<?php if (isset($data['quantity'])) {echo $data['quantity']; }?>" disabled>
                           <?php echo form_error('quantity'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                        <label for="booking_date" class="col-md-2">Booking Date</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($data['booking_date'])) {echo $data['booking_date']; }?>"  disabled>
                            <?php echo form_error('booking_date'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                        <label for="booking_timeslot" class="col-md-2">Booking Time</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_timeslot" value = "<?php if (isset($data['booking_timeslot'])) {echo $data['booking_timeslot']; }?>"  disabled>    
                            <?php echo form_error('booking_timeslot'); ?>
                        </div>
                    </div>

                    <div>
                    <div class="form-group <?php if( form_error('booking_remarks') ) { echo 'has-error';} ?>">
                <label style="width:200px;" for="booking_remarks" class="col-md-2">Booking Remark:</label>
                <div class="col-md-2">
                <textarea style="height:100px;width:600px;" type="text" class="form-control"  name="booking_remarks" value = "<?php if (isset($data['booking_remarks'])){echo $data['booking_remarks'];}?>" disabled><?php if (isset($data['booking_remarks'])){echo $data['booking_remarks'];}?></textarea>
                            <?php echo form_error('booking_remarks'); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group <?php if( form_error('amount_due') ) { echo 'has-error';} ?>">
                        <label for="amount_due" class="col-md-2">Amount Due</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="amount_due" value = "<?php if (isset($data['amount_due'])) {echo $data['amount_due']; }?>"  disabled>    
                            <?php echo form_error('amount_due'); ?>
                        </div>
                    </div>

                 <?php if (strstr($booking, "SS") == TRUE) { ?>
                  <div class="form-group <?php if( form_error('internal_status') ) { echo 'has-error';} ?>">
                  <label for="internal_status" class="col-md-2">Internal Status</label>
                  <div class="col-md-10">
                    <?php foreach($internal_status as $status){?>
                    <div style="float:left;">
                        <input type="radio" class="form-control" name="internal_status" id="internal_status"
                            value="<?php  echo $status->status;?>" style="height:20px;width:20px;margin-left:20px;" required>
                        <?php  echo $status->status;?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <?php } ?> 
                    <?php echo form_error('internal_status'); ?>
                  </div>
                </div>  
                <?php } else { ?>
                    <input type ="hidden" name ="internal_status" value = "Completed" >
                <?php } ?>
                                     
                <div class="form-group <?php if( form_error('service_charge') ) { echo 'has-error';} ?>">
                        <label for="service_charge" class="col-md-2">Service Charge</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control"  id="service_charge" name="service_charge" value ="<?php echo set_value('service_charge'); ?>"  placeholder="Enter Service Charge" required>
                            <?php echo form_error('service_charge'); ?>
                        </div>
                        <label for="service_charge" class="col-md-2">Collected By</label>
                        <div class="col-md-4">
                            <input type="radio" id="vendor" name="service_charge_collected_by" value="Vendor" required>Vendor &nbsp;
                            <input type="radio" id="around" name="service_charge_collected_by" value="Around" required>Around &nbsp;
                            <input type="radio" id="snapdeal" name="service_charge_collected_by" value="Snapdeal" required>Snapdeal &nbsp;
                        </div>
                </div>
                

                    <div class="form-group <?php if( form_error('additional_service_charge') ) { echo 'has-error';} ?>">
                        <label for="additional_service_charge" class="col-md-2">Additional Service Charge</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="additional_service_charge" name="additional_service_charge" value ="<?php echo set_value('additional_service_charge'); ?>"  placeholder="Enter Additional Service Charge" required>
                            <?php echo form_error('additional_service_charge'); ?>
                        </div>
                        <label for="service_charge" class="col-md-2">Collected By</label>
                        <div class="col-md-4">
                            <input type="radio" id="vendor" name="additional_service_charge_collected_by" value="Vendor" required>Vendor &nbsp;
                            <input type="radio" id="around" name="additional_service_charge_collected_by" value="Around" required>Around &nbsp;
                            <input type="radio" id="snapdeal" name="additional_service_charge_collected_by" value="Snapdeal" required>Snapdeal &nbsp;
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('parts_cost') ) { echo 'has-error';} ?>">
                        <label for="parts_cost" class="col-md-2">Parts Cost</label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="parts_cost" name="parts_cost" value ="<?php echo set_value('parts_cost'); ?>"  placeholder="Enter Parts Cost">
                            <?php echo form_error('parts_cost'); ?>
                        </div>
                        <label for="service_charge" class="col-md-2">Collected By</label>
                        <div class="col-md-4">
                            <input type="radio" id="vendor" name="parts_cost_collected_by" value="Vendor" required>Vendor &nbsp;
                            <input type="radio" id="around" name="parts_cost_collected_by" value="Around" required>Around &nbsp;
                            <input type="radio" id="snapdeal" name="parts_cost_collected_by" value="Snapdeal" required>Snapdeal &nbsp;
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('amount_paid') ) { echo 'has-error';} ?>">
                        <div class="col-md-6">
                            <input type="hidden" class="form-control"  name="amount_paid" value ="<?php echo set_value('amount_paid'); ?>">
                            <?php echo form_error('amount_paid'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('closing_remarks') ) { echo 'has-error';} ?>">
                        <label for="description" class="col-md-2">Closing Remarks</label>
                        <div class="col-md-6">
                            <textarea class="form-control"  name="closing_remarks" ></textarea>
                            <?php echo form_error('closing_remarks'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('rating_star') ) { echo 'has-error';} ?>">
                        <label for="rating_star" class="col-md-2">Star Rating</label>
                        <div class="col-md-4">
                            <Select type="text" class="form-control"  name="rating_star" value="<?php echo set_value('rating_star'); ?>">
                            <option>Select</option>
                            <option>0</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            </Select>
                        </div>
                        <label for="rating_star" class="col-md-2">Vendor Star Rating</label>
                        <div class="col-md-3" style="width:300px;">
                            <Select type="text" class="form-control"  name="vendor_rating_star" value="<?php echo set_value('vendor_rating_star'); ?>">
                            <option>Select</option>
                            <option>0</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            </Select>
                        </div>


                    </div>
                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                        <label for="remark" class="col-md-2">Rating Comment</label>
                        <div class="col-md-4">
                            <textarea class="form-control"  name="rating_comments"></textarea>
                            <?php echo form_error('rating_comments'); ?>
                        </div>

                        <label for="remark" class="col-md-2">Vendor Rating Comment</label>
                        <div class="col-md-3" style="width:300px;">
                            <textarea class="form-control"  name="vendor_rating_comments"></textarea>
                            <?php echo form_error('vendor_rating_comments'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit" name="submit" onclick="total_amount_check();" class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
                </form>
            </div>
        </div>
    </div>
</div>