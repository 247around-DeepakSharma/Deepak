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
    label.error {
        color:red;
    }
</style>
<!-- page content -->
<div id="page-wrapper"  role="main">
    <div class="panel panel-default">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active"><a href="#onMsl" aria-controls="onMsl" role="tab" data-toggle="tab">Inventory On MSL</a></li>
<!--                <li role="presentation" ><a href="#onBooking" aria-controls="onBooking" role="tab" data-toggle="tab">Inventory On Booking</a></li>-->
            </ul>
        </div>
    </div>
    <div class="tab-content panel-body" id="tab-content">
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
                                                <label for="partner_id" class="error"></label>
                                            </div>
                                            <label class="col-xs-4 col-sm-2 control-label"><?php if(!$saas){ ?>247around<?php }?> Warehouses *</label>
                                            <div class="col-xs-8 col-sm-4">
                                                <select class="form-control" name="wh_id" id="wh_id" required="" >
                                                    <option value="" disabled="">Select Warehouse</option>
                                                </select>
                                                <label for="wh_id" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">                                            
                                            
                                            <label class="col-xs-2 control-label">AWB Number *</label>
                                            <div class="col-xs-4">
                                                <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="despatch_doc_no" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13" />
                                                <label for="despatch_doc_no" class="error"></label>
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
                                                <label for="courier_name" class="error"></label>
                                            <?php echo form_error('courier_name'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">                                            
                                           
                                            <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                            <div class="col-xs-4">
                                                <input placeholder="Select Courier Shipment Date" readonly=""  style="background-color:#FFF;" onkeydown="return false;" type="text" class="form-control" name="courier_shipment_date" id="courier_shipment_date" autocomplete="off"/>
                                            </div>
                                            <label class="col-xs-2 control-label">Courier File</label>
                                            <div class="col-xs-4">
                                                <input type="file" class="form-control" name="courier_file" id="courier_file"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-xs-2 control-label">From GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Your GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                            <div class="col-xs-4">
                                                <select class="form-control" name="to_gst_number" id="from_gst_number" required="">
                                                    <option value="" disabled="">Select From GST Number</option>
                                                </select>
                                                <label for="from_gst_number" class="error"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="dynamic-form-box" id="appliance_details_id">
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
                                                <label for="serviceId_0" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" name="part[0][part_name]" id="partName_0" required="" onchange="get_part_details(this.id)"></select>
                                                <label for="partName_0" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" name="part[0][part_number]" id="partNumber_0" ></select>
                                                <label for="partNumber_0" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                                <input type="text" class="form-control" name="part[0][booking_id]" id="booking_id_0" onblur="check_booking_id(this.id)"/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control allowNumericWithOutDecimal" name="part[0][quantity]" id="quantity_0" min="1" required="" onblur="get_part_details(this.id)" />
                                                <label for="quantity_0" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control allowNumericWithDecimal" name="part[0][part_total_price]" onkeyup="validateDecimal(this.id, this.value);calculate_total_price()" id="partBasicPrice_0" value="0" />
                                                <label for="partBasicPrice_0" id="lbl_partBasicPrice_0" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="text" class="form-control allowNumericWithOutDecimal" name="part[0][hsn_code]" id="partHsnCode_0" value=""/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control allowNumericWithOutDecimal" name="part[0][gst_rate]" onkeyup="calculate_total_price()" id="partGstRate_0" min="12" max="28" value="" />
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
                                                <label for="service_id" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" id="part_name"  required="" onchange="get_part_details(this.id)"></select>
                                                <label for="part_name" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-3">
                                                <select class="form-control" id="part_number"></select>
                                                <label for="part_number" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-2" style="display:none">
                                                <input type="text" class="form-control" id="booking_id"  onblur="check_booking_id(this.id)"/>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control allowNumericWithOutDecimal" id="quantity" min="1" required="" onblur="get_part_details(this.id)" />
                                                <label for="quantity" class="error"></label>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-1">
                                                <input type="number" class="form-control allowNumericWithDecimal part-total-price" id="part_total_price"  value="0" />
                                                <label for="part_total_price" id="lbl_part_total_price" class="error"></label>
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
                                                <button type="submit" class="btn btn-success" id="submit_btn" name="submit_btn">Preview</button>
                                                <input type="hidden" class="form-control" id="partner_name"  name="partner_name" value=""/>
                                                <input type="hidden" class="form-control" id="wh_name"  name="wh_name" value=""/>
                                                <input type="hidden" class="form-control"  name="dated" id="dated" value="<?php echo date('Y-m-d');?>"/>
                                                <input type="hidden" name="sender_entity_type" value="<?php echo _247AROUND_SF_STRING; ?>">
                                                <input type="hidden" name="sender_entity_id" value="<?php echo $this->session->userdata('service_center_id'); ?>">
                                                <input type="hidden" name="invoice_tag" value="<?php echo MSL; ?>">
                                                <input type="hidden" name="transfered_by" value="<?php echo MSL_TRANSFERED_BY_WAREHOUSE; ?>">
                                                <input type="hidden" id="confirmation" value="0">
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
        
    </div>
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
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
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
        $('label.error').css('display','none');
    });
    
    $("input:text").on('input',function(){
        $("#sumit_msl,#submit_btn,#on_submit_btn").attr('disabled',false);
        $('label.error').css('display','none');
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
         
        $('#from_gst_number').select2({
            placeholder:'Select From GST Number'
        });
        
        get_partner_list();
        get_247around_wh_gst_number();
       // get_vendor('','');        
        $("#partner_id").on('change',function(){
            var partner_id = $("#partner_id").val();
              get_vendor('1',partner_id);  
        });
        
        
//         $("#on_partner_id").on('change',function(){
//            var partner_id = $("#partner_id").val();              ;   
//              get_vendor_by_booking('1',partner_id);
//        });
        
        
        
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
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
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
        
        //$("#spareForm").validate();    
        $("#spareForm").on('submit', function(e) { 
            e.preventDefault();
            $("#submit_btn").attr('disabled',true);
            var isvalid = $("#spareForm").valid();
            var flag = true;
            if (isvalid) {
                var wh_name = $('#wh_id option:selected').text();
                $('#wh_name').val(wh_name);
                
                var partner_name = $('#partner_id option:selected').text();
                $('#partner_name').val(partner_name);
                  
                $(".part-total-price").each(function(i) {
                    if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                        validateDecimal('partBasicPrice_'+i,$('#partBasicPrice_'+i).val());
    
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
                
                //var entered_invoice_amt = Number($('#invoice_amount').val());
                var our_invoice_amt = Number($('#total_spare_invoice_price').text());
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
                    
                    if(confirm('Are you sure to continue')){
                        //Serializing all For Input Values (not files!) in an Array Collection so that we can iterate this collection later.
                        var params = $('#spareForm').serializeArray();
    
                        
    
                        //Getting Courier Files Collection
                        var courier_file = $("#courier_file")[0].files;
    
                        //Declaring new Form Data Instance  
                        var formData = new FormData();
                        
                        var is_micro = $("#wh_id").find(':selected').attr('data-warehose');
                        formData.append("is_wh_micro", is_micro);
    
                        var from_gst_number = $("#from_gst_number").find(':selected').text().split(' - ')[1];
                        formData.append("247around_gst_number", from_gst_number);
    
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
                            beforeSend: function(){
                                // Handle the beforeSend event
                                $('#sumit_msl,#submit_btn').attr('disabled',true);
                                $('#submit_btn').html("<i class='fa fa-spinner fa-spin'></i> Processing...");
                                $("#spareForm")[0].reset();
                                $("#spareForm").find('input:text, input:file, select').val('');
                                $('label.error').css('color','white');
                            },
                            success:function(response){
                                obj = JSON.parse(response);
//                                if(obj['warehouse_id']!='' && obj['total_quantity']!=''){                                   
//                                    var confirmation = confirm("Want to Print Warehouse Address");
//                                    if (confirmation){
//                                       window.location.href = "<?php echo base_url();?>employee/inventory/print_warehouse_address/"+obj['partner_id']+"/"+obj['warehouse_id']+"/"+obj['total_quantity']+""; 
//                                    }
//                                }                                
                                if(obj.status){
                                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                                    $('#success_msg').html(obj.message);
                                    $("#spareForm")[0].reset();
                                    $("#spareForm").find('input:text, input:file, select').val('');
                                    $('#select2-partner_id-container').text('Select Partner');
                                    $('#select2-partner_id-container').attr('title','Select Partner');
                                    $('#select2-from_gst_number-container').text('Select From GST Number');
                                    $('#select2-from_gst_number-container').attr('title','Select From GST Number');
                                    $('#select2-wh_id-container').text('Select Warehouse');
                                    $('#select2-wh_id-container').attr('title','Select Warehouse');
                                    $('#select2-serviceId_0-container').text('Select Appliance');
                                    $('#select2-serviceId_0-container').attr('title','Select Appliance');
                                    $('#select2-partName_0-container').text('Select Part Name');
                                    $('#select2-partName_0-container').attr('title','Select Part Name');
                                    $('#select2-partNumber_0-container').text('Select Part Number');
                                    $('#select2-partNumber_0-container').attr('title','Select Part Number');
                                    $('#total_spare_invoice_price').html('0');
                                    $(".warehouse_print_address").css({'display':'block'});
                                    $("#print_warehouse_addr").attr("href","<?php echo base_url();?>employee/inventory/print_warehouse_address/"+obj['partner_id']+"/"+obj['warehouse_id']+"/"+obj['total_quantity']+"");
                                }else{
                                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                                    $('#error_msg').html(obj.message);
                                }
    
                           },
                            complete: function() {
                                $('#sumit_msl,#submit_btn').attr('disabled',false);
                                $('#submit_btn').html("Preview");
                                $('label.error').css('color','red');
                                $('label.error').css('display','none');
                                $("#confirmation").val('0');    
                            }
                        });
                    }else{
                        $("#confirmation").val('0');
                        return false;
                    }
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
                .find('[for="service_id"]').attr('for','serviceId_'+partIndex).end()
                .find('[id="part_name"]').attr('name', 'part[' + partIndex + '][part_name]').attr('id','partName_'+partIndex).select2({placeholder:'Select Part Name'}).end()
                .find('[for="part_name"]').attr('for','partName_'+partIndex).end()
                .find('[id="part_number"]').attr('name', 'part[' + partIndex + '][part_number]').attr('id','partNumber_'+partIndex).select2({placeholder:'Select Part Number'}).end()
                .find('[for="part_number"]').attr('for','partNumber_'+partIndex).end()
                .find('[id="booking_id"]').attr('name', 'part[' + partIndex + '][booking_id]').attr('id','bookingId_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').attr('id','quantity_'+partIndex).end()
                .find('[for="quantity"]').attr('for','quantity_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventoryId_'+partIndex).end()
                .find('[id="partGstRate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','partGstRate_'+partIndex).attr('onkeyup','calculate_total_price()').end()
                .find('[id="partHsnCode"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','partHsnCode_'+partIndex).end()
                .find('[id="part_total_price"]').attr('name', 'part[' + partIndex + '][part_total_price]').attr('id','partBasicPrice_'+partIndex).attr('onkeyup','validateDecimal(this.id, this.value);calculate_total_price()').end()
                .find('[for="part_total_price"]').attr('for','partBasicPrice_'+partIndex).attr('id','lbl_partBasicPrice_'+partIndex).end();
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
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $("#on_partner_id").html(response);
                var option_length = $('#on_partner_id').children('option').length;
                if(option_length == 2){
                 $("#on_partner_id").change();   
                }
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
                url: '<?php echo base_url() ?>employee/inventory/get_parts_name_without_model_mapping',
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
    
    function calculate_total_price(){
        var total_spare_invoice_price = 0;
         $(".part-total-price").each(function(i) {
            if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
            }
         });
        $('#total_spare_invoice_price').html(Number(total_spare_invoice_price.toFixed(2)));
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
            var quantity = $("#quantity_"+index).val();
            if(partner_id && service_id && part_number){
               
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/inventory/get_inventory_price',
                    data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,part_number:part_number},
                    success: function (response) {
                        var obj = JSON.parse(response);
                        console.log(obj);
                        if(obj.inventory_id){
                            $('#submit_btn').attr('disabled',false);
                            var parts_total_price = Number($('#quantity_'+index).val()) * Number(obj.price);
                            $('#inventoryId_'+index).val(obj.inventory_id);
                            $('#partBasicPrice_'+index).val(parts_total_price.toFixed(2));
                            $('#partGstRate_'+index).val(obj.gst_rate);
                            $('#partHsnCode_'+index).val(obj.hsn_code);
                            $('#partHsnCode_'+index).val(obj.hsn_code);

                            if (Number($.trim(quantity)) > Number($.trim(obj.total_stock))) {
                                swal("Stock is less!", "Stock availability is less than your entered quantity!", "error");
                                $("#quantity_"+index).val("");
                                $('#partHsnCode_'+index).val("");
                                $('#partGstRate_'+index).val("");
                                $('#partBasicPrice_'+index).val("");
                                $('#inventoryId_'+index).val("");
                            }else{

                            var total_spare_invoice_price = 0;
                            $(".part-total-price").each(function(i) {
                                if($.trim($('#partBasicPrice_'+i).val()) !== '') {
                                    total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
                                }
                            });
                            $('#total_spare_invoice_price').html(Number(total_spare_invoice_price.toFixed(2)));
                            }

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
                        $('#on_submit_btn').attr('disabled',false);
                        is_valid_booking = true;
                    }else{
                        is_valid_booking = false;
                        $('#'+id).css('border','1px solid red');
                        $('#on_submit_btn').attr('disabled',true);
                        alert('Booking id not found');
                    }
                }
            });
        }else{
            is_valid_booking = true;
            $('#'+id).css('border','1px solid #ccc');
            $('#on_submit_btn').attr('disabled',false);
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
//     $('#on_invoice_date').daterangepicker({
//            autoUpdateInput: false,
//            singleDatePicker: true,
//            showDropdowns: true,
//            minDate: date_before_15_days,
//            maxDate:'today',
//            locale:{
//                format: 'YYYY-MM-DD'
//            }
//        });
        
//    $('#on_invoice_date').on('apply.daterangepicker', function(ev, picker) {
//            $(this).val(picker.startDate.format('YYYY-MM-DD'));
//     });
//    
//    $('#on_invoice_date').on('cancel.daterangepicker', function(ev, picker) {
//        $(this).val('');
//    });
//    
//    $('#on_courier_shipment_date').daterangepicker({
//            autoUpdateInput: false,
//            singleDatePicker: true,
//            showDropdowns: true,
//            minDate: date_before_15_days,
//            maxDate:'today',
//            locale:{
//                format: 'YYYY-MM-DD'
//            }
//        });
//    
//    $('#on_courier_shipment_date').on('apply.daterangepicker', function(ev, picker) {
//        $(this).val(picker.startDate.format('YYYY-MM-DD'));
//    });
//
//    $('#on_courier_shipment_date').on('cancel.daterangepicker', function(ev, picker) {
//        $(this).val('');
//    });
    
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
               .find('[for="onbookingid"]').attr('for','onbookingid_'+onBookingIndex).end()
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
       
        function booking_calculate_total_price(id){

            var total_spare_invoice_price = Number($('#onpartBasicPrice_'+id).val()) + (Number($('#onpartBasicPrice_'+id).val()) * Number($('#onpartGstRate_'+id).val())/100);
            $('#ontotal_amount_'+id).val(Number(total_spare_invoice_price.toFixed(2)));
        }
        
        function booking_calculate_basic_price(id){
           var qty = (($.trim($('#onquantity_'+id).val()) !== '') ? Number($('#onquantity_'+id).val()) : 1);
           var spare_invoice_price = qty * Number($('#onpartBasic_'+id).val());
           $('#onpartBasicPrice_'+id).val(Number(spare_invoice_price.toFixed(2)));
        }
        
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
                        $('#onpartBasic_'+index).val(obj.basic_price);
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
        $('#onpartBasic_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-basic_price'));
        $('#onpartGstRate_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-gst_rate'));
        $('#onpartHsnCode_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-hsn_code'));
        $('#onspareType'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-type'));

        $('#ontotal_amount_'+index).val($("#onpartNumber_"+index).find(':selected').attr('data-inventory_id'));
    }
    
    $("#onBookingspareForm").on('submit', function(e) {
            e.preventDefault();
            $("#on_submit_btn").attr('disabled',true);
            var isvalid = $("#onBookingspareForm").valid();
            var flag = true;
            if (isvalid) {
                $(".onpartBasicPrice").each(function(i) {
                    validateDecimal('onpartBasicPrice_'+i,$('#onpartBasicPrice_'+i).val());
                    
                    if(Number($('#onpartBasicPrice_'+i).val()) === 0){
                        onBookingshowConfirmDialougeBox('Please enter total basic price', 'warning');
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
               .find('[id="onpartBasic"]').attr('id','onpartBasic_'+onBookingIndex).end()
               .find('[id="onpartBasicPrice"]').attr('name', 'part[' + onBookingIndex + '][part_total_price]').attr('id','onpartBasicPrice_'+onBookingIndex).attr('onkeyup','validateDecimal(this.id, this.value);booking_calculate_total_price('+onBookingIndex+')').addClass('onpartBasicPrice').end()
               .find('[for="onpartBasicPrice"]').attr('for','onpartBasicPrice_'+onBookingIndex).attr('id','lbl_onpartBasicPrice_'+onBookingIndex).end()
               .find('[id="onquantity"]').attr('name', 'part[' + onBookingIndex + '][quantity]').attr('id','onquantity_'+onBookingIndex).attr('onkeyup','booking_calculate_basic_price('+onBookingIndex+');booking_calculate_total_price('+onBookingIndex+')').end()
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
    
    function showConfirmDialougeBox(title,type){
        if(type === 'info'){
            swal({
            title: title,
            type: type,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
            function(){
                 $("#submit_btn").attr('disabled',true);
                 $("#spareForm").submit();
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
    
    function submitBookingForm(){
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
        $.ajax({
            method:"POST",
            url:"<?php echo base_url();?>employee/inventory/process_spare_invoice_tagging",
            data:formData,
            contentType: false,
            processData: false,
            beforeSend: function(){
                // Handle the beforeSend event
                $('#on_submit_btn').attr('disabled',true);
                $('#on_submit_btn').html("<i class='fa fa-spinner fa-spin'></i> Processing...");
                $("#onBookingspareForm")[0].reset();
                $("#onBookingspareForm").find('input:text, input:file, select').val('');
            },
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
    
    function get_247around_wh_gst_number(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/inventory/get_247around_wh_gst_number',
            data:{partner_id:$("#partner_id").val()},
            success: function (response) {
                $("#from_gst_number").html(response);
            }
        });
    }
   
    function validateDecimal(id,value) {
        var RE = /^\d+(?:\.\d{1,2})?$/
        if(($.trim(value) !== '') && !RE.test(value)){
           $('#lbl_'+id).text("Enter value upto 2 decimal places");
           $('#'+id).focus();
           $('#submit_btn').attr('disabled',true);
        }
        else {
            $('#lbl_'+id).text("");
            $('#submit_btn').attr('disabled',false);
        }
    }
</script>
