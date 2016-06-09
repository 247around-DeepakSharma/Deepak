<html>
	<head>
		<script>
	function validate()
	{
		var us_em=document.forms["myForm1"]["user_email"].value;
    var pincode=document.forms["myForm1"]["pincode"].value;
    var city=document.forms["myForm1"]["city"].value;
		var name=document.forms["myForm1"]["name"].value;
    var pin=document.forms["myForm1"]["pincode"].value;
    var alt_ph_no=document.forms["myForm1"]["alternate_phone_number"].value;
		var exp1 = /^\w+([\.-]?\w+)*@\w+([\.-]?(\w)+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/;
		var exp2=/^[0-9]+$/;
    var exp3 = /^[A-Za-z _]+$/;
		
		if(name=="")
		{
			alert("Please enter the name.");
			return false;
		}
		if (!name.match(exp3)) 
		{
			alert("Please enter only letters in name");
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
    if(pin != "" && isNaN(pin) && !pin.match(exp2))
    {
      alert("Please enter only digits in pincode.");
      return false;
    }
    if(alt_ph_no!="" && isNaN(alt_ph_no))
    {
      alert("Please enter only digits in alternate phone number.");
      return false;
    }

    if(city ==""){
      alert("Please Select City");
      return false;
    }
    if(pincode == ""){
       alert("Please fill pincode");
       return false;
    }
	}

</script>

	</head>
	<div id="page-wrapper"> 
   <div class="container-fluid">
      	<div class="row">
        	<div class="col-lg-12">
        		
               	<h1 class="page-header">
               		Register User 
            	</h1>
            	<center><strong><div><p style="color:red;"><?php echo 'User Does Not Exists'; ?></p></div></strong></center>
            	<form name="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/user/adduser" method="POST" onSubmit="return(validate());">
            		<div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                  <label for="phone_number" class="col-md-2">User Phone<span class="red">*</span></label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="phone_number" value = "<?php echo $user['phone_number'];?>">
                        <?php echo form_error('phone_number'); ?>
                      </div>  
                 </div>
            
                	<div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
            		<label for="name" class="col-md-2">User Name<span class="red">*</span></label>
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
            
                  <div class="form-group ">
                  <label for="home_address"  class="col-md-2">Home State<span class="red">*</span></label>
                      <div class="col-md-6">
                        <select name="state" id="state" onchange="getcity()" class="form-control" >
                          <option value="" >Select State</option>
                          <?php foreach ($state as $value) { ?>
                          <option value="<?php echo $value['state']; ?>"><?php echo $value['state']; ?></option>
                        <?php  } ?>
                          
                        </select>
                      </div>  
                 </div>
                 <div class="col-md-12">
                    <center><img src="" id="loader_gif"></center>
                 </div>
                  <div class="form-group <?php if( form_error('city') ) { echo 'has-error';} ?>">
                  <label for="home_address" class="col-md-2">Home City<span class="red">*</span></label>
                      <div class="col-md-6">
                       <select name="city" id="city"  class="form-control" >
                          <option value="" >Select City</option>
                        
                        </select>
                      </div>  
                 </div>

                 <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                  <label for="pincode" class="col-md-2">Pincode<span class="red">*</span></label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  id="pincode" name="pincode" value = "<?php echo set_value('pincode');  ?>" placeholder="Please enter user's pincode." required>
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

<script type="text/javascript">
  $('#state').select2();

  function getcity(){
     var state = $("#state").val();
     $('#loader_gif').css('display','inherit');
     $('#loader_gif').attr('src', "<?php echo base_url(); ?>/images/loader.gif");
    
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict/1',
       data: {state: state},
       success: function (data) {
      
         $("#city").html(data);          
         $('#loader_gif').attr('src', "");
         $('#loader_gif').css('display','none');
       }
     });
   }
</script>
<style>
.red{
    color:red;
    font-size: 18px;
}
</style>

</html>