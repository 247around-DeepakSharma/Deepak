<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
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
                <div class="panel-heading">Add Engineer Deatils</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="name" value = "" placeholder="Enter Engineer Name" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Mobile" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="mobile" name="phone" value = "" placeholder="Enter Mobile Number" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Mobile" class="col-md-4">Alternate Mobile Number </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="alternate_phone" name="alternate_phone" value = "" placeholder="Enter Mobile Number" >
                                    </div>
                                </div>
                                 <div class="form-group ">
                                    <label for="Appliances" class="col-md-4">Appliances *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="service_id" name="service_id[]" multiple="multiple"  required>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option value=<?php echo $values->id; ?>>
                                                <?php echo $values->services; }    ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                               
                                
                            </div>
                            <!-- end div -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="Mobile" class="col-md-4">Address </label>
                                    <div class="col-md-6">
                                        <textarea name="address" class="form-control" id="address" rows="4" placeholder="Please Enter Address" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="phone type" class="col-md-4">Phone Type </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="phone_type" name="phone_type"  >
                                            <option disabled selected>Select Phone type</option>
                                            <option>Android</option>
                                            <option>Non-Android</option>
                                            <option>Apple</option>
                                        </select>
                                    </div>
                                </div>
                               

                            </div>
                            <!-- end div -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Identity Proof Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="bank name" class="col-md-4">Identity Proof </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="identity_proof" name="identity_proof"  >
                                            <option disabled selected>Select Identity Proof</option>
                                            <option value="Aadhar Card">Aadhar Card</option>
                                            <option value="Passport">Passport</option>
                                            <option value="Driving License">Driving License</option>
                                            <option value="Voter ID Card">Voter ID Card</option>
                                            <option value="PAN Card">PAN Card</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    <label for="Identity Picture" class="col-md-4">Identity Picture</label>
                                    <div class="col-md-6">
                                       <input type="file" class="form-control" name="file" disabled >
                                    </div>
                                </div>
                            </div>
                            <!-- end -->
                            <div class="col-md-6">
                             <div class="form-group">
                                    <label for="Identity ID Number" class="col-md-4">Identity ID Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="identity_id_number" name="identity_id_number" value = "" placeholder="Enter Identity Id Number" >
                                    </div>
                                </div>
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
                               
                                <div class="form-group ">
                                    <label for="bank name" class="col-md-4">Bank Name </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="bank_name" name="bank_name"  >
                                             <option disabled selected>Select Bank Name</option>
                                            <option value="ANDHRA">Andhra Bank</option>
                                            <option value="AXIS">Axis Bank</option>
                                            <option value="BOB">Bank of Baroda</option>
                                            <option value="BHARAT">Bharat Bank</option>
                                            <option value="BOI">Bank of India</option>
                                             <option value="BBK">Bank of Bahrain and Kuwait</option>
                                            <option value="BOM">Bank of Maharashtra</option>
                                            <option value="CANARA">Canara Bank</option>
                                            <option value="CSB">Catholic Syrian Bank</option>
                                            <option value="CBI">Central Bank of India</option>
                                            <option value="CITIUB">City Union Bank</option>
                                            <option value="CORP">Corporation Bank</option>
                                            <option value="COSMOS">Cosmos Bank</option>
                                            <option value="CITI">Citibank</option>
                                            <option value="ICICI">ICICI Bank</option>
                                            <option value="DEUTS">Deutsche Bank</option>
                                            <option value="HDFC">HDFC Bank</option>
                                            <option value="JSB">Janata Sahakari Bank Ltd Pune</option>
                                            <option value="KOTAK">Kotak Bank</option>
                                           <option value="PNB">Punjab National Bank</option>
                                            <option value="SBI">State Bank of India</option>                        
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="bank_ifsc_code" class="col-md-4">Bank IFSC Code </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_ifsc_code" name="bank_ifsc_code" value = "" placeholder="Enter Bank IFSC code" >
                                    </div>
                                </div>
                            </div>
                            <!-- end div -->
                            <div class="col-md-6">
                                
                                <div class="form-group ">
                                    <label for="bank account no" class="col-md-4">Bank Account No. </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_account_no" name="bank_account_no" value = "" placeholder="Enter Bank Account Number" >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="bank account no" class="col-md-4">Bank Account Holder Name </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_holder_name" name="bank_holder_name" value = "" placeholder="Enter Bank Account Holder Name" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
            <input type="submit" class="form-control btn btn-md btn-primary" value="Submit Form"></input>
            </div>
            
        </form>
    </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<script type="text/javascript">
    $("#service_center_id").select2();
    $("#bank_name").select2();
    
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
                
                service_id: "required",
                bank_account_no: {
                        digits: true
                        
                    }
                },
                messages: {
                name: "Please Enter Name",
                phone: "Please Enter Mobile Number",
                service_id:"Please Select Appliances",
                bank_account_no: "Please Enter Only Digits"

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



</script>