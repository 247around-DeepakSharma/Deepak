<style type="text/css">
    #update_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 0px 0 0px 0px;
    padding: 0;
    text-align: left;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Requested Spare Parts</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form action="#" class ="form-horizontal" >
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="booking_ids" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_ids" name="booking_ids" value = "<?php echo ((isset($spare_parts[0]->booking_id)) ? $spare_parts[0]->booking_id : '') ?>" placeholder="Enter Booking ID" readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="user" class="col-md-4">User</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="user" name="user_name" value = "<?php echo ((isset($spare_parts[0]->name)) ? $spare_parts[0]->name : '') ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="mobile" class="col-md-4">Mobile</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="mobile" name="mobile" value = "<?php echo ((isset($spare_parts[0]->booking_primary_contact_no)) ? $spare_parts[0]->booking_primary_contact_no : '') ?>" placeholder="Enter Mobile" readonly="readonly" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="model_number" class="col-md-4">Model Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="model_number" name="model_number" value = "<?php echo ((isset($spare_parts[0]->model_number)) ? $spare_parts[0]->model_number : '') ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="serial_number" class="col-md-4">Serial Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="serial_number" name="serial_number" value = "<?php echo ((isset($spare_parts[0]->serial_number)) ? $spare_parts[0]->serial_number : '') ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="dop" class="col-md-4">Date of Purchase</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="dop" name="dop" value = "<?php echo ((isset($spare_parts[0]->date_of_purchase)) ? $spare_parts[0]->date_of_purchase : '') ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="invoice_pic" class="col-md-4">Invoice Image</label>
                                    <div class="col-md-6">
                                        <?php if(!empty($spare_parts) && !is_null($spare_parts[0]->invoice_pic)){ ?>
                                        <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo ((isset($spare_parts[0]->invoice_pic)) ? $spare_parts[0]->invoice_pic : '')?>" target="_blank" id="invoice_pic">View Image</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="serial_no_pic" class="col-md-4">Serial Number Image</label>
                                    <div class="col-md-6">
                                        <?php if(!empty($spare_parts) && !is_null($spare_parts[0]->serial_number_pic)){ ?>
                                        <a href="https://s3.amazonaws.com/bookings-collateral/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo ((isset($spare_parts[0]->serial_number_pic)) ? $spare_parts[0]->serial_number_pic : '')?>" target="_blank" id="serial_no_pic">View Image</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <form enctype="multipart/form-data" action="<?php echo base_url(); ?>partner/process_update_spare_parts/<?php echo ((isset($spare_parts[0]->booking_id)) ? $spare_parts[0]->booking_id : '') ?>" class ="form-horizontal" name="update_form" id="update_form"  method="POST">
        <div class="row">
            <div class="col-md-12 col-sm12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2 class="col-md-12">Update Spare Part <button type="button" class="btn btn-primary pull-right addButton">Ship More Parts</button></h2>
                        <div class="clearfix"></div>
                    </div>
                    <input type="hidden" name="request_type" value="<?php echo ((isset($spare_parts[0]->request_type)) ? $spare_parts[0]->request_type : '')?>"/>
                    <input type="hidden" class="form-control" name="booking_id" value = "<?php echo ((isset($spare_parts[0]->booking_id)) ? $spare_parts[0]->booking_id : '') ?>"  required>
                    <?php
                    $purchase_price = 0;
                    $warranty_status = SPARE_PART_IN_WARRANTY_STATUS;
                    foreach ($spare_parts as $key => $value) {                        
                        if ($value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS) {
                            $warranty_status = SPARE_PART_IN_OUT_OF_WARRANTY_STATUS;
                        }
                        ?>
                    <div class="div_class panel panel-default" >
                        <div class="panel-body" >
                            <div class="x_content">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                        <input type="radio" name="part[<?php echo $key;?>][shippingStatus]" class="courier_shipping" required=""  value="1">Shipping
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                            <input type="radio" name="part[<?php echo $key;?>][shippingStatus]" required="" class="courier_not_shipping" value="0">Not Shipping
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="radio-inline col-md-6" style="font-weight:bold">
                                        <input type="radio" name="part[<?php echo $key;?>][shippingStatus]" required="" value="-1">To be Shipped
                                        </label>
                                    </div>
                                </div>
                                                               
                                <div class="col-md-5">
                                   <div style="margin-bottom: 40px;">
                                    <div class="form-group ">
                                        <label for="parts_type" class="col-md-4">Requested Parts Code</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="<?php echo "requestedpartscode_". $key; ?>" name="part[<?php echo $key;?>][requested_parts_code]" value="<?php echo $value->part_number; ?>" readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Requested Parts</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="<?php echo "partsname_".$key; ?>" name="part[<?php echo $key; ?>][parts_name]" readonly="readonly" value="<?php echo $value->parts_requested; ?>" required>
                                            <input type="hidden" name="part[<?php echo $key; ?>][requested_inventory_id]" id="<?php echo "requested_inventory_id_".$key;?>" value="<?php echo $value->requested_inventory_id; ?>" />
                                        </div>
                                    </div>
                                    </div>
                                    <div style="margin-bottom: 40px;">
                                        <div class="form-group">
                                        <label for="shipped_model_number" class="col-md-4">Shipped Model Number *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-7">
                                            <select class="form-control spare_parts shipped_model_number_id" onchange="change_shipped_model('<?php echo $key;?>')" id="<?php echo "shippedmodelnumberid_".$key;?>" name="part[<?php echo $key; ?>][shipped_model_number_id]" required="">
                                                <option value="" disabled="" selected="">Select Model Number</option>
                                                <?php foreach ($inventory_details as $key1 => $value1) { ?> 
                                                <option value="<?php echo $value1['id']; ?>" <?php if($value1['model_number'] == $value->model_number){ echo "selected"; } ?> ><?php echo $value1['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" id="<?php echo "shippedmodelnumber_".$key;?>" name="part[<?php echo $key;?>][shipped_model_number]">
                                        </div>
                                        <?php } else if(isset($appliance_model_details) && !empty($appliance_model_details)){ ?>
                                            <div class="col-md-7">
                                            <select class="form-control spare_parts shipped_model_number_id" onchange="change_shipped_model('<?php echo $key;?>')" id="<?php echo "shippedmodelnumberid_".$key;?>" name="part[<?php echo $key; ?>][shipped_model_number_id]" required="">
                                                <option value="" disabled="" selected="">Select Model Number</option>
                                                <?php foreach ($appliance_model_details as $key1 => $value1) { ?> 
                                                <option value="<?php echo $value1['id']; ?>" <?php if($value1['model_number'] == $value->model_number){ echo "selected"; } ?> ><?php echo $value1['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                            <input type="hidden" id="<?php echo "shippedmodelnumber_".$key;?>" name="part[<?php echo $key;?>][shipped_model_number]">
                                        
                                        <?php } else { ?> 
                                        <div class="col-md-7">
                                            <input required="" type="hidden" id="<?php echo "shippedmodelnumberid_".$key;?>" class="shipped_model_number_id" name="part[<?php echo $key; ?>][shipped_model_number_id]">
                                            <input type="text" class="form-control spare_parts" id="<?php echo "shippedmodelnumber_".$key;?>" name="part[<?php echo $key;?>][shipped_model_number]" value = "<?php echo $value->model_number;?>" placeholder="Shipped Model Number">
                                        </div>
                                        <?php } ?>
                                    </div>                                     
                                    <div class="form-group">
                                            <label for="shipped_parts_name" class="col-md-4">Shipped Parts Name *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                                <div class="col-md-7">
                                                    <select required="" class="form-control spare_parts shipped_parts_name shipped-part-name" id="<?php echo "shippedpartsname_" . $key; ?>" name="part[<?php echo $key; ?>][shipped_parts_name]"  data-key="<?=$key?>">
                                                    </select>
                                                    <span id="spinner" style="display:none"></span>
                                                </div>
                                            <?php } else { ?> 
                                                <div class="col-md-7">
                                                    <input required="" type="text" class="form-control spare_parts shipped-part-name" onchange="change_parts_name('<?php echo $key; ?>')" id="<?php echo "shippedpartsname_" . $key; ?>" name="part[<?php echo $key; ?>][shipped_parts_name]" value = "" placeholder="Shipped Parts Name"  data-key="<?=$key?>">
                                                </div>
                                            <?php } ?>
                                        </div>
                                        
                                    <?php if (isset($inventory_details) && !empty($inventory_details)) { ?>
                                        <div class="form-group">
                                            <label for="shipped_parts_number" class="col-md-4">Shipped Parts Number</label>
                                            <div class="col-md-7">
                                                <select required="" class="form-control spare_parts shipped_parts_number" id="<?php echo "shippedpartsnumber_" . $key; ?>" disabled>
                                                    <option value="">Select Part Name First</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                        
                                    </div>
                                    <?php if($value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){ ?>                                    
                                    <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Invoice Id *</label>
                                        <div class="col-md-7">
                                            <input class="form-control" id="<?php echo "invoice_id_".$key; ?>" name="part[<?php echo $key; ?>][invoice_id]" value="" placeholder="Please Enter Invoice Id" required/>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if($value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){ 
                                            if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                    <div class="form-group">
                                        <label for="hsn_code" class="col-md-4">HSN Code *</label>
                                        <div class="col-md-7">
                                            <select  class="form-control hsn_code" id="<?php echo "hsn_code_" . $key; ?>" name="part[<?php echo $key; ?>][hsn_code]" required=""> </select>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="form-group">
                                        <label for="hsn_code" class="col-md-4">HSN Code *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="<?php echo "hsn_code_" . $key; ?>" name="part[<?php echo $key; ?>][hsn_code]" value = "" placeholder="Please Enter HSN Code"  required>
                                        </div>
                                    </div> 
                                    <?php } } ?>
                                    
                                    <?php if(!is_null($value->estimate_cost_given_date) || $value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){ ?>
                                    <div class="form-group <?php
                                        if (form_error('invoice_amount')) { echo 'has-error'; } ?>">
                                        <label for="invoice_amount" class="col-md-4">Invoice Amount (including tax)</label>
                                        <div class="col-md-7">
                                            <input type="number" class="form-control" id="<?php echo "invoiceamount_". $key; ?>" name="part[<?php echo $key; ?>][invoiceamount]" value = "" placeholder="Please Enter Invoice Amount"  required>
                                            <?php echo form_error('invoice_amount'); ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    
                                      <div class="form-group <?php
                                        if (form_error('remarks_by_partner')) { echo 'has-error'; } ?>">
                                        <label for="remarks_by_partner" class="col-md-4">Remarks*</label>
                                        <div class="col-md-7">
                                            <textarea class="form-control" id="<?php echo "remarks_".$key; ?>" name="part[<?php echo $key;?>][remarks_by_partner]" placeholder="Please Enter Remarks"  required></textarea>
                                            <?php echo form_error('remarks_by_partner'); ?>
                                        </div>
                                    </div>
                                
                               </div>
                                <div class="col-md-5">
                                    <div style="margin-bottom: 40px;">
                                    <div class="form-group ">
                                        <label for="parts_type" class="col-md-4">Requested Parts Type</label>
                                        <div class="col-md-7">
                                            <input type="text"  class="form-control" id="<?php echo "requestedpartstype_". $key; ?>" name="part[<?php echo $key;?>][requested_parts_type]" readonly="readonly" value="<?php echo $value->parts_requested_type; ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Requested Quantity</label>
                                        <div class="col-md-7">
 
                                            <input type="text" class="form-control" id="<?php echo "req_quantity_".$key; ?>" name="part[<?php echo $key; ?>][quantity]" readonly=""  value="<?php echo $value->quantity; ?>"  required /> 
 
                                        </div>
                                    </div>
                                     </div>
                                    <div style="margin-bottom: 40px;">
                                    <div class="form-group">
                                        <label for="shipped_part_type" class="col-md-4">Shipped Parts Type *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-7">
                                            <select required="" class="form-control shipped_part_type spare_parts" onchange="change_shipped_part_type('<?php echo $key;?>')" id="<?php echo "shippedparttype_".$key ?>" name="part[<?php echo $key;?>][shipped_part_type]">
                                                <option selected disabled>Select Part Type</option>
                                            </select>
                                            <span id="<?php echo "spinner_". $key?>" style="display:none"></span>
                                        </div>
                                        <?php } else { ?> 
                                        <div class="col-md-7">                                            
                                            <select required="" class="form-control spare_parts_type" id="<?php echo "shippedpart_type_".$key ?>" name="part[<?php echo $key;?>][shipped_part_type]" value = "">
                                                <option selected disabled>Select Part Type</option>
                                            </select>
                                        </div>
                                        <?php } ?>
                                    </div>
                                                                   
                                   <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Shipped Quantity</label>
                                        <div class="col-md-7">
 
                                            <input type="text" min="1" class="form-control quantity" data-id="<?php echo $key; ?>" id="<?php echo "quantity_".$key; ?>" name="part[<?php echo $key; ?>][shipped_quantity]" readonly="" value="<?php echo $value->quantity; ?>"    required  />


                                            <span id="error_span_<?php echo $key; ?>" style="color:red;" class="hide"></span>
 
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <?php if($value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){ ?>
                                    <div class="form-group">
                                        <label for="invoice_amount" class="col-md-4">Invoice Date *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control invoice_date" id="<?php echo "invoice_date_". $key; ?>" name="part[<?php echo $key; ?>][invoice_date]" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" style="background-color:#FFF; cursor: pointer;">
                                        </div>
                                    </div>
                                    <?php } ?>
                                    
                                                                                                         
                                    <?php if(!is_null($value->estimate_cost_given_date) || $value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){ ?>
                                    <div class="form-group">
                                        <label for="gst_number" class="col-md-4">GST Rate *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="<?php echo "gst_rate_". $key; ?>" name="part[<?php echo $key; ?>][gst_rate]" value = "" placeholder="Please Enter GST Rate  "  required>
                                            
                                        </div>
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if(!is_null($value->estimate_cost_given_date) || $value->part_warranty_status == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){  $purchase_price += $value->purchase_price; ?>
                                    <div class="form-group <?php
                                        if (form_error('incoming_invoice')) { echo 'has-error';} ?>">
                                        <label for="incoming_invoice" class="col-md-4">Spare Invoice (PDF)*</label>
                                        <div class="col-md-7">
                                            <input type="file" name="incominginvoice[<?php echo $key; ?>]" id="<?php echo "incominginvoice_".$key; ?>" class="form-control" required />
                                            <input type="hidden"  name="part[<?php echo $key;?>][purchase_price]" value="<?php echo $value->purchase_price; ?>" id="<?php echo "purchase_price".$key; ?>" class="form-control purchase_price"  />
                                            <?php echo form_error('incoming_invoice'); ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="part[<?php echo $key;?>][spare_id]"  id="<?php echo "spare_id_".$key; ?>" value="<?php echo $value->id;?>">
<!--                    <input type="hidden" name="part[<?php echo $key;?>][inventory_id]" id="<?php echo "inventoryid_". $key;?>">-->
                    <input type="hidden" id="<?php echo "estimatecostgivendate_".$key ?>" name= "part[<?php echo $key;?>][estimate_cost_given_date_h]" value="<?php echo $value->estimate_cost_given_date; ?>">
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
                                        <!--<div style="margin-bottom: 40px;">  </div> this one is causing alignment issue-->
                                      <div class="form-group">
                                        <label for="shipped_model_number" class="col-md-4">Shipped Model Number *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-7">
                                            <select class="form-control spare_parts " id="shippedmodelnumberid" >
                                                <option value="" disabled="" selected="">Select Model Number</option>
                                                <?php foreach ($inventory_details as $key1 => $value1) { ?> 
                                                <option value="<?php echo $value1['id']; ?>" ><?php echo $value1['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" id="shippedmodelnumber" >
                                        </div>
                                        <?php } else { ?> 
                                        <div class="col-md-7">
                                            <input type="hidden" id="shippedmodelnumberid" class="shipped_model_number_id" >
                                            <input type="text" class="form-control spare_parts" id="shippedmodelnumber"  value = "" placeholder="Shipped Model Number">
                                        </div>
                                        <?php } ?>
                                    </div>
                                      <div class="form-group">
                                        <label for="shipped_parts_name" class="col-md-4">Shipped Parts Name *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-7">
                                            <select class="form-control spare_parts shipped-part-name" id="shippedpartsname" >
                                            </select>
                                            <span id="spinner" style="display:none"></span>
                                        </div>
                                        <?php } else { ?> 
                                        <div class="col-md-6">
                                            <input type="text" class="form-control spare_parts shipped-part-name" id="shippedpartsname" value = "" placeholder="Shipped Parts Name" >
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php if (isset($inventory_details) && !empty($inventory_details)) { ?>
                                        <div class="form-group">
                                            <label for="shipped_parts_name" class="col-md-4">Shipped Parts Number</label>
                                            <div class="col-md-7">
                                                <select class="form-control spare_parts" id="shippedpartsnumber" disabled>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                     <?php if($request_type == REPAIR_OOW_TAG){ ?>   
                                     <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Invoice Id *</label>
                                        <div class="col-md-7">
                                            <input class="form-control" id="invoice_id"  value="" placeholder="Please Enter Invoice Id" required/>
                                        </div>
                                    </div>  
                                    <?php if (isset($inventory_details) && !empty($inventory_details)) { ?>
                                    <div class="form-group">
                                        <label for="hsn_code" class="col-md-4">HSN Code *</label>
                                        <div class="col-md-7">
                                            <select  class="form-control" id="hsn_code">
                                               <option value="" disabled="" selected="">Select HSN code</option> 
                                            </select>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                      <div class="form-group">
                                        <label for="hsn_code" class="col-md-4">HSN Code *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="hsn_code" value = "" placeholder="Please Enter HSN Code"  required>
                                        </div>
                                    </div>
                                    <?php } ?>                                      
                                    <div class="form-group">
                                        <label for="invoice_amount" class="col-md-4">Invoice Amount (including tax)</label>
                                        <div class="col-md-7">
                                            <input type="number" class="form-control" id="invoiceamount"  value = "" placeholder="Please Enter Invoice Amount"  required>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="remarks_by_partner" class="col-md-4">Remarks*</label>
                                        <div class="col-md-7">
                                            <textarea class="form-control" id="remarks" placeholder="Please Enter Remarks"  required></textarea>
                                           
                                        </div>
                                    </div>
                                    
                                </div>
                                    
                                <div class="col-md-5">
                                    <div style="margin-bottom: ">  
                                        <div class="form-group" style="">
                                            <label for="shipped_part_type" class="col-md-4">Shipped Parts Type *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-7">
                                                <select class="form-control spare_parts part_type_data" id="shippedparttype" >
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>

                                            <?php } else { ?> 
                                            <div class="col-md-7">                                            
                                                <select required="" class="form-control spare_parts_type" id="shippedparttype"  value = "">
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                            </div>
                                            <?php } ?>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-default pull-right removeButton"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="parts_name" class="col-md-4">Shipped Quantity</label>
                                        <div class="col-md-7">
                                            <input type="text" min="1" class="form-control quantity " id="quantity" value="1" name="" readonly=""     required  />
                                            <span id="error_span" style="color:red;" class="hide"></span>


                                            <span id="error_span" style="color:red;" class="hide"></span>
                                        </div>
                                    </div>
                                    <?php if ($request_type == REPAIR_OOW_TAG) { ?>   
                                    <div class="form-group">
                                        <label for="invoice_amount" class="col-md-4">Invoice Date *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control invoice_date" onclick="date_picker()" id="invoice_date" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" style="background-color:#FFF; cursor: pointer;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="gst_number" class="col-md-4">GST Rate *</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" id="gst_rate" value = "" placeholder="Please Enter GST Rate"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="incoming_invoice" class="col-md-4">Spare Invoice (PDF)*</label>
                                        <div class="col-md-7">
                                            <input type="file" id="incominginvoice" class="form-control" required />
                                            <input type="hidden"  value="" id="purchase_price" class="form-control purchase_price"  />
                                        </div>
                                    </div>
                                     <?php } ?>
                                </div>
                                    
                              </div>
                            </div>
                            <input type="hidden"  id="inventoryid">
                            <input type="hidden" id="spare_id">
                        </div>
                    </div>
                </div>
            </div>
        </div>       
        <?php //if($spare_parts[0]->request_type != REPAIR_OOW_TAG){ ?>
        <div class="row" id="courier_detail_section">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Update Challan Details</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-6">
                            <div class="form-group <?php
                                if (form_error('awb')) { echo 'has-error'; } ?>">
                                <label for="awb" class="col-md-4">AWB *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="awb" name="awb" value = "" placeholder="Please Enter AWB"  required>
                                    <?php echo form_error('awb'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('shipment_date')) { echo 'has-error';} ?>">
                                <label for="shipment_date" class="col-md-4">Shipment Date *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" readonly  style="background-color:#FFF; cursor: pointer;" id="shipment_date" name="shipment_date"  value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>"  required>
                                    <?php echo form_error('shipment_date'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('approx_value')) { echo 'has-error'; } ?>">
                                <label for="approx_value" class="col-md-4">Approx Value <?php if($warranty_status != SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){  ?>*<?php } ?></label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="approx_value" name="approx_value" max="100000" value = "" placeholder="Please Enter approx value"  <?php if(isset($spare_parts[0]->part_warranty_status) && ($spare_parts[0]->part_warranty_status != SPARE_PART_IN_OUT_OF_WARRANTY_STATUS)){  ?> required  <?php } ?>>
                                    <?php echo form_error('approx_value'); ?>
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
                                        <?php foreach ($courier_details as $value1) { ?> 
                                        <option value="<?php echo $value1['courier_code']?>"><?php echo $value1['courier_name']?></option>
                                        <?php } ?>
                                    </select>
                                    <?php echo form_error('courier_name'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('partner_challan_number')) { echo 'has-error'; } ?>">
                                <label for="partner_challan_number" class="col-md-4">Challan Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="partner_challan_number" name="partner_challan_number" value = "" placeholder="Please Enter challan Number">
                                    <?php echo form_error('partner_challan_number'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php
                                if (form_error('challan_file')) { echo 'has-error'; } ?>">
                                <label for="challan_file" class="col-md-4">Challan File</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" id="challan_file" name="challan_file">
                                    <?php echo form_error('challan_file'); ?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="courier_status" name="courier_status" value="1">
                    </div>
                </div>
            </div>
        </div>
        <?php //} ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="text-center">
                            
                            <input type="hidden" name="assigned_vendor_id" id="assigned_vendor_id" value="<?php echo ((isset($spare_parts[0]->assigned_vendor_id)) ? $spare_parts[0]->assigned_vendor_id : '') ?>">
                            <input type="hidden" name="part_warranty_status" value="<?php echo $warranty_status ;?>">
                            <input type="submit"  <?php if ($purchase_price > 0) { ?> 
                                onclick="return check_invoice_amount()" <?php } ?> value="Update Booking" class="btn btn-md btn-success" id="submit_form"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    
    $(".hsn_code").on("change",function(){
    var string_id = $(this).attr("id");
    var array = string_id.split("_");
    var hsn_code = $(this).find('option:selected').attr("data-gst");
        if(hsn_code!='' && hsn_code != 'undefined'){
            $("#gst_rate_"+array[2]).val(hsn_code);
        }
    });
    
    date_picker();
    function date_picker(){
    var someDate = new Date();
    var numberOfDaysToAdd = 7;
    
    someDate.setDate(someDate.getDate() + numberOfDaysToAdd); 
    $('#shipment_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        maxDate: someDate,
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
     
    $(".invoice_date").daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        maxDate: someDate,
        minDate:false,
        locale:{
            format: 'YYYY-MM-DD'
        }
    });
    
    $(".invoice_date").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $(".invoice_date").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });     
    }
     
    
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
                shipment_date:"required",
                approx_value:{
                    min:1,
                    required: true,
                    maxlength: 100000
                }
                },
                messages: {
                courier_name: "Please Courier Name",
                awb: "Please Enter Valid AWB",
                shipment_date:"Please Enter Shipped date",
                approx_value :"Please Enter Approx Value."
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
    
    function check_invoice_amount(){
        var flag = true;
        /*
        $(".purchase_price").each(function (i){
                var estimate_given = $(this).val();
                var invoice_amount = Number($("#invoiceamount_" + i).val()); 
                if(Number(invoice_amount) < 1){
                    swal("OOPS!", "Please Enter Invoice amount.", "error");
                    flag = false;
                    return false;
                } else if(invoice_amount > Number(estimate_given)){
                    swal("OOPS!", "Invoice amount exceeding the quote provided earlier.", "error");
                    flag = false;
                    return false;
                }
        });
        */
        if(flag){
            return true;
        } else {
            return false;
        }
       
    }
    
    function ucword(str){
        str = str.toLowerCase().replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, function(replace_latter) { 
            return replace_latter.toUpperCase();
        });  
        return str;  
    }
       
    <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 

    $('.shipped_model_number_id').select2();
    $('.shipped_parts_name').select2({
        placeholder:'Select Part Name',
        allowClear:true
    });
    
    $('.shipped_part_type').select2({
        placeholder:'Select Part Type',
        allowClear:true
    });
    
     $('.hsn_code').select2({
        placeholder:'Select HSN Code',
        allowClear:true
    });
 
    $(document).on('keyup', ".quantity", function(e)
       {
        //alert();
        var id = $(this).attr("id");
        var str_arr =id.split("_");
        var indexId = str_arr[1]; 
        var val = parseInt($(this).val());

         var charCode = (e.which) ? e.which : e.keyCode;
        if ((charCode > 47 && charCode < 58) || (charCode > 95 && charCode < 105) || charCode == 8) {

        if (val>0) {
        var max = parseInt($("#shippedpartsname_"+indexId+" option").filter(":selected").attr("data-maxquantity"));
        if(val>max){
         $(this).val("1");
           $("#error_span_"+indexId).text('Maximum quantity allowed to ship is : '+max);
           $("#error_span_"+indexId).removeClass('hide');
        }else{
            $("#error_span_"+indexId).addClass('hide');
        }
        }else{
        $(this).val("");
          $("#error_span_"+indexId).text('0 quantity,special charcter or negative value not allowed ');
          $("#error_span_"+indexId).removeClass('hide'); 

        }
        }else{

        $(this).val("");
           $("#error_span_"+indexId).text('');
           $("#error_span_"+indexId).text('Special chars not allowed');
           $("#error_span_"+indexId).removeClass('hide');
        }
       });
 
    
    function change_shipped_model(key){
        
        var model_number_id = $('#shippedmodelnumberid_' + key).val();
        var model_number = $("#shippedmodelnumberid_" + key + " option:selected").text();
        $('#spinner_' + key).addClass('fa fa-spinner').show();
        if(model_number){
            $('#shippedmodelnumber_' + key).val(model_number);
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
                data: {model_number_id:model_number_id },
                success:function(data){
                    //.log(data);
                    $('#shippedparttype_' +key ).val('val', "");
                    $('#shippedparttype_' + key).val('Select Part Type').change();
                    $('#shippedparttype_' + key).select2().html(data);
                    $('#spinner_' + key).removeClass('fa fa-spinner').hide();                    
                    var part_type = $("#requestedpartstype_"+key).val();
                    request_part_type = ucword(part_type);
                    if(request_part_type){
                        $('#shippedparttype_' +key).val(request_part_type).change(); 
                    }
                }
            });
        }else{
            alert("Please Select Model Number");
        }
    }
        
    function change_shipped_part_type(key){
        var model_number_id = $('#shippedmodelnumberid_' + key).val();
        var part_type = $('#shippedparttype_'+ key).val();
       
        $('#spinner_'+key).addClass('fa fa-spinner').show();
        if(model_number_id && part_type){
            var requested_inventory_id = $("#requested_inventory_id_"+key).val();
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_parts_name',
                data: { model_number_id:model_number_id,requested_inventory_id:requested_inventory_id, entity_id: '<?php echo ((isset($spare_parts[0]->partner_id)) ? $spare_parts[0]->partner_id : '') ?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo ((isset($spare_parts[0]->service_id)) ? $spare_parts[0]->service_id : '') ?>',part_type:part_type,is_option_selected:true },
                success:function(data){
                    //console.log(data);
                    $('#shippedpartsname_'+key).val('val', "");
                    $('#shippedpartsname_' +key).val('Select Part Name').change();
                    $('#shippedpartsname_' + key).html(data).change();
                    $('#spinner_' + key).removeClass('fa fa-spinner').hide();
                    //change_parts_number(key);
//                    var request_part_type = $("#partsname_"+key).val();
//                    if(request_part_type){
//                        $('#shippedpartsname_' +key).val(request_part_type).change(); 
//                    }
                }
            });
        }else{
          //  alert("Please Select Model Number");
        }
    }
    
//    $('#shipped_parts_name').on('change', function() {
//        change_parts_name();
//        
//    });
     
    function change_parts_name(key){
    
  var model_number_id = $('#shipped_model_number_id').val();
        var part_name = $('#shippedpartsname_' + key).val();
        var inventory=  $('#shippedpartsname_' +key).find(':selected').attr('data-inventory');
        var service_id =  $('#shippedparttype_' +key).find(':selected').attr('data-service_id');
        $('#inventoryid_'+key).val(inventory);
        //$("#quantity_0").removeAttr("readonly");
        get_hsn_code_list(key,service_id);
        if(model_number_id && part_name){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_inventory_price',
                data: { part_name:part_name,model_number_id:model_number_id, entity_id: '<?php echo ((isset($spare_parts[0]->partner_id)) ? $spare_parts[0]->partner_id : '') ?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo ((isset($spare_parts[0]->service_id)) ? $spare_parts[0]->service_id : '') ?>'},
                success:function(data){
                    //console.log(data);
                    var obj = JSON.parse(data);
                    if(obj.inventory_id){
                        $('#submit_form').attr('disabled',false);
                    }else{
                        alert("Inventory Details not found for the selected combination.");
                        $('#submit_form').attr('disabled',true);
                    }
                    
                }
            });
        }
    }
    $(document).ready(function(){
        $(document).on("change",".shipped-part-name",function(){
            var key = $(this).data("key");

            if(typeof key=="undefined" || key === null){
                return false;
            }
            var part_name = $(this).val();
            var model_number_id = $('#shippedmodelnumberid_' + key).val();
            var part_type = $('#shippedparttype_'+ key).val();

            $('#spinner_'+key).addClass('fa fa-spinner').show();
            if(!!part_name){
                if(model_number_id && part_type){
                    var requested_inventory_id = $("#requested_inventory_id_"+key).val();
                    $.ajax({
                        method:'POST',
                        url:'<?php echo base_url(); ?>employee/inventory/get_part_number',
                        data: {
                            model_number_id:model_number_id,
                            requested_inventory_id:requested_inventory_id,
                            entity_id: '<?php echo ((isset($spare_parts[0]->partner_id)) ? $spare_parts[0]->partner_id : '') ?>' ,
                            entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' ,
                            service_id: '<?php echo ((isset($spare_parts[0]->service_id)) ? $spare_parts[0]->service_id : '') ?>',
                            part_type:part_type,
                            is_option_selected:true,
                            part_name : part_name
                        },
                        success:function(data){
                            //console.log(data);
                            $('#shippedpartsnumber_' + key).val("");
                            $('#shippedpartsnumber_' + key).val('Select Part Number').change();
                            $('#shippedpartsnumber_' +key).html(data).change();
                            $('#spinner_'+ key).removeClass('fa fa-spinner').hide();
                            $("#quantity_"+key).removeAttr("readonly");
                            $('#shippedpartsnumber_' + key).select2();

                        }
                    });
                }else{
                  //  alert("Please Select Model Number");
                }
            }else{
                $('#shippedpartsnumber_' + key).empty().select2({placeholder:'Select part name first'});
            }
        });
    });
        
    <?php } ?>
    
    $('#courier_name').select2();
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
            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                $clone
                .find('[id="shipping_status_1"]').attr('name', 'part[' + partIndex + '][shippingStatus]').attr('class','courier_shipping').attr("required", true).end()
                .find('[id="shippedmodelnumberid"]').attr('name', 'part[' + partIndex + '][shipped_model_number_id]').attr("onchange", "change_shipped_model('"+partIndex+"')").attr('id','shippedmodelnumberid_'+partIndex).select2({placeholder:'Select Model Number'}).attr('id','shippedmodelnumberid_'+partIndex).attr("required", true).end()
                .find('[id="inventoryid"]').attr('name', 'part[' + partIndex + '][requested_inventory_id]').attr('id','inventoryid_'+partIndex).end()
                .find('[id="shippedmodelnumber"]').attr('name', 'part[' + partIndex + '][shipped_model_number]').attr('id','shippedmodelnumber_'+partIndex).end()
                .find('[id="shippedpartsname"]').attr('name', 'part[' + partIndex + '][shipped_parts_name]').data("key",partIndex).attr("onchange", "change_parts_name('"+partIndex+"')").attr('id','shippedpartsname_'+partIndex).attr("required", true).select2({placeholder:'Select Part Name'}).end()
                .find('[id="shippedpartsnumber"]').attr('id','shippedpartsnumber_'+partIndex).select2({placeholder:'Select Part Number'}).end()
                .find('[id="shippedparttype"]').attr('name', 'part[' + partIndex + '][shipped_part_type]').attr("onchange", "change_shipped_part_type('"+partIndex+"')").attr('id','shippedparttype_'+partIndex).attr("required", true).select2({placeholder:'Select Part Type'}).end()
                .find('[id="remarks"]').attr('name', 'part[' + partIndex + '][remarks_by_partner]').attr('id','remarks_'+partIndex).end()
                .find('[id="approx_value"]').attr('name', 'part[' + partIndex + '][approx_value]').attr('id','approx_value_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventory_id_'+partIndex).end()
                .find('[id="invoice_id"]').attr('name', 'part[' + partIndex + '][invoice_id]').attr('id','invoice_id_'+partIndex).end()
                .find('[id="hsn_code"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','hsn_code_'+partIndex).attr("onchange", "get_hsn_code_list('"+partIndex+"','')").select2({placeholder:'Select HSN Code'}).end() 
                .find('[id="invoiceamount"]').attr('name', 'part[' + partIndex + '][invoiceamount]').attr('id','invoiceamount_'+partIndex).end()
                .find('[id="incominginvoice"]').attr('name', 'incominginvoice[' + partIndex + ']').attr('id','incominginvoice_'+partIndex).end()
                .find('[id="invoice_date"]').attr('name', 'part[' + partIndex + '][invoice_date]').attr('id','invoice_date_'+partIndex).end()
                .find('[id="gst_rate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','gst_rate_'+partIndex).end()
                .find('[id="purchase_price"]').attr('name', 'part[' + partIndex + '][purchase_price]').attr('id','purchase_price_'+partIndex).end()
                .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][shipped_quantity]').attr('id','quantity_'+partIndex).end()
                .find('[id="error_span"]').attr('id','error_span_'+partIndex).end()
                .find('[id="spare_id"]').attr('name', 'part[' + partIndex + '][spare_id]').attr('id','spare_id_'+partIndex).end();  
    
        })
            <?php } else { ?>
                 $clone
                .find('[id="shipping_status_1"]').attr('name', 'part[' + partIndex + '][shippingStatus]').attr('id','shippingStatus_'+partIndex).attr('class','courier_shipping').attr("required", true).end()
                .find('[id="shippedmodelnumberid"]').attr('name', 'part[' + partIndex + '][shipped_model_number_id]').attr("onchange", "change_shipped_model('"+partIndex+"')").attr('id','shippedmodelnumberid_'+partIndex).attr("required", true).end()
                .find('[id="inventoryid"]').attr('name', 'part[' + partIndex + '][requested_inventory_id]').attr('id','inventoryid_'+partIndex).end()
                .find('[id="shippedmodelnumber"]').attr('name', 'part[' + partIndex + '][shipped_model_number]').attr('id','shippedmodelnumber_'+partIndex).end()
                .find('[id="shippedpartsname"]').attr('name', 'part[' + partIndex + '][shipped_parts_name]').data("key",partIndex).attr('id','shippedpartsname_'+partIndex).attr("required", true).end()
                .find('[id="shippedparttype"]').attr('name', 'part[' + partIndex + '][shipped_part_type]').attr('id','shippedparttype_'+partIndex).attr("required", true).end()
                .find('[id="remarks"]').attr('name', 'part[' + partIndex + '][remarks_by_partner]').attr('id','remarks_'+partIndex).end()
                .find('[id="approx_value"]').attr('name', 'part[' + partIndex + '][approx_value]').attr('id','approx_value_'+partIndex).end()
                .find('[id="inventory_id"]').attr('name', 'part[' + partIndex + '][inventory_id]').attr('id','inventory_id_'+partIndex).end()
                .find('[id="invoice_id"]').attr('name', 'part[' + partIndex + '][invoice_id]').attr('id','invoice_id_'+partIndex).end()
                .find('[id="hsn_code"]').attr('name', 'part[' + partIndex + '][hsn_code]').attr('id','hsn_code_'+partIndex).attr("onchange", "get_hsn_code_list('"+partIndex+"','')").select2({placeholder:'Select HSN Code'}).end() 
                .find('[id="invoiceamount"]').attr('name', 'part[' + partIndex + '][invoiceamount]').attr('id','invoiceamount_'+partIndex).end()
                .find('[id="incominginvoice"]').attr('name', 'incominginvoice[' + partIndex + ']').attr('id','incominginvoice_'+partIndex).end()
                .find('[id="invoice_date"]').attr('name', 'part[' + partIndex + '][invoice_date]').attr('id','invoice_date_'+partIndex).end()
                .find('[id="gst_rate"]').attr('name', 'part[' + partIndex + '][gst_rate]').attr('id','gst_rate_'+partIndex).end()
                .find('[id="purchase_price"]').attr('name', 'part[' + partIndex + '][purchase_price]').attr('id','purchase_price_'+partIndex).end()
                 .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][shipped_quantity]').attr('id','quantity_'+partIndex).end()
                 .find('[id="error_span"]').attr('id','error_span_'+partIndex).end()
                .find('[id="spare_id"]').attr('name', 'part[' + partIndex + '][spare_id]').attr('id','spare_id_'+partIndex).end();
    
        })
           <?php } ?>
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.spare_clone'),
                index = $row.attr('data-part-index');
                partIndex = partIndex -1;
            $row.remove();
        });  
        
        $(".courier_not_shipping").click(function(){
               courier_deatil_visibility();       
        });
        
        $(".addButton").click(function(){
            $("#courier_detail_section").show();
            $("#courier_status").val('1');
        });
        
         $(".courier_shipping").click(function(){
            $("#courier_detail_section").show();
            $("#courier_status").val('1');
        });
        
        function courier_deatil_visibility(){
            var flag = false;
            $(".courier_shipping:checked").each(function() {
            var check_val  = $(this).val();
            if(check_val !='' && check_val == 1){
             flag = true;
             break;
            }
            });

            if(flag){
              $("#courier_detail_section").show();
              $("#courier_status").val('1');
            }else{
              $("#courier_detail_section").hide();  
              $("#courier_status").val('0');
            }
        }
        
</script>
<?php if(isset($appliance_model_details) && !empty($appliance_model_details)){ ?>
<script>
    function change_shipped_model(key){

            var model_number_id = $('#shippedmodelnumberid_' + key).val();
            var model_number = $("#shippedmodelnumberid_" + key + " option:selected").text();
            $('#spinner_' + key).addClass('fa fa-spinner').show();
            if(model_number){
                $('#shippedmodelnumber_' + key).val(model_number);
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
                    data: {model_number_id:model_number_id },
                    success:function(data){
                        //console.log(data);
                        $('#shippedparttype_' +key ).val('val', "");
                        $('#shippedparttype_' + key).val('Select Part Type').change();
                        $('#shippedparttype_' + key).html(data).select2();
                        $('#spinner_' + key).removeClass('fa fa-spinner').hide();
                        /*
                            NOTE: this part will fill part type
                            however this is not recommended to fill with ajax here will be removed soon
                            and will come prefilled from server. ~Priyank
                            */
                        var part_type = $("#requestedpartstype_"+key).val();
                        request_part_type = ucword(part_type);
                        if(request_part_type){
                            $('#shippedparttype_0 option').each(function(){
                                if (this.value.toLowerCase() == request_part_type.toLowerCase()) {
                                    $('#shippedparttype_' +key).val(this.value).change();
                                    return false;
                                }
                            });
                        }
                    }
                });
            }else{
                alert("Please Select Model Number");
            }
        }
        
        
    function get_hsn_code_list(key,service_id){
        var hsn_code = $("#hsn_code_"+key+" option:selected").attr("data-gst");
        if(hsn_code!='' && hsn_code != 'undefined'){
            $("#gst_rate_"+key).val(hsn_code);
        }
        if(service_id !=''){
          $.ajax({
             method:'POST',
             url:'<?php echo base_url(); ?>employee/inventory/get_hsn_code_list',
             data: { service_id:service_id},
             success:function(data){                       
                 $('#hsn_code_'+key).html(data);
             }
        });  
        }
        
    } 
        
</script>
<?php } ?>

 <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
        <?php foreach($spare_parts as $ssKey => $sp) { ?>
         <script> 
                change_shipped_model('<?php echo $ssKey; ?>'); 
                get_hsn_code_list('<?php echo $ssKey; ?>','<?php echo $sp->service_id; ?>');
         </script> 
             <?php  } ?>
     <?php }else{ ?>         
<script type="text/javascript">
    $(document).ready(function(){       
    var service_id = "<?php echo ((isset($spare_parts[0]->service_id)) ? $spare_parts[0]->service_id : '') ?>"; 
    get_inventory_pary_type(service_id,'shippedparttype_0');
    $(".addButton").on('click',function(){  
        var service_id = "<?php echo ((isset($spare_parts[0]->service_id)) ? $spare_parts[0]->service_id : '') ?>";    
        var numItems = $('.spare_clone').length;
        spare_part_type_id = "shippedparttype_"+numItems;
        get_inventory_pary_type(service_id,spare_part_type_id);

    });

    function get_inventory_pary_type(service_id,spare_part_type_id){
       $.ajax({
       method:'POST',
       url:'<?php echo base_url(); ?>employee/inventory/get_inventory_parts_type',
       data: { service_id:service_id},
       success:function(data){                       
           $('#'+spare_part_type_id).html(data);  
           var section_length = $(".div_class").length
            for(i=0; i < section_length; i++){
                $("#shippedpart_type_"+i).html(data);
            }
           $('#shippedpart_type_0 option[value="<?php echo (isset($spare_parts[0]->parts_requested) ? strtoupper($spare_parts[0]->parts_requested) : '') ?>"]').attr('selected','selected');                
       }
    });
    } 
    });
</script>
        
<?php } ?>
