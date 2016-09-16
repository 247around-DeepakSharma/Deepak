<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
   <h1>
       Add Engineer
   </h1>
    <div class="container" >
       <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
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
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/vendor/process_add_engineer"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Engineer Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                                    <label for="name" class="col-md-4">Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="name" value = "<?php echo set_value('name');  ?>" placeholder="Enter Engineer Name" required>
                                    </div>
                                    <?php echo form_error('name'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>" >
                                    <label for="Mobile" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="mobile" name="phone" value = "<?php echo set_value('phone');  ?>" placeholder="Enter Mobile Number" required>
                                    </div>
                                    <?php echo form_error('phone'); ?>
                                </div>
                                <div class="form-group  <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                                    <label for="Mobile" class="col-md-4">Alternate Mobile </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="alternate_phone" name="alternate_phone" value = "<?php echo set_value('alternate_phone');  ?>" placeholder="Enter Alternate Mobile" >
                                    </div>
                                    <?php echo form_error('alternate_phone'); ?>
                                </div>
                               
                                
                            </div>
                            <!-- end div -->
                            <div class="col-md-6">
                                
                                <div class="form-group <?php if( form_error('phone_type') ) { echo 'has-error';} ?>">
                                    <label for="phone type" class="col-md-4">Phone Type </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="phone_type" name="phone_type"  >
                                            <option disabled selected>Select Phone type</option>
                                            <option  <?php if(set_value('phone_type') == 'Android'){ echo "selected"; }  ?>>Android</option>
                                            <option <?php if(set_value('phone_type') == 'Non-Android'){ echo "selected"; }  ?>>Non-Android</option>
                                            <option <?php if(set_value('phone_type') == 'Apple'){ echo "selected"; }  ?>>Apple</option>
                                        </select>
                                    </div>
                                    <?php echo form_error('phone_type'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                    <label for="Appliances" class="col-md-4">Appliances *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="service_id" name="service_id[]" multiple="multiple"  required>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option <?php if(set_value('service_id') == $values->id){ echo "selected"; }  ?> value=<?php echo $values->id; ?>>
                                                <?php echo $values->services; }    ?>
                                            </option>
                                        </select>
                                    </div>
                                     <?php echo form_error('service_id'); ?>
                                </div>
                                 <div class="form-group <?php if( form_error('service_center_id') ) { echo 'has-error';} ?>">
                                    <label for="Mobile" class="col-md-4">Service Center *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="service_center_id" name="service_center_id"  required>
                                            <option disabled selected>Select Service Center</option>
                                            <?php foreach ($service_center as $key => $values) { ?>
                                            <option <?php if(set_value('service_center_id') == $values['id']){ echo "selected"; }  ?> value=<?php echo $values['id']; ?>>
                                                <?php echo $values['name']; }    ?>
                                            </option>
                                        </select>
                                    </div>
                                    <?php echo form_error('service_center_id'); ?>

                                </div>

                            </div>
                            <!-- end div -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">ID Proof Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('identity_proof') ) { echo 'has-error';} ?>">
                                    <label for="identity proof" class="col-md-4">ID Proof </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="identity_proof" name="identity_proof"  >
                                            <option disabled selected>Select ID Proof</option>
                                            <option <?php if(set_value('identity_proof') == 'Aadhar Card'){ echo "selected"; }  ?> value="Aadhar Card">Aadhar Card</option>
                                            <option <?php if(set_value('identity_proof') == 'Driving License'){ echo "selected"; }  ?> value="Driving License">Driving License</option>
                                            <option <?php if(set_value('identity_proof') == 'Voter ID Card'){ echo "selected"; }  ?> value="Voter ID Card">Voter ID Card</option>
                                            <option <?php if(set_value('identity_proof') == 'PAN Card'){ echo "selected"; }  ?> value="PAN Card">PAN Card</option>
                                            <option <?php if(set_value('identity_proof') == 'Ration Card'){ echo "selected"; }  ?> value="Ration Card">Ration Card</option>
                                            <option <?php if(set_value('identity_proof') == 'Passport'){ echo "selected"; }  ?> value="Passport">Passport</option>
                                            <option <?php if(set_value('identity_proof') == 'Others'){ echo "selected"; }  ?> value="Others">Others</option>
                                        </select>
                                    </div>
                                    <?php echo form_error('identity_proof'); ?>
                                </div>
                                
                            </div>
                            <!-- end -->
                            <div class="col-md-6 <?php if( form_error('identity_id_number') ) { echo 'has-error';} ?>">
                             <div class="form-group">
                                    <label for="Identity ID Number" class="col-md-4">ID Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="identity_id_number" name="identity_id_number" value = "<?php echo set_value('identity_id_number');  ?>" placeholder="Enter ID Number" >
                                    </div>
                                </div>
                                <?php echo form_error('identity_id_number'); ?>
                            </div>
                            <!-- end -->
                        </div>
                    </div>
                </div>
            </div>
             <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Bank Account Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                               
                                <div class="form-group <?php if( form_error('bank_name') ) { echo 'has-error';} ?>">
                                    <label for="bank name" class="col-md-4">Bank Name </label>
                                    <div class="col-md-6">
                                  
                                    <input type="text" class="form-control"  id="bank_name" name="bank_name" value = "<?php echo set_value('bank_name');  ?>" placeholder="Enter Bank Name" >

                                    </div>
                                    <span id="errmsg1"></span>
                                    <?php echo form_error('bank_name'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('bank_ifsc_code') ) { echo 'has-error';} ?>">
                                    <label for="bank_ifsc_code" class="col-md-4">IFSC Code </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_ifsc_code" name="bank_ifsc_code" value = "<?php echo set_value('bank_ifsc_code');  ?>" placeholder="Enter IFSC code" >
                                    </div>
                                    <?php echo form_error('bank_ifsc_code'); ?>
                                </div>
                               
                                
                            </div>
                            <!-- end div -->
                            <div class="col-md-6">
                                
                                <div class="form-group <?php if( form_error('bank_account_no') ) { echo 'has-error';} ?>">
                                    <label for="bank account no" class="col-md-4">Bank Account No. </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_account_no" name="bank_account_no" value = "<?php echo set_value('bank_account_no');  ?>" placeholder="Enter Account Number" >
                                    </div>
                                     <?php echo form_error('bank_account_no'); ?>
                                    <span id="bank_account_no1" style="color: red; "></span>
                                </div>
                                 
                                <div class="form-group <?php if( form_error('bank_holder_name') ) { echo 'has-error';} ?>">
                                    <label for="bank account no" class="col-md-4">Bank Account Holder Name </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_holder_name" name="bank_holder_name" value = "<?php echo set_value('bank_holder_name');  ?>" placeholder="Enter Account Holder Name" >
                                    </div>
                                </div>
                                <?php echo form_error('bank_holder_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
            <input type="submit" class="form-control btn btn-md btn-primary" onclick="return validate_bank_ac()"  value="Save Engineer"></input>
            </div>
            
        </form>
    </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<script type="text/javascript">
    $("#service_center_id").select2();
    
    
    $("#service_id").select2({
    tags: "true",
    placeholder: " Select Appliances",
    allowClear: true
    });
    
</script>
<style type="text/css">
    

    #engineer_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 0px 0 0px 0px;
    padding: 0;
    text-align: left;
    
    
    }
</style>

<script type="text/javascript">

    function onsubmit(){
    
        var bank_account_no = $('#bank_account_no').val();
        alert(bank_account_no);
    }

    (function ($, W, D)
    {
    var JQUERY4U = {};

    JQUERY4U.UTIL =
        {
            setupFormValidation: function ()
            {
            //form validation rules
            $("#engineer_form").validate({
                rules: {
                name: "required",
                phone: {
                        required: true,
                        minlength: 10
                    },
                alternate_phone:{
                    
                    minlength: 10
                    },
                service_center_id:"required",
                bank_ifsc_code: {
                        required: true,
                        rangelength: [11, 11]
                    },
                bank_name:{
                        required: true,
                        minlength: 3
                    },
                bank_holder_name:{
                        required: true,
                        minlength: 3
                    },
                service_id: "required",
                bank_account_no: {
                    digits: true,
                    required:true
                        
                    }
                },
                messages: {
                name: "Please Enter Name",
                phone: "Please Enter Mobile Number",
                service_center_id: "Please Select Service Center",
                bank_name: "Please Enter Bank Name",
                bank_ifsc_code:"Please Enter Correct IFSC Code",
                bank_holder_name: "Please Enter Account Holder Name",
                bank_account_no: "Please Enter Account Number",
                service_id:"Please Select Appliances"

                },
                submitHandler: function (form) {
                form.submit();
                }
            });
            }
        }

    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

    })(jQuery, window, document);

    function validate_bank_ac(){
       
        var bank_account_no = Number($("#bank_account_no").val());
        if(bank_account_no > 0){
             document.getElementById('bank_account_no').style.borderColor = "#ccc";
            document.getElementById("bank_account_no1").innerHTML = "";
          return true;

        } else {
            document.getElementById('bank_account_no').style.borderColor = "red";
            document.getElementById("bank_account_no1").innerHTML = "Please Enter Valid Account Number";
            return false;
        }

    }

</script>

