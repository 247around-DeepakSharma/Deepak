<script src="<?php echo base_url();?>js/custom_js.js?v=<?=mt_rand()?>"></script>
<style type="text/css">
    /* example styles for validation form demo */
    .err {
    color: red;
    }
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
    }
    .col-md-3{
        width: 25%;
    }
    
    #dealer_list{
        float:left;
        width:88%;
        max-height: 300px;
        list-style:none;
        margin-top:0px;
        padding:0;
        position: absolute;
        z-index: 99999;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 3px;
        overflow-y: auto;
    }
    #dealer_list li{padding: 10px; border-bottom: #bbb9b9 1px solid;}
    #dealer_list li:hover{background:#e9ebee;cursor: pointer;}
    
</style>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="#" onSubmit="document.getElementById('submitform').disabled=true;"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 1</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if($this->session->userdata('success')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
                                }
                                ?>
                            <?php if($this->session->userdata('error')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('error') . '</strong>
                                </div>';
                                }
                                ?>
                           
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12 <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no">Mobile * <span id="error_mobile_number" style="color:red"></span></label>
                                    <input autocomplete="off" type="text" class="form-control"  id="booking_primary_contact_no" placeholder="Enter Mobile Number" name="booking_primary_contact_no" 
                                           value = "" required>
                                    
                                </div>
                            </div>
                            <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                            <input type="hidden" name="user_id" id="user_id" value="" />
                            <input type="hidden" name="partner_id" id="partner_id" value="" />
                            <input type="hidden" name="booking_type" id="booking_type" value="" />
                           
                            <input type="hidden" name="appliance_name" id="appliance_name" value=""/>
                            <div class="col-md-4" >
                                <div class="form-group col-md-12 <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                                    <label for="user_name">Name * <span id="error_name" style="color: red;"></span></label>
                                    
                                    <input type="text" class="form-control" id="name" name="user_name" 
                                           value = "" placeholder="Please Enter User Name">
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                    <label for="Appliance">Appliance * <span id="error_appliance" style="color: red;"></span></label>
                                    <select type="text" class="form-control"  id="service_name" name="service_id"   required onchange="return get_city(), get_brands(), get_category(), get_capacity()">
                                        <option selected disabled>Select Appliance</option>
                                        
                                    </select>
                                    <?php echo form_error('service_id'); ?>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12  <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?> ">
                                    <label for="booking_pincode">Pincode *   <span id="error_pincode" style="color: red;"></span></label>
                                    <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} else { echo set_value('booking_pincode');} ?>" placeholder="Enter Area Pin" required>
                                    <?php echo form_error('booking_pincode'); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('city') ) { echo 'has-error';} ?>">
                                    <label for="city ">City * <span id="error_city" style="color: red;"></span><span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                                    
                                    <select type="text" class="form-control"  id="booking_city" name="city" required>
                                        <option selected="selected" disabled="disabled">Select City</option>
                                       
                                    </select>
                                    <?php echo form_error('city'); ?>
                                </div>
                            </div>
                            
                            <input type="hidden" name="appliance_name" id="appliance_name" value=""/>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                                    <label for="appliance_brand">Brand *   <span style="color:grey;display:none" id="brand_loading">Loading ...</span> <span id="error_brand" style="color: red;"></label>
                                   
                                    <select type="text" class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required onchange="return get_category()">
                                        <option selected disabled >Select Brand</option>
                                    </select>
                                    <?php echo form_error('appliance_brand'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                                    <label for="appliance_category">Category *<span style="color:grey;display:none" id="category_loading">Loading ...</span> <span id="error_category" style="color: red;"></label>
                                    
                                    <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity()">
                                        <option selected disabled >Select Appliance Category</option>
                                    </select>
                                    <?php echo form_error('appliance_category'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                                    <label for="appliance_capacity">Capacity   <span style="color:grey;display:none" id="capacity_loading">Loading ...</span> <span id="error_capacity" style="color: red;"></label>
                                   
                                    <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" onchange="return get_models(), getPrice()">
                                        <option selected disabled value="option1">Select Appliance Capacity</option>
                                    </select>
                                    <?php echo form_error('appliance_capacity'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="Model Number">Model Number  <span id="error_model" style="color: red;"></label>
                                    <span id="model_number_2">
                                    <select class="form-control select-model"  name="model_number" id="model_number_1" >
                                        <option selected disabled>Select Model</option>
                                    </select>
                                    </span>
                                    <?php echo form_error('model_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                    <label for="Booking Date ">Booking Date *</label>
                                    <input type="date" class="form-control"  id="booking_date" name="booking_date"  value = "<?php if(date('H') < '12'){echo  date("Y-m-d");}else{ echo date("Y-m-d", strtotime("+1 day"));} ?>"  >
                                    <!--   -->
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 " id="purchase_d">
                                    <label for="Purchase Date">Purchase Date <span id="error_purchase" style="color: red;"></label>
                                <div class="input-group date">
                                    <input id="purchase_date" class="form-control purchase_date"  name="purchase_date" type="text" value = "" max="<?=date('Y-m-d');?>" autocomplete='off' onkeydown="return false" >
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="Appliance unit ">Appliance Unit* <span id="error_seller" style="color: red;"></label>
                                     
                                    <select type="text" class="form-control"  id="appliance_unit" name="appliance_unit" >
                                      
                                        <?php for($i =1; $i <26; $i++) { ?>
                                        <option value="<?php echo $i;?>"><?php echo $i; ?></option>
                                        <?php }?>
                                       
                                       
                                        
                                    </select>
                                    <!--   -->
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                            
                            
                            <!-- end col-md-6 -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- end -->
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 2</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">
                                <div  class="col-md-12">
                                    <table class="table priceList table-striped table-bordered" name="priceList" id="priceList">
                                        <tr class="text-center">
                                            <th class="text-center">Service Category</th>
                                            <th class="text-center">Final Charges</th>
                                            <th class="text-center" id="selected_services">Selected Services</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                           
                                <div class="col-md-4 ">
                                <div class="form-group col-md-12  ">
                                    <label for="Grand total">Grand Total * </label>
                                    <input  type="text" class="form-control"  name="grand_total" id="grand_total" value = "<?php echo set_value('grand_total'); ?>" placeholder="0.00" readonly >
                                   
                                </div>
                            </div> 
                           
                        </div>
                    </div>
                </div>
            </div>
            <!-- row End  -->
            <div class="clonedInput panel panel-info " id="clonedInput1">
                <div class="panel-heading">
                    Step 3
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12    <?php if( form_error('order_id') ) { echo 'has-error';} ?>">
                                    <label for="order id">Invoice No.  <span id="error_order_id" style="color:red"></span></label>
                                    <input class="form-control" name= "order_id" value="<?php echo set_value('order_id'); ?>" placeholder ="Please Enter Order ID" id="order_id"  />
                                    
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group col-md-12  <?php if( form_error('serial_number') ) { echo 'has-error';} ?>">
                                    <label for="serial NUmber">Serial Number   <span id="error_serial_number" style="color:red"></span></label>
                                    <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php echo set_value('serial_number'); ?>" placeholder="Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" >
                                    
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                                    <label for="alternate_phone_number ">Alternate Mobile</label>
                                    <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if(isset($user[0]['alternate_phone_number'])){  echo $user[0]['alternate_phone_number']; } else { echo set_value('alternate_phone_number');} ?>" placeholder ="Please Enter Alternate Contact No" >
                                    <?php echo form_error('alternate_phone_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                <div class="form-group col-md-12" style="margin: 0px;padding: 0px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12  <?php if( form_error('booking_request_symptom') ) { echo 'has-error';} ?>">
                                <label for="booking_request_symptom">Symptom * <span id="error_booking_request_symptom" style="color: red;"></span></label>
                                <select class="form-control" name="booking_request_symptom" id="booking_request_symptom">
                                    <option disabled selected>Please Select Any Symptom</option>
                                </select>
                                <?php echo form_error('booking_request_symptom'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                            <div class="col-md-3">
                                 <div class="form-group col-md-12  ">
                                     <label for="landmark ">Remarks  <span id="error_remarks" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="remarks" name="booking_remarks"  placeholder="Enter Remarks" ></textarea>
                                   
                                </div>
                            </div>
                             <div class="col-md-6 ">
                                <div class="form-group col-md-12  <?php if( form_error('booking_address') ) { echo 'has-error';} ?>">
                                    <label for="landmark ">Booking Address *  <span id="error_address" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ></textarea>
                                    <?php echo form_error('booking_address'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="not_visible" name="not_visible" value="0"/>
             <input type="hidden" id="tmp_city" name="tmp_city" value=""/>
            <div class="row">
                <div class="form-group  col-md-12" >
                    <center>
                        <input type="submit" id="submitform" onclick="return check_validation()" class="btn btn-primary " value="Submit Booking">
                        <span id="error_not_visible" style="color: red"></span>
                    </center>
                </div>
            </div>
        </form>
        <!-- end Panel Body  -->
    </div>
</div>
<script type="text/javascript">
    function check_validation(){
      
        var booking_address = $('#booking_address').val();
        var mobile_number = $('#booking_primary_contact_no').val();
        var city = $('#booking_city').val();
        var pincode = $('#booking_pincode').val();
       
        var category = $('#appliance_category_1').val();
        var remarks = $('#remarks').val();
        var user_name = $("#name").val();
        
        var appliance = $("#service_name").val();
        var brand = $("#appliance_brand_1").val();
        var not_visible = $("#not_visible").val();
        var purchase_date = $("#purchase_date").val();
        
         if(mobile_number === "" || mobile_number.match(/^[6-9]{1}[0-9]{9}$/) === null){
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Mobile");
             return false;
        } else {
            display_message("booking_primary_contact_no","error_mobile_number","green","");
             
        }
       
        if(user_name === ""){
            display_message("name","error_name","red","Please Enter User Name");
             return false;
        } else {
            display_message("name","error_name","green","");
             
        }
       
        if(appliance === null){
            display_message("service_name","error_appliance","red","Please Select Appliance");
             return false;
        } else {
            display_message("service_name","error_appliance","green","");
        }
        if(pincode === ""){
              display_message("booking_pincode","error_pincode","red","Please Enter Pincode");
             return false;
        } else {
           display_message("booking_pincode","error_pincode","green","");
            
        }
        if(city === null){
            
             display_message("booking_city","error_city","red","Please Enter City");
             return false;
        } else {
             display_message("booking_city","error_city","green","");
            
        }
       
        if(brand === null){
            display_message("appliance_brand_1","error_brand","red","Please Select Brand");
             return false;
        } else {
            display_message("appliance_brand_1","error_brand","green","");
        }
        
        if(category === null){
            display_message("appliance_category_1","error_category","red","Please Select Category");
             return false;
        } else {
              display_message("appliance_category_1","error_category","green","");
        }
        if($('.appliance_capacity').length > 0) {
            var count1=0;
            $(".appliance_capacity").each(function(){
                var capacity_value = document.getElementById(this.id).innerHTML;
                if(($.trim(capacity_value) !== '<option selected="" disabled="">Select Capacity</option><option selected="" value=""></option>') && ($("#"+this.id).val() === null)) {
                    display_message("appliance_capacity_1","error_capacity","red","Please Select Capacity");
                    $("#"+this.id).focus();
                    ++count1;
                    return false;
                }
            });
            if(count1 > 0) {
                return false;
            }
            else {
                display_message("appliance_capacity_1","error_capacity","green","");
            }
        }
         if(purchase_date == ""){
            display_message("purchase_d","error_purchase","red","Please Select Purchase Date");
             return false;
        } else {
              display_message("purchase_d","error_purchase","green","");
        }
        
        
        if(not_visible === 0){
             display_message("not_visible","error_not_visible","red","Service Temporarily Un-available In This Pincode, Please Contact 247around Team.");
             return false;
        }
        
        service_category =0;
         $("input[type=checkbox]:checked").each(function(i) {
            service_category = 1;
            
        });
        if(service_category === 0){
             $("#selected_service").css("color","red");
             alert("Please Select Checkbox");
            return false;
        } else{
           
             $("#selected_service").css("color","black");
          
        }
        
        if(booking_address === ""){
             display_message("booking_address","error_address","red","Please Enter Booking Address");
             return false;
        } else {
          display_message("booking_address","error_address","green","");
        }
      
        if(remarks === ""){
             document.getElementById('remarks').style.borderColor = "red";
             document.getElementById('error_remarks').innerHTML = "Please Enter Problem Description";
             return false;
        } else {
            document.getElementById('remarks').style.borderColor = "green";
            document.getElementById('error_remarks').innerHTML = "";  
        }
       
        $('#submitform').val("Please wait.....");
        return send_post_request();
    }
    
    
    function display_message(input_id, error_id, color,message){
    
            document.getElementById(input_id).style.borderColor = color;
            document.getElementById(error_id).innerHTML = message;
    }
</script>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script type="text/javascript">
    
    $("#booking_city").select2({
         tags: true
    });
    $("#booking_request_symptom").select2();
    $("#model_number_1").select2();
    $("#price_tag").select2();
    $("#service_name").select2();
    $("#appliance_brand_1").select2();
    $("#appliance_capacity_1").select2();
    $("#appliance_category_1").select2();
    $("#partner_source").select2();
    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', 
        minDate: '<?php echo date('H') >= 12 ? date("Y-m-d", strtotime("+1 day")):date("Y-m-d", strtotime("+0 day")); ?>',
        maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>'});
    
    
    //This funciton is used to get Distinct Brands for selected service for Logged Partner
    function get_brands(){
        service_id =  $("#service_name").val();
      
        if(service_id !== null){
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                    $('#brand_loading').css("display", "inherit");
                },
                url: '<?php echo base_url(); ?>employee/dealers/get_brands',
                data: {service_id: service_id, brand:'<?php echo set_value('appliance_brand');?>'},
                success: function (response) {
                        var data = jQuery.parseJSON(response);
                        if(data.code === '0001'){
                            $("#appliance_brand_1 option[value !='option1']").remove();

                           $('#appliance_brand_1').html(data.brand).change();
                        }

                    },
                complete: function(){
                    $('#brand_loading').css("display", "none");
                }
            });
        }
    }
    
    //This function is used to get Category for partner id , service , brands specified
    
    function get_category(){
        service_id =  $("#service_name").val();
        brand =  $("#appliance_brand_1").val();
        partner_id =  $("#appliance_brand_1").find(':selected').attr('data-id');
        $("#partner_id").val(partner_id);
        $.ajax({
                        type: 'POST',
                        beforeSend: function(){
                            $('#category_loading').css("display", "inherit");
                        },
                        url: '<?php echo base_url(); ?>employee/dealers/get_category',
                        data: {service_id: service_id, brand: brand, category:'<?php echo set_value('appliance_category');?>', partner_id:partner_id},
                        success: function (response) {

                              var data = jQuery.parseJSON(response);
                                if(data.code === '0001'){
                                    //First Resetting Options values present if any
                                    $("#appliance_category_1 option[value !='option1']").remove();
                                    $('#appliance_category_1').html(data.category).change();
                                    get_capacity();
                                   
                                } else  if(data.code === '0003'){
                                    $('#category_loading').css("display", "inherit");
                                     $('#submitform').attr('disabled',true);
                                    alert(data.msg);
                                    return false;
                                } else {
                                    
                                }
                                
                                
                            },
                        complete: function(){
                            $('#category_loading').css("display", "none");
                        }            
                    });
        
    }
    
    //This function is used to get Capacity and Model
    function get_capacity(){
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        partner_id =  $("#appliance_brand_1").find(':selected').attr('data-id');
        $("#partner_id").val(partner_id);
        
        $.ajax({
            type: 'POST',
            beforeSend: function(){
                $('#capacity_loading').css("display", "inherit");
            },
            url: '<?php echo base_url(); ?>employee/dealers/get_capacity',
            data: {service_id: service_id, brand: brand,category:category, capacity:'<?php echo set_value('appliance_category');?>', partner_id:partner_id},
            
            success: function (response) {
                    var data = jQuery.parseJSON(response);
                    if(data.code === '0001'){
                        //First Resetting Options values present if any
                        $("#appliance_capacity_1 option[value !='option1']").remove();
                        $('#appliance_capacity_1').html(data.capacity).change();
                        if(($.trim(data.capacity) !== "") && ($.trim(data.capacity) !== "<option selected disabled>Select Capacity</option><option   selected  value = '' ></option>")) {
                            $("#appliance_capacity_1").attr("required",true);
                        }
                        else{
                            $("#appliance_capacity_1").removeAttr("required");
                        }
                    }
                    
                    get_models();
                    getPrice();


                },
            complete: function(){
                $('#capacity_loading').css("display", "none");
            }  
        });
    }
    
    //This function is used to get Model for corresponding previous data's
    function get_models(){
        service_id =  $("#service_name").val();
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        capacity = $("#appliance_capacity_1").val();
        partner_id =  $("#appliance_brand_1").find(':selected').attr('data-id');
        $("#partner_id").val(partner_id);
        if(capacity === null && capacity === ""){
            capacity = '';
            $("#appliance_capacity_1").removeAttr("required");
        }
        
        if(service_id !== null && brand !== 'option1' && brand !== ''){
         
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner',
                data: {service_id: service_id, brand: brand,category:category,capacity:capacity,partner_id:partner_id,partner_type:"OEM"},

                success: function (data) {
                  console.log(data);
                        if($.trim(data) === "Data Not Found"){

                            var input = '<input type="text" name="model_number" id="model_number_1" class="form-control" placeholder="Please Enter Model">';
                            $("#model_number_2").html(input).change();
                            $('.select-model').next(".select2-container").hide();
                        } else {
                            //First Resetting Options values present if any
                             var input_text = '<span id="model_number_2"><select class="form-control select-model"  name="model_number" id="model_number_1" ><option selected disabled>Select Model</option></select></span>';
                            $("#model_number_2").html(input_text).change();
                            $("#model_number_1").append(data).change();
                            $("#model_number_1").select2();
                            $('.select-model').next(".select2-container").show();
                           // getPrice();
                        }
                    }
            });
        }
    }
    
    function getPrice() {
        
        var postData = {};
        appliance_name = $("#service_name").find(':selected').attr('data-id');
        $("#appliance_name").val(appliance_name);
        appliance_name = $("#service_name").find(':selected').attr('data-id');
        $("#appliance_name").val(appliance_name);
        $("#priceList").html('<div class="text-center"><img src= "<?php echo base_url(); ?>images/loadring.gif" /></div>').delay(1200).queue(function () {
        partner_id =  $("#appliance_brand_1").find(':selected').attr('data-id');
        $("#partner_id").val(partner_id);
        postData['service_id'] = $("#service_name").val();
        postData['brand'] = $('#appliance_brand_1').val();
        postData['category'] = $("#appliance_category_1").val();
        postData['partner_id'] = partner_id;
        capacity = $("#appliance_capacity_1").val();
        if(capacity === null && capacity === ""){
            postData['capacity'] = "";
            $("#appliance_capacity_1").removeAttr("required");
            
        } else {
            postData['capacity'] = capacity;
        }
        postData['service_category'] = "";
        postData['pincode'] = $("#booking_pincode").val();
        postData['city'] = $("#booking_city").val();
        
        if(postData['brand'] !== null 
                && postData['category'] !== null && postData['pincode'].length === 6 && postData['city'] !== null){
          
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                  $('#submitform').attr('disabled',true);
                  
                },
                url: '<?php echo base_url(); ?>employee/dealers/get_service_tag',
                data: postData,
                success: function (data) {
                   
                     if(data === "ERROR"){
                        
                         //alert("Outstation Bookings Are Not Allowed, Please Contact 247around Team.");

                     } else { 
                          var data1 = jQuery.parseJSON(data);
                          $("#priceList").html("");
                          $("#priceList").html(data1.table);
                          $("#upcountry_data").val(data1.upcountry_data);
                          $('#submitform').attr('disabled',false);
                     }
                }
            });
        } else {
          //console.log("error");
        }
        
        $(this).dequeue();
    });

    
    }
    
   
    
    function get_city(){
        var pincode = $("#booking_pincode").val();
        var service_id =  $("#service_name").val();
        var city =  $("#city").val();
        if(pincode.length === 6 && service_id !== null){
         
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                    $('#city_loading').css("display", "-webkit-inline-box");
                    $('#submitform').prop('disabled', true);
                },
                url: '<?php echo base_url(); ?>employee/partner/get_district_by_pincode/'+ pincode+"/"+service_id, 
                data:{city:city},
                success: function (data) {
                  console.log(data);
                    if(data !== "ERROR"){
                        $('#booking_city').select2().html(data).change();
                        $('#submitform').prop('disabled', false);
                        $("#not_visible").val('1');
                    } else {
                        alert("Service Temporarily Un-available In This Pincode, Please Contact 247around Team.");
                        $('#submitform').prop('disabled', true);
                        $("#not_visible").val('0');
                        
                    }
                   
                    
                },
                complete: function(){
                    $('#city_loading').css("display", "none");
                }  
            }); 
        }
    }
    

    $(document).ready(function(){
         $("#booking_pincode").keyup(function(event) {
            get_city();
        
        });
        $("#booking_primary_contact_no").keyup(function(event) {
            var phone_number = $("#booking_primary_contact_no").val();

            if(phone_number.length === 10){
                $.ajax({
                    type:"POST",
                    url: "<?php echo base_url();?>employee/dealers/get_users_details",
                    data:{phone_number: phone_number},
                    beforeSend: function(){
                        $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                      });
                     // $('body').loadingModal('hide');


                    },
                    success: function(response){
                        var data = jQuery.parseJSON(response);
                       console.log(data);
                        if(data.code === '0001'){
                            $("#tmp").val(data.user_data['0']['city']);
                            $("#name").val(data.user_data['0']['name']);
                            $("#booking_alternate_contact_no").val(data.user_data['0']['alternate_phone_number']);

                            $("#booking_address").val(data.user_data['0']['home_address']);
                            $("#user_id").val(data.user_data['0']['user_id']);
                            $("#booking_pincode").val(data.user_data['0']['pincode']);

                        } 
                        $("#service_name option[value !='option1']").remove();
                        $("#service_name").append(data.appliance_data).change();


                        $('body').loadingModal('destroy');

                    }

                });
            }

        });
    });
    
    function set_upcountry(){
    var upcountry_data = $("#upcountry_data").val();
    
    is_upcountry = 0;
    count = 0;
    non_upcountry = 0;
    $("input[type=checkbox]:checked").each(function (i) {
        count = count + 1;

        var id = this.id.split('checkbox_');

        var up_val = $("#is_up_val_" + id[1]).val();
        n = 0;

        if (Number(up_val) === 1) {
            is_upcountry = 1;
        } else  if (Number(up_val) === -1) {
            non_upcountry = -1;
        } else {
            n =0;
        }
    });
    if (count > 0) {
        var data1 = jQuery.parseJSON(upcountry_data);
        switch(data1.message){
             case 'UPCOUNTRY BOOKING':
            case 'UPCOUNTRY LIMIT EXCEED':
                if(Number(is_upcountry) == 1){
                    var partner_approval = Number(data1.partner_upcountry_approval);

                    if (data1.message === "UPCOUNTRY BOOKING") {
                        $("#upcountry_charges").text("0.00");
                        $("#checkbox_upcountry").val("upcountry_0_0");
                        document.getElementById("checkbox_upcountry").checked = false;
                        final_price();
                        $('#submitform').attr('disabled', false); 

                    } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 0) {
                        $('#submitform').attr('disabled', true);
                         document.getElementById("checkbox_upcountry").checked = false;
                         $("#upcountry_charges").text("0.00");
                         $("#checkbox_upcountry").val("upcountry_0_0"); 
                         document.getElementById("checkbox_upcountry").checked = false;
                         final_price();

                        alert("Out-Station Booking Not Allowed, Please Contact 247around.");

                    } else if (data1.message === "UPCOUNTRY LIMIT EXCEED" && partner_approval === 1) {
                        alert("This Is Out-Station Booking, Please Wait For Brand Approval.");
                         $("#upcountry_charges").text("0.00");
                         $("#checkbox_upcountry").val("upcountry_0_0"); 
                         document.getElementById("checkbox_upcountry").checked = false;
                         final_price();
                         $('#submitform').attr('disabled', false);
                    } else {
                        $("#upcountry_charges").text("0.00");
                        $("#checkbox_upcountry").val("upcountry_0_0");
                         document.getElementById("checkbox_upcountry").checked = false;
                        $('#submitform').attr('disabled', false); 
                    }

                } else {
                     if(Number(is_upcountry) == 0 && Number(non_upcountry) == 0){
                          var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
               
                        $("#upcountry_charges").text(upcountry_charges);
                        $("#checkbox_upcountry").val("upcountry_" + upcountry_charges + "_0");
                        document.getElementById("checkbox_upcountry").checked = true;
                
                        final_price();
                     } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 0){
                         document.getElementById("checkbox_upcountry").checked = false;
                        $("#upcountry_charges").text("0.00");
                        $("#checkbox_upcountry").val("upcountry_0_0");
                     } else if(Number(is_upcountry) == 0 && Number(non_upcountry) == -1 && n == 1){
                        var upcountry_charges = (Number(3) * Number(data1.upcountry_distance)).toFixed(2);
               
                        $("#upcountry_charges").text(upcountry_charges);
                        $("#checkbox_upcountry").val("upcountry_" + upcountry_charges + "_0");
                        document.getElementById("checkbox_upcountry").checked = true;

                        final_price();
                         
                     }
                     $('#submitform').attr('disabled', false);
                }
                break;
                default:
                    $("#upcountry_charges").text("0.00");
                    $("#checkbox_upcountry").val("upcountry_0_0");
                    document.getElementById("checkbox_upcountry").checked = false;
                    final_price();
                    $('#submitform').attr('disabled', false);
                    break;
                        
        }

    } else {
        
        $("#upcountry_charges").text("0.00");
        $("#checkbox_upcountry").val("upcountry_0");
        final_price();
        $('#submitform').attr('disabled', true);
    }
    
    var not_visible = $("#not_visible").val();
    
    if(Number(not_visible) === 0){
      
     alert('Service Temporarily Un-available In This Pincode, Please Contact 247around Team');
     display_message("not_visible","error_not_visible","red","Service Temporarily Un-available In This Pincode, Please Contact 247around Team.");
      $('#submitform').attr('disabled', true);
         return false;
    } else {
      display_message("not_visible","error_not_visible","","");
      $('#submitform').attr('disabled', false);
         
   }        
    
    
   
   }        
    function final_price(){
        var price = 0;
        var price_array ;
        ch =0;

         $("input[type=checkbox]:checked").each(function(i) {
            price_array = $(this).val().split('_');
            //console.log(price_array);
            price += (Number(price_array[1]) -Number(price_array[2]) );
            if(price_array[0] !== "upcountry"){
                ch = 1;
            }
           
        });
        if(ch === 0){
            document.getElementById("checkbox_upcountry").checked = false;
            $("#grand_total").val("0.00");
            
        } else {
            var final_price = Number(price);
            $("#grand_total").val(final_price.toFixed(2));
        }
        
  }
    
    function send_post_request(){
       
        var fd = new FormData(document.getElementById("booking_form"));
        fd.append("label", "WEBUPLOAD");
          $.ajax({
                type:"POST",
                url: "<?php echo base_url()?>dealers/process_addbooking",
                data:fd,
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
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
                success: function(response){
                    
                    var data1 = jQuery.parseJSON(response);
                    $('body').loadingModal('destroy');
                    alert(data1.msg);
                    
                    window.open("<?php echo base_url(); ?>dealers/add_booking","_self");
                }
            });
            
            return false;
    }
  $("#purchase_date").datepicker({dateFormat: 'yy-mm-dd'});
  
  $('.purchase_date').each(function () {
    if ($(this).hasClass('hasDatepicker')) {
        $(this).removeClass('hasDatepicker');
    } 
    $(this).datepicker({dateFormat: 'yy-mm-dd', maxDate: 0});
  });
  
  function get_symptom(symptom_id = ""){
        var array = [];
        var postData = {};
        $(".price_checkbox:checked").each(function (i) {
            var price_tags = $("#"+ $(this).attr('id')).attr('data-price_tag');
            array.push(price_tags);

        });
        if(array.length > 0){
            postData['partner_id'] = $("#appliance_brand_1 option:selected").attr('data-id');
            postData['request_type'] = array;
            postData['service_id'] = $("#service_name").val();
            postData['booking_request_symptom'] = symptom_id;
            var url = '<?php echo base_url();?>employee/booking_request/get_booking_request_dropdown';
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
</script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>