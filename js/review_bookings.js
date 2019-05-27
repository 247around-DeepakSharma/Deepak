
var review_completeUrl = baseUrl + '/employee/booking/complete_review_booking/';
var admin_remarksUrl = baseUrl + '/employee/booking/reject_booking_from_review/';
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
    $('#modal-title').text("");
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
function review_search(status,is_partner){
    bookingID = $('#search').val();
    if(bookingID == '') {
        bookingID = 0;
    }
    var cancellation_reason = '';
    if($('#cancellation_reason').length){
	cancellation_reason = $('#cancellation_reason').val();
    }
   
    var tab = "#tabs-3";
    if(status == "Completed"){
       var tab = "#tabs-2";
    }
    if(is_partner){
        var tab = "#tabs-4";
    }
 
     load_view('employee/booking/review_bookings_by_status/'+status+'/0/'+is_partner+'/'+bookingID+'/'+cancellation_reason, tab,0);
}


