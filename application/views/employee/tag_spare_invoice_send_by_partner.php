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
</style>
<!-- page content -->
<div id="page-wrapper" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Spare Dispatched by Partner</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="loader"></div>
                    <div class="form-box">
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
                                    <label class="col-xs-4 col-sm-2 control-label">247around Warehouses *</label>
                                    <div class="col-xs-8 col-sm-4">
                                        <select class="form-control" name="wh_id" id="wh_id" required="">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-4 col-sm-2 control-label">Invoice / DC Date *</label>
                                    <div class="col-xs-8 col-sm-4">
                                        <input type="text" class="form-control" name="dated" id="dated" required="" autocomplete="off"/>
                                    </div>
                                    <label class="col-xs-2 control-label">Invoice / DC Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                    <div class="col-xs-8 col-sm-4">
                                        <input type="text" class="form-control" name="invoice_id" id="invoice_id" required="" onblur="check_invoice_id(this.id)"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Invoice / DC Amount * </label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="invoice_amount" required=""/>
                                    </div>
                                    <label class="col-xs-4 col-sm-2 control-label">Invoice / DC File *  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 2 MB."><i class="fa fa-info"></i></span></label>
                                    <div class="col-xs-8 col-sm-4">
                                        <input type="file" class="form-control" name="file" id="invoice_file" required="" accept="application/pdf"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">AWB Number *</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="awb_number" id="despatch_doc_no" required=""/>
                                    </div>
                                    <label class="col-xs-2 control-label">Courier Name *</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="courier_name" id="courier_name" required=""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="courier_shipment_date" id="courier_shipment_date" autocomplete="off"/>
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
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <p class="text-center"><strong>Part Name</strong></p>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <p class="text-center"><strong>Part Number</strong></p>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
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
                                        <select class="form-control" name="part[0][service_id]" id="serviceId_0" required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <select class="form-control" name="part[0][part_name]" id="partName_0" required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <select class="form-control" name="part[0][part_number]" id="partNumber_0" ></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <input type="text" class="form-control" name="part[0][booking_id]" id="booking_id_0" onblur="check_booking_id(this.id)"/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="number" class="form-control" name="part[0][quantity]" id="quantity_0" min="1" required="" onblur="get_part_details(this.id)" />
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control" name="part[0][part_total_price]" id="partBasicPrice_0" value="0" readonly=""/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control" name="part[0][hsn_code]" id="partHsnCode_0" value="" readonly=""/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control" name="part[0][gst_rate]" id="partGstRate_0" value="" readonly=""/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="hidden" class="form-control" name="part[0][inventory_id]" id="inventoryId_0" value=""/>
                                        <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                                <!-- The template for adding new field -->
                                <div class="form-group hide" id="partTemplate">
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <select class="form-control" id="service_id"  required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <select class="form-control" id="part_name"  required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <select class="form-control" id="part_number"></select>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2">
                                        <input type="text" class="form-control" id="booking_id"  onblur="check_booking_id(this.id)"/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="number" class="form-control" id="quantity" min="1" required="" onblur="get_part_details(this.id)" />
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control part-total-price" id="part_total_price"  value="0" readonly=""/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control" id="partHsnCode" value="" readonly=""/>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-1">
                                        <input type="text" class="form-control" id="partGstRate" value="" readonly=""/>
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
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
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
        get_vendor();
        
        $('[data-toggle="popover"]').popover(); 
        $("#dated").datepicker({dateFormat: 'yy-mm-dd'});
        $("#courier_shipment_date").datepicker({dateFormat: 'yy-mm-dd'});
        
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
                
                var entered_invoice_amt = Number($('#invoice_amount').val());
                var our_invoice_amt = Number($('#total_spare_invoice_price').text());
                if((our_invoice_amt >= entered_invoice_amt - 10) && (our_invoice_amt <= entered_invoice_amt + 10) ){
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
                                $('#submit_btn').attr('disabled',false);
                                $('#submit_btn').html("Submit");
                                if(obj.status){
                                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                                    $('#success_msg').html(obj.message);
                                    $("#spareForm")[0].reset();
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
                .find('[id="service_id"]').attr('name', 'part[' + partIndex + '][service_id]').attr('id','serviceId_'+partIndex).select2({placeholder:'Select Appliance'}).end()
                .find('[id="part_name"]').attr('name', 'part[' + partIndex + '][part_name]').attr('id','partName_'+partIndex).select2({placeholder:'Select Part Name'}).end()
                .find('[id="part_number"]').attr('name', 'part[' + partIndex + '][part_number]').attr('id','partNumber_'+partIndex).select2({placeholder:'Select Part Number'}).end()
                .find('[id="booking_id"]').attr('name', 'part[' + partIndex + '][booking_id]').attr('id','bookingId_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').attr('id','quantity_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventoryId_'+partIndex).end()
                .find('[id="partGstRate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','partGstRate_'+partIndex).end()
                .find('[id="partHsnCode"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','partHsnCode_'+partIndex).end()
                .find('[id="part_total_price"]').attr('name', 'part[' + partIndex + '][part_total_price]').attr('id','partBasicPrice_'+partIndex).end();
            get_appliance(partIndex);
        })

        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.form-group'),
                index = $row.attr('data-part-index');
            $row.remove();
        });
    });
    
    $('#partner_id').on('change',function(){
        get_appliance(0);
    });
    
    function get_vendor() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#wh_id').html(response);
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
            }
        });
    }
    
    function get_appliance(index){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            $.ajax({
                type: 'GET',
                url: '<?php echo base_url() ?>employee/booking/get_service_id_by_partner',
                data:{is_option_selected:true,partner_id:partner_id},
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
                    $('#partNumber_'+index).val('Select Part Name').change();
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
                            var parts_total_price = parseInt($('#quantity_'+index).val()) * parseInt(obj.price);
                            $('#inventoryId_'+index).val(obj.inventory_id);
                            $('#partBasicPrice_'+index).val(parts_total_price);
                            $('#partGstRate_'+index).val(obj.gst_rate);
                            $('#partHsnCode_'+index).val(obj.hsn_code);
                            var total_spare_invoice_price = 0;
                            $(".part-total-price").each(function(i) {
                                total_spare_invoice_price += Number($('#partBasicPrice_'+i).val()) + (Number($('#partBasicPrice_'+i).val()) * Number($('#partGstRate_'+i).val())/100);
                            });
                            $('#total_spare_invoice_price').html(total_spare_invoice_price);
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
    
    function check_invoice_id(id){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){
            
            if( invoice_id.indexOf('/') !== -1 ){
                $('#'+id).css('border','1px solid red');
                $('#submit_btn').attr('disabled',true);
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
                            $('#submit_btn').attr('disabled',true);
                            alert('Invoice number already exists');
                        }else{
                            $('#'+id).css('border','1px solid #ccc');
                            $('#submit_btn').attr('disabled',false);
                        }
                    }
                });
            }
        }
    }
    
</script>
