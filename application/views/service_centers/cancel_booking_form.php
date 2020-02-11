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
             <form class="form-horizontal" onSubmit="document.getElementById('submitform').disabled=true;" name="myForm" action="<?php echo base_url()?>employee/service_centers/process_cancel_booking/<?php if(!empty($user_and_booking_details)){ echo $user_and_booking_details[0]['booking_id']; }?>" method="POST">
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
                        <?php 
                            // Do not allow to re-assign booking if Booking is in InProcess state
                            if($data1->reason == _247AROUND_WRONG_PINCODE_CANCEL_REASON && empty($user_and_booking_details['allow_reshedule'])){
                                continue;
                            } 
                        ?>
                     <div class="radio">
                        <label>
                           <input class="inputradio <?php if($data1->reason==_247AROUND_WRONG_PINCODE_CANCEL_REASON){echo 'wrong_pincode';} ?>    <?php if($data1->reason==CANCELLATION_REASON_WRONG_AREA){echo 'not_servicable';} ?>"   data-attr   type="radio" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" <?php if($this->session->userdata('is_engineer_app') == 1){ if(!empty($engineer_data)){ if($data1->reason == $engineer_data[0]['cancellation_reason']){ echo "checked"; }  }  } ?> required>
                        <?php  echo $data1->reason;?>
                        </label>
                     </div>
                      <?php if($data1->reason==_247AROUND_WRONG_PINCODE_CANCEL_REASON){ ?>
                      <label id="correctpin" class="hide" >
                        Enter  Correct Pincode <input class="form-control" type="text" minlength="6"  maxlength="6"     name="correct_pincode" /> 
                      </label>
                     <?php } 
                      ?> 
                     <?php } ?>
                  </div>
                  <?php echo form_error('cancellation_reason'); ?>
               </div>
               <div class="form-group">
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                     <textarea placeholder="Enter Remarks" class="form-control" id="cancellation_reason_text" name="cancellation_reason_text" value="<?php echo set_value('cancellation_reason'); ?>"  rows="8" ><?php if($this->session->userdata('is_engineer_app') == 1){ if(!empty($engineer_data)){ echo $engineer_data[0]['cancellation_remark']; }  } ?></textarea>
                  </div>
               </div>
                <input type="hidden" name="partner_id" value="<?php if (isset($user_and_booking_details[0]['partner_id'])) {echo $user_and_booking_details[0]['partner_id']; } ?>">
                <input type="hidden" name="city" value="<?php if (isset($user_and_booking_details[0]['city'])) {echo $user_and_booking_details[0]['city']; } ?>">
                <input type="hidden" name="booking_pincode" value="<?php if (isset($user_and_booking_details[0]['booking_pincode'])) {echo $user_and_booking_details[0]['booking_pincode']; } ?>">
                <input type="hidden" name="brand" value="<?php if(!empty($brand)) {echo $brand[0]['appliance_brand'];} else { echo ''; } ?>">
                <input type="hidden" name="service_id" value="<?php if (isset($user_and_booking_details[0]['service_id'])) {echo $user_and_booking_details[0]['service_id']; } ?>">
                <input type="hidden" name="en_closed_date" value="<?php if($this->session->userdata('is_engineer_app') == 1){ if(!empty($engineer_data)){ if(!is_null($engineer_data[0]['closed_date'])){ echo $engineer_data[0]['closed_date']; } }  } ?>">
               <div>
                <div class="col-md-6 col-md-offset-2">
                   <?php $isdisable = false; if(!empty($user_and_booking_details['spare_parts'])) {  
                       foreach($user_and_booking_details['spare_parts'] as $sp){
                           /**
                            * check for non-cancelled spare parts.
                            * modified by : Ankit Rajvanshi
                            */
                           if ($sp['status'] != _247AROUND_CANCELLED) {
                                switch ($sp['status']){
                                   case SPARE_PARTS_REQUESTED: 
                                        $status = CANCEL_PAGE_SPARE_NOT_SHIPPED;
                                        $isdisable= true;
                                    break;
                                default:
                                    if(!empty($sp['shipped_date'])) {
                                        $status = CANCEL_PAGE_SPARE_SHIPPED;
                                        $isdisable= true;
                                    }
                                }    
                            }
                       }
                   } ?>
                    <?php if($isdisable) { ?>
                    <p style="margin-bottom:60px;"> <strong> <?php echo $status;?></strong></p>
                    <?php } else { ?>
                    <input type="submit" id="submitform" value="Cancel Booking" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return(check_text())" class="btn btn-danger btn-large">
                    <?php } ?>
                  
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<script>
    
// $(".wrong_pincode").click(function(){
//    $("#correctpin").removeClass('hide');
// });   
 
 $(".inputradio").click(function(){
     if($(this).hasClass('wrong_pincode')){
          $("#correctpin").removeClass('hide'); 
     }else{
       $("#correctpin").addClass('hide');  
     }
     
 });
    
</script>