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
    .vertical-align{
         height:100%;
         padding-top: 1%
    }
    #warehouse_datatable_filter{
        text-align: right;
    }
    .form-horizontal .control-label{
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="clear"></div>
        <div class="panel panel-info">
            <div class="panel-heading" style="padding: 6px 7px 0px 10px;">
                <b><?php if (isset($query[0]['id'])) {
                    echo "EDIT PARTNER";
                    } else {
                    echo "ADD PARTNER";
                    } ?></b>
                 <div class="pull-right">
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/partner/viewpartner" style="margin-right:5px;">View Partners</a>
                    <?php if (isset($query[0]['id'])) { ?>
                        <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/partner/upload_partner_brand_logo/<?php echo $query[0]['id'] ?>/<?php echo $query[0]['public_name'] ?>" style="margin-right:5px;">Upload Partner Brand Logo</a>
                        <a href="<?php echo base_url() ?>employee/partner/get_partner_login_details_form/<?php echo $query[0]['id'] ?>" class="btn btn-primary"><b>MANAGE LOGIN</b></a>
                    <?php } ?>
            </div>
                <div class="clear"></div>
            </div>
            <div id="tabs" style="border:0px solid #fff;float:left;">
                <div class="col-md-12" style="">
                    <ul>
                        <?php
                            if (!isset($query[0]['id'])) {
                            ?>
                        <li style="background:#fff"><a id="1" href="#tabs-1"><span class="panel-title">Basic Details</span></a></li>
                        <li><a id="2" href="#tabs-2" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Documents</span></a></li>
                        <li><a id="3" href="#tabs-3" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Operation Region</span></a></li>
                        <li><a id="4" href="#tabs-4" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Contracts</span></a></li>
                        <li><a id="5" href="#tabs-5" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Brand Mapping</span></a></li>
                        <li><a id="6" href="#tabs-6" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Brand Collateral</span></a></li>
                        <li><a id="7" href="#tabs-7" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Upload Serial No</span></a></li>
                        <li><a id="8" href="#tabs-8" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Add Contacts</span></a></li>
                        <li><a id="9" href="#tabs-9" ><span class="panel-title" onclick="alert('Please Add Basic Details First')">Warehouse Details</span></a></li>
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
                        <li><a id="8" href="#tabs-8" onclick="load_form(this.id)"><span class="panel-title">Add Contacts</span></a></li>
                        <li><a id="9" href="#tabs-9" onclick="load_form(this.id)"><span class="panel-title">Warehouse Details</span></a></li>
                        <?php
                            }
                            ?>
                    </ul>
                </div>
            </div>
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
                                            echo $query[0]['invoice_email_cc'];}  else { echo "anuj@247around.com,nits@247around.com,".ACCOUNTANT_EMAILID; }
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
                         <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading"><b>Warehouse Details</b></div>
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="is_wh" class="col-md-4">Is partner using 247around warehouse</label>
                                            <div class="col-md-1">
                                                <input  type="checkbox" class="form-control"  name="is_wh" value = "1" <?php if (isset($query[0])) {
                                                    if($query[0]['is_wh'] == '1'){ echo "checked"; }
                                                    } ?> >
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
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo $query[0]['id']?>>
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
                                        <label for="gst_number" class="col-md-3">GST Number</label>
                                        <div class="col-md-4" style="width:25%">
                                            <input type="text" style="text-transform:uppercase" class="form-control blockspacialchar"  name="gst_number" id="gst_number" value = "<?php if (isset($query[0]['gst_number'])) {
                                                echo $query[0]['gst_number'];
                                                } ?>" placeholder="GST Number">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="file" class="form-control"  name="gst_number_file">
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['gst_number_file']) && !empty($query[0]['gst_number_file'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['gst_number_file'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($query[0]['gst_number_file']) && !empty($query[0]['gst_number_file'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>, '<?php echo $query[0]['gst_number_file'] ?>', 'gst_number_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="public_name" name="public_name" value="<?php if (isset($query[0]['public_name'])) {echo $query[0]['public_name'];} ?>"
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
                                    ?>
                                                    </table>
                </div>
                                <?php
                                    }
                                    ?>
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
                                <button type="button" class="btn btn-success" style="float:right;margin-top: -33px;background: #31b0d5;border-color: #31b0d5;" id="add_more_1" onclick="add_more_fields(this.id)">Add More Contracts</button>
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
                                <div class="col-md-6" style="padding:0px;">
                                    <label for="contract file" class="col-md-4">Contract Description</label>
                                    <div class="form-group" style=" margin: 0px 17px;    width: 569px;">
                                        <textarea class="form-control" rows="1" id="contract_description" name="contract_description[]"></textarea>
                                    </div>
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
                                  ?>
                                                    </table>
                </div>
                                <?php
                                    }
                                    ?>

            </div>
            <div class="clear"></div>
             <div id="container_5" style="display:none;margin: 30px 10px;" class="form_container">
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
              <div id="container_8" style="display:none;margin: 30px 10px;" class="form_container">
                 <button class="btn" onclick="show_add_contact_form()" style="background-color: #337ab7;color: #fff;margin-bottom: 10px;">Add Contacts</button>
                 <form name="contact_form" class="form-horizontal" id ="contact_form" action="<?php echo base_url() ?>employee/partner/process_partner_contacts" method="POST" enctype="multipart/form-data" onsubmit="return process_contact_persons_validations()">
                    <?php
                        if(isset($query[0]['id'])){
                            if($query[0]['id']){
                            ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                        }
                        }
                        ?>
                        <div class="clonedInput panel panel-info " id="clonedInput1">
                        <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                            <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                            <div class="panel-heading" style=" background-color: #f5f5f5;">
                                <p style="color: #000;"><b>Contact Persons</b></p>  
                                <div class="clone_button_holder" style="float:right;margin-top: -31px;">
                                    <button class="clone btn btn-sm btn-info">Add</button>
                            <button class="remove btn btn-sm btn-info">Remove</button>
                                    </div>
                        </div> 
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Name *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-contact-name"  name="contact_person_name[]" id="contact_person_name_1" value = "" placeholder="Enter Name">
                                            </div>
                                        </div>
                                       <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Email *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_email[]" id="contact_person_email_1" value = "" placeholder="Enter Email">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Contact Number *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_contact[]" id="contact_person_contact_1" value = "" placeholder="Enter Contact">
                                            </div>
                                        </div>
                                         <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Alternate Email </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_alt_email[]" id="contact_person_alt_email_1" value = "" placeholder="Alternative Email">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Alternate Contact Number</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_alt_contact[]" id="contact_person_alt_contact_1" value = "" placeholder="Alternative Contact">
                                            </div>
                                        </div> 
                                        <div class="form-group "> 
                                            <input type="hidden" value="" id="checkbox_value_holder_1" name="checkbox_value_holder[]">
                                              <div class="col-md-6"> 
                                                  <label><b>Create Login</b></label><input style="margin-left: 167px;" type="checkbox" value="" id="login_checkbox_1" name="login_checkbox[]">
                                            </div>   
                                                  </div>
                                </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Department *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control"  id="contact_person_department_1" name="contact_person_department[]" onChange="getRoles(this.value,this.id)" >
                                                    <option value="" disabled="" selected="">Select Department</option>
                                                <?php
                                                foreach ($department as $value){
                                                ?> 
                                                     <option value="<?php echo $value['department'] ?>"> <?php echo $value['department'] ?></option>
                                                <?php
                                                }
                                                ?>
                                                            </select>  
                                          </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Role *</label>
                                            <div class="col-md-6">
                                                <select disabled="" type="text" class="form-control"  id="contact_person_role_1" name="contact_person_role[]" onChange="getFilters(this.value,this.id)" >
                                                    <option value = "">Select Roles</option>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="form-group ">
                                            <input type="hidden" value="" id="states_value_holder_1" name="states_value_holder[]">
                                            <label for="service_name" class="col-md-4">States <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Applicable only for roles, where state filter is required eg - Area Sales Manager">?</button> </label>
                                            <div class="col-md-6">
                                                <div class="filter_holder" id="filter_holder_1">
                                                    <select multiple="" class=" form-control contact_person_states" name ="contact_person_states[0][]" id="contact_person_states_1" disabled="">
                                                      <option value = "">Select States</option>
                                                <?php
                                                    foreach ($results['select_state'] as $state) {
                                                        ?>
                                                <option value = "<?php echo $state['state'] ?>">
                                                    <?php echo $state['state']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                                  </div>
                                            </div>
                                        </div> 
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Permanent Address</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-model"  name="contact_person_address[]" id="contact_person_address_1" value = "" placeholder="Enter Address"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Correspondence Address</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-model"  name="contact_person_c_address[]" id="contact_person_c_address_1" value = "" placeholder="Enter Address"></textarea>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="cloned"></div>
                    <div class="form-group " style="text-align:center">
                    <input type="submit" class="btn btn-primary" value="Save Contacts">
                    </div>
                </form>
                  <?php
                    if(!empty($results['contact_persons'])){
                        ?>
                <div id="exist_documents">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Permanent Address</th>
                                <th>Alt Email</th>
                                <th>Alt Contact</th>
                                <th>Correspondence Address</th>
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                                foreach($results['contact_persons'] as $value){
                                    $index ++;
                                ?>
                            <tr>
                                <td><?php echo $index; ?></td>
                                <td><?php echo $value['name'] ?></td>
                                <td><?php echo $value['department'] ?></td>
                                <td><?php echo $value['role'] ?></td>
                                <td><?php echo $value['official_email'] ?></td>
                                <td><?php echo $value['official_contact_number'] ?></td>
                                <td><?php echo $value['permanent_address'] ?></td>
                                 <td><?php echo $value['alternate_email'] ?></td>
                                <td><?php echo $value['alternate_contact_number'] ?></td>
                                <td><?php echo $value['correspondence_address'] ?></td>
                                <td><button type="button" class="btn btn-info btn-sm" onclick="create_edit_form(this.value)" data-toggle="modal"  id="edit_button" value='<?=json_encode($value)?>'><i class="fa fa-edit"></i></button>
                                    <a  class="btn btn-danger btn-sm" href="<?php echo base_url();?>employee/partner/delete_partner_contacts/<?php echo $value['id'];?>/<?php echo  $query[0]['id']?>" title="Delete" onclick="return confirm('Are you sure you want to delete this contact?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                  }
                                  ?>
                                </table>
            </div>
                                <?php
                                    }
                                    ?>
             </div>
              <div class="clear"></div>
             <div id="container_9"  style="display:none;margin: 30px 10px;" class="form_container">
                <button class="btn btn-primary" onclick="show_add_warehouse_form()" style="background-color: #337ab7;color: #fff;margin-bottom: 10px;">Add Warehouse</button>
                    <form  class="form-horizontal" id ="warehouse_form" action="<?php echo base_url() ?>employee/partner/process_add_warehouse_details" method="POST" enctype="multipart/form-data" >
                    <?php if(isset($query[0]['id'])){ ?>
                        <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php } ?>
                        <div class="clonedInput panel panel-info " id="clonedInput1">
                            <div class="panel-heading" style=" background-color: #f5f5f5;">
                                <p style="color: #000;"><b>Warehouse Details</b></p>  
                                <div class="clone_button_holder" style="float:right;margin-top: -31px;">
                                    <button class="clone btn btn-sm btn-info">Add</button>
                                    <button class="remove btn btn-sm btn-info">Remove</button>
                                </div>
                            </div> 
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_address_line1') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_address_line1" class="col-md-4">Warehouse Address( Line 1 ) *</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-contact-name"  name="warehouse_address_line1" id="warehouse_address_line1" value = "" placeholder="Enter Warehouse Address( Line1 )" required=""></textarea>
                                                <?php echo form_error('warehouse_address_line1'); ?>
                                            </div>
                                        </div>
                                    </div>    
                                     <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_address_line2') ) { echo 'has-error';} ?> ">
                                            <label for="warehouse_address_line2" class="col-md-4">Warehouse Address( Line 2 ) *</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-contact-name"  name="warehouse_address_line2" id="warehouse_address_line2" value = "" placeholder="Enter Warehouse Address( Line 2 )" required=""></textarea>
                                                 <?php echo form_error('warehouse_address_line2'); ?>
                                            </div>
                                        </div>
                                    </div>    
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_city') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_city" class="col-md-4">Warehouse City *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-contact-name"  name="warehouse_city" id="warehouse_city" value = "" placeholder="Enter Warehouse City" required="">
                                                 <?php echo form_error('warehouse_city'); ?>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_region') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_region" class="col-md-4">Warehouse Region *</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="warehouse_region" id="warehouse_region" required="">
                                                    <option selected="" value="" disabled="">Select Region</option>
                                                    <option value="East">East</option>
                                                    <option value="North">North</option>
                                                    <option value="South">South</option>
                                                    <option value="West">West</option>
                                                </select>
                                                <?php echo form_error('warehouse_region'); ?>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_pincode') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_pincode" class="col-md-4">Warehouse Pincode *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-contact-name allowNumericWithOutDecimal"  name="warehouse_pincode" id="warehouse_pincode" value = "" minlength="6" maxlength="6" title="Pincode can only be 6 number digit" placeholder="Enter Warehouse Pincode" required="">
                                                <?php echo form_error('warehouse_pincode'); ?>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_state') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_state" class="col-md-4">Warehouse State *</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="warehouse_state" id="warehouse_state" required>
                                                    <option selected="" value="" disabled="">Select State</option>
                                                     <?php foreach ($results['select_state'] as $value) { ?>
                                                        <option value = "<?php echo $value['state']?>" > <?php echo $value['state']; ?> </option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('warehouse_state'); ?>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('contact_person_id') ) { echo 'has-error';} ?>">
                                            <label for="contact_person_id" class="col-md-4">Contact Person *</label>
                                            <div class="col-md-6">
                                                 <select name="contact_person_id" class="form-control" id="contact_person_id" required>
                                            <option selected="" value="" disabled="">Select Contact Person</option>
                                            <?php foreach ($results['contact_name'] as $value) { ?>
                                                 <option value="<?php echo $value->id;?>"<?php if(set_value('contact_name') == $value->id) {echo 'selected';} ?> ><?php echo $value->name;?></option>

                                           <?php }?>
                                        </select>
                                             <?php echo form_error('contact_person_id'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group <?php if( form_error('warehouse_state_mapping') ) { echo 'has-error';} ?>">
                                            <label for="warehouse_state_mapping" class="col-md-4">Warehouse State Mapping*</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="warehouse_state_mapping[]" id="warehouse_state_mapping" required multiple="">
                                                     <?php foreach ($results['select_state'] as $value) { ?>
                                                        <option value = "<?php echo $value['state']?>" > <?php echo $value['state']; ?> </option>
                                                    <?php } ?>
                                                    <option value = "All">All</option>
                                                </select>
                                                <?php echo form_error('warehouse_state'); ?>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="cloned"></div>
                    <div class="form-group " style="text-align:center">
                        <input type="submit" class="btn btn-primary" value="Save Warehouse">
                    </div>
                </form>
                 
                <div id="warehouse_section">
                    <table class="table" id="warehouse_datatable">
                        <thead>
                            <tr>
                                <th>Contact Person</th>
                                <th>Warehouse Address</th>
                                <th>Warehouse Address</th>
                                <th>Warehouse City</th>
                                <th>Warehouse Region</th>
                                <th>Warehouse Pincode</th>
                                <th>Warehouse State</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody id="wh_table_body"></tbody>
                    </table>
                </div>
                                
             </div>

        </div>
    </div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="    background-color: #164f4e;color: #Fff;text-align: center;margin: 0px;border-color: #164f4e;">
                <button type="button" class="close btn-primary well" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Contact</h4>
            </div>
            <div class="modal-body">
                <form name="edit_contact_form" action="<?php echo base_url().'employee/partner/edit_partner_contacts'?>" class="form-horizontal" id ="edit_contact_form" method="POST" enctype="multipart/form-data" onsubmit="return edit_contact_persons_validations()">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <input type="hidden" id="contact_id" name="contact_id" value=""/>
                                    <label for="service_name" class="col-md-4 vertical-align">Name *</label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control input-contact-name"  name="contact_person_name" id="contact_person_name" value = "" placeholder="Enter Name">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Email *</label>
                                    <div class="col-md-6">
                                        <input  type="email" class="form-control input-model"  name="contact_person_email" id="contact_person_email" value = "" placeholder="Enter Email">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Contact Number*</label>
                                    <div class="col-md-6">
                                        <input  type="number" class="form-control input-model"  name="contact_person_contact" id="contact_person_contact" value = "" placeholder="Enter Contact">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Alternate Email </label>
                                    <div class="col-md-6">
                                        <input  type="email" class="form-control input-model"  name="contact_person_alt_email" id="contact_person_alt_email" value = "" placeholder="Alternative Email">
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Alternate Contact No.</label>
                                    <div class="col-md-6">
                                        <input  type="number" class="form-control input-model"  name="contact_person_alt_contact" id="contact_person_alt_contact" value = "" placeholder="Alternative Contact">
                                    </div>
                                </div> 
                                <div class="clear"></div>
                                <div class="form-group "> 
                                    <input type="hidden" value="" id="checkbox_value_holder" name="checkbox_value_holder">
                                    <div class="col-md-6"> 
                                        <label><b>Create Login</b></label><input style="margin-left: 33%;padding-top: 5%;" type="checkbox" value="" id="login_checkbox" name="login_checkbox">
                                    </div>   
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Department *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="contact_person_department" name="contact_person_department" onChange="getEditRole(this.value)" >
                                            <option value="" disabled="">Select Department</option>
                                            <?php
                                            foreach ($department as $values) {
                                                ?> 
                                                <option value="<?php echo $values['department'] ?>"> <?php echo $values['department'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select> 
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Role *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="contact_person_role" name="contact_person_role" onChange="getFilter(this.value)" >
                                            
                                        </select>
                                        <!--<input type="text"value="<?php// echo $value['role'] ?>" onclick="{$('#contact_person_role').removeClass('hidden');this.addClass('hidden');}"/>-->
                                    </div>                                                                <div class="clear"></div>
                                </div> 
                                <div class="form-group ">
                                    <input type="hidden" value="" id="states_value_holder" name="states_value_holder">
                                    <label for="service_name" class="col-md-4 vertical-align">States </label>
                                    <div class="col-md-6">
                                        <div class="filter_holder" id="filter_holder">
                                            <select multiple="" class=" form-control contact_person_states well well-lg" name ="contact_person_states[]" id="contact_person_states">
                                                <!--<option value = "" disabled>Select States</option>-->
                                                <?php
                                                foreach ($results['select_state'] as $value) {
                                                    ?>
                                                    <option value = "<?php echo $value['state']?>" >
                                                                <?php echo $value['state']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div> 
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Permanent Address</label>
                                    <div class="col-md-6">
                                        <textarea  type="text" rows="2" class="form-control input-model"  name="contact_person_address" id="contact_person_address" value = "" placeholder="Enter Address"></textarea>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Correspondence Address</label>
                                    <div class="col-md-6">
                                        <textarea  type="text" rows="2" class="form-control input-model"  name="contact_person_c_address" id="contact_person_c_address" value = "" placeholder="Enter Address"></textarea>
                                        <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                                        <input type="hidden" id="agentid" name="agentid" value="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <input type="submit" value="Update" class=" btn btn-primary" style="background: #164f4e;">
            </form>
        </div>
    </div>

</div>
    </div>
<!--Modal ends-->

<!-- warehouse modal start-->
    <div id="wh_edit_form_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action">Edit Details </h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" id="wh_details">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_address_line1">Warehouse Address Line 1 *</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="wh_address_line1" name="wh_address_line1" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_address_line2">Warehouse Address Line 2 *</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea class="form-control" id="wh_address_line2" name="wh_address_line2" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_city">Warehouse City*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="wh_city" name="wh_city">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_region">Warehouse Region*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" name="wh_region" id="wh_region" required="">
                                            <option selected="" value="" disabled="">Select Region</option>
                                            <option value="East">East</option>
                                            <option value="North">North</option>
                                            <option value="South">South</option>
                                            <option value="West">West</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_pincode">Pincode*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input class="form-control allowNumericWithOutDecimal" id="wh_pincode" name="wh_pincode" minlength="6" maxlength="6"></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_state">State*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" name="wh_state" id="wh_state" required>
                                            <option selected="" value="" disabled="">Select State</option>
                                            <?php foreach ($results['select_state'] as $value) { ?>
                                                <option value = "<?php echo $value['state'] ?>" > <?php echo $value['state']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="wh_contact_person_id">Contact Person</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select name="wh_contact_person_id" class="form-control" id="wh_contact_person_id" required>
                                            <option selected="" value="" disabled="">Select Contact Person</option>
                                            <?php foreach ($results['contact_name'] as $value) { ?>
                                                 <option value="<?php echo $value->id;?>" ><?php echo $value->name;?></option>

                                           <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php if (form_error('wh_state_mapping')) { echo 'has-error'; } ?>">
                                    <label for="wh_state_mapping" class="control-label col-md-3">Warehouse State Mapping*</label>
                                    <div class="col-md-8 col-md-offset-1" style="margin-left: 5.333333%;">
                                        <select class="form-control" name="wh_state_mapping[]" id="wh_state_mapping" required multiple="">
                                            <?php foreach ($results['select_state'] as $value) { ?>
                                                <option value = "<?php echo $value['state'] ?>" > <?php echo $value['state']; ?> </option>
                                        <?php } ?>
                                        </select>
                                        <?php echo form_error('warehouse_state'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <input type="hidden"  id="entity_type" name='entity_type' value="partner">
                            <input type="hidden"  id="wh_id" name='wh_id' value="">
                            <input type="hidden"  id="old_contact_person_id" name='old_contact_person_id' value="">
                            <input type="hidden"  id="old_mapped_state_data" name='old_mapped_state_data' value="">
                            <button type="submit" class="btn btn-success" id="wh_details_submit_btn" name='submit_type' value="Submit">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <p class="pull-left text-danger">* These fields are required</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- warehouse modal end -->

<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;

    function clone(){
       $(this).parents(".clonedInput").clone()
           .appendTo(".cloned")
           .attr("id", "cat" +  cloneIndex)
           .find("*")
           .each(function() {
               var id = this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1] + (cloneIndex);
               }
           })
            .on('click', 'button.clone', clone)
            .on('click', 'button.remove', remove);
            $('#filter_holder_'+cloneIndex+' .select2').hide();
          // $("#e1").select2('destroy'); 
            $('#contact_person_states_'+cloneIndex).select2({
                 placeholder: "Select State",
                 allowClear: true,
                 includeSelectAllOption:true
             });
             document.getElementById("contact_person_states_"+cloneIndex).name = 'contact_person_states['+(cloneIndex-1)+'][]';
             $('#contact_person_states_'+cloneIndex).attr('disabled', true);
             $("#contact_person_name_"+cloneIndex).val("");
             $("#contact_person_email_"+cloneIndex).val("");
             $("#contact_person_contact_"+cloneIndex).val("");
             $("#contact_person_department_"+cloneIndex).val("");
             $("#contact_person_role_"+cloneIndex).val("");
             $("#contact_person_alt_email_"+cloneIndex).val("");
             $("#contact_person_alt_contact_"+cloneIndex).val("");
             $("#contact_person_address_"+cloneIndex).val("");
             $("#contact_person_c_address"+cloneIndex).val("");
       cloneIndex++;
       return false;
    }
    function remove(){
       $(this).parents(".clonedInput").remove();
       final_price();
       return false;
    }
    $("button.clone").on("click", clone);

    $("button.remove").on("click", remove);
    
     $(document).ready(function () {
  $('#contact_form').hide();
  $('#warehouse_form').hide();
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
   
   
});
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
     $('#contact_person_states_1').select2({
        placeholder: "Select State",
        allowClear: true,
        includeSelectAllOption:true
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
    
    $('#warehouse_state_mapping').select2({
        placeholder: "Select State",
        allowClear: true,
        tags: true
    });
    
    $('#wh_state_mapping').select2({
        placeholder: "Select State",
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
       else if(tab_id=== '9'){
           get_warehouse_details();
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
            document.getElementById("l_c_url").disabled = false;
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
    url = $("#l_c_url").val();
    if(file && url){
        alert("Please enter either File or URL but not both");
        return false;
    }
    if(service && brands && category && collateral_type && (file || url)&& request_type){
       if(url){
           $("#l_c_type").attr("selected","selected");
           $("#l_c_type").val("6_User Manual_pdf");
       }
        document.getElementById("l_c_form").submit();
    }
    else{
        alert("Please Select All mendatory Fields");
        return false;
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
    <?php if(isset($query[0]['id'])) { ?>
     $(document).ready(function () {
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
                    d.file_type = '<?PHP echo $query[0]['id']."_".PARTNER_SERIAL_NUMBER_FILE_TYPE ;?>';
                }
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3,4],
                    "orderable": false
                }
            ]
        });
    });

   
    function getserial_number_history(){
        serialNo.ajax.reload(null, false);
    }

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
function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    function getRoles(department,id){
        divID = id.split("_")[3];
        var data = {department:department};
        url =  '<?php echo base_url(); ?>employee/partner/get_partner_roles/'+department;
        sendAjaxRequest(data,url,"POST").done(function(response){
            $("#contact_person_role_"+divID).prop('disabled', false);
            $("#contact_person_role_"+divID).html(response);
        });
    }
    function getEditRole(department){
        //divID = id.split("_")[3];
        var data = {department:department};
        //alert(data);
        url =  '<?php echo base_url()?>employee/partner/get_partner_roles/'+department;
        sendAjaxRequest(data,url,"POST").done(function(response){
            //$("#contact_person_role_"+divID).prop('disabled', false);
            //alert(response);
            $("#contact_person_role").html(response);
        });
    }
    function getMultipleSelectedValues(fieldName){
    fieldObj = document.getElementById(fieldName);
    var values = [];
    var length = fieldObj.length;
    for(var i=0;i<length;i++){
       if (fieldObj[i].selected == true){
           values.push(fieldObj[i].value);
       }
    }
   return values.toString();
}
    function process_contact_persons_validations(){
        var div_count = $('.input-contact-name').length;
        for(var i=1;i<=div_count;i++){
            name = $("#contact_person_name_"+i).val();
            email = $("#contact_person_email_"+i).val();
            contact = $("#contact_person_contact_"+i).val();
            department = $("#contact_person_department_"+i).val();
            role = $("#contact_person_role_"+i).val();
            states = getMultipleSelectedValues("contact_person_states_"+i);
            if(name && email && contact && department && role){ 
                $('#checkbox_value_holder_'+i).val($('#login_checkbox_'+i).is(':checked'));
                $('#states_value_holder_'+i).val(states);
            }
            else{
                alert('Please add all mendatory fields');
                return false;
            }
        }
        return true;
    }
    function edit_contact_persons_validations(){
            name = $("#contact_person_name").val();
            email = $("#contact_person_email").val();
            contact = $("#contact_person_contact").val();
            department = $("#contact_person_department").val();
            role = $("#contact_person_role").val();
            states = getMultipleSelectedValues("contact_person_states");
            if(name && email && contact && department && role){ 
                $('#states_value_holder').val(states);
                 return true;
            }
            else{
                alert('Please add all mendatory fields');
                return false;
            }
    }
    function getFilters(role,id){
        divID = id.split("_")[3];
        var data = {role:role};
        url =  '<?php echo base_url(); ?>employee/partner/get_partner_roles_filters';
        sendAjaxRequest(data,url,"POST").done(function(response){
            if(response == 1){
                $("#contact_person_states_"+divID).prop('disabled', false);
            }
            else{
                $("#contact_person_states_"+divID).prop('disabled', true);
            }
        });
    }
    function getFilter(role){
        //divID = id.split("_")[3];
        var data = {role:role};
        url =  '<?php echo base_url(); ?>employee/partner/get_partner_roles_filters';
        sendAjaxRequest(data,url,"POST").done(function(response){
            if(response == 1){
                $("#contact_person_states").prop('disabled', false);
            }
            else{
                $("#contact_person_states").prop('disabled', true);
            }
        });
    }
    function show_add_contact_form(){
        $('#contact_form').show();
    }
     function show_add_warehouse_form(){
        $('#warehouse_form').show();
    }
    
    function get_warehouse_details(){
        var partner = $("#partner_id").val();
        $(document).ready(function() {
            
            var data = {partner_id: partner};
            url =  '<?php echo base_url(); ?>employee/partner/get_warehouse_details';
            sendAjaxRequest(data,url,"POST").done(function(response){
                //console.log(response);
                create_wh_table_body(response);
            });
        }); 
    }
    
    function create_wh_table_body(response){
        var table_body = '';
        var obj = JSON.parse(response);
        //console.log(obj[0]);
        $.each(obj,function(index,value){
            table_body += '<tr>';
            table_body += '<td>' +value['name'] +'</td>';
            table_body += '<td>' +value['warehouse_address_line1'] +'</td>';
            table_body += '<td>' +value['warehouse_address_line2'] +'</td>';
            table_body += '<td>' +value['warehouse_city'] +'</td>';
            table_body += '<td>' +value['warehouse_region'] +'</td>';
            table_body += '<td>' +value['warehouse_pincode'] +'</td>';
            table_body += '<td>' +value['warehouse_state'] +'</td>';
            table_body += "<td><a class='btn btn-sm btn-success' href='#' onClick='show_wh_edit_form("+ response + ','+ index  +")'>Edit</a></td>";
            table_body += '</tr>';
        });
        $('#wh_table_body').html(table_body);
    }
    
    function show_wh_edit_form(obj,index){
        var form_data = obj[index];
        url =  '<?php echo base_url(); ?>employee/partner/get_warehouse_state_mapping';
        var data = {wh_id: form_data.wh_id};
        sendAjaxRequest(data,url,"POST").done(function(response){
            var mapped_state_obj = JSON.parse(response);
            if(mapped_state_obj.status){
                $('#wh_state_mapping').select2().val(mapped_state_obj.msg).trigger("change");
            }else{
                console.log(mapped_state_obj.msg);
            }
            $('#wh_address_line1').val(form_data.warehouse_address_line1);
            $('#wh_address_line2').val(form_data.warehouse_address_line2);
            $('#wh_city').val(form_data.warehouse_city);
            $('#wh_region').val(form_data.warehouse_region);
            $('#wh_pincode').val(JSON.parse(form_data.warehouse_pincode));
            $("#wh_state").val(form_data.warehouse_state.toUpperCase());
            $("#wh_contact_person_id").val(form_data.contact_person_id);
            $("#wh_id").val(form_data.wh_id);
            $("#old_contact_person_id").val(form_data.contact_person_id);
            $('#old_mapped_state_data').val(mapped_state_obj.msg);
            $('#wh_edit_form_modal').modal('toggle');
        });
    }
    
    
    $('#wh_details_submit_btn').click(function(){
        event.preventDefault();
        var arr = {};
        var form_data = $("#wh_details").serializeArray();
        if(!$('#wh_address_line1').val()){
            alert('Please Enter Warehouse Address');
        }else if($('#wh_city').val().trim() === "" || $('#wh_city').val().trim() === " "){
            alert("Please Enter Warehouse City");
        }else if($('#wh_region option:selected').val().trim() === "" || $('#wh_region option:selected').val().trim() === null){
            alert("Please Select Warehouse Region");
        }else if($('#wh_pincode').val().trim() === "" || $('#wh_pincode').val().trim() === null || $('#wh_pincode').val().trim().length !== 6){
            alert("Please Select Enter Correct Pincode");
        }else if($('#wh_state option:selected').val().trim() === "" || $('#wh_state').val().trim() === " "){
            alert("Please Select Warehouse State");
        }else if($('#wh_contact_person_id option:selected').val() === null || $('#wh_contact_person_id option:selected').val() === ""){
            alert("Please Select Contact Person");
        }else if($('#wh_state_mapping option:selected').val() === undefined || $('#wh_state_mapping option:selected').val() === null){
            alert("Please Select State Mapping");
        }else{
            $('#wh_details_submit_btn').attr('disabled',true).html("<i class = 'fa fa-spinner fa-spin'></i> Processing...");
            form_data.push(arr);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>employee/partner/edit_warehouse_details',
                data : form_data,
                success:function(response){
                    //console.log(response);
                    $('#inventory_master_list_data').modal('toggle');
                    $('#wh_details_submit_btn').attr('disabled',false).html('Submit');
                    var data = JSON.parse(response);
                    if(data.status){
                        alert(data.msg);
                    }else{
                        alert(data.msg);
                        console.log(data.msg);
                    }
                    location.reload(true);
                }
            });
        }
    });
    
    $(".allowNumericWithOutDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46,8, 9, 27, 13, 190]) !== -1 ||
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
</script>
<style>
    .progress{
        display:none;
    }
    .panel-title {
    font-size: 15px;
}
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>

<script type="text/javascript">

    $('#contact_person_states').select2({
        placeholder: "Select State",
        allowClear: true
    });
    
    $(document).ready(function(){
        $("#login_checkbox").change(function(){
            if($("#login_checkbox").is(':checked'))
                $("#checkbox_value_holder").val("true");
        });
        
        $("#contact_person_department").change(function(){
            $("#contact_person_states").val("").trigger('change');
            //$("#contact_person_states").val();
            $("#contact_person_states").attr('disabled','disabled');
            $("#contact_person_role").val();
            //$('#contact_person_states option:selected').removeAttr('selected');
        });
        
    });
    function create_edit_form(json){
        var value = JSON.parse(json);
        var data="";
        if(value.state){
            var states=value.state;
            var states = states.split(',');
            var Values = new Array()
            for(var element in states){
                var state=states[element];
                $('#contact_person_states option[value="'+state+'"]').select2().attr("selected", "selected");
                Values.push(state);
            }
            $("#contact_person_states").val(Values).trigger('change');
        }
        if(value.login_agent_id){
          $("#checkbox_value_holder").val(true);
          $( "#login_checkbox" ).prop( "checked", true );
        }
        $("#contact_id").val(value.id);
        $("#contact_person_name").val(value.name);
        $("#contact_person_email").val(value.official_email);
        $("#contact_person_contact").val(value.official_contact_number);
        $("#contact_person_alt_email").val(value.alternate_email);
        $("#contact_person_alt_contact").val(value.alternate_contact_number);
        
        data = "<option value = '' disabled>Select Roles</option><option value = "+value.role_id+" selected>"+value.role+"</option>";
        $("#contact_person_role").html(data);
        switch(value.role){
            case "poc" :
                $("#contact_person_department").val("Admin");
                $('#contact_person_department option[value="Admin"]').attr("selected", "selected");
                break;
            case "area_sales_manager":
                $("#contact_person_department").val("Management");
                $('#contact_person_department option[value="Management"]').attr("selected", "selected");
                break;
        }
        $("#contact_person_address").val(value.permanent_address);
        $("#contact_person_c_address").val(value.correspondence_address);
        if(value.agentid){
            $("#login_checkbox").prop('checked',true);
            $("#login_checkbox_holder").val(true);
            $("#agentid").val(value.agentid);
        }
        $("#myModal").modal("show");
    }

</script>    