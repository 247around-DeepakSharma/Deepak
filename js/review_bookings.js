var review_completeUrl = baseUrl + '/employee/booking/complete_review_booking/';
var admin_remarksUrl = baseUrl + '/employee/booking/reject_booking_from_review';
var partner_remarksUrl = baseUrl + '/employee/partner/reject_booking_from_review/';

$(document).on("click", ".open-AddBookingDialog", function () {
    $('#modal-title2').text("");
    var service_charge = document.getElementById("service_charge" + this.id).innerHTML;
    var additional_charge = document.getElementById("additional_charge" + this.id).innerHTML;
    var parts_cost = document.getElementById("parts_cost" + this.id).innerHTML;
    var booking_id = $('#booking_id' + this.id).val();
    var internal_status = document.getElementById("internal_status" + this.id).innerHTML;
    var cancellation_reason = document.getElementById("cancellation_reason" + this.id).innerHTML;
    var service_center_remarks = document.getElementById("service_center_remarks" + this.id).innerHTML;
     var admin_remarks = document.getElementById("admin_remarks_" + this.id).innerHTML;

    $('#modal-title2').text(booking_id);
    $('#input_service_charge').val(service_charge);
    $('#input_additional_charge').val(additional_charge);
    $('#input_parts_cost').val(parts_cost);
    $('#input_internal_status').val(internal_status);
    $('#input_cancellation_reason').val(cancellation_reason);
    $('#input_service_center_remarks').val(service_center_remarks);
    $('#input_admin_remarks').val(admin_remarks);

    sum_charges();

});

function getData() {
    var postData = {};
    postData['booking_id'] = $('#modal-title2').text();
    postData['service_charge'] = $("#input_service_charge").val();
    postData['additional_charge'] = $("#input_additional_charge").val();
    postData['parts_cost'] = $("#input_parts_cost").val();
    postData['amount_paid'] = $("#input_total_charge").val();
    postData['internal_status'] = $("#input_internal_status").val();
    postData['admin_remarks'] = $("#input_admin_remarks").val();
    postData['cancellation_reason'] = $("#input_cancellation_reason").val();
    postData['service_center_remarks'] = $("#input_service_center_remarks").val();

    console.log(postData);

    return postData;
}

function sum_charges() {
    var postData = getData();
    var sum = Number(postData['service_charge']) + Number(postData['additional_charge']) + Number(postData['parts_cost']);
    $('#input_total_charge').val(sum);
}

$(document).on('keyup', '#input_service_charge', function (e) {

    sum_charges();
});
$(document).on('keyup', '#input_additional_charge', function (e) {

    sum_charges();
});
$(document).on('keyup', '#input_parts_cost', function (e) {

    sum_charges();
});

function close_model() {
    $('.modal-title').text("");
    $('#input_service_charge').val("");
    $('#input_additional_charge').val("");
    $('#input_parts_cost').val("");
}

function approve_booking() {
    var img = $('<img />', { 
  id: 'Myid',
  src:  baseUrl +"/images/loader.gif",
  alt: 'MyAlt'
});
img.appendTo($('#edit_form'));
    var postData = getData();
    
    sendAjaxRequest(postData, review_completeUrl).done(function (data) {
       
	   location.reload();
    });
}

function sendAjaxRequest(postData, url) {
    return $.ajax({
	data: postData,
	url: url,
	type: 'post'
    });
}


$(document).on("click", ".open-adminremarks", function () {
//    $('#modal-title').text("");
    var id = this.id;
    var split_id = id.split('_');

    var input_textarea = $('#admin_remarks' + split_id[1]).val();
    var booking_id = $('#booking_id' + split_id[1]).val();
    $("#id_no").val(split_id[1]);
    $('#modal-title').text(booking_id);
    $('#textarea').val(input_textarea);

});

function send_remarks() {
    var bookingID = $('#modal-title').text();
    var postData = {};
    postData['booking_id'] = $('#modal-title').text();
    postData['admin_remarks'] = $('#textarea').val();
    postData['rejected_by'] = $('#admin_id').val();
    postData['internal_booking_status'] = $("#internal_boking_status").val();
    sendAjaxRequest(postData, admin_remarksUrl).done(function (data) {
        alert(data);
        document.getElementById("row_"+bookingID).style.background = "#89d4a7";
    });

}

    function send_remarks_multitab(review_status, is_partner) {
        var sub_id = "";
        if ($('#sub_id').length && $('#sub_id').val() != '') {
            sub_id = $('#sub_id').val();
        }
        var str = review_status + "_" + is_partner + sub_id;
        var bookingID = $('#modal_booking_id_' + str).val();
        var postData = {};

        // Check whether rejection reason is selected or not
        $("#remarks_msg_" + str).html('');
        if (($('#select_' + str).val() == null) || ($('#select_' + str).val() == undefined))
        {
            $("#remarks_msg_" + str).html("*Select Rejection Reason");
            return false;
        }
    
        postData['booking_id'] = bookingID;
        postData['remarks'] = $('#textarea_'+str).val();
        // selected rejection reason drop down value
        postData['admin_remarks'] = $('#select_' + str).val(); 
        postData['rejected_by'] = $('#admin_id_' + str).val();
        postData['internal_booking_status'] = $("#internal_boking_status_" + str).val();
        console.log(postData);
        $('#loader_gif_' + str).show();
        $('#btn_send_remarks_' + str).prop("disabled", true);
        sendAjaxRequest(postData, admin_remarksUrl).done(function (data) {
            alert(data);
            $('#loader_gif_' + str).hide();
            $('#btn_send_remarks_' + str).prop("disabled", false);
            document.getElementById("row_" + bookingID).style.background = "#89d4a7";
            $('.modal').modal('hide');
        });
    
}

function review_search(status,is_partner,sub_id,sort_on){
    sub_id = sub_id || '';
    sort_on = sort_on || '0';        
    // Add sort order value 
    var sort_order = '0';
    if($("input[name='sort_order']").length && $("input[name='sort_order']:checked"). val() !== undefined){
        sort_order = $("input[name='sort_order']:checked"). val();
    } 
    
    var bookingID = $('#search_'+status+'_'+is_partner+sub_id).val();
     
    if(bookingID == '') {
        bookingID = '0';
    }
    var cancellation_reason = '0';
    if($('#cancellation_reason_'+is_partner+sub_id).length && $('#cancellation_reason_'+is_partner+sub_id).val() != '') {
	cancellation_reason = $('#cancellation_reason_'+is_partner+sub_id).val();
    }

    var state_input_id = '#state_cancelled_'+is_partner+'_'+status+sub_id;
    var state = '0';
    if(status == 'Completed') {
        state_input_id = '#state_completed_'+is_partner+'_'+status+sub_id;
    }
    if($(state_input_id).length && $(state_input_id).val() != ''){
	state = $(state_input_id).val();
    }

    var partner_input_id = '#partner_cancelled_'+is_partner+'_'+status+sub_id;
    var partner = '0';
    if(status == 'Completed' || status == "Completed_By_SF") {
        partner_input_id = '#partner_completed_'+is_partner+'_'+status+sub_id;
    }
    if($(partner_input_id).length && $(partner_input_id).val() != '') {
	partner = $(partner_input_id).val();
    }
    
    var request_type = '0';
    if($('#request_type_'+is_partner+'_'+status+sub_id).length && $('#request_type_'+is_partner+'_'+status+sub_id).val() != '') {    
        var request_type_id = '#request_type_'+is_partner+'_'+status+sub_id;
        request_type = $(request_type_id).val();
    }
    
    // set value for the Review Range filter
    var min_review_age = 'not_set';
    var max_review_age = 'not_set';
    if($('#review_age_min_'+is_partner+'_'+status+sub_id).length && $('#review_age_min_'+is_partner+'_'+status+sub_id).val() != '') {
        var min_review_age_id = '#review_age_min_'+is_partner+'_'+status+sub_id;
        min_review_age = $(min_review_age_id).val();
    }
    if($('#review_age_max_'+is_partner+'_'+status+sub_id).length && $('#review_age_max_'+is_partner+'_'+status+sub_id).val() != '') {
        var max_review_age_id = '#review_age_max_'+is_partner+'_'+status+sub_id;
        max_review_age = $(max_review_age_id).val();
    }
    
    var service = '0';
    if($('#service_id_'+is_partner+'_'+status+sub_id).length && $('#service_id_'+is_partner+'_'+status+sub_id).val() != '') {    
        var service_id = '#service_id_'+is_partner+'_'+status+sub_id;
        service = $(service_id).val();
    }
    
    var free_paid = '0';
    if($('#free_paid_'+is_partner+'_'+status+sub_id).length && $('#free_paid_'+is_partner+'_'+status+sub_id).val() != '') {    
        var free_paid_id = '#free_paid_'+is_partner+'_'+status+sub_id;
        free_paid = $(free_paid_id).val();
    }
    
    var sf = '0';
    if($('#sf_id_'+is_partner+'_'+status+sub_id).length && $('#sf_id_'+is_partner+'_'+status+sub_id).val() != '') {    
        var sf_id = '#sf_id_'+is_partner+'_'+status+sub_id;
        sf = $(sf_id).val();
    }
    
    var sf_wise_data = 0;
    if($('#sf_wise_data_'+is_partner+'_'+status+sub_id).length && $('#sf_wise_data_'+is_partner+'_'+status+sub_id).val() != '') {    
        var sf_wise_data_id = '#sf_wise_data_'+is_partner+'_'+status+sub_id;
        sf_wise_data = $(sf_wise_data_id).val();
    }
    
    var tab = "#tabs-3";
    if(status == "Completed"){
       var tab = "#tabs-2";
    }
    else if(status == "Completed_By_SF"){
       var tab = "#tabs-5";
    }
    if(is_partner){
        var tab = "#tabs-4";
    }
    if(sub_id != ""){
        var tab = "#tabs-6";
    }
    // Load View
    if(sf_wise_data == 0)
    {
        load_view('employee/booking/review_bookings_by_status/'+status+'/0/'+is_partner+'/'+bookingID+'/'+ cancellation_reason+'/'+partner+'/'+state+'/'+request_type+'/'+min_review_age+'/'+max_review_age+'/'+sort_on+'/'+sort_order+'/'+service+'/'+free_paid+'/'+sf+'/'+sf_wise_data, tab,0);
    }
    else
    {
        window.open(baseUrl+'/employee/booking/review_bookings_by_status/'+status+'/0/0/'+bookingID+'/0/'+partner+'/'+state+'/'+request_type+'/'+min_review_age+'/'+max_review_age+'/'+sort_on+'/'+sort_order+'/'+service+'/'+free_paid+'/'+sf+'/'+sf_wise_data, '_self');        
    }
}
