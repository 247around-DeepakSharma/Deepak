<script type="text/javascript">
  function validate(){
  var exp1 = /^\w+([\.-]?\w+)*@\w+([\.-]?(\w)+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/;
  var exp2=/^[0-9 -]+$/;
  var exp3 = /^[A-Za-z _]+$/;
  var name=document.forms['myForm']['name'].value;
  var us_email=document.forms['myForm']['user_email'].value;
  var alt_ph_no=document.forms['myForm']['alternate_phone_number'].value;
  var pin=document.forms['myForm']['pincode'].value;
  if (!name.match(exp3)) 
    {
      alert("Please enter only letters in name");
      return false;
    }
  if(us_email!="" && !us_email.match(exp1)){
    alert("Please enter a valid email.");
    return false;
  }
  if(alt_ph_no!="" && !alt_ph_no.match(exp2)){
      alert("Please Check Alternate Number's Format.");
    return false;
  }
  if(alt_ph_no!="" && (alt_ph_no.length<10 || alt_ph_no.length>11)){
    alert("Alternate Number length is not correct");
    return false;
  }  
  // if(!pin.match(exp2)){
  //   alert("Enter only digits in pincode");
  //   return false;
  // }
  if(pin.length!=6 && pin.match(exp2)){
    alert("Enter 6 digits pincode.");
    return false;
  }
}
function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

    }


</script>
<style>
.red{
    color:red;
    font-size: 18px;
}
</style>
<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="margin:50px;">
        <h2>Edit User Personal Details</h2><hr>
        <form class="form-horizontal" name="myForm" id="booking_form" action="<?php echo base_url()?>employee/user/process_edit_user_form" method="POST" enctype="multipart/form-data">
        	<div><input type="hidden" name="user_id" value="<?php echo $user[0]['user_id']; ?>"></div>
        	<div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                <label for="name" class="col-md-2">User Name<span class="red">*</span></label>
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
                <label for="phone_number" class="col-md-2">Phone Number<span class="red">*</span></label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="phone_number" value = "<?php echo $user[0]['phone_number']; ?>">
                    <?php echo form_error('phone_number'); ?>
                </div>
                <label for="phone_icon" class="col-md-2"><?php if($c2c){ ?><button type="button" onclick="outbound_call(<?php echo $user[0]['phone_number']  ?>)" class="btn btn-sm btn-info"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button><?php } ?></label>
            </div>
            <div class="form-group <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                <label for="alternate_phone_number" class="col-md-2">Alternate Phone Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="alternate_phone_number" value = "<?php echo $user[0]['alternate_phone_number']; ?>">
                    <?php echo form_error('alternate_phone_number'); ?>
                </div>
                <?php
                if(!empty($user[0]['alternate_phone_number'])){
                ?>
                <label for="phone_icon" class="col-md-2"><?php if ($c2c){ ?><button type="button" onclick="outbound_call(<?php echo $user[0]['alternate_phone_number']  ?>)" class="btn btn-sm btn-info"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button><?php } ?></label>
                <?php } ?>
            </div>
            <div class="form-group <?php if( form_error('home_address') ) { echo 'has-error';} ?>">
                <label for="home_address" class="col-md-2">Home Address</label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="home_address" value = "<?php echo $user[0]['home_address']; ?>">
                    <?php echo form_error('home_address'); ?>
                </div>
            </div>

            <div class="form-group ">
                <label for="city" class="col-md-2">State<span class="red">*</span></label>
                <div class="col-md-4">
                 <select name="state" id="state" onchange="getcity()" class="form-control" >
                          <option value="" >Select State</option>
                          <?php foreach ($state as $value) { ?>
                          <option value="<?php echo $value['state']; ?>" <?php if($user[0]['state'] == $value['state']){ echo "selected";}?> ><?php echo $value['state']; ?></option>
                        <?php  } ?>
                          
                        </select>    
                </div>
            </div>
            <div class="col-md-12">
                    <center><img src="" id="loader_gif"></center>
            </div>
            <div class="form-group">
                <label for="city" class="col-md-2">City<span class="red">*</span></label>
                <div class="col-md-4">
                  <select name="city" id="city"  class="form-control" >
                        <option value="">Select City</option>
                        
                    </select>
                    <input type="hidden" class="form-control" id="city1" value = "<?php echo $user[0]['city']; ?>">
                    
                </div>
            </div>

            <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                <label for="pincode" class="col-md-2">Pincode</span></label>
                <div class="col-md-4">
                    <input type="text" class="form-control"  name="pincode" value = "<?php echo $user[0]['pincode']; ?>" >
                    <?php echo form_error('pincode'); ?>
                </div>
            </div>

              <div class="col-md-offset-3"><input type="Submit" onclick="return(validate())" value="Save" class="btn btn-primary">

              <a id='edit' class='btn btn-success' href="<?php echo base_url(); ?>employee/user/user_details/0/0/<?php echo $user[0]['phone_number']; ?>">Cancel</a>

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
     $('#loader_gif').css('display','inherit');
     $('#loader_gif').attr('src', "<?php echo base_url(); ?>/images/loader.gif");

     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict',
       data: {state: state, district:city},
       success: function (data) {
      
         $("#city").html(data);
         $('#loader_gif').attr('src', "");
         $('#loader_gif').css('display','none');
                   
       }
     });
   }
</script>

