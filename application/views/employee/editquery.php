<div id="page-wrapper"> 
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

              <?php if (isset($data)) 
                  {
                    foreach ($data as $key => $data) 
                    $booking = $data['booking_id'];
                    
                  }?>

                  <?php if (isset($data1)) {
                    foreach ($data1 as $key => $data1) 
                    $booking1 = $data1['booking_id'];
            
                    }?>
              <h1 class="page-header">
                    Edit Query 
              </h1>

              <form class="form-horizontal" action="<?php echo base_url()?>employee/booking/process_edit_query_form/<?php echo $booking ?>" method="POST" >

                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">User Name</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="name" value = "<?php if (isset($data1['name'])) {echo $data1['name']; }?>"  disabled>
                    <?php echo form_error('name'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="name" class="col-md-2">Current Query Date</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($data['booking_date'])) {echo $data['booking_date']; }?>"  disabled>
                    <?php echo form_error('booking_date'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">Current Query Timeslot</label>
                  <div class="col-md-6">
                    <input type="text" name="booking_timeslot" value="<?php if (isset($data['booking_timeslot'])) {echo $data['booking_timeslot']; }?>"  disabled>
                    <?php echo form_error('booking_timeslot'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                  <label for="booking_date" class="col-md-2"> New Query Date</label>
                  <div class="col-md-6">
                    <input type="date" name="booking_date" value="<?php echo set_value('booking_date'); ?>" required>
                    <?php echo form_error('booking_date'); ?>
                  </div>
                </div>

                <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                  <label for="reason" class="col-md-2">New Query Timeslot</label>
                  <div class="col-md-6">
                    <select type="text" name="booking_timeslot" value="<?php echo set_value('booking_timeslot'); ?>" >
                    <option>Select</option>
                    <option>10AM-1PM</option>
                    <option>1PM-4PM</option>
                    <option>4PM-7PM</option>
                    </select>
                    <?php echo form_error('booking_timeslot'); ?>
                  </div>
                </div>

                <div>
                <div class="form-group <?php if( form_error('query_remarks') ) { echo 'has-error';} ?>">
                <label style="width:200px;" for="query_remarks" class="col-md-2">Query Remark:</label>
                <div class="col-md-2">
                <textarea style="height:150px;width:800px;" type="text" class="form-control"  name="query_remarks" value = "<?php if (isset($data['query_remarks'])){echo $data['query_remarks'];}?>"><?php if (isset($data['query_remarks'])){echo $data['query_remarks'];}?></textarea>
                  <?php echo form_error('query_remarks'); ?>
                </div>
              </div>
              </div>

                <div>
                  <center>
                    <input type="submit" value="Edit Query" class="btn btn-danger">
                    <?php echo "<a id='edit' class='btn btn-small btn-primary' href=".base_url()."employee/booking/view_queries/FollowUp>Cancel</a>";?>
                    
                  </center>
                </div>

              </form>
            </div>
        </div>
    </div>
</div>
