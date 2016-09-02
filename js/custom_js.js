
 var brandServiceUrl = baseUrl + '/employee/booking/getBrandForService/';
 var categoryForServiceUrl = baseUrl + '/employee/booking/getCategoryForService/';
 var CapacityForCategoryUrl = baseUrl + '/employee/booking/getCapacityForCategory/';
 var SelectStateUrl = baseUrl + '/employee/booking/get_state_by_city';
 var pricesForCategoryCapacityUrl = baseUrl + '/employee/booking/getPricesForCategoryCapacity/';
 var count_number = 0;

  function getBrandForService(service_id) {

    var postData = {};
    postData['service_id'] = $("#service_id").val();

    var service = $("#service_id option:selected").text();
    $("#services").val(service);

    sendAjaxRequest(postData, brandServiceUrl).done(function(data) {
     
      $(".appliance_brand").html(data);

      

    });
  }
    
  function getCategoryForService(div_id = "") {
    var postData = {};
    
    postData['service_id'] = $("#service_id").val();
    postData['booking_pincode'] = $('#booking_pincode').val();
    postData['partner_code'] = $("#source_code option:selected").val();
    
    sendAjaxRequest(postData, categoryForServiceUrl).done(function(data) {

        if(div_id ==""){
          $(".appliance_category").html(data);   
          

        } else {

           var div_no = div_id.split('_');
           $("#appliance_category"+div_no[2]).html(data);
        }
        

    });

  }

    
  function getCapacityForCategory(service_id, category, div_id) {
    var postData = {};
    
    postData['service_id'] = $("#service_id").val();
    postData['partner_code'] = $("#source_code option:selected").val();
    postData['booking_pincode'] = $('#booking_pincode').val();
    postData['category'] = category;

    var div_no = div_id.split('_');

    sendAjaxRequest(postData, CapacityForCategoryUrl).done(function(data) {
      

        $("#appliance_capacity_"+div_no[2]).html(data);
    
        if (data != "<option></option>") {
            var capacity= $("#appliance_capacity_"+div_no[2]).val();
    
            getPricesForCategoryCapacity(div_id);
        } else {
    
            $("#appliance_capacity_"+div_no[2]).html(data);
            var capacity="NULL";
            getPricesForCategoryCapacity(div_id);
        }

    });
  }
    
  function getPricesForCategoryCapacity(div_id) {
    
    var postData = {};
    postData['service_id'] = $("#service_id").val();
    
    var div_no = div_id.split('_');
    postData['brand'] = $('#appliance_brand_'+ div_no[2]).val();
    postData['category'] = $("#appliance_category_"+div_no[2]).val();
    postData['partner_code'] = $("#source_code option:selected").val();    
    postData['booking_pincode'] = $('#booking_pincode').val();
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

    var discount = 0;
    var price = 0;
    
    $("input.discount").each(function(){
          discount += Number($(this).val());        
    });
  
   price = get_selected_price();
    
    var final_price = Number(price) - Number(discount);
    $("#grand_total_price").val(final_price);

  }

  $(document).on('keyup', '.discount', function(e) {

    var discount = 0;
    var price = 0;
    $("input.discount").each(function(){
          discount += Number($(this).val());
        
    });
    
    price = get_selected_price();
    
    var final_price = Number(price) - Number(discount);

    $("#grand_total_price").val(final_price);
});


  function get_selected_price(){
    var price = 0;
    var price_array ;

     $("input[type=checkbox]:checked").each(function(i) {
        price_array = $(this).val().split('_');
        price += Number(price_array[1]);
   
    });

    return price;
  }

  $(document).on("click", ".open-AddBookingDialog", function () {

     count_number++;

     var name = $("#name").val();
     var p_contact_no = $('#booking_primary_contact_no').val();
     var address = $('#booking_address').val();
     var email = $('#booking_user_email').val();
     var service = $("#service_id option:selected").text();
     var pincode = $("#booking_pincode").val();
     var city = $("#booking_city").val();
     var booking_date = $("#booking_date").val();
     var timeslot = $('#booking_timeslot').val();
     var source = $("#source_code option:selected").text();
     var grand_total_price = $("#grand_total_price").val();
     
     var alt_contact_no = $('#booking_alternate_contact_no').val();
     var potential_value =$("#potential_value").val();
     var query_remarks =$("#query_remarks").val();
     var type = $('input[name=type]:checked', '#booking_form').val(); 


      if ($("input[type=checkbox]:checked").length === 0) {
        $('#myModal').modal('toggle');
        alert('Please select at least one check box');
        return false;
      
      }

     if(p_contact_no ==""){
        $('#myModal').modal('toggle');
        alert("Please fill Phone Number "); 
        return false;
     }

     if(city == "" || city =="Select City"){
        $('#myModal').modal('toggle');
        alert("Please fill city "); 
        return false;
     }

      if(source == "Select Booking Source"){
        
         alert("Please Select Booking Source");
         $('#myModal').modal('toggle');
         return false;
      }

      if(service == null || service == "" || service == "Select Service"){
         $('#myModal').modal('toggle');
         alert('Please Select Booking Service');
         return false;

      }

  
     if(booking_date == ""){
        $('#myModal').modal('toggle');
        alert("Please fill Booking date "); 
        return false;
     }

      if(timeslot == null){
         $('#myModal').modal('toggle');
         alert('Please Select Booking Time Slot');
        return false; 
      }

    if(type == null){
        $('#myModal').modal('toggle');
        alert("Please Select Booking Type ");
        return false;

    }  else {
          if(type == "Booking"){
            if(address == ""){
              $('#myModal').modal('toggle');
              alert("Please fill Address "); 
              return false;
            } else {

              if(pincode == ""){
                $('#myModal').modal('toggle');
                alert("Please fill pincode "); 
                return false;
              }
         }
      } else {
       if ($('input[name=internal_status]:checked').length > 0) {
        // something when checked
        } else {
          $('#myModal').modal('toggle');
          alert("For Query, Internal Status is MANDATORY."); 
          return false;
        }
      }
    }
      

     document.getElementById("user_name").innerHTML = name;
     document.getElementById("p_contact_no").innerHTML = p_contact_no;
     document.getElementById("b_alt_contact_no").innerHTML = alt_contact_no;
     document.getElementById("b_address").innerHTML = address;
     document.getElementById("b_email").innerHTML = email;
     document.getElementById("b_source").innerHTML = source;
     document.getElementById("b_service").innerHTML = service;
     document.getElementById("b_pincode").innerHTML =  pincode;
     document.getElementById("b_city").innerHTML = city;
     document.getElementById("b_date").innerHTML = booking_date;
     document.getElementById("b_timeslot").innerHTML = timeslot;
     document.getElementById('b_type').innerHTML = type;  
     document.getElementById('bgrand_total_charge').innerHTML = grand_total_price; 
     document.getElementById("bremarks").innerHTML = query_remarks;  
     document.getElementById("bpotential_value").innerHTML = potential_value;  

     if(count_number >1 ){

        $('.clone_m').html("");
     }
    
    var regex1 = /^(.+?)(\d+)$/i; 
    
    var priceIndexClone = $("#bpriceList1").length +1;
   
    var numItems = $('.clonedInput').length;
    
    for(var i = 1; i<= numItems; i++){
      var indexClone = $(".preview_booking").length +1;

      if(i !=1)
        cloned_model(regex1, indexClone);
      
      setAppliances(i);   
    }
    
    for(var k =1; k<= numItems; k ++){
      cloned_price(regex1, priceIndexClone,k);
     
   }

    if ( $("#submitform").hasClass('disabled') ) {
      
      $("#submitform").removeClass('disabled').addClass('active');
    }

});

function setAppliances(i){
  
  var brand = $("#appliance_brand_"+i).val();
  
  if(brand == null){
    
    alert("Please select Brand " +i);
    $('#myModal').modal('toggle');
    return false;
  }

  var appliance_category = $("#appliance_category_"+i).val();
  if(appliance_category == null){
    $('#myModal').modal('toggle');
    alert("Please select Category " +i);
    return false;
  }

  var appliance_capacity =$("#appliance_capacity_"+i).val();

  var model_number =$("#model_number_"+i).val();
  if(model_number == null){
    $('#myModal').modal('toggle');
    alert("Please fill Model Number " +i);
    return false;
  }

  var appliance_tags =$("#appliance_tags_"+i).val();
  var purchase_year =$("#purchase_year_"+i).val();

  var month = $("#purchase_month_"+i).val();


  document.getElementById("bbrand_"+i).innerHTML = brand;
  document.getElementById("bcategory_"+i).innerHTML = appliance_category;
  document.getElementById("bcapacity_"+i).innerHTML = appliance_capacity;
  document.getElementById("bmodel_"+i).innerHTML = model_number;
  document.getElementById("btags_"+i).innerHTML = appliance_tags;  
  document.getElementById("bpurchase_year_"+i).innerHTML = purchase_year;
  document.getElementById("bpurchase_month_"+i).innerHTML = month;
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
            if (match.length == 3) {
                this.id = match[1] + (indexClone);
            }
      })

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
            console.log(match[1]);
            if (match.length == 3) {
                this.id = match[1] + (indexClone);
            }
      })
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

  final_price();
}

 $(document).ready(function () {
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
       
        if (confirm_call == true) {
            
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



       
    
  