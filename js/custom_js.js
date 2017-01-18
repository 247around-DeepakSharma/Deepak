
 var brandServiceUrl = baseUrl + '/employee/booking/getBrandForService/';
 var applianceUrl = baseUrl + '/employee/booking/get_appliances/';
 var categoryForServiceUrl = baseUrl + '/employee/booking/getCategoryForService/';
 var CapacityForCategoryUrl = baseUrl + '/employee/booking/getCapacityForCategory/';
 var SelectStateUrl = baseUrl + '/employee/booking/get_state_by_city';
 var pricesForCategoryCapacityUrl = baseUrl + '/employee/booking/getPricesForCategoryCapacity/';
 var count_number = 0;
  
  
  function getAppliance(service_id){

    var postData = {};
    postData['source_code'] = $("#source_code").val();

    var service = $("#service_id option:selected").text();
    $("#services").val(service);
    
    sendAjaxRequest(postData, applianceUrl+service_id).done(function(data) {
      var data1 = jQuery.parseJSON(data);
      $("#partner_type").val(data1.partner_type);
     
      $("#service_id").html(data1.services);  
      
      getBrandForService();

    });
  }

  function getBrandForService() {

    var postData = {};
    postData['service_id'] = $("#service_id").val();
    postData['source_code'] = $("#source_code").val();

    var service = $("#service_id option:selected").text();
    $("#services").val(service);
    
    sendAjaxRequest(postData, brandServiceUrl).done(function(data) {
      var data1 = jQuery.parseJSON(data);
      $("#partner_type").val(data1.partner_type);
     
      $(".appliance_brand").html(data1.brand);  
       

    });
  }
    
  function getCategoryForService(div_id) {
    var postData = {};
    var div_no = div_id.split('_');
    
    postData['service_id'] = $("#service_id").val();
    postData['partner_code'] = $("#source_code option:selected").val();
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    
    sendAjaxRequest(postData, categoryForServiceUrl).done(function(data) {

        if(div_id === undefined){
          $(".appliance_category").html(data);
          $(".appliance_capacity").html(data2); 

        } else {

           $("#appliance_category_"+div_no[2]).html(data); 
           var data2 = "<option disabled></option>";
           $("#appliance_capacity_"+div_no[2]).html(data2); 
           $("#priceList_"+div_no[2]).html("");
           
        }
        
    });

  }

    
  function getCapacityForCategory(category, div_id) {
    var postData = {};
    var div_no = div_id.split('_');

    postData['service_id'] = $("#service_id").val();
    postData['partner_code'] = $("#source_code option:selected").val();
    postData['category'] = category;
    postData['partner_type'] =  $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();

    
    sendAjaxRequest(postData, CapacityForCategoryUrl).done(function(data) {
      

        $("#appliance_capacity_"+div_no[2]).html(data);
    
        if (data !== "<option></option>") {
            $("#priceList_"+div_no[2]).html(""); 
            
            getPricesForCategoryCapacity(div_id);
          
        } else {
            $("#priceList_"+div_no[2]).html(""); 
            
            
            getPricesForCategoryCapacity(div_id);
            
        }

    });
  }
    
  function getPricesForCategoryCapacity(div_id) {
    
    var postData = {};       
    var div_no = div_id.split('_');
    
    postData['service_id'] = $("#service_id").val();
    postData['brand'] = $('#appliance_brand_'+ div_no[2]).val();
    postData['category'] = $("#appliance_category_"+div_no[2]).val();
    postData['partner_code'] = $("#source_code option:selected").val();  
    postData['partner_type'] =  $("#partner_type").val();
   
    postData['clone_number'] = div_no[2];

    if($("#appliance_capacity_"+div_no[2]).val()!=="") {

        postData['capacity'] = $("#appliance_capacity_"+div_no[2]).val();

    } else {

       postData['capacity'] = "";
    }

    sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function(data) {
    
        $("#priceList_"+div_no[2]).html(data);
        final_price();

    });
    
  }

  function final_price(){
    var price = 0;
    var price_array ;
    var around_discount = 0;
    var partner_discount = 0;
    var get_around_discount = 0;
    var get_partner_discount = 0;

     $("input[type=checkbox]:checked").each(function(i) {
        price_array = $(this).val().split('_');
        //console.log(price_array);
        price += Number(price_array[1]);
        get_around_discount = $('#discount_'+price_array[2]+"_"+price_array[3]).val();
        around_discount += Number(get_around_discount);
       // console.log('#partner_paid_basic_charges_'+price_array[2]+"_"+price_array[3]);
        get_partner_discount = $('#partner_paid_basic_charges_'+price_array[2]+"_"+price_array[3]).val();
        
        partner_discount += Number(get_partner_discount);
   
    });

     var final_price = Number(price) - Number(around_discount) - Number(partner_discount);
  
    $("#grand_total_price").val(final_price);
    
  }

  $(document).on('keyup', '.discount', function(e) {
    final_price();    
});

  $(document).on('keyup', '.partner_discount', function(e) {
    final_price();    
});


 function addBookingDialog(){

     count_number++;

    
     var p_contact_no = $('#booking_primary_contact_no').val();
     var address = $('#booking_address').val();
     var service = $("#service_id option:selected").text();
     var pincode = $("#booking_pincode").val();
     var city = $("#booking_city").val();
     var booking_date = $("#booking_date").val();
     var timeslot = $('#booking_timeslot').val();
     var type = $('input[name=type]:checked', '#booking_form').val(); 
     var source_code = $("#source_code option:selected").val();
     
     if(source_code === "Select Booking Source"){
        
         alert("Please Select Booking Source");
        
         return false;
      }

    if(service === null || service === "" || service === "Select Service"){

        alert('Please Select Booking Appliance');
        return false;

    }
    if ($("input[type=checkbox]:checked").length === 0) {
       
        alert('Please select at least one check box');
        return false;
      
    }
    
    if(type === null || type === undefined){
       
        alert("Please Select Booking Type ");
        return false;

    }  else {
          if(type === "Booking"){
            if(address === ""){
             
              alert("Please fill Address "); 
              return false;
            } else {

              if(pincode === ""){
               
                alert("Please fill pincode "); 
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

     if(p_contact_no ===""){
        
        alert("Please fill Phone Number "); 
        return false;
     }

     if(city === "" || city ==="Select City"){
        
        alert("Please fill city "); 
        return false;
     }
      
     
    if(source_code === "SS" || source_code  === 'SP' || source_code === "SZ"){

        var order_id = $('#order_id').val();
        
        if(order_id === ""){
             
              alert('Please Fill Order Id');
              return false;
        }
    }
 
    if(booking_date === ""){
       
        alert("Please fill Booking date "); 
        return false;
     }else{
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

      if(timeslot === null){
         
         alert('Please Select Booking Time Slot');
        return false; 
      }

     if(count_number >1 ){

        $('.clone_m').html("");
     }
    
    var regex1 = /^(.+?)(\d+)$/i; 
    
    var priceIndexClone = $("#bpriceList1").length +1;
   
    var numItems = $('.clonedInput').length;
    $status = 1;
    for(var i = 1; i<= numItems; i++){
      var indexClone = $(".preview_booking").length +1;

      if(i !== 1)
        cloned_model(regex1, indexClone);
      
      var set_st = setAppliances(i);
      if(set_st ===  false){
          $status = 0;
      }
     
    }
    
    if($status === 0){
        return false;
    }

    $("#submitform").button('loading');
    for(var k =1; k<= numItems; k ++){
      cloned_price(regex1, priceIndexClone,k);
     
   }

}

function setAppliances(i){
  
  var brand = $("#appliance_brand_"+i).val();
  
  if(brand === null){
    
    alert("Please select Brand " +i);
    return false;
  }

  var appliance_category = $("#appliance_category_"+i).val();
  if(appliance_category === null){

    alert("Please select Category " +i);
    return false;
  }

  var appliance_capacity =$("#appliance_capacity_"+i).val();

  if(appliance_capacity === null){
    alert("Please select Capacity " +i);
    return false;
  }

  var model_number =$("#model_number_"+i).val();
  if(model_number === null){

    alert("Please fill Model Number " +i);
    return false;
  }

}  

  function cloned_model(regex1, indexClone){
   
      $("#preview_booking1").clone()
          .appendTo(".clone_m")
          .attr("id", "rat" +  indexClone)
          .find("*")
          .each(function() {
            
            var id = this.id || "";
            var match = id.match(regex1) || [];
            //console.log(match[1]);
            if (match.length === 3) {
                this.id = match[1] + (indexClone);
            }
      });

      indexClone++;
    
  }

  function cloned_price(regex1, indexClone,i){

    var checkduplicate = $("#bat" +i).length;
    
    if(checkduplicate >0){
      // $('bat'+i).remove();

    } else {
          $("#priceList_"+i).clone()
          .appendTo("#bpriceList_"+i)
          .attr("id", "bat" +  i)
          .find("*")
          .each(function() {
            
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

function enable_discount(div_id){

  var div_no = div_id.split('_');;

  if ($("#checkbox_"+div_no[1]+"_"+div_no[2]).is(':checked')){

     $("#discount_"+div_no[1]+"_"+div_no[2]).attr("readonly", false);
     $("#partner_paid_basic_charges_"+div_no[1]+"_"+div_no[2]).attr("readonly", false);

  }
  else{
    //$("#discount_"+div_no[1]+"_"+div_no[2]).val(0);

     $("#discount_"+div_no[1]+"_"+div_no[2]).attr("readonly", true);
    
      $("#partner_paid_basic_charges_"+div_no[1]+"_"+div_no[2]).attr("readonly", true);

  }

  //final_price();
}

 $(document).ready(function () {
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
       
        if (confirm_call === true) {
            
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    console.log(response);
                   
                }
            });
        } else {
            return false;
        }

}

function enable_submit_button(){
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

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}





       
    
  