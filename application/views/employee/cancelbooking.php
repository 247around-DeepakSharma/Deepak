<script type="text/javascript">
//function check(){
//    var reason = document.myForm.cancellation_reason.value;
//    if(reason =='Other'){
//      document.getElementById("cancellation_reason_text").disabled = false;            
//    }else{
//    document.getElementById("cancellation_reason_text").disabled = true;
//    }
//}

function check_text(){
  var reason = document.myForm.cancellation_reason.value;
  //var cancel_text = document.myForm.cancellation_reason_text.value;  

    if(reason === '' ){
      alert("Please select checkbox to select cancellation reason");
      return false;
    }

}
</script>
                   <?php $isdisable = false; if(isset($user_and_booking_details['spare_parts'])){ 
                       foreach($user_and_booking_details['spare_parts'] as $sp){
                           switch ($sp['status']){
                               case SPARE_PARTS_REQUESTED: 
                                    $status = CANCEL_PAGE_SPARE_NOT_SHIPPED;
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
<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              
              <h1 class="page-header">
                    <?php if(isset($internal_status)){ echo "Cancel Query "; } else { echo "Cancel Booking"; } ?>
              </h1>
                
                <?php if($isdisable || !empty($is_invoice_generated)) {?>
                <div class="alert alert-warning">
                    <span style="font-weight:bold;color:red;"><?php if($isdisable) { echo $status; } ?></span>
                    <span style="font-weight:bold;color:red;"><?php if(!empty($is_invoice_generated)) { echo UNABLE_TO_COMPLETE_BOOKING_INVOICE_GENERATED_MSG;} ?></span>
                </div>
                <?php } ?>
               <div class="row">
                <div class="col-md-6">
                  <div class="form-group-cancel">
                      <label for="name" class="col-md-4">Name</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="name" name="user_name" value = "<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; } ?>" readonly="readonly">
                         
                        </div>
                  </div>

                  <div class="form-group-cancel">
                           <label for="booking_id" class="col-md-4">Booking ID</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_id" name="booking_id" value = "<?php if (isset($user_and_booking_details[0]['booking_id'])) {echo $user_and_booking_details[0]['booking_id']; } ?>" readonly="readonly">
                           </div>
                        </div>

                        <div class="form-group-cancel <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                           <label for="service_name" class="col-md-4">Appliance</label>
                           <div class="col-md-6">
                              <input type="hidden" name="service" id="services"/>
                              <select type="text" disabled="disabled"  class="form-control"  id="service_id" name="service_id" required>
                                 <option value="<?php if (isset($user_and_booking_details[0]['service_id'])) {echo $user_and_booking_details[0]['service_id']; } ?>" selected="selected" disabled="disabled"><?php if (isset($user_and_booking_details[0]['services'])) {echo $user_and_booking_details[0]['services']; } ?></option>
                              </select>
                           </div>
                        </div>

                </div>

                <div class="col-md-6">

                  <div class="form-group-cancel">
                           <label for="booking_primary_contact_no" class="col-md-4">Mobile</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if (isset($user_and_booking_details[0]['booking_primary_contact_no'])) {echo $user_and_booking_details[0]['booking_primary_contact_no']; } ?>" readonly="readonly">
                           </div>
                        </div>

                        <div class="form-group-cancel">
                           <label for="booking_date" class="col-md-4">Booking Timeslot</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_timeslot" name="booking_timeslot" value = "<?php if (isset($user_and_booking_details[0]['booking_timeslot'])) {echo $user_and_booking_details[0]['booking_timeslot']; } ?>" readonly="readonly">
                           </div>
                        </div>

                        <div class="form-group-cancel">
                           <label for="booking_date" class="col-md-4">Booking Date</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_date" name="booking_date" value = "<?php if (isset($user_and_booking_details[0]['booking_date'])) {echo $user_and_booking_details[0]['booking_date']; } ?>" readonly="readonly">
                           </div>
                        </div>
                  </div>
              </div>
              <hr>
              <!-- </div> -->

              <form style="padding-bottom: 100px;" class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/booking/process_cancel_form/<?php echo $user_and_booking_details[0]['booking_id']; ?>/<?php echo $user_and_booking_details[0]['current_status']; ?>" method="POST" >
                    <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($user_and_booking_details[0]['partner_id'])) {echo $user_and_booking_details[0]['partner_id']; } ?>" >
                    <input type="hidden" class="form-control"  name="name" value = "<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; }?>">
             
                 <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                  <label for="cancellation_reason" class="col-md-2">Cancellation Reason</label>
                  <div class="col-md-6">
                   <?php 
                   $flag = 0;
                   if($user_and_booking_details[0]['current_status'] == "FollowUp"){
                      if (strstr($user_and_booking_details[0]['booking_id'], "SS") == FALSE) {
                          $flag = 0;
                      } else {
                        $flag = 1;
                      }

                   } else {

                    $flag = 0;
                   }


                  ?>
                     <?php
                        if($flag == 0){
                        $count = 1;
                        foreach($reason as $key =>$data1){?>
                     <div class="radio">
                        <label>
                        <input type="radio" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
                        <?php  echo $data1->reason;?>
                        </label>
                     </div>
                     <?php } } else { 
                      $count = 1;
                      foreach($internal_status as $key =>$data1){?>
                    
                     <div class="radio">
                        <label>
                        <input type="radio"  name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->status;?>" required>
                        <?php  echo $data1->status;?>
                        </label>
                     </div>

                    <?php  } } ?>
                    <?php echo form_error('cancellation_reason'); ?>
                  </div>
               </div>
                <?php //if($flag == 0){?>
                <div class="form-group <?php if( form_error('cancellation_reason_text') ) { echo 'has-error';} ?>">                
                  <label for="cancellation_reason" class="col-md-2">Remarks </label>
                  <div class="col-md-6">
                    <textarea class="form-control"  id="cancellation_reason_text" name="cancellation_reason_text" placeholder="Please Enter Remarks" value = "<?php echo set_value('cancellation_reason'); ?>" rows="8" ></textarea>                            
                  </div>
                </div>
                <?php  //}?>
 
                    <div class="col-md-6 col-md-offset-3">
                    <?php if(!$isdisable && empty($is_invoice_generated)) {?>
                        <input type="submit" value="<?php if(isset($internal_status)){ echo "Cancel Query "; } else { echo "Cancel Booking"; } ?>" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return(check_text())" class="btn btn-danger btn-large">
                    <?php } ?>
                  
                </div>
              </form>
              
            </div>
        </div>
    </div>
</div>