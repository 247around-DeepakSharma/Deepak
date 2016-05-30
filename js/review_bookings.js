 var getUrl = window.location;
// var baseUrl = getUrl .protocol + "//" + getUrl.host  + "/" + getUrl.pathname.split('/')[1];
var baseUrl = getUrl .protocol + "//" + getUrl.host ;

 var review_completeUrl = baseUrl + '/employee/new_booking/complete_review_booking/';
  var admin_remarksUrl = baseUrl + '/employee/new_booking/admin_remarks/';

 $(document).on("click", ".open-AddBookingDialog", function () {
   $('#modal-title2').text("");
    var service_charge = document.getElementById("service_charge" +this.id).innerHTML;
    var additional_charge = document.getElementById("additional_charge" + this.id).innerHTML;
    var parts_cost = document.getElementById("parts_cost"+this.id).innerHTML;
    var booking_id = $('#booking_id'+this.id).val();
    var internal_status = document.getElementById("internal_status"+this.id).innerHTML;;
  
        
    $('#modal-title2').text(booking_id);
    $('#input_service_charge').val(service_charge);
    $('#input_additional_charge').val(additional_charge);
    $('#input_parts_cost').val(parts_cost);
    $('#input_internal_status').val(internal_status);
    sum_charges();
        
 });

  function getData(){
      var postData ={};
      postData['booking_id'] = $('#modal-title2').text();
      postData['service_charge'] = $("#input_service_charge").val();
      postData['additional_charge'] = $("#input_additional_charge").val();
      postData['parts_cost'] = $("#input_parts_cost").val();
      postData['amount_paid'] = $("#input_total_charge").val();
      postData['internal_status'] = $("#input_internal_status").val();
      postData['admin_remarks'] = $("#input_admin_remarks").val();
        
      return postData;
  }

  function sum_charges(){
      var postData = getData();
      var sum = Number(postData['service_charge']) + Number(postData['additional_charge']) + Number(postData['parts_cost']);
      $('#input_total_charge').val(sum);
  }

  $(document).on('keyup', '#input_service_charge', function(e) {

      sum_charges();
  });
  $(document).on('keyup', '#input_additional_charge', function(e) {

    sum_charges();
  });
 $(document).on('keyup', '#input_parts_cost', function(e) {

    sum_charges();
  });

 function close_model(){
    $('.modal-title').text("");
    $('#input_service_charge').val("");
    $('#input_additional_charge').val("");
    $('#input_parts_cost').val("");
 }

 function approve_booking(){
   var postData = getData();
   console.log(postData);
   sendAjaxRequest(postData, review_completeUrl).done(function(data) {
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
      
     var input_textarea = $('#admin_remarks'+ split_id[1]).val();
     var booking_id = $('#booking_id'+ split_id[1]).val();
     $("#id_no").val(split_id[1]);
     $('#modal-title').text(booking_id);
     $('#textarea').val(input_textarea);

   });

   function send_remarks(){
      var postData = {};
      postData['booking_id'] = $('#modal-title').text();
      postData['admin_remarks'] = $('#textarea').val();

      sendAjaxRequest(postData, admin_remarksUrl).done(function(data) {
        location.reload();

      });

   }


