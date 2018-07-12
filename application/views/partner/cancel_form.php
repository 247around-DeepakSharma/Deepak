<?php $isdisable = false; if(isset($user_and_booking_details['spare_parts'])){ 
                       foreach($user_and_booking_details['spare_parts'] as $sp){
                           switch ($sp['status']){
                               case SPARE_PARTS_REQUESTED: 
                                    $status = CANCEL_PAGE_SPARE_NOT_SHIPPED_FOR_PARTNER;
                                    $isdisable= true;
                                   break;
                               case SPARE_SHIPPED_BY_PARTNER:
                               case SPARE_DELIVERED_TO_SF:
                               case DEFECTIVE_PARTS_REJECTED:
                               case DEFECTIVE_PARTS_RECEIVED:
                               case DEFECTIVE_PARTS_SHIPPED:
                               case DEFECTIVE_PARTS_PENDING:
                               case _247AROUND_COMPLETED:
                               case DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH:
                                    $status = CANCEL_PAGE_SPARE_SHIPPED;
                                    $isdisable= true;
                                    break;
                           }
                          
                       }
                   } ?>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Cancelled Booking</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/partner/process_cancel_form/<?php echo $user_and_booking_details[0]['booking_id']; ?>/<?php echo $user_and_booking_details[0]['current_status']; ?>" method="POST">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="col-md-4">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="name" name="user_name" value="<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; } ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="booking_id" class="col-md-4">Booking ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_id" name="booking_id" value="<?php if (isset($user_and_booking_details[0]['booking_id'])) {echo $user_and_booking_details[0]['booking_id']; } ?>" readonly="readonly">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="service_name" class="col-md-4">Appliance</label>
                                <div class="col-md-6">
                                    <input type="hidden" name="service" id="services" />
                                    <select disabled="disabled" class="form-control" id="service_id" name="service_id" required>
                                        <option value="<?php if (isset($user_and_booking_details[0]['service_id'])) {echo $user_and_booking_details[0]['service_id']; } ?>" selected="selected" disabled="disabled">
                                            <?php if (isset($user_and_booking_details[0]['services'])) {echo $user_and_booking_details[0]['services']; } ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="booking_primary_contact_no" class="col-md-4">Mobile</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_primary_contact_no" name="booking_primary_contact_no" value="<?php if (isset($user_and_booking_details[0]['booking_primary_contact_no'])) {echo $user_and_booking_details[0]['booking_primary_contact_no']; } ?>" readonly="readonly">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="booking_date" class="col-md-4">Booking Timeslot</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_timeslot" name="booking_timeslot" value="<?php if (isset($user_and_booking_details[0]['booking_timeslot'])) {echo $user_and_booking_details[0]['booking_timeslot']; } ?>" readonly="readonly">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="booking_date" class="col-md-4">Booking Date</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_date" name="booking_date" value="<?php if (isset($user_and_booking_details[0]['booking_date'])) {echo $user_and_booking_details[0]['booking_date']; } ?>" readonly="readonly">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 col-sm-12">
                            <input type="hidden" class="form-control" name="name" value="<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; }?>">
                            <input type="hidden" class="form-control" id="partner_id" name="partner_id" value="<?php if (isset($user_and_booking_details[0]['partner_id'])) {echo $user_and_booking_details[0]['partner_id']; } ?>">
                            <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                                <label for="cancellation_reason" class="col-md-2">Cancellation Reason</label>
                                <div class="col-md-6">
                                    <?php $count = 1;foreach($reason as $key =>$data1){ ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" class="cancellation_reason" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
                                            <?php  echo $data1->reason;?>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group <?php if( form_error('remarks') ) { echo 'has-error';} ?>">
                                <label for="cancellation_reason" class="col-md-2">Remarks</label>
                                <div class="col-md-6">
                                    <textarea name="remarks" rows="5" class="form-control" placeholder="Enter Remarks"></textarea>
                                </div>

                            </div>
                        </div>
                        <?php if($isdisable) {?>
                        <div class="col-md-6 col-md-offset-3">
                            <p style="color:red"> <?php echo $status; ?></p>
                        </div>
                        <?php } else { ?>
                        <div class="col-md-6 col-md-offset-4">
                            <input type="submit" value="Cancel Booking" class="btn btn-success">
                            </div>
                         <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function check_text() {

        var reason = $('.cancellation_reason:checked').val();

        //var cancel_text = document.myForm.cancellation_reason_text.value;  
        if (reason == undefined) {
            alert("Please select any cancellation reason");
            return false;
        }
    }
    
</script>