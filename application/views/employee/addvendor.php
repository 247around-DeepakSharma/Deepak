<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;" >
            <div class="panel-heading" style="font-size:130%;">
                <?php if(isset($query)){?>
                <form action="<?php echo base_url(); ?>employee/upcountry/assign_sc_to_upcountry" method="POST" style="margin-bottom:8px;" target="_blank">
                    <input type="hidden" value="<?php echo $query[0]['id']; ?>" name="service_center_id" />
                     <input type="hidden" value="<?php echo $query[0]['state']; ?>" name="state" />
                     <input type="submit" value="Add Upcountry" class="btn btn-primary btn-md pull-right"/>
                </form>
                <?php }?>
                <center><b>
                    <?php
                        if (isset($selected_brands_list)) {
                            echo "Edit Vendor";
                        } else {
                            echo "Add Vendor";
                        }
                        ?>
                </b>
                    </center>
                <?php  if (isset($selected_brands_list)) { ?>
               
               <?php }?>
            </div>
            <div class="panel-body">
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
                <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor" method="POST" enctype="multipart/form-data">
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Company Information</b></div>
                        <div class="panel-body">
                            <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
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
                                        <label  for="company_name" class="col-md-3">Company Name</label>
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
                                        <label  for="name" class="col-md-3">Display Name</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="name" name="name" value = "<?php
                                                if (isset($query[0]['name'])) {
                                                    echo $query[0]['name'];
                                                }
                                                ?>" placeholder="Public Name">
                                            <?php echo form_error('name'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('rm')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="rm" class="col-md-3">RM</label>
                                        <div class="col-md-8">
                                            <select id="rm" class="form-control" name ="rm">
                                                <option selected disabled>Select Regional Manager</option>
                                                <?php
                                                    foreach ($results['employee_rm'] as $value) {
                                                    ?>
                                                <option value = "<?php echo $value['id'] ?>"
                                                    <?php
                                                        if (isset($rm[0]['agent_id']) && $rm[0]['agent_id'] == $value['id']) { echo "selected";}
                                                        ?>
                                                    >
                                                    <?php echo $value['full_name']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                            <?php echo form_error('rm'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('address')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="address" class="col-md-3 ">Address</label>
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
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group">
                                        <label  for="address" class="col-md-3">Landmark</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control" value = "<?php
                                                if (isset($query[0]['landmark'])) {
                                                    echo $query[0]['landmark'];
                                                }
                                                ?>" name="landmark" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('district')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3">District</label>
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
                                    <div class="form-group <?php
                                        if (form_error('state')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3">State</label>
                                        <div class="col-md-8">
                                            <select class=" form-control" name ="state" id="state" onChange="getDistrict()" placeholder="Select State">
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
                                    <div class="form-group ">
                                        <label for="state" class="col-md-3">Pincode</label>
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
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="phone_1" class="col-md-3">Phone 1</label>
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
                                        <label  for="phone_2" class="col-md-3">Phone 2</label>
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
                                        if (form_error('email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="email" class="col-md-3">Email</label>
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
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('address_proof_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="address_proof_file" class="col-md-4">Address Proof File</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="address_proof_file" >
                                            <?php echo form_error('address_proof_file'); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['address_proof_file']) && !empty($query[0]['address_proof_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['address_proof_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php if(isset($query[0]['address_proof_file']) && !empty($query[0]['address_proof_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('address_proof_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['address_proof_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('company_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="company_type" class="col-md-3">Company Type</label>
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
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('contract_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="contract_file" class="col-md-4">Contract File</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="contract_file" value = "<?php
                                                if (isset($query[0]['contract_file'])) {
                                                    echo $query[0]['contract_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('contract_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['contract_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('contract_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['contract_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>POC Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="primary_contact_name" class="col-md-3">Name</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar"  name="primary_contact_name" value = "<?php
                                                if (isset($query[0]['primary_contact_name'])) {
                                                    echo $query[0]['primary_contact_name'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_email" class="col-md-2">Email</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php
                                                if (isset($query[0]['primary_contact_email'])) {
                                                    echo $query[0]['primary_contact_email'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_phone_1" class="col-md-3">Phone 1</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php
                                                if (isset($query[0]['primary_contact_phone_1'])) {
                                                    echo $query[0]['primary_contact_phone_1'];
                                                }
                                                ?>" >
                                            <?php echo form_error('primary_contact_phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_phone_2" class="col-md-3">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php
                                                if (isset($query[0]['primary_contact_phone_2'])) {
                                                    echo $query[0]['primary_contact_phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Owner Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_name" class="col-md-3">Name</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control blockspacialchar"  name="owner_name" value = "<?php
                                                if (isset($query[0]['owner_name'])) {
                                                    echo $query[0]['owner_name'];
                                                }
                                                ?>" >
                                            <?php echo form_error('owner_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_email" class="col-md-3">Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="owner_email" value = "<?php
                                                if (isset($query[0]['owner_email'])) {
                                                    echo $query[0]['owner_email'];
                                                }
                                                ?>" >
                                            <?php echo form_error('owner_email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="owner_phone_1" class="col-md-3">Phone 1</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php
                                                if (isset($query[0]['owner_phone_1'])) {
                                                    echo $query[0]['owner_phone_1'];
                                                }
                                                ?>">
                                            <?php echo form_error('owner_phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_phone_2" class="col-md-3">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="owner_phone_2" name="owner_phone_2" value = "<?php
                                                if (isset($query[0]['owner_phone_2'])) {
                                                    echo $query[0]['owner_phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('owner_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('id_proof_1_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="id_proof_1_file" class="col-md-3">ID Proof 1</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="id_proof_1_file" value = "<?php
                                                if (isset($query[0]['id_proof_1_file'])) {
                                                    echo $query[0]['id_proof_1_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('id_proof_1_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['id_proof_1_file']) && !empty($query[0]['id_proof_1_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['id_proof_1_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['id_proof_1_file']) && !empty($query[0]['id_proof_1_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('id_proof_1_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['id_proof_1_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('id_proof_2_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="id_proof_2_file" class="col-md-3">ID Proof 2</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="id_proof_2_file" value = "<?php
                                                if (isset($query[0]['id_proof_2_file'])) {
                                                    echo $query[0]['id_proof_2_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('id_proof_2_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['id_proof_2_file']) && !empty($query[0]['id_proof_2_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['id_proof_2_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['id_proof_2_file']) && !empty($query[0]['id_proof_2_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('id_proof_2_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['id_proof_2_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Registration Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-4" style="margin-right:21px;">
                                    <div class="form-group <?php
                                        if (form_error('name_on_pan')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="name_on_pan" class="col-md-4">PAN </label>
                                        <div class="col-md-7">
                                            <input placeholder="Name on PAN CARD" type="text" class="form-control blockspacialchar"  id="name_on_pan" name="name_on_pan" value = "<?php
                                                if (isset($query[0]['name_on_pan'])) {
                                                    echo $query[0]['name_on_pan'];
                                                }
                                                ?>">
                                            <span class="err1"><?php echo form_error('name_on_pan'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-right:14px;">
                                    <div class="form-group  <?php
                                        if (form_error('pan_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <!--                                        <label  for="pan_no" class="col-md-4">PAN No.</label>-->
                                        <input type="text" class="form-control blockspacialchar"  id="pan_no" name="pan_no" placeholder="PAN Number" value = "<?php
                                            if (isset($query[0]['pan_no'])) {
                                                echo $query[0]['pan_no'];
                                            }
                                            ?>" style="width:117%">
                                        <span class="err1"><?php echo form_error('pan_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:40px;">
                                    <div class="form-group">
                                        <!--                                        <label  for="pan_file" class="col-md-4">PAN File :</label>-->
                                        <input type="file" class="form-control blockspacialchar"  id="pan_file" name="pan_file" value = "<?php
                                            if (isset($query[0]['pan_file'])) {
                                                echo $query[0]['pan_file'];
                                            }
                                            ?>">
                                        <?php echo form_error('pan_file'); ?>
                                    </div>
                                </div>
                                <div class="col-md-1" style="margin-left: 20px;">
                                    <?php
                                        $src = base_url() . 'images/no_image.png';
                                        $image_src = $src;
                                        if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) {
                                            //Path to be changed
                                            $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['pan_file'];
                                            $image_src = base_url().'images/view_image.png';
                                        }
                                        ?>
                                    <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                    <?php if(isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])){?>
                                    <a href="javascript:void(0)" onclick="remove_image('pan_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['pan_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                    <?php }?>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b> 
                                        </label>
                                        <input type="checkbox"  value="0" id="is_pan_doc" name ="is_pan_doc" <?php if(isset($query[0]['is_pan_doc'])){ if($query[0]['is_pan_doc'] == 0){ echo "checked" ;}}?> style="margin-left:16px;zoom:1.5"> 
                                    </div>
                                </div>
                                <!--                                <div class="col-md-2" style="margin-left:6px;">
                                    </div>-->
                            </div>
                            <!--                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                    if (form_error('vat_no')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                        <label  for="vat_no" class="col-md-3">VAT No.:</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="vat_no" value = "<?php
                                    if (isset($query[0]['vat_no'])) {
                                        echo $query[0]['vat_no'];
                                    }
                                    ?>">
                                                   <?php echo form_error('vat_no'); ?>
                                </div>
                                </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                    if (form_error('vat_file')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                        <label  for="vat_file" class="col-md-3">VAT File:</label>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control"  name="vat_file" value = "<?php
                                    if (isset($query[0]['vat_file'])) {
                                        echo $query[0]['vat_file'];
                                    }
                                    ?>">
                                                   <?php echo form_error('vat_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                    $src = base_url() . 'images/no_image.png';
                                    if (isset($query[0]['vat_file']) && !empty($query[0]['vat_file'])) {
                                        //Path to be changed
                                        $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['vat_file'];
                                    }
                                    ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['vat_file']) && !empty($query[0]['vat_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('vat_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['vat_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                
                                </div>
                                
                                </div>-->
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('cst_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="cst_no" class="col-md-4">CST No.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control blockspacialchar"  name="cst_no" id="cst_no" value = "<?php
                                                if (isset($query[0]['cst_no'])) {
                                                    echo $query[0]['cst_no'];
                                                }
                                                ?>">
                                            <span class="err1"><?php echo form_error('cst_no'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('cst_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="cst_file" class="col-md-4 blockspacialchar">CST File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  name="cst_file" value = "<?php
                                                if (isset($query[0]['cst_file'])) {
                                                    echo $query[0]['cst_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('cst_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['cst_file']) && !empty($query[0]['cst_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['cst_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['cst_file']) && !empty($query[0]['cst_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('cst_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['cst_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:60px;">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b> 
                                        </label>
                                        <input type="checkbox"  value="0" id="is_cst_doc" name ="is_cst_doc" <?php if(isset($query[0]['is_cst_doc'])){ if($query[0]['is_cst_doc'] == 0){ echo "checked" ;}}?> style="margin-top:5px; margin-left:24px;zoom:1.5;"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('tin_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="tin_no" class="col-md-4">TIN/VAT No.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control blockspacialchar"  id="tin_no" name="tin_no" value = "<?php
                                                if (isset($query[0]['tin_no'])) {
                                                    echo $query[0]['tin_no'];
                                                }
                                                ?>">
                                            <span class="err1"><?php echo form_error('tin_no'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('tin_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="tin_file" class="col-md-4 blockspacialchar">TIN/VAT File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  name="tin_file" value = "<?php
                                                if (isset($query[0]['tin_file'])) {
                                                    echo $query[0]['tin_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('tin_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['tin_file']) && !empty($query[0]['tin_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['tin_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['tin_file']) && !empty($query[0]['tin_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('tin_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['tin_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:60px;">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b>  
                                        </label>
                                        <input type="checkbox"  value="0" id ="is_tin_doc" name ="is_tin_doc" <?php if(isset($query[0]['is_tin_doc'])){ if($query[0]['is_tin_doc'] == 0){ echo "checked" ;}}?> style="margin-top:5px; margin-left:24px;zoom:1.5;"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('service_tax_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="service_tax_no" class="col-md-4">Service Tax No.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control blockspacialchar"  id ="service_tax_no" name="service_tax_no" value = "<?php
                                                if (isset($query[0]['service_tax_no'])) {
                                                    echo $query[0]['service_tax_no'];
                                                }
                                                ?>">
                                            <span class="err1"> <?php echo form_error('service_tax_no'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('service_tax_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="service_tax_no" class="col-md-4 blockspacialchar">Tax File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  name="service_tax_file" value = "<?php
                                                if (isset($query[0]['service_tax_file'])) {
                                                    echo $query[0]['service_tax_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('service_tax_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['service_tax_file']) && !empty($query[0]['service_tax_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['service_tax_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['service_tax_file']) && !empty($query[0]['service_tax_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('service_tax_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['service_tax_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:60px;">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b>   
                                        </label>
                                        <input type="checkbox"  value="0" id="is_st_doc" name ="is_st_doc" <?php if(isset($query[0]['is_st_doc'])){ if($query[0]['is_st_doc'] == 0){ echo "checked" ;}}?> style="    margin-left: 24px;margin-top: 5px;zoom:1.5;">
                                    </div>
                                </div>
                            </div>
                                    
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('service_tax_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="gst_no" class="col-md-4">GST No.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control blockspacialchar"  id ="gst_no" name="gst_no" value = "<?php
                                                if (isset($query[0]['gst_no'])) {
                                                    echo $query[0]['gst_no'];
                                                }
                                                ?>">
                                            <span class="err1"> <?php echo form_error('gst_no'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('gst_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="gst_file" class="col-md-4 blockspacialchar">Tax File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  name="gst_file" value = "<?php
                                                if (isset($query[0]['gst_file'])) {
                                                    echo $query[0]['gst_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('gst_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['gst_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('gst_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['gst_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:60px;">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b>   
                                        </label>
                                        <input type="checkbox"  value="0" id="is_gst_doc" name ="is_gst_doc" <?php if(isset($query[0]['is_gst_doc'])){ if($query[0]['is_gst_doc'] == 0){ echo "checked" ;}}?> style="    margin-left: 24px;margin-top: 5px;zoom:1.5;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Bank Details</b></div>
                        <div class="panel-body" id="bank_details">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div  class="form-group <?php
                                        if (form_error('bank_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="bank_name" class="col-md-4">Bank Name</label>
                                        <div class="col-md-6">
                                            <select class="form-control" id="bank_name" name="bank_name">
                                                <option selected disabled>Select Bank</option>
                                                <?php foreach($results['bank_name'] as $key => $value) { ?> 
                                                <option value="<?php echo $value['bank_name']; ?>" 
                                                        <?php if(isset($query[0]['bank_name']) && $query[0]['bank_name'] === $value['bank_name']){ echo 'selected';}?>> <?php echo $value['bank_name'] ;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('account_type')) {
                                            echo 'account_type';
                                        }
                                        ?>">
                                        <label for="account_type" class="col-md-4">Account Type</label>
                                        <div class="col-md-6">
                                           <!-- <input type="text" class="form-control blockspacialchar"  name="account_type" value = "<?php
                                                //if (isset($query[0]['account_type'])) {
                                                  //  echo $query[0]['account_type'];
                                                //}
                                                ?>">
                                            <?php //echo form_error('account_type'); ?> -->
                                            <select class="form-control" id="account_type" name="account_type">
                                                <option selected disabled>Account Type</option>
                                                <option value="Saving" <?php if(isset($query[0]['account_type']) && $query[0]['account_type'] === 'Saving'){echo 'selected';}?>>Saving</option>
                                                <option value="Current" <?php if(isset($query[0]['account_type']) && $query[0]['account_type'] === 'Current'){echo 'selected';}?>>Current</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('bank_account')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="bank_account" class="col-md-4">Bank Account</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control allownumericwithdecimal"  name="bank_account" value = "<?php
                                                if (isset($query[0]['bank_account'])) {
                                                    echo $query[0]['bank_account'];
                                                }
                                                ?>">
                                            <?php echo form_error('bank_account'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('ifsc_code')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="ifsc_code" class="col-md-4">IFSC Code</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control blockspacialchar"  name="ifsc_code" id="ifsc_code" value = "<?php
                                                if (isset($query[0]['ifsc_code'])) {
                                                    echo $query[0]['ifsc_code'];
                                                }
                                                ?>">
                                            <?php echo form_error('ifsc_code'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('beneficiary_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="beneficiary_name" class="col-md-4">Beneficiary Name</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control blockspacialchar"  name="beneficiary_name" value = "<?php
                                                if (isset($query[0]['beneficiary_name'])) {
                                                    echo $query[0]['beneficiary_name'];
                                                }
                                                ?>">
                                            <?php echo form_error('beneficiary_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('cancelled_cheque_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="cancelled_cheque_file" class="col-md-4">Cancelled Cheque File</label>
                                        <div class="col-md-5">
                                            <input type="file" class="form-control"  name="cancelled_cheque_file" value = "<?php
                                                if (isset($query[0]['cancelled_cheque_file'])) {
                                                    echo $query[0]['cancelled_cheque_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('cancelled_cheque_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['cancelled_cheque_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('cancelled_cheque_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['cancelled_cheque_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                        <?php if($this->session->userdata('user_group')=== 'admin'){ ?>
                                        <label for="is_bank_details_verified" class="col-md-3">Verified/Not Verified </label>
                                        <div class="col-md-3">
                                        <input type="checkbox" value="1" name="is_verified" id="is_bank_details_verified" <?php if(isset($query[0]['is_verified']) && $query[0]['is_verified'] == '1') { ?>checked<?php } ?> style="zoom:1.5;">
                                        <?php }else { ?>
                                        <input type="hidden" name="is_verified" value="<?php if(isset($query[0]['is_verified'])) { echo $query[0]['is_verified']; }?>">
                                        <?php } ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Appliance</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div  class="form-group <?php
                                    if (form_error('appliance')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <?php foreach ($results['services'] as $key => $appliance) { ?>
                                    <label for="Appliance" >
                                    <input type="checkbox" name="appliances[]" value ="<?php echo $appliance->services; ?>"
                                        <?php
                                            if (isset($selected_appliance_list)) {
                                                if (in_array($appliance->services, $selected_appliance_list))
                                                    echo "checked";
                                            }
                                            ?> >
                                    <?php echo $appliance->services; ?> &nbsp;&nbsp;&nbsp;
                                    </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Brands</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <?php foreach ($results['brands'] as $key => $brands) {
                                    ?>
                                <label for="Brand" >
                                <input type="checkbox" name="brands[]" value ="<?php echo $brands->brand_name; ?>"
                                    <?php
                                        if (isset($selected_brands_list)) {
                                            if (in_array($brands->brand_name, $selected_brands_list))
                                                echo "checked";
                                        }
                                        ?>>
                                <?php echo $brands->brand_name; ?> &nbsp;&nbsp;&nbsp;
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading"><b>Non Working Days</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <?php foreach ($days as $key => $day) { ?>
                                <label for="non_working_days" >
                                <input type="checkbox" name="day[]" value ="<?php echo $day; ?>"
                                    <?php
                                        if (isset($selected_non_working_days)) {
                                            if (in_array($day, $selected_non_working_days))
                                                echo "checked";
                                        }
                                        ?> >
                                <?php echo $day; ?> &nbsp;&nbsp;&nbsp;
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div style="float:left;width:90%;" class="form-group <?php
                        if (form_error('non_working_days')) {
                            echo 'has-error';
                        }
                        ?>">
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="col-md-9"></div>
                            <div class="col-md-2">
                                <input id="submit_btn" type="Submit" value="<?php
                                    if (isset($query[0]['id'])) {
                                        echo "Update Vendor";
                                    } else {
                                        echo "Save Vendor";
                                    }
                                    ?>" class="btn btn-primary" >
                            </div>
                        </div>
                        <div class="col-md-6"><?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->session->unset_userdata('checkbox')?>
<script type="text/javascript">
    /*$(".js-example-placeholder-single").select2({
      placeholder: "Select a state",
      allowClear: true
    }); */
    
    //Adding select 2 in Dropdowns
    $("#district_option").select2();
    $("#state").select2();
    $("#pincode").select2();
    $("#rm").select2();
    $("#bank_name").select2();
    
    
    function getDistrict() {
     var state = $("#state").val();
     var district = $(".district").val();
    // alert(district);
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
                function getPincode() {
      var district = $(".district").val();
      var pincode = $(".pincode").val();
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/vendor/getPincode/1',
        data: {pincode: pincode, district: district},
        success: function (data) {
          //console.log(data);
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
        
        //Function to vlaidate registration numbers entered
        function validate_registration_no(){
            //Check for PAN
            if($('#is_pan_doc').is(":checked")){
               if($('#pan_no').val() != '' && $('#name_on_pan').val() != ''){
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
               else if($('#pan_no').val().length != '10' && $('#name_on_pan').val() != ''){
                   alert('Please add valid 10 digit pan number');
                   return false;
               }
               //checking case when pan number 10 and pan name is enterd but panfile is not uploaded
               <?php if(empty($query[0]['pan_file'])){ ?>
                           else if($('#pan_no').val().length == '10' && $('#name_on_pan').val() != '' && !$('#pan_file').val()!= ''){
                   alert('Please upload pan file also');
                   return false;
               } <?php }?>
            }
            //Check for CST
            if($('#is_cst_doc').is(":checked")){
               if($('#cst_no').val() != ''){
                   alert('Please Enter CST Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#cst_no').val() == ''){
                   alert('Please Enter CST Number or Tick "Not Available" checkbox');
                   return false;
               }
               else if($('#cst_no').val().length < '6'){
                   alert('Please Enter Valid CST Number');
                   return false;
               }
            }
            
            //Check for TIN
            if($('#is_tin_doc').is(":checked")){
               if($('#tin_no').val() != ''){
                   alert('Please Enter TIN Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#tin_no').val() == ''){
                   alert('Please Enter TIN Number or Tick "Not Available" checkbox');
                   return false;
               }
               else if($('#tin_no').val().length < '6'){
                   alert('Please Enter Valid TIN Number');
                   return false;
               }
            }
            
            //Check for Service Tax no.
            if($('#is_st_doc').is(":checked")){
               if($('#service_tax_no').val() != ''){
                   alert('Please Enter Service Tax Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#service_tax_no').val() == ''){
                   alert('Please Enter Service Tax Number or Tick "Not Available" checkbox');
                   return false;
               }
               else if($('#service_tax_no').val().length < '6'){
                   alert('Please Enter Valid Service Tax Number');
                   return false;
               }
            }
             
            //Check for GST  no.
            if($('#is_gst_doc').is(":checked")){
               if($('#gst_no').val() != ''){
                   alert('Please Enter GST Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#gst_no').val() == ''){
                   alert('Please Enter GST Number or Tick "Not Available" checkbox');
                   return false;
               }
               else if($('#gst_no').val().length === '15'){
                   alert('Please Enter Valid GST Number');
                   return false;
               }
            }
          
        }
        
</script>
<style type="text/css">
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
</style>
<script type="text/javascript">
    $.validator.addMethod("regx", function (value, element, regexpr) {
        return regexpr.test(value);
    }, "Please enter a valid Phone Number.");
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
        state: "required",
        primary_contact_name: "required",
        owner_name: "required",
        email: {
            email: true
        },
        primary_contact_email: {
            required: true,
            email: true
        },
        owner_email: {
            required: true,
            email: true
        }
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
        primary_contact_phone_1: "Please fill correct phone number",
        primary_contact_phone_2: "Please fill correct phone number",
        owner_phone_1: "Please fill correct phone number",
        owner_phone_2: "Please fill correct phone number",
        primary_contact_name: "Please fill Name",
        owner_name: "Please fill Name",
        email: "Please fill correct email",
        primary_contact_email: "Please fill correct email",
        owner_email: "Please fill correct email"
    },
        submitHandler: function (form) {
            //Checking registration number validation
        var check = validate_registration_no();
        if(check === false){
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
<script>
    <?php if((isset($query[0]['is_verified']) && !empty($query[0]['is_verified'])) && $this->session->userdata('user_group') !='admin'){?>
        $('#bank_details').find('input').attr('readonly', true);
    <?php } ?>
        
   $('#pan_no').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });  
    
    $(".allownumericwithdecimal").on("keypress blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
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