<html>
	<head>
		<script>
	function validate()
	{
		var us_em=document.forms["myForm1"]["user_email"].value;
		var name=document.forms["myForm1"]["name"].value;
		var ho_ad=document.forms["myForm1"]["home_address"].value;
    var pin=document.forms["myForm1"]["pincode"].value;
    var alt_ph_no=document.forms["myForm1"]["alternate_phone_number"].value;
		var exp1 = /^\w+([\.-]?\w+)*@\w+([\.-]?(\w)+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/;
		var exp2=/^[0-9]+$/;
		
		if(name=="")
		{
			alert("Please enter the name.");
			return false;
		}
		if (name.match(exp2)) 
		{
			alert("Please enter only letters in name");
			return false;
		}
		if (ho_ad=="") 
		{
			alert("Please enter home address");
			return false;
		}
		if(us_em!="" && !us_em.match(exp1))
		{
			alert("Please enter valid email.");
			return false;
		}
    if(pin != "" && pin.length!=6)
    {
      alert("Please enter 6 digts pincode.");
      return false;
    }
    if(pin != "" && isNaN(pin))
    {
      alert("Please enter only digits in pincode.");
      return false;
    }
    if(alt_ph_no!="" && isNaN(alt_ph_no))
    {
      alert("Please enter only digits in alternate phone number.");
      return false;
    }
    if(alt_ph_no!="" && alt_ph_no.length !=10)
    {
      alert("Please enter 10 digits alternate phone number.");
      return false;
    }
	}

</script>

	</head>
	<div id="page-wrapper"> 
   <div class="container-fluid">
      	<div class="row">
        	<div class="col-lg-12">
        		
               	<h1 class="page-header" style="color:blue;">
               		Register User 
            	</h1>
            	<center><strong><div><p style="color:red;"><?php echo 'User Does Not Exists'; ?></p></div></strong></center>
            	<form name="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/user/adduser" method="POST" onSubmit="return(validate());">
            		<div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                  <label for="phone_number" class="col-md-2">User Phone</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="phone_number" value = "<?php echo $user['phone_number'];?>">
                        <?php echo form_error('phone_number'); ?>
                      </div>  
                 </div>
            
                	<div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
            			<label for="name" class="col-md-2">User Name</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="name" value = "<?php echo set_value('name');  ?>" placeholder="Enter user's name.">
                     		<?php echo form_error('name'); ?>
                  		</div>	
            		 </div>

            		 <div class="form-group <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
            			<label for="user_email" class="col-md-2">User Email</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="user_email" value = "<?php echo set_value('user_email');  ?>" placeholder="Enter user's email.(optional)">
                     		<?php echo form_error('user_email'); ?>
                  		</div>	
            		 </div>

            		 <div class="form-group <?php if( form_error('home_address') ) { echo 'has-error';} ?>">
            			<label for="home_address" class="col-md-2">Home Address</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="home_address" value = "<?php echo set_value('home_address');  ?>" placeholder="Enter user's home address.">
                     		<?php echo form_error('home_address'); ?>
                  		</div>	
            		 </div>

                  <div class="form-group <?php if( form_error('city') ) { echo 'has-error';} ?>">
                  <label for="home_address" class="col-md-2">Home City</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="city" value = "<?php echo set_value('city');  ?>" placeholder="Enter user's home City.">
                        <?php echo form_error('city'); ?>
                      </div>  
                 </div>

                 <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
                  <label for="home_address" class="col-md-2">Home State</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="state" value = "<?php echo set_value('state');  ?>" placeholder="Enter user's home State.">
                        <?php echo form_error('state'); ?>
                      </div>  
                 </div>



                 <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                  <label for="pincode" class="col-md-2">Pincode</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="pincode" value = "<?php echo set_value('pincode');  ?>" placeholder="Please enter user's pincode.(optional)">
                        <?php echo form_error('pincode'); ?>
                      </div>  
                 </div>

                 <div class="form-group <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                  <label for="alternate_phone_number" class="col-md-2">Alternate Contact Number</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="alternate_phone_number" value = "<?php echo set_value('alternate_phone_number');  ?>" placeholder="Enter user's alternate phone number.(optional)">
                        <?php echo form_error('alternate_phone_number'); ?>
                      </div>  
                 </div>
            		<input type="submit" value="Register User" class="btn btn-primary">	
            	</form>
           	</div>
        </div>
    </div>
</div>

</html>