<style>
    .select2-container .select2-selection--single{
        height: 32px;
    }
    .form-horizontal .control-label{
        text-align: left;
    }
</style>
<!-- page content -->
<div id="page-wrapper" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Tag Spare Invoice</h2>
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
                                    <label class="col-xs-2 control-label">Select Partner*</label>
                                    <div class="col-xs-4">
                                        <select class="form-control" name="partner_id" id="partner_id" required=""></select>
                                    </div>
                                    <label class="col-xs-2 control-label">Invoice Number*</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="invoice_id" id="invoice_id" placeholder="Enter Invoice ID" required=""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Invoice Dated*</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="dated" id="dated" placeholder="Select Date" required=""/>
                                    </div>
                                    <label class="col-xs-2 control-label">Warehouse*</label>
                                    <div class="col-xs-4">
                                        <select class="form-control" name="wh_id" id="wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                </div>
<!--                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Despatch Document Number*</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="despatch_doc_no" id="despatch_doc_no" placeholder="Enter Despatch Document No" required=""/>
                                    </div>
                                    <label class="col-xs-2 control-label">Courier Name*</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" name="courier_name" id="courier_name" placeholder="Enter Courier Name" required=""/>
                                    </div>
                                </div>-->
                            </div>
                            <hr>
                            <div class="dynamic-form-box">
                                <div class="form-group">
                                    <div class="col-xs-2">
                                        <p class="text-center"><strong>Appliance</strong></p>
                                    </div>
                                    <div class="col-xs-2">
                                        <p class="text-center"><strong>Model Number</strong></p>
                                    </div>
                                    <div class="col-xs-2">
                                        <p class="text-center"><strong>Part Name</strong></p>
                                    </div>
                                    <div class="col-xs-2">
                                        <p class="text-center"><strong>Part Type</strong></p>
                                    </div>
                                    <div class="col-xs-2">
                                        <p class="text-center"><strong>Booking Id</strong></p>
                                    </div>
                                    <div class="col-xs-1">
                                        <p class="text-center"><strong>Quantity</strong></p>
                                    </div>
                                    <div class="col-xs-1">
                                        <p class="text-center"><strong>Price</strong></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-2">
                                        <select class="form-control" name="part[0][service_id]" id="serviceId_0" required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" name="part[0][model_number]" id="modelNumber_0" required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" name="part[0][part_type]" id="partType_0" required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" name="part[0][part_name]" id="partName_0" required=""></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="text" class="form-control" name="part[0][booking_id]" id="booking_id_0" placeholder="Booking ID" />
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="number" class="form-control" name="part[0][quantity]" id="quantity_0" placeholder="Quantity" required="" onblur="get_part_details(this.id)" />
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="text" class="form-control" name="part[0][part_total_price]" id="partTotalPrice_0" value="0" readonly=""/>
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="hidden" class="form-control" name="part[0][inventory_id]" id="inventoryId_0" value=""/>
                                        <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                                <!-- The template for adding new field -->
                                <div class="form-group hide" id="partTemplate">
                                    <div class="col-xs-2">
                                        <select class="form-control" id="service_id"  required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" id="model_number"  required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" id="part_type"  required="" onchange="get_part_details(this.id)"></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <select class="form-control" id="part_name"  required="" ></select>
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="text" class="form-control" id="booking_id"  placeholder="Booking ID" />
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="number" class="form-control" id="quantity"  placeholder="Quantity" min="1" required="" onblur="get_part_details(this.id)" />
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="text" class="form-control" id="part_total_price"  value="0" readonly=""/>
                                    </div>
                                    <div class="col-xs-1">
                                        <input type="hidden" class="form-control" id="inventory_id"  value=""/>
                                        <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="col-xs-5">
                                    <button type="submit" class="btn btn-success" id="submit_btn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
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
        $('#modelNumber_0').select2({
            placeholder:'Select Model Number'
        });
        $('#partType_0').select2({
            placeholder:'Select Part Type'
        });
        $('#partName_0').select2({
            placeholder:'Select Part Name'
        });
        
        get_partner_list();
        get_vendor();
        get_appliance(0);
        
        
        $("#dated").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
        
        ("#spareForm").validate();    
        $("#spareForm").on('submit', function(e) {
            var isvalid = $("#spareForm").valid();
            if (isvalid) {
                e.preventDefault();
                var formData =  $('#spareForm').serializeArray(); 
                $.ajax({
                   method:"POST",
                   url:"<?php echo base_url();?>employee/inventory/process_spare_invoice_tagging",
                   data:formData,
                   success:function(response){
                       obj = JSON.parse(response);
                       if(obj.status){
                            $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                            $('#success_msg').html(obj.message);
                            $("#spareForm")[0].reset();
                        }else{
                            $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                            $('#error_msg').html(obj.message);
                        }
                       
                   }
                });
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
                .find('[id="model_number"]').attr('name', 'part[' + partIndex + '][model_number]').attr('id','modelNumber_'+partIndex).select2({placeholder:'Select Model Number'}).end()
                .find('[id="part_type"]').attr('name', 'part[' + partIndex + '][part_type]').attr('id','partType_'+partIndex).select2({placeholder:'Select Part Type'}).end()
                .find('[id="part_name"]').attr('name', 'part[' + partIndex + '][part_name]').attr('id','partName_'+partIndex).select2({placeholder:'Select Part Name'}).end()
                .find('[id="booking_id"]').attr('name', 'part[' + partIndex + '][booking_id]').attr('id','bookingId_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').attr('id','quantity_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventoryId_'+partIndex).end()
                .find('[id="part_total_price"]').attr('name', 'part[' + partIndex + '][part_total_price]').attr('id','totalPrice_'+partIndex).end();
            get_appliance(partIndex);
        })

        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.form-group'),
                index = $row.attr('data-part-index');
            $row.remove();
        });
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
            success: function (response) {
                $("#partner_id").html(response);
            }
        });
    }
    
    function get_appliance(index){
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url() ?>employee/booking/get_service_id',
            data:{is_option_selected:true},
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
                get_model_number(index);
                break;
            case 'modelNumber':
                get_part_type(index);
                break;
            case 'partType':
                get_part_name(index);
                break;
            case 'quantity':
                get_part_price(index);
                break;
        }
    }
    
    function get_model_number(index){
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        if(partner_id && service_id){
            $.ajax({
                type: 'GET',
                url: '<?php echo base_url() ?>employee/inventory/get_part_model_number',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id},
                success: function (response) {
                    $("#modelNumber_"+index).html(response);
                }
            });
        }else{
            alert("Please Select All Field");
        }
    }
    
    function get_part_type(index){
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        var model_number = $('#modelNumber_'+index).val();
        if(partner_id && service_id && model_number){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_parts_type',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,model_number:model_number},
                success: function (response) {
                    $("#partType_"+index).html(response);
                }
            });
        }else{
            alert("Please Select All Field");
        }
    }
    
    function get_part_name(index){
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        var model_number = $('#modelNumber_'+index).val();
        var part_type = $('#partType_'+index).val();
        if(partner_id && service_id && model_number && part_type){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_parts_name',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,model_number:model_number,part_type:part_type,is_option_selected:true},
                success: function (response) {
                    $("#partName_"+index).html(response);
                }
            });
        }else{
            alert("Please Select All Field");
        }
    }
    
    function get_part_price(index){
        var partner_id = $('#partner_id').val();
        var service_id = $('#serviceId_'+index).val();
        var model_number = $('#modelNumber_'+index).val();
        var part_name = $('#partName_'+index).val();
        if(partner_id && service_id && model_number && part_name){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/inventory/get_inventory_price',
                data:{entity_id:partner_id,entity_type:'<?php echo _247AROUND_PARTNER_STRING; ?>',service_id:service_id,model_number:model_number,part_name:part_name},
                success: function (response) {
                    var obj = JSON.parse(response);
                    
                   var parts_total_price = parseInt($('#quantity_'+index).val()) * parseInt(obj.price);
                   $('#inventoryId_'+index).val(obj.inventory_id);
                   $('#partTotalPrice_'+index).val(parts_total_price);
                }
            });
        }else{
            alert("Please Select All Field");
        }
    }
    
</script>