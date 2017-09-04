<style>
    .main_container_block{
        padding: 10px;
        background: #F7F7F7;
        box-sizing: border-box;
        color: #73879C;
    }
    .x_panel {
        position: relative;
        width: 100%;
        margin-bottom: 10px;
        padding: 10px 17px;
        display: inline-block;
        background: #fff;
        border: 1px solid #E6E9ED;
        -webkit-column-break-inside: avoid;
        -moz-column-break-inside: avoid;
        column-break-inside: avoid;
        opacity: 1;
        transition: all .2s ease
    }
    .x_title {
        border-bottom: 2px solid #E6E9ED;
        padding: 1px 5px 6px;
        margin-bottom: 10px
    }
    .x_title .filter {
        width: 40%;
        float: right
    }
    .x_title h2 {
        margin: 5px 0 6px;
        float: left;
        display: block;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .x_title h2 small {
        margin-left: 10px
    }
    .x_title span {
        color: #BDBDBD
    }
    .x_content {
        padding: 0 5px 6px;
        position: relative;
        width: 100%;
        float: left;
        clear: both;
        margin-top: 5px
    }
    .x_content h4 {
        font-size: 16px;
        font-weight: 500
    }
    .form-control {
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 0px!important;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }
</style>

<div class="main_container_block">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit Details <small></small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <?php
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success_msg').'</strong>
                    </div>';
                    }
                    if ($this->session->flashdata('error_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error_msg').'</strong>
                    </div>';
                    }
                    ?>
                    <form id="profile-details-form" class="form-horizontal form-label-left" action="<?php echo base_url();?>employee/partner/process_partner_edit_details" method="post">

                        <div class="panel panel-default">
                            <div class="panel-heading">Comapny Details</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="company-name">Company Name
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="company-name" name="company_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['company_name'])){ echo $partner_details[0]['company_name'];}?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="public-name">Public Name
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="last-name" name="public_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['public_name'])){ echo $partner_details[0]['public_name'];}?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="address">Address<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="address" name="address" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['address'])){ echo $partner_details[0]['address'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="landmark">Landmark
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="last-name" name="landmark" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['landmark'])){ echo $partner_details[0]['landmark'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="pincode">Pincode <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="pincode" name="pincode" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['pincode'])){ echo $partner_details[0]['pincode'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="district">District <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="last-name" name="district" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['district'])){ echo $partner_details[0]['district'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="state">State <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="state" name="state" required="required" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['state'])){ echo $partner_details[0]['state'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">POC Details</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="poc-name">Primary Contact Name <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="poc-name" name="primary_contact_name"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['primary_contact_name'])){ echo $partner_details[0]['primary_contact_name'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="poc-email">Primary Contact Email <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="email" id="poc-email" name="primary_contact_email" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['primary_contact_email'])){ echo $partner_details[0]['primary_contact_email'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="poc-contact-number">Primary Contact Number<span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="poc-contact-number" name="primary_contact_phone_1" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['primary_contact_phone_1'])){ echo $partner_details[0]['primary_contact_phone_1'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="poc-alternate-number">Primary Contact Alternate Number
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="poc-alternate-number" name="primary_contact_phone_2"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['primary_contact_phone_2'])){ echo $partner_details[0]['primary_contact_phone_2'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Owner Details</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="owner-name">Owner Name <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="owner-name" name="owner_name" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['owner_name'])){ echo $partner_details[0]['owner_name'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="owner-email">Owner Email <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="email" id="owner-email" name="owner_email" class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['owner_email'])){ echo $partner_details[0]['owner_email'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="owner-phone-1">Owner Contact Number <span class="required">*</span>
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="owner-phone-1" name="owner_phone_1"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['owner_phone_1'])){ echo $partner_details[0]['owner_phone_1'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="owner-phone-2">Owner Alternate Number
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="owner-phone-1" name="owner_phone_2"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['owner_phone_2'])){ echo $partner_details[0]['owner_phone_2'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="owner-alternate-email">Owner Alternate Email
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="owner-alternate-email" name="owner_alternate_email"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['owner_alternate_email'])){ echo $partner_details[0]['owner_alternate_email'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Spare Details</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="spare_notification_email">Spare Notification Email
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="spare_notification_email" name="spare_notification_email"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['spare_notification_email'])){ echo $partner_details[0]['spare_notification_email'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Registration Details</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="pan-no">PAN No
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="pan-no" name="pan"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['pan'])){ echo $partner_details[0]['pan'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="tin-no">Tin No
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="tin-no" name="tin"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['tin'])){ echo $partner_details[0]['tin'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="cin">CIN
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="cin" name="registration_no"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['registration_no'])){ echo $partner_details[0]['registration_no'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 col-xs-12" for="cst-no">CST No
                                            </label>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <input type="text" id="cst-no" name="cst_no"  class="form-control col-md-7 col-xs-12" value="<?php if(isset($partner_details[0]['cst_no'])){ echo $partner_details[0]['cst_no'];}?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?php if(isset($partner_details[0]['id'])){ echo $partner_details[0]['id'];}?>">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-5">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>