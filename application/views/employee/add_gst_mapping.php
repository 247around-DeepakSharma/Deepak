<style>
    .form-control{
        border-radius: 0;
        width: 100%;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add GST Number  </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        <?php
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success_msg').'</strong>
                    </div>';
                    }
                    if ($this->session->flashdata('error_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error_msg').'</strong>
                    </div>';
                    }
                    ?>
                        <form id="new_credit_note" data-parsley-validate class="form-horizontal form-label-left" action="<?php echo base_url(); ?>employee/spare_parts/process_add_gst_details_for_partner" method="POST" enctype="multipart/form-data" >

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="entity_type">Entity Type <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                  
                                  <!--     <label class="radio-inline">
                                  <input type="radio" name="entity_type"  class="radiobutton" checked value="warehouse" id="warehouse"   >Warehouse Hub
                                  </label>-->
                                  <label class="radio-inline">
                                      <input type="radio" name="entity_type" class="radiobutton" value="partner"  id="partner" checked="checked" disabled>Partner
                                  </label>

                                    <span class="text-danger"><?php echo form_error('entity_type'); ?></span>
                                </div>
                            </div>

                               <div class="form-group" id="warehousepartner" >
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select Partner Warehouse <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                      <select class="form-control" name="warehousehub" id="partnerwarehouselist">
                                            
                                      </select>
                                     
                                </div>
                               </div>
                                 <div class="form-group hide" id="partnersdiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select Partner   <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select style="width: 100% !important;" name="partner" class="form-control" id="partners" required>
                                            
                                    </select>
                                </div>
                               </div>

                            
                            <div class="form-group" id="PincodeDiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Pincode   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="booking_pincode_for_gst" name="booking_pincode" value="" autocomplete="off" onpaste="return false;" onkeypress="return isNumber(event)" placeholder="Enter Area Pin" maxlength="6" required>
                                     <span id="error_pincode"></span>
                                </div>
                               </div>

                                <div class="form-group" id="Citydiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select City   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                      <select style="width: 100% !important;" name="city" class="form-control" id="booking_city" required>
                                          <option selected="" disabled="" value="">Select City</option>
                                            
                                      </select>
                                     
                                </div>
                               </div>
                            
                            
                                <div class="form-group hide" id="statediv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select State   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                      <select style="width: 100% !important;" name="state" class="form-control" id="states" required>
                                          <option selected="" disabled="" value="">Select State</option>
                                            <?php foreach ($select_state as   $state) { ?>
                                                 <option value="<?php echo $state['state_code']; ?>"><?php echo $state['state'];  ?></option>
                                            <?php }  ?>
                                      </select>
                                     
                                </div>
                               </div>


                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">GST Number <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="gst_number" required="required" class="form-control col-md-7 col-xs-12" name="gst_number" placeholder="Enter GST Number">
                                    <span class="text-danger"><?php echo form_error('gst_number'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gst_file" class="control-label col-md-3 col-sm-3 col-xs-12">GST File<span class="required"></span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="file" id="gst_file" class="form-control col-md-7 col-xs-12" name="gst_file">
                                    <span class="text-danger"><?php echo form_error('gst_file'); ?></span>
                                </div>
                            </div>
                            
                            <div class="form-group" id="contactDiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="mobile_no">Contact No   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="" autocomplete="off" onpaste="return false;" onkeypress="return isNumber(event)" placeholder="Enter Contact No without palcing 0 or +91 " maxlength="10" required>
                                </div>
                            </div>
                            
                            <div class="form-group" id="EmailDiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="email_id">Email Id   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="email" class="form-control" id="email_id" name="email_id" value=""  placeholder="Enter Email Id" required>
                                </div>
                            </div>
                            
                            <div class="form-group" id="AddressDiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="address">Address   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea type="text" class="form-control" id="address" name="address" value="" placeholder="Enter address" required rows="6" cols="6"></textarea>
                                </div>
                            </div>
                            
                            
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button class="btn btn-primary" type="reset">Reset</button>
                                    <button type="submit" id="submitform" class="btn btn-success">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function(){
        
        $("#submitform").click(function(){            
            // Address Validation
            var address = $.trim($("#address").val());
            if(address == ""){
                alert("Please fill Address Details");
                return false;
            }
            
            // Email Validation
            var email = document.getElementById('email_id');
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if ((email.value == "") || (!filter.test(email.value))) {
                alert('Please provide a valid email address');
                return false;
            }
            
            // Phone Number Validation
            var phone = $('#mobile_no').val(),
            phoneRegex = /[0-9]+$/;
            if((phone.length != 10) || (!phoneRegex.test(phone)))
            {
                 alert('Please enter a valid phone number.');
                 return false;
            }
            
            // GST Validation
            var gst = $('#gst_number').val(),
            gstRegex = /^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}$/;
            if((gst == "") || (!gstRegex.test(gst)))
            {
                 alert('Please enter a valid GSTIN');
                 return false;
            }                    
        });

        //get_partner_list_warehouse();
        get_partners();
        $("#partnersdiv").addClass('show');
        $("#statediv").addClass('show');
        $("#warehousepartner").addClass('hide');
        $("#states").select2();

        $("#booking_pincode_for_gst").keyup(function(event) {       
            check_pincode();
            get_city_based_on_pincode();
            get_state_based_on_pincode();
        });

        function check_pincode(){
            var pincode = $("#booking_pincode_for_gst").val();
            if(pincode.length === 6){

                $.ajax({
                    type: 'POST',
                    beforeSend: function(){
                        $('#submitform').attr('disabled', true); 
                    },
                    url: '<?php echo base_url(); ?>employee/vendor/check_pincode_exist_in_india_pincode/'+ pincode,          
                    success: function (data) {

                        if(data === "Not Exist"){
                            $('#submitform').attr('disabled', true); 
                            alert("Check Pincode.. Pincode Not Exist");
                             document.getElementById("error_pincode").style.Color = "red";
                             document.getElementById("error_pincode").innerHTML = "Check Pincode.. Pincode Not Exist";
                            return false;
                        }  else {
                            $('#submitform').attr('disabled', false); 
                            document.getElementById("error_pincode").style.Color = "none";
                             document.getElementById("error_pincode").innerHTML = "";
                        } 
                    }

                }); 
            }
            else
            {
                $('#submitform').attr('disabled', true); 
                document.getElementById("error_pincode").style.borderColor = "blue";
                document.getElementById("error_pincode").style.color = "red";
                document.getElementById("error_pincode").innerHTML = "Enter 6 Digit Valid Pincode";
            }
        }
    });
    
    var URLGETCITYFROMPINCODE ='<?php echo base_url(); ?>employee/booking/get_city_from_pincode/';
    
    function sendAjaxRequest(postData, url) {
        return $.ajax({
            data: postData,
            url: url,
            type: 'post'
        });
    }
    
    /*get city from user entered pin code and generate city list*/
    function get_city_based_on_pincode() {
    var postData = {};
    var pincode = $("#booking_pincode_for_gst").val();
    pincode = pincode.trim();
    if (pincode.length == 6)
    {
        postData['booking_pincode'] = pincode;
        var selectedCity = $("#booking_city").val();
        if (postData['booking_pincode'] !== null) {
            sendAjaxRequest(postData, URLGETCITYFROMPINCODE).done(function (data) {
                var data1 = jQuery.parseJSON(data);
                $("#booking_city").html('');
                var newOption = new Option('Select City', '', false, false);
                $('#booking_city').append(newOption).trigger('change');
                $.each(data1, function (i, item) {
                    //alert(item.district);
                     var seleted = false;
                    if(item.district == selectedCity)
                    {
                        var seleted = true;
                    }
                    var newOption = new Option(item.district, item.district, false, seleted);
                    $('#booking_city').append(newOption).trigger('change');
                });                
            });
            }
        }
    }

    var URLGETSTATEFROMPINCODE ='<?php echo base_url(); ?>employee/vendor/get_state_from_pincode/';

    /*get state from user entered pin code and generate state list*/
    function get_state_based_on_pincode() {
    var postData = {};
    var pincode = $("#booking_pincode_for_gst").val();
    pincode = pincode.trim();
    if (pincode.length == 6)
    {
        postData['booking_pincode'] = pincode;
        if (postData['booking_pincode'] !== null) {
            sendAjaxRequest(postData, URLGETSTATEFROMPINCODE).done(function (data) {
                var data1 = jQuery.parseJSON(data);
                $("#states").html('');
                var newOption = new Option('Select State', '', false, false);
                $('#states').append(newOption).trigger('change');
                $.each(data1, function (i, item) {
                    //alert(item.district);
                    var newOption = new Option(item.state, item.state_code, false, true);
                    $('#states').append(newOption).trigger('change');
                });                
            });
        }
        }
    }
    
    
    function isNumber(evt)
    {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                    return false;
            }
            return true;
    }

    $(".allownumericwithdecimal").on("keypress blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });


    function get_partner_list_warehouse() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list_warehouse',
            data:{'is_wh' : 1},
            success: function (response) {
               // console.log(response);
                 $('#partnerwarehouselist').append(response);
                 $('#partnerwarehouselist').select2();
                
            }
        });
    }


    function get_partners(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success: function (response) {
                $("#partners").html(response);
                $('#partners').select2();
            }
        });
    }

</script>