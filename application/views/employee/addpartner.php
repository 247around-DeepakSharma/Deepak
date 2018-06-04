<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    #booking_form .form-group label.error {
    margin:4px 0 5px !important;
    width:auto !important;
    }
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
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    .select2-container{
    width:100% !important;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="clear"></div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <b><?php if (isset($query[0]['id'])) {
                    echo "EDIT PARTNER";
                    } else {
                    echo "ADD PARTNER";
                    } ?></b>
            </div>
            <div id="tabs" style="border:0px solid #fff;float:left;">
                <div class="col-md-12" style="">
                    <ul>
                        <?php
                            if (!isset($query[0]['id'])) {
                            ?>
                        <li style="background:#fff"><a id="1" href="#tabs-1"><span class="panel-title">Basic Details</span></a></li>
                        <li><a id="2" href="#tabs-2" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Documents</span></a></li>
                        <li><a id="3" href="#tabs-3" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Operation Region</span></a></li>
                        <li><a id="4" href="#tabs-4" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Contracts</span></a></li>
                        <li><a id="5" href="#tabs-5" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Brand Mapping</span></a></li>
                        <li><a id="6" href="#tabs-6" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Brand Collateral</span></a></li>
                        <li><a id="7" href="#tabs-7" ><span class="panel-title" onclick="alert('Please Add Basic Details FIrst')">Brand Collateral</span></a></li>
                        <?php
                            }
                            else{
                            ?>
                        <li style="background:#fff"><a id="1" href="#tabs-1" onclick="load_form(this.id)"><span class="panel-title">Basic Details</span></a></li>
                        <li><a id="2" href="#tabs-2"  onclick="load_form(this.id)"><span class="panel-title">Documents</span></a></li>
                        <li><a id="3" href="#tabs-3" onclick="load_form(this.id)"><span class="panel-title">Operation Region</span></a></li>
                        <li><a id="4" href="#tabs-4" onclick="load_form(this.id)"><span class="panel-title">Contracts</span></a></li>
                        <li><a id="5" href="#tabs-5" onclick="load_form(this.id)"><span class="panel-title">Brand Mapping</span></a></li>
                        <li><a id="6" href="#tabs-6" onclick="load_form(this.id)"><span class="panel-title">Brand Collateral</span></a></li>
                        <li><a id="7" href="#tabs-7" onclick="load_form(this.id)"><span class="panel-title">Upload Serial No</span></a></li>
                        <?php
                            }
                            ?>
                    </ul>
                </div>
            </div>
            <div class="pull-right" style="margin-top:10px;">
                <div class="row">
                    <div class="col-md-3" style="width: 22%;">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/partner/viewpartner" style="margin-right:5px;">View Partners</a>
                    </div>
                    <?php if (isset($query[0]['id'])) { ?>
                    <div class="col-md-3" style="width: 37.5%;">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/partner/upload_partner_brand_logo/<?php echo $query[0]['id'] ?>/<?php echo $query[0]['public_name'] ?>" style="margin-right:5px;">Upload Partner Brand Logo</a>
                    </div>
                    <div class="col-md-3" style="width: 22%;">
                        <a href="<?php echo base_url() ?>employee/partner/get_partner_login_details_form/<?php echo $query[0]['id'] ?>" class="btn btn-primary"><b>MANAGE LOGIN</b></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <br>
            <div class="clear"></div>
            <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
            <div id="container_1" class="form_container">
                <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/partner/process_add_edit_partner_form" method="POST" enctype="multipart/form-data">
                    <div>
                        <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php if (isset($query[0]['id'])) {
                            echo $query[0]['id'];
                            } ?>"
                            >
                        <?php echo form_error('id'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Company Information</b></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div  class="form-group <?php if (form_error('company_name')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label  for="company_name" class="col-md-4">Company Name *</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="company_name" name="company_name" value = "<?php if (isset($query[0]['company_name'])) {
                                                echo $query[0]['company_name'];
                                                } ?>" >
                                            <?php echo form_error('company_name'); ?>
                                        </div>
                                    </div>
                                    <div  class="form-group <?php if (form_error('public_name')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label  for="public_name" class="col-md-4">Public Name *</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="public_name" name="public_name" value = "<?php if (isset($query[0]['public_name'])) {
                                                echo $query[0]['public_name'];
                                                } ?>" >
                                            <?php echo form_error('public_name'); ?>
                                        </div>
                                    </div>
                                    <div  class="form-group <?php if (form_error('address')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label  for="address" class="col-md-4">Address *</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="address" value = "<?php if (isset($query[0]['address'])) {
                                                echo $query[0]['address'];
                                                } ?>" >
                                            <?php echo form_error('address'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  for="address" class="col-md-4">Landmark </label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control" value = "<?php if (isset($query[0]['landmark'])) {
                                                echo $query[0]['landmark'];
                                                } ?>" name="landmark" >
                                        </div>
                                    </div>
                                    <div  class="form-group <?php if (form_error('partner_type')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label  for="partner_type" class="col-md-4">Type *</label>
                                        <div class="col-md-8">
                                            <select name="partner_type" class="form-control" >
                                                <option selected disabled>Select Partner Type</option>
                                                <option value=<?php echo BUYBACKTYPE ?> 
                                                    <?php if (isset($results['partner_code'][0]['partner_type'])) {
                                                        if ($results['partner_code'][0]['partner_type'] == BUYBACKTYPE) {
                                                            echo "selected";
                                                        }
                                                        } ?> ><?php echo BUYBACKTYPE ?></option>
                                                <option value=<?php echo ECOMMERCETYPE ?> 
                                                    <?php if (isset($results['partner_code'][0]['partner_type'])) {
                                                        if ($results['partner_code'][0]['partner_type'] == ECOMMERCETYPE) {
                                                            echo "selected";
                                                        }
                                                        } ?> ><?php echo ECOMMERCETYPE ?></option>
                                                <option value=<?php echo EXTWARRANTYPROVIDERTYPE ?> 
                                                    <?php if (isset($results['partner_code'][0]['partner_type'])) {
                                                        if ($results['partner_code'][0]['partner_type'] == EXTWARRANTYPROVIDERTYPE) {
                                                            echo "selected";
                                                        }
                                                        } ?> ><?php echo EXTWARRANTYPROVIDERTYPE ?></option>
                                                <option value=<?php echo INTERNALTYPE ?> 
                                                    <?php if (isset($results['partner_code'][0]['partner_type'])) {
                                                        if ($results['partner_code'][0]['partner_type'] == INTERNALTYPE) {
                                                            echo "selected";
                                                        }
                                                        } ?> ><?php echo INTERNALTYPE ?></option>
                                                <option value=<?php echo OEM ?> 
                                                    <?php if (isset($results['partner_code'][0]['partner_type'])) {
                                                        if ($results['partner_code'][0]['partner_type'] == OEM) {
                                                            echo "selected";
                                                        }
                                                        } ?> ><?php echo OEM ?></option>
                                            </select>
                                            <?php echo form_error('partner_type'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php
                                        if (form_error('company_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="company_type" class="col-md-4">Company Type</label>
                                        <div class="col-md-8">
                                            <select name="company_type" class="form-control">
                                                <option disabled selected >Select Company Type</option>
                                                <option value="Individual" <?php
                                                    if (isset($query[0]['company_type'])) {
                                                        if ($query[0]['company_type'] == "Individual") {
                                                            echo "Selected";
                                                        }
                                                    }
                                                    ?>>Individual</option>
                                                <option value="Proprietorship Firm" <?php
                                                    if (isset($query[0]['company_type'])) {
                                                        if ($query[0]['company_type'] == "Proprietorship Firm") {
                                                            echo "Selected";
                                                        }
                                                    }
                                                    ?>>Proprietorship Firm</option>
                                                <option value="Partnership Firm" <?php
                                                    if (isset($query[0]['company_type'])) {
                                                        if ($query[0]['company_type'] == "Partnership Firm") {
                                                            echo "Selected";
                                                        }
                                                    }
                                                    ?>>Partnership Firm</option>
                                                <option value="Private Ltd Company" <?php
                                                    if (isset($query[0]['company_type'])) {
                                                        if ($query[0]['company_type'] == "Private Ltd Company") {
                                                            echo "Selected";
                                                        }
                                                    }
                                                    ?>>Private Ltd Company</option>
                                            </select>
                                            <?php echo form_error('company_type'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="margin-bottom: 31px;" class="form-group <?php if (form_error('state')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="state" class="col-md-4">State *</label>
                                        <div class="col-md-8">
                                            <select class=" form-control" name ="state" id="state" onChange="getDistrict()" >
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
                                    <div style="margin-bottom: 31px;" class="form-group <?php if (form_error('district')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="state" class="col-md-4">District *</label>
                                        <div class="col-md-8">
                                            <select class="district form-control" name ="district" id="district" onChange="getPincode()">
                                                <option <?php if (isset($query[0]['district'])) {
                                                    echo "selected";
                                                    } ?>><?php if (isset($query[0]['district'])) {
                                                    echo $query[0]['district'];
                                                    } ?></option>
                                            </select>
                                            <?php echo form_error('district'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom: 31px;">
                                        <label for="state" class="col-md-4">Pincode</label>
                                        <div class="col-md-8">
                                            <select class="pincode form-control" name ="pincode"  id="pincode">
                                                <option <?php if (isset($query[0]['pincode'])) {
                                                    echo "selected";
                                                    } ?>><?php if (isset($query[0]['pincode'])) {
                                                    echo $query[0]['pincode'];
                                                    } ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom: 31px;">
                                        <label for="partner_code" class="col-md-4">Partner Code</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name ="partner_code"  id="partner_code">
                                                <option value=""  selected="">Select Partner Code</option>
                                                <?php
                                                    //Checking for Edit Parnter
                                                    if (isset($query[0]['id'])) {
                                                        foreach (range('A', 'Z') as $char) {
                                                            $code = "R" . $char;
                                                            if (!in_array($code, $results['partner_code_availiable']) || isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code)) {
                                                                ?>
                                                <option value="<?php echo $code; ?>" <?php
                                                    if (isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code )) {
                                                        echo "selected=''";
                                                    }
                                                    ?>><?php echo $code; ?></option>
                                                <?php
                                                    }
                                                    }
                                                    foreach (range('A', 'Z') as $char) {
                                                    $code = "S" . $char;
                                                    if (!in_array($code, $results['partner_code_availiable']) || isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code)) {
                                                        ?>
                                                <option value="<?php echo $code; ?>" <?php
                                                    if (isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code )) {
                                                        echo "selected=''";
                                                    }
                                                    ?>><?php echo $code; ?></option>
                                                <?php
                                                    }
                                                    }
                                                    
                                                    foreach (range('A', 'Z') as $char) {
                                                    $code = "P" . $char;
                                                    if (!in_array($code, $results['partner_code_availiable']) || isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code)) {
                                                        ?>
                                                <option value="<?php echo $code; ?>" <?php
                                                    if (isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code )) {
                                                        echo "selected=''";
                                                    }
                                                    ?>><?php echo $code; ?></option>
                                                <?php
                                                    }
                                                    }
                                                    
                                                    
                                                    
                                                    } else {// New Partner Addition
                                                    foreach (range('A', 'Z') as $char) {
                                                        $code = "R" . $char;
                                                        if (!in_array($code, $results['partner_code'])) {
                                                            ?>
                                                            <option value="<?php echo $code; ?>" ><?php echo $code; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                    }
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 31px;" class="form-group <?php if (form_error('account_manager_id')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label  for="account_manager" class="col-md-4">Account Managers </label>
                                        <div class="col-md-8">
                                            <select name="account_manager_id" class="form-control" id="account_manager">
                                                <option selected disabled>Select Account Managers</option>
                                                <?php foreach($employee_list as $employee){ ?>
                                                <option value="<?php echo $employee['id']; ?>" <?php if(isset($query[0]['account_manager_id']) && ($query[0]['account_manager_id'] === $employee['id'] )){ echo "selected";}?>><?php echo $employee['full_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php echo form_error('account_manager_id'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>POC Details</b></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="form-group <?php if( form_error('primary_contact_name') ) { echo 'has-error';} ?>">
                                        <label  for="primary_contact_name" class="col-md-4 blockspacialchar">Primary Contact Name</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="primary_contact_name" value = "<?php if (isset($query[0]['primary_contact_name'])){echo $query[0]['primary_contact_name'];}?>">
                                            <?php echo form_error('primary_contact_name'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('primary_contact_email') ) { echo 'has-error';} ?>">
                                        <label for="primary_contact_email" class="col-md-4">Primary Contact Email</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php if (isset($query[0]['primary_contact_email'])){echo $query[0]['primary_contact_email'];}?>">
                                            <?php echo form_error('primary_contact_email'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('customer_care_contact') ) { echo 'has-error';} ?>">
                                        <label for="customer_care_contact" class="col-md-4">Customer Care Number</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="customer_care_contact" name="customer_care_contact" value = "<?php if (isset($query[0]['customer_care_contact'])){echo $query[0]['customer_care_contact'];}?>">
                                            <?php echo form_error('customer_care_contact'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php if( form_error('primary_contact_phone_1') ) { echo 'has-error';} ?>">
                                        <label for="primary_contact_phone_1" class="col-md-4">Primary Contact Ph.No. 1</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php if (isset($query[0]['primary_contact_phone_1'])){echo $query[0]['primary_contact_phone_1'];}?>" >
                                            <?php echo form_error('primary_contact_phone_1'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('primary_contact_phone_2') ) { echo 'has-error';} ?>">
                                        <label for="primary_contact_phone_2" class="col-md-4">Primary Contact Ph.No. 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control verigymobileNumber" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php if (isset($query[0]['primary_contact_phone_2'])){echo $query[0]['primary_contact_phone_2'];}?>">
                                            <?php echo form_error('primary_contact_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>UpCountry Details</b></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label  for="upcountry_rate" class="col-md-4">UpCountry Rate</label>
                                        <div class="col-md-1">
                                            <input type="checkbox" name="is_upcountry" id="upcountry" style="zoom:1.5" 
                                                <?php if (isset($query[0])) {
                                                    if ($query[0]['is_upcountry'] == 1) {
                                                        echo "checked";
                                                    }
                                                    } ?>/>
                                        </div>
                                        <div class="col-md-3">
                                            <input  type="number" class="form-control up_message"  id="up_rate" value = "<?php if (isset($query[0])) {
                                                echo $query[0]['upcountry_rate'];
                                                } ?>" name="upcountry_rate" id="upcountry_rate" placeholder="Enter KM's">
                                        </div>
                                        <div class="col-md-4">
                                            <span><i>[Enter Rate per KM]</i></span>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="upcountry_max_distance_threshold" class="col-md-4">Auto Approval Upcountry Distance(One Ways)</label>
                                        <div class="col-md-8">
                                            <input  type="number" id="up_threshold" class="form-control up_message"  name="upcountry_max_distance_threshold" value = "<?php if (isset($query[0])) {
                                                echo $query[0]['upcountry_max_distance_threshold'];
                                                } ?>">
                                            <p id="up_th_msg" style="font-weight:bold;"></p>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label  for="upcountry_approval" class="col-md-4">Upcountry Approval</label>
                                        <div class="col-md-1">
                                            <input type="checkbox" name="upcountry_approval" id="upcountry_approval" style="zoom:1.5" 
                                                <?php if (isset($query[0])) {
                                                    if ($query[0]['upcountry_approval'] == 1) {
                                                        echo "checked";
                                                    }
                                                    } ?> />
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="upcountry_approval_email" class="col-md-4">Upcountry Approval Email</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="upcountry_approval_email" value = "<?php if (isset($query[0])) {
                                                echo $query[0]['upcountry_approval_email'];
                                                } ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Spare Parts</b></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="is required Def parts" class="col-md-4">Is Spare Parts Required</label>
                                        <div class="col-md-1">
                                            <input  type="checkbox" class="form-control"  name="is_def_spare_required" value = "1" <?php if (isset($query[0])) {
                                                if($query[0]['is_def_spare_required'] == '1'){ echo "checked"; }
                                                } ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php if (form_error('spare_notification_email')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="spare_notification_email" class="col-md-4">Spare Notification Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="spare_notification_email" value = "<?php if (isset($query[0]['spare_notification_email'])) {
                                                echo $query[0]['spare_notification_email'];
                                                } ?>" >
                                            <?php echo form_error('spare_notification_email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Owner Details</b></div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div class="form-group <?php if (form_error('owner_name')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="owner_name" class="col-md-4">Owner Name</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control blockspacialchar"  name="owner_name" value = "<?php if (isset($query[0]['owner_name'])) {
                                                echo $query[0]['owner_name'];
                                                } ?>" >
                                            <?php echo form_error('owner_name'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if (form_error('owner_email')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="owner_email" class="col-md-4">Owner Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="owner_email" value = "<?php if (isset($query[0]['owner_email'])) {
                                                echo $query[0]['owner_email'];
                                                } ?>" >
                                            <?php echo form_error('owner_email'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if (form_error('owner_alternate_email')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="owner_alternate_email" class="col-md-4">Owner Alternate Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="owner_alternate_email" value = "<?php if (isset($query[0]['owner_alternate_email'])) {
                                                echo $query[0]['owner_alternate_email'];
                                                } ?>" >
                                            <?php echo form_error('owner_alternate_email'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php if (form_error('owner_phone_1')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="owner_phone_1" class="col-md-4">Owner Ph. No. 1</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php if (isset($query[0]['owner_phone_1'])) {
                                                echo $query[0]['owner_phone_1'];
                                                } ?>">
                                            <?php echo form_error('owner_phone_1'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if (form_error('owner_phone_2')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="owner_phone_2" class="col-md-4">Owner Ph. No. 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control verigymobileNumber" id="owner_phone_2" name="owner_phone_2" value = "<?php if (isset($query[0]['owner_phone_2'])) {
                                                echo $query[0]['owner_phone_2'];
                                                } ?>">
                                            <?php echo form_error('owner_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Summary Email</b></div>
                            <div class="panel-body">
                                <div class="col-md-4 form-group <?php if (form_error('summary_email_to')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label  for="summary_email_to" class="col-md-4">To</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="summary_email_to" value = "<?php if (isset($query[0]['summary_email_to'])) {
                                            echo $query[0]['summary_email_to'];
                                            } ?>">
                                        <?php echo form_error('summary_email_to'); ?>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group <?php if (form_error('summary_email_cc')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="summary_email_cc" class="col-md-4">cc</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="summary_email_cc" value = "<?php if (isset($query[0]['summary_email_cc'])) {
                                        echo $query[0]['summary_email_cc'];} else { echo "anuj@247around.com,nits@247around.com";} ?>">
                                        <?php echo form_error('summary_email_cc'); ?>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label  for="is_reporting_mail" class="col-md-4">Reporting Mail</label>
                                    <div class="col-md-4">
                                        <input type="checkbox" name="is_reporting_mail" id="is_reporting_mail" style="zoom:1.5" 
                                            <?php if (isset($query[0])) {
                                                if ($query[0]['is_reporting_mail'] == 1) {
                                                    echo "checked";
                                                }
                                                } ?> />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Invoice Details</b></div>
                            <div class="panel-body">
                                <div class="col-md-6 form-group <?php if (form_error('invoice_email_to')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="invoice_email_to" class="col-md-4">To</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="invoice_email_to" value = "<?php if (isset($query[0]['invoice_email_to'])) {
                                            echo $query[0]['invoice_email_to'];
                                            } ?>">
                                        <?php echo form_error('invoice_email_to'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('invoice_email_cc')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="invoice_email_cc" class="col-md-4">cc</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="invoice_email_cc" value = "<?php if (isset($query[0]['invoice_email_cc'])) {
                                            echo $query[0]['invoice_email_cc'];}  else { echo "anuj@247around.com,nits@247around.com,adityag@247around.com"; }
                                            ?>">
                                        <?php echo form_error('invoice_email_cc'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('invoice_courier_name')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="invoice_courier_name" class="col-md-4">Invoice Courier Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="invoice_courier_name" value = "<?php if (isset($query[0]['invoice_courier_name'])) {
                                            echo $query[0]['invoice_courier_name'];
                                            } ?>">
                                        <?php echo form_error('invoice_courier_name'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('invoice_courier_address')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="invoice_courier_address" class="col-md-4">Invoice Courier Address</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control"  name="invoice_courier_address"><?php if (isset($query[0]['invoice_courier_address'])) {
                                            echo $query[0]['invoice_courier_address'];
                                            } ?></textarea>
                                        <?php echo form_error('invoice_courier_address'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('invoice_courier_phone_number')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="invoice_courier_phone_number" class="col-md-4">Invoice Courier Phone Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="invoice_courier_phone_number" value = "<?php if (isset($query[0]['invoice_courier_phone_number'])) {
                                            echo $query[0]['invoice_courier_phone_number'];
                                            } ?>">
                                        <?php echo form_error('invoice_courier_phone_number'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading"><b>Prepaid Account Details</b></div>
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="is prepaid" class="col-md-4">Is Prepaid Account</label>
                                            <div class="col-md-1">
                                                <input  type="checkbox" class="form-control"  name="is_prepaid" value = "1" <?php if (isset($query[0])) {
                                                    if($query[0]['is_prepaid'] == '1'){ echo "checked"; }
                                                    } ?> >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group <?php if (form_error('prepaid_amount_limit')) {
                                            echo 'has-error';
                                            } ?>">
                                            <label for="prepaid_amount_limit" class="col-md-4">Prepaid Minimum Amt Limit</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control"  name="prepaid_amount_limit" value = "<?php if (isset($query[0]['prepaid_amount_limit'])) {
                                                    echo $query[0]['prepaid_amount_limit'];
                                                    } ?>" >
                                                <?php echo form_error('prepaid_amount_limit'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group <?php if (form_error('prepaid_notification_amount')) {
                                            echo 'has-error';
                                            } ?>">
                                            <label for="prepaid_notification_amount" class="col-md-4">Notification Amt Limit</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control"  name="prepaid_notification_amount" value = "<?php if (isset($query[0]['prepaid_notification_amount'])) {
                                                    echo $query[0]['prepaid_notification_amount'];
                                                    } ?>" >
                                                <?php echo form_error('prepaid_notification_amount'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group <?php if (form_error('grace_period_date')) {
                                            echo 'has-error';
                                            } ?>">
                                            <label for="grace_period_date" class="col-md-4">Grace Period Date </label>
                                            <div class="col-md-8">
                                                <input type="text" id="grace_period_date" placeholder="Select Date When Partner De-Activate" class="form-control"  name="grace_period_date" value = "<?php if (isset($query[0]['grace_period_date'])) {
                                                    echo $query[0]['grace_period_date'];
                                                    } ?>" >
                                                <?php echo form_error('grace_period_date'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Partner";
                            } else {
                            echo "Save Partner";
                            } ?>" class="btn btn-primary" id="submit_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
            </div>
            <div class="clear"></div>
            <div id="container_2" style="display:none" class="form_container">
                <form name="document_form" class="form-horizontal" id ="document_form" action="<?php echo base_url() ?>employee/partner/process_partner_document_form" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Registration Details</b></div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('pan')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="pan" class="col-md-3">PAN No.</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" class="form-control blockspacialchar"  name="pan" id="pan_no" value = "<?php if (isset($query[0]['pan'])) {
                                                echo $query[0]['pan'];
                                                } ?>" placeholder="PAN Number">
                                        </div>
                                        <div class="col-md-4">  
                                            <input type="file" class="form-control"  name="pan_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['pan_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src; ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['pan_file'] ?>', 'pan_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('registration_no')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="registration_no" class="col-md-3">Company Registration No(CIN).</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" class="form-control blockspacialchar"  name="registration_no" id="registration_no" value = "<?php if (isset($query[0]['registration_no'])) {
                                                echo $query[0]['registration_no'];
                                                } ?>" placeholder="Company Registration No">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="file" class="form-control"  name="registration_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['registration_file']) && !empty($query[0]['registration_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['registration_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['registration_file']) && !empty($query[0]['registration_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['registration_file'] ?>', 'registration_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('tin')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="tin" class="col-md-3">TIN</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" class="form-control blockspacialchar"  name="tin" id="tin_no" value = "<?php if (isset($query[0]['tin'])) {
                                                echo $query[0]['tin'];
                                                } ?>" placeholder="TIN No">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="file" class="form-control"  name="tin_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['tin_file']) && !empty($query[0]['tin_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['tin_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['tin_file']) && !empty($query[0]['tin_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['tin_file'] ?>', 'tin_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('cst_no')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="cst_no" class="col-md-3">CST</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" class="form-control blockspacialchar"  name="cst_no" id="cst_no" value = "<?php if (isset($query[0]['cst_no'])) {
                                                echo $query[0]['cst_no'];
                                                } ?>" placeholder="CST No">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="file" class="form-control"  name="cst_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['registration_file']) && !empty($query[0]['cst_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['cst_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['cst_file']) && !empty($query[0]['cst_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['cst_file'] ?>', 'cst_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('service_tax')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="service_tax" class="col-md-3">Service Tax No</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" class="form-control blockspacialchar"  name="service_tax" id="service_tax_no" value = "<?php if (isset($query[0]['registration_no'])) {
                                                echo $query[0]['service_tax'];
                                                } ?>" placeholder="service tax no">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="file" class="form-control"  name="service_tax_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['service_tax_file']) && !empty($query[0]['service_tax_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['service_tax_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['service_tax_file']) && !empty($query[0]['service_tax_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['service_tax_file'] ?>', 'service_tax_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group <?php if (form_error('gst_number')) {
                                        echo 'has-error';
                                        } ?>">
                                        <label for="service_tax" class="col-md-3">GST Number</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" style="text-transform:uppercase" class="form-control blockspacialchar"  name="gst_number" id="gst_number" value = "<?php if (isset($query[0]['gst_number'])) {
                                                echo $query[0]['gst_number'];
                                                } ?>" placeholder="GST Number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Documents";
                            } else {
                            echo "Save Documents";
                            } ?>" class="btn btn-primary" id="submit_document_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
                <?php
                    if(!empty($results['partner_documents'])){
                        ?>
                <div id="exist_documents">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th>Document</th>
                                <th>is valid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($results['partner_documents'] as $value){
                                ?>
                            <tr>
                                <td><?php echo $value['document_name'] ?></td>
                                <td><a target="_blank" href=<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$value['file']?>><?php echo $value['file'] ?></a></td>
                                <td><?php echo $value['is_valid'] ?></td>
                            </tr>
                            <tr>
                                <?php
                                    }
                                    }
                                    ?>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
            <div id="container_3" style="display:none;" class="form_container">
                <form name="myForm" class="form-horizontal" id ="operation_region_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/partner/process_partner_operation_region_form" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Partner Operation Region</b></div>
                            <div class="panel-body">
                                <?php
                                    foreach ($results['services'] as $value) {
                                        //Checking Operation regions if Present for User Edit
                                        $operation_region_state = [];
                                        if (!empty($results['partner_operation_region'])) {
                                            foreach ($results['partner_operation_region'] as $val) {
                                                if ($val['service_id'] == $value->id) {
                                                    $operation_region_state[] = $val['state'];
                                                }
                                            }
                                        }
                                        ?>
                                <div class="col-md-12 form-group">
                                    <div class="col-md-3" style="padding: 5px 2px;"><?php echo $value->services ?></div>
                                    <select name ="select_state[<?php echo $value->id ?>][]" class=" col-md-4 select_state" multiple="multiple">
                                        <option value="all">ALL</option>
                                        <?php foreach ($results['select_state'] as $val) { ?>
                                        <option value="<?php echo $val['state'] ?>" <?php echo (isset($operation_region_state) && in_array($val['state'], $operation_region_state)) ? 'selected="selected"' : '' ?> ><?php echo $val['state'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Operation Regions";
                            } else {
                            echo "Save Operation Regions";
                            } ?>" class="btn btn-primary" id="submit_document_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
            </div>
            <div class="clear"></div>
            <div id="container_4" style="display:none" class="form_container">
                <form name="document_form" class="form-horizontal" id ="document_form" action="<?php echo base_url() ?>employee/partner/process_partner_contracts" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <p><b>Contracts</b></p>
                                <button type="button" class="btn btn-success" style="float:right;margin-top: -33px;" id="add_more_1" onclick="add_more_fields(this.id)">Add More Contracts</button>
                            </div>
                            <div class="panel-body contract_holder" id="contract_holder_1">
                                <div class="col-md-6 form-group <?php if (form_error('agreement_start_date')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="agreement_start_date" class="col-md-4">Partnership Start Date</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date" >
                                            <?php
                                                if (isset($query[0]['agreement_start_date'])) {
                                                    if ($query[0]['agreement_start_date'] != "0000-00-00") {
                                                        $aggrement_date = $query[0]['agreement_start_date'];
                                                    } else {
                                                        $aggrement_date = date("Y-m-d", strtotime($query[0]['create_date']));
                                                    }
                                                } else {
                                                    $aggrement_date = date("Y-m-d");
                                                }
                                                ?>
                                            <input type="date" class="form-control agreement_start_date"  name="agreement_start_date[]"  id="agreement_start_date" >
                                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <?php echo form_error('agreement_start_date'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('agreement_end_date')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="agreement_end_date" class="col-md-4">Partnership End Date</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date" >
                                            <input type="date" class="form-control"  name="agreement_end_date[]" id="agreement_end_date">
                                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <?php echo form_error('agreement_end_date'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('contract_type')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="contract_type" class="col-md-4">Contract Type</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="contract_type[]" class="contract_type" required="">
                                            <option value="">Select Contract Type</option>
                                            <?php
                                                foreach($results['collateral_type'] as $collateral){
                                                    ?>
                                            <option value="<?php echo $collateral['id']?>"><?php echo $collateral['collateral_type'] ?></option>
                                            <?php
                                                }
                                                ?>
                                        </select>
                                        <?php echo form_error('contract_type'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group <?php if (form_error('contract_file')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="contract_file" class="col-md-4">Contract File</label>
                                    <div class="col-md-5">
                                        <input type="file" class="form-control"  name="contract_file[]" required="">
                                        <?php echo form_error('contract_file'); ?>
                                    </div>
                                    <div class="col-md-1">
                                        <?php
                                            $src = base_url() . 'images/no_image.png';
                                            $image_src = $src;
                                            if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                                                $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['contract_file'];
                                                $image_src = base_url() . 'images/view_image.png';
                                            }
                                            ?>
                                        <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src; ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                        <?php if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) { ?>
                                        <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['contract_file'] ?>', 'contract_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="contract file" class="col-md-4">Contract Description</label>
                                    <div class="col-md-6">
                                       <div class="form-group" style=" margin: 0px;    width: 333px;">
                                        <textarea class="form-control" rows="2" id="contract_description" name="contract_description[]"></textarea>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding:0px;">
                                    <hr style="border: 1px solid #a1e8a1;">
                                </div>
                            </div>
                            <div id="cloned"></div>
                        </div>
                    </div>
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Contracts";
                            } else {
                            echo "Save Contracts";
                            } ?>" class="btn btn-primary" id="submit_contract_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
                <?php
                    if(!empty($results['partner_contracts'])){
                        ?>
                <div id="exist_documents">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Contract Type</th>
                                <th>Contract File</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                                foreach($results['partner_contracts'] as $value){
                                    $index ++;
                                    if($value['collateral_tag'] == CONTRACT){
                                ?>
                            <tr>
                                <td><?php echo $index; ?></td>
                                <td><?php echo $value['collateral_type'] ?></td>
                                <td><a target="_blank" href=<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$value['file']?>><?php echo $value['file'] ?></a></td>
                                <td><?php echo $value['document_description'] ?></td>
                                <td><?php echo $value['start_date'] ?></td>
                                <td><?php echo $value['end_date'] ?></td>
                            </tr>
                            <tr>
                                <?php
                                    }
                                  }
                                    }
                                    ?>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
             <div id="container_5" style="display:none;" class="form_container">
                <form name="myForm" class="form-horizontal" id ="operation_region_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/partner/process_partner_brand_mapping" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                    
                        <div class="panel panel-default">
                            <div class="panel-heading"><b>Add Brand</b></div>
                            <div class="panel-body">
                                <?php
                                    foreach ($results['services'] as $value) {
                                       
                                       
                                        ?>
                                <div class="col-md-12 form-group">
                                    <div class="col-md-3" style="padding: 5px 2px;"><?php echo $value->services ?></div>
                                    <select name ="brand[<?php echo $value->id ?>][]"  id="brand_mapping_<?php echo $value->id;?>" class=" col-md-4 brand_mapping" multiple="multiple">
                                       <?php if (!empty($results['brand_mapping'])) {
                                            foreach ($results['brand_mapping'] as $val) {
                                                if ($val['service_id'] == $value->id) { ?>
                                         
                                                    <option value="<?php echo $val['brand'] ?>" <?php if($val['active'] == 1){ echo "selected";} ?>><?php echo $val['brand'] ?></option>
                                                <?php }
                                            }
                                        } ?>
                                     
                                    </select>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                   <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Brand";
                            } else {
                            echo "Save Brand";
                            } ?>" class="btn btn-primary" id="submit_contract_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
             </div>
            <div class="clear"></div>
             <div id="container_6" style="display:none;" class="form_container">
               <form name="l_c_form" class="form-horizontal" id ="l_c_form" action="<?php echo base_url() ?>employee/partner/process_partner_learning_collaterals" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <p><b>Brand Collateral</b></p>
                            </div>
                           <div class="panel-body">
                               <div class="col-md-6" style="width: 49%;">
                                <div class="form-group">
                                <label for="Services">Select Appliance *</label>
                                <p id="brand_mapping_holder" style="display:none;"><?php if(isset($results['brand_mapping'])){ echo json_encode($results['brand_mapping']); }?></p>
                                <select class="form-control" id="l_c_service" name="l_c_service" onchange="get_brand_category_capacity_model_for_service(this.value,<?php if(isset($query[0]['id'])){echo  $query[0]['id'];}?>)" disabled=""> 
                                </select>
                                </div>
                                   <div class="form-group">
                                <label for="Services">Select Catagory *</label>
                                <select class="form-control" id="l_c_category" name="l_c_category[]" multiple="multiple" disabled="">
                                </select>
                                </div>
                                <div class="form-group">
                                <label for="Services">Select File *</label>
                                <input type="file" class="form-control"  name="l_c_file" id="l_c_file" disabled="">
                                </div>
                                   <div class="form-group">
                                <label for="Services">Select Request Type*</label>
                                <select class="form-control" id="l_c_request_type" name="l_c_request_type[]" multiple="multiple" disabled="">
                                    <option value="installation">Installation</option>
                                    <option value="repair">Repair</option>
                                    </select>
                                </div>
                                   </div>
                               <div class="col-md-6" style="float:right; width: 49%;">
                                   <div class="form-group">
                                <label for="Services">Select Brand *</label>
                                <select class="form-control" id="l_c_brands" name="l_c_brands[]" multiple="multiple" disabled="">
                                </select>
                                </div>
                                   <div class="form-group">
                                       <label for="Services">Select Capacity</label><div class="checkbox" style="float:right;"><input disabled="disabled" onchange="select_all_capacity()" id="capacity_all" type="checkbox" value="">Select All</div>
                                <select class="form-control" id="l_c_capacity" name="l_c_capacity[]" multiple="multiple" disabled="">
                                </select>
                                </div>
                                   <div class="form-group">
                                <label for="Services">Select Collateral Type*</label>
                                <select class="form-control" id="l_c_type" name="l_c_type" disabled="">
                                </select>
                                </div>
                                <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="l_c_description" name="description" disabled="" placeholder="Add Description">
                                </select>
                                </div>
                                   </div>
                               <div class="clear"></div>
                             
                            </div>
                        </div>
                    </div>
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="button" onclick="validate_l_c_form()" value="<?php if (isset($query[0]['id'])) {
                            echo "Update Learning Collateral";
                            } else {
                            echo "Save Learning Collateral";
                            } ?>" class="btn btn-primary" id="submit_l_c_btn">
                            <?php echo "<a class='btn btn-small btn-primary' href=" . base_url() . "employee/partner/viewpartner>Cancel</a>"; ?>
                        </center>
                    </div>
                </form>
                 <div id="exist_documents">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Document Type</th>
                                <th>Appliance</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Capacity</th>
                                <th>Request Type</th>
                                <th>File</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                            if(array_key_exists("partner_contracts", $results)){
                                foreach($results['partner_contracts'] as $value){
                                    if($value['collateral_tag'] == LEARNING_DOCUMENT){
                                        $index++;
                                        $url = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$value['file'];
                                ?>
                            <tr>
                                <td><?php echo $index?></td>
                                <td><?php echo $value['collateral_type'] ?></td>
                                <td><?php echo $value['services'] ?></td>
                                <td><?php echo $value['brand'] ?></td>
                                <td><?php echo $value['category'] ?></td>
                                <td><?php echo $value['capacity'] ?></td>
                                <td><?php echo $value['request_type'] ?></td>
                                <td><?php echo $this->miscelleneous->get_reader_by_file_type($value['document_type'],$url,"200")?></td>
                                <td><?php echo $value['document_description'] ?></td>
                                <td><?php echo $value['start_date'] ?></td>
                            </tr>
                            <tr>
                                <?php
                                    }
                                  }
                            }
                                    ?>
                    </table>
                </div>
             </div>
             <div class="clear"></div>
<!--             action="<?php //echo base_url(); ?>file_upload/process_upload_serial_number" -->
             <div id="container_7" style="display:none;" class="form_container">
<!--                  <form class="form-horizontal"  id="fileinfo"  method="POST" enctype="multipart/form-data">-->
                    <div class="form-group  <?php if (form_error('excel')) {
                        echo 'has-error';
                        } ?>">
                        <label for="excel" class="col-md-1">Upload Serial No</label>
                        <div class="col-md-4">
<!--                            <input type="text" name="partner_id"  value="247034" />-->
                            <input type="file" class="form-control" id="SerialNofile"  name="file" >
                        </div>
<!--                        <input type= "submit" class="btn btn-danger btn-md"  value="upload">-->
                        <button id="serialNobtn" class="btn btn-primary btn-md"  >Upload</button>
                    </div>
<!--                  </form>-->
                 <div class="form-group">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                        </div>
                        <div class="msg"></div>
                    </div>
                
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Download</th>
                                <th>Uploaded By</th>
                                <th>Uploaded Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
             </div>
             <div class="clear"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
up_message();
$(document).on('keyup', '.up_message', function (e) {
    up_message();
});

function up_message(){
    var up_rate = $("#up_rate").val();
    var up_threshold = $("#up_threshold").val();
    
    var up_price = Number(up_rate) * Number(up_threshold);
    if(up_threshold === ""){
        up_threshold = 0;
    }
    
    $("#up_th_msg").html(up_threshold + " KM (<i class='fa fa-rupee'></i> "+ up_price +") will be auto approved by system" );
}
    $('.select_state').select2({
        placeholder: "Select State",
        allowClear: true
    });
    $('.brand_mapping').select2({
        placeholder: "Enter Brand",
        allowClear: true,
        tags: true
    });
    $('#state').select2({
        placeholder: "Select State"
    });
    $('#district').select2({
        placeholder: "Select City"
    });
    $('#pincode').select2({
        placeholder: "Select Pincode"
    });
    $('#account_manager').select2({
        placeholder: "Select Account Managers"
    }); 
    $('#l_c_brands').select2({
        placeholder: "Select Brand",
        allowClear: true,
        tags: true
    });
    $('#l_c_category').select2({
        placeholder: "Select category",
        allowClear: true,
        tags: true
    });
    $('#l_c_capacity').select2({
        placeholder: "Select Capacity",
        allowClear: true,
        tags: true
    });
     $('#l_c_request_type').select2({
        placeholder: "Select Request Type",
        allowClear: true,
        tags: true
    });
    //$( ".agreement_start_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
    //$("#agreement_end_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
    $("#grace_period_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
    
    
    //Check for upcountry
    $("#upcountry").change(function () {
        if (this.checked) {
            $("#upcountry_rate").attr('required', true);
            $("#upcountry_rate").attr('disabled', false);
        } else {
            $("#upcountry_rate").attr('required', false);
            $("#upcountry_rate").attr('disabled', true);
            // $("#upcountry_rate").val('');
        }
    
    });
    
    
    function getDistrict() {
        var state = $("#state").val();
        var district = $(".district").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/getDistrict/1',
            data: {state: state, district: district},
            success: function (data) {
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
    
    $(function () {
        $('#username').on('keypress', function (e) {
            if (e.which == 32)
                return false;
        });
    });
    
    function remove_image(vendor_id, file_name, type) {
        var c = confirm('Do you want to permanently remove photo?');
        if (c) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/partner/remove_uploaded_image',
                data: {id: vendor_id, file_name: file_name, type: type},
                success: function (data) {
                    location.reload();
                }
            });
        } else {
            return false;
        }
    }
    
</script>
<script type="text/javascript">
     var serialNo;
    $.validator.addMethod("regx", function (value, element, regexpr) {
        return regexpr.test(value);
    }, "Please enter a valid Number.");
    (function ($, W, D)
    {
        var JQUERY4U = {};
    
        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        $("#booking_form").validate({
                            rules: {
                                company_name: "required",
                                public_name: "required",
                                address: "required",
                                district: "required",
                                username: "required",
                                partner_type: "required",
                                phone_1: {
                                    required: true,
                                    minlength: 10,
                                    number: true
                                },
                                phone_2: {
                                    number: true
                                },
                                primary_contact_phone_1: {
                                    minlength: 10,
                                    number: true
                                },
                                primary_contact_phone_2: {
                                    number: true
                                },
                                owner_phone_1: {
                                    minlength: 10,
                                    number: true
                                },
                                owner_phone_2: {
                                    number: true
                                },
                                state: {
                                    required:true
                                },
                                email: {
                                    email: true
                                },
                                primary_contact_email: {
                                    required: true
                                },
                                owner_email: {
                                    required: true
                                },
                                invoice_courier_phone_number: {
                                    number: true
                                }
                            },
                            messages: {
                                company_name: "Please enter your Company Name",
                                public_name: "Please enter your Public Name",
                                address: "Please enter Address",
                                district: "Please Select District",
                                partner_type: "Please Select Partner Type",
                                state: "Please Select State",
                                primary_contact_phone_1: "Please fill correct phone number",
                                primary_contact_phone_2: "Please fill correct phone number",
                                owner_phone_1: "Please fill correct phone number",
                                owner_phone_2: "Please fill correct phone number",
                                primary_contact_name: "Please fill Name",
                                owner_name: "Please fill Name",
                                primary_contact_email: "Please fill correct email",
                                owner_email: "Please fill correct email",
                                username: "Please fill User Name",
                                invoice_courier_phone_number:'Please fill correct phone number'
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
    
    $('#pan_no').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
    
        e.preventDefault();
        return false;
    });
    
    $('#registration_no').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
    
        e.preventDefault();
        return false;
    });
    
    $(document).ready(function () {
        var charReg = /^[0-9a-zA-Z,.()+\/\s-]*$/;
        $('.blockspacialchar').focusout(function () {
            var inputVal = $(this).val();
    
            if (!charReg.test(inputVal)) {
                alert("Spacial Characters are not allowed");
                $(this).css({'border-color' : 'red'});
                $('#submit_btn').attr('disabled',true);
            }else{
                $(this).css({'border-color' : '#ccc'});
                $('#submit_btn').attr('disabled',false);
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
                $('#submit_btn').attr('disabled',true);
            }else{
                $(this).css({'border-color' : '#ccc'});
                $('#submit_btn').attr('disabled',false);
            }
    
        });
    });
    function load_form(tab_id){
       total_div  = document.getElementsByClassName('form_container').length;
       for(var i =1;i<=total_div;i++){
           if(i != tab_id){
             document.getElementById("container_"+i).style.display='none';
             document.getElementById(i).style.background='#d9edf7';
            }
            else{
                document.getElementById("container_"+i).style.display='block';
                document.getElementById(i).style.background='#fff';
            }
       }
      
       if(tab_id === '6'){
           get_partner_services();
       }
       else if(tab_id === '7'){

           getserial_number_history();
       }
    }
    function add_more_fields(id){
      var buttonIdArray =  id.split("_");
      id_number = parseInt(buttonIdArray[2]);
    var div = document.getElementById('contract_holder_'+id_number),
    clone = div.cloneNode(true); 
    clone.id = "contract_holder_"+(id_number+1);
    document.getElementById(id).id = buttonIdArray[0]+"_"+buttonIdArray[1]+"_"+(id_number+1);
    document.getElementById("cloned").appendChild(clone); 
    //var targetDiv = document.getElementById("contract_holder_"+(id_number+1)).getElementsByClassName("contract_type")[0];
    //targetDiv.id = "contract_type_"+(id_number+1);
    }
    function create_drop_down(brandMappingJson){
        var brandDropdownString  = '';
        var categoryDropdownString  = '';
        var capacityDropdownString  = '';
        var collateral_typeDropdownString ='<option value="">Select Collateral</option>';
        var obj = JSON.parse(brandMappingJson);
        for(var i=0;i<obj.brand.length;i++){
                var brandDropdownString = brandDropdownString+"<option value='"+obj.brand[i].brand+"'>"+obj.brand[i].brand+"</option>";
        }
        for(var i=0;i<obj.category.length;i++){
                var categoryDropdownString = categoryDropdownString+"<option value='"+obj.category[i].category+"'>"+obj.category[i].category+"</option>";
        }
        for(var i=0;i<obj.capacity.length;i++){
                var capacityDropdownString = capacityDropdownString+"<option value='"+obj.capacity[i].capacity+"'>"+obj.capacity[i].capacity+"</option>";
        }
        for(var i=0;i<obj.collateral_type.length;i++){
                var collateral_typeDropdownString = collateral_typeDropdownString+"<option value='"+obj.collateral_type[i].id+"_"+obj.collateral_type[i].collateral_type+"'>"+obj.collateral_type[i].collateral_type+"</option>";
        }
        if(brandDropdownString !== ''){
            document.getElementById("l_c_brands").disabled = false;
            document.getElementById("l_c_brands").innerHTML = brandDropdownString;
        }
        if(categoryDropdownString !== ''){
            document.getElementById("l_c_category").disabled = false;
            document.getElementById("l_c_file").disabled = false;
            document.getElementById("l_c_description").disabled = false;
            document.getElementById("capacity_all").disabled = false;
            document.getElementById("l_c_request_type").disabled = false;
            document.getElementById("l_c_category").innerHTML = categoryDropdownString;
            
        }
        if(capacityDropdownString !== ''){
            document.getElementById("l_c_capacity").disabled = false;
            document.getElementById("l_c_capacity").innerHTML = capacityDropdownString;
        }
        if(collateral_typeDropdownString !== ''){
            document.getElementById("l_c_type").disabled = false;
            document.getElementById("l_c_type").innerHTML = collateral_typeDropdownString;
        }
    }
    function get_brand_category_capacity_model_for_service(service,partner){
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/partner/get_service_details',
                data: {partner_id: partner, service_id: service},
                success: function (data) {
                    create_drop_down(data);
                }
            });
    }
    function validate_l_c_form(){
    service = $("#l_c_service").val();
    brands = $("#l_c_brands").val();
    category = $("#l_c_category").val();
    collateral_type = $("#l_c_type").val();
    request_type = $("#l_c_request_type").val();
    file = $("#l_c_file").val();
    if(service && brands && category && collateral_type && file && request_type){
       document.getElementById("l_c_form").submit();
    }
    else{
        alert("Please Select All mendatory Fields");
    }
    }
    function 
    get_partner_services(){
        var serviceDropdownString = '<option value="">Select Appliance</option>';
        var partner = $("#partner_id").val();
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/partner/get_partner_services',
                data: {partner_id: partner},
                success: function (data) {
                    var obj = JSON.parse(data);
                    for(var i=0;i<obj.length;i++){
                        serviceDropdownString = serviceDropdownString+"<option value='"+obj[i].service_id+"'>"+obj[i].services+"</option>";
                    }
                    if(serviceDropdownString !== '<option value="">Select Appliance</option>'){
                        document.getElementById("l_c_service").disabled = false;
                        document.getElementById("l_c_service").innerHTML = serviceDropdownString;
                    }
                    else{
                        alert("This Partner Does'nt have any Appliance yet");
                    }
                }
            });
    }
    function select_all_capacity(){
   if ($('#capacity_all').is(":checked"))
{
 $('#l_c_capacity option').prop('selected', true);
 $('#l_c_capacity').select2({
        placeholder: "All Selected",
        allowClear: true,
        tags: true
    });
}
else{
     $('#l_c_capacity option').prop('selected', false);
     $('#l_c_capacity').select2({
        placeholder: "Select All",
        allowClear: true,
        tags: true
    });
    }

    }
    
   
    function getserial_number_history(){
        serialNo = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
            pageLength: 5,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function(d){
                    d.file_type = '<?PHP echo PARTNER_SERIAL_NUMBER_FILE_TYPE ;?>';
                }
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3,4],
                    "orderable": false
                }
            ]
        });
    }
    <?php if(isset($query[0]['id'])) { ?>
    $(function () {
        $('#serialNobtn').click(function () {
            $('.myprogress').css('width', '0');
            $('.msg').text('');

            var file = $('#SerialNofile').val();

            if (file === '') {
                alert('Please select a file');
                return;
            }
            var formData = new FormData();
            formData.append('file', $('#SerialNofile')[0].files[0]);
            formData.append('partner_id', '<?php echo $query[0]['id']; ?>');

            $('#serialNobtn').attr('disabled', 'disabled');
            $('.progress').css('display', 'block');
            $('.msg').text('Uploading in progress...');
            $.ajax({
                url: '<?php echo base_url(); ?>file_upload/process_upload_serial_number',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.myprogress').text(percentComplete + '%');
                            $('.myprogress').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (data) {
                    alert(data);
                    $('.msg').text(data);
                    serialNo.ajax.reload(null, false);
                   
                }
            });
        });
    });
    <?php } ?>
</script>
<style>
    .progress{
        display:none;
    }
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>