//This function is used to get appliance for Brand
function get_appliance(){        
    brand = $("#appliance_brand_1").val();
    if (brand){
        $.ajax({
        type: 'POST',
            beforeSend: function(){
                $('#appliance_loading').css("display", "inherit");
            },
            url: baseUrlLink+'employee/partner/get_services_from_brand',
            data: {
                brand: brand
            },
            success: function (data) {
                //First Resetting Options values present if any
                $("#service_name option[value !='option1']").remove();
                $('#service_name').append(data);
                $('#appliance_loading').css("display", "none");
                get_city();
                get_category();
                get_capacity();
            }
        });
    }
}

// This function is used to get Partner from Brand
function get_partner(){        
    brand = $("#appliance_brand_1").val();
    if (brand){
        $.ajax({
        type: 'POST',
            url: baseUrlLink+'employee/partner/get_partner_from_brand',
            data: {
                brand: brand
            },
            success: function (data) {
                var partner_data = jQuery.parseJSON(data);
                $('#partner_id').val(partner_data.id);   
                $('#partner_type').val(partner_data.partner_type); 
                $('#partner_name').val(partner_data.entity_name); 
                $('#agent_id').val(partner_data.agent_id); 
                $('#partner_code').val(partner_data.code);
            }
        });
    }
}

// This function is used to Validate Form Data
// Split Validations among sections
function check_validation(){
    var exp1 = /^[6-9]{1}[0-9]{9}$/;
    var user_name = $("#name").val();
    var order_id = $('#order_id').val();
    var booking_address = $('#booking_address').val();
    var mobile_number = $('#booking_primary_contact_no').val();
    var city = $('#booking_city').val();
    var pincode = $('#booking_pincode').val();
    var serial_number = $('#serial_number').val();
    var category = $('#appliance_category_1').val();
    var remarks = $('#remarks').val();
    var partner_source = $("#partner_source").val();
    var appliance = $("#service_name").val();
    var brand = $("#appliance_brand_1").val();
    var dealer_name = $("#dealer_name").val();
    var dealer_phone_number = $("#dealer_phone_number").val();
    var not_visible = $("#not_visible").val();
    var purchase_date = $("#purchase_date").val();
    var symptom = $('#booking_request_symptom option:selected').text();
    var alternate_contact_no = $("#booking_alternate_contact_no").val();
    var user_regex = /^([a-zA-Z\s]*)$/;        

    $("#submitform").prop( "disabled", true);
    // Appliance Details Validation
    // Check Brand 
    if (brand === null){
        display_message("appliance_brand_1", "error_brand", "red", "Please Select Brand");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {        
        display_message("appliance_brand_1", "error_brand", "green", "");
    }
    // Check Product
    if (appliance === null){
        display_message("service_name", "error_appliance", "red", "Please Select Appliance");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("service_name", "error_appliance", "green", "");
    }
    // Check Partner Source
    if (partner_source === "" || partner_source === null){
        display_message("partner_source", "error_seller", "red", "Please Seller Channel");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("partner_source", "error_seller", "green", "");
    }
    // Check Category 
    if (category === null){
        display_message("appliance_category_1", "error_category", "red", "Please Select Category");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("appliance_category_1", "error_category", "green", "");
    }
    // Check Capacity
    if ($('.appliance_capacity').length > 0) {
        var count1 = 0;
        $(".appliance_capacity").each(function(){
            var capacity_value = document.getElementById(this.id).innerHTML;
            if (($.trim(capacity_value) !== '<option selected="" value=""></option>') && ($("#" + this.id).val() === '')) {
                display_message("appliance_capacity_1", "error_capacity", "red", "Please Select Capacity");
                $("#submitform").prop( "disabled", false);
                $("#" + this.id).focus();
                ++count1;
                return false;
            }
        });
        if (count1 > 0) {
            return false;
        }
        else {
            display_message("appliance_capacity_1", "error_capacity", "green", "");
        }
    }
    // Check Purchase Date
    if (purchase_date === ""){
        display_message("purchase_date", "error_purchase_date", "red", "Please Enter Purchase Date");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("purchase_date", "error_purchase_date", "green", "");
    }
    // Check Remarks
    if (remarks === ""){
        document.getElementById('remarks').style.borderColor = "red";
        document.getElementById('error_remarks').innerHTML = "Please Enter Problem Description";
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        document.getElementById('remarks').style.borderColor = "green";
        document.getElementById('error_remarks').innerHTML = "";
    }        
    // Check Dealer Phone Number 
    if (dealer_phone_number !== "" && dealer_name === ""){
        alert("Please Enter Dealer Name");
        $("#submitform").prop( "disabled", false);
        return false;
    }
    if (dealer_phone_number !== "" && !dealer_phone_number.match(exp1)){
        alert('Please Enter Valid Dealer Phone Number');
        $("#submitform").prop( "disabled", false);
        return false;
    }
    // Check Symptom
    if (symptom === "" || symptom === "Please Select Any Symptom"){
        alert("Please Enter Symptom");
        $("#submitform").prop( "disabled", false);
        return false;
    }
    // Personal Details Validation
    // Check Mobile Number
    if (!mobile_number.match(exp1)){
        alert('Please Enter Valid User Phone Number');
        display_message("booking_primary_contact_no", "error_mobile_number", "red", "Please Enter Valid User Phone Number");
        $("#submitform").prop( "disabled", false);
        return false;
    }
    if (mobile_number === ""){
        display_message("booking_primary_contact_no", "error_mobile_number", "red", "Please Enter Mobile");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_primary_contact_no", "error_mobile_number", "green", "");
    }
    // Check User Name
    if (user_name === "" || user_name.trim().length == '0'){
        display_message("name", "error_username", "red", "Please Enter User Name");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("name", "error_username", "green", "");
    }
    if (!user_name.match(user_regex)){
        display_message("name", "error_username", "red", "Please Enter Valid User Name");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("name", "error_username", "green", "");
    }
    // Check Pincode
    if (pincode === ""){
        display_message("booking_pincode", "error_pincode", "red", "Please Enter Pincode");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_pincode", "error_pincode", "green", "");
    }
    // Check City
    if ((city === null) || ($.trim(city) === '')){
        display_message("booking_city", "error_city", "red", "Please Enter City");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_city", "error_city", "green", "");
    }
    // Check Alternate Number 
    if (alternate_contact_no !== "" && !alternate_contact_no.match(exp1)){
        alert('Please Enter Valid Alternate Phone Number');
        display_message("booking_alternate_contact_no", "error_alternate_contact_no", "red", "Please Enter Valid Alternate Mobile");
        $("#submitform").prop( "disabled", false);
        return false;
    }
    // Check Booking Address
    if (booking_address.trim().length < 1){
        display_message("booking_address", "error_address", "red", "Please Enter Booking Address");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_address", "error_address", "green", "");
    }
    // Check if Service available in given Area or not        
    if (not_visible === 0){
        display_message("not_visible", "error_not_visible", "red", "Service Temporarily Un-available In This Pincode, Please Contact backoffice Team.");
        $("#submitform").prop( "disabled", false);
        return false;
    }

    $("#booking_appliance").val($.trim($("#service_name option:selected").text()));
    $("#service_id").val(appliance);
    $("#pincode").val(pincode);
    $("#pincode").keyup();
    $('#city').val(city);
    $("#address").val(booking_address);
    EnableDisableFields('booking_form', true);

    // Check Address Validation
    var booking_address = $('#address').val();
    var city = $('#city').val();
    var pincode = $('#pincode').val();
    var appliance = $("#booking_appliance").val();
    if ((appliance === null) || ($.trim(appliance) === '')){
        display_message("booking_appliance", "error_appliance", "red", "Please Enter Appliance");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_appliance", "error_appliance", "green", "");
    }
    if ($.trim(pincode) === ""){
        display_message("booking_pincode", "error_pincode", "red", "Please Enter Pincode");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_pincode", "error_pincode", "green", "");
    }
    if ((city === null) || ($.trim(city) === '')){
        display_message("city", "error_city", "red", "Please Select City");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("city", "error_city", "green", "");
    }
    if ($.trim(booking_address).length < 1){
        display_message("booking_address", "error_address", "red", "Please Enter Booking Address");
        $("#submitform").prop( "disabled", false);
        return false;
    } else {
        display_message("booking_address", "error_address", "green", "");
    }

    // Validate OTP
    // save in session & match OTP for Booking creation
    var original_otp = $("#customer_code").val(); 
    var customer_otp = $("#booking_otp").val(); 
    $.ajax({
        method:'POST',
        url: baseUrlLink+"employee/partner/match_booking_otp",
        async : false,
        data:{
            original_otp : original_otp,
            customer_otp : customer_otp
        },
        success:function(response){
            if ($.trim(response) == "success"){
                var appliance_name = $("#service_name").find(':selected').attr('data-id');
                $("#appliance_name").val(appliance_name);
                $('#submitform').val("Please wait.....");
                document.getElementById('submitform').disabled = true;
                document.getElementById('booking_form').submit();
                return true;
            }
            else
            {
                alert("OTP Verification Failed");
                $("#submitform").prop( "disabled", false);
                return false;
            }
        }
    });
}

function EnableDisableFields(id, status)
{
    var form = document.getElementById(id);
    var elements = form.elements;
    for (var i = 0, len = elements.length; i < len; ++i) {
        if (elements[i].id !== 'close_modal') {
            elements[i].readonly = status;
        }
    }                                
}

function display_message(input_id, error_id, color, message){
    document.getElementById(input_id).style.borderColor = color;
    document.getElementById(error_id).innerHTML = message;
}

//This function is used to get Category for partner id , service , brands specified
function get_category(){
    service_id = $("#service_name").val();
    brand = $("#appliance_brand_1").val();
    partner_id = $("#partner_id").val();
    partner_type = $("#partner_type").val();
    if (service_id && brand){
        $.ajax({
        type: 'POST',
                beforeSend: function(){
                $('#category_loading').css("display", "inherit");
                },
                url: baseUrlLink+'employee/partner/get_category_from_service',
                data: {
                        service_id: service_id,
                        partner_id: partner_id,
                        brand: brand,
                        partner_type:partner_type
                },
                success: function (data) {
                //First Resetting Options values present if any
                $("#appliance_category_1 option[value !='option1']").remove();
                    $('#appliance_category_1').append(data).change();
                },
                complete: function(){
                    $('#category_loading').css("display", "none");
                }
        });
    }
}

//This function is used to get Capacity and Model
function get_capacity(){
    service_id = $("#service_name").val();
    brand = $("#appliance_brand_1").find(':selected').val();
    category = $("#appliance_category_1").find(':selected').val();
    partner_type = $("#partner_type").val();
    partner_id = $("#partner_id").val();
    if (service_id && brand && category){
        $.ajax({
            type: 'POST',
            beforeSend: function(){
                $('#capacity_loading').css("display", "inherit");
            },
            url: baseUrlLink+'employee/partner/get_capacity_for_partner',
            data: {
                service_id: service_id,
                partner_id: partner_id,
                brand: brand, category:category,
                partner_type:partner_type
            },
            success: function (data) {
                $('#appliance_capacity_1').html(data).change();
                if (($.trim(data) !== "") && ($.trim(data) !== "<option  selected  value=''></option>")) {
                    $("#appliance_capacity_1").attr("required", true);
                }
                else{
                    $("#appliance_capacity_1").removeAttr("required");
                }
                get_models();
            },
            complete: function(){
                $('#capacity_loading').css("display", "none");
            }
        });
    }
}

//This function is used to get Model for corresponding previous data's
function get_models(){
    service_id = $("#service_name").val();
    brand = $("#appliance_brand_1").find(':selected').val();
    category = $("#appliance_category_1").find(':selected').val();
    capacity = $("#appliance_capacity_1").val();
    partner_type = $("#partner_type").val();
    partner_id = $("#partner_id").val();
    if (capacity === null && capacity === ""){
        capacity = '';
        $("#appliance_capacity_1").removeAttr("required");
    }
    if (service_id && brand && category){
        $.ajax({
            type: 'POST',
            url: baseUrlLink+'employee/partner/get_model_for_partner',
            data: {
                service_id: service_id,
                partner_id: partner_id,
                brand: brand,
                category:category,
                capacity:capacity,
                partner_type:partner_type
            },
            success: function (data) {
                if ($.trim(data) === "Data Not Found"){
                    var input = '<input type="text" name="model_number" id="model_number_1" class="form-control" placeholder="Please Enter Model" onkeypress="return checkQuote(event);" oninput="return checkInputQuote(this);">';
                    $("#model_number_2").html(input).change();
                    $('.select-model').next(".select2-container").hide();
                } else {
                    //First Resetting Options values present if any
                    var input_text = '<span id="model_number_2"><select class="form-control select-model"  name="model_number" id="model_number_1" ><option selected disabled>Select Model</option></select></span>';
                    $("#model_number_2").html(input_text).change();
                    $("#model_number_1").append(data).change();
                    $("#model_number_1").select2();
                    $('.select-model').next(".select2-container").show();
                }
            }
        });
    }
}

// function to cross check request type of booking , only IW bookings can be created from this page
function check_booking_request()
{
    var model_number = $(".select-model").val();
    var dop = $("#purchase_date").val();
    var partner_id = $("#partner_id").val();
    var service_id = $("#service_name").val();
    var booking_id = 1;
    var booking_request_types = [Repair_IW];
    $("#submitform").attr("disabled", false);
    if (dop !== "" && booking_request_types.length > 0){
        $.ajax({
            method:'POST',
            url: baseUrlLink+"employee/service_centers/get_warranty_data/2",
            data:{
                'bookings_data[0]' : {
                    'partner_id' : partner_id,
                    'booking_id' : booking_id,
                    'booking_create_date' : booking_create_date,
                    'service_id' : service_id,
                    'model_number' : model_number,
                    'purchase_date' : dop,
                    'booking_request_types' : booking_request_types
                }
            },
            success:function(response){
                var returnData = JSON.parse(response);
                if (returnData['status'] == 1)
                {
                    alert("Booking not Valid. Your Product Warranty has Expired !");
                    $('#error_not_visible').html("Booking not Valid. Your Product Warranty has Expired !");                    
                    $("#submitform").prop('disabled', true);
                }
            }
        });
    }
}