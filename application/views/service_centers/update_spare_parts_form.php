<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style type="text/css">
    #update_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 0px 0 0px 0px;
    padding: 0;
    text-align: left;
    }
</style>
<div class="right_col" role="main" style="padding:0 30px; margin-bottom: 30px;">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <?php
                    if ($this->session->userdata('success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top: 55px;">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>' . $this->session->userdata('success') . '</strong>
                                    </div>';
                    }
                    if ($this->session->userdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top: 55px;">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>' . $this->session->userdata('error') . '</strong>
                                    </div>';
                    }
                    ?>
                <div class="x_title">
                    <h3>Requested Spare Parts</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form action="#" class ="form-horizontal" >
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="booking_ids" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_ids" name="booking_ids" value = "<?php echo $spare_parts[0]->booking_id; ?>" placeholder="Enter Booking ID" readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="user" class="col-md-4">User</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="user" name="user_name" value = "<?php echo $spare_parts[0]->name; ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="mobile" class="col-md-4">Mobile</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="mobile" name="mobile" value = "<?php echo $spare_parts[0]->booking_primary_contact_no; ?>" placeholder="Enter Mobile" readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="invoice_pic" class="col-md-4">Invoice Image</label>
                                    <div class="col-md-6">
                                        <?php if(!is_null($spare_parts[0]->invoice_pic)){ ?>
                                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $spare_parts[0]->invoice_pic;?>" target="_blank" id="invoice_pic">View Image</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="model_number" class="col-md-4">Model Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="model_number" name="model_number" value = "<?php echo $spare_parts[0]->model_number; ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="serial_number" class="col-md-4">Serial Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="serial_number" name="serial_number" value = "<?php echo $spare_parts[0]->serial_number; ?>"  readonly="readonly" required>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if(!is_null($spare_parts[0]->serial_number_pic)){ ?>
                                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $spare_parts[0]->serial_number_pic;?>" target="_blank"><img src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $spare_parts[0]->serial_number_pic;?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="dop" class="col-md-4">Date of Purchase</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="dop" name="dop" value = "<?php echo $spare_parts[0]->date_of_purchase; ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <form enctype="multipart/form-data" action="<?php echo base_url(); ?>service_center/process_update_spare_parts/<?php echo $spare_parts[0]->booking_id; ?>" class ="form-horizontal" name="update_form" id="update_form"  method="POST">
        <div class="row">
            <div class="col-md-12 col-sm12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>Spare Part Details <button type="button" class="btn btn-primary pull-right addButton">Ship More Parts</button></h3>
                        <hr>
                        <div class="clearfix"></div>
                    </div>
                    <input type="hidden" name="request_type" value="<?php echo $spare_parts[0]->request_type;?>"/>
                    <input type="hidden" class="form-control" name="booking_id" value = "<?php echo $spare_parts[0]->booking_id; ?>">
                    <?php foreach ($spare_parts as $skey => $sp) { ?>
                    <p style="font-weight:bold; margin-bottom: 15px;"><?php if($sp->inventory_invoice_on_booking == 1){ echo 'Invoice on Booking';} else { echo "Invoice on MSL";} ?></p>
                    <div class="panel panel-default" style="<?php if($sp->inventory_invoice_on_booking == 1){ echo 'border-color: green;';} ?>">
                        <div class="panel-body" >
                            <div class="x_content">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                        <input type="radio" name="part[<?php echo $skey;?>][shippingStatus]" required=""  value="1">Shipping
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                        <input type="radio" name="part[<?php echo $skey;?>][shippingStatus]" required=""  value="0">Not Shipping
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                        <input type="radio" name="part[<?php echo $skey;?>][shippingStatus]" required="" value="-1">To be Shipped
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group ">
                                        <label for="parts_type" class="col-md-4">Requested Parts Type</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="<?php echo "partstype_".$skey;?>" name="part[<?php echo $skey;?>][parts_type]" readonly="readonly" required><?php echo $sp->parts_requested_type; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group <?php
                                        if (form_error('shipped_model_number')) {
                                            echo 'has-error';
                                        } ?>">
                                        <label for="shipped_model_number" class="col-md-4">Shipped Model Number *</label>
                                        <div class="col-md-6">
                                            <select class="form-control spare_parts shipped_model_number_id" onchange="change_shipped_model('<?php echo $skey;?>')" id="<?php echo "shippedmodelnumberid_".$skey;?>" name="part[<?php echo $skey;?>][shipped_model_number_id]">
                                                <option value="" disabled="" selected="">Select Model Number</option>
                                                <?php foreach ($inventory_details as $key => $value) { ?> 
                                                <option value="<?php echo $value['id']; ?>" <?php if($value['model_number'] == $sp->model_number){ echo "selected";} ?>><?php echo $value['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" id="<?php echo "shippedmodelnumber_".$skey; ?>" name="part[<?php echo $skey;?>][shipped_model_number]">
                                            <input type="hidden" name="part[<?php echo $skey;?>][requested_inventory_id]" id="<?php echo "requested_inventory_id_".$skey; ?>" value="<?php echo $sp->requested_inventory_id; ;?>">
                                            <?php echo form_error('shipped_model_number'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php
                                        if (form_error('shipped_parts_name')) {
                                            echo 'has-error';
                                        } ?>">
                                        <label for="shipped_parts_name" class="col-md-4">Shipped Parts *</label>
                                        <div class="col-md-6">
                                            <select class="form-control spare_parts shipped_parts_name" onchange="change_parts_name('<?php echo $skey;?>')" id="<?php echo "shippedpartsname_".$skey;?>" name="part[<?php echo $skey; ?>][shipped_parts_name]" required="">
                                                <!--                                        <option selected disabled >Select Part Name</option>-->
                                            </select>
                                            <span id="<?php echo "spinner_". $skey;?>" style="display:none"></span>
                                            <?php echo form_error('shipped_parts_name'); ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-5">
                                    <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Requested Parts</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" id="<?php echo "partsname_".$skey;?>" name="part[<?php echo $skey;?>][parts_name]" readonly="readonly" required><?php echo $sp->parts_requested; ?></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <?php if(!is_null($sp->defective_parts_pic)){ ?>
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp->defective_parts_pic;?>" target="_blank"><img src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp->defective_parts_pic;?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php if(!is_null($sp->defective_back_parts_pic)){ ?>
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp->defective_back_parts_pic;?>" target="_blank"><img src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $sp->defective_back_parts_pic;?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="parts_type" class="col-md-4">Part Warranty Status</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="<?php echo "part_warranty_statusid_".$skey;?>" value="<?php if(isset($sp->part_warranty_status) && $sp->part_warranty_status == 1){ echo "In Warranty";} else { echo "Out Of Warranty";} ?>" readonly="readonly" >
                                            <input type="hidden" class="form-control" id="<?php echo "part_warranty_statusid_".$skey;?>" value="<?php if(isset($sp->part_warranty_status) && $sp->part_warranty_status == 1){ echo SPARE_PART_IN_WARRANTY_STATUS; } else { echo SPARE_PART_IN_OUT_OF_WARRANTY_STATUS; } ?>" name="part[<?php echo $skey;?>][part_warranty_status]" readonly="readonly" >
                                        </div>
                                    </div>
                                    <div class="form-group <?php
                                        if (form_error('shipped_part_type')) {
                                            echo 'has-error';
                                        } ?>">
                                        <label for="shipped_part_type" class="col-md-4">Shipped Part Type *</label>
                                        <div class="col-md-6">
                                            <select onchange="change_shipped_part_type('<?php echo $skey;?>')" class="form-control spare_parts shipped_part_type" id="<?php echo "shippedparttype_".$skey;?>" name="part[<?php echo $skey; ?>][shipped_part_type]" required="">
                                                <option selected disabled>Select Part Type</option>
                                            </select>
                                            <?php echo form_error('shipped_part_type'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group <?php
                                        if (form_error('remarks_by_partner')) { echo 'has-error'; } ?>">
                                        <label for="remarks_by_partner" class="col-md-4">Remarks *</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" rows="3" id="<?php echo "remarks_".$skey; ?>" name="part[<?php echo $skey;?>][remarks_by_partner]" placeholder="Please Enter Remarks"  required></textarea>
                                            <?php echo form_error('remarks_by_partner'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="part[<?php echo $skey;?>][approx_value]" id="<?php echo "approx_value_".$skey;?>" value="">
                        <input type="hidden" name="part[<?php echo $skey; ?>][inventory_id]" id="<?php echo "inventory_id_".$skey; ?>">
                        <input type="hidden" name="part[<?php echo $skey; ?>][spare_id]" value="<?php echo $sp->id;?>" id="<?php echo "spare_id_".$skey; ?>">
                    </div>
                    <?php } ?>
                    <div id="template" class="hide">
                        <div class="panel panel-default spare_clone" >
                            <div class="panel-body" >
                                <div class="x_content">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="radio-inline col-md-6" style="font-weight:bold">
                                            <input type="radio" id='shipping_status_1'  value="1" checked>Shipping
                                            </label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group ">
                                            <label for="shipped_model_number" class="col-md-4">Shipped Model Number *</label>
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts"  id="shippedmodelnumberid" >
                                                    <option value="" disabled="" selected="">Select Model Number</option>
                                                    <?php foreach ($inventory_details as $key => $value) { ?> 
                                                    <option value="<?php echo $value['id']; ?>" ><?php echo $value['model_number']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" id="requestedParts" >
                                                <input type="hidden" id="requestedPartsType" >
                                                <input type="hidden" id="shippedmodelnumber" >
                                                <input type="hidden" id="requested_inventory_id" >
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="shipped_parts_name" class="col-md-4">Shipped Parts *</label>
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts "  id="shippedpartsname" >
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                        <label for="shipped_parts_name" class="col-md-4"> Part In Warranty *</label>
                                        <div class="col-md-6">
                                            <select class="form-control" id="part_warranty_status">
                                                <option selected="" disabled="">Select warranty status</option>
                                                <option value="1"> In-Warranty </option>
                                                <option value="2"> Out-Warranty </option>
                                            </select>
                                        </div>
                                    </div>
                                        
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group ">
                                            <label for="shipped_part_type" class="col-md-4">Shipped Part Type *</label>
                                            <div class="col-md-6">
                                                <select  class="form-control spare_parts" id="shippedparttype" >
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-default pull-right removeButton"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="remarks_by_partner" class="col-md-4">Remarks *</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" rows="3" id="remarks"  placeholder="Please Enter Remarks"  required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="approx_value" value="">
                            <input type="hidden" id="inventory_id">
                            <input type="hidden"  value="new" id="spare_id">                                                      
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>Courier Details</h3>
                        <hr>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-6">
                            <div class="form-group <?php
                                if (form_error('awb')) { echo 'has-error'; } ?>">
                                <label for="awb" class="col-md-4">AWB *</label>
                                <div class="col-md-6">
                                    <input type="text" onblur="check_awb_exist()" class="form-control" id="awb" name="awb" value = "" placeholder="Please Enter AWB"  required>
                                    <?php echo form_error('awb'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('shipment_date')) { echo 'has-error';} ?>">
                                <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  id="shipment_date" name="shipment_date"  value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>"  required>
                                    <?php echo form_error('shipment_date'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('courier_image')) { echo 'has-error';} ?>">
                                <label for="courier_image" class="col-md-4">Courier Image *</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  id="courier_image" name="courier_image" >
                                    <input type="hidden" class="form-control"  id="exist_courier_image" name="exist_courier_image" >
                                    <?php echo form_error('courier_image'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?php
                                if (form_error('courier_name')) {echo 'has-error';} ?>">
                                <label for="courier" class="col-md-4">Courier Name *</label>
                                <div class="col-md-6">
                                    <!--                                    <input type="text" class="form-control" id="courier_name" name="courier_name" value = "" placeholder="Please Enter courier Name"  required>-->
                                    <select class="form-control" id="courier_name" name="courier_name" required>
                                        <option selected="" disabled="" value="">Select Courier Name</option>
                                        <?php foreach ($courier_details as $value) { ?> 
                                        <option value="<?php echo $value['courier_code']?>"><?php echo $value['courier_name']?></option>
                                        <?php } ?>
                                    </select>
                                    <?php echo form_error('courier_name'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('courier_price_by_partner')) { echo 'has-error';} ?>">
                                <label for="courier_price_by_partner" class="col-md-4">Courier Price *</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control"  id="courier_price_by_partner" name="courier_price_by_partner" placeholder="Please Enter courier price" required>
                                    <?php echo form_error('courier_price_by_partner'); ?>
                                    <span id="same_awb" style="display:none">This AWB already used same price will be added</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="text-center">
                            <input type="hidden" id="estimate_cost_given_date" name= "estimate_cost_given_date_h" value="<?php echo $spare_parts[0]->estimate_cost_given_date; ?>">
                            <input type="hidden" name="partner_id" id="partner_id" value="<?php echo $spare_parts[0]->partner_id ;?>">
                            <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="<?php echo $spare_parts[0]->assigned_vendor_id ;?>">
                            <input type="hidden" name="is_wh" id="is_wh" value="<?php echo $is_wh ;?>">
                            <input type="hidden" name="amount_due" value="<?php echo $spare_parts[0]->amount_due; ?>">
                            <input type="submit"  value="Update Booking" class="btn btn-md btn-success" id="submit_form"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $('#shipment_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate:false,
        locale:{
            format: 'YYYY-MM-DD'
        }
    });
            
    $('#shipment_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $('#shipment_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
     (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
        {
            setupFormValidation: function ()
            {
            //form validation rules
          $("#update_form").validate({
                rules: {
                
                courier_name:"required",
                awb: "required",
                shipment_date:"required"
                },
                messages: {
               
                courier_name: "Please Courier Name",
                awb: "Please Enter Valid AWB",
                shipment_date:"Please Enter Shipped date"
              
                },
                submitHandler: function (form) {
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
    
    
    $('.shipped_model_number_id').select2();
    $('.shipped_parts_name').select2({
        placeholder:'Select Part Name',
        allowClear:true
    });
    $('.shipped_part_type').select2({
        placeholder:'Select Part Type',
        allowClear:true
    });
    
    
    function change_shipped_model(sp_id){
        var model_number_id = $('#shippedmodelnumberid_' + sp_id).val();
        var model_number = $("#shippedmodelnumberid_" + sp_id+" option:selected").text();
        $('#spinner_'+sp_id).addClass('fa fa-spinner').show();
    
        if(model_number){
            $('#shippedmodelnumber_' + sp_id).val(model_number);
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
                data: { model_number_id:model_number_id},
                success:function(data){
                    
                    $('#shippedparttype_'+sp_id).val('val', "");
                    $('#shippedparttype_' + sp_id).val('Select Part Type').change();
                    $('#shippedparttype_' + sp_id).select2().html(data);
                    $('#shippedpartsname_' + sp_id).val('val', "");
                    $('#shippedpartsname_' + sp_id).val('Select Part Type').change();
                    $('#spinner_' + sp_id).removeClass('fa fa-spinner').hide();
                    
                    var request_part_type = $("#partstype_"+sp_id).val();
                    if(request_part_type){
                        $('#shippedparttype_' +sp_id).val(request_part_type).change(); 
                    }
    
                }
            });
        }else{
            alert("Please Select Model Number");
        }
    }
    
    function change_shipped_part_type(sp_id){
        var model_number_id = $('#shippedmodelnumberid_' + sp_id).val();
        var part_type = $('#shippedparttype_' + sp_id).val();
        $('#spinner_' + sp_id).addClass('fa fa-spinner').show();
        if(model_number_id){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_parts_name',
                data: { model_number_id:model_number_id, entity_id: '<?php echo $spare_parts[0]->partner_id ;?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $spare_parts[0]->service_id; ?>',part_type:part_type,is_option_selected:true },
                success:function(data){
                    
                    $('#shippedpartsname_' + sp_id).val('val', "");
                    $('#shippedpartsname_' + sp_id).val('Select Part Name').change();
                    $('#shippedpartsname_' +sp_id).html(data);
                    $('#spinner_'+ sp_id).removeClass('fa fa-spinner').hide();
                    
                    var request_part = $("#partsname_"+sp_id).val();
                    if(request_part){
                        $('#shippedpartsname_' + sp_id).val(request_part).change(); 
                    }
                }
            });
        }else{
            alert("Please Select Model Number");
        }
    }
    
    function change_parts_name(sp_id){
        var model_number_id = $('#shippedmodelnumberid_' + sp_id).val();
        var part_name = $('#shippedpartsname_' +sp_id).val();
        if(model_number_id && part_name){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_inventory_price',
                data: { part_name:part_name,model_number_id:model_number_id, entity_id: '<?php echo $spare_parts[0]->partner_id ;?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $spare_parts[0]->service_id; ?>' },
                success:function(data){
                    //console.log(data);
                    var obj = JSON.parse(data);
                    if(obj.price){
                        $('#submit_form').attr('disabled',false);
                        $('#approx_value_'+ sp_id).val(obj.price);
                        $('#inventory_id_' +sp_id).val(obj.inventory_id);
                    }else{
                        alert("Inventory Details not found for the selected combination.");
                        $('#submit_form').attr('disabled',true);
                        console.log(data);
                    }
                }
            });
        }
    }
    
    var partIndex = Number('<?php echo (count($spare_parts) - 1);?>');
    $('#update_form').on('click', '.addButton', function () {
            partIndex++;
            var $template = $('#template'),
                $clone = $template
                        .clone()
                        .removeClass('hide')
                        .removeAttr('id')
                        .attr('data-book-index', partIndex)
                        .insertBefore($template);
    
            // Update the name attributes 
            $clone
                .find('[id="shipping_status_1"]').attr('name', 'part[' + partIndex + '][shippingStatus]').attr('id','shippingStatus_'+partIndex).attr("required", true).end()
                .find('[id="shippedmodelnumberid"]').attr('name', 'part[' + partIndex + '][shipped_model_number_id]').attr("onchange", "change_shipped_model('"+partIndex+"')").attr('id','shippedmodelnumberid_'+partIndex).select2({placeholder:'Select Model Number'}).end()
                .find('[id="requested_inventory_id"]').attr('name', 'part[' + partIndex + '][requested_inventory_id]').attr('id','requested_inventory_id_'+partIndex).end()
                .find('[id="shippedmodelnumber"]').attr('name', 'part[' + partIndex + '][shipped_model_number]').attr('id','shippedmodelnumber_'+partIndex).end()
                .find('[id="shippedpartsname"]').attr('name', 'part[' + partIndex + '][shipped_parts_name]').attr("onchange", "change_parts_name('"+partIndex+"')").attr('id','shippedpartsname_'+partIndex).attr("required", true).select2({placeholder:'Select Part Name'}).end()
                .find('[id="shippedparttype"]').attr('name', 'part[' + partIndex + '][shipped_part_type]').attr("onchange", "change_shipped_part_type('"+partIndex+"')").attr('id','shippedparttype_'+partIndex).attr("required", true).select2({placeholder:'Select Part Type'}).end()
                .find('[id="remarks"]').attr('name', 'part[' + partIndex + '][remarks_by_partner]').attr('id','remarks_'+partIndex).end()
                .find('[id="approx_value"]').attr('name', 'part[' + partIndex + '][approx_value]').attr('id','approx_value_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventory_id_'+partIndex).end()
                .find('[id="spare_id"]').attr('name', 'part[' + partIndex + '][spare_id]').attr('id','spare_id_'+partIndex).end()
                .find('[id="part_warranty_status"]').attr('name', 'part[' + partIndex + '][part_warranty_status]').attr('id','part_warranty_status_'+partIndex).end();
        
    
        })
    
        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.spare_clone'),
                index = $row.attr('data-part-index');
                partIndex = partIndex -1;
            $row.remove();
        });
        
        function check_awb_exist(){
            var awb = $("#awb").val();
            if(awb){
                    $.ajax({
                    type: 'POST',
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
                    url: '<?php echo base_url() ?>employee/service_centers/check_warehouse_shipped_awb_exist',
                    data:{awb:awb},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.code === 247){
                            alert("This AWB already used same price will be added");
                            $("#same_awb").css("display","block");
                            $('body').loadingModal('destroy');
                            $("#shipment_date").val(data.message[0].shipped_date);
                            $("#courier_name").val(data.message[0].courier_name_by_partner).trigger('change');
                            $("#courier_price_by_partner").val("0");
                            $("#courier_price_by_partner").css("display","none");
                            if(data.message[0].courier_pic_by_partner){
                                $("#exist_courier_image").val(data.message[0].courier_pic_by_partner);
                                $("#courier_image").css("display","none");
                            }
    
                        } else {
    
                            $('body').loadingModal('destroy');
                            $("#courier_image").css("display","block");
                            $("#courier_price_by_partner").css("display","block");
                            $("#same_awb").css("display","none");
                            $("#exist_courier_image").val("");
                        }
    
                    }
                });
            }
            
        }
        
        $('#courier_name').select2();
        
       $(".part_warranty_status").on('change',function(){                 
                 $("#part_warranty_status_0").val($(this).val());
       }); 
    
</script>
<?php foreach($spare_parts as $ssKey => $sp) { if(!empty($sp->requested_inventory_id)) { ?><script> change_shipped_model('<?php echo $ssKey; ?>'); </script> <?php } } ?>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
