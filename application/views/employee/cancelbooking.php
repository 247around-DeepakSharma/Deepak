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
            <div class="col-lg-12">
              
              <h1 class="page-header">
                    <?php if(isset($internal_status)){ echo "Cancel Query "; } else { echo "Cancel Booking"; } ?>
              </h1>

              <form class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/booking/process_cancel_form/<?php echo $user_and_booking_details[0]['booking_id']; ?>" method="POST" >

              <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($user_and_booking_details[0]['name'])) {echo $user_and_booking_details[0]['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>
                 <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                  <label for="cancellation_reason" class="col-md-2">Cancelation Reason</label>
                  <div class="col-md-6">
                     <?php
                        $count = 1;
                        foreach($reason as $key =>$data1){?>
                     <div class="radio">
                        <label>
                        <input type="radio"onclick="check()" name="cancellation_reason" id="<?php echo " cancellation_reason ".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required>
                        <?php  echo $data1->reason;?>
                        </label>
                     </div>
                     <?php } ?>
                  </div>
               </div>

                <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">                
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                    <textarea class="form-control"  id="cancellation_reason_text" name="cancellation_reason_text" value = "<?php echo set_value('cancellation_reason'); ?>" rows="8" disabled></textarea>                            
                  </div>
                </div>

                <?php if(isset($internal_status)){ if($user_and_booking_details[0]['source'] =="SS"){?>
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
                <?php }  } else { ?>
                 <input type="hidden" name="internal_status" value="Cancelled"></input>

                <?php } ?>
 
                 <div class="col-md-6 col-md-offset-4">
                  
                     <input type="submit" value="Cancel Booking" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return(check_text())" class="btn btn-danger btn-large">
                     <a href="<?php if(isset($internal_status)){ echo base_url()."employee/booking/view_queries/FollowUp/0/All"; } else { echo base_url()."employee/booking/view";} ?>"><input type="Button" value="Back" class="btn btn-primary"></a>
                  
                  </div>

              </form>
            </div>
        </div>
    </div>
</div>
