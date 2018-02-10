<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script type="text/javascript">

   
   function check_text() {
       var reason = document.myForm.cancellation_reason.value;
       
       if (reason === '' ) {
           alert("Cancellation reason is missing");
           return false;
       } 
        $('#submitform').val("Please wait.....");
              

        return true;
          
       
   }
   
</script>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Cancel Booking
            </h1>
            <form class="form-horizontal" onSubmit="document.getElementById('submitform').disabled=true;" name="myForm" action="<?php echo base_url()?>employee/service_centers/process_cancel_booking/<?php echo $user_and_booking_details[0]['booking_id']; ?>" method="POST">
               <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control" name="name" value="<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; }?>" disabled>
                     <?php echo form_error('name'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                  <label for="cancellation_reason" class="col-md-2">Cancellation Reason</label>
                  <div class="col-md-6">
                     <?php
                        $count = 1;
                        foreach($reason as $key =>$data1){?>
                     <div class="radio">
                        <label>
                        <input type="radio" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
                        <?php  echo $data1->reason;?>
                        </label>
                     </div>
                     <?php } ?>
                  </div>
                  <?php echo form_error('cancellation_reason'); ?>
               </div>
               <div class="form-group">
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                     <textarea class="form-control" id="cancellation_reason_text" name="cancellation_reason_text" value="<?php echo set_value('cancellation_reason'); ?>" rows="8" ></textarea>
                  </div>
               </div>
                <input type="hidden" name="partner_id" value="<?php if (isset($user_and_booking_details[0]['partner_id'])) {echo $user_and_booking_details[0]['partner_id']; } ?>">
               <div>
                <div class="col-md-6 col-md-offset-3">
                   <?php $isdisable = false; if(isset($user_and_booking_details['spare_parts'])){ 
                       foreach($user_and_booking_details['spare_parts'] as $sp){
                           switch ($sp['status']){
                               case "Shipped":
                               case "Defective Part Pending":
                               case "Defective Part Received By Partner":
                               case "Defective Part Rejected By Partner":
                               case "Defective Part Shipped By SF":
                               case "Delivered": 
                                   $isdisable= true;
                                   break;
                           }
                          
                       }
                   } ?>
                    <?php if($isdisable) { ?>
                    <p style="margin-bottom:60px;"> <strong> You are unable to cancel this booking because Spare Parts Shipped.</strong></p>
                    <?php } else { ?>
                    <input type="submit" id="submitform" value="Cancel Booking" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return(check_text())" class="btn btn-danger btn-large">
                    <?php }?>
                     
                    
                  
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>