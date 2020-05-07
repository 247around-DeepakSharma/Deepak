<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"> 
                    Schedule Installation
                </h1>
                <form class="form-horizontal" action="<?php echo base_url()?>main/schedule_booking" method="POST" >
                    
                    <div class="form-group <?php if( form_error('order_id') ) { echo 'has-error';} ?>">
                        <label for="order_id" class="col-md-2">Order ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="order_id" value = "<?php echo $booking['order_id']; ?>" readonly>
                            <?php echo form_error('order_id'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                        <label for="name" class="col-md-2">Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="name" value = "<?php echo $booking['name']; ?>" readonly>
                            <?php echo form_error('name'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                        <label for="booking_primary_contact_no" class="col-md-2">Mobile</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_primary_contact_no" value = "<?php echo $booking['booking_primary_contact_no']; ?>" readonly>
                            <?php echo form_error('booking_primary_contact_no'); ?>
                        </div>
                    </div>
                                                            
                    <div class="form-group <?php if( form_error('booking_alternate_contact_no') ) { echo 'has-error';} ?>">
                        <label for="booking_alternate_contact_no" class="col-md-2">Alternate No</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_alternate_contact_no" value = "<?php echo $booking['booking_alternate_contact_no']; ?>">
                            <?php echo form_error('booking_alternate_contact_no'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('home_address') ) { echo 'has-error';} ?>">
                        <label for="home_address" class="col-md-2">Address</label>
                        <div class="col-md-2">
                            <textarea class="form-control"  name="home_address" rows="3" ><?php echo $booking['home_address']; ?></textarea>
                            <?php echo form_error('home_address'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('city') ) { echo 'has-error';} ?>">
                        <label for="city" class="col-md-2">City</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="city" value = "<?php echo $booking['city']; ?>">
                            <?php echo form_error('city'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                        <label for="pincode" class="col-md-2">Pincode</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="pincode" value = "<?php echo $booking['pincode']; ?>">
                            <?php echo form_error('pincode'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('services') ) { echo 'has-error';} ?>">
                        <label for="services" class="col-md-2">Product</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="services" value = "<?php echo $booking['services']; ?>" readonly>
                            <?php echo form_error('services'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                        <label for="appliance_brand" class="col-md-2">Brand</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="appliance_brand" value = "<?php echo $booking['appliance_brand']; ?>" readonly>
                            <?php echo form_error('appliance_brand'); ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if( form_error('description') ) { echo 'has-error';} ?>">
                        <label for="description" class="col-md-2">Product Details</label>
                        <div class="col-md-2">
                            <textarea class="form-control"  name="description"><?php echo $booking['description']; ?></textarea>
                            <?php echo form_error('description'); ?>
                        </div>
                    </div>
                    
                  <div class="col-md-10">
                     <center><input type= "submit" name="submit" class="btn btn-danger btn-lg" value ="Schedule Booking" style="width:33%"></center>
                  </div>
                </form>
            </div>
        </div>
    </div>
</div>
