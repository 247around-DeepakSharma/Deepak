<head>
    <script src="<?php echo base_url(); ?>js/validation_js.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>247around</title>
    <!-- Bootstrap -->
    <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- bootstrap-progressbar -->
    <link href="<?php echo base_url() ?>css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="<?php echo base_url() ?>css/daterangepicker.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="<?php echo base_url() ?>css/dashboard_custom.min.css" rel="stylesheet">
    <!-- Sweet Alert Css -->
    <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="<?php echo base_url() ?>css/select2.min.css" rel="stylesheet" />
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assest/DataTables/datatables.min.css"/>
    <!-- jQuery -->
    <script src="<?php echo base_url() ?>js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
    <!-- moment.js -->
    <script src="<?php echo base_url() ?>js/moment.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="<?php echo base_url() ?>js/daterangepicker.js"></script>
    <!-- DateJS -->
    <script src="<?php echo base_url() ?>assest/DateJS/build/date.js"></script>
    <!-- Select2 JS -->
    <script src="<?php echo base_url(); ?>js/select2.min.js"></script>
    <!-- sweet Alert JS -->
    <script src="<?php echo base_url(); ?>js/sweetalert.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assest/DataTables/datatables.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
    <script src="<?php echo base_url() ?>js/partner.js"></script>
    <script src="<?php echo base_url() ?>js/add_booking_validation.js"></script>
    <style>
        .right_col{
            min-height:700px!important;
        }
        .profile_pic {
            width: 100%;
            float: left;
        }
        .img-circle {
            border-radius: 0%;
        }
        .profile_info{
            width: 100%;
        }
        .img-circle.profile_img {
            background: #fff;
            z-index: 1000;
            position: inherit;
            margin: 0px;
            border: 1px solid rgba(52,73,94,.44);
            padding: 4px;
        }
        .select2-container--default .select2-selection--single {
            border-radius: 0px;
            border: 1px solid #ccc;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            padding-top: 0px;
        }
        .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single{
            min-height: 34px;
        }
        .profile_details .profile_view{
            padding: 0px;
        }
        .nav-sm .main_container .top_nav{
            margin-left: 0px;
        }
        .nav-sm .container.body .col-md-3.left_col{
            margin-top: 60px;
            width: 40px;
        }
        .nav-md .container.body .col-md-3.left_col{
            margin-top: 60px;
            width: 206px;
        }
        .nav-md .container.body .right_col{
            margin-left: 206px;;
        }
        .nav-sm .container.body .right_col{
            margin-left: 40px;
        }
        .navbar-brand, .navbar-nav > li > a{
            line-height: 24px;
        }
        .right_col{
            min-height: 600px!important;
            margin-top: 50px!important;
        }
        .navbar{
            height: 45px;
        }
        .navbar-header{
            background: transparent;
            border: 1px solid #eee;
        }
        .navbar-brand {
            padding: 0px;
            height: 45px;
        }
        .navbar-brand>img {
            height: 100%;
            padding: 15px;
            width: auto;
        }
        .nav-sm .sidebar-footer{
            width: 40px;
        }
        .nav-sm .sidebar-footer a{
            width: 100%;
        }
        .nav-sm footer ,.nav-md footer{
            margin-left: 0px;
        }
        footer{
            padding: 20px;
        }
        .nav.side-menu > li:hover{
            background: #1ABB9C;
        }
        .side_menu_list_title{
            display: none;
        }
        .nav-md .sidebar-footer{
            width: 206px;
        }
        .sidebar-footer a{
            width: 100%;
        }
        .custom_pagination{
            float: right;
            background: #ddd;
            margin: 2px 0;
            padding: 4px;
            border-radius: 4px;
        }
        .custom_pagination a{
            border: 1px solid #ddd;
            padding: 6px 10px;
            line-height: 24px;
        }
        .custom_pagination > strong {
            color:#fff;
        }
        .nav-sm .nav.side-menu li a{
            font-size: 12px;
        }
        .navbar-brand > img{
            padding: 0px 2px;
        }
        .form-control{
            font-size: 13px;
        }
        ul.bar_tabs{
            background: transparent;
        }
        @media (max-width: 768px) {
            .navbar-fixed-top {
                position: static;
                bottom: 0;
            }
            .top_nav .navbar-right{
                width: 94%;
            }
        }
        @media (max-width: 480px) {
            .navbar-header{
                display: none;
            }
        }
        .col-md-3{
            width: 25%;
        }
        #dealer_list{
            float:left;
            width:92%;
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
        
        body {
            background : #f1fbfa;
            color : #2c9d8c;
        }
    </style>
</head>
<body class="nav-sm">
    <div class="container body">
        <div class="main_container">
            <div style="padding:20px;" role="main">  
                <?php
                    if ($this->session->userdata('success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top: 55px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>' . $this->session->userdata('success') . '</strong>
                            </div>';
                    }
                    if ($this->session->userdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top: 55px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('error') . '</strong>
                                </div>';
                    }
                ?>
                <form name="myForm" class="form-horizontal" onSubmit="document.getElementById('submitform').disabled = true;" id ="booking_form" action="<?php echo base_url() ?>employee/partner/process_addbooking_walkin"  method="POST" enctype="multipart/form-data">
                    <div class="row">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Appliance Details</h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">                                    
                                    <div class="col-md-4 col-sm-12 form-group">
                                        <input type="hidden" name="appliance_name" id="appliance_name" value=""/> 
                                        <input type="hidden" name="partner_id" id="partner_id" value="" />
                                        <label for="appliance_brand_1">Brand * </label>
                                        <select class="form-control appliance_brand" name="appliance_brand" id="appliance_brand_1" required onchange="return get_appliance(), get_partner()">
                                            <option selected disabled value="option1">Select Brand</option>
                                            <?php foreach ($brands as $values) { ?>
                                                <option <?php if (count($brands) == 1) {  echo "selected"; } ?> data-id="<?php $values ?>" value=<?= $values; ?>>
                                                <?php echo $values;  ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span id="error_brand" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-sm-12">
                                        <label for="service_name">Appliance * <span style="color:grey;display:none" id="appliance_loading">Loading ...</span></label>
                                        <input type="hidden" class="form-control" id="booking_appliance" name="booking_appliance">
                                        <input type="hidden" id="service_id" name="service_id">
                                        <select class="form-control"  id="service_name" name="service_id" required onchange="return get_city(), get_category(), get_capacity()">
                                            <option selected disabled value="option1">Select Appliance</option>
                                        </select>                                        
                                        <span id="error_appliance" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-sm-12">                                        
                                        <input type="hidden" class="form-control" readonly="" id="booking_date" name="booking_date"  value = "<?php echo date("d-m-Y"); ?>">                                                                                                                      
                                        <label for="partner_source">Seller Channel* </label>
                                        <select class="form-control" id="partner_source" name="partner_source" required>
                                            <option value="" selected disabled>Please select seller channel</option>
                                            <?php
                                            if (isset($channel)) {
                                                foreach ($channel as $key => $value) {
                                                    echo "<option>".$value['channel_name']."</option>";
                                                }
                                            }
                                            ?>
                                        </select>                                   
                                        <span id="error_seller" style="color: red;"></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="appliance_category_1">Category *<span style="color:grey;display:none" id="category_loading">Loading ...</span></label>
                                        <select class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required onchange="return get_capacity()">
                                            <option selected disabled value="option1">Select Appliance Category</option>
                                        </select>
                                        <span id="error_category" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="appliance_capacity_1">Capacity *<span style="color:grey;display:none" id="capacity_loading">Loading ...</span></label>
                                        <select class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" onchange="return get_models()">
                                            <option selected disabled value="option1">Select Appliance Capacity</option>
                                        </select>
                                        <span id="error_capacity" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <input type="hidden" class="form-control" id="appliance_unit" name="appliance_unit" value = "1">
                                        <label for="model_number_1">Model Number</label>
                                        <span id="model_number_2">
                                            <select class="form-control select-model"  name="model_number" id="model_number_1" >
                                                <option selected disabled>Select Model</option>
                                            </select>
                                            <span id="error_model" style="color: red;"></span>
                                       </span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="order_id">Reference / Invoice / Order Number </label>
                                        <input class="form-control" name= "order_id" value="" placeholder ="Please Enter Reference / Invoice / Order Number" id="order_id" />
                                        <span id="error_order_id" style="color:red"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="serial_number">Serial Number </label>
                                        <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "" placeholder="Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" >
                                        <span id="error_serial_number" style="color:red"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="purchase_date">Purchase Date * </label>
                                        <input style="background-color:#FFF;"  readonly="" placeholder="Please Choose Purchase Date" type="text" class="form-control"  id="purchase_date" name="purchase_date"  value = "" max="<?= date('d-m-Y'); ?>" autocomplete='off' onkeydown="return false" >
                                        <span id="error_purchase_date" style="color: red;"></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12 form-group">
                                        <label for="remarks">Remarks* </label>
                                        <textarea class="form-control" rows="2" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ></textarea>
                                        <span id="error_remarks" style="color: red;"></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="dealer_phone_number">Dealer Phone Number  <span id="error_dealer_phone_number" style="color:red"></span></label>
                                        <input  type="text" class="form-control"  name="dealer_phone_number" id="dealer_phone_number" value = "" placeholder="Enter Dealer Phone Number" autocomplete="off">
                                        <div id="dealer_phone_suggesstion_box"></div>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="dealer_name">Dealer Name *  <span id="error_dealer_name" style="color:red"></span></label>
                                        <input  type="text" class="form-control"  name="dealer_name" id="dealer_name" value = "" placeholder="Enter Dealer Name" autocomplete="off">
                                        <input type="hidden" name="dealer_id" id="dealer_id" value="">
                                        <div id="dealer_name_suggesstion_box"></div>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="booking_request_symptom">Symptom * <span id="error_booking_request_symptom" style="color: red;"></span></label>
                                        <select class="form-control" name="booking_request_symptom" id="booking_request_symptom">
                                            <option disabled selected>Please Select Any Symptom</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Personal Details</h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">                                    
                                    <div class="col-md-4 form-group col-sm-12">
                                        <label for="booking_primary_contact_no">Mobile *</label>
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "" required>
                                        <span id="error_mobile_number" style="color:red"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-sm-12" >
                                        <label for="name">Name *</label>
                                        <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="<?php echo $assigned_vendor_id; ?>" />
                                        <input type="hidden" id="partner_channel" value=""/>
                                        <input type="hidden" name="upcountry_data" id="upcountry_data" value="" />
                                        <input type="hidden" name="partner_type" id="partner_type" value="" />
                                        <input type="hidden" name="partner_code" id="partner_code" value="" />
                                        <input type="hidden" name="partner_name" id="partner_name" value="" />
                                        <input type="hidden" name="agent_id" id="agent_id" value="" />
                                        <input type="hidden" name="is_active" id="is_active" value="" />
                                        <input type="hidden" name="customer_code" id="customer_code" value="" />
                                        <input type="text" class="form-control" id="name" name="user_name"onkeyup="if (/[^|a-z0-9A-Z ]+/g.test(this.value)) this.value = this.value.replace(/[^|a-z0-9A-Z ]+/g, '')"  value = "" placeholder="Please Enter User Name">
                                        <span id="error_username" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-sm-12" >
                                        <label for="booking_user_email">Email </label>
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "" placeholder="Please Enter User Email">
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-group col-sm-12 ">
                                        <label for="booking_pincode">Pincode * </label>
                                        <input type="hidden" class="form-control" id="pincode" name="pincode">
                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "" placeholder="Enter Area Pin" required>
                                        <span id="error_pincode" style="color: red;"></span>
                                    </div>
                                    <div class="col-md-4 form-group col-md-12">
                                        <label for="booking_city">City * <span style="color:grey;display:none" id="city_loading">Loading ...</span></label>
                                        <input type="hidden" class="form-control" id="city" name="city" value = "" >                                                            
                                        <select class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                        </select>
                                        <span id="error_city" style="color: red;"></span>
                                    </div>                                    
                                    <div class="col-md-4 form-group col-sm-12 ">
                                        <label for="booking_alternate_contact_no">Alternate Mobile</label>
                                        <input class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "" placeholder ="Please Enter Alternate Contact No" >                                                            
                                        <span id="error_alternate_contact_no" style="color: red;"></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-group col-sm-12 ">
                                        <label for="landmark">Landmark </label>
                                        <input type="text" class="form-control" id="landmark" name="landmark" value = "" placeholder="Enter Any Landmark">
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12 ">
                                        <label for="booking_address">Booking Address * </label>
                                        <input type="hidden" name="address" id="address" value=""/>
                                        <textarea class="form-control" rows="2" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ></textarea>
                                        <span id="error_address" name="error_address" style="color: red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_content">
                                    <div class="col-md-4">
                                        <label for="booking_otp">OTP * <span id="response_otp" style="color:orange;"></span></label>
                                        <input type="text" name="booking_otp" id="booking_otp" class="form-control" value=""/>
                                    </div>
                                    <div class="col-md-12">
                                        <i class="fa fa-mobile" style="padding-right: 2px;"></i><a id="request_otp" style="cursor: pointer;"> Request OTP </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">                            
                            <center>
                               <input type="hidden" id="not_visible" name="not_visible" value="0"/>
                               <input type="hidden" name="product_type" value="Delivered"/>
                               <input type="button" id="submitform" class="btn btn-primary " onclick="return check_validation()" value="Submit Booking" style="background-color: #2c9d9c;border: 1px solid #2c9d8c;width:300px;border-radius: 20px;">
                               <p id="error_not_visible" style="color: red"></p>
                           </center>  
                        </div>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<!------------------------ HTML Ends Here ------------------>

<script type="text/javascript">
    var Repair_IW = "<?php echo REPAIR_IN_WARRANTY_STRING; ?>";
    var Repair_ELS = "<?php echo REQUEST_TYPE_ELS; ?>";
    var baseUrlLink = "<?php echo base_url(); ?>";
    var booking_create_date = "<?= date('Y-m-d')?>";
    
    // DO not call function, in case of multiple brands
    if($("#appliance_brand_1 option").length < 2){
        get_appliance();
        get_partner();
    }

    $("#booking_request_symptom").select2();
    $("#model_number_1").select2();
    $("#service_name").select2();
    $("#appliance_brand_1").select2();
    $("#appliance_capacity_1").select2();
    $("#appliance_category_1").select2();
    $("#partner_source").select2();
    $("#booking_city").select2({
        tags: true
    });

    $("#booking_pincode").keyup(function(event) {
        get_city();
    });
    
    $('#purchase_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate:"01-01-1998",
        maxDate:'<?php echo date("d-m-Y"); ?>',
        locale:{
        format: 'DD-MM-YYYY'
        }
    });
    
    $('#purchase_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        check_booking_request();
    });

    $('#purchase_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    getPartnerChannel();    
    function getPartnerChannel(){
        var partnerChannelServiceUrl = '<?php echo base_url(); ?>employee/partner/get_partner_channel/';
        var postData = {};
        postData['partner_id'] = $("#partner_id").val();
        postData['channel'] = $("#partner_channel").val();
        if (postData['partner_id'] !== null){
            sendAjaxRequest(postData, partnerChannelServiceUrl).done(function (data) {
                $("#partner_source").html("");
                $("#partner_source").html(data).change();
            });
        }
    }
    
    function get_city(pincode1 = '', service_id1 = ''){
        var pincode = pincode1;
        var service_id = service_id1;
        var btn_submit = 'btn_submit';
        if (pincode1 == '') {
            pincode = $("#booking_pincode").val();
            btn_submit = 'submitform';
        }
        if (service_id1 == '') {
            service_id = $("#service_name").val();
            btn_submit = 'submitform';
        }

        $('#' + btn_submit).prop('disabled', true);
        if (pincode.length === 6 && service_id != null){
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                    $('#city_loading').css("display", "-webkit-inline-box");
                    $('#' + btn_submit).prop('disabled', true);
                },
                url: '<?php echo base_url(); ?>employee/partner/get_district_by_pincode/' + pincode + "/" + service_id,
                async: false,
                success: function (data) {
                    if (data.includes("ERROR")){
                            alert("Service Temporarily Un-available In This Pincode, Please Contact backoffice Team.");
                            $('#booking_city').select2().html("").change();
                            $('#' + btn_submit).prop('disabled', true);
                            $("#not_visible").val('0');
                    }
                    else if (data.includes("Not_Serve")){
                            alert("This PINCODE is not in your Serviceable Area associated with us!");
                            $('#booking_city').select2().html("").change();
                            $('#' + btn_submit).prop('disabled', true);
                            $("#not_visible").val('0');
                    }
                    else {
                        if (pincode1 == '') {
                            $('#booking_city').select2().html(data).change();
                        }
                        else {
                            $('#city').select2().html(data).change();
                            getVendorData();
                        }
                        $('#' + btn_submit).prop('disabled', false);
                        $("#not_visible").val('1');
                    }
                },
                complete: function(){
                    $('#city_loading').css("display", "none");
                }
            });
        }
    }
    
    get_symptom();
    function get_symptom(symptom_id = ""){
        var array = [Repair_ELS];
        var postData = {};

        if (array.length > 0){
            postData['partner_id'] = $("#partner_id").val();
            postData['request_type'] = array;
            postData['service_id'] = $("#service_name").val();
            postData['booking_request_symptom'] = symptom_id;
            var url = '<?php echo base_url(); ?>employee/booking_request/get_booking_request_dropdown';
            sendAjaxRequest(postData, url).done(function (data) {
                $('#booking_request_symptom').html("<option disabled selected>Please Select Any Symptom</option>");
                if (data === "Error"){
                    $('#booking_request_symptom').append("").change();
                    $("#booking_request_symptom").removeAttr('required');
                } else {
                    $('#booking_request_symptom').append(data).change();
                    $("#booking_request_symptom").attr('required', 'required');
                }
            });
        }
    }

    $("#request_otp").click(function(){
        var exp1 = /^[6-9]{1}[0-9]{9}$/;
        var mobile_number = $('#booking_primary_contact_no').val();
        // Check Mobile Number
        if (!mobile_number.match(exp1)){
            alert('Please Enter Valid User Phone Number');
            display_message("booking_primary_contact_no", "error_mobile_number", "red", "Please Enter Valid User Phone Number");
            return false;
        }
        else if (mobile_number === ""){
            display_message("booking_primary_contact_no", "error_mobile_number", "red", "Please Enter Mobile");
            return false;
        }
        else{
            $("#request_otp").html("Sending OTP ... ");
            $("#request_otp").css('pointer-events','none');
            $('#booking_primary_contact_no').attr("readonly", true);
            $.ajax({
                method:'POST',
                url: baseUrlLink+"employee/partner/request_booking_otp",
                data:{
                    'booking_primary_contact_no' : mobile_number
                },
                success:function(response){
                    console.log(response);
                    $("#request_otp").css('pointer-events','auto');
                    $("#request_otp").html(" Request OTP");
                    $("#response_otp").html("  (OTP Sent Successfully) ");
                    $("#customer_code").val(response);
                }
            });
        }       
    });
</script>
<!--------------------------------- Script Ends Here ---------------------------------------------->