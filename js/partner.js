
 var getUrl = window.location;

 //Un-comment below line for localhost
 //var baseUrl = getUrl .protocol + "//" + getUrl.host  + "/" + getUrl.pathname.split('/')[1];

 //Comment below line for localhost, this is for main server
 var baseUrl = getUrl .protocol + "//" + getUrl.host ;

 var addbooking_form = baseUrl + '/partner/get_addbooking_form/';
 var getCategoryUrl = baseUrl + '/employee/partner/get_category';
 var getCapacityUrl = baseUrl + '/employee/partner/get_capacity';
 var getPriceTableUrl = baseUrl + '/employee/partner/get_price_table';
  var count_number = 0;



function call_booking_form(){
	
	var phone_number  = $("#customer_phone_number").val();
    window.location.href = addbooking_form + phone_number;
	
}

function getCategory(){
	var postData = {};
	postData['service_id'] = $("#service_id").val();
	postData['city'] = $('#booking_city').val();
	postData['partner_id'] = $("#partner_id").val();

	sendAjaxRequest(postData, getCategoryUrl).done(function(data) {

        $(".appliance_category").html(data);   
    });
}

function getCapacity(category, div_id){
	var postData = {};
	postData['service_id'] = $("#service_id").val();
	postData['city'] = $('#booking_city').val();
	postData['partner_id'] = $("#partner_id").val();
	postData['category'] = category;

    var div_no = div_id.split('_');
    sendAjaxRequest(postData, getCapacityUrl).done(function(data) {

        $("#appliance_capacity_"+div_no[2]).html(data);
    
        if (data != "<option></option>") {
            var capacity= $("#appliance_capacity_"+div_no[2]).val();
    
            get_price_table(div_id);
        } else {
    
            $("#appliance_capacity_"+div_no[2]).html(data);
            var capacity="NULL";
            get_price_table(div_id);
        }

    });
}
function get_price_table(div_id) {
    
    var postData = {};
    postData['service_id'] = $("#service_id").val();
    
    var div_no = div_id.split('_');
    postData['brand'] = $('#appliance_brand_'+ div_no[2]).val();
    postData['category'] = $("#appliance_category_"+div_no[2]).val();
    postData['partner_id'] = $("#partner_id").val();    
    //postData['city'] = $('#booking_city').val();
   
    postData['clone_number'] = div_no[2];

    if($("#appliance_capacity_"+div_no[2]).val()!="") {

        postData['capacity'] = $("#appliance_capacity_"+div_no[2]).val();

    } else {

       postData['capacity'] = "";
    }

    sendAjaxRequest(postData, getPriceTableUrl).done(function(data) {
      
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
     var service = $("#service_id").val();
     var pincode = $("#booking_pincode").val();
     var city = $("#booking_city").val();
     var booking_date = $("#booking_date").val();
     var timeslot = $('#booking_timeslot').val();
     var partner_source = $("#partner_source option:selected").text();
     var grand_total_price = $("#grand_total_price").val();
     var order_id  = $("#order_id").val();
     var landmark  = $("#landmark").val();
     
     var alt_contact_no = $('#booking_alternate_contact_no').val();
     var query_remarks =$("#query_remarks").val();
     
     if(name ==""){
     	$('#myModal1').modal('toggle');
        alert('Please fill Customer Name');
        return false;
      
     }

      if ($("input[type=checkbox]:checked").length === 0) {
        $('#myModal1').modal('toggle');
        alert('no way you submit it without checking a box');
        return false;
      
      }

     if(p_contact_no ==""){
        $('#myModal1').modal('toggle');
        alert("Please fill Phone Number "); 
        return false;
     }

     if(city == "" || city =="Select City"){
        $('#myModal1').modal('toggle');
        alert("Please fill city "); 
        return false;
     }

      if(partner_source == "Select Booking Source"){
        
         alert("Please Select Booking Source");
         $('#myModal1').modal('toggle');
         return false;
      }

     if(address == ""){
        $('#myModal1').modal('toggle');
        alert("Please fill Address "); 
        return false;
     }

     if(booking_date == ""){
        $('#myModal1').modal('toggle');
        alert("Please fill Booking date "); 
        return false;
     }
      
      if(timeslot == null){
         $('#myModal1').modal('toggle');
         alert('Please Select Booking Time Slot');
        return false; 
      }
     document.getElementById("b_order_id").innerHTML = order_id;
     document.getElementById("user_name").innerHTML = name;
     document.getElementById("landmark").innerHTML = landmark;
     document.getElementById("p_contact_no").innerHTML = p_contact_no;
     document.getElementById("b_alt_contact_no").innerHTML = alt_contact_no;
     document.getElementById("b_address").innerHTML = address;
     document.getElementById("b_email").innerHTML = email;
     document.getElementById("b_source").innerHTML = partner_source;
    
     document.getElementById("b_pincode").innerHTML =  pincode;
     document.getElementById("b_city").innerHTML = city;
     document.getElementById("b_date").innerHTML = booking_date;
     document.getElementById("b_timeslot").innerHTML = timeslot;
     document.getElementById('bgrand_total_charge').innerHTML = grand_total_price; 
     document.getElementById("bremarks").innerHTML = query_remarks;  

    

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
    $('#myModal1').modal('toggle');
    return false;
  }

  var appliance_category = $("#appliance_category_"+i).val();
  if(appliance_category == null){
    $('#myModal1').modal('toggle');
    alert("Please select Category " +i);
    return false;
  }

  var appliance_capacity =$("#appliance_capacity_"+i).val();

  var model_number =$("#model_number_"+i).val();
  if(model_number == null){
    $('#myModal1').modal('toggle');
    alert("Please fill Model Number " +i);
    return false;
  }

  var purchase_year =$("#purchase_year_"+i).val();

  var month = $("#purchase_month_"+i).val();


  document.getElementById("bbrand_"+i).innerHTML = brand;
  document.getElementById("bcategory_"+i).innerHTML = appliance_category;
  document.getElementById("bcapacity_"+i).innerHTML = appliance_capacity;
  document.getElementById("bmodel_"+i).innerHTML = model_number;
  
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
            console.log(match[1]);
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


$(document).ready(function () {
  getCategory();
});

function sendAjaxRequest(postData, url) {
     return $.ajax({
         data: postData,
         url: url,
         type: 'post'
     });
 }
