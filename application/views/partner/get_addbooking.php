
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/partner/process_addbooking"  method="POST" enctype="multipart/form-data">
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
                           
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12   <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no">Mobile *</label>
                                    <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if(isset($user[0]['phone_number'])){ echo $user[0]['phone_number']; } else if($phone_number !="process_addbooking"){ echo  $phone_number; }  ?>" required>
                                    <span id="error_mobile_number" style="color:red"></span>
                                </div>
                            </div>
                            <input type="hidden" name="partner_source" value="CallCenter" id="partner_source"/>
                            <div class="col-md-4" >
                                <div class="form-group col-md-12 <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no">Name *</label>
                                    <input type="hidden" name="vendor_id" id="service_center_id" value="" />
                                    <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                                    <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($user[0]['name'])){ echo $user[0]['name']; } else { echo set_value('user_name'); }  ?>" <?php if(isset($user[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                                    <?php echo form_error('user_name'); ?>
                                </div>
                            </div>
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
                                        <?php if(isset($user[0]['city'])){ ?>
                                           
                                        <option selected><?php echo $user[0]['city']; ?></option>
                                        <?php  }
                                            ?>
                                    </select>
                                    <?php echo form_error('city'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                    <label for="Appliance">Appliance * <span id="error_appliance" style="color: red;"></span></label>
                                    <select type="text" class="form-control"  id="service_name" name="service_id"   required onchange="return get_brands(), get_category(), get_capacity(), getservice_category()">
                                        <option selected disabled>Select Appliance</option>
                                        <?php foreach ($appliances as $values) { ?>
                                        <option <?php if(count($appliances) ==1){echo "selected";} ?> data-id="<?php echo $values->id;?>" value=<?= $values->id; ?>>
                                            <?php echo $values->services; }    ?>
                                        </option>
                                    </select>
                                    <?php echo form_error('service_id'); ?>
                                    <span id="error_pincode" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                                    <label for="appliance_brand">Brand *  <span id="error_brand" style="color: red;"></label>
                                    <p style="color:grey;display:none" id="brand_loading">Loading ...</p>
                                    <select type="text" class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required onchange="return get_category(), getservice_category()">
                                        <option selected disabled value="option1">Select Brand</option>
                                    </select>
                                    <?php echo form_error('appliance_brand'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                                    <label for="appliance_category">Category * <span id="error_category" style="color: red;"></label>
                                    <p style="color:grey;display:none" id="category_loading">Loading ...</p>
                                    <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity(), getservice_category()">
                                        <option selected disabled value="option1">Select Appliance Category</option>
                                    </select>
                                    <?php echo form_error('appliance_category'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                                    <label for="appliance_capacity">Capacity  <span id="error_capacity" style="color: red;"></label>
                                    <p style="color:grey;display:none" id="capacity_loading">Loading ...</p>
                                    <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" onchange="return get_models(), getservice_category()">
                                        <option selected disabled value="option1">Select Appliance Capacity</option>
                                    </select>
                                    <?php echo form_error('appliance_capacity'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="Model Number">Model Number  <span id="error_model" style="color: red;"></label>
                                    <span id="model_number_2">
                                    <select class="form-control"  name="model_number" id="model_number_1" >
                                        <option selected disabled>Select Model</option>
                                    </select>
                                    </span>
                                    <?php echo form_error('model_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-12">
                                <div class="form-group col-md-12 <?php if( form_error('price_tag') ) { echo 'has-error';} ?>">
                                    <label for="price tag">Call Type *  <span id="error_call_type" style="color: red;"></label>
                                     
                                    <select type="text" class="form-control price_tags" onchange="getPrice()"  id="price_tag" name="price_tag" required>
                                        <option selected disabled>Select Call Type</option>
<!--                                        <option <?php //if(set_value('price_tag') == "Installation & Demo"){ echo "selected";} ?>>Installation & Demo</option>
                                        <option <?php //if(set_value('price_tag') == "Repair - In Warranty"){ echo "selected";} ?>>Repair - In Warranty</option>
                                        <option <?php// if(set_value('price_tag') == "Repair - Out Of Warranty"){ echo "selected";} ?>>Repair - Out Of Warranty</option>-->
                                    </select>
                                    <?php echo form_error('price_tag'); ?>
                                </div>
                            </div>
                             <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                    <label for="Booking Date ">Booking Date *</label>
                                    <input type="date" min="<?php echo date("Y-m-d"); ?>" class="form-control"  id="booking_date" name="booking_date"   value = "<?php echo  date("Y-m-d"); ?>"  >
                                    <!--   -->
                                    <?php echo form_error('booking_date'); ?>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top:15px;">
                                <span style="font-size:20px;"><b>Customer Payable: </b><span style="margin-top:15px;"> <b style="font-size:20px;" id="total_price"><br/>Rs.</b></span></span>
                            </div>
                            <!-- end col-md-6 -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- row End  -->
            <div class="clonedInput panel panel-info " id="clonedInput1">
                <div class="panel-heading">
                    Step 2
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12    <?php if( form_error('order_id') ) { echo 'has-error';} ?>">
                                    <label for="order id">Order ID *</label>
                                    <input class="form-control" name= "order_id" value="<?php echo set_value('order_id'); ?>" placeholder ="Please Enter Order ID" id="order_id"  />
                                    <p><span id="error_order_id" style="color:red"></span></p>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('serial_number') ) { echo 'has-error';} ?>">
                                    <label for="serial NUmber">Serial Number *</label>
                                    <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php echo set_value('serial_number'); ?>" placeholder="Enter Serial Number" >
                                    <span id="error_serial_number" style="color:red"></span>
                                </div>
                            </div> 
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('purchase_month') ) { echo 'has-error';} ?>">
                                    <label for="Date of Purchase">Date of Purchase</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select  type="text" class=" form-control "   name="purchase_month" id="purchase_month_1" >
                                                <option selected="selected" value="">Month</option>
                                                <option <?php if(set_value('purchase_month') == "Jan"){ echo "selected";} else if(date("M") == "Jan"){ echo "selected";} ?> >Jan</option>
                                                <option <?php if(set_value('purchase_month') == "Feb"){ echo "selected";} else if(date("M") == "Feb"){ echo "selected";} ?>>Feb</option>
                                                <option <?php if(set_value('purchase_month') == "Mar"){ echo "selected";} else if(date("M") == "Mar"){ echo "selected";} ?>>Mar</option>
                                                <option <?php if(set_value('purchase_month') == "Apr"){ echo "selected";} else if(date("M") == "Apr"){ echo "selected";} ?>>Apr</option>
                                                <option <?php if(set_value('purchase_month') == "May"){ echo "selected";} else if(date("M") == "May"){ echo "selected";} ?>>May</option>
                                                <option <?php if(set_value('purchase_month') == "Jun"){ echo "selected";} else if(date("M") == "Jun"){ echo "selected";} ?>>Jun</option>
                                                <option <?php if(set_value('purchase_month') == "July"){ echo "selected";} else if(date("M") == "July"){ echo "selected";} ?> >July</option>
                                                <option <?php if(set_value('purchase_month') == "Aug"){ echo "selected";} else if(date("M") == "Aug"){ echo "selected";} ?>>Aug</option>
                                                <option <?php if(set_value('purchase_month') == "Sept"){ echo "selected";} else if(date("M") == "Sept"){ echo "selected";} ?>>Sept</option>
                                                <option <?php if(set_value('purchase_month') == "Oct"){ echo "selected";} else if(date("M") == "Oct"){ echo "selected";} ?>>Oct</option>
                                                <option <?php if(set_value('purchase_month') == "Nov"){ echo "selected";} else if(date("M") == "Nov"){ echo "selected";} ?>>Nov</option>
                                                <option <?php if(set_value('purchase_month') == "Dec"){ echo "selected";} else if(date("M") == "Dec"){ echo "selected";}?>>Dec</option>
                                            </select>
                                            <p><?php echo form_error('purchase_month'); ?></p>
                                        </div> 
                                        <div class="col-md-6">
                                            <select  type="text" class="form-control "   name="purchase_year" id="purchase_year_1" >
                                                <option selected="selected" value="" >Year</option>
                                                <?php for($i = 0; $i> -26; $i--){ ?>
                                                <option  <?php if(set_value('purchase_year') == date("Y",strtotime($i." year"))){ echo "selected";} 
                                                  ?> >
                                                    <?php echo date("Y",strtotime($i." year")); ?>
                                                </option>
                                                <?php }  ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                 <div class="form-group col-md-12  <?php if( form_error('query_remarks') ) { echo 'has-error';} ?>">
                                     <label for="landmark ">Remarks  <span id="error_remarks" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ><?php echo set_value('query_remarks'); ?></textarea>
                                    <?php echo form_error('query_remarks'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Step 3</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                           
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                                    <label for="alternate_phone_number ">Alternate Mobile</label>
                                    <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if(isset($user[0]['alternate_phone_number'])){  echo $user[0]['alternate_phone_number']; } else { echo set_value('alternate_phone_number');} ?>" placeholder ="Please Enter Alternate Contact No" >
                                    <?php echo form_error('alternate_phone_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
                                    <label for="user_email ">Email </label>
                                    <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(isset($user[0]['user_email'])){  echo $user[0]['user_email'];  }  ?>" placeholder="Please Enter User Email">
                                    <?php echo form_error('user_email'); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12  <?php if( form_error('landmark') ) { echo 'has-error';} ?>">
                                    <label for="landmark ">Landmark </label>
                                    <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(isset($user[0]['landmark'])){echo $user[0]['landmark'];} else { echo set_value('landmark');} ?>" placeholder="Enter Any Landmark">
                                    <?php echo form_error('landmark'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group col-md-12  <?php if( form_error('booking_address') ) { echo 'has-error';} ?>">
                                    <label for="landmark ">Booking Address *  <span id="error_address" style="color: red;"></label>
                                    <textarea class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ><?php if(isset($user[0]['home_address'])){  echo $user[0]['home_address']; } else { echo set_value('booking_address'); } ?></textarea>
                                    <?php echo form_error('booking_address'); ?>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group  col-md-12" >
                    <center>
                        <input type="submit" id="submitform" class="btn btn-primary " onclick="return check_validation()" value="Submit Booking">
                    </center>
                </div>
            </div>
        </form>
        <!-- end Panel Body  -->
    </div>
</div>
<script type="text/javascript">
    function check_validation(){
        var order_id =  $('#order_id').val();
        var booking_address = $('#booking_address').val();
        var mobile_number = $('#booking_primary_contact_no').val();
        var city = $('#booking_city').val();
        var pincode = $('#booking_pincode').val();
        var serial_number = $('#serial_number').val();
        var category = $('#appliance_category_1').val();
        var remarks = $('#remarks').val();
        var call_type  =  $('#price_tag').val();
        var appliance = $("#service_name").val();
        var brand = $("#appliance_brand_1").val();
        
         if(mobile_number === ""){
            display_message("booking_primary_contact_no","error_mobile_number","red","Please Enter Mobile");
             return false;
        } else {
            display_message("booking_primary_contact_no","error_mobile_number","green","");
             
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
        if(appliance === null){
            display_message("service_name","error_appliance","red","Please Select Appliance");
             return false;
        } else {
            display_message("service_name","error_appliance","green","");
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

        if(call_type === null){
            display_message("price_tag","error_call_type","red","Please Select Call Type");
             return false;
        } else {
           display_message("price_tag","error_call_type","green","");
        }
        
         if (order_id === "" && serial_number === ""  ) {
             document.getElementById('order_id').style.borderColor = "red";
             document.getElementById('serial_number').style.borderColor = "red";
            document.getElementById('error_order_id').innerHTML = "Please enter Order ID";
            document.getElementById('error_serial_number').innerHTML = "Please enter Serial Number";
               
            return false;
        } else {
    
            document.getElementById('order_id').style.borderColor = "green";
            document.getElementById('serial_number').style.borderColor = "green";
            document.getElementById('error_order_id').innerHTML = "";
            document.getElementById('error_serial_number').innerHTML = "";
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
       
    }
    
    
    function display_message(input_id, error_id, color,message){
    
            document.getElementById(input_id).style.borderColor = color;
            document.getElementById(error_id).innerHTML = message;
    }
</script>
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
</style>
<?php $this->session->unset_userdata('success'); ?>
<script type="text/javascript">
    $("#booking_city").select2({
         tags: true
    });
    $("#price_tag").select2();
    $("#service_name").select2();
    $("#appliance_brand_1").select2();
    $("#appliance_capacity_1").select2();
    $("#appliance_category_1").select2();
    
    get_brands();
    
    //This funciton is used to get Distinct Brands for selected service for Logged Partner
    function get_brands(){
        service_id =  $("#service_name").find(':selected').attr('data-id');
        $("#total_price").html("<br/>Rs.");
        
         $.ajax({
                        type: 'POST',
                        beforeSend: function(){
                            $('#brand_loading').css("display", "block");
                        },
                        url: '<?php echo base_url(); ?>employee/partner/get_brands_from_service',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>},
                        success: function (data) {
                               
                                //First Resetting Options values present if any
                                $("#appliance_brand_1 option[value !='option1']").remove();
                                $('#appliance_brand_1').append(data);
                            },
                        complete: function(){
                            $('#brand_loading').css("display", "none");
                        }
                    });
    }
    
    //This function is used to get Category for partner id , service , brands specified
    
    function get_category(brand){
        service_id =  $("#service_name").find(':selected').attr('data-id');
        brand =  $("#appliance_brand_1").val();
        $("#total_price").html("<br/>Rs.");
        $.ajax({
                        type: 'POST',
                        beforeSend: function(){
                            $('#category_loading').css("display", "block");
                        },
                        url: '<?php echo base_url(); ?>employee/partner/get_category_from_service',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, brand: brand},
                        success: function (data) {
                               
                                //First Resetting Options values present if any
                                $("#appliance_category_1 option[value !='option1']").remove();
                                $('#appliance_category_1').append(data).change();
                                get_capacity();
                            },
                        complete: function(){
                            $('#category_loading').css("display", "none");
                        }            
                    });
        
    }
    
    //This function is used to get Capacity and Model
    function get_capacity(){
        service_id =  $("#service_name").find(':selected').attr('data-id');
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        $("#total_price").html("<br/>Rs.");
        $.ajax({
            type: 'POST',
            beforeSend: function(){
                $('#capacity_loading').css("display", "block");
            },
            url: '<?php echo base_url(); ?>employee/partner/get_capacity_for_partner',
            data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, brand: brand,category:category},
            dataType:"json",
            success: function (data) {


                    //First Resetting Options values present if any
                    $("#appliance_capacity_1 option[value !='option1']").remove();
                    $('#appliance_capacity_1').append(data['capacity']);
                    get_models();
                },
            complete: function(){
                $('#capacity_loading').css("display", "none");
            }  
        });
    }
    
    //This function is used to get Model for corresponding previous data's
    function get_models(){
        service_id =  $("#service_name").find(':selected').attr('data-id');
        brand = $("#appliance_brand_1").find(':selected').val();
        category = $("#appliance_category_1").find(':selected').val();
        capacity = $("#appliance_capacity_1").val();
        if(capacity === null && capacity === ""){
            capacity = '';
        }
        $("#total_price").html("<br/>Rs.");
        $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner',
                        data: {service_id: service_id,partner_id:<?php echo $this->session->userdata('partner_id')?>, brand: brand,category:category,capacity:capacity},
                       
                        success: function (data) {
                         
                                if(data === "Data Not Found"){
                                  
                                    var input = '<input type="text" name="model_number" id="model_number_1" class="form-control" placeholder="Please Enter Model">';
                                    $("#model_number_2").html(input).change();
                                } else {
                                    //First Resetting Options values present if any
                                    $("#model_number_1").html(data).change();
                                    getPrice();
                                }
                            }
                    });
    }
    
    function getPrice() {
    
        var postData = {};       
       
        postData['service_id'] = $("#service_name").find(':selected').attr('data-id');
        postData['brand'] = $('#appliance_brand_1').val();
        postData['category'] = $("#appliance_category_1").val();
        capacity = $("#appliance_capacity_1").val();
        if(capacity === null && capacity === ""){
            postData['capacity'] = "";
            
        } else {
            postData['capacity'] = capacity;
        }
        postData['service_category'] = $("#price_tag").val();
        postData['pincode'] = $("#booking_pincode").val();
        postData['city'] = $("#booking_city").val();
        $("#total_price").html("<br/>Rs.");
        if( postData['service_category'] !== null && postData['brand'] !== null 
                && postData['category'] !== null && postData['pincode'].length === 6 && postData['city'] !== null){
           
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  $("#total_price").html("Loading......");
                  $('#submitform').attr('disabled',true);
                  
                },
                url: '<?php echo base_url(); ?>employee/partner/get_price_for_partner',
                data: postData,
                success: function (data) {
                    
                     if(data === "ERROR"){
                         // $("#total_price").text("Price is not defined" );
                          alert("This is out station booking, Do not allow to add this booking");

                     } else {
                         
                          var data1 = jQuery.parseJSON(data);
                         
                          $("#total_price").html(data1.price);
                          $("#service_center_id").val(data1.vendor_id);
                          $("#upcountry_data").val(data1.upcountry_data);
                          $('#submitform').attr('disabled',false);
                     }
                }
            });
        } else {
       // $("#total_price").html("Please Enter Above Field");
         //  return false;
        }
    
    }
    
    function getservice_category() {
    
        var postData = {};       
       
        postData['service_id'] = $("#service_name").find(':selected').attr('data-id');
        postData['brand'] = $('#appliance_brand_1').val();
        postData['category'] = $("#appliance_category_1").val();
        capacity = $("#appliance_capacity_1").val();
        $("#total_price").html("<br/>Rs.");
        if(capacity === null && capacity === ""){
            postData['capacity'] = "";
            
        } else {
            postData['capacity'] = capacity;
        }

        $.ajax({
            type: 'POST',
            beforeSend: function(){
              $("#error_call_type").html("Loading......");
              $('#submitform').attr('disabled',true);

            },
            url: '<?php echo base_url(); ?>employee/partner/get_service_category',
            data: postData,
            success: function (data) {
console.log(data);
                 if(data === "ERROR"){
                     
                   // alert("Price is not defined");

                 } else {
 
                    $("#price_tag option[value !='option1']").remove();
                    $('#price_tag').append(data).change();
                    getPrice();
                    $('#submitform').attr('disabled',false);
                    $("#error_call_type").css("display","none");
                 }
            }
        });
        
    
    }
    
    $("#booking_pincode").keyup(function(event) {
        var pincode = $("#booking_pincode").val();
        if(pincode.length === 6){
            
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                    $('#city_loading').css("display", "-webkit-inline-box");
                },
                url: '<?php echo base_url(); ?>employee/partner/get_district_by_pincode/'+ pincode,          
                success: function (data) {
                 
                    $('#booking_city').select2().html(data).change();
                    
                },
                complete: function(){
                    $('#city_loading').css("display", "none");
                }  
            }); 
        }
        
    });
    
</script>