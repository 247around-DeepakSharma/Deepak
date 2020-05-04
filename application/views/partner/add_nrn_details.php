<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<style>
    label{
        font-size: 12px !important;
    }
</style>
<div class="right_col" role="main">
    <div id="page-wrapper" >
        <div class="container">
            <?php if (validation_errors()) { ?>
                <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
                    <div class="panel-heading" style="padding:7px 0px 0px 13px">
                        <?php echo validation_errors(); ?>

                    </div>
                </div>
            <?php } ?>
            <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
            }
            ?> 
            <?php
            if ($this->session->flashdata('success')) {

                echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->flashdata('success') . '</strong>
               </div>';
            }
            ?>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Add New NRN Detail</div>
                <div class="panel-body">
                    <form name="myForm" class="form-horizontal" id ="nrn_details_form" action="<?php echo base_url() ?>partner/add_nrn_details"  method="POST" enctype="multipart/form-data">
                        <legend>
                            <div class="row">
                                <fieldset>247 CRM Based Initial Data
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="booking_id" class="col-md-4">Call/JobNo/Booking Id *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Call/JobNo/Booking Id" type="text" class="form-control" name="booking_id" id="booking_id" required="" value="<?php echo (set_value('booking_id') != '' ) ? set_value('booking_id') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asm_name" class="col-md-4">Service ASM Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Service ASM Name" type="text" class="form-control" name="asm_name" id="asm_name" required="" value="<?php echo (set_value('asm_name') != '' ) ? set_value('asm_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="crm_name" class="col-md-4">CRM *</label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('crm_name') != '' ) ? set_value('crm_name') : '';
                                                    $extra = 'class="form-control" id="crm_name" required=""';
                                                    echo form_dropdown('crm_name', $crm_name, $selected, $extra);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Appliance * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected_service = (set_value('service_id') != '' ) ? set_value('service_id') : '';
                                                    //$extra = 'class="form-control" id="product_id" required=""';
                                                    //echo form_dropdown('product_id', $products, $selected, $extra);
                                                    ?>
                                                    <select class="form-control" id="service_id" name="service_id" required="">
                                                        <option disabled="" selected="">Select Appliance</option>

                                                    </select>
                                                    <span id="error_service_id" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Product * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected_product = (set_value('product_id') != '' ) ? set_value('product_id') : '';
                                                    //$extra = 'class="form-control" id="product_id" required=""';
                                                    //echo form_dropdown('product_id', $products, $selected, $extra);
                                                    ?>
                                                    <select class="form-control" id="product_id" name="product_id" required="">
                                                        <option disabled="" selected="">Select Product</option>

                                                    </select>
                                                    <span id="error_product_id" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_capacity" class="col-md-4">Product capacity *</label>
                                                <div class="col-md-6">
                                                    <select class="form-control" name="product_capacity" id="product_capacity" required="">
                                                        <option disabled="" selected="">Select Product capacity</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_model_no" class="col-md-4">Product Model No *</label>
                                                <div class="col-md-6">
                                                    <select class="form-control" name="product_model_no" id="product_model_no" required="">
                                                        <option disabled="" selected="">Select Product Model no</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="product_serial_no" class="col-md-4">Product Serial No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Product Serial No." type="text" class="form-control" name="product_serial_no" id="product_serial_no" required="" value="<?php echo (set_value('product_serial_no') != '' ) ? set_value('product_serial_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Owner * </label>
                                                <div class="col-md-6">

                                                    <?php
                                                    $selected = (set_value('owner') != '' ) ? set_value('owner') : '';
                                                    $extra = 'class="form-control" id="owner" required=""';
                                                    echo form_dropdown('owner', $owners, $selected, $extra);
                                                    ?>

                                                    <span id="error_owner" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="distributor_name" class="col-md-4">Dealer/Distributor Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Dealer/Distributor Name" type="text" class="form-control" name="distributor_name" id="distributor_name" required="" value="<?php echo (set_value('distributor_name') != '' ) ? set_value('distributor_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_name" class="col-md-4">ASF Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASF Name" type="text" class="form-control" name="asf_name" id="asf_name" required="" value="<?php echo (set_value('asf_name') != '' ) ? set_value('asf_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_location" class="col-md-4">ASF Location *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASF Location" type="text" class="form-control" name="asf_location" id="asf_location" required="" value="<?php echo (set_value('asf_location') != '' ) ? set_value('asf_location') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Physical Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('physical_status') != '' ) ? set_value('physical_status') : '';
                                                    $extra = 'class="form-control" id="physical_status" required=""';
                                                    echo form_dropdown('physical_status', $physical_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_owner" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="physical_status_remark_date" class="col-md-4">Physical Status remark Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="physical_status_remark_date" id="physical_status_remark_date" required="" autocomplete="off" value="<?php echo (set_value('physical_status_remark_date') != '' ) ? set_value('physical_status_remark_date') : ''; ?>"/>
                                                    <span id="error_physical_status_remark_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="asm_email" class="col-md-4">Service ASM E-mail *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Service ASM E-mail ID" type="email" class="form-control" name="asm_email" id="asm_email" value="<?php echo (set_value('asm_email') != '' ) ? set_value('asm_email') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="booking_date" class="col-md-4">Job date/Booking Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Job date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="booking_date" id="booking_date" required="" autocomplete="off"  value="<?php echo (set_value('booking_date') != '' ) ? set_value('booking_date') : ''; ?>"/>
                                                    <span id="error_booking_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="nrn_month" class="col-md-4">Month *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Month & Year" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="nrn_month" id="nrn_month" required="" autocomplete="off"  value="<?php echo (set_value('nrn_month') != '' ) ? set_value('nrn_month') : ''; ?>"/>
                                                    <span id="error_nrn_month" class="error" style="color: red;"></span>  
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tr_reporting_date" class="col-md-4">TR Reporting Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select TR Reporting date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="tr_reporting_date" id="tr_reporting_date" required="" autocomplete="off"  value="<?php echo (set_value('tr_reporting_date') != '' ) ? set_value('tr_reporting_date') : ''; ?>"/>
                                                    <span id="error_tr_reporting_date" class="error" style="color: red;"></span> 

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="purchase_date" class="col-md-4">Product Purchase Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Product Purchase date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="purchase_date" id="purchase_date" required="" autocomplete="off"  value="<?php echo (set_value('purchase_date') != '' ) ? set_value('purchase_date') : ''; ?>"/>
                                                    <span id="error_purchase_date" class="error" style="color: red;"></span> 

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Make * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('make') != '' ) ? set_value('make') : '';
                                                    $extra = 'class="form-control" id="make" required=""';
                                                    echo form_dropdown('make', $make, $selected, $extra);
                                                    ?>
                                                    <span id="error_make" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="customer_name" class="col-md-4">Customer Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Customer Name" type="text" class="form-control" name="customer_name" id="customer_name" required=""  value="<?php echo (set_value('customer_name') != '' ) ? set_value('customer_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="customer_location" class="col-md-4">Customer Location *</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter Customer location" class="form-control" name="customer_location" id="customer_location" required=""><?php echo (set_value('customer_location') != '' ) ? set_value('customer_location') : ''; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="state" class="col-md-4">State *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter State Name" type="text" class="form-control" name="state" id="state" required=""  value="<?php echo (set_value('state') != '' ) ? set_value('state') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="branch" class="col-md-4">Branch *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Branch Name" type="text" class="form-control" name="branch" id="branch" required=""  value="<?php echo (set_value('branch') != '' ) ? set_value('branch') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="problem_found" class="col-md-4">Problem Found *</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter Problem Found" type="text" class="form-control" name="problem_found" id="problem_found" required=""><?php echo (set_value('problem_found') != '' ) ? set_value('problem_found') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>As per Approval Mail (To be updated by Trivender)
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">Approval Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('approval_status') != '' ) ? set_value('approval_status') : '';
                                                    $extra = 'class="form-control" id="approval_status" required=""';
                                                    echo form_dropdown('approval_status', $approval_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_make" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="approval_rejection_date" class="col-md-4">Approval/Rejection Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Approval/Rejection date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="approval_rejection_date" id="approval_rejection_date" required="" autocomplete="off"  value="<?php echo (set_value('approval_rejection_date') != '' ) ? set_value('approval_rejection_date') : ''; ?>"/>
                                                    <span id="error_approval_rejection_date" class="error" style="color: red;"></span> 

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="repair_cost" class="col-md-4">Repair Cost *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Repair Cost" type="text" class="form-control" name="repair_cost" id="repair_cost" required=""  value="<?php echo (set_value('repair_cost') != '' ) ? set_value('repair_cost') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Warranty Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('warranty_status') != '' ) ? set_value('warranty_status') : '';
                                                    $extra = 'class="form-control" id="warranty_status" required=""';
                                                    echo form_dropdown('warranty_status', $warranty_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_make" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Service Partner * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('service_partner') != '' ) ? set_value('service_partner') : '';
                                                    $extra = 'class="form-control" id="service_partner" required=""';
                                                    echo form_dropdown('service_partner', $service_partner, $selected, $extra);
                                                    ?>
                                                    <span id="error_service_partner" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_distributor_name" class="col-md-4">ASF/Distributor Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASF/Distributor Name" type="text" class="form-control" name="asf_distributor_name" id="asf_distributor_name" required=""  value="<?php echo (set_value('asf_distributor_name') != '' ) ? set_value('asf_distributor_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_distributor_contact_no" class="col-md-4">Contact No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASF/Distributor Contact No." type="text" class="form-control" name="asf_distributor_contact_no" id="asf_distributor_contact_no" required=""  value="<?php echo (set_value('asf_distributor_contact_no') != '' ) ? set_value('asf_distributor_contact_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_distributor_pickup_address" class="col-md-4">Pickup Address *</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter ASF/Distributor Pickup Address" type="text" class="form-control" name="asf_distributor_pickup_address" id="asf_distributor_pickup_address" required=""><?php echo (set_value('asf_distributor_pickup_address') != '' ) ? set_value('asf_distributor_pickup_address') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="hdpl_invoice_no" class="col-md-4">HDPL Direct Billing Point Invoice No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter HDPL Direct Billing Point Invoice No." type="text" class="form-control" name="hdpl_invoice_no" id="hdpl_invoice_no" required=""  value="<?php echo (set_value('hdpl_invoice_no') != '' ) ? set_value('hdpl_invoice_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="hdpl_point" class="col-md-4">HDPL Direct Billing Point *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter HDPL Direct Billing Point" type="text" class="form-control" name="hdpl_point" id="hdpl_point" required=""  value="<?php echo (set_value('hdpl_point') != '' ) ? set_value('hdpl_point') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="vendor_warranty_expire_month" class="col-md-4">Vendor Warranty Exipre Month *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Vendor Warranty Exipre Month" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="vendor_warranty_expire_month" id="vendor_warranty_expire_month" required="" autocomplete="off"  value="<?php echo (set_value('vendor_warranty_expire_month') != '' ) ? set_value('vendor_warranty_expire_month') : ''; ?>"/>
                                                    <span id="error_vendor_warranty_expire_month" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Action Plan by Commercial Team * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('action_plan') != '' ) ? set_value('action_plan') : '';
                                                    $extra = 'class="form-control" id="physical_status" required=""';
                                                    echo form_dropdown('action_plan', $action_plan, $selected, $extra);
                                                    ?>
                                                    <span id="error_action_plan" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="asf_distributor_pincode" class="col-md-4">Pin code *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASF/Distributor Pin code" type="text" class="form-control" name="asf_distributor_pincode" id="asf_distributor_pincode" required=""  value="<?php echo (set_value('asf_distributor_pincode') != '' ) ? set_value('asf_distributor_pincode') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="control_no" class="col-md-4">Control No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Control No." type="text" class="form-control" name="control_no" id="control_no" required=""  value="<?php echo (set_value('control_no') != '' ) ? set_value('control_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>To be updated by Jatin <br/>
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">Replacement Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('replacement_status') != '' ) ? set_value('replacement_status') : '';
                                                    $extra = 'class="form-control" id="replacement_status" required=""';
                                                    echo form_dropdown('replacement_status', $replacement_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_replacement_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Replacement With Accessory * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('replacement_with_accessory') != '' ) ? set_value('replacement_with_accessory') : '';
                                                    $extra = 'class="form-control" id="replacement_with_accessory" required=""';
                                                    echo form_dropdown('replacement_with_accessory', $replacement_with_accessory, $selected, $extra);
                                                    ?>
                                                    <span id="error_replacement_with_accessory" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_model" class="col-md-4">Replacement Model *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Replacement Model" type="text" class="form-control" name="replacement_model" id="replacement_model" required=""  value="<?php echo (set_value('replacement_model') != '' ) ? set_value('replacement_model') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_serial_no" class="col-md-4">Replacement Serial No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Replacement Serial No." type="text" class="form-control" name="replacement_serial_no" id="replacement_serial_no" required=""  value="<?php echo (set_value('replacement_serial_no') != '' ) ? set_value('replacement_serial_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Defective Pickup Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('defective_pickup_status') != '' ) ? set_value('defective_pickup_status') : '';
                                                    $extra = 'class="form-control" id="defective_pickup_status" required=""';
                                                    echo form_dropdown('defective_pickup_status', $defective_pickup_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_defective_pickup_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="defective_received_wh_location" class="col-md-4">Defective Received WH Location *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Defective Received WH Location" type="text" class="form-control" name="defective_received_wh_location" id="defective_received_wh_location" required="" value="<?php echo (set_value('defective_received_wh_location') != '' ) ? set_value('defective_received_wh_location') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="defective_receiving_date" class="col-md-4">Defective Receiving Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Defective Receiving Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="defective_receiving_date" id="defective_receiving_date" required="" autocomplete="off" value="<?php echo (set_value('defective_receiving_date') != '' ) ? set_value('defective_receiving_date') : ''; ?>"/>
                                                    <span id="error_defective_receiving_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">TR Status</label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('tr_status') != '' ) ? set_value('tr_status') : '';
                                                    $extra = 'class="form-control" id="tr_status" required=""';
                                                    echo form_dropdown('tr_status', $tr_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_tr_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="replacement_awb_no" class="col-md-4">Replacement AWB No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Replacement AWB No." type="text" class="form-control" name="replacement_awb_no" id="replacement_awb_no" required="" value="<?php echo (set_value('replacement_awb_no') != '' ) ? set_value('replacement_awb_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_courier_name" class="col-md-4">Courier Name *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Courier Name" type="text" class="form-control" name="replacement_courier_name" id="replacement_courier_name" required="" value="<?php echo (set_value('replacement_courier_name') != '' ) ? set_value('replacement_courier_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_dispatch_date" class="col-md-4">Replacement Dispatch Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Replacement Dispatch Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="replacement_dispatch_date" id="replacement_dispatch_date" required="" autocomplete="off"  value="<?php echo (set_value('replacement_dispatch_date') != '' ) ? set_value('replacement_dispatch_date') : ''; ?>"/>
                                                    <span id="error_replacement_dispatch_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_delivery_date" class="col-md-4">Replacement Delivery date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Replacement Delivery Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="replacement_delivery_date" id="replacement_delivery_date" required="" autocomplete="off"  value="<?php echo (set_value('replacement_delivery_date') != '' ) ? set_value('replacement_delivery_date') : ''; ?>"/>
                                                    <span id="error_replacement_delivery_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Replacement as per action Plan * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('replacement_action_plan') != '' ) ? set_value('replacement_action_plan') : '';
                                                    $extra = 'class="form-control" id="replacement_action_plan" required=""';
                                                    echo form_dropdown('replacement_action_plan', $replacement_action_plan, $selected, $extra);
                                                    ?>
                                                    <span id="error_replacement_action_plan" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="replacement_remarks" class="col-md-4">Remarks *</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter Remarks" type="text" class="form-control" name="replacement_remarks" id="replacement_remarks"> <?php echo (set_value('replacement_remarks') != '' ) ? set_value('replacement_remarks') : ''; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="srn_no" class="col-md-4">SRN No. *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter SRN No." type="text" class="form-control" name="srn_no" id="srn_no"  value="<?php echo (set_value('srn_no') != '' ) ? set_value('srn_no') : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>To be updated by alligned AKAI ASM
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">TR Vs. Physical receiving Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('tr_physical_receiving_status') != '' ) ? set_value('tr_physical_receiving_status') : '';
                                                    $extra = 'class="form-control" id="tr_physical_receiving_status" required=""';
                                                    echo form_dropdown('tr_physical_receiving_status', $tr_physical_receiving_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_tr_physical_receiving_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">If "NO" Mail to the Concerned person  from where gap received * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('gap_received') != '' ) ? set_value('gap_received') : '';
                                                    $extra = 'class="form-control" id="gap_received" required=""';
                                                    echo form_dropdown('gap_received', $gap_received, $selected, $extra);
                                                    ?>
                                                    <span id="error_gap_received" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="category_done_by_asm" class="col-md-4">Category done by (Alligned ASM Name) *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASM Name" type="text" class="form-control" name="category_done_by_asm" id="category_done_by_asm" required="" value="<?php echo (set_value('category_done_by_asm') != '' ) ? set_value('category_done_by_asm') : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">FCA Category after inspection or PDI-1 * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('fca_category_pdi1') != '' ) ? set_value('fca_category_pdi1') : '';
                                                    $extra = 'class="form-control" id="fca_category_pdi1" required=""';
                                                    echo form_dropdown('fca_category_pdi1', $fca_category_pdi1, $selected, $extra);
                                                    ?>
                                                    <span id="error_fca_category_pdi1" class="error" style="color: red;"></span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="category_after_inspection_date" class="col-md-4">Category after inspection Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Category after inspection Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="category_after_inspection_date" id="category_after_inspection_date" required="" autocomplete="off"  value="<?php echo (set_value('category_after_inspection_date') != '' ) ? set_value('category_after_inspection_date') : ''; ?>"/>
                                                    <span id="error_category_after_inspection_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="akai_asm_remarks" class="col-md-4">AKAI ASM Remarks</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter Remarks" type="text" class="form-control" name="akai_asm_remarks" id="akai_asm_remarks"><?php echo (set_value('akai_asm_remarks') != '' ) ? set_value('akai_asm_remarks') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>Final PDI Status
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdi2_done_by_asm" class="col-md-4">PDI-2 done by (Alligned ASM Name) *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter ASM Name" type="text" class="form-control" name="pdi2_done_by_asm" id="pdi2_done_by_asm" required=""  value="<?php echo (set_value('pdi2_done_by_asm') != '' ) ? set_value('pdi2_done_by_asm') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">FCA Category after inspection or PDI-2 * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('fca_category_pdi2') != '' ) ? set_value('fca_category_pdi2') : '';
                                                    $extra = 'class="form-control" id="fca_category_pdi2" required=""';
                                                    echo form_dropdown('fca_category_pdi2', $fca_category_pdi2, $selected, $extra);
                                                    ?>
                                                    <span id="error_fca_category_pdi2" class="error" style="color: red;"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="final_pdi_category_after_inspection_date" class="col-md-4">Category after inspection Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Category after inspection Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="final_pdi_category_after_inspection_date" id="final_pdi_category_after_inspection_date" required="" autocomplete="off"  value="<?php echo (set_value('final_pdi_category_after_inspection_date') != '' ) ? set_value('final_pdi_category_after_inspection_date') : ''; ?>"/>
                                                    <span id="error_final_pdi_category_after_inspection_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="final_pdi_akai_asm_remarks" class="col-md-4">Remarks</label>
                                                <div class="col-md-6">
                                                    <textarea placeholder="Enter Remarks" type="text" class="form-control" name="final_pdi_akai_asm_remarks" id="final_pdi_akai_asm_remarks"><?php echo (set_value('final_pdi_akai_asm_remarks') != '' ) ? set_value('final_pdi_akai_asm_remarks') : ''; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">Defective Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('final_defective_status') != '' ) ? set_value('final_defective_status') : '';
                                                    $extra = 'class="form-control" id="final_defective_status" required=""';
                                                    echo form_dropdown('final_defective_status', $final_defective_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_final_defective_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="final_defective_status_date" class="col-md-4">Date of Defective Status *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="final_defective_status_date" id="final_defective_status_date" required="" autocomplete="off" value="<?php echo (set_value('final_defective_status_date') != '' ) ? set_value('final_defective_status_date') : ''; ?>"/>
                                                    <span id="error_final_defective_status_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vendor_name" class="col-md-4">Vendor PDI status (Assigned Person Name) *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Name" type="text" class="form-control" name="vendor_name" id="vendor_name" required="" value="<?php echo (set_value('vendor_name') != '' ) ? set_value('vendor_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <legend>
                            <div class="row">
                                <fieldset>
                                    <div class="col-md-12">
                                        <br/>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-4">Vendor Reversal Status * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('vendor_reversal_status') != '' ) ? set_value('vendor_reversal_status') : '';
                                                    $extra = 'class="form-control" id="vendor_reversal_status" required=""';
                                                    echo form_dropdown('vendor_reversal_status', $vendor_reversal_status, $selected, $extra);
                                                    ?>
                                                    <span id="error_vendor_reversal_status" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="vendor_reversal_date" class="col-md-4">Vendor Reversal Date *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Select Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="vendor_reversal_date" id="vendor_reversal_date" required="" autocomplete="off"  value="<?php echo (set_value('vendor_reversal_date') != '' ) ? set_value('vendor_reversal_date') : ''; ?>"/>
                                                    <span id="error_vendor_reversal_date" class="error" style="color: red;"></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vendor_reversal_status_person_name" class="col-md-4">Vendor reversal PDI Status (Assigned Person Name by QC team) *</label>
                                                <div class="col-md-6">
                                                    <input placeholder="Enter Name" type="text" class="form-control" name="vendor_reversal_status_person_name" id="vendor_reversal_status_person_name" value="<?php echo (set_value('vendor_reversal_status_person_name') != '' ) ? set_value('vendor_reversal_status_person_name') : ''; ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Vendor Reversal Category * </label>
                                                <div class="col-md-6">
                                                    <?php
                                                    $selected = (set_value('vendor_reversal_category') != '' ) ? set_value('vendor_reversal_category') : '';
                                                    $extra = 'class="form-control" id="vendor_reversal_category" required=""';
                                                    echo form_dropdown('vendor_reversal_category', $vendor_reversal_category, $selected, $extra);
                                                    ?>
                                                    <span id="error_vendor_reversal_category" class="error" style="color: red;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </legend>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12 col-md-4 col-md-offset-4">
                                    <input type="hidden" name="brand" id="brand" value="<?php echo $brand; ?>"/>
                                    <input type="hidden" name="partner_type" id="partner_type" value="<?php echo $partner_type; ?>"/>
                                    <input type="hidden" name="partner_id" id="partner_id" value="<?php echo $partner_id; ?>"/>
                                    <button type="submit" class="btn btn-success" id="on_submit_btn">Submit</button>
                                    <button type="button" class="btn btn-default" id="btn_cancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#vendor_warranty_expire_month').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/YYYY'));
        });

        $('#vendor_warranty_expire_month').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
        $('#booking_date').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
            $('#nrn_month').val(picker.startDate.format('MM/YYYY'));
        });
        $('#booking_date').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $('#nrn_month').val('');
        });
        $('#tr_reporting_date, #purchase_date, #physical_status_remark_date,#replacement_dispatch_date,#defective_receiving_date,#category_after_inspection_date,#final_pdi_category_after_inspection_date,#vendor_reversal_date,#approval_rejection_date,#final_defective_status_date,#replacement_delivery_date').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });

        $('#booking_date, #tr_reporting_date, #purchase_date, #physical_status_remark_date,#replacement_dispatch_date,#defective_receiving_date,#category_after_inspection_date,#final_pdi_category_after_inspection_date,#vendor_reversal_date,#approval_rejection_date,#final_defective_status_date,#replacement_delivery_date').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        $('#vendor_warranty_expire_month').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: false, //date_before_15_days,
            maxDate: false, //'today',
            locale: {
                format: 'MM/YYYY'
            }
        });

        $('#booking_date,#tr_reporting_date, #purchase_date, #physical_status_remark_date,#replacement_dispatch_date,#defective_receiving_date,#category_after_inspection_date,#final_pdi_category_after_inspection_date,#vendor_reversal_date,#approval_rejection_date,#final_defective_status_date,#replacement_delivery_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: false, //date_before_15_days,
            maxDate: false, //'today',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        
        $('#replacement_action_plan').on('change', function () {
            if ($('#replacement_action_plan').val() == 'No') {
                $('#replacement_remark').val('Open Ended');
            } else {
                $('#replacement_remark').val('');
            }
        });
        $('#btn_cancel').on('click', function () {
            if (confirm('Do you want to discard the changes')) {
                window.location.href = '<?php echo base_url("partner/list_nrn_records") ?>';
            }
        });
        // Get booking data by sending booking id 
        $('#booking_id').on('change', function () {
            var _booking_id = $('#booking_id').val();
            if (_booking_id != '') {


                $.ajax({
                    type: 'GET',
                    url: '<?php echo base_url() . 'employee/NRN_TR/finduser?search_value=+'; ?>' + _booking_id,
                    dataType: 'json',
                    success: function (responce) {
                        if (responce) {
                            $('#customer_name').val(responce.Bookings[0].customername);
                            $('#customer_location').val(responce.Bookings[0].booking_address);
                            $('#state').val(responce.Bookings[0].state);
                            $('#distributor_name').val(responce.Bookings[0].service_centre_name);
                            $('#asf_name').val(responce.Bookings[0].primary_contact_name);
                            $('#physical_status option').each(function (val) {
                                //alert($(this).val());
                                if (responce.Bookings[0].current_status === $(this).val()) {
                                    $(this).attr('selected');
                                }
                            });
                            var dateAr = responce.Bookings[0].booking_date.split('-');
                            var newDate = dateAr[0] + '/' + dateAr[1] + '/' + dateAr[2];
                            var newMonth = dateAr[1] + '/' + dateAr[2];
                            $('#booking_date').data('daterangepicker').setStartDate(newDate);
                            $('#booking_date').val(newDate);
                            $('#nrn_month').val(newMonth);
                        }
                    }
                });
            }
        });

        $(document).ready(function () {
            var partner_type = $('#partner_type').val();
            var partner_id = $('#partner_id').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url('partner/get_appliances'); ?>',
                //dataType:'text/html',
                data: {partner_type: partner_type, partner_id: partner_id},
                success: function (responce) {
                    $('#service_id').html(responce);
                }
            });
            $('#service_id').on('change', function () {
                var partner_type = $('#partner_type').val();
                var partner_id = $('#partner_id').val();
                var brand = $('#brand').val();
                var service_id = $('#service_id').val();

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url('partner/getCategoryForService'); ?>',
                    //dataType:'text/html',
                    data: {service_id: service_id, brand: brand, partner_type: partner_type, partner_id: partner_id},
                    success: function (responce) {
                        $('#product_id').html(responce);
                    }
                });
            });

            $('#product_id').on('change', function () {
                var partner_type = $('#partner_type').val();
                var partner_id = $('#partner_id').val();
                var brand = $('#brand').val();
                var service_id = $('#service_id').val();
                var category = $('#product_id').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url('partner/getCapacityForCategory'); ?>',
                    //dataType:'text/html',
                    data: {service_id: service_id, brand: brand, partner_type: partner_type, partner_id: partner_id, category: category},
                    success: function (responce) {
                        $('#product_capacity').html(responce);
                    }
                });
            });

            $('#product_capacity').on('change', function () {
                var partner_type = $('#partner_type').val();
                var partner_id = $('#partner_id').val();
                var brand = $('#brand').val();
                var service_id = $('#service_id').val();
                var category = $('#product_id').val();
                var capacity = $('#product_capacity').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url('partner/getModelForService'); ?>',
                    //dataType:'text/html',
                    data: {service_id: service_id, brand: brand, partner_type: partner_type, partner_id: partner_id, category: category, capacity: capacity},
                    success: function (responce) {
                        $('#product_model_no').html(responce);
                    }
                });
            });
        });
    </script>   
