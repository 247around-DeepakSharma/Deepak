<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>
    .select2-container{
    width: 100%!important;
    }
    .select2-container .select2-selection--single{
    height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
    height: 31px;
    }
    .dataTables_length{
    width: 10%;
    }
    .col-md-1 {
    width: 6.333333%;
    }
    .form-horizontal .control-label {
    text-align: left;
    }    
    .isDisabled {
        pointer-events: none;
        color: currentColor;
        cursor: not-allowed;
        opacity: 0.5;
        text-decoration: none;
    }
    #print_warehouse_addr{
    background-color: blue;
    color: white;
    padding: 5px 4px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    text-decoration: none;
    }
</style>
<div class="right_col" role="main">
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <ul class="nav nav-tabs" role="tablist" >
            <li role="presentation" class="active"><a href="#onMsl" aria-controls="onMsl" role="tab" data-toggle="tab">Inventory On MSL</a></li>
            <li role="presentation" ><a href="#onBooking" class="<?php if(!empty($this->session->userdata('is_micro_wh')) && empty($this->session->userdata('is_wh'))){ echo 'isDisabled'; } ?>" aria-controls="onBooking" role="tab" data-toggle="tab">Inventory On Booking</a></li>
        </ul>
    </div>
</div>
<div class="tab-content" id="tab-content">
    <div role="tabpanel" class="tab-pane active" id="onMsl">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                   
                    <div class="x_content">
                        <div class="warehouse_print_address" style="display:none;">                                    
                            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                                Do You Want to Print Warehouse Address
                                <a href="#" id="print_warehouse_addr" target="_blank"> Print Warehouse Address </a>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>                                        
                            </div>
                        </div>
                        <div class="success_msg_div" style="display:none;">
                            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                                <strong><span id="success_msg"></span></strong>
                            </div>
                        </div>
                        <div class="error_msg_div" style="display:none;">
                            <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                                <strong><span id="error_msg"></span></strong>
                            </div>
                        </div>
                        <div class="form-box">
                            
                            <form id="spareForm" method="post" class="form-horizontal" novalidate="novalidate">
                                <div class="static-form-box">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice Date*</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Select Invoice Date" type="text" readonly=""   onkeydown="return false;"  class="form-control" name="dated" id="dated" required="" autocomplete="off"/>
                                            <span id="error_dated" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-2 control-label">Invoice Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Enter Invoice Number" type="text" class="form-control" name="invoice_id" id="invoice_id" required="" onblur="check_invoice_id(this.id, false)"/>
                                            <span id="error_invoice_id" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">Invoice Amount * </label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter Invoice Amount" type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="invoice_amount" required=""/>
                                            <span id="error_invoice_amount" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice File*  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 5 MB."><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input type="file" class="form-control" name="file" id="invoice_file" required=""/>
                                            <span id="error_invoice_file" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                      <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                        <label class="col-xs-2 control-label">Courier Name *</label>
                                        <div class="col-xs-4">
<!--                                            <input placeholder="Enter Courier Name" type="text" class="form-control" name="courier_name" id="courier_name" required=""/>-->
                                            <select class="form-control" name="courier_name" id="courier_name" required="">
                                                <option selected="" disabled="" value="">Select Courier Name</option>
                                                <?php foreach ($courier_details as $value1) { ?> 
                                                    <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span id="error_courier_name" class="error" style="color: red;"></span>
                                            <?php echo form_error('courier_name'); ?>
                                        </div>
                                        <label class="col-xs-2 control-label">AWB Number *</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="despatch_doc_no" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13" />
                                            <span id="error_despatch_doc_no" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Select Courier Shipment Date" readonly=""   onkeydown="return false;" type="text" class="form-control" name="courier_shipment_date" id="courier_shipment_date" autocomplete="off"/>
                                        </div>
                                        <label class="col-xs-2 control-label">Courier File</label>
                                        <div class="col-xs-4">
                                            <input type="file" class="form-control" name="courier_file" id="courier_file"/>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label class="col-xs-2 control-label">From GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Your GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="from_gst_number" id="from_gst_number" required="">
                                                <option value="" disabled="">Select From GST Number</option>
                                            </select>
                                            <span id="error_from_gst_number" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-2 control-label">To GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="247around GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <select class="form-control" name="to_gst_number" id="to_gst_number" required="">
                                                <option value="" disabled="">Select To GST Number</option>
                                            </select>
                                            <span id="error_to_gst_number" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-sm-2 control-label">247around Warehouse *</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <select class="form-control" name="wh_id" id="wh_id" required="">
                                                <option value="" disabled="">Select Warehouse</option>
                                            </select>
                                            <span id="error_wh_id" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="dynamic-form-box" id="appliance_details_id">
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <p class="text-center"><strong>Appliance</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <p class="text-center"><strong>Part Name</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <p class="text-center"><strong>Part Number</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                            <p class="text-center"><strong>Booking Id <small>(Optional)</small></strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <p class="text-center"><strong>Quantity</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <p class="text-center"><strong>Total Basic Price</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <p class="text-center"><strong>HSN Code</strong></p>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <p class="text-center"><strong>GST Rate</strong></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <input type="hidden" name="part[0][shippingStatus]" id="shippingStatus_0" value="1">
                                            <select class="form-control" name="part[0][service_id]" id="serviceId_0" required="" onchange="get_part_details(this.id)"></select>
                                            <span id="error_serviceId_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <select class="form-control" name="part[0][part_name]" id="partName_0" required="" onchange="get_part_details(this.id)"></select>
                                            <span id="error_partName_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <select class="form-control" name="part[0][part_number]" id="partNumber_0" ></select>
                                            <span id="error_partNumber_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                            <input type="hidden" class="form-control" name="part[0][booking_id]" id="booking_id_0" placeholder="Booking ID" onblur="check_booking_id(this.id)"/>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithOutDecimal" name="part[0][quantity]" id="quantity_0" min="1" placeholder="Quantity" required="" onblur="get_part_details(this.id)" />
                                            <span id="error_quantity_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithDecimal" onkeyup="validateDecimal(this.id, this.value);calculate_total_price()" name="part[0][part_total_price]" id="partBasicPrice_0" value="0" />
                                            <span id="error_partBasicPrice_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="text" class="form-control allowNumericWithOutDecimal" name="part[0][hsn_code]" id="partHsnCode_0" value="" />
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithOutDecimal" onkeyup="calculate_total_price()" name="part[0][gst_rate]" id="partGstRate_0" min="12" max="28" value=""/>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="hidden" class="form-control" name="part[0][inventory_id]" id="inventoryId_0" value=""/>
                                            <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <!-- The template for adding new field -->
                                    <div class="form-group hide" id="partTemplate">
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <input type="hidden" id="shippingStatus" value="1">
                                            <select class="form-control" id="service_id"  required="" onchange="get_part_details(this.id)"></select>
                                            <span id="error_service_id" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <select class="form-control" id="part_name"  required="" onchange="get_part_details(this.id)"></select>
                                            <span id="error_part_name" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2">
                                            <select class="form-control" id="part_number"></select>
                                            <span id="error_part_number" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                            <input type="hidden" class="form-control" id="booking_id"  placeholder="Booking ID" onblur="check_booking_id(this.id)"/>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithOutDecimal" id="quantity"  placeholder="Quantity" min="1" required="" onblur="get_part_details(this.id)" />
                                            <span id="error_quantity" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithDecimal part-total-price" id="part_total_price"  value="0" />
                                            <span id="error_part_total_price" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="text" class="form-control allowNumericWithOutDecimal" id="partHsnCode" value="" />
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="number" class="form-control allowNumericWithOutDecimal" id="partGstRate" value="" min="12" max="28" />
                                        </div>
                                        
                                        <div class="col-xs-12 col-sm-6 col-md-1">
                                            <input type="hidden" class="form-control" id="inventory_id"  value=""/>
                                            <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="pull-right" style="margin-right:15px;">
                                                <strong>
                                                Total Price : <span id="total_spare_invoice_price">0</span>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-4 col-md-offset-5">
                                            <input type="hidden" class="form-control" id="partner_id"  name="partner_id" value="<?php echo $this->session->userdata('partner_id');?>"/>
                                            <input type="hidden" class="form-control" id="partner_name"  name="partner_name" value="<?php echo $this->session->userdata('partner_name');?>"/>
                                            <input type="hidden" class="form-control" id="wh_name"  name="wh_name" value=""/>
                                            <input type="hidden" name="invoice_tag" value="<?php echo MSL; ?>">
                                            <input type="hidden" name="transfered_by" value="<?php echo MSL_TRANSFERED_BY_PARTNER; ?>">
                                            <input type="hidden" id="is_defective_part_return_wh" name="is_defective_part_return_wh" value="<?php echo $is_defective_part_return_wh; ?>"/>
                                            <input type="hidden" id="confirmation" value="0">
                                            <input type="hidden" id="requested_appliance_count" value="">
                                            <button type="submit" class="btn btn-success" id="submit_btn" name="submit_btn">Preview</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="onBooking">
        <form novalidate="novalidate" id="onBookingspareForm" method="post" class="form-horizontal"  action="javascript:void(0)" >
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="form-box">
                                <div class="static-form-box">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice Date*</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Select Invoice Date" readonly=""   onkeydown="return false;"  type="text" class="form-control" name="dated" id="on_invoice_date" required="" autocomplete="off"/>
                                            <span id="error_on_invoice_date" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-2 control-label">Invoice Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Enter Invoice Number" type="text" class="form-control" name="invoice_id" id="on_invoice_number" required="" onblur="check_invoice_id(this.id, true)"/>
                                            <span id="error_on_invoice_number" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">Invoice Amount * </label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter Invoice Amount" type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="on_invoice_amount" required=""/>
                                            <span id="error_on_invoice_amount" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice File*  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 5 MB."><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input type="file" class="form-control" name="file" id="on_invoice_file" required=""/>
                                            <span id="error_on_invoice_file" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                        <label class="col-xs-2 control-label">Courier Name *</label>
                                        <div class="col-xs-4">
<!--                                            <input placeholder="Enter Courier Name" type="text" class="form-control" name="courier_name" id="on_courier_name" required=""/>-->
                                            <select class="form-control" name="courier_name" id="on_courier_name" required="">
                                                <option selected="" disabled="" value="">Select Courier Name</option>
                                                <?php foreach ($courier_details as $value1) { ?> 
                                                    <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span id="error_on_courier_name" class="error" style="color: red;"></span>
                                            <?php echo form_error('courier_name'); ?>
                                        </div>
                                        <label class="col-xs-2 control-label">AWB Number *</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="on_despatch_doc_no" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13" />
                                            <span id="error_on_despatch_doc_no" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Select Courier Shipment Date"   readonly=""   onkeydown="return false;"   type="text" class="form-control" name="courier_shipment_date" id="on_courier_shipment_date" autocomplete="off"/>
                                        </div>
                                        <label class="col-xs-2 control-label">Courier File</label>
                                        <div class="col-xs-4">
                                            <input type="file" class="form-control" name="courier_file" id="on_courier_file"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">From GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Your GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-4">
                                            <select class="form-control" name="from_gst_number" id="on_from_gst_number" required="">
                                                <option value="" disabled="">Select From GST Number</option>
                                            </select>
                                            <span id="error_on_from_gst_number" class="error" style="color: red;"></span>
                                        </div>
                                        <label class="col-xs-4 col-sm-2 control-label">To GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="247around GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <select class="form-control" name="to_gst_number" id="on_to_gst_number" required="">
                                                <option value="" disabled="">Select To GST Number</option>
                                            </select>
                                            <span id="error_on_to_gst_number" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-4 col-sm-2 control-label">247around Warehouse *</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <select class="form-control" name="wh_id" id="on_wh_id" required="">
                                                <option value="" disabled="">Select Warehouse</option>
                                            </select>
                                            <span id="error_on_wh_id" class="error" style="color: red;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12" id="booking_duplicate">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Enter Booking ID</h2>
                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="form-box">
                                <div class="static-form-box">
                                    <div class="form-group">
                                        <label class="col-xs-4 col-sm-2 control-label">Booking ID *</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input  placeholder="Enter Booking ID" onblur="bookingBlur('0')"  type="text" class="form-control searchbooking_id" id="onbookingid_0" required="" autocomplete="off" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13" />
                                            <span id="error_onbookingid_0" class="error" style="color: red;"></span>
                                        </div>
                                        <div class="col-xs-8 col-sm-4">
                                            <button type="button" onclick="search_booking_details('0')"  id="searchbookingid_0" class="btn btn-default searchbooking" >Search</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div id="sparelineitem_0" class="sparelinetem">
                        </div>
                    </div>
                </div>
            </div>
                <div class="dynamic-form-box hide" id="template">
                    <div class="clone">
                        <div class="col-md-12 col-sm-12 col-xs-12" >
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2>Enter Booking ID</h2>
                                    <div class="col-xs-4 col-sm-4 pull-right">
                                        <button type="button" class="btn btn-default pull-right onremoveButton"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="form-box">
                                        <div class="static-form-box">
                                            <div class="form-group">
                                                <label class="col-xs-4 col-sm-2 control-label">Booking ID *</label>
                                                <div class="col-xs-8 col-sm-4">
                                                    <input placeholder="Enter Booking ID" type="text" class="form-control searchbooking_id" id="onbookingid" required="" autocomplete="off" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13" />
                                                    <span id="error_onbookingid" class="error" style="color: red;"></span>
                                                </div>
                                                <div class="col-xs-8 col-sm-4">
                                                    <button type="button"  id="searchbookingid" class="btn btn-default searchbooking" >Search</button>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div id="sparelineitem">
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-offset-4">
                        <input type="hidden" class="form-control" id="on_partner_id"  name="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>"/>
                        <input type="hidden" class="form-control" id="on_partner_name"  name="partner_name" value="<?php echo $this->session->userdata('partner_name'); ?>"/>
                        <input type="hidden" class="form-control" id="on_wh_name"  name="wh_name" value=""/>
                        <input type="hidden" name="invoice_tag" value="<?php echo IN_WARRANTY; ?>">
                        <input type="hidden" name="transfered_by" value="<?php echo MSL_TRANSFERED_BY_PARTNER; ?>">
                        <button type="button" class="btn btn-default onaddButton">Add Booking</button>
                        <button type="submit" class="btn btn-success" id="on_submit_btn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <!--Modal start [ send spare parts list ]-->
      <div id="map_appliance_model" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg" style="width: 90%;">
              <div class="modal-content">
                  <button type="button" class="close btn-primary" style="margin: 6px 10px;" data-dismiss="modal">×</button>
                  <div class="modal-header">
                      <h4 class="modal-title">Send MSL Details To <strong id="modal_title_action"></strong> </h4>
                  </div>
                  <div class="modal-body" style="margin-right: -400px;">
                          <form class="form-horizontal">
                              <div id="clone_id" style="text-align: center;"></div>
                              <div class="modal-footer" style="margin-right: 389px;text-align: center;">
                                  <input type="hidden" id="mapped_model_table_id">
                                  <button type="button" class="btn btn-success" id="sumit_msl" name="sumit_msl">Submit</button>
                                  <button type="button" class="btn btn-default" onclick="submit_btn.disabled = false;sumit_msl.disabled = false;" data-dismiss="modal">Cancel</button>
                              </div>
                          </form>
                  </div>
              </div>
          </div>
      </div>
    <!--Modal end--> 
    
<script>
    
    $("#wh_id").on('change',function(){
        var wh_name = $("#wh_id option:selected").text();
        $("#modal_title_action").html(wh_name);
    });

    $("#sumit_msl").click(function(){
        $("#sumit_msl,#submit_btn").attr('disabled',true);
        $("#confirmation").val('1');
        $("#spareForm").submit();
    });
    
    $("input:text, input:file, select").on('change',function(){
        $("#sumit_msl,#submit_btn,#on_submit_btn").attr('disabled',false);
        $('span.error').text('');
        $(this).css('border-color','#ccc');
    });
    
    $("input:text").on('input',function(){
        $("#sumit_msl,#submit_btn,#on_submit_btn").attr('disabled',false);
        $('span.error').text('');
        $(this).css('border-color','#ccc');
    });
    
    $("input:text").on('click',function(){
        $("#sumit_msl,#submit_btn,#on_submit_btn").attr('disabled',false);
        $('span.error').text('');
        $(this).css('border-color','#ccc');
    });
    
    $('#submit_btn').click(function(){
        $("#sumit_msl").attr('disabled',false);
    });
    
    var date_before_15_days = new Date();
    date_before_15_days.setDate(date_before_15_days.getDate()-15);


    var onBookingIndex = 0;
    var is_valid_booking = true;
    $(document).ready(function () {
        
        partIndex = 0;
        $('#wh_id').select2({
            placeholder:"Select Warehouse"
        });
        $('#on_wh_id').select2({
            placeholder:"Select Warehouse"
        });
        $('#serviceId_0').select2({
            placeholder:'Select Appliance'
        });
        
        $('#partName_0').select2({
            placeholder:'Select Part Name'
        });
        $('#partNumber_0').select2({
            placeholder:'Select Part Number'
        });
        
        $('#from_gst_number,on_from_gst_number').select2({
            placeholder:'Select From GST Number'
        });
        
        $('#to_gst_number,on_to_gst_number').select2({
            placeholder:'Select To GST Number'
        });
        
        get_vendor();
        get_vendor_by_booking_id();
        get_appliance(0);
        get_partner_gst_number();
        get_247around_wh_gst_number();
        
        $('[data-toggle="popover"]').popover(); 
        $('#dated').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: date_before_15_days,
            maxDate:'today',
            locale:{
                format: 'YYYY-MM-DD'
            }
        });
        
        $('#on_invoice_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: date_before_15_days,
            maxDate:'today',
            locale:{
                format: 'YYYY-MM-DD'
            }
        });
    
        $('#dated').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    
        $('#dated').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#on_invoice_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    
        $('#on_invoice_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#courier_shipment_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: date_before_15_days,
            maxDate:'today',
            locale:{
                format: 'YYYY-MM-DD'
            }
        });
    
        $('#courier_shipment_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    
        $('#courier_shipment_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#on_courier_shipment_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: date_before_15_days,
            maxDate:'today',
            locale:{
                format: 'YYYY-MM-DD'
            }
        });
    
        $('#on_courier_shipment_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    
        $('#on_courier_shipment_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $(".allowNumericWithDecimal").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
              // Allow: Ctrl+A,Ctrl+C,Ctrl+V, Command+A
              ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
              // Allow: home, end, left, right, down, up
              (e.keyCode >= 35 && e.keyCode <= 40)) {
              // let it happen, don't do anything
              return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
              e.preventDefault();
            }
        });
        
        $(".allowNumericWithOutDecimal").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter
            if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 190]) !== -1 ||
              // Allow: Ctrl+A,Ctrl+C,Ctrl+V, Command+A
              ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
              // Allow: home, end, left, right, down, up
              (e.keyCode >= 35 && e.keyCode <= 40)) {
              // let it happen, don't do anything
              return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
              e.preventDefault();
            }
        });
        
        $("#spareForm").on('submit', function(e) {
            e.preventDefault();
            $("#submit_btn").attr('disabled',true);
            var isvalid = check_validation();//$("#spareForm").valid();
            var flag = true;
            if (isvalid) {
                var wh_name = $('#wh_id option:selected').text();
                $('#wh_name').val(wh_name);
                
                $(".part-total-price").each(function(i) {
                    if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                        validateDecimal('partBasicPrice_'+i,$('#partBasicPrice_'+i).val());

                        var count = $("#requested_appliance_count").val();
                        if(count == ''){
                            $("#requested_appliance_count").val(i);
                        }

                        if(Number($('#partBasicPrice_'+i).val()) == 0){
                            showConfirmDialougeBox('Please enter total basic price', 'warning');
                            $('#partBasicPrice_'+i).addClass('text-danger');
                            flag = false;
                            return false;
                        }

                        if(Number($('#partHsnCode_'+i).val()) === ""){
                            showConfirmDialougeBox('Please enter HSN Code', 'warning');
                            $('#partHsnCode_'+i).addClass('text-danger');
                            flag = false;
                            return false;
                        }

                        if(Number($('#partGstRate_'+i).val()) === ""){
                            showConfirmDialougeBox('Please enter Gst Rate', 'warning');
                            $('#partGstRate_'+i).addClass('text-danger');
                            flag = false;
                            return false;
                        }
//                        else {
//                            if((Number($('#partGstRate_'+i).val()) !== 5) && (Number($('#partGstRate_'+i).val()) !== 12) && (Number($('#partGstRate_'+i).val()) !== 18) && (Number($('#partGstRate_'+i).val()) !== 28) ){
//                                showConfirmDialougeBox('Invalid Gst Rate', 'warning');
//                                $('#partGstRate_'+i).addClass('text-danger');
//                                flag = false;
//                                return false;
//                            }
//                        }
                    }
                });
                
                var entered_invoice_amt = Number($('#invoice_amount').val());
                var our_invoice_amt = Number($('#total_spare_invoice_price').text());
                if((our_invoice_amt >= entered_invoice_amt - 10) && (our_invoice_amt <= entered_invoice_amt + 10) ){
                    $('#invoice_amount').css('border','1px solid #ccc');
                    $('#total_spare_invoice_price').removeClass('text-danger');

                    /* Open Modal */
                    $("#clone_id").empty();
                    $('#appliance_details_id').clone(true).appendTo('#clone_id');
                    $('#clone_id .form-control').each(function(){
                    $(this).attr("readonly","readonly");
                    });
                    $("#clone_id .select2-selection__rendered").css('background','#eee');
                    $("#clone_id .addButton").hide();
                    $("#clone_id .removeButton").hide(); 
                    if(flag == true){
                        $('#map_appliance_model').modal('toggle');
                    }

                    var c_status = $("#confirmation").val();
                    if((c_status !='')&& (c_status == '1')){
                        showConfirmDialougeBox('Are you sure you want to submit ?', 'info');
                    }
                    
                }else{
                    showConfirmDialougeBox('Amount of invoice does not match with total price', 'warning');
                    $('#invoice_amount').css('border','1px solid red');
                    $('#total_spare_invoice_price').addClass('text-danger');
                    return false;
                }
            }
        });
        
        // Add button click handler
        $('#spareForm').on('click', '.addButton', function () {
            partIndex++;
            var $template = $('#partTemplate'),
                $clone = $template
                        .clone()
                        .removeClass('hide')
                        .removeAttr('id')
                        .attr('data-book-index', partIndex)
                        .insertBefore($template);
    
            // Update the name attributes
            $clone
                .find('[id="shippingStatus"]').attr('name', 'part[' + partIndex + '][shippingStatus]').attr('id','shippingStatus_'+partIndex).end()
                .find('[id="service_id"]').attr('name', 'part[' + partIndex + '][service_id]').attr('id','serviceId_'+partIndex).select2({placeholder:'Select Appliance'}).end()
                .find('[id="error_service_id"]').attr('id','error_serviceId_'+partIndex).end()
                .find('[id="part_name"]').attr('name', 'part[' + partIndex + '][part_name]').attr('id','partName_'+partIndex).select2({placeholder:'Select Part Name'}).end()
                .find('[id="error_part_name"]').attr('id','error_partName_'+partIndex).end()
                .find('[id="part_number"]').attr('name', 'part[' + partIndex + '][part_number]').attr('id','partNumber_'+partIndex).select2({placeholder:'Select Part Number'}).end()
                .find('[id="error_part_number"]').attr('id','error_partNumber_'+partIndex).end()
                .find('[id="booking_id"]').attr('name', 'part[' + partIndex + '][booking_id]').attr('id','bookingId_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').attr('id','quantity_'+partIndex).end()
                .find('[id="error_quantity"]').attr('id','error_quantity_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventoryId_'+partIndex).end()
                .find('[id="partGstRate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','partGstRate_'+partIndex).attr('onkeyup','calculate_total_price()').end()
                .find('[id="partHsnCode"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','partHsnCode_'+partIndex).end()
                .find('[id="part_total_price"]').attr('name', 'part[' + partIndex + '][part_total_price]').attr('id','partBasicPrice_'+partIndex).attr('onkeyup','validateDecimal(this.id, this.value);calculate_total_price()').end()
                .find('[id="error_part_total_price"]').attr('id','error_partBasicPrice_'+partIndex).end();
            get_appliance(partIndex);
        })
    
        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.form-group'),
                index = $row.attr('data-part-index');
                partIndex = partIndex -1;
            $row.remove();
            calculate_total_price();
        });
    });
    
    function display_message(input_id, error_id, color,message){
        document.getElementById(input_id).style.borderColor = color;
        document.getElementById(error_id).innerHTML = message;
    }

    function check_validation(){
        var flag=true;

        if($.trim($("#dated").val()) === ""){
            display_message("dated","error_dated","red","Please Enter Invoice Date");
            flag=false;
             return false;
        } else {
            display_message("dated","error_dated","green","");
            flag=true;
        }

        if($.trim($("#invoice_id").val()) === ""){
            display_message("invoice_id","error_invoice_id","red","Please Enter Invoice Number");
            flag=false;
             return false;
        } else {
            display_message("invoice_id","error_invoice_id","green","");
            flag=true;
        }

        if($.trim($("#invoice_amount").val()) === ""){
            display_message("invoice_amount","error_invoice_amount","red","Please Enter Invoice Amount");
            flag=false;
             return false;
        } else {
            display_message("invoice_amount","error_invoice_amount","green","");
            flag=true;
        }

        if($.trim($("#invoice_file").val()) === ""){
            display_message("invoice_file","error_invoice_file","red","Please Enter Invoice File");
            flag=false;
             return false;
        } else {
            display_message("invoice_file","error_invoice_file","green","");
            flag=true;
        }

        if($.trim($("#courier_name").val()) === ""){
            display_message("courier_name","error_courier_name","red","Please Enter Courier Name");
            flag=false;
             return false;
        } else {
            display_message("courier_name","error_courier_name","green","");
            flag=true;
        }

        if($.trim($("#despatch_doc_no").val()) === ""){
            display_message("despatch_doc_no","error_despatch_doc_no","red","Please Enter AWB Number");
            flag=false;
             return false;
        } else {
            display_message("despatch_doc_no","error_despatch_doc_no","green","");
            flag=true;
        }

        if($.trim($("#from_gst_number").val()) === ""){
            display_message("from_gst_number","error_from_gst_number","red","Please Enter From GST Number");
            flag=false;
             return false;
        } else {
            display_message("from_gst_number","error_from_gst_number","green","");
            flag=true;
        }

        if($.trim($("#to_gst_number").val()) === ""){
            display_message("to_gst_number","error_to_gst_number","red","Please Enter To GST Number");
            flag=false;
             return false;
        } else {
            display_message("to_gst_number","error_to_gst_number","green","");
            flag=true;
        }

        if($.trim($("#wh_id").val()) === ""){
            display_message("wh_id","error_wh_id","red","Please Enter 247around Warehouse");
            flag=false;
             return false;
        } else {
            display_message("wh_id","error_wh_id","green","");
            flag=true;
        }

        for(var i=0;i<=partIndex;i++) {
            if($.trim($('#serviceId_'+i).val()) === ""){
                display_message("serviceId_"+i,"error_serviceId_"+i,"red","Please Enter Appliance");
                flag=false;
                 return false;
            } else {
                display_message("serviceId_"+i,"error_serviceId_"+i,"green","");
                flag=true;
            }

            if($.trim($('#partName_'+i).val()) === ""){
                display_message("partName_"+i,"error_partName_"+i,"red","Please Enter Part Name");
                flag=false;
                 return false;
            } else {
                display_message("partName_"+i,"error_partName_"+i,"green","");
                flag=true;
            }

            if($.trim($('#partNumber_'+i).val()) === ""){
                display_message("partNumber_"+i,"error_partNumber_"+i,"red","Please Enter Part Number");
                flag=false;
                 return false;
            } else {
                display_message("partNumber_"+i,"error_partNumber_"+i,"green","");
                flag=true;
            }

            if($.trim($('#quantity_'+i).val()) === ""){
                display_message("quantity_"+i,"error_quantity_"+i,"red","Please Enter Quantity");
                flag=false;
                 return false;
            } else {
                display_message("quantity_"+i,"error_quantity_"+i,"green","");
                flag=true;
            }

            if($.trim($('#partBasicPrice_'+i).val()) === ""){
                display_message("partBasicPrice_"+i,"error_partBasicPrice_"+i,"red","Please Enter Total Basic Price");
                flag=false;
                 return false;
            } else {
                display_message("partBasicPrice_"+i,"error_partBasicPrice_"+i,"green","");
                flag=true;
            }
        }

        return flag;
    }

    function showConfirmDialougeBox(title,type){
        if(type === 'info'){
            swal({
            title: title,
            type: type,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
            function(isConfirm) {
              if (isConfirm) {
                $("#submit_btn").attr('disabled',true);
                 ajax_call();
              } else {
                $("#submit_btn").attr('disabled',false);
                $("#confirmation").val('0');
              }
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
 
    function ajax_call(){
        $('#sumit_msl,#submit_btn').attr('disabled',true);
        $('#submit_btn').html("<i class='fa fa-spinner fa-spin'></i> Processing...");
    
        //Serializing all For Input Values (not files!) in an Array Collection so that we can iterate this collection later.
        var params = $('#spareForm').serializeArray();
        
        //Getting Invoice Files Collection
        var invoice_files = $("#invoice_file")[0].files;
    
        //Getting Courier Files Collection
        var courier_file = $("#courier_file")[0].files;
    
        //Declaring new Form Data Instance  
        var formData = new FormData();
        
        var is_micro = $("#wh_id").find(':selected').attr('data-warehose');
        formData.append("is_wh_micro", is_micro);
    
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < invoice_files.length; i++) {
            formData.append('invoice_file', invoice_files[i]);
        }
    
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
         for (var i = 0; i < courier_file.length; i++) {
             formData.append('courier_file', courier_file[i]);
        }
        //Now Looping the parameters for all form input fields and assigning them as Name Value pairs. 
        $(params).each(function (index, element) {
            formData.append(element.name, element.value);
        });
    
        $("#spareForm")[0].reset();
        $("#spareForm").find('input:text, input:file, select').val('');
        $.ajax({
            method:"POST",
            url:"<?php echo base_url();?>employee/inventory/process_spare_invoice_tagging",
            data:formData,
            contentType: false,
            processData: false,
            success:function(response){
                //console.log(response);
                obj = JSON.parse(response);
                $('#sumit_msl,#submit_btn').attr('disabled',false);
                $('#submit_btn').html("Preview");
                if(obj.status){
                    swal("Thanks!", "Details updated successfully!", "success");
                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                    $('#success_msg').html(obj.message);
                    $("#spareForm")[0].reset();
                    $("#spareForm").find('input:text, input:file, select').val('');
                    $('#select2-from_gst_number-container').text('Select From GST Number');
                    $('#select2-from_gst_number-container').attr('title','Select From GST Number');
                    $('#select2-to_gst_number-container').text('Select To GST Number');
                    $('#select2-to_gst_number-container').attr('title','Select To GST Number');
                    $('#select2-wh_id-container').text('Select Warehouse');
                    $('#select2-wh_id-container').attr('title','Select Warehouse');
                    $('#select2-serviceId_0-container').text('Select Appliance');
                    $('#select2-serviceId_0-container').attr('title','Select Appliance');
                    $('#select2-partName_0-container').text('Select Part Name');
                    $('#select2-partName_0-container').attr('title','Select Part Name');
                    $('#select2-partNumber_0-container').text('Select Part Number');
                    $('#select2-partNumber_0-container').attr('title','Select Part Number');
                    $('#total_spare_invoice_price').html('0');
                    $('span.error').text('');
                    $("#spareForm").find('input:text, input:file, select').css('border-color','#ccc');
                    $(".warehouse_print_address").css({'display':'block'});
                    $("#print_warehouse_addr").attr("href","<?php echo base_url();?>employee/inventory/print_warehouse_address/"+obj['partner_id']+"/"+obj['warehouse_id']+"/"+obj['total_quantity']+"");
                    $("#confirmation").val('0');
                }else{
                    showConfirmDialougeBox(obj.message, 'warning');
                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                    $('#error_msg').html(obj.message);
                    $("#confirmation").val('0');
                }
            }
        });
    }
        
    function get_vendor() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
            data:{'partner_id':<?php echo $this->session->userdata('partner_id'); ?>},
            success: function (response) {                
                $('#wh_id').html(response);
            }
        });
    }
    
    function get_vendor_by_booking_id() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{'is_wh' : 1},
            success: function (response) {                
                $("#on_wh_id").html(response);
            }
        });
    }
        
    function get_appliance(index){
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url() ?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id:'<?php echo $this->session->userdata('partner_id');?>'},
            success: function (response) {
                $('#serviceId_'+index).html(response);
            }
        });
    }
    
    function get_part_details(e){
        var element = e.split('_');
        var index = element[1];
        var part_element = element[0];
        switch(part_element){
            case 'serviceId':
                get_part_name(index);
                break;
            case 'partName':
                get_part_number(index);
                break;
            case 'quantity':
                get_part_price(index);
                break;
        }
    }
    
    function get_part_name(index){
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        if(partner_id){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_parts_name',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,is_option_selected:true},
                success: function (response) {
                    $('#partName_'+index).val('val', "");
                    $('#partName_'+index).val('Select Part Name').change();
                    $('#partName_'+index).html(response);
                    $('#inventoryId_'+index).val('');
                    $('#partBasicPrice_'+index).val('');
                    $('#partGstRate_'+index).val('');
                    $('#partHsnCode_'+index).val('');
                    $('#quantity_'+index).val('');
                }
            });
        }else{
            showConfirmDialougeBox('Please Select All Field', 'warning');
        }
    }
    
    function get_part_number(index){
   
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        var part_name = $('#partName_'+index).val();
        if(partner_id){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_parts_number',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,part_name:part_name,is_option_selected:true},
                success: function (response) {
                    console.log(response);
                    $('#partNumber_'+index).val('val', "");
                    $('#partNumber_'+index).val('Select Part Number').change();
                    $('#partNumber_'+index).html(response);
                    
                    $('#inventoryId_'+index).val('');
                    $('#partBasicPrice_'+index).val('');
                    $('#partGstRate_'+index).val('');
                    $('#partHsnCode_'+index).val('');
                    $('#quantity_'+index).val('');
                }
            });
        }else{
            showConfirmDialougeBox('Please Select All Field', 'warning');
        }
    }
    
    function get_part_price(index){
        var booking_id = $('#booking_id_0');
        if(booking_id){
            check_booking_id('booking_id_0');
        }
        
        if(is_valid_booking){
            var partner_id = $('#partner_id').val();
            var service_id = $('#serviceId_'+index).val();
            var part_number = $('#partNumber_'+index).val();
            if(partner_id && service_id && part_number){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/inventory/get_inventory_price',
                    data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,part_number:part_number},
                    success: function (response) {
                        var obj = JSON.parse(response);
    
                        if(obj.inventory_id){
                            $('#submit_btn').attr('disabled',false);
                            var parts_total_price = Number($('#quantity_'+index).val()) * Number(obj.price);
                            $('#inventoryId_'+index).val(obj.inventory_id);
                            $('#partBasicPrice_'+index).val(parts_total_price.toFixed(2));
                            $('#partGstRate_'+index).val(obj.gst_rate);
                            $('#partHsnCode_'+index).val(obj.hsn_code);
                            
                            var total_spare_invoice_price = 0;
                            for(var i=0;i<=partIndex;i++) {
                                if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                                    total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
                                }
                            }
                            $('#total_spare_invoice_price').html(Number(total_spare_invoice_price.toFixed(2)));
                        }else{
                            showConfirmDialougeBox('Inventory Details not found for the selected combination', 'warning');
                            $('#submit_btn').attr('disabled',true);
                        }
    
                    }
                });
            }else{
                $('#quantity_'+index).val('');
                showConfirmDialougeBox('Please Select All Field', 'warning');
            }
        }else{
            showConfirmDialougeBox('Booking id not found', 'warning');
        }
        
    }
    
    function check_booking_id(id){
    
        var booking_id = $('#'+id).val().trim();
        if(booking_id){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>check_booking_id_exists/'+booking_id,
                data:{is_ajax:true},
                success:function(res){
                    //console.log(res);
                    var obj = JSON.parse(res);
                    if(obj.status === true){
                        $('#'+id).css('border','1px solid #ccc');
                        $('#on_submit_btn').attr('disabled',false);
                        is_valid_booking = true;
                    }else{
                        is_valid_booking = false;
                        $('#'+id).css('border','1px solid red');
                        $('#on_submit_btn').attr('disabled',true);
                        showConfirmDialougeBox('Booking id not found', 'warning');
                    }
                }
            });
        }else{
            is_valid_booking = true;
            $('#'+id).css('border','1px solid #ccc');
            $('#on_submit_btn').attr('disabled',false);
        }
    }
    
    function check_invoice_id(id, isOnBooking ){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){
            
            if( invoice_id.indexOf('/') !== -1 ){
                $('#'+id).css('border','1px solid red');
                if(isOnBooking){
                     $('#on_submit_btn').attr('disabled',true);
                } else {
                     $('#submit_btn').attr('disabled',true);
                }
                
               
                showConfirmDialougeBox("Use '-' in place of '/'", 'warning');
            }
            else{
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>check_invoice_id_exists/'+invoice_id,
                    data:{is_ajax:true},
                    success:function(res){
                        //console.log(res);
                        var obj = JSON.parse(res);
                        if(obj.status === true){
                            $('#'+id).css('border','1px solid red');
                            if(isOnBooking){
                               $('#on_submit_btn').attr('disabled',true);
                            } else {
                               $('#submit_btn').attr('disabled',true);
                            }
                            
                            showConfirmDialougeBox('Invoice number already exists', 'warning');
                        }else{
                            $('#'+id).css('border','1px solid #ccc');
                            if(isOnBooking){
                              $('#on_submit_btn').attr('disabled',false);
                            } else {
                               $('#submit_btn').attr('disabled',false);
                            }
                            
                        }
                    }
                });
            }
        }
    }
    
    function search_booking_details(count){
        var booking_id = $("#onbookingid_" + count).val();
        if(booking_id !== ""){
            $.ajax({
                method:'POST',
                beforeSend: function(){

                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                });

                    },
                url:'<?php echo base_url(); ?>employee/inventory/get_spare_line_item_for_tag_spare/'+booking_id +"/" + count,
                data:{is_ajax:true},
                success:function(res){
                  // console.log(res);
                    var obj = JSON.parse(res);
                    $('body').loadingModal('destroy');
                    if(obj.code === 247){
                        //onBookingIndex = (count + Number(obj.count) - 1);

                        $("#sparelineitem_"+count).html(obj.data);
                        $(".part_name").select2();
                    } else {
                        alert(obj.data);
                        return false;
                    }
                }
            });
        } else {
           alert("Please Enter Booking ID");
        }
        
        
    }
</script>
<script>
   
    $('#onBookingspareForm').on('click', '.onaddButton', function () {
           onBookingIndex++;
           var $template = $('#template'),
               $clone = $template
                       .clone()
                       .removeClass('hide')
                       .removeAttr('id')
                       .attr('data-book-index', onBookingIndex)
                       .insertBefore($template);
    
           // Update the name attributes
           $clone
               .find('[id="onbookingid"]').attr('id','onbookingid_'+onBookingIndex).attr('onblur', 'bookingBlur("'+onBookingIndex+'")').end()
               .find('[id="error_onbookingid"]').attr('id','error_onbookingid_'+onBookingIndex).end()
               .find('[id="sparelineitem"]').attr('id', 'sparelineitem_'+onBookingIndex).end()
               .find('[id="searchbookingid"]').attr('id', 'searchbookingid_'+onBookingIndex).attr('onclick','search_booking_details("'+ onBookingIndex+'")').end()
       })
    
       // Remove button click handler
       .on('click', '.onremoveButton', function () {
           var $row = $(this).parents('.clone'),
               index = $row.attr('data-part-index');
               onBookingIndex--;
           $row.remove();
       });
       
        function get_part_number_on_booking(index){
            var partner_id = $('#onpartnerId_'+index).val();
            var service_id = $('#onserviceId_'+index).val();
            var part_name = $('#onpartName_'+index).val();
            
            if(partner_id){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/inventory/get_part_number_data',
                    data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,part_name:part_name,is_option_selected:true},
                    success: function (response) {
                         console.log(response);
                        var obj = JSON.parse(response);
                       
                        $('#onpartNumber_'+index).val('val', "");
                        $('#onpartNumber_'+index).val('Select Part Number').change();
                        $('#onpartNumber_'+index).html(obj.option);
                        $('#oninventoryId_'+index).val(obj.inventory_id);
                        $('#onpartBasicPrice_'+index).val(obj.basic_price);
                        $('#onpartGstRate_'+index).val(obj.gst_rate);
                        $('#onpartHsnCode_'+index).val(obj.hsn_code);
                        $('#onspareType_'+index).val(obj.type);
                       
                        $('#ontotal_amount_'+index).val(obj.total_price);
                    }
                });
            }else{
                showConfirmDialougeBox('Please Select All Field', 'warning');
            }
        }
       
       function bookingBlur(count){
          var booking_id = $("onbookingid_"+count).val();
          if(booking_id === ''){
              $("#sparelineitem_"+count[1]).html("");
          }
          
            var booking_tmp = [];
            $(".searchbooking_id").each(function (i) {
                 
                 if ($(this).val() !== '') {
                     booking_tmp.push($(this).val());
                 }
            });
            
            temp = [];
            
            $.each(booking_tmp, function(key, value) {
                if ($.inArray(value, temp) === -1) {
                    temp.push(value);
                } else {
                    alert(value + " is a Duplicate Booking ID");
                    $("#onbookingid_"+count).val('');
                }
            });
       }
       
         
    function onchange_part_number(index){

        $('#oninventoryId_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-inventory_id'));
        $('#onpartBasicPrice_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-basic_price'));
        $('#onpartGstRate_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-gst_rate'));
        $('#onpartHsnCode_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-hsn_code'));
        $('#onspareType'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-type'));

        $('#ontotal_amount_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-inventory_id'));
    }
    
    $("#onBookingspareForm").on('submit', function(e) {
            e.preventDefault();
            $("#on_submit_btn").attr('disabled',true);
            var isvalid = check_booking_msl_validation();//$("#onBookingspareForm").valid();
            var flag = true;
            if (isvalid) {

                $(".onpartBasicPrice").each(function(i) {
                    validateDecimal('onpartBasicPrice_'+i,$('#onpartBasicPrice_'+i).val());
    
                    if(Number($('#onpartBasicPrice_'+i).val()) === 0){
                        onBookingshowConfirmDialougeBox('Please enter basic price', 'warning');
                        $('#onpartBasicPrice_'+i).addClass('text-danger');
                        flag = false;
                        return false;
                    }

                });
                
                
                $(".onpartGstRate").each(function(i) {
    
                    if(Number($('#onpartGstRate_'+i).val()) === ""){
                        onBookingshowConfirmDialougeBox('Please enter Gst Rate', 'warning');
                        $('#onpartGstRate_'+i).addClass('text-danger');
                        flag = false;
                        return false;
                    }
//                    else {
//                        if((Number($('#onpartGstRate_'+i).val()) !== 5) && (Number($('#onpartGstRate_'+i).val()) !== 12) && (Number($('#onpartGstRate_'+i).val()) !== 18) && (Number($('#onpartGstRate_'+i).val()) !== 28) ){
//                            onBookingshowConfirmDialougeBox('Invalid Gst Rate', 'warning');
//                            $('#onpartGstRate_'+i).addClass('text-danger');
//                            flag = false;
//                            return false;
//                        }
//                    }

                });
                
                $(".onpartHsnCode").each(function(i) {
    
                    if(Number($('#onpartHsnCode_'+i).val()) === ""){
                        onBookingshowConfirmDialougeBox('Please enter HSN Code', 'warning');
                        $('#onpartHsnCode_'+i).addClass('text-danger');
                        flag = false;
                        return false;
                    }

                });

                if(flag == true) {
                    var booking_id = $("#onbookingid_0").val();
                    if(booking_id !== ""){
                        var wh_name = $('#on_wh_id option:selected').text();
                        $('#on_wh_name').val(wh_name);
                        var entered_invoice_amt = Number($('#on_invoice_amount').val());
                        var our_invoice_amt = 0;
                        $(".total_spare_amount").each(function (i) {
                            if(Number($(this).val()) > 0){
                                var sh_id = this.id;
                                var split_id = sh_id.split('_');
                                var c = split_id[2];

                                if ($("#s_shippingStatus_" + c+":checked").val()) {
                                    var checked_shipped = $("#s_shippingStatus_" + c).val();
                                     if(Number(checked_shipped) === 1 ){
                                        our_invoice_amt += Number($(this).val());
                                     }
                                 }

                            }

                        });
                        if((our_invoice_amt >= entered_invoice_amt - 10) && (our_invoice_amt <= entered_invoice_amt + 10) ){

                            onBookingshowConfirmDialougeBox('Are you sure you want to submit ?', 'info');

                        }else{
                            onBookingshowConfirmDialougeBox('Amount of invoice does not match with total price', 'warning');

                            return false;
                        }
                    } else {
                        onBookingshowConfirmDialougeBox('Please Enter Booking ID', 'warning');

                        return false;
                    }
                }
            }
        });
    
    function submitBookingForm(){
        $('#on_submit_btn').attr('disabled',true);
        $('#on_submit_btn').html("<i class='fa fa-spinner fa-spin'></i> Processing...");
         //Serializing all For Input Values (not files!) in an Array Collection so that we can iterate this collection later.
        var params = $('#onBookingspareForm').serializeArray();
    
        //Getting Invoice Files Collection
        var invoice_files = $("#on_invoice_file")[0].files;
    
        //Getting Courier Files Collection
        var courier_file = $("#on_courier_file")[0].files;
    
        //Declaring new Form Data Instance  
        var formData = new FormData();
        var is_micro = $("#on_wh_id").find(':selected').attr('data-warehose');
        formData.append("is_wh_micro", is_micro);
    
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < invoice_files.length; i++) {
            formData.append('invoice_file', invoice_files[i]);
        }
    
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
         for (var i = 0; i < courier_file.length; i++) {
             formData.append('courier_file', courier_file[i]);
        }
        //Now Looping the parameters for all form input fields and assigning them as Name Value pairs. 
        $(params).each(function (index, element) {
            formData.append(element.name, element.value);
        });
        $("#onBookingspareForm")[0].reset();
        $("#onBookingspareForm").find('input:text, input:file, select').val('');
        $.ajax({
            method:"POST",
            url:"<?php echo base_url();?>employee/inventory/process_spare_invoice_tagging",
            data:formData,
            contentType: false,
            processData: false,
            success:function(response){
                //console.log(response);
                obj = JSON.parse(response);
                $('#on_submit_btn').attr('disabled',false);
                $('#on_submit_btn').html("Submit");
                if(obj.status){
                    swal("Thanks!", "Details updated successfully!", "success");
                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                    $('#success_msg').html(obj.message);
                    $("#onBookingspareForm")[0].reset();
                    $(".sparelinetem").html("");
                    location.reload();
                }else{
                    showConfirmDialougeBox(obj.message, 'warning');
                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                    $('#error_msg').html(obj.message);
                }
            }
        });
    }
    
    function onBookingshowConfirmDialougeBox(title,type){
        if(type === 'info'){
            swal({
            title: title,
            type: type,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
            function(){
                 $("#on_submit_btn").attr('disabled',true);
                 submitBookingForm();
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
    
    
    $('#onBookingspareForm').on('click', '.onspareaddButton', function () {
           onBookingIndex++;
            var b=  $(this).attr('data-count');
           var $template = $('#spare_line_template_'+b),
               $clone = $template
                       .clone()
                       .removeClass('hide')
                       .removeAttr('id')
                       .attr('data-book-index', onBookingIndex)
                       .insertBefore($template);
    
           // Update the name attributes
           $clone
               .find('[id="shipping_status_1"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','s_shippingStatus_'+onBookingIndex).attr("required", true).attr('class','shippingStatus').end()
               .find('[id="shipping_status_2"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','n_shippingStatus_'+onBookingIndex).attr("required", true).attr('class','shippingStatus').end()
               .find('[id="shipping_status_3"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','l_shippingStatus_'+onBookingIndex).attr("required", true).attr('class','shippingStatus').end()
               .find('[id="error_shippingStatus"]').attr('id','error_shippingStatus_'+onBookingIndex).end()
               .find('[id="onpartName"]').attr('name', 'part[' + onBookingIndex + '][part_name]').attr('id','onpartName_'+onBookingIndex).attr('onchange','get_part_number_on_booking("'+ onBookingIndex+'")').addClass('part_name').attr("required", true).end()
               .find('[id="error_onpartName"]').attr('id','error_onpartName_'+onBookingIndex).end()
               .find('[id="onpartBasicPrice"]').attr('name', 'part[' + onBookingIndex + '][part_total_price]').attr('id','onpartBasicPrice_'+onBookingIndex).attr('onkeyup','validateDecimal(this.id, this.value);booking_calculate_total_price('+onBookingIndex+')').addClass('onpartBasicPrice').end()
               .find('[id="error_onpartBasicPrice"]').attr('id','error_onpartBasicPrice_'+onBookingIndex).end()
               .find('[id="onquantity"]').attr('name', 'part[' + onBookingIndex + '][quantity]').attr('id','onquantity_'+onBookingIndex).end()
               .find('[id="onpartGstRate"]').attr('name', 'part[' + onBookingIndex + '][gst_rate]').attr('id','onpartGstRate_'+onBookingIndex).addClass('onpartGstRate').attr('onkeyup','booking_calculate_total_price('+onBookingIndex+')').end()
               .find('[id="onpartNumber"]').attr('name', 'part[' + onBookingIndex + '][part_number]').attr('id','onpartNumber_'+onBookingIndex).attr('onchange', 'onchange_part_number("'+onBookingIndex+'")').end()
               .find('[id="onpartHsnCode"]').attr('name', 'part[' + onBookingIndex + '][hsn_code]').attr('id','onpartHsnCode_'+onBookingIndex).addClass('onpartHsnCode').end()
               .find('[id="onbookingID"]').attr('name', 'part[' + onBookingIndex + '][booking_id]').attr('id','onbookingID'+onBookingIndex).end()
               .find('[id="onserviceId"]').attr('name', 'part[' + onBookingIndex + '][service_id]').attr('id','onserviceId_'+onBookingIndex).end()
               .find('[id="onpartnerId"]').attr('name', 'part[' + onBookingIndex + '][partner_id]').attr('id','onpartnerId_'+onBookingIndex).end()
               .find('[id="oninventoryId"]').attr('name', 'part[' + onBookingIndex + '][inventory_id]').attr('id','oninventoryId_'+onBookingIndex).end()
               .find('[id="onspareID"]').attr('name', 'part[' + onBookingIndex + '][spare_id]').attr('id', 'onspareID_'+onBookingIndex).end()
               .find('[id="ontotal_amount"]').attr('name', 'part[' + onBookingIndex + '][total]').attr('id', 'ontotal_amount_'+onBookingIndex).end()
               .find('[id="onrequestedInventoryId"]').attr('name', 'part[' + onBookingIndex + '][requested_inventory_id]').attr('id', 'onrequestedInventoryId_'+onBookingIndex).end()
               .find('[id="onspareType"]').attr('name', 'part[' + onBookingIndex + '][type]').attr('id', 'onspareType_'+onBookingIndex).end()
               
            $('#onpartName_'+onBookingIndex).select2();
            
       })
    
       // Remove button click handler
       .on('click', '.onspareremoveButton', function () {
           var $row = $(this).parents('.spare_clone'),
               index = $row.attr('data-part-index');
               onBookingIndex--;
           $row.remove();
       });

        function check_booking_msl_validation(){
            var flag=true;

            if($.trim($("#on_invoice_date").val()) === ""){
                display_message("on_invoice_date","error_on_invoice_date","red","Please Enter Invoice Date");
                flag=false;
                 return false;
            } else {
                display_message("on_invoice_date","error_on_invoice_date","green","");
                flag=true;
            }

            if($.trim($("#on_invoice_number").val()) === ""){
                display_message("on_invoice_number","error_on_invoice_number","red","Please Enter Invoice Number");
                flag=false;
                 return false;
            } else {
                display_message("on_invoice_number","error_on_invoice_number","green","");
                flag=true;
            }

            if($.trim($("#on_invoice_amount").val()) === ""){
                display_message("on_invoice_amount","error_on_invoice_amount","red","Please Enter Invoice Amount");
                flag=false;
                 return false;
            } else {
                display_message("on_invoice_amount","error_on_invoice_amount","green","");
                flag=true;
            }

            if($.trim($("#on_invoice_file").val()) === ""){
                display_message("on_invoice_file","error_on_invoice_file","red","Please Enter Invoice File");
                flag=false;
                 return false;
            } else {
                display_message("on_invoice_file","error_on_invoice_file","green","");
                flag=true;
            }

            if($.trim($("#on_courier_name").val()) === ""){
                display_message("on_courier_name","error_on_courier_name","red","Please Enter Courier Name");
                flag=false;
                 return false;
            } else {
                display_message("on_courier_name","error_on_courier_name","green","");
                flag=true;
            }

            if($.trim($("#on_despatch_doc_no").val()) === ""){
                display_message("on_despatch_doc_no","error_on_despatch_doc_no","red","Please Enter AWB Number");
                flag=false;
                 return false;
            } else {
                display_message("on_despatch_doc_no","error_on_despatch_doc_no","green","");
                flag=true;
            }

            if($.trim($("#on_from_gst_number").val()) === ""){
                display_message("on_from_gst_number","error_on_from_gst_number","red","Please Enter From GST Number");
                flag=false;
                 return false;
            } else {
                display_message("on_from_gst_number","error_on_from_gst_number","green","");
                flag=true;
            }

            if($.trim($("#on_to_gst_number").val()) === ""){
                display_message("on_to_gst_number","error_on_to_gst_number","red","Please Enter To GST Number");
                flag=false;
                 return false;
            } else {
                display_message("on_to_gst_number","error_on_to_gst_number","green","");
                flag=true;
            }

            if($.trim($("#on_wh_id").val()) === ""){
                display_message("on_wh_id","error_on_wh_id","red","Please Enter 247around Warehouse");
                flag=false;
                 return false;
            } else {
                display_message("on_wh_id","error_on_wh_id","green","");
                flag=true;
            }

            if($.trim($('#onbookingid_0').val()) === ""){
                display_message("onbookingid_0","error_onbookingid_0","red","Please Enter Booking ID");
                flag=false;
                 return false;
            } else {
                display_message("onbookingid_0","error_onbookingid_0","green","");
                flag=true;
            }

//            $('.shippingStatus').each(function(i) {
//            if(!$('#s_shippingStatus_'+i).is(':checked') && !$('#n_shippingStatus_'+i).is(':checked') && !$('#l_shippingStatus_'+i).is(':checked')) {
//                document.getElementById('error_shippingStatus_'+i).innerHTML = "Please Select 1 Shipping Status";
//                flag=false;
//                 return false;
//            } else {
//                document.getElementById('error_shippingStatus_'+i).innerHTML = "";
//                flag=true;
//            }
//        });

            for(var i=0;i<=onBookingIndex;i++) {
                if(!$('#s_shippingStatus_'+i).is(':checked') && !$('#n_shippingStatus_'+i).is(':checked') && !$('#l_shippingStatus_'+i).is(':checked')) {
                    document.getElementById('error_shippingStatus_'+i).innerHTML = "Please Select 1 Shipping Status";
                    flag=false;
                     return false;
                } else {
                    document.getElementById('error_shippingStatus_'+i).innerHTML = "";
                    flag=true;
                }

                if($.trim($('#onpartName_'+i).val()) === ""){
                    display_message("onpartName_"+i,"error_onpartName_"+i,"red","Please Select Part Name");
                    flag=false;
                     return false;
                } else {
                    display_message("onpartName_"+i,"error_onpartName_"+i,"green","");
                    flag=true;
                }

                if($.trim($('#onpartBasicPrice_'+i).val()) === ""){
                    display_message("onpartBasicPrice_"+i,"error_onpartBasicPrice_"+i,"red","Please Enter Basic Price");
                    flag=false;
                     return false;
                } else {
                    display_message("onpartBasicPrice_"+i,"error_onpartBasicPrice_"+i,"green","");
                    flag=true;
                }
            }

            return flag;
        }

       function calculate_total_price(){
           var total_spare_invoice_price = 0;
            for(var i=0;i<=partIndex;i++) {
                if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                    total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
                }
            }
            $('#total_spare_invoice_price').html(Number(total_spare_invoice_price.toFixed(2)));
       }
       
       function booking_calculate_total_price(id){
          
           var total_spare_invoice_price = Number($('#onpartBasicPrice_'+id).val()) + (Number($('#onpartBasicPrice_'+id).val()) * Number($('#onpartGstRate_'+id).val())/100);
           $('#ontotal_amount_'+id).val(Number(total_spare_invoice_price.toFixed(2)));
       }
       
       function get_partner_gst_number(){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_partner_gst_number',
                data:{partner_id:<?php echo $this->session->userdata('partner_id'); ?>},
                success: function (response) {
                    $("#from_gst_number").html(response);
                    $("#on_from_gst_number").html(response);
                }
            });
       }
       
       function get_247around_wh_gst_number(){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_247around_wh_gst_number',
                data:{partner_id:<?php echo $this->session->userdata('partner_id'); ?>},
                success: function (response) {
                    $("#to_gst_number").html(response);
                    $("#on_to_gst_number").html(response);
                }
            });
       }
   
    function validateDecimal(id,value) {
        var RE = /^\d+(?:\.\d{1,2})?$/
        if(($.trim(value) !== '') && !RE.test(value)){
           $('#error_'+id).text("Enter value upto 2 decimal places");
           $('#'+id).focus();
           $('#submit_btn').attr('disabled',true);
           $('#on_submit_btn').attr('disabled',true);
        }
        else {
            $('#error_'+id).text("");
            $('#submit_btn').attr('disabled',false);
            $('#on_submit_btn').attr('disabled',false);
        }
    }
</script>
