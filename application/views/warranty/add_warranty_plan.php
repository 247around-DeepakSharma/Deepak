<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
    #tabs ul{
    margin:0px;
    padding:0px;
    
    }
    #tabs li{
    list-style: none;
    float: left;
    position: relative;
    top: 0;
    margin: 1px .2em 0 0;
    border-bottom-width: 0;
    padding: 0;
    white-space: nowrap;
    border: 1px solid #2c9d9c;
    background: #d9edf7 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
    font-weight: normal;
    color: #555555;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    border-bottom: 0px;
    background-color: white;
    }
    #tabs button{
        
        align:center;
        font-weight: bold
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    
    /* example styles for validation form demo */
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .err1{
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .vertical-align{
        vertical-align: middle;
        padding-top: 1%;
    }
</style>
<div id="page-wrapper">
    <div class="row">
<!--        <div  class = "panel panel-info" style="margin:20px;" >
            <div class="panel-heading" style="font-size:130%;">
                <?php if(isset($query)){?>
                <form action="<?php echo base_url(); ?>employee/upcountry/assign_sc_to_upcountry" method="POST" style="margin-bottom:8px;" target="_blank">
                    <input type="hidden" value="<?php echo $query[0]['id']; ?>" name="service_center_id" />
                     <input type="hidden" value="<?php echo $query[0]['state']; ?>" name="state" />
                     <input type="submit" value="Add Upcountry" class="btn btn-primary btn-md pull-right" style="margin-left: 1%;"/>
                     <?php if(in_array($this->session->userdata['user_group'], [_247AROUND_ACCOUNTANT, _247AROUND_ADMIN, _247AROUND_RM, _247AROUND_DEVELOPER])) { ?>
                        <a onclick="edit_form();" class="btn btn-primary pull-right" href="javascript:void(0);" title="Edit Service Center" style="margin-left:1%;"><span class="glyphicon glyphicon-pencil"></span></a>
                     <?php } ?>
                </form>
                <?php }?>

            </div>
            <div class="panel-body" style="padding: 0px 23px;">
                <?php if($this->session->userdata('checkbox')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('checkbox') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if(validation_errors()) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . validation_errors() . '</strong>
                    </div>';
                    }
                    ?>
                Tabs below
            <div id="tabs" style="border:0px solid #fff;float:left;" class="panel-info">
                <div class="row">
                    <?php
                        if (!isset($selected_brands_list)) {            
                    ?>

                    <div id="tabs" style=""  class="col-md-12 panel-title"style="padding: 10px 8px 0px;">
                        <ul>
                            <li><a href="#" id="1" class="btn nav nav-pills panel-title" style="background-color:#fff">Basic Details</a></li>
                            <li><a href="#tab-2"  id="2" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title" style="background-color:#d9edf7">Documents</a</li>
                            <li><a href="#tab-3"  id="3" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Products and Brands</a></li>
                            <li><a href="#tab-4"  id="4" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Contact Person</a></li>
                             <li><a href="#tab-5"  id="4" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Bank Details</a></li>
                        </ul>
                    </div>
                </div>

First condition tab ends here
                    <?php
                         }
                        else{
                    ?>
Case 2 tab starts here
                    <div id="tabs" style=""  class="col-md-12 panel-title" style="padding: 10px 8px 0px;">
                        <ul>
                            <li><a href="#" id="1" onclick="load_form(this.id)"  class="nav nav-pills panel-title">Basic Details</a></li>
                            <li><a href="#tab-2"  id="2" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Documents</a></li>
                            <li><a href="#tab-3"  id="3" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Products and Brands</a></li>
                            <li><a href="#tab-4"  id="4" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Contact Person</a></li>
                            <li><a href="#tab-4"  id="5" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Bank Details</a></li>
                        </ul>
                    </div>
                    <?php
                         }    
                    ?>
                </div>
            </div>
        </div>-->
        <?php
        if($this->session->flashdata('warranty_plan_added')){
            echo "<p style ='text-align: center;line-height: 22px;background: #70e2b3;'>".$this->session->flashdata('warranty_plan_added')."</p>";
        }
        ?>
        <div id="container-1" class="panel-body form_container" style="display:block;padding-top: 0px;">
            <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/warranty/save_warranty_plan" method="POST">
                <div  class = "panel panel-info">
                    <div class="panel-heading" style="background-color:#ECF0F1"><center><b>Add Warranty Plan</b></center></div>
                        <div class="panel-body">
                            <div>
                                <input style="width:200px;" type="hidden" class="form-control" id="vendor_id"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('company_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="company_name" class="col-md-3">Company Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="company_name" name="company_name" value = "<?php
                                                if (isset($query[0]['company_name'])) {
                                                    echo $query[0]['company_name'];
                                                }
                                                ?>" placeholder="Company Name">
                                            <?php echo form_error('company_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="name" class="col-md-3">Display Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="name" name="name" value = "<?php
                                                if (isset($query[0]['name'])) {
                                                    echo $query[0]['name'];
                                                }
                                                ?>" placeholder="Public Name" onchange="remove_white_space(this.value)">
                                            <?php echo form_error('name'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('address')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="address" class="col-md-3 vertical-align">Address*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="address" value = "<?php
                                                if (isset($query[0]['address'])) {
                                                    echo $query[0]['address'];
                                                }
                                                ?>" >
                                            <?php echo form_error('address'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group">
                                        <label  for="address" class="col-md-3 vertical-align">Landmark</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control" value = "<?php
                                                if (isset($query[0]['landmark'])) {
                                                    echo $query[0]['landmark'];
                                                }
                                                ?>" name="landmark" >
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-12">
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('state')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">State*</label>
                                        <div class="col-md-8">
                                            <select class=" form-control" name ="state" id="state" onChange="getDistrict(); getRMs(); getASMs();" placeholder="Select State">
                                                <option disabled="disabled" selected="selected"> Select State</option>
                                                <?php
                                                    foreach ($results['select_state'] as $state) {
                                                    ?>
                                                <option value = "<?php echo $state['state'] ?>"
                                                    <?php
                                                        if (isset($query[0]['state'])) {
                                                            if (strtolower(trim($query[0]['state'])) == strtolower(trim($state['state']))) {
                                                        echo "selected";
                                                        }
                                                        }
                                                        ?>
                                                    >
                                                    <?php echo $state['state']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                            <?php echo form_error('state'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('district')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">District*</label>
                                        <div class="col-md-8">
                                            <select id="district_option" class="district form-control" name ="district" onChange="getPincode()">
                                                <option selected disabled>Select District</option>
                                                <option <?php
                                                    if (isset($query[0]['district'])) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php
                                                    if (isset($query[0]['district'])) {
                                                        echo $query[0]['district'];
                                                    }
                                                    ?></option>
                                            </select>
                                            <?php echo form_error('district'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class ="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="state" class="col-md-3 vertical-align">Pincode</label>
                                        <div class="col-md-8">
                                            <select class="pincode form-control" id="pincode" name ="pincode"  >
                                                <option selected disabled>Select Pincode</option>
                                                <option <?php
                                                    if (isset($query[0]['pincode'])) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php
                                                    if (isset($query[0]['pincode'])) {
                                                        echo $query[0]['pincode'];
                                                    }
                                                    ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('company_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="company_type" class="col-md-3">Company Type*</label>
                                        <div class="col-md-8">
                                            <select name="company_type" class="form-control">
                                                <option disabled selected >Select Company Type</option>
                                                <option value="Individual" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Individual") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Individual</option>
                                                <option value="Proprietorship Firm" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Proprietorship Firm") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Proprietorship Firm</option>
                                                <option value="Partnership Firm" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Partnership Firm") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Partnership Firm</option>
                                                <option value="Private Ltd Company" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Private Ltd Company") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Private Ltd Company</option>
                                            </select>
                                            <?php echo form_error('company_type'); ?>
                                        </div>
                                    </div>
                                </div>                                                               
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="phone_1" class="col-md-3 vertical-align">Phone 1*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="phone_1" name="phone_1" value = "<?php
                                                if (isset($query[0]['phone_1'])) {
                                                    echo $query[0]['phone_1'];
                                                }
                                                ?>">
                                            <?php echo form_error('phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="phone_2" class="col-md-3 vertical-align">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="phone_2" name="phone_2" value = "<?php
                                                if (isset($query[0]['phone_2'])) {
                                                    echo $query[0]['phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('asm')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="asm" class="col-md-3 vertical-align">ASM*</label>
                                        <div class="col-md-8">
                                            <select id="asm" class="form-control" name="asm">
                                                <option selected disabled>Select Area Sales Manager</option>                                                
                                            </select>
                                            <?php echo form_error('asm'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('rm')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="rm" class="col-md-3 vertical-align">RM*</label>
                                        <div class="col-md-8">
                                            <select id="rm" class="form-control" name="rm">
                                                <option selected disabled>Select Regional Manager</option>                                                
                                            </select>
                                            <?php echo form_error('rm'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class ="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="email" class="col-md-3 vertical-align">Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="email" value = "<?php
                                                if (isset($query[0]['email'])) {
                                                    echo $query[0]['email'];
                                                }
                                                ?>">
                                            <?php echo form_error('email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
              
                <div class="panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><center><b>Plan Period Details</b></center></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="minimum_guarantee" class="col-md-4 vertical-align">Minimum Guarantee</label>
                                    <div class="col-md-8">
                                        <input  type="text" id="minimum_guarantee" class="form-control"  name="minimum_guarantee_charge" value = "<?php if (isset($query[0]['minimum_guarantee_charge'])) {
                                            echo $query[0]['minimum_guarantee_charge'];
                                        } else { echo "0";} ?>">
                                    </div>
                                    
                                </div>
                            </div>
                            </div>
                        </div>
                   
                        
                    </div>    
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php
                        if (isset($selected_brands_list)) {
                            echo "Update Plan Details";
                        } else {
                            echo "Save Plan Details";
                        }
                        ?>" class="btn btn-primary" id="submit_btn">
                        <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/view_warranty_plan>Cancel</a>"; ?>
                        </center>
<!--                    </div>-->
                </div>
            
        </form>
     </div>      
</div>
</div>
<!--Validations here-->
<?php if($this->session->userdata('checkbox')){$this->session->unset_userdata('checkbox');}?>
<!--Validation for page1-->
<script type="text/javascript">

    $(document).ready(function(){
        
        var rm_id = '<?php if(!empty($rm) && !empty($rm[0]['agent_id'])) { echo $rm[0]['agent_id']; } else { echo ''; }; ?>';
        var vendor_rm_id = '<?php if(!empty($query) && !empty($query[0]['rm_id'])) { echo $query[0]['rm_id']; } else { echo ''; }; ?>';
        var vendor_asm_id = '<?php if(!empty($query) && !empty($query[0]['asm_id'])) { echo $query[0]['asm_id']; } else { echo ''; }; ?>';

        getRMs(vendor_rm_id);
        getASMs(vendor_asm_id);
        get_brands();
    });

function manageAccountNameField(value){
        document.getElementById("bank_account").disabled = false;
    }
    //Adding select 2 in Dropdowns
    $("#district_option").select2();
    $("#state").select2();
    $("#pincode").select2();
    $("#bank_name").select2();

    function getDistrict() {
     var state = $("#state").val();
     var district = $(".district").val();
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict/1',
       data: {state: state, district: district},
       success: function (data) {
        // console.log(data);
         $(".district").html(data);
         if (district != "") {
           getPincode();
         }
       }
     });
    }
        function getRMs(rm_id = '') {
        var state = $("#state").val();
        if(state != ''){
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/vendor/getRMs',
          data: {state: state, rm_id:rm_id},
          success: function (data) {
            $("#rm").html(data);
          }
        });
        }
    }
    
    function getASMs(asm_id = '') {
        var state = $("#state").val();
        if(state != ''){
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/vendor/getASMs',
          data: {state: state, asm_id:asm_id},
          success: function (data) {
            $("#asm").html(data);
          }
        });
        }
    }
    
                function getPincode() {
      var district = $(".district").val();
      var pincode = $(".pincode").val();
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/vendor/getPincode/1',
        data: {pincode: pincode, district: district},
        success: function (data) {
          $(".pincode").html(data);
       }
     });
    }
    
                $(function () {
    var state = $("#state").val();
                    if (state != "") {
        getDistrict();
    }
    });
    
    function get_brands() {
        var appliance = [];
        var service_center_id = $('#vendor_id').val();
        
        $. each($(".appliance:checked"), function(){
            appliance.push($(this).val());
        });
 
        if(appliance.length > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/get_brands',
                data: {appliance: appliance, service_center_id: service_center_id},
                success: function (data) {
                    $('.brands').html(data);
                }
            });
        } else {
            $('.brands').html('Please select appliance.');
        }
    }
</script>
<!--page 1 validations begin here-->
    
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
                    setupFormValidation: function ()
    {
    //form validation rules
    $("#booking_form").validate({
    rules: {
        company_name: "required",
        name: "required",
        address: "required",
        district: "required",
        company_type:"required",
        rm: "required",
        phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        phone_2: {
            minlength: 10,
            number: true
        },
        state: "required",
        
        email: {
            email: true
        },
        municipal_limit: {
            number: true
        },
    },
    messages: {
        company_name: "Please enter Company Name",
        name: "Please enter Public Name",
        address: "Please enter Address",
        district: "Please Select District",
        rm: "Please Select RM",
        state: "Please Select State",
        phone_1: "Please enter Phone Number",
        phone_2: "Please fill correct phone number",
        
        email: "Please fill correct email",
        
        
    },
        submitHandler: function (form) {
            
        var municipal_limit = $("#municipal_limit").val();
            if(Number(municipal_limit) < 1){
            alert("Please Add Municipal Limit");
            return false;}
        if($('#is_sf').is(':checked')==0 && $('#is_cp').is(':checked')==0 && $('#is_wh').is(':checked')==0 && $('#is_buyback_gst_invoice').is(':checked')==0){
            alert("Please Select Atleast One Checkbox of Service Center OR Collection Partner OR Warehouse OR Buyback Invoice on GST");
            return false;
        }
        form.submit();
        }
    });
    }
    };
    
    //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
    JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    
           
</script> 
<!--page 1 validations end here-->
<!--page 2 validations begin-->
<script type="text/javascript">
    var is_saas = '<?php if($saas_module) { echo '1'; } else { echo '0'; } ?>';
    var gstRegExp = /^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[a-zA-Z0-9]{1}/;
    $.validator.addMethod('gstregex', function (value, element, param) {
                return this.optional(element) || gstRegExp.test( value);
            }, 'Please enter Valid GST Number'); 
     function validate_documents(){
         
        if(is_saas == '1') { // check all documents uploaded for kenstar.
            var is_documents_submit = check_documents();
            if(is_documents_submit === false) {
                return false;
            }
        }
         
            if($('#is_pan_doc').is(":checked")){
               if($('#pan_no').val()!== '' && $('#name_on_pan').val() != ''){
                   alert('Please Enter PAN Details or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#pan_no').val() == '' && $('#name_on_pan').val() == ''){
                   alert('Please Enter PAN Details or Tick "Not Available" checkbox');
                   return false;
               }
                //checking case when pan number is empty and pan name is enterd
                else if($('#pan_no').val() == '' && $('#name_on_pan').val() != ''){
                   alert('Please add Pan No along with Pan Name');
                   return false;
               }
               //checking case when pan number is less than 6 and greater than 10 and pan name is enterd
               else if($('#pan_no').val().length !== 10 && $('#name_on_pan').val() != ''){
                   alert('Please add valid 10 digit pan number');
                   return false;
               }
               //checking case when pan number 10 and pan name is enterd but panfile is not uploaded
               <?php if(empty($query[0]['pan_file'])){ ?>
                           else if($('#pan_no').val().length === 10 && $('#name_on_pan').val() != '' && $('#pan_file').val()== 0){
                           alert('Please upload pan file also');
                   return false;
               } <?php }?>
            }
            //Check for GST  no.
            if($('#is_gst_doc').is(":checked")){ 
               if($('#gst_no').val() != ''){
                   alert('Please Enter valid GST Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{ 
                var is_gst_file = <?php if(isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])){ echo '1';}else{echo '0';}?>;
                if($('#gst_no').val() == ''){
                   alert('Please Enter GST Number or Tick "Not Available" checkbox');
                   return false;
                }
                else if($('#gst_no').val().length === '15'){
                   alert('Please Enter Valid GST Number');
                   return false;
                }
                else if($('#gst_type').val() == '' || $('#gst_status').val() == ''){
                   alert('Please Enter Valid GST Number or Tick "Not Available" checkbox');
                   return false;
                }
                else if($('#gst_no').val() != '' && $('#gst_file').get(0).files.length === 0 && is_gst_file === 0){
                   alert("Please Upload GST File");
                   return false;
                }
            }
             var is_signature_file = <?php if(isset($query[0]['signature_file']) && !empty($query[0]['signature_file'])){ echo '1';}else{echo '0';}?>;
             if(is_signature_file == 0){
                var is_signature_file = $('#signature_file').get(0).files.length;
             }
             if(!(is_signature_file) && ($('#is_gst_doc').is(":checked")) ){
                   alert('Please Update Signature file');
                   return false;
    }
    }
</script>

<!--page 2 validations end-->

<!--page 3 validations - none-->

<!--page 4 validations begin here-->
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
                    setupFormValidation: function ()
    {
    //form validation rules
    $("#booking_form4").validate({
    rules: {
        primary_contact_phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        primary_contact_phone_2: {
            number: true
        },
        owner_phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        owner_phone_2: {
            number: true
        },
        primary_contact_name: "required",
        owner_name: "required",
        primary_contact_email: {
            required: true
        },
        owner_email: {
            required: true
        },
    },
    messages: {
        primary_contact_phone_1: "Please fill correct phone number",
        primary_contact_phone_2: "Please fill correct phone number",
        owner_phone_1: "Please fill correct phone number",
        owner_phone_2: "Please fill correct phone number",
        primary_contact_name: "Please fill Name",
        owner_name: "Please fill Name",
        primary_contact_email: "Please fill correct email",
        owner_email: "Please fill correct email",
        
    },
        submitHandler: function (form) {
        form.submit();
        }
    });
    }
    };
    
    //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
    JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    
</script>    
<!--page 4 validations end here-->

<script type="text/javascript">
    function load_form(tab_id){
       total_div  = document.getElementsByClassName('form_container').length;
       for(var i =1;i<=total_div;i++){
           if(i != tab_id){
             document.getElementById("container-"+i).style.display='none';
             document.getElementById(i).style.background='#d9edf7';
            }
            else{
                document.getElementById("container-"+i).style.display='block';
                document.getElementById(i).style.background='#fff';
            }
       }
       
    }
</script>
<script type="text/javascript">
    <?php if((isset($query[0]['is_verified']) && !empty($query[0]['is_verified'])) && (!in_array($this->session->userdata('user_group'), [_247AROUND_ACCOUNTANT, _247AROUND_ADMIN]))){?>
        $('#bank_details').find('input').attr('readonly', true);
    <?php } ?>
    $(".allowNumericWithDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) || e.ctrlKey) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(document).ready(function () {
        var charReg = /^[0-9a-zA-Z,.()+\/\s-]*$/;
        $('.blockspacialchar').focusout(function () {
            var inputVal = $(this).val();

            if (!charReg.test(inputVal)) {
                alert("Spacial Characters are not allowed");
                $(this).css({'border-color' : 'red'});
                $('#submit_btn').attr('disabled','disabled');
            }else{
                $(this).css({'border-color' : '#ccc'});
                $('#submit_btn').removeAttr('disabled');
            }

        });
    });
    function remove_white_space(name){
        newValue = name.replace(/\s+$/, '');
        $('#name').val(newValue);
    }
    
    $(document).ready(function () {
        var regxp = /^(\s*|\d+)$/;
        $('.verigymobileNumber').focusout(function () {
            var inputVal = $(this).val();

            if (!regxp.test(inputVal)) {
                alert("Please Enter Valid Phone Number");
                $(this).css({'border-color' : 'red'});
            }else{
                $(this).css({'border-color' : '#ccc'});
            }

        });
    }); 
    
  
</script>
<!--function to remove image-->
<script>
    function remove_image(type,vendor_id,file_name){
            var c  = confirm('Do you want to permanently remove photo?');
            if(c){
             $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/vendor/remove_image',
                        data: {type: type, id: vendor_id,file_name:file_name},
                        success: function (data) {
                             location.reload();
    //                             console.log(data);
                            }
                    });
                 }else{
                    return false;
                 }
        }
        function validate_bank_details(){
                cheque_final = 1
                    var cheque_already = <?php if(isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])){ echo '1';}else{echo '0';}?>;
                    var cheque = $('#cancelled_cheque_file').val();      
                    if(cheque == null || cheque == ''){
                        if(cheque_already == 0){
                            cheque_final = 0;
                        }
                }
            var bank_name = $('#bank_name').val();
            var account_type = $('#account_type').val();
            var bank_account = $('#bank_account').val();
            var ifsc_code = $('#ifsc_code').val();
            var beneficiary = $('#beneficiary_name').val();
            if(!$('#ifsc_code').val().match('^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$')){
                alert("Please enter alphanumeric value for IFSC Code");
                return false;
            }
            
            if($('#ifsc_code').val().length < 11){
                alert("Please enter 11 alphanumeric number");
                return false;
            }
            
            if($("#ifsc_validation").val() != null && $("#ifsc_validation").val() == ""){
               alert("Incorrect IFSC Code");
               return false; 
            }
            
            if((cheque_final) && !(bank_name == null || bank_name == '') && !(account_type == null || account_type == '') && !(bank_account == null || bank_account == '') && 
                    !(ifsc_code == null || ifsc_code == '') && !(beneficiary == null || beneficiary == '')){
               return true;
                }
                else{
                    alert("Please Fill all banks related fields");
                    return false;
                }
             
        }
    function validateGSTNo(){
        var gstin = $("#gst_no").val();
        gstin = gstin.trim().toUpperCase();
        $("#gst_no").val(gstin);
        var vendor_id="";
        if($("#vendor_id").val()){
            vendor_id = "/"+$("#vendor_id").val()+"/vendor";
        }
        if(gstin.length == '15'){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/check_GST_number/'+gstin+vendor_id,
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status_cd != '0'){
                        $("#gst_type").val(response.dty);
                        $("#gst_status").val(response.sts);
                        $("#gst_cancelled_date").val(response.cxdt);
                        $("#gst_type").attr("readonly", "readonly");
                        $("#gst_status").attr("readonly", "readonly");
                        if(response.dty !== 'Regular' || response.sts !== 'Active'){
                            alert('Filled GST number detail - \n GST Type - '+response.dty+' \n GST Status - '+ response.sts);
                        }
                    }
                    else{
                        $("#gst_type, #gst_status").val("");
                        if(response.errorMsg){
                           alert(response.errorMsg);
                        }
                        else if(response.error.message){
                            if(response.error.error_cd == '<?php echo INVALID_GSTIN; ?>'){
                                alert("<?php echo INVALID_GSTIN_MSG; ?>");
                            }else{
                                alert("Error occured while checking GST number try again");
                            }
                        }
                        else{
                           alert("API unable to work contact tech team!"); 
                        }
                    }
                }
            });
        }
        else{
            $("#gst_type, #gst_status").val("");
        }
    }
    
    function validate_ifsc_code(){
        var ifsc_code = $("#ifsc_code").val();
        if(ifsc_code.length == '11'){
            var first4char =  ifsc_code.substring(0, 4);
            var first5char =  ifsc_code.substring(4, 5);
            if(!first4char.match(/^[A-Za-z]+$/)){
                alert("In IFSC code first four digit should be Charecter");
                return false;
            }
            else if(first5char != "0"){
                alert("In IFSC code fifth digit should be 0");
                return false;
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/vendor/validate_ifsc_code',
                    data: {ifsc_code:ifsc_code, entity_type:"vendor", entity_id:$("#vendor_id").val()},
                    success: function (response) {
                        response = response.trim();
                        if(response=='"Not Found"'){
                            $("#ifsc_validation").val("");
                            $("#info_div").css("display", "none");
                            alert("Incorrect IFSC Code");
                        }
                        else{
                            if(IsJsonString(response)){
                                var bank_data = JSON.parse(response);
                                $("#ifsc_validation").val(JSON.stringify(bank_data));
                                $("#info_div").css("display", "block");
                                $("#info_msg").html("You have entered valid IFSC code  - <br/> Bank Name = "+bank_data.BANK.toLowerCase()+" <br/> Branch = "+bank_data.BRANCH.toLowerCase()+" <br/> City = "+bank_data.CITY.toLowerCase()+" <br/> State = "+bank_data.STATE.toLowerCase()+" <br/> Address = "+bank_data.ADDRESS.toLowerCase());
                            }
                            else{
                                $("#ifsc_validation").val("");
                                alert("IFSC code verification API fail. Please contact tech team");
                            }
                        }
                    }
                });
            }
        }
        else{
           $("#ifsc_validation").val("");
        }
    }
    
    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    
    $('#brands_all').click(function(){
        if($(this).prop('checked'))
        {
            $('input[name="brands[]"]').prop("checked", true);
        }
        else
        {
            $('input[name="brands[]"]').prop("checked", false);
        }
    });

</script>

<style>
    .panel {
        border-radius:0px !important;
    }

<?php if(empty($is_editable) && !empty($query[0]['id'])) {  ?>
    
    #container-1, .form-control, .select2 {
        pointer-events:none; 
    }
    .form-control, .select2 { 
        background-color:#e6ede8;
    }
    
    .select2-container--default .select2-selection--single {
         background-color:#e6ede8;
    }
    
    #submit_btn, .cancel{
        display:none;
    }
    a[title="Remove Image"] {
         display:none;
    }
  .select2-container .select2-selection--multiple {
    background-color: #e6ede8;
}
<?php } ?>

</style>    

<script>
    function edit_form() {
        $('#container-1, .form-control, .select2, #submit_btn').css('pointer-events', 'auto');
        $('.form-control, .select2, .select2-container--default .select2-selection--single, .select2-container .select2-selection--multiple').css('background-color', 'white');
        $('#submit_btn, .cancel, a[title="Remove Image"]').css('display', 'inline');
    }
    
    function check_documents() {
        var documents = ['name_on_pan', 'pan_no', 'gst_no'];
        var bl_validate = 1;
        $.each(documents, function( index, value ) {
            if(value.includes('file')) { // check input type is file
                var input_value = $('#'+value).attr('value');
                if(input_value == '') {
                    input_value = $('#'+value).val();
                }
            } else {
                input_value = $('#'+value).val();
            }

            if(input_value == '') {
                $('#'+value).css('border-color', 'red');
                alert('Please fill the manadatory details.');
                bl_validate = 0;
                return false;
            } else {
                $('#'+value).css('border-color', 'white');
            }
        });
        
        if(bl_validate == 0) {
            return false;
        } else {
            return true;
        }

    }
</script>