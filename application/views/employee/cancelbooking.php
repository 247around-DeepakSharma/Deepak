<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php 

              $booking ='';

              if (isset($data['booking_id'])) 
                  {
                    foreach ($data['booking_id'] as $key => $data) 
                    $booking = $data['booking_id'];
                  }?>

                  <?php if (isset($data1['booking_id'])) {
                    foreach ($data1['booking_id'] as $key => $data1) 
                    $booking1 = $data1['booking_id'];
                    }?>
              <h1 class="page-header">
                    Cancel Booking 
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_cancel_booking_form/<?php echo $booking; ?>" method="POST" >

              <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">
                  <label for="cancellation_reason" class="col-md-2">Cancelation Reason</label>
                  <div class="col-md-6">
                    <?php foreach($reason as $key =>$data1){?>
                    <input style="width:100px;height:20px;" type="radio" class="form-control" name="cancellation_reason" value="<?php  echo $data1->reason;?>" required><?php  echo $data1->reason;?>
                    <?php } ?> 
                    <?php echo form_error('cancellation_reason'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('cancellation_reason') ) { echo 'has-error';} ?>">

                <input type ="hidden" value = <?php echo $book_id; ?> name ="booking_id">
                  <label for="cancellation_reason" class="col-md-2"> </label>
                  <div class="col-md-6">
                    <textarea class="form-control"  name="cancellation_reason_text" value = "<?php echo set_value('cancellation_reason'); ?>"></textarea>
                            <?php echo form_error('cancellation_reason'); ?>
                    <?php echo form_error('cancellation_reason'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('closing_remarks') ) { echo 'has-error';} ?>">
                    <label for="closing_remarks" class="col-md-2">Closing Remarks</label>
                    <div class="col-md-6">
                        <textarea class="form-control"  name="closing_remarks" ></textarea>
                        <?php echo form_error('closing_remarks'); ?>
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
