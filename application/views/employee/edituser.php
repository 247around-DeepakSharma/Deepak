<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="margin:50px;">
        <h2>Edit User Personal Details</h2><hr>
        <form class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/user/process_edit_user_form" method="POST" enctype="multipart/form-data">
        	<div><input type="hidden" name="user_id" value="<?php echo $user[0]['user_id']; ?>"></div>
        	<div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                <label for="name" class="col-md-2">User Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="name" value = "<?php echo $user[0]['name']; ?>">
                    <?php echo form_error('name'); ?>
                </div>
            </div>
            <div class="form-group <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
                <label for="user_email" class="col-md-2">User Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="user_email" value = "<?php echo $user[0]['user_email']; ?>">
                    <?php echo form_error('user_email'); ?>
                </div>
            </div>
            <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                <label for="phone_number" class="col-md-2">Phone Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="phone_number" value = "<?php echo $user[0]['phone_number']; ?>">
                    <?php echo form_error('phone_number'); ?>
                </div>
            </div>
            <div class="form-group <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                <label for="alternate_phone_number" class="col-md-2">Alternate Phone Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="alternate_phone_number" value = "<?php echo $user[0]['alternate_phone_number']; ?>">
                    <?php echo form_error('alternate_phone_number'); ?>
                </div>
            </div>
            <div class="form-group <?php if( form_error('home_address') ) { echo 'has-error';} ?>">
                <label for="home_address" class="col-md-2">Home Address</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="home_address" value = "<?php echo $user[0]['home_address']; ?>">
                    <?php echo form_error('home_address'); ?>
                </div>
            </div>

            <div class="form-group ">
                <label for="city" class="col-md-2">State</label>
                <div class="col-md-4">
                 <select name="state" id="state" onchange="getcity()" class="form-control" >
                          <option value="" >Select State</option>
                          <?php foreach ($state as $value) { ?>
                          <option value="<?php echo $value['state']; ?>" <?php if($user[0]['state'] == $value['state']){ echo "selected";}?> ><?php echo $value['state']; ?></option>
                        <?php  } ?>
                          
                        </select>    
                </div>
            </div>

            <div class="form-group">
                <label for="city" class="col-md-2">City</label>
                <div class="col-md-4">
                  <select name="city" id="city"  class="form-control" >
                        <option value="">Select City</option>
                        
                    </select>
                    <input type="hidden" class="form-control" id="city1" value = "<?php echo $user[0]['city']; ?>">
                    
                </div>
            </div>

            <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                <label for="pincode" class="col-md-2">Pincode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="pincode" value = "<?php echo $user[0]['pincode']; ?>">
                    <?php echo form_error('pincode'); ?>
                </div>
            </div>

              <div class="col-md-offset-3"><input type="Submit" value="Save" class="btn btn-primary">

              <a id='edit' class='btn btn-success' href="<?php echo base_url(); ?>employee/user/user_details/<?php echo $user[0]['phone_number']; ?>">Cancel</a>

             </div>
    
       </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $('#state').select2();
    var city1 = $("#city1").val();
    getcity(city1);

    function getcity(city = ""){
     var state = $("#state").val();
    
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict',
       data: {state: state, district:city},
       success: function (data) {
      
         $("#city").html(data);
                   
       }
     });
   }
</script>

