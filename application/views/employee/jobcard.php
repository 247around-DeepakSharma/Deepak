<html>
<head>
</head>
<body>
<div id="page-wrapper">
   <div class="container-fluid">
      	<div class="row">
        	<div class="col-lg-12">

               	<h1 class="page-header">
                   <b> Prepare Job Cards</b>
                </h1>

                <?php
                if (isset($success) && $success !== 0) {
    echo '<div class="alert alert-success alert-dismissible" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $success . '</strong>
                   </div>';
                }
?>

                    <form class="form-horizontal" action="<?php echo base_url(); ?>employee/bookingjobcard/prepare_job_card_by_booking_id" method="POST" >
                        <div class="form-group <?php if (form_error('booking_id')) {
    echo 'has-error';
} ?>">
                            <label for="booking_id" class="col-md-1">Booking ID</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control"  name="booking_id" value = "<?php echo set_value('booking_id'); ?>" placeholder="Enter Booking ID" required>
                                <?php echo form_error('booking_id'); ?>
                            </div>
                            <div class="form-group">
                               
                              <input type= "submit"  class="btn btn-danger btn-md" value ="Submit">
                              
                            </div>
                        </div>
            	</form>
           	</div>
        </div>
    </div>
</div>
</body>
</html>