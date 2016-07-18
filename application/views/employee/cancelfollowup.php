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
                    Cancel Query
              </h1>

              <form class="form-horizontal" name="myForm" action="<?php echo base_url()?>employee/booking/process_cancel_followup_form/<?php echo $query['booking_id']; ?>" method="POST" >

                <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                  <label for="booking_id" class="col-md-2">Query ID</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_id" value = "<?php echo $query['booking_id']; ?>"  disabled>
                    <?php echo form_error('booking_id'); ?>
                  </div>
                </div>

                <?php if (strstr($query['booking_id'], "SS") == FALSE) { ?>
                   
                    <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                      <label for="cancellation_reason" class="col-md-2">Cancelation Reason</label>
                      <div class="col-md-6">
                        <?php $count = 1;
                        foreach($reasons as $reason){?>
                         <input style="width:100px;height:20px;" type="radio" class="form-control" onclick="check()" name="cancellation_reason" id="<?php echo "cancellation_reason".$count; $count++;?>" value="<?php  echo $reason->reason;?>" required><?php  echo $reason->reason;?>
                        <!-- <input style="width:100px;height:20px;" type="radio" class="form-control" onclick="check()" name="cancellation_reason" id="<?php echo "cancellation_reason".$count; $count++;?>" value="<?php  echo $reason->reason;?>" required ><?php  echo $reason->reason;?> -->
                        <?php } ?> 
                        <?php echo form_error('cancellation_reason'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                    <input type ="hidden" value = <?php echo $query['booking_id']; ?> name ="booking_id">
                      <label for="cancellation_reason_text" class="col-md-2"> </label>
                      <div class="col-md-6">
                        <textarea class="form-control"  name="cancellation_reason_text" id="cancellation_reason_text" value = "<?php echo set_value('cancellation_reason'); ?>"></textarea>                        
                      </div>
                    </div>
                <?php } else { ?>
                    <input type ="hidden" name ="cancellation_reason" value = "Other" >
                <?php } ?>
                
                <?php if($query['source'] =="SS"){?>
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
                <?php } ?>
                
                <div>
                  <center>
                    <input type="submit" value="Save" onclick="return(check_text())" class="btn btn-danger">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/view_queries/FollowUp>Back</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>
