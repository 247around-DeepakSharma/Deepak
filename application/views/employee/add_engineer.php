<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
       <?php echo (isset($data)?'Edit Engineer':'Add Engineer')?>
   </h1>
    
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
        <?php if($this->session->userdata('update_error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('update_error') . '</strong>
                    </div>';
                    }
        ?>
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/vendor/<?php echo isset($data)?'process_edit_engineer':'process_add_engineer'?>"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Engineer Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php if(!empty($data)){?>
                                <input type="hidden" value="<?php echo $data[0]['id']?>" name="id"/>
                                <?php }?>
                                <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                                    <label for="name" class="col-md-4">Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="name" value = "<?php echo isset($data[0]['name'])?$data[0]['name']:set_value('name');  ?>" placeholder="Enter Engineer Name" required>
                                    </div>
                                    <?php echo form_error('name'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>" >
                                    <label for="Mobile" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="mobile" name="phone" value = "<?php echo isset($data[0]['phone'])?$data[0]['phone']:set_value('phone');  ?>" placeholder="Enter Mobile Number" required>
                                        SMS will be delivered to this Mobile
                                    </div>

                                    <?php echo form_error('phone'); ?>
                                </div>

                                <div class="form-group  <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                                    <label for="Mobile" class="col-md-4">Alternate Mobile </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="alternate_phone" name="alternate_phone" value = "<?php echo isset($data[0]['alternate_phone'])?$data[0]['alternate_phone']:set_value('alternate_phone');  ?>" placeholder="Enter Alternate Mobile" >
                                    </div>
                                    <?php echo form_error('alternate_phone'); ?>
                                </div>


                            </div>
                            <!-- end div -->
                            <div class="col-md-6">

<!--                                <div class="form-group <?php //if( form_error('phone_type') ) { echo 'has-error';} ?>">
                                    <label for="phone type" class="col-md-4">Phone Type </label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="phone_type" name="phone_type"  >
                                            <option disabled <?php //echo isset($data[0]['phone_type'])?'':'selected'?>>Select Phone type</option>
                                            <option  <?php //echo (isset($data[0]['phone_type']) && $data[0]['phone_type'] == 'Android' )?"selected":(set_value('phone_type') == 'Android')?"selected":'';   ?>>Android</option>
                                            <option <?php //echo (isset($data[0]['phone_type']) && $data[0]['phone_type'] == 'Non-Android' )?"selected":(set_value('phone_type') == 'Non-Android')?"selected":'';   ?>>Non-Android</option>
                                            <option <?php //echo (isset($data[0]['phone_type']) && $data[0]['phone_type'] == 'Apple' )?"selected":(set_value('phone_type') == 'Apple')?"selected":'';   ?>>Apple</option>
                                        </select>
                                    </div>
                                    <?php //echo form_error('phone_type'); ?>
                                </div>-->
                                <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                    <label for="Appliances" class="col-md-4">Appliances *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="service_id" name="service_id[]" multiple="multiple"  required>
                                                <?php 
                                                $list = [];
                                                if(isset($data)){
                                                    $appliance_id = $data[0]['appliance_id'];
                                                    foreach($appliance_id as $key =>$value){
                                                        $list[] = $value['service_id'];
                                                    }
                                                }?>
                                            <?php foreach ($services as $key => $values) { 
                                                ?>
                                            
                                            <option <?php echo isset($data)?(in_array($values->id,$list))?"selected":'':(set_value('service_id') == $values->id)?"selected":'';  ?> value=<?php echo $values->id; ?>>
                                                <?php echo $values->services; ?>
                                            </option>
                                            <?php } ?>
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
                                            <option <?php echo (isset($data[0]['service_center_id']) && $data[0]['service_center_id'] == $values['id'])?"selected":(set_value('service_center_id') == $values['id'])?"selected":'';  ?> value=<?php echo $values['id']; ?>>
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
                                        <select type="text" class="form-control"  id="identity_proof" name="identity_proof">
                                            <option disabled selected>Select ID Proof</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Aadhar Card' )?"selected":(set_value('identity_proof') == 'Aadhar Card')?"selected":'';   ?> value="Aadhar Card">Aadhar Card</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Driving License' )?"selected":(set_value('identity_proof') == 'Driving License')?"selected":'';   ?> value="Driving License">Driving License</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Voter ID Card' )?"selected":(set_value('identity_proof') == 'Voter ID Card')?"selected":'';   ?> value="Voter ID Card">Voter ID Card</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'PAN Card' )?"selected":(set_value('identity_proof') == 'PAN Card')?"selected":'';   ?> value="PAN Card">PAN Card</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Ration Card' )?"selected":(set_value('identity_proof') == 'Ration Card')?"selected":'';   ?> value="Ration Card">Ration Card</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Passport' )?"selected":(set_value('identity_proof') == 'Passport')?"selected":'';   ?> value="Passport">Passport</option>
                                            <option <?php echo (isset($data[0]['identity_proof']) && $data[0]['identity_proof'] == 'Others' )?"selected":(set_value('identity_proof') == 'Others')?"selected":'';   ?> value="Others">Others</option>
                                        </select>
                                    </div>
                                    <?php echo form_error('identity_proof'); ?>
                                </div>


                                 <div class="form-group <?php if( form_error('file') ) { echo 'has-error';} ?>">
                                    <label for="Identity Picture" class="col-md-4">ID Photo </label>
                                    <div class="col-md-6" >
                                       <input type="file" class="form-control" name="file" >
                                    </div>
                                    <div class='col-md-2'>
                                        <?php
                                                $src = base_url() . 'images/no_image.png';
                                                if (isset($data[0]['identity_proof_pic']) && !empty($data[0]['identity_proof_pic'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/engineer-id-proofs/" . $data[0]['identity_proof_pic'];
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($data[0]['identity_proof_pic']) && !empty($data[0]['identity_proof_pic'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('identity_proof_pic',<?php echo $data[0]['id']?>)" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 40px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                    </div>

                                    <?php echo form_error('file'); ?>
                                </div>
                                <input type="hidden" name="identity_uploaded" value="<?php if(isset($data[0]['identity_proof_pic'])){ echo $data[0]['identity_proof_pic'];} ?>" />

                            </div>
                            <!-- end -->
                            <div class="col-md-6 <?php if( form_error('identity_id_number') ) { echo 'has-error';} ?>">
                             <div class="form-group">
                                    <label for="Identity ID Number" class="col-md-4">ID Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="identity_id_number" name="identity_id_number" value = "<?php echo isset($data[0]['identity_proof_number'])?$data[0]['identity_proof_number']:set_value('identity_proof_number');  ?>" placeholder="Enter ID Number" >
                                    </div>
                                </div>
                                <?php echo form_error('identity_id_number'); ?>
                            </div>
                            <!-- end -->
                        </div>
                    </div>
                </div>
            </div>
<!--             <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Bank Account Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">

                                <div class="form-group <?php //if( form_error('bank_name') ) { echo 'has-error';} ?>">
                                    <label for="bank name" class="col-md-4">Bank Name </label>
                                    <div class="col-md-6">

                                    <input type="text" class="form-control"  id="bank_name" name="bank_name" value = "<?php //echo isset($data[0]['bank_name'])?$data[0]['bank_name']:set_value('bank_name');  ?>" placeholder="Enter Bank Name" >

                                    </div>
                                    <span id="errmsg1"></span>
                                    <?php //echo form_error('bank_name'); ?>
                                </div>
                                <div class="form-group <?php //if( form_error('bank_ifsc_code') ) { echo 'has-error';} ?>">
                                    <label for="bank_ifsc_code" class="col-md-4">IFSC Code </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_ifsc_code" name="bank_ifsc_code" value = "<?php //echo isset($data[0]['bank_ifsc_code'])?$data[0]['bank_ifsc_code']:set_value('bank_ifsc_code');  ?>" placeholder="Enter IFSC code" >
                                    </div>
                                    <?php //echo form_error('bank_ifsc_code'); ?>
                                </div>

                                 <div class="form-group <?php //if( form_error('bank_proof_pic') ) { echo 'has-error';} ?>">
                                    <label for="Identity Picture" class="col-md-4">Bank Passbook/Cheque Photo</label>
                                    <div class="col-md-6" >
                                       <input type="file" class="form-control" name="bank_proof_pic" >
                                    </div>
                                    <div class='col-md-2'>
                                        <?php
                                               // $src = base_url() . 'images/no_image.png';
                                                //if (isset($data[0]['bank_proof_pic']) && !empty($data[0]['bank_proof_pic'])) {
                                                    //Path to be changed
                                                    //$src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/engineer-bank-proofs/" . $data[0]['bank_proof_pic'];
                                               // }
                                                ?>
                                            <a href="<?php //echo $src?>" target="_blank"><img src="<?php //echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php //if(isset($data[0]['bank_proof_pic']) && !empty($data[0]['bank_proof_pic'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('bank_proof_pic',<?php //echo $data[0]['id']?>)" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 40px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php //}?>
                                    </div>

                                    <?php //echo form_error('bank_proof_pic'); ?>
                                </div>


                            </div>
                             end div 
                            <div class="col-md-6">

                                <div class="form-group <?php //if( form_error('bank_account_no') ) { echo 'has-error';} ?>">
                                    <label for="bank account no" class="col-md-4">Bank Account No. </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_account_no" name="bank_account_no" value = "<?php echo isset($data[0]['bank_ac_no'])?$data[0]['bank_ac_no']:set_value('bank_ac_no');  ?>" placeholder="Enter Account Number" >
                                    </div>
                                     <?php //echo form_error('bank_account_no'); ?>
                                   <span id="bank_account_no1" style="color: red;margin-left: 38%;margin-top:40px;"></span>
                                </div>

                                <div class="form-group <?php //if( form_error('bank_holder_name') ) { echo 'has-error';} ?>">
                                    <label for="bank account no" class="col-md-4">Bank Account Holder Name </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="bank_holder_name" name="bank_holder_name" value = "<?php //echo isset($data[0]['bank_holder_name'])?$data[0]['bank_holder_name']:set_value('bank_holder_name');  ?>" placeholder="Enter Account Holder Name" >
                                    </div>
                                     <?php //echo form_error('bank_holder_name'); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
            <input type="submit" class="btn btn-primary"  onclick="" value="<?php echo isset($data)?'Update Engineer':'Save Engineer'?>" />
            <a href='<?php echo base_url()?>employee/vendor/get_engineers' class='btn btn-primary' >Cancel</a></center>
            </div>

        </form>
    </div>
</div>
<?php if($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('update_error')) {$this->session->unset_userdata('update_error');} ?>
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

//    function onsubmit(){
//
//        var bank_account_no = $('#bank_account_no').val();
//        alert(bank_account_no);
//    }

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
                        minlength: 10,
                        number: true
                    },
//                alternate_phone:{
//
//                    minlength: 10,
//                    number: true
//                    },
                service_center_id:"required",
                //identity_id_number : "required",
                //file : "required",
//                bank_ifsc_code: {
//                        required: true,
//                        rangelength: [11, 11]
//                    },
//                bank_name:{
//                        required: true,
//                        minlength: 3
//                    },
//                bank_holder_name:{
//                        required: true,
//                        minlength: 3
//                    },
//                service_id: "required",
//                bank_account_no: {
//                    digits: true,
//                    required:true,
//                    minlength:5
//
//                    }
                },
                messages: {
                name: "Please Enter Name",
                phone: "Please Enter valid Mobile Number",
                service_center_id: "Please Select Service Center",
//                bank_name: "Please Enter Bank Name",
//                bank_ifsc_code:"Please Enter Correct IFSC Code",
//                bank_holder_name: "Please Enter Account Holder Name",
//                bank_account_no: "Please Enter Account Number",
                service_id:"Please Select Appliances",
                //identity_id_number:"Please Enter Identity Number",
                //file : "Plesae Select ID Photo"
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
    
    function remove_image(type,engineer_id){
            var c  = confirm('Do you want to permanently remove photo?');
            if(c){
             $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/vendor/remove_engineer_image',
                        data: {type: type, id: engineer_id},
                        success: function (data) {
                             location.reload();
                            }
                    });
                 }else{
                    return false;
                 }
        }

</script>

