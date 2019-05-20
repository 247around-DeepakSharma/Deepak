<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <h1 class="page-header">
                    Reschedule Booking 
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_reschedule_booking_form/<?php echo $data[0]['booking_id'] ?>" method="POST" >

                <div class="col-md-12">
                  <div class="col-md-6">

                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                      <label for="name" class="col-md-4">Name</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="name" value = "<?php if (isset($data[0]['name'])) {echo $data[0]['name']; }?>"  disabled>
                        <?php echo form_error('name'); ?>
                      </div>
                    </div>


                    <div class="form-group">
                      <label for="name" class="col-md-4">Current Booking Date</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="current_booking_date" value = "<?php if (isset($data[0]['booking_date'])) {echo $data[0]['booking_date']; }?>"  disabled>
                        
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4">Current Booking Timeslot</label>
                      <div class="col-md-6">
                        <input type="text"  class="form-control" name="booking_timeslot" value="<?php if (isset($data[0]['booking_timeslot'])) {echo $data[0]['booking_timeslot']; }?>"  disabled>
                        <?php echo form_error('booking_timeslot'); ?>
                      </div>
                    </div>

                    <hr style="width:200%;">
                    <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4"> New Booking Date</label>
                      <div class="col-md-6">
                        <div class="input-group input-append date">
                            <input id="booking_date" class="form-control" placeholder="Select Date" name="booking_date" type="text" value = "<?php echo set_value('booking_date'); ?>" required="">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                            <?php echo form_error('booking_date'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4">New Booking Timeslot</label>
                      <div class="col-md-6">
                        <select name="booking_timeslot" class= "form-control" required="" >
                            <option value="">Select</option>
                            <option>10AM-1PM</option>
                            <option>1PM-4PM</option>
                            <option>4PM-7PM</option>
                        </select>
                        <?php echo form_error('booking_timeslot'); ?>
                      </div>
                    </div>
                      <div class="form-group <?php if( form_error('reason') ) { echo 'has-error';} ?>">
                      <label for="reason" class="col-md-4">Reschedule Reason</label>
                      <div class="col-md-6">
                        <select name="reason" class= "form-control" required="" >
                            <option value="">Select</option>
                            <?php
                                if($data[0]['is_upcountry'] == 1)
                                {
                            ?>
                            <option value="<?php echo RESCHEDULE_FOR_UPCOUNTRY;?>"> <?php echo RESCHEDULE_FOR_UPCOUNTRY;?></option> 
                            <?php
                                }
                            ?>
                             <option value="<?php echo CUSTOMER_ASK_TO_RESCHEDULE;?>"> <?php echo CUSTOMER_ASK_TO_RESCHEDULE;?></option> 
                             <option value="<?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER;?>"> <?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER;?></option> 
                             <?php if(!empty($data['spare_shipped_flag'])){ ?>
                             <option value="<?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF;?>"><?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF;?></option>
                             <?php } ?>
                           
                        </select>
                        <?php echo form_error('reason'); ?>
                      </div>
                    </div>
                     <div class="form-group <?php if( form_error('remark') ) { echo 'has-error';} ?>">
                      <label for="remarks" class="col-md-4">Remarks </label>
                      <div class="col-md-6">
                         <textarea class="form-control remarks"  id="remark" name="remark" value = "" required placeholder="Enter Remarks" rows="5" ></textarea>
                      </div>
                        <?php echo form_error('remark'); ?>
                      </div>
              </div>

                  <div class="col-md-6">

                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                      <label for="Mobile" class="col-md-4">Mobile</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="mobile" value = "<?php if (isset($data[0]['booking_primary_contact_no'])) {echo $data[0]['booking_primary_contact_no']; }?>"  disabled>
                        <?php echo form_error('mobile'); ?>
                      </div>
                      <?php if($c2c){ ?>
                      <div class="col-md-2">
                          <button type="button" onclick="outbound_call(<?php echo $data[0]['booking_primary_contact_no']; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                      </div>
                      <?php } ?>
                    </div>

                    <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                      <label for="booking_id" class="col-md-4">Booking ID</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="booking_id" value = "<?php if (isset($data[0]['booking_id'])) {echo $data[0]['booking_id']; }?>"  disabled>
                        <?php echo form_error('booking_id'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('services') ) { echo 'has-error';} ?>">
                      <label for="services" class="col-md-4">Appliance</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="services" value = "<?php if (isset($data[0]['services'])) {echo $data[0]['services']; }?>"  disabled>
                        <?php echo form_error('services'); ?>
                      </div>
                    </div>
                    <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($data[0]['partner_id'])) {echo $data[0]['partner_id']; } ?>" >
<!--
                    <?php if(isset($reason)){?>  
                <div class="form-group <?php if( form_error('reason') ) { echo 'has-error';} ?>" style="margin-top:50px;">
                  <label for="reason" class="col-md-2">Reason</label>
                  <div class="col-md-6">
                      <ul>
                <?php foreach($reason as $value){ ?>
                    <div class="radio">
                        <label>
                        <input type="radio" name="reason"  value="<?php  echo $value['reason']?>" required>
                        <?php  echo $value['reason'];?>
                        </label>
                     </div>
                       
                <?php }?>
                    </div>
                  </div>
                    <?php }  echo form_error('reason'); ?>
-->
                      
                  </div>

                </div>
               

                <div>
                  <center>
                    <input type="submit" value="Reschedule" class="btn btn-danger">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href='".base_url()."employee/booking/view_bookings_by_status/Pending' >Cancel</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

   $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>'});
   
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