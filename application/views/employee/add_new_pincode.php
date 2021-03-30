<style type="text/css">
    .repeat_input_container{
   border: 1px solid #5fa2c3;
    margin: 0px auto;
    padding: 17px 20px 0px 20px;
    width: 61%;
    }
    .button_container{
        width: 36%;
    margin: 0px auto;
    padding: 20px 0px;
    }
    .view_container{
        width: 557px;
        text-align: center; 
    }
    .ul_holder{
         background: #d9edf7;
         border: 1px solid #70adac;
    margin: 10px;
    }
    #view_display_holder{
    margin: 10px;
    padding: 0px;
    }
</style>
<div id="page-wrapper" >
<div class="container" >
<div class="panel panel-info" style="margin-top:20px;">
    <div class="panel-heading"style="text-align:center;font: bold 20px/28px Arial"><b>Before Assigning the SF to Pincode, Please enter Pincode details</b></div>
        <div id="view_display_holder">
        <div style="clear:both"></div>
    </div>

 <div class="panel-body">
     <div style="clear:both"></div>
        	<div class="row">
               <form name="myForm" class="form-horizontal" id ="pincode_form" action="<?php echo base_url()?>employee/vendor/add_new_pincode"  method="POST" enctype="multipart/form-data">
                   <div class="container">
                    <div class="form-group">
                           <label for="pincode" class="col-md-4">Pincode*</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="pincode" name="pincode" onkeyup="return get_city_based_on_pincode();" <?php if (!empty($pincode)) { echo "value =".$pincode." readonly='' required";   } else{}?>>
                            </div>
                            <div id="loader_gif"></div>
                        </div>  
                     <div class="form-group <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="States" class="col-md-4">States*</label>
                           <div class="col-md-6">
                               <select type="text" class="form-control"  id="states" name="states" required>
                                            <option value="">Select States</option>
                                            <?php if (isset($states)){?>
                                                    <?php foreach ($states as $key => $values) { ?>
                                                    <option  value="<?php echo $values['state'] ?>" > <?php echo $values['state'];?></option>
                                                    <?php } 
                                            }?>
                               </select>
                           </div>
                        </div> 
                        <div class="form-group">
                                <label for="City" class="col-md-4">City*</label>
                                <div class="col-md-6">
                                    <select type="text" class="form-control"  id="city" name="city[]" required>
                                                 <option value="">Select City</option>
                                                 <?php if (isset($city)){?>
                                                         <?php foreach ($city as $key => $values) { ?>
                                                         <option  value="<?php echo $values['district'] ?>" > <?php echo $values['district'];?></option>
                                                         <?php } 
                                                 }?>
                                    </select>
                                </div>
                        </div> 
                   </div>
               </form>
                    <div class="button_container">
                        <input type="button" id="submitform" class="btn btn-info " onclick="savePincode()" value="Save"/>
                    </div>
                        
  </div>
</div>
    	</div>
</div>
</div>
<script type="text/javascript">
    $("#city").select2();
    $("#states").select2();

    function savePincode(){  
        var  stateValue  = $("#states").val();
        var  pincode  = $("#pincode").val();
        var  city  = $("#city").val();
        
        // Check Pincode Validation
        var pinRegex = /^\d{6}$/;
        if (!pinRegex.test(pincode))
        {
            alert("Enter Valid Pincode");
            return false;
        }
        
        if(stateValue && pincode && city){
           document.getElementById("pincode_form").submit();
        }
        else{
            alert("Please Select All Fields");
        }
    }
    
    function get_city_based_on_pincode() {
        var postData = {};
        var  pincode  = $("#pincode").val();
        pincode = pincode.trim();
        if (pincode.length == 6)
        {
            $('#loader_gif').html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:40px'>");
            $("#submitform").prop("disabled", true);
            $.ajax({
                data: {
                    booking_pincode : pincode,
                },
                url:"<?php echo base_url(); ?>employee/booking/get_city_state_from_pincode",
                type: 'post',
                success:function(data){
                    var data1 = jQuery.parseJSON(data);
                    if(data1.length){
                        $("#city").html('');
                        $("#states").html('');
                        // Set Default Option for City Dropdown
                        var newOption = new Option('Select City', '', false, false);
                        $('#city').append(newOption).trigger('change');
                        // Set Default Option for State Dropdown
                        var newStateOption = new Option('Select State', '', false, false);
                        $('#states').append(newStateOption).trigger('change');                            
                        $.each(data1, function (i, item) {
                            // Fill City as per Pincode
                            var newOption = new Option(item.district, item.district, false, false);
                            $('#city').append(newOption).trigger('change');
                            // Fill State as per Pincode
                            var newStateOption = new Option(item.state, item.state, false, false);
                            $('#states').append(newStateOption).trigger('change');
                        });
                        $('#loader_gif').html('');
                        $("#submitform").prop("disabled", false);
                    }
                    else
                    {
                        alert("Data Not Found.");
                        $('#loader_gif').html('');   
                    }
                } 
            });
        }
    }

</script>   