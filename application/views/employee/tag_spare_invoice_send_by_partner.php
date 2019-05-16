<style>
    .select2-container .select2-selection--single{
    height: 32px;
    }
    .form-horizontal .control-label{
    text-align: left;
    }
    .col-md-2 {
    width: 12.666667%;
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
<!-- page content -->
<div id="page-wrapper" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active"><a href="#onMsl" aria-controls="onMsl" role="tab" data-toggle="tab">Inventory On MSL</a></li>
                <li role="presentation" ><a href="#onBooking" aria-controls="onBooking" role="tab" data-toggle="tab">Inventory On Booking</a></li>
            </ul>
        </div>
    </div>
    <div class="tab-content" id="tab-content">
        <div role="tabpanel" class="tab-pane active" id="onMsl">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel" style="margin-top: 0px;">
                       
                        <div class="x_content">
                            <div class="loader"></div>                            
                            <div class="form-box">
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
                                <form id="spareForm" method="post" class="form-horizontal" novalidate="novalidate">
                                    <div class="static-form-box">
                                        <div class="form-group">
                                            <label class="col-xs-4 col-sm-2 control-label">Partner *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <select class="form-control" name="partner_id" id="partner_id" required=""></select>
                                            </div>
                                            <label class="col-xs-4 col-sm-2 control-label"><?php if(!$saas){ ?>247around<?php }?> Warehouses *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <select class="form-control" name="wh_id" id="wh_id">
                                                    <option value="" disabled="">Select Warehouse</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">                                            
                                                                                        
                                            <label class="col-xs-4 col-sm-2 control-label">Invoice Date *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <input placeholder="Select Date" type="text" class="form-control"  readonly=""  onkeydown="return false;"  name="dated" id="dated" autocomplete="off"/>
                                                <input type="hidden" name="invoice_tag" value="<?php echo MSL; ?>">
                                            </div>
                                             <label class="col-xs-2 control-label">Invoice Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                            <div class="col-xs-8 col-sm-4">
                                                <input type="text" placeholder="Enter Invoice Number" class="form-control" name="invoice_id" id="invoice_id" required="" onblur="check_invoice_id(this.id)"/>
                                            </div>
                                        </div>
                                        <div class="form-group">               
                                            <label class="col-xs-2 control-label">Invoice Amount * </label>
                                            <div class="col-xs-4">
                                                <input placeholder="Enter Invoice Value" type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="invoice_amount" required=""/>
                                            </div>
                                            <label class="col-xs-4 col-sm-2 control-label">Invoice File *  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 2 MB."><i class="fa fa-info"></i></span></label>
                                            <div class="col-xs-8 col-sm-4">
                                                <input type="file" class="form-control" name="file" id="invoice_file" required="" accept="application/pdf"/>
                                            </div>
                                        </div>
                                        <div class="form-group">                                            
                                            
                                            <label class="col-xs-2 control-label">AWB Number *</label>
                                            <div class="col-xs-4">
                                                <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="despatch_doc_no" required=""/>
                                            </div>
                                            <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                             <label class="col-xs-2 control-label">Courier Name *</label>
                                            <div class="col-xs-4">
<!--                                                <input placeholder="Enter Courier Name" type="text" class="form-control" name="courier_name" id="courier_name" required=""/>-->
                                                <select class="form-control" id="courier_name" name="courier_name" id="courier_name" required="">
                                                    <option selected="" disabled="" value="">Select Courier Name</option>
                                                    <?php foreach ($courier_details as $value1) { ?> 
                                                        <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php echo form_error('courier_name'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">                                            
                                           
                                            <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                            <div class="col-xs-4">
                                                <input placeholder="Select Courier Shipment Date" readonly=""  onkeydown="return false;" type="text" class="form-control" name="courier_shipment_date" id="courier_shipment_date" autocomplete="off"/>
                                            </div>
                                            <label class="col-xs-2 control-label">Courier File</label>
                                            <div class="col-xs-4">
                                                <input type="file" class="form-control" name="courier_file" id="courier_file"/>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <hr>
                                    <div class="dynamic-form-box">
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-6 col-md-2">
                                                <p class="text-center"><strong>Appliance</strong></p>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <p class="text-center"><strong>Part Name</strong></p>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <p class="text-center"><strong>Part Number</strong></p>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                                <p class="text-center"><strong>Booking Id <small>(Optional)</small></strong></p>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <p class="text-center"><strong>Quantity</strong></p>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <p class="text-center"><strong>Basic Price</strong></p>
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
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" name="part[0][part_name]" id="partName_0" required="" onchange="get_part_details(this.id)"></select>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" name="part[0][part_number]" id="partNumber_0" ></select>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                                <input type="text" class="form-control" name="part[0][booking_id]" id="booking_id_0" onblur="check_booking_id(this.id)"/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control" name="part[0][quantity]" id="quantity_0" min="1" required="" onblur="get_part_details(this.id)" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control" name="part[0][part_total_price]" onkeyup="calculate_total_price()" id="partBasicPrice_0" value="0" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control" name="part[0][hsn_code]" id="partHsnCode_0" value="" readonly=""/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control" name="part[0][gst_rate]" onkeyup="calculate_total_price()" id="partGstRate_0" value="" />
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
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2">
                                                <select class="form-control" id="part_name"  required="" onchange="get_part_details(this.id)"></select>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2">
                                                <select class="form-control" id="part_number"></select>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                                <input type="text" class="form-control" id="booking_id"  onblur="check_booking_id(this.id)"/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control" id="quantity" min="1" required="" onblur="get_part_details(this.id)" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control part-total-price" id="part_total_price"  value="0" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control" id="partHsnCode" value="" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control" id="partGstRate" value="" />
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
                                            <div class="col-xs-12">
                                                <div class="pull-right" style="margin-right:15px;">
                                                    <strong>
                                                    Total Price : <span id="total_spare_invoice_price">0</span>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-xs-5 col-md-4 col-md-offset-5">
                                                <button type="submit" class="btn btn-success" id="submit_btn">Submit</button>
                                                <input type="hidden" class="form-control" id="partner_name"  name="partner_name" value=""/>
                                                <input type="hidden" class="form-control" id="wh_name"  name="wh_name" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div role="tabpanel" class="tab-pane" id="onBooking">
        <form novalidate="novalidate" id="onBookingspareForm" method="post" class="form-horizontal"  action="javascript:void(0)" >
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel" style="margin-top: 0px;">
                        <div class="x_content">
                            <div class="form-box">
                                <div class="static-form-box">
                                    <div class="form-group">
                                            <label class="col-xs-4 col-sm-2 control-label">Partner *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <select class="form-control" name="partner_id" class="partner_id" id="on_partner_id" required=""></select>
                                            </div>
                                            <label class="col-xs-4 col-sm-2 control-label"><?php if(!$saas){ ?>247around <?php } ?>Warehouses *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <select class="form-control" name="wh_id" class="wh_id" id="on_wh_id">
                                                    <option value="" disabled="">Select Warehouse</option>
                                                </select>
                                            </div>
                                    </div>
                                    
                                      <div class="form-group">                       
                                                                                    
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice Date*</label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Select Invoice Date" type="text" readonly=""  onkeydown="return false;" class="form-control" name="dated" id="on_invoice_date" required="" autocomplete="off"/>
                                        </div>
                                        <label class="col-xs-2 control-label">Invoice Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input placeholder="Enter Invoice Number" type="text" class="form-control" name="invoice_id" id="on_invoice_number" required="" onblur="check_invoice_id(this.id, true)"/>
                                        </div>
                                    </div>
                                    <div class="form-group">                                        
                                       
                                         <label class="col-xs-2 control-label">Invoice Amount * </label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter Invoice Amount" type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="on_invoice_amount" required=""/>
                                        </div>
                                         
                                        <label class="col-xs-4 col-sm-2 control-label">Invoice File*  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 2 MB."><i class="fa fa-info"></i></span></label>
                                        <div class="col-xs-8 col-sm-4">
                                            <input type="file" class="form-control" name="file" id="on_invoice_file" required=""/>
                                            <input type="hidden" name="invoice_tag" value="<?php echo IN_WARRANTY; ?>">
                                        </div>
                                        
                                    </div>
                                    <div class="form-group">
                                       <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                        <label class="col-xs-2 control-label">Courier Name *</label>
                                        <div class="col-xs-4">
<!--                                            <input placeholder="Enter Courier Name" type="text" class="form-control" name="courier_name" id="on_courier_name" required=""/>-->
                                                <select class="form-control" id="courier_name" name="courier_name" id="courier_name" required="">
                                                    <option selected="" disabled="" value="">Select Courier Name</option>
                                                    <?php foreach ($courier_details as $value1) { ?> 
                                                        <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php echo form_error('courier_name'); ?>
                                        </div>
                                        <label class="col-xs-2 control-label">AWB Number *</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="on_despatch_doc_no" required=""/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        
                                        
                                         <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                        <div class="col-xs-4">
                                            <input placeholder="Select Courier Shipment Date" readonly=""  onkeydown="return false;" type="text" class="form-control" name="courier_shipment_date" id="on_courier_shipment_date" autocomplete="off"/>
                                        </div>
                                         <label class="col-xs-2 control-label">Courier File</label>
                                        <div class="col-xs-4">
                                            <input type="file" class="form-control" name="courier_file" id="on_courier_file"/>
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
                                            <input  placeholder="Enter Booking ID" onblur="bookingBlur('0')"  type="text" class="form-control searchbooking_id" id="onbookingid_0" required="" autocomplete="off"/>
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
                                                    <input placeholder="Enter Booking ID" type="text" class="form-control searchbooking_id" id="onbookingid" required="" autocomplete="off"/>
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
                        
                         <input type="hidden" class="form-control" id="on_wh_name"  name="wh_name" value=""/>
                         <input type="hidden" class="form-control" id="on_partner_name"  name="partner_name" value=""/>
                         <button type="button" class="btn btn-default onaddButton">Add Booking</button>
                        <button type="submit" class="btn btn-success" id="on_submit_btn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    </div>
</div>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
    var date_before_15_days = new Date();
    date_before_15_days.setDate(date_before_15_days.getDate()-15);
    
     var onBookingIndex = 0;
    var is_valid_booking = true;
    $(document).ready(function () {
        
        partIndex = 0;
        $('#partner_id').select2({
            placeholder:'Select Partner'
        });
        
        $('#wh_id').select2({
            placeholder:"Select Warehouse"
        });
       
        
       
        $('#serviceId_0').select2({
            placeholder:'Select Appliance'
        });
    //        $('#modelNumber_0').select2({
    //            placeholder:'Select Model Number'
    //        });
    //        $('#partType_0').select2({
    //            placeholder:'Select Part Type'
    //        });
        $('#partName_0').select2({
            placeholder:'Select Part Name'
        });
        $('#partNumber_0').select2({
            placeholder:'Select Part Number'
        });
            
        get_partner_list();
       // get_vendor('','');        
        $("#partner_id").on('change',function(){
            var partner_id = $("#partner_id").val();
              get_vendor('1',partner_id);              
        });
        
        
         $("#on_partner_id").on('change',function(){
            var partner_id = $("#partner_id").val();              ;   
              get_vendor_by_booking('1',partner_id);
        });
        
        
        
        $('[data-toggle="popover"]').popover(); 
        $("#dated").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: date_before_15_days,
            maxDate:'today',
        });
        $("#courier_shipment_date").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: date_before_15_days,
            maxDate:'today',
        });
        
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
        
        //$("#spareForm").validate();    
        $("#spareForm").on('submit', function(e) {
            e.preventDefault();
            var isvalid = $("#spareForm").valid();
            if (isvalid) {
                var wh_name = $('#wh_id option:selected').text();
                $('#wh_name').val(wh_name);
                
                var partner_name = $('#partner_id option:selected').text();
                $('#partner_name').val(partner_name);
                
                var entered_invoice_amt = Number($('#invoice_amount').val());
                var our_invoice_amt = Number($('#total_spare_invoice_price').text());
                if((our_invoice_amt >= entered_invoice_amt - 10) && (our_invoice_amt <= entered_invoice_amt + 10) ){
                    
                    $(".part-total-price").each(function(i) {
    
                        if(Number($('#partBasicPrice_'+i).val()) == 0){
                            showConfirmDialougeBox('Please enter basic price', 'warning');
                            $('#partBasicPrice_'+i).addClass('text-danger');
                            return false;
                        }

                        if(Number($('#partHsnCode_'+i).val()) === ""){
                            $('#partHsnCode_'+i).addClass('text-danger', 'warning');
                            showConfirmDialougeBox('Please enter HSN Code');
                            return false;
                        }

                        if(Number($('#partGstRate_'+i).val()) == 5 || Number($('#partGstRate_'+i).val()) == 12 || Number($('#partGstRate_'+i).val()) == 18 || Number($('#partGstRate_'+i).val())  == 28){

                        } else {
                            $('#partGstRate_'+i).addClass('text-danger');
                            showConfirmDialougeBox('Please invalid Gst Number', 'warning');

                            return false;
                        }
                    });
                
                    $('#invoice_amount').css('border','1px solid #ccc');
                    $('#total_spare_invoice_price').removeClass('text-danger');
                    
                    if(confirm('Are you sure to continue')){
                        $('#submit_btn').attr('disabled',true);
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
    
                        $.ajax({
                            method:"POST",
                            url:"<?php echo base_url();?>employee/inventory/process_spare_invoice_tagging",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success:function(response){
                                obj = JSON.parse(response);
//                                if(obj['warehouse_id']!='' && obj['total_quantity']!=''){                                   
//                                    var confirmation = confirm("Want to Print Warehouse Address");
//                                    if (confirmation){
//                                       window.location.href = "<?php echo base_url();?>employee/inventory/print_warehouse_address/"+obj['partner_id']+"/"+obj['warehouse_id']+"/"+obj['total_quantity']+""; 
//                                    }
//                                }                                
                                $('#submit_btn').attr('disabled',false);
                                $('#submit_btn').html("Submit");                               
                                if(obj.status){
                                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                                    $('#success_msg').html(obj.message);
                                    $("#spareForm")[0].reset();
                                    $(".warehouse_print_address").css({'display':'block'});
                                    $("#print_warehouse_addr").attr("href","<?php echo base_url();?>employee/inventory/print_warehouse_address/"+obj['partner_id']+"/"+obj['warehouse_id']+"/"+obj['total_quantity']+"");
                                }else{
                                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                                    $('#error_msg').html(obj.message);
                                }
    
                           }
                        });
                    }else{
                        return false;
                    }
                    
                }else{
                    alert('Amount of invoice does not match with total price');
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
                .find('[id="part_name"]').attr('name', 'part[' + partIndex + '][part_name]').attr('id','partName_'+partIndex).select2({placeholder:'Select Part Name'}).end()
                .find('[id="part_number"]').attr('name', 'part[' + partIndex + '][part_number]').attr('id','partNumber_'+partIndex).select2({placeholder:'Select Part Number'}).end()
                .find('[id="booking_id"]').attr('name', 'part[' + partIndex + '][booking_id]').attr('id','bookingId_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').attr('id','quantity_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventoryId_'+partIndex).end()
                .find('[id="partGstRate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','partGstRate_'+partIndex).attr('onkeyup','calculate_total_price()').end()
                .find('[id="partHsnCode"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','partHsnCode_'+partIndex).end()
                .find('[id="part_total_price"]').attr('name', 'part[' + partIndex + '][part_total_price]').attr('id','partBasicPrice_'+partIndex).attr('onkeyup','calculate_total_price()').end();
            get_appliance(partIndex);
        })
    
        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.form-group'),
                index = $row.attr('data-part-index');
                partIndex = partIndex -1;
            $row.remove();
        });
    });
    
    $('#partner_id').on('change',function(){
        get_appliance(0);
    });
      
    function get_vendor(is_wh,partner_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
            data:{is_wh:is_wh,partner_id:partner_id},
            success: function (response) {
                $('#wh_id').html(response);                
            }
        });
    }
    
    function get_vendor_by_booking(is_wh,partner_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{is_wh:is_wh,partner_id:partner_id},
            success: function (response) {               
                $('#on_wh_id').html(response);
            }
        });
    }
    function get_partner_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success: function (response) {
                $("#partner_id").html(response);
                $("#on_partner_id").html(response);
            }
        });
    }
    
    function get_appliance(index){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
                data:{partner:partner_id},
                success: function (response) {
                    if(response){
                        $('#serviceId_'+index).html(response);
                    }else{
                        console.log(response);
                    }
                }
            });
        }else{
            alert('Please Select Partner');
        }
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
            alert("Please Select All Field");
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
            alert("Please Select All Field");
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
                            
                            $('#partBasicPrice_'+index).val(parts_total_price);
                            $('#partGstRate_'+index).val(obj.gst_rate);
                            $('#partHsnCode_'+index).val(obj.hsn_code);
                            
                            
                            $('#partHsnCode_'+index).val(obj.hsn_code);
                            var total_spare_invoice_price = 0;
                            $(".part-total-price").each(function(i) {
                                total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
                            });
                            $('#total_spare_invoice_price').html(Number(Math.round(total_spare_invoice_price)));
                        }else{
                            alert("Inventory Details not found for the selected combination.");
                            $('#submit_btn').attr('disabled',true);
                        }
    
                    }
                });
            }else{
                $('#quantity_'+index).val('');
                alert("Please Select All Field");
            }
        }else{
            alert('Booking id not found');
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
                        $('#submit_btn').attr('disabled',false);
                        is_valid_booking = true;
                    }else{
                        is_valid_booking = false;
                        $('#'+id).css('border','1px solid red');
                        $('#submit_btn').attr('disabled',true);
                        alert('Booking id not found');
                    }
                }
            });
        }else{
            is_valid_booking = true;
            $('#'+id).css('border','1px solid #ccc');
            $('#submit_btn').attr('disabled',false);
        }
    }
    
    function check_invoice_id(id, isOnBooking){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){
            
            if( invoice_id.indexOf('/') !== -1 ){
                $('#'+id).css('border','1px solid red');
                if(isOnBooking){
                     $('#on_submit_btn').attr('disabled',true);
                } else {
                     $('#submit_btn').attr('disabled',true);
                }
                
                alert("Use '-' in place of '/'");
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
                            alert('Invoice number already exists');
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
    
   //Inventory On Booking
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
        
    $('#on_invoice_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
     });
    
    $('#on_invoice_date').on('cancel.daterangepicker', function(ev, picker) {
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
                        //onBookingIndex = (Number(count) + Number(obj.count) - 1);

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
            var isvalid = $("#onBookingspareForm").valid();
            if (isvalid) {
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
                
                $(".onpartBasicPrice").each(function(i) {
    
                    if(Number($('#onpartBasicPrice_'+i).val()) === 0){
                        onBookingshowConfirmDialougeBox('Please enter basic price', 'warning');
                        $('#onpartBasicPrice_'+i).addClass('text-danger');
                        return false;
                    }

                });
                
                
                $(".onpartGstRate").each(function(i) {
    
                     if(Number($('#onpartGstRate_'+i).val()) === 5 || Number($('#onpartGstRate_'+i).val()) === 12 || Number($('#onpartGstRate_'+i).val()) === 18 
                             || Number($('#onpartGstRate_'+i).val())  == 28){

                    } else {
                        $('#onpartGstRate_'+i).addClass('text-danger');
                        onBookingshowConfirmDialougeBox('Please invalid Gst Number', 'warning');
                       
                        return false;
                    }

                });
                
                $(".onpartHsnCode").each(function(i) {
    
                    if(Number($('#onpartHsnCode_'+i).val()) === ""){
                        $('#onpartHsnCode_'+i).addClass('text-danger', 'warning');
                        onBookingshowConfirmDialougeBox('Please enter HSN Code');
                        return false;
                    }

                });
            }
        });
        
     $('#onBookingspareForm').on('click', '.onspareaddButton', function () {
        var b=  $(this).attr('data-count');
           onBookingIndex++;
           var $template = $('#spare_line_template_'+b),
               $clone = $template
                       .clone()
                       .removeClass('hide')
                       .removeAttr('id')
                       .attr('data-book-index', onBookingIndex)
                       .insertBefore($template);
    
           // Update the name attributes
           $clone
               .find('[id="shipping_status_1"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','s_shippingStatus_'+onBookingIndex).attr("required", true).end()
               .find('[id="shipping_status_2"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','n_shippingStatus_'+onBookingIndex).attr("required", true).end()
               .find('[id="shipping_status_3"]').attr('name', 'part[' + onBookingIndex + '][shippingStatus]').attr('id','l_shippingStatus_'+onBookingIndex).attr("required", true).end()
               .find('[id="onpartName"]').attr('name', 'part[' + onBookingIndex + '][part_name]').attr('id','onpartName_'+onBookingIndex).attr('onchange','get_part_number_on_booking("'+ onBookingIndex+'")').addClass('part_name').attr("required", true).end()
               .find('[id="onpartBasicPrice"]').attr('name', 'part[' + onBookingIndex + '][part_total_price]').attr('id','onpartBasicPrice_'+onBookingIndex).attr('onkeyup','booking_calculate_total_price('+onBookingIndex+')').addClass('onpartBasicPrice').end()
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
                 submitBookingForm();
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
    
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
        var is_micro = $("on_wh_id").find(':selected').attr('data-warehose');
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
</script>
