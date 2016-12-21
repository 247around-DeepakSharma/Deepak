<script type="text/javascript">
function check(){
    var reason = document.myForm.cancellation_reason.value;
    if(reason =='Other'){
      document.getElementById("cancellation_reason_text").disabled = false;            
    }else{
    document.getElementById("cancellation_reason_text").disabled = true;
    }
}

function check_text(){
  var reason = document.myForm.cancellation_reason.value;
  var cancel_text = document.myForm.cancellation_reason_text.value;  
    if(reason == 'Other' && cancel_text == ""){
      alert("Please enter cancellation reason in other's option");
      return false;
    }
}
</script>

<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              
              <h1 class="page-header">
                    <?php if(isset($internal_status)){ echo "Cancel Query "; } else { echo "Cancel Booking"; } ?>
              </h1>

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

              <form class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/booking/process_cancel_form/<?php echo $user_and_booking_details[0]['booking_id']; ?>/<?php echo $user_and_booking_details[0]['current_status']; ?>" method="POST" >

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
                        <input type="radio"onclick="check()" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
                        <?php  echo $data1->reason;?>
                        </label>
                     </div>
                     <?php } } else { 
                      $count = 1;
                      foreach($internal_status as $key =>$data1){?>
                    
                     <div class="radio">
                        <label>
                        <input type="radio" onclick="check()" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->status;?>" required>
                        <?php  echo $data1->status;?>
                        </label>
                     </div>

                    <?php  } } ?>
                  </div>
               </div>
                <?php if($flag == 0){?>
                <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">                
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                    <textarea class="form-control"  id="cancellation_reason_text" name="cancellation_reason_text" value = "<?php echo set_value('cancellation_reason'); ?>" rows="8" disabled></textarea>                            
                  </div>
                </div>
                <?php } ?>
 
                 <div class="col-md-6 col-md-offset-4">
                  
                     <input type="submit" value="<?php if(isset($internal_status)){ echo "Cancel Query "; } else { echo "Cancel Booking"; } ?>" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return(check_text())" class="btn btn-danger btn-large">
                     <a href="<?php if(isset($internal_status)){ echo base_url()."employee/booking/view_queries/FollowUp/p_all/0/All"; } else { echo base_url()."employee/booking/view";} ?>"><input type="Button" value="Back" class="btn btn-primary"></a>
                  
                </div>
              </form>
            </div>
        </div>
    </div>
</div>