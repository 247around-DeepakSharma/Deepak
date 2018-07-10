
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
    });
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

            $(".appliance_brand").html(data1.brand);


        });
    }
}

function getCategoryForService(div_id) {
    var postData = {};
    var div_no = div_id.split('_');

    postData['service_id'] = $("#service_id").val();
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
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
            $("#priceList_" + div_no[2]).html("");

        }

    });

}


function getCapacityForCategory(category, div_id) {
    var postData = {};
    var div_no = div_id.split('_');

    postData['service_id'] = $("#service_id").val();
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
    postData['category'] = category;
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();


    sendAjaxRequest(postData, CapacityForCategoryUrl).done(function (data) {


        $("#appliance_capacity_" + div_no[2]).html(data).change();

        if (data !== "<option></option>") {
            $("#priceList_" + div_no[2]).html("");
            if(category){
                getModelForServiceCategoryCapacity(div_id);
                getPricesForCategoryCapacity(div_id);
            }


        } else {
            $("#priceList_" + div_no[2]).html("");

            if(category){
                getModelForServiceCategoryCapacity(div_id);
                getPricesForCategoryCapacity(div_id);
            }
            

        }

    });
}

function getPricesForCategoryCapacity(div_id) {
    var postData = {};
    var div_no = div_id.split('_');
    $("#priceList_" + div_no[2]).html('<div class="text-center"><img src= "'+ baseUrl+'/images/loadring.gif" /></div>').delay(1200).queue(function () {
        
        postData['service_id'] = $("#service_id").val();
        postData['brand'] = $('#appliance_brand_' + div_no[2]).val();
        postData['category'] = $("#appliance_category_" + div_no[2]).val();
        
        postData['partner_type'] = $("#partner_type").val();
        postData['booking_city'] = $("#booking_city").val();
        postData['booking_pincode'] = $("#booking_pincode").val();
        postData['clone_number'] = div_no[2];
        postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
        postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
        $('#submitform').attr('disabled', true);

        if ($("#appliance_capacity_" + div_no[2]).val() !== "") {

            postData['capacity'] = $("#appliance_capacity_" + div_no[2]).val();

        } else {

            postData['capacity'] = "";
        }
        if(postData['category']){
            //  $("#priceList_" + div_no[2]).html("Loading......");
            sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function (data) {
                console.log(data);
                var data1 = jQuery.parseJSON(data);

                $("#priceList_" + div_no[2]).html(data1.price_table);
                $("#upcountry_data").val(data1.upcountry_data);
                final_price();
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
   
    if (type === "Booking") {
           
        var booking_type = $("#booking_type").val();
        var is_active = $("#is_active").val();
        if (booking_type === "" || booking_type === "Query" || booking_type === undefined ) {
            
            if (Number(is_active) === 0) {

                alert(LOW_CREDIT_MSG);
               
                document.getElementById("booking").checked = false;
                return false;
            }
        }
    }

}

function addBookingDialog() {

    count_number++;
    var exp1 = /^[6-9]{1}[0-9]{9}$/;


    var p_contact_no = $('#booking_primary_contact_no').val();
    var alternate_contact_no = $('#booking_alternate_contact_no').val();
    var address = $('#booking_address').val();
    var service = $("#service_id option:selected").text();
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
   // var customer_paid = $("#grand_total_price").val();
    
    if (pincode.length !== 6) {

        alert("Please Select 6 Digit Valid Pincode Number");

        return false;
    }

    if (source_code === "Select Booking Source") {

        alert("Please Select Booking Source");

        return false;
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
            if (address === "") {

                alert("Please fill Address ");
                return false;
            } else {

                if (pincode === "") {

                    alert("Please fill pincode ");
                    return false;
                }
            }
            
            if(booking_type ==="" || booking_type === "Query" || booking_type === undefined){
                if(Number(is_active) === 0){

                    alert(LOW_CREDIT_MSG);
                    return false;
                 }  
            } 
    
    
        } else {
            if ($('input[name=internal_status]:checked').length > 0) {
                // something when checked
            } else {

                alert("For Query, Internal Status is MANDATORY.");
                return false;
            }
        }
    }

    if (p_contact_no === "") {

        alert("Please fill Phone Number ");
        return false;
    }

    if (p_contact_no !== "" && !p_contact_no.match(exp1)) {
        alert("Enter Valid Phone Number Only");
        return false;
    }
    
    if (alternate_contact_no !== "" && !alternate_contact_no.match(exp1)) {
        alert("Enter Valid Alternate Phone Number Only");
        return false;
    }

    if (city === "" || city === "Select City") {

        alert("Please fill city ");
        return false;
    }

    var partner_id = $("#source_code").find(':selected').attr('data-id');
    var grand_total_price = $("#grand_total_price").val();
    if (Number(grand_total_price) === 0) {
       
        if (partner_id !== "247001") {
            var order_id = $('#order_id').val();
            if (order_id === "" && dealer_phone_number === "") {

                alert('Please Fill Order Id Or Dealer Phone Number');
                return false;
            }
            
        }
    }
    if(dealer_phone_number !=="" && !dealer_phone_number.match(exp1)){
        alert('Please Enter Valid Dealer Phone Number');   
        return false;
    }
    
    if(dealer_phone_number !=="" && dealer_name === ""){
        alert("Please Enter Dealer Name");
        return false;
    }
//    if(customer_paid == 0  && type === "Booking"){
//        var select_model = $(".select-model");
//        var input_model = $(".input-model");
//        for(var tt = 0; tt< select_model.length; tt++){
//            var select_model_value = $(select_model[tt]).val();
//            var input_model_value = $(input_model[tt]).val();
//            if(select_model_value || input_model_value){
//            }
//            else{
//                  alert("Please Add Model Number");
//            }
//        }
//    }
    if(type === "Booking"){
        for(var t=1; t<=div_count; t++){
            var p_date_value = $("#purchase_date_"+t).val();
            if (p_date_value === "") {
                alert("Purchase Date Should not be blank");
                return false;
            } 
        }
    }

    if (booking_date === "") {
        alert("Please fill Booking date ");
        return false;
    } else {
        //Adding Previous date validation on Booking Edit
//        var selectedDate = booking_date;
//        var d = new Date();
//        var today = formatDate(d);
//        selectedDate = new Date(selectedDate);
//        today = new Date(today);
//        if (selectedDate < today) {
//            $('#myModal').modal('toggle');
//            alert("Please select Today or Future date ");
//            return false;
//        }
    }

    if (timeslot === null) {

        alert('Please Select Booking Time Slot');
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

    for (var k = 1; k <= numItems; k++) {
        cloned_price(regex1, priceIndexClone, k);

    }
    $("#submitform").button('loading');
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
        alert("Please select Capacity " + i);
        return false;
    }

    var model_number = $("#model_number_" + i).val();
    if (model_number === null) {
        alert("Please fill Model Number " + i);
        return false;
    }

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
    ;

    if ($("#checkbox_" + div_no[1] + "_" + div_no[2]).is(':checked')) {

        $("#discount_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);
        $("#partner_paid_basic_charges_" + div_no[1] + "_" + div_no[2]).attr("readonly", false);

    }
    else {
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
    console.log(upcountry_data);
    is_upcountry = 0;
    non_upcountry = 0;
    count = 0;
    $("input[type=checkbox]:checked").each(function (i) {
        count = count + 1;

        var id = this.id.split('checkbox_');

        var up_val = $("#is_up_val_" + id[1]).val();
        if (Number(up_val) === 1) {
            is_upcountry = 1;
        } else  if (Number(up_val) === -1) {
            non_upcountry = -1;
        }
    });
    if (count > 0) {
        if(is_upcountry == 0){
             var data1 = jQuery.parseJSON(upcountry_data);
            if (data1.message === "UPCOUNTRY BOOKING" || data1.message === "UPCOUNTRY LIMIT EXCEED") {

                var upcountry_charges = (Number(DEFAULT_UPCOUNTRY_RATE) * Number(data1.upcountry_distance)).toFixed(2);
                total_price = $("#grand_total_price").val();
                $("#upcountry_charges").val(upcountry_charges);
                $("#grand_total_price").val(Number(total_price) + Number(upcountry_charges));

            } else {
                $("#upcountry_charges").val("0");
            }
            $('#submitform').attr('disabled', false);
            
        } else if(non_upcountry === -1){
            $("#upcountry_charges").val("0");
            $('#submitform').attr('disabled', false);
            final_price();
             
        } else if (is_upcountry === 1) {
            var total_price = $("#grand_total_price").val();
            var data1 = jQuery.parseJSON(upcountry_data);
            console.log(data1);
            var partner_approval = Number(data1.partner_upcountry_approval);

            if (data1.message === "UPCOUNTRY BOOKING") {
                $("#upcountry_charges").val("0");
                $('#submitform').attr('disabled', false);

            } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 0) {
                $('#submitform').attr('disabled', true);
                alert("This is out station Booking, not allow to submit Booking/Query. Upcountry Distance " + data1.upcountry_distance + " KM");
            } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 1) {
                alert("This is out station boking, Waiting for Partner Approval. Upcountry Distance " + data1.upcountry_distance + " KM");
                $('#submitform').attr('disabled', false);
            } else {
                // $("#upcountry_charges").val("0");
                $('#submitform').attr('disabled', false);
            }
            

        } 
        
    } else {
        final_price();
        $("#upcountry_charges").val("0");
        $('#submitform').attr('disabled', true);
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
                        $('#submitform').attr('disabled', false); 
                        document.getElementById("error_pincode").style.borderColor = "red";
                         document.getElementById("error_pincode").innerHTML = "";
                    } 
                }
                 
            }); 
        }
    }
    $(document).ready(function(){
         $("#booking_pincode").keyup(function(event) {
       
            check_pincode();
            getBrandForService();
        
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
    postData['partner_id'] = $("#source_code").find(':selected').attr('data-id');
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    postData['category'] = $("#appliance_category_" + div_no[2]).val();
    postData['capacity'] = $("#appliance_capacity_" + div_no[2]).val();

    if (postData['category']) {
        sendAjaxRequest(postData, modelServiceUrl).done(function (data) {
            var obj = JSON.parse(data);
            if(obj.status === false){
                $('.select-model').hide();
                $('.input-model').show();
                $('.input-model').removeAttr('disabled');
            }else{
                $('.select-model').show();
                $('.input-model').attr('disabled', 'disabled');
                $('.input-model').hide();
                $(".select-model#model_number_" + div_no[2]).html(obj.msg);
            }
            
        });
    }
}
 







