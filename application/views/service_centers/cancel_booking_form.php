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
                            <input class="inputradio  <?php if($data1->reason==_247AROUND_WRONG_PINCODE_CANCEL_REASON){echo 'wrong_pincode';} ?>    <?php if($data1->reason==_247AROUND_WRONG_NOT_SERVICABLE_CANCEL_REASON){echo 'not_servicable';} ?>"   data-attr   type="radio" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
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
                     <textarea class="form-control" id="cancellation_reason_text" name="cancellation_reason_text" value="<?php echo set_value('cancellation_reason'); ?>" rows="8" ></textarea>
                  </div>
               </div>
                <input type="hidden" name="partner_id" value="<?php if (isset($user_and_booking_details[0]['partner_id'])) {echo $user_and_booking_details[0]['partner_id']; } ?>">
                <input type="hidden" name="city" value="<?php if (isset($user_and_booking_details[0]['city'])) {echo $user_and_booking_details[0]['city']; } ?>">
                <input type="hidden" name="booking_pincode" value="<?php if (isset($user_and_booking_details[0]['booking_pincode'])) {echo $user_and_booking_details[0]['booking_pincode']; } ?>">
                
                  <input type="hidden" name="service_id" value="<?php if (isset($user_and_booking_details[0]['service_id'])) {echo $user_and_booking_details[0]['service_id']; } ?>">
                
               <div>
                <div class="col-md-6 col-md-offset-3">
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
                               case SPARE_OOW_SHIPPED:
                                    $status = CANCEL_PAGE_SPARE_SHIPPED;
                                    $isdisable= true;
                                    break;
                           }
                          
                       }
                   } ?>
                    <?php if($isdisable) { ?>
                    <p style="margin-bottom:60px;"> <strong> <?php echo $status;?></strong></p>
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