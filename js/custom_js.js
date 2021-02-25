
var brandServiceUrl = baseUrl + '/employee/booking/getBrandForService/';
var applianceUrl = baseUrl + '/employee/booking/get_appliances/';
var categoryForServiceUrl = baseUrl + '/employee/booking/getCategoryForService/';
var CapacityForCategoryUrl = baseUrl + '/employee/booking/getCapacityForCategory/';
var SelectStateUrl = baseUrl + '/employee/booking/get_state_by_city';
var pricesForCategoryCapacityUrl = baseUrl + '/employee/booking/getPricesForCategoryCapacity/';
var get_booking_upcountry_details = baseUrl + '/employee/booking/get_booking_upcountry_details/';
var count_number = 0;
var DEFAULT_UPCOUNTRY_RATE = 3;
var LOW_CREDIT_MSG = "Low Balance, Please Inform Brand To Recharge Account Immediately";

var modelServiceUrl = baseUrl + '/employee/booking/getModelForService/';
var partnerChannelServiceUrl = baseUrl + '/employee/partner/get_partner_channel/';
var CategoryCapacityForModelUrl =  baseUrl + '/employee/booking/getCategoryCapacityForModel/';
var URLGETCITYFROMPINCODE =  baseUrl + '/employee/booking/get_city_from_pincode/';
var UrlValidateSerialNumber =  baseUrl + '/employee/service_centers/validate_booking_serial_number';
var UrlCheckWarranty =  baseUrl + '/employee/service_centers/get_warranty_data/2/1';

function getAppliance(service_id) {

    var postData = {};
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');

    var service = $("#service_id option:selected").text();
    $("#services").val(service);
    
    sendAjaxRequest(postData, applianceUrl + service_id).done(function (data) {
        var data1 = jQuery.parseJSON(data);
        $("#partner_type").val(data1.partner_type);
        $("#partner_id").val(data1.partner_id);
        $("#service_id").html(data1.services).change();
        $("#is_active").val(data1.active);
        var booking_type = $("#booking_type").val();
        
        if(booking_type ==="" || booking_type === "Query" || booking_type === undefined){
            if(Number(data1.active) === 0){
                
                LOW_CREDIT_MSG = data1.prepaid_msg;
                alert(LOW_CREDIT_MSG);
               
             } 
        } 
       getBrandForService();
       getPartnerChannel();
    });
}

function getPartnerChannel(){
    var postData = {};
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
    postData['channel'] = $("#partner_channel").val();
    if( postData['partner_id'] !== null){
        sendAjaxRequest(postData, partnerChannelServiceUrl).done(function (data) {
           $("#partner_source").html("");
           $("#partner_source").html(data).change();
        });
    }
}

function getBrandForService() {

    var postData = {};
    postData['service_id'] = $("#service_id").val();
    postData['source_code'] = $("#source_code").val();
    
    var service = $("#service_id option:selected").text();
    $("#services").val(service);
    if( postData['source_code'] !== null){
        sendAjaxRequest(postData, brandServiceUrl).done(function (data) {
            var data1 = jQuery.parseJSON(data);
            $("#partner_type").val(data1.partner_type);

            $(".appliance_brand").html(data1.brand).change();


        });
    }
}
$(document).ready(function(){
   get_city_based_on_pincode(); 
});
function get_city_based_on_pincode() {
    var postData = {};
    var pincode = $("#booking_pincode").val();
    pincode = pincode.trim();
    if (pincode.length == 6)
    {
        postData['booking_pincode'] = pincode;
        var selectedCity = $("#booking_city").val();
        if (postData['source_code'] !== null) {
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

function getCategoryForService(div_id) {
    var postData = {};
    var div_no = div_id.split('_');

    postData['service_id'] = $("#service_id").val();
    postData['partner_id'] = $("#partner_id").val();
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();

    sendAjaxRequest(postData, categoryForServiceUrl).done(function (data) {

        if (div_id === undefined) {
            $(".appliance_category").html(data).change();
            // $(".appliance_capacity").html(data2); 

        } else {

            $("#appliance_category_" + div_no[2]).html(data).change();
            var data2 = "<option disabled></option>";
            $("#appliance_capacity_" + div_no[2]).html(data2).change();
            $("#appliance_capacity_" + div_no[2]).removeAttr("required");
            $("#priceList_" + div_no[2]).html("");

        }

    });

}


function getCapacityForCategory(category, div_id, add_booking) {
    add_booking = add_booking || false;
    var postData = {};
    var div_no = div_id.split('_');
    postData['service_id'] = $("#service_id").val();
    postData['partner_id'] = $("#partner_id").val();
    postData['category'] = category;
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    postData['capacity'] = $("#appliance_capacity_" + div_no[2]).val();
    sendAjaxRequest(postData, CapacityForCategoryUrl).done(function (data) {


        $("#appliance_capacity_" + div_no[2]).html(data).change();
        $("#appliance_capacity_" + div_no[2]).val(postData['capacity']);
        if (data !== "<option></option>") {
            $("#priceList_" + div_no[2]).html("");
            if(category){
                getModelForServiceCategoryCapacity(div_id);
                getPricesForCategoryCapacity(div_id,add_booking);
            }

        } else {
            $("#priceList_" + div_no[2]).html("");

            if(category){
                getModelForServiceCategoryCapacity(div_id);
                getPricesForCategoryCapacity(div_id,add_booking);
            }

        }
        
        if($.trim(data) !== "<option></option>") {
            $("#appliance_capacity_" + div_no[2]).attr("required",true);
        }
        else {
            $("#appliance_capacity_" + div_no[2]).removeAttr("required");
        }

    });
}

function getPricesForCategoryCapacity(div_id,add_booking) {
    add_booking = add_booking || 0;
    var postData = {};
    var div_no = div_id.split('_');
    var source_code = $("#source_code").find(':selected').attr('data-id');
    if(source_code === undefined)
    {
        source_code = $("#source_code").val();
    }
    
    $("#priceList_" + div_no[2]).html('<div class="text-center"><img src= "'+ baseUrl+'/images/loadring.gif" /></div>').delay(1200).queue(function () {
        
        postData['service_id'] = $("#service_id").val();
        postData['brand'] = $('#appliance_brand_' + div_no[2]).val();
        postData['category'] = $("#appliance_category_" + div_no[2]).val();
        postData['is_sf_panel'] = $("#is_sf_panel").val(); 
        postData['partner_type'] = $("#partner_type").val();
        postData['booking_city'] = $("#booking_city").val();
        postData['booking_pincode'] = $("#booking_pincode").val();
        postData['clone_number'] = div_no[2];
        postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
        postData['partner_id'] = $("#partner_id").val();;
        postData['add_booking'] = add_booking;
        postData['is_repeat'] = (($("#is_repeat").val()) ? $("#is_repeat").val(): 0);
        postData['booking_id'] = "";
        if($('#booking_id').length){
            postData['booking_id'] = $("#booking_id").val();
        }
        // sending all prev. selected prices tags with ajax request, to select them again if price table changes
        postData['selected_price_tags'] = (($("#selected_price_tags").val()) ? $("#selected_price_tags").val(): "");
        postData['arr_partner_discount'] = (($("#arr_partner_discount").val()) ? $("#arr_partner_discount").val(): "");
        postData['arr_247around_discount'] = (($("#arr_247around_discount").val()) ? $("#arr_247around_discount").val(): "");
        if(postData['is_repeat'] !== 1) {
            console.log("is_repeat");
            $('#submitform').attr('disabled', true);
        }

        if ($("#appliance_capacity_" + div_no[2]).val() !== "") {

            postData['capacity'] = $("#appliance_capacity_" + div_no[2]).val();

        } else {

            postData['capacity'] = "";
            $("#appliance_capacity_" + div_no[2]).removeAttr("required");
        }
        if(postData['category']){
            //  $("#priceList_" + div_no[2]).html("Loading......");
            sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function (data) {
                var data1 = jQuery.parseJSON(data);

                $("#priceList_" + div_no[2]).html(data1.price_table);
                $("#upcountry_data").val(data1.upcountry_data);
                final_price();
                if((postData['is_repeat'] == 1) || ($("input[type=checkbox]:checked").length > 0)) {
                    if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                        $('#submitform').attr('disabled', false);
                    }
                }
            });
        }


        $(this).dequeue();
    });

}

function final_price() {
    var price = 0;
    var price_array;
    var around_discount = 0;
    var partner_discount = 0;
    var get_around_discount = 0;
    var get_partner_discount = 0;

    $("input[type=checkbox]:checked").each(function (i) {
        price_array = $(this).val().split('_');
        //console.log(price_array);
        price += Number(price_array[1]);
        get_around_discount = $('#discount_' + price_array[2] + "_" + price_array[3]).val();
        around_discount += Number(get_around_discount);
        // console.log('#partner_paid_basic_charges_'+price_array[2]+"_"+price_array[3]);
        get_partner_discount = $('#partner_paid_basic_charges_' + price_array[2] + "_" + price_array[3]).val();

        partner_discount += Number(get_partner_discount);

    });

    var final_price = Number(price) - Number(around_discount) - Number(partner_discount);

    $("#grand_total_price").val(final_price);
    var is_sf_panel = $("#is_sf_panel").val();    
    if(!is_sf_panel && ($("#flag_add_booking").length) && ($("#flag_add_booking").val() == 1)) {
        // These request types can not be selected while creating Booking
        // uncheck these request types if selected
        $('.price_checkbox[data-price_tag="Gas Recharge (R410) - In Warranty"]').prop('checked', false);
        $('.price_checkbox[data-price_tag="Gas Recharge (R410) - Out of warranty"]').prop('checked', false);
        $('.price_checkbox[data-price_tag="Gas Recharge - In Warranty"]').prop('checked', false);
        $('.price_checkbox[data-price_tag="Gas Recharge - Out of Warranty"]').prop('checked', false);
        $('.price_checkbox[data-price_tag="Small Stand"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (In Warranty)"]').prop('checked', false);
        $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (Out Warranty)"]').prop('checked', false);
        
        // Make this Request types disabled
        $('.price_checkbox[data-price_tag="Gas Recharge (R410) - In Warranty"]').prop('disabled', true);
        $('.price_checkbox[data-price_tag="Gas Recharge (R410) - Out of warranty"]').prop('disabled', true);
        $('.price_checkbox[data-price_tag="Gas Recharge - In Warranty"]').prop('disabled', true);
        $('.price_checkbox[data-price_tag="Gas Recharge - Out of Warranty"]').prop('disabled', true);
        $('.price_checkbox[data-price_tag="Small Stand"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="Drain Pipe Per Meter"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="22 Gauge Refrigerant Pipe, Insulation, Wire Set / ft"]').css('pointer-events', 'none');
        $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (In Warranty)"]').prop('disabled', true);
        $('.price_checkbox[data-price_tag="Gas Recharge with Dryer (Out Warranty)"]').prop('disabled', true);
    }

}

$(document).on('keyup', '.discount', function (e) {
    final_price();
    set_upcountry();
});

$(document).on('keyup', '.partner_discount', function (e) {
    final_price();
    set_upcountry();
});


function check_prepaid_balance(type) {
   
//    if (type === "Booking") {
//           
//        var booking_type = $("#booking_type").val();
//        var is_active = $("#is_active").val();
//        if (booking_type === "" || booking_type === "Query" || booking_type === undefined ) {
//            
//            if (Number(is_active) === 0) {
//
//                alert(LOW_CREDIT_MSG);
//               
//                document.getElementById("booking").checked = false;
//                return false;
//            }
//        }
//    }

}

function addBookingDialog(chanel = '', check_serial_no = '0') {
    var delivered_price_tags = [];
    var delivered_price_tags_pod = [];
    var partner_id = $("#partner_id").val();
    var is_sf_panel = $("#is_sf_panel").val();
    $(".price_checkbox:checked").each(function (i) {
             var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
             delivered_price_tags.push(price_tags);
             if($("#"+ $(this).attr('id')).attr('data-pod')){
                var pod = $("#"+ $(this).attr('id')).attr('data-pod');
                delivered_price_tags_pod.push(pod);
             }
     });
    $("#selected_price_tags").val(delivered_price_tags.join());
    $("#pod").val("0");
    if(jQuery.inArray("1", delivered_price_tags_pod) !== -1){
        $("#pod").val("1");
    }

    var pr = checkPriceTagValidation(delivered_price_tags, partner_id);
    if(pr === false){
        alert('Not Allow to select multiple different type of service category');
        return false;
    }
    count_number++;
    var exp1 = /^[6-9]{1}[0-9]{9}$/;
    var email_exp =  /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
    var user_name = $('#name').val();
    var p_contact_no = $('#booking_primary_contact_no').val();
    var alternate_contact_no = $('#booking_alternate_contact_no').val();
    var address = $('#booking_address').val();
    var service = $("#service_id option:selected").text();
    if(chanel == "sf_update"){
         var service = $("#service_id").val();
    }
    var pincode = $("#booking_pincode").val();
    var city = $("#booking_city").val();
    var booking_date = $("#booking_date").val();
    var timeslot = $('#booking_timeslot').val();
    var type = $('input[name=type]:checked', '#booking_form').val();
    var source_code = $("#source_code option:selected").val();
    var dealer_phone_number = $('#dealer_phone_number').val();
    var dealer_name = $('#dealer_name').val();
    var booking_type = $("#booking_type").val();
    var is_active = $("#is_active").val();
    var div_count = $('.purchase_date').length;
    var partner_id = $("#partner_id").val();
    var is_sf_panel = $("#is_sf_panel").val();
    var partner_source = $("#partner_source").val();
    var user_email = $("#booking_user_email").val();
    var parant_id = $('#parent_id').val();
    var repeat_reason = $('#repeat_reason').val();
    var isRepeatChecked = $('.repeat_Service:checkbox:checked').length;
    var isServiceChecked = $('.Service:checkbox:checked').length;
    var symptom = $('#booking_request_symptom option:selected').text();
    if(chanel == "sf_update"){
       symptom =  $('#booking_request_symptom').val();
    }
   // var customer_paid = $("#grand_total_price").val()
     if(chanel != "sf_update"){
        if($('.appliance_capacity').length > 0) {
            var count1=0;
            $(".appliance_capacity").each(function(){
                var capacity_value = document.getElementById(this.id).innerHTML;
                if(($.trim(capacity_value) !== '<option></option>') && ($("#"+this.id).val() === '')) {
                    alert("Select capacity, if capacity not found please check the model mapping for this brand");
                    $("#"+this.id).focus();
                    ++count1;
                    return false;
                }
            });
            if(count1 > 0) {
                return false;
            }
        }
     }
    if (!is_sf_panel && user_name == "" || user_name.trim().length ==0 || user_name == null) {

        alert("Please Enter User Name");

        return false;
    }
    if (!is_sf_panel && pincode.length !== 6) {

        alert("Please Select 6 Digit Valid Pincode Number");

        return false;
    }

    if (!is_sf_panel && (partner_source == "" || partner_source== null)) {

        alert("Please Select a partner source");

        return false;
    }

    if (!is_sf_panel && source_code === "Select Booking Source") {

        alert("Please Select Booking Source");

        return false;
    }
    
    if($('.support_file').length > 1) {
        var i=1;
        var count=0;
        $('.support_file').each(function () {
            var file = $("#"+this.id).val();
            if (file === '') {
               $('div.clonedInputSample').show();
               alert('Please Select file or Remove Add Support File Panel '+i+" !! ");
               $("#"+this.id).focus();
               ++count;
               return false;
            }
            ++i;
        });
        if(count > 0) {
            return false;
        }
    }

    if (service === null || service === "" || service === "Select Service") {

        alert('Please Select Booking Appliance');
        return false;

    }
    if ($("input[type=checkbox]:checked").length === 0) {

        alert('Please select at least one check box');
        return false;

    }
    
    if (type === null || type === undefined) {

        alert("Please Select Booking Type ");
        return false;

    } else {
        if (type === "Booking") {
            if (!is_sf_panel && address === "") {

                alert("Please fill Address ");
                return false;
            } else {

                if (!is_sf_panel && pincode === "") {

                    alert("Please fill pincode ");
                    return false;
                }
            }
            
            var grand_total_price = Number($("#grand_total_price").val());
            if(grand_total_price < 2){
                if(booking_type ==="" || booking_type === "Query" || booking_type === undefined){
                    if(Number(is_active) === 0){

                        alert(LOW_CREDIT_MSG);
                        return false;
                     }  
                } 
            }
            
        } else {
            if ($('input[name=internal_status]:checked').length > 0) {
                // something when checked
            } else {
                if (!is_sf_panel){
                    alert("For Query, Internal Status is MANDATORY.");
                    return false;
                }                
            }
        }
    }

    if (!is_sf_panel && p_contact_no === "") {

        alert("Please fill Phone Number ");
        return false;
    }

    if (!is_sf_panel && p_contact_no !== "" && !p_contact_no.match(exp1)) {
        alert("Enter Valid Phone Number Only");
        return false;
    }
    
    if (!is_sf_panel && alternate_contact_no !== "" && !alternate_contact_no.match(exp1)) {
        alert("Enter Valid Alternate Phone Number Only");
        return false;
    }
    
    if (!is_sf_panel && user_email !== "" && !user_email.match(email_exp)) {
        alert("Enter Valid Email Only");
        return false;
    }

    if (!is_sf_panel && city === "" || city === "Select City") {

        alert("Please fill city ");
        return false;
    }
    var grand_total_price = $("#grand_total_price").val();
    if (Number(grand_total_price) === 0) {
       
        if (partner_id !== "247001") {
            if(partner_id === "3"){
                firstPartNumaricValidation = firstPartLengthValidation = false;
                secondPartNumaricValidation  =  secondPartLengthValidation = true;
                orderIDSplitArray = $("#order_id").val().split("-");
                orderIDSplitLength = orderIDSplitArray.length;
                firstPartNumaricValidation = /^\d+$/.test(orderIDSplitArray[0]);
                if(orderIDSplitArray[0].length >= 10){
                    firstPartLengthValidation = true;
                }
                if(orderIDSplitLength === 2){
                    secondPartNumaricValidation = /^\d+$/.test(orderIDSplitArray[1]);
                    if(orderIDSplitArray[1].length < 10){
                        secondPartLengthValidation = false;
                    }
                }
                if(!is_sf_panel && !(firstPartNumaricValidation && secondPartNumaricValidation && firstPartLengthValidation && secondPartLengthValidation)){
                    alert("Please Enter Correct Order ID");
                    return false;
                }
            }
            else{
                old_type = $("#booking_old_type_holder").text();
                if(!(chanel == 'admin_update' && old_type == 'Booking' && type == 'Booking')){
                    var order_id = $('#order_id').val();
                    if (!is_sf_panel && order_id === "" && dealer_phone_number === "") {
                        alert('Please Fill Order Id Or Dealer Phone Number');
                        return false;
                    }
                }
            }
        }
    }
    if(!is_sf_panel && dealer_phone_number !=="" && !dealer_phone_number.match(exp1)){
        alert('Please Enter Valid Dealer Phone Number');   
        return false;
    }
    
    if(!is_sf_panel && dealer_phone_number !=="" && dealer_name === ""){
        alert("Please Enter Dealer Name");
        return false;
    }
    
    if((symptom == 0) || (symptom == '0')) {
        
    }
    else if(!is_sf_panel && symptom === "" || symptom === "Please Select Any Symptom"){
        alert("Please Enter Symptom");
        return false;
    }

    if(type === "Booking"){
        for(var t=1; t<=div_count; t++){
            var p_date_value = $("#purchase_date_"+t).val();
            if (p_date_value === "") {
                alert("Purchase Date Should not be blank");
                return false;
            } 
        }
    }
    if(isRepeatChecked > 0){
        //If Repeat Selected than no other Service Should be Selected
        if(isServiceChecked >0){
            alert("You Can Not Select any other Service in case of Repeat Booking");
            return false;
        }
        //If anyone select repeat booking than parent ID Shoud not blank
        if(!parant_id){
            alert("Parent ID not found, Repeat booking can not be created");
            $(".repeat_Service").prop("checked", false);
            $('.Service').each(function() {
                $(this).prop('disabled', false);
            });
            return false;
        }
        //If Repeat Booking is Selected than Repeat Reason Should not be blank
        if(!is_sf_panel && !repeat_reason){
            if($('#repeat_reason_holder').length)
            {
                $('#repeat_reason_holder').show();
            }            
            alert("Please Write the Repeat Reason");
            return false;
        }
    }

  
    if(isRepeatChecked > 0 && !parant_id){
        
    }
    //If Repeat is checked than Repeat Reason Should not be blank
    if (booking_date === "") {
        alert("Please fill Booking date ");
        return false;
    } else {

    }

    if (!is_sf_panel && timeslot === null) {

        alert('Please Select Booking Time Slot');
        return false;
    }

    // Make remarks mandatory 
    if(!is_sf_panel && ($('#query_remarks').length > 0) && ($('#query_remarks').val() == ""))
    {
        alert('Please Enter Remarks');
        $('#query_remarks').focus();
        return false;
    }

    // Check for serial number
    //if POD is also 1, only then check for serial number.
    if (check_serial_no == "1" && $("#pod").val() == "1") {
        var serial_number = $("#serial_number").val(); 
        if (serial_number === "" || serial_number === "0") {    
            alert('Please Enter Valid Serial Number');
            return false;                        
        }  

        // Check for serial number Image
        if($('#serial_number_pic').val() == '' ){
            alert('Please Attach Serial Number image');
            return false;
        }  

//        var duplicateSerialNo = $('#duplicate_sno_required').val();
//        if(duplicateSerialNo === '1'){
//            alert(DUPLICATE_SERIAL_NUMBER_USED);
//            $("#error_serial_no").html(DUPLICATE_SERIAL_NUMBER_USED);
//            return false;
//        }
    }
    
    // If serial number is filled , Image should also be uploaded and vice-versa
    if($('#serial_number_pic').val() == '' && $("#serial_number").val() != ''){
            alert('Please Attach Serial Number image');
            return false;
    }  
    
    if($('#serial_number_pic').val() != '' && $("#serial_number").val() == ''){
            alert('Please Fill Serial Number');
            return false;
    }  
    
    if (count_number > 1) {

        $('.clone_m').html("");
    }

    var regex1 = /^(.+?)(\d+)$/i;

    var priceIndexClone = $("#bpriceList1").length + 1;

    var numItems = $('.clonedInput').length;
    $status = 1;
    for (var i = 1; i <= numItems; i++) {
        var indexClone = $(".preview_booking").length + 1;

        if (i !== 1)
            cloned_model(regex1, indexClone);

        var set_st = setAppliances(i);
        if (set_st === false) {
            $status = 0;
        }

    }

    if ($status === 0) {
        return false;
    }
    else
    {
        for (var k = 1; k <= numItems; k++) {
            cloned_price(regex1, priceIndexClone, k);
        }
        if(!is_sf_panel){
            return true;
        }
        else
        {
            if(confirm("Validating Serial Number & Booking Warranty Status, Click OK to continue.")){
                var btn_text = $("#btn_text").val();
                $("#submitform").prop("disabled", true);
                $("#submitform").attr('value', 'Verifying Data ... ');
                setTimeout(function(){ 
                    var is_correct = validate_serial_number_and_warranty();
                    if(is_correct){
                        $("#booking_form").submit();
                    }
                    else{
                        $("#submitform").attr('value', btn_text);
                        $("#submitform").prop("disabled", false);
                        return false;
                    }
                }, 3000);                         
            }
        }             
    }
}

function setAppliances(i) {

    var brand = $("#appliance_brand_" + i).val();

    if (brand === null) {

        alert("Please select Brand " + i);
        return false;
    }

    var appliance_category = $("#appliance_category_" + i).val();
    if (appliance_category === null) {

        alert("Please select Category " + i);
        return false;
    }

    var appliance_capacity = $("#appliance_capacity_" + i).val();

    if (appliance_capacity === null) {
        alert("Select capacity, if capacity not found please check the model mapping for this brand");
        return false;
    }

    var model_number = $("#model_number_" + i).val();
    if (model_number === null) {
        alert("Please fill Model Number " + i);
        return false;
    }

}
function checkPriceTagValidation(delivered_price_tags, partner_id){
        var repair_flag = false;
        var repair_out_flag = false;
        var installation_flag = false;
        var amc_flag = false;
        var replacement_flag = false;
        var pdi = false;
        var extended_warranty = false;
        var pre_sales = false;
        var others_flag = false;
        var array =[];
        var videocon_id = "247130";
        

        if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1 
                )){
            
            repair_flag = true;
            array.push(repair_flag);
         } 
         
         if((findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1
                || findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1)){
            
            repair_out_flag = true;
            array.push(repair_out_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Extended Warranty') > -1 ){
             extended_warranty = true;
             array.push(extended_warranty);
         }
         
         if(findInArray(delivered_price_tags, 'Presale Repair') > -1 ){
             pre_sales = true;
             array.push(pre_sales);
         }
         
         if(findInArray(delivered_price_tags, 'Installation & Demo (Free)') > -1 
                || findInArray(delivered_price_tags, 'Installation & Demo (Paid)') > -1){
                   installation_flag = true;
                   array.push(installation_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1
                || findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1){
                    pdi = true;
                    array.push(pdi);
                }
                
         if(findInArray(delivered_price_tags, 'AMC (Annual Maintenance Contract)') > -1 ){
             amc_flag = true;
             array.push(amc_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Replacement') > -1 
                || findInArray(delivered_price_tags, 'Replacement - In Warranty (Service Center Visit)') > -1){
             replacement_flag = true;
             array.push(replacement_flag);
         }
         
         // ----------------------------------------------------------------------------------------
         // DO NOT ALLOW SAME REQUEST TYPE COMBINATIONS IN BOOKING         
         if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1)|| 
            (findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1)
            ){            
            repair_flag = true;
            array.push(repair_flag);
         } 
         
         if((findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Home Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1) ||
            (findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Service Center Visit)') > -1 && findInArray(delivered_price_tags, 'Repair - Out Of Warranty (Customer Location)') > -1)            
           ){            
            repair_out_flag = true;
            array.push(repair_out_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Installation & Demo (Free)') > -1 && findInArray(delivered_price_tags, 'Installation & Demo (Paid)') > -1){
                   installation_flag = true;
                   array.push(installation_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - With Packing') > -1 && findInArray(delivered_price_tags, 'Pre-Dispatch Inspection PDI - Without Packing') > -1){
                    pdi = true;
                    array.push(pdi);
         }
         
         if(findInArray(delivered_price_tags, 'Gas Recharge - In Warranty') > -1 && findInArray(delivered_price_tags, 'Gas Recharge - Out of Warranty') > -1){
                    others_flag = true;
                    // Both In-out Recharge types can not be selected together, that's why adding 2 values in others_flag 
                    array.push(others_flag, others_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Gas Recharge (R410) - In Warranty') > -1 && findInArray(delivered_price_tags, 'Gas Recharge (R410) - Out of warranty') > -1){
                    others_flag = true;
                    // Both In-out Recharge types can not be selected together, that's why adding 2 values in others_flag 
                    array.push(others_flag, others_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Wet Service - In Warranty') > -1 && findInArray(delivered_price_tags, 'Wet Service - Out of Warranty') > -1){
                    others_flag = true;
                    // Both In-out Recharge types can not be selected together, that's why adding 2 values in others_flag 
                    array.push(others_flag, others_flag);
         }
         
         if(findInArray(delivered_price_tags, 'Gas Recharge with Dryer (In Warranty)') > -1 && findInArray(delivered_price_tags, 'Gas Recharge with Dryer (Out Warranty)') > -1){
                    others_flag = true;
                    // Both In-out Recharge types can not be selected together, that's why adding 2 values in others_flag 
                    array.push(others_flag, others_flag);
         }
         
         if(partner_id === videocon_id){
              if((findInArray(delivered_price_tags, 'Repair - In Warranty (Home Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Service Center Visit)') > -1 
                || findInArray(delivered_price_tags, 'Repair - In Warranty (Customer Location)') > -1
                || findInArray(delivered_price_tags, 'Presale Repair') > -1 
                || findInArray(delivered_price_tags, 'AMC (Annual Maintenance Contract)') > -1
                )
                &&(
                  findInArray(delivered_price_tags, 'Gas Recharge - In Warranty') > -1
                ||findInArray(delivered_price_tags, 'Gas Recharge - Out of Warranty') > -1
                ||findInArray(delivered_price_tags, 'Gas Recharge (R410) - In Warranty') > -1
                ||findInArray(delivered_price_tags, 'Gas Recharge with Dryer (In Warranty)') > -1
                )){
                    others_flag = true;
                    array.push(others_flag);
                }
         }
         
         // ---------------------------------------------------------------------------------------------------------
                
         if(array.length > 1){
             return false;
         } else {
             return true;
         }
    }
    function findInArray(ar, val) {
        for (var i = 0,len = ar.length; i < len; i++) {
            if ( ar[i] === val ) { // strict equality test
                return i;
            }
        }
        return -1;
    }
function cloned_model(regex1, indexClone) {

    $("#preview_booking1").clone()
            .appendTo(".clone_m")
            .attr("id", "rat" + indexClone)
            .find("*")
            .each(function () {

                var id = this.id || "";
                var match = id.match(regex1) || [];
                //console.log(match[1]);
                if (match.length === 3) {
                    this.id = match[1] + (indexClone);
                }
            });

    indexClone++;

}

function cloned_price(regex1, indexClone, i) {

    var checkduplicate = $("#bat" + i).length;

    if (checkduplicate > 0) {
        // $('bat'+i).remove();

    } else {
        $("#priceList_" + i).clone()
                .appendTo("#bpriceList_" + i)
                .attr("id", "bat" + i)
                .find("*")
                .each(function () {

                    var id = this.id || "";
                    var match = id.match(regex1) || [];
                    //console.log(match[1]);
                    if (match.length === 3) {
                        this.id = match[1] + (indexClone);
                    }
                });
        indexClone++;
    }
}

function sendAjaxRequest(postData, url) {
    return $.ajax({
        data: postData,
        url: url,
        type: 'post'
    });
}

function enable_discount(div_id) {

    var div_no = div_id.split('_');


    if ($("#checkbox_" + div_no[1] + "_" + div_no[2]).is(':checked')) {

        var price_array = $("#checkbox_" + div_no[1] + "_" + div_no[2]).val();
        var customer_total = Number(price_array.split('_')[1]);
        var around_discount = Number($("#discount_" + div_no[1] + "_" + div_no[2]).val());
        var partner_discount = Number($("#partner_paid_basic_charges_" + div_no[1] + "_" + div_no[2]).val());
        var final_price = customer_total - around_discount - partner_discount;
        if(customer_total === 0){
            $("#discount_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);
            $("#partner_paid_basic_charges_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);
            
        } else if (final_price > 1) {

            $("#discount_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);
            $("#partner_paid_basic_charges_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);

        } else {
            if ($("#booking").is(':checked')) {
                var booking_type = $("#booking_type").val();
                var is_active = $("#is_active").val();
                if (booking_type === "" || booking_type === "Query" || booking_type === undefined) {

                    if (Number(is_active) === 0) {

                        alert(LOW_CREDIT_MSG);

                        document.getElementById("checkbox_" + div_no[1] + "_" + div_no[2]).checked = false;
                    }
                }
            } else if ($("#query").is(':checked')) {
                
            } else {
                alert("Please select Booking Type First");
                document.getElementById("checkbox_" + div_no[1] + "_" + div_no[2]).checked = false;
            }
        }

    } else {
        //$("#discount_"+div_no[1]+"_"+div_no[2]).val(0);

        $("#discount_" + div_no[1] + "_" + div_no[2]).attr("readonly", true);

        $("#partner_paid_basic_charges_" + div_no[1] + "_" + div_no[2]).attr("readonly", true);

    }

    //final_price();
}

function outbound_call(phone_number) {
    var confirm_call = confirm("Call Customer ?");

    if (confirm_call === true) {

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
            success: function (response) {
                console.log(response);

            }
        });
    } else {
        return false;
    }

}

function enable_submit_button() {
    $("#submitform").button('reset');
}

/**
 * @desc: This function is used to format date 
 * @param {type} date
 * @returns {Array}
 */
function formatDate(date) {
    var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function set_upcountry() {
    var upcountry_data = $("#upcountry_data").val();
    var is_sf_panel = $("#is_sf_panel").val();
    is_upcountry = 0;
    non_upcountry = 0;
    n = 0;
    count = 0;
    flat_upcountry = 0;
    var customer_price = 0;
    $("input[type=checkbox]:checked").each(function (i) {
        count = count + 1;

        var id = this.id.split('checkbox_');

        var up_val = $("#is_up_val_" + id[1]).val();
        
        var f = Number($("#is_up_val_" + id[1]).attr("data-flat_upcountry"));
        if(f === 1){
            customer_price = Number($("#is_up_val_" + id[1]).attr("data-customer_price"));
        
            flat_upcountry = f;
        }
       
        if (Number(up_val) === 1) {
            is_upcountry = 1;

        } else  if (Number(up_val) === -1) {
            non_upcountry = -1;
        } else {
            n =1;
        }
    });
    if (count > 0 && upcountry_data != "") {
        var data1 = jQuery.parseJSON(upcountry_data);
        switch(data1.message) {
            case 'UPCOUNTRY BOOKING':
            case 'UPCOUNTRY LIMIT EXCEED':
                if(Number(is_upcountry) == 1 && Number(data1.partner_provide_upcountry) == 0){
                    
                    if(flat_upcountry == 1){
                        var upcountry_charges =  customer_price;
                        alert(upcountry_charges);
                    } else {
                        var upcountry_charges = (Number(DEFAULT_UPCOUNTRY_RATE) * Number(data1.upcountry_distance)).toFixed(2);
                    }
                    total_price = $("#grand_total_price").val();
                    $("#upcountry_charges").val(upcountry_charges);
                    $("#grand_total_price").val(Number(total_price) + Number(upcountry_charges));
                    alert("This is upcountry call. Please inform to customer that booking will be completed in 3 Days");
                    if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                        $('#submitform').attr('disabled', false);
                    }
                    
                } else if(Number(is_upcountry) == 1 && Number(data1.partner_provide_upcountry) == 1 ){
                    var total_price = $("#grand_total_price").val();

                    var partner_approval = Number(data1.partner_upcountry_approval);

                    if (data1.message === "UPCOUNTRY BOOKING") {
                        $("#upcountry_charges").val("0");
                        if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                            $('#submitform').attr('disabled', false);
                        }
                        final_price();
                        if(!is_sf_panel)
                        {
                            alert("This is upcountry call. Please inform to customer that booking will be completed in 3 Days");
                        }       

                    } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 0) {
                        if(!is_sf_panel)
                        {
                            $('#submitform').attr('disabled', true);
                            alert("This is out station Booking, not allow to submit Booking/Query. Upcountry Distance " + data1.upcountry_distance + " KM");
                        }
                    } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 1) {
                        $("#upcountry_charges").val("0");
                        if(!is_sf_panel && (!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')))
                        {
                            alert("This is out station boking, Waiting for Partner Approval. Upcountry Distance " + data1.upcountry_distance + " KM");
                            $('#submitform').attr('disabled', false);
                        }
                    } else {
                        // $("#upcountry_charges").val("0");
                        if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                            $('#submitform').attr('disabled', false);
                        }
                    }
                } else {
                    if(Number(is_upcountry) == 0 && Number(non_upcountry) == 0){
                        
                        if(flat_upcountry == 1){
                            var upcountry_charges = customer_price;
                        } else {
                            var upcountry_charges = (Number(DEFAULT_UPCOUNTRY_RATE) * Number(data1.upcountry_distance)).toFixed(2);
                        }
                        
                        
                        total_price = $("#grand_total_price").val();
                        $("#upcountry_charges").val(upcountry_charges);
                        $("#grand_total_price").val(Number(total_price) + Number(upcountry_charges));
                        
                    } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 0){
                        
                        $("#upcountry_charges").val("0");
                        if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                            $('#submitform').attr('disabled', false);
                        }
                        final_price();
                    } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 1){
                        
                        if(flat_upcountry == 1){
                            var upcountry_charges = customer_price;
                        } else {
                            var upcountry_charges = (Number(DEFAULT_UPCOUNTRY_RATE) * Number(data1.upcountry_distance)).toFixed(2);
                        }
                        
                        
                        total_price = $("#grand_total_price").val();
                        $("#upcountry_charges").val(upcountry_charges);
                        $("#grand_total_price").val(Number(total_price) + Number(upcountry_charges));
                    }
                    if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                        $('#submitform').attr('disabled', false);
                    }
                }
                break;
                
            default:
                    $("#upcountry_charges").val("0");
                    final_price();
                    if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                        $('#submitform').attr('disabled', false);
                    }
                    break;
            
        }

    } else {
        final_price();
        $("#upcountry_charges").val("0");
        console.log("Upcountry Charges 0");
        if(!is_sf_panel){
            $('#submitform').attr('disabled', true);
        }
    }
}

    function check_pincode(){
        var pincode = $("#booking_pincode").val();
        if(pincode.length === 6){
            
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                    $('#submitform').attr('disabled', true); 
                },
                url: baseUrl +'/employee/vendor/check_pincode_exist_in_india_pincode/'+ pincode,          
                success: function (data) {
                  
                    if(data === "Not Exist"){
                        $('#submitform').attr('disabled', true); 
                        alert("Check Pincode.. Pincode Not Exist");
                         document.getElementById("error_pincode").style.borderColor = "red";
                         document.getElementById("error_pincode").innerHTML = "Check Pincode.. Pincode Not Exist";
                        return false;
                    }  else {
                        if(!($("#is_sn_correct").length) || ($("#is_sn_correct").val() != '1')){
                            $('#submitform').attr('disabled', false);   
                        }
                        document.getElementById("error_pincode").style.borderColor = "red";
                         document.getElementById("error_pincode").innerHTML = "";
                    } 
                }
                 
            }); 
        }
        else
        {
            $('#submitform').attr('disabled', true); 
            document.getElementById("error_pincode").style.borderColor = "blue";
            document.getElementById("error_pincode").style.color = "blue";
            document.getElementById("error_pincode").innerHTML = "Enter 6 Digit Valid Pincode";
        }
    }
    $(document).ready(function(){
        var is_spare_requested = $('#is_spare_requested').val();
        if(is_spare_requested == 1){
            $("input[data-price_tag='Installation & Demo (Free)']").prop("disabled", true);
            $("input[data-price_tag='Installation & Demo (Paid)']").prop("disabled", true);            
        }
        
         $("#booking_pincode").keyup(function(event) {
       
            check_pincode();
            getBrandForService();
            get_city_based_on_pincode();
        
        });
        $("#dealer_phone_number").keyup(function(){
            var partner_id = $("#source_code").find(':selected').attr('data-id');
            if(partner_id !== undefined){
                 var search_term = $(this).val();
                 dealer_setup(partner_id, search_term, "dealer_phone_number_1");
            } else{
                alert("Please Select Partner");
            }
        });
        
        $("#dealer_name").keyup(function(){
            var partner_id = $("#source_code").find(':selected').attr('data-id');
            if(partner_id !== undefined){
                var search_term = $(this).val();
                dealer_setup(partner_id, search_term, "dealer_name");
                 
            } else{
                alert("Please Select Partner");
            }
        });
    
        $(".repeat-close").click(function(){
            alert("Repeat booking can not be created without Parent Booking");
            $(".repeat_Service").prop("checked", false);
            // enable other checkboxes
            $('.Service').each(function() {
               $(this).prop('disabled', false);
            });
        });
});

function dealer_setup(partner_id,search_term,search_filed){
                
    if(search_term === ""){
        $("#dealer_id").val("");
        $("#dealer_name").val("");
        $("#dealer_phone_number").val("");
        $("#dealer_phone_suggesstion_box").hide();
        $("#dealer_name_suggesstion_box").hide();
    }else{

        $.ajax({
            type: "POST",
            url: baseUrl + "/employee/partner/get_dealer_details",
            data: {partner_id: partner_id, search_term: search_term,dealer_field: search_filed},
            beforeSend: function () {
                //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function (data) {
                if(search_filed === "dealer_phone_number_1"){
                    $("#dealer_phone_suggesstion_box").show();
                    $("#dealer_phone_suggesstion_box").html(data);
                    $("#dealer_phone_number").css("background", "#FFF");
               } else {
                    $("#dealer_name_suggesstion_box").show();
                    $("#dealer_name_suggesstion_box").html(data);
                    $("#dealer_name").css("background", "#FFF");
               }
            }
        });
    }
}

function selectDealer(name,ph, id) {

    $("#dealer_phone_number").val(ph);
    $("#dealer_name").val(name);
    $("#dealer_id").val(id);

    $("#dealer_phone_suggesstion_box").hide();
    $("#dealer_name_suggesstion_box").hide();
 }
 
function getModelForServiceCategoryCapacity(div_id) {
   
    var postData = {};
    var div_no = div_id.split('_');
    
    postData['service_id'] = $("#service_id").val();
    postData['partner_id'] = $("#partner_id").val();
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    postData['category'] = $("#appliance_category_" + div_no[2]).val();
    postData['capacity'] = $("#appliance_capacity_" + div_no[2]).val();
    if (postData['category']) {
        sendAjaxRequest(postData, modelServiceUrl).done(function (data) {
            var obj = JSON.parse(data);
            if(obj.status === false){
                $('.select-model').hide();
                $('.select-model-div').hide();
                $('.select-model').next(".select2-container").hide();
                $('.input-model').show();
                $('.input-model-div').show();
                $('.input-model').removeAttr('disabled');
            }else{
                $('.select-model').show();
                $('.select-model-div').show();
                $('.select-model').next(".select2-container").show();
                $('.input-model').attr('disabled', 'disabled');
                $('.input-model').hide();
                $('.input-model-div').hide();
                if($.trim(postData['capacity']) !== '' || !$("#is_repeat").val()) {
                    //$('#model_number_1').val('');
                    //$('#select2-model_number_1-container').empty();
                    $(".select-model#model_number_" + div_no[2]).html(obj.msg);
                }
            }
            
        });
    }
}

function escapeRegExp(string){
   return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function replaceAll(str, term, replacement) {
    if(str !== undefined)
    {
        return str.replace(new RegExp(escapeRegExp(term), 'g'), replacement);
    }
}
 
function get_symptom(symptom_id = ""){
    var array = [];
    var postData = {};
    $(".price_checkbox:checked").each(function (i) {
        var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
        var price_tags1 = replaceAll(price_tags, '(Free)', '');
        var price_tags2 = replaceAll(price_tags1, '(Paid)', '')
        array.push(price_tags2);

    });
    
    if(array.length > 0){
        postData['partner_id'] = $("#source_code option:selected").attr('data-id');
        postData['request_type'] = array;
        postData['service_id'] = $("#service_id").val();
        postData['booking_request_symptom'] = symptom_id;
        var url = baseUrl + '/employee/booking_request/get_booking_request_dropdown';
        sendAjaxRequest(postData, url).done(function (data) {
            $('#booking_request_symptom').html("<option disabled selected>Please Select Any Symptom</option>");
            if(data === "Error"){
                $('#booking_request_symptom').append("").change();
                $("#booking_request_symptom").removeAttr('required');
            } else {
                $('#booking_request_symptom').append(data).change();
                $("#booking_request_symptom").attr('required', 'required');
                
            }
        });
    }
   
}

function getCapacityCategoryForModel(model_number, div_id) {
    var postData = {};
    var div_no = div_id.split('_');
    postData['model_number'] = model_number;
    postData['partner_id'] = $("#partner_id").val();
    sendAjaxRequest(postData, CategoryCapacityForModelUrl).done(function (data) {
        var obj = JSON.parse(data);
        if(obj.length)
        {
            $("#appliance_brand_" + div_no[2]).val($.trim(obj[0]['brand']));
            $("#appliance_category_" + div_no[2]).val($.trim(obj[0]['category']));
            $("#appliance_capacity_" + div_no[2]).val($.trim(obj[0]['capacity']));
        }  
        
        getPricesForCategoryCapacity(div_id,false);
    });
}

function validateSerialNo(count = ""){
        var postData = {};
        var booking_request_types = []; 
        $(".price_checkbox:checked").each(function(){
            var price_tag = $(this).attr('data-price_tag');
            booking_request_types.push(price_tag);
        });
        postData['serial_number'] = $.trim($("#serial_number"+count).val());
        postData['price_tags'] = $("#price_tags"+count).text();
        postData['user_id'] = $("#user_id").val();
        postData['booking_id'] = $("#booking_id").val();
        postData['partner_id'] = $("#partner_id").val();
        postData['appliance_id'] = $('#service_id').val();
        postData['booking_request_types'] = booking_request_types;
        $("#submitform").attr("disabled",false);
        $('#serial_number' + count).css("border-color", "#ccc");
        if(postData['serial_number'] !== ''){
            $.ajax({
                    type: 'POST',
                    beforeSend: function(){
                        $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });
                },
                url: UrlValidateSerialNumber,
                data:postData,
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if(data.code === 247){
                        console.log("Correct Serial Number");
                        $('body').loadingModal('destroy');
                        $("#error_serial_no" +count).text("");
                        $("#is_sn_correct" +count).val('0');
                        $("#sno_required"+count).val('0');
                        $("#duplicate_sno_required"+count).val('0');
                    } else if(data.code === Number(DUPLICATE_SERIAL_NO_CODE)){
                        console.log("Duplicate Serial Number");
                        $("#duplicate_sno_required"+count).val('1');
                        $("#is_sn_correct" +count).val('1');
                        $("#error_serial_no" +count).html(data.message);
                        $('body').loadingModal('destroy');
                        $("#submitform").attr("disabled",true);
                    } else {
                        console.log("Incorrect Serial Number");
                        $("#sno_required"+count).val('1');
                        $("#is_sn_correct" +count).val('1');
                        $("#error_serial_no" +count).html(data.message);
                        $("#duplicate_sno_required"+count).val('0');
                        $('body').loadingModal('destroy');
                        $("#submitform").attr("disabled",true);
//                        if(data.code == "247" || (data.message.indexOf('Serial Number not valid') !== -1)) {
//                            $("#submitform").attr("disabled",true);
//                        }
                    }
                }
            });
        }
    }
    
    function validate_serial_number_and_warranty(){
        // Validate Serial Number
        var postData = {};
        var valid_serial_number = false;
        var booking_request_types = [];
        if(!$(".input-model").length)
        {
            var model_number = $(".select-model").val();
        }
        else
        {
            var model_number = $(".input-model").val();
        }  
        $(".price_checkbox:checked").each(function(){
            var price_tag = $(this).attr('data-price_tag');
            booking_request_types.push(price_tag);
        });
        postData['serial_number'] = $.trim($("#serial_number").val());
        postData['price_tags'] = $("#price_tags").text();
        postData['user_id'] = $("#user_id").val();
        postData['booking_id'] = $("#booking_id").val();
        postData['partner_id'] = $("#partner_id").val();
        postData['appliance_id'] = $('#service_id').val();
        postData['model_number'] = model_number;
        postData['booking_request_types'] = booking_request_types;

        if(postData['serial_number'] !== ''){
            $.ajax({
                type: 'POST',
                async: false,
                url: UrlValidateSerialNumber,
                data:postData,
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if(data.code === 247){
                        console.log("Correct Serial Number");
                        $("#error_serial_no").text("");
                        $("#is_sn_correct").val('0');
                        $("#sno_required").val('0');
                        $("#duplicate_sno_required").val('0');
                        valid_serial_number = check_booking_warranty();
                    } else if(data.code === Number(DUPLICATE_SERIAL_NO_CODE)){
                        console.log("Duplicate Serial Number");
                        $("#duplicate_sno_required").val('1');
                        $("#is_sn_correct").val('1');
                        $("#error_serial_no").html(data.message);                        
                    } else {
                        console.log("Incorrect Serial Number");
                        $("#sno_required").val('1');
                        $("#is_sn_correct").val('1');
                        $("#error_serial_no").html(data.message);
                        $("#duplicate_sno_required").val('0');
                    }
                }
            });
        }
        else{
            valid_serial_number = check_booking_warranty();
        }
        return valid_serial_number; 
    }
    
    // function to cross check request type of booking with warranty status of booking 
    function check_booking_warranty()
    {
        if(!$(".input-model").length)
        {
            var model_number = $(".select-model").val();
        }
        else
        {
            var model_number = $(".input-model").val();
        }        
        var dop = $("#purchase_date_1").val();
        var partner_id = $("#partner_id").val();
        var service_id = $("#service_id").val();
        var serial_number = $("#serial_number").val();
        var brand = $("#appliance_brand_1").val();
        var booking_id = $("#booking_id").val();
        var booking_create_date = $("#booking_create_date").val();
        var booking_request_types = []; 
        $(".price_checkbox:checked").each(function(){
            var price_tag = $(this).attr('data-price_tag');
            booking_request_types.push(price_tag);
        });
        $('.errorMsg').html("");
        var valid_request = false;
        // Model Number & DOP/Serial number should be there for checking warranty
        // Booking Request Type should not be AMC/repeat
        if((model_number !== "" && model_number !== null && model_number !== undefined) && (dop !== "" || serial_number != "") && (booking_request_types.length > 0)){                             
            $.ajax({
                method: 'POST',
                async: false,
                url: UrlCheckWarranty,
                data: {
                    'bookings_data[0]' : {
                        'partner_id' : partner_id,
                        'booking_id' : booking_id,
                        'booking_create_date' : booking_create_date,
                        'service_id' : service_id,
                        'brand' : brand,
                        'model_number' : model_number,
                        'purchase_date' : dop,
                        'serial_number' : serial_number,
                        'booking_request_types' : booking_request_types
                    }
                },
                success:function(response){
                    var returnData = JSON.parse(response);
                    $('.errorMsg').html(returnData['message']);
                    if(returnData['status'] == 1)
                    {
                        console.log("Invalid Booking Request Type");
                    }
                    else
                    {                        
                        console.log("Valid Booking Request Type");
                        valid_request = true;
                    }
                }                            
            });
            return valid_request;
        }
    }
