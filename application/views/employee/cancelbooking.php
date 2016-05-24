<script type="text/javascript">
function check(){
    var reason = document.myForm.cancellation_reason.value;
    if(reason =='Other'){
  document.getElementById("cancellation_reason_text").disabled = false;
  }else{
    document.getElementById("cancellation_reason_text").disabled = true;
  }
}
</script>

<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
              
              <h1 class="page-header">
                    Cancel Booking 
              </h1>

              <form class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/booking/process_cancel_booking_form/<?php echo $user_and_booking_details[0]['booking_id']; ?>" method="POST" >

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
                    <input style="width:100px;height:20px;" type="radio" class="form-control" onclick="check()" name="cancellation_reason" id="<?php echo "cancellation_reason".$count; $count++;?>" value="<?php  echo $data1->reason;?>" required ><?php  echo $data1->reason;?>
                    <?php } ?>                     
                  </div>
                </div>

                <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">                
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                    <textarea class="form-control"  id="cancellation_reason_text" name="cancellation_reason_text" value = "<?php echo set_value('cancellation_reason'); ?>" disabled></textarea>                            
                  </div>
                </div>

                <div>
                  <center>
                  <input type="submit" value="Cancel Booking" class="btn btn-danger">
                  <a href="<?php echo base_url();?>employee/booking/view"><input type="Button" value="Back" class="btn btn-primary"></a>
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>
