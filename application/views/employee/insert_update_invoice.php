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
    label.error {
        color:red;
    }
    .padding_space{
        padding: 6px 5px;
    }
    .cannot_update_invoice {
        color:red;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading ">
                <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice (".$invoice_details[0]['invoice_id'].")"; } else { echo "Insert Invoice";}?>  </h1>
            </div>
            <div class="panel-body">
                <div class="error_msg_div" style="display:none;">
                    <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><span id="error_msg"></span></strong>
                    </div>
                </div>
                <form enctype="multipart/form-data"  class="form-horizontal" id="insertUpdateForm" method="post" novalidate >
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['vertical'])){ echo $invoice_details[0]['vertical'];  } ?>" id="vertical_input">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['category'])){ echo $invoice_details[0]['category'];  } ?>" id="category_input">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['sub_category'])){ echo $invoice_details[0]['sub_category'];  } ?>" id="sub_category_input">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['third_party_entity'])){ echo $invoice_details[0]['third_party_entity'];  } ?>" id="third_party_entity" name="third_party_entity">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['third_party_entity_id'])){ echo $invoice_details[0]['third_party_entity_id'];  } ?>" id="third_party_entity_id" name="third_party_entity_id">
<!--                    <input type="hidden" value="<?php //if(isset($invoice_details[0]['invoice_file_pdf'])){ echo $invoice_details[0]['invoice_file_pdf'];  } ?>" id="invoice_file_pdf" name="invoice_file_pdf" >-->
                    <div class="row">
                        <div class="col-md-12 col-md-offset-3">
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="Around Type">247Around is</label>
                                <div class="col-md-9">
                                    <label class="radio-inline">
                                    <input onchange="control_type_code(1)" <?php if (isset($invoice_details[0]['type_code'])) {
                                        if($invoice_details[0]['type_code'] == "B"){ echo "checked";}
                                        } ?> data-val="true" data-val-required="247Around Type is required." class="around_type" id="Buyer" name="around_type" type="radio" value="B" required>
                                    Buyer
                                    </label>
                                    <label class="radio-inline">
                                    <input onchange="control_type_code(1)" <?php if (isset($invoice_details[0]['type_code'])) {
                                        if($invoice_details[0]['type_code'] == "A"){ echo "checked";}
                                        } ?> class="around_type" name="around_type" id="Seller" type="radio" value="A">
                                    Seller
                                    </label>
                                    <label for="around_type" class="error"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div class="col-md-6 ">
                                <div class="form-group <?php if( form_error('gst_number') ) { echo 'has-error';} ?>">
                                    <label for="gst_number" class="col-md-4">247around GST Number *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="gst_number" name="gst_number" onchange="check_gst_tax_type()" required>
                                            <option value="" disabled selected>Select 247around GST Number *</option>
                                        </select>
                                        <label for="gst_number" class="error"></label>
                                    </div>
                                    <?php echo form_error('gst_number'); ?>
                                    <?php if (isset($invoice_details[0]['invoice_id'])) {} else {?> <a href="javascript:void(0)" id="fetch_invoice_id" onclick="fetch_invoice_id()" class="btn btn-sm btn-success" >Fetch Invoice ID</a><?php  } ?>
                                </div>
                                <div class="form-group" >
                                    <label for="reference_invoice_id" class="col-md-4">Reference Invoice ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="reference_invoice_id" id="reference_invoice_id" value="<?php if (isset($invoice_details[0]['reference_invoice_id'])) {
                                        echo $invoice_details[0]['reference_invoice_id'];
                                        } ?>" placeholder="Reference Invoice ID" />
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="from_date" class="col-md-4">Period *</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="from_date" class="form-control" placeholder="Select Date" name="from_date" type="text" required readonly='true' style="background-color:#fff;" value="">
                                            <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <label for="from_date" class="error"></label>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('vendor_partner_id') ) { echo 'has-error';} ?>">
                                    <label for="vendor_partner_id" class="col-md-4">Entity *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="vendor_partner_id" name="vendor_partner_id" onchange="check_gst_tax_type(), tcs_input_change()" required>
                                            <option value="" disabled selected>Select Entity</option>
                                        </select>
                                        <label for="vendor_partner_id" class="error"></label>
                                    </div>
                                     <?php echo form_error('vendor_partner_id'); ?>
                                </div>
                                <div class="form-group" >
                                    <label for="vertical" class="col-md-4">Vertical *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="vertical" id="vertical" onchange="get_category('<?php echo base_url(); ?>')" required>
                                        </select>
                                        <label for="vertical" class="error"></label>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="category" class="col-md-4">Category *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="category" id="category" onchange="get_sub_category('<?php echo base_url(); ?>')" required>
                                        </select>
                                        <label for="category" class="error"></label>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="sub_category" class="col-md-4">Sub Category *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="sub_category" id="sub_category" onchange="get_accounting(this);" required>
                                        </select>
                                        <label for="sub_category" class="error"></label>
                                    </div>
                                </div>
                                
                                <div class="form-group" >
                                    <label for="accounting" class="col-md-4">Accounting *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="accounting" id="accounting" required >
                                            <option selected disabled>Select Accounting Yes/No</option>
                                            <option value="1" <?php if(isset($invoice_details[0]['accounting']) && ($invoice_details[0]['accounting'] === '1')){ echo 'selected'; } ?>>Yes</option>
                                            <option value="0" <?php if(isset($invoice_details[0]['accounting']) && ($invoice_details[0]['accounting'] === '0')){ echo 'selected'; } ?>>No</option>
                                        </select>
                                        <label for="accounting" class="error"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group <?php if( form_error('invoice_id') ) { echo 'has-error';} ?>">
                                    <label for="invoice_id_gen" class="col-md-4">Invoice ID *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="invoice_id" id="invoice_id_gen" value="<?php if (isset($invoice_details[0]['invoice_id'])) {
                                            echo $invoice_details[0]['invoice_id'];
                                            } ?>" placeholder="Invoice ID" <?php if (isset($invoice_details[0]['invoice_id'])) { echo "readonly";} ?> onblur="check_invoice_id(this.id)" required/>
                                        <label for="invoice_id_gen" class="error"></label>
                                    </div>
                                    <?php echo form_error('invoice_id'); ?>
                                </div>
                                <div class="form-group" >
                                    <label for="invoice_date" class="col-md-4">Invoice Date *</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="invoice_date" class="form-control" placeholder="Select Date" name="invoice_date" type="text" required readonly='true' style="<?php echo ((isset($invoice_details[0]['invoice_date'])) ? 'pointer-events: none;' : 'background-color:#fff;cursor: pointer;' ); ?>" value="<?php if (isset($invoice_details[0]['invoice_date'])) {
                                                echo $invoice_details[0]['invoice_date'];
                                                } else { echo date('Y-m-d');} ?>">
                                            <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <label for="invoice_date" class="error"></label>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="due_date" class="col-md-4">Due Date *</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="due_date" class="form-control" placeholder="Select Date" name="due_date" type="text" required readonly='true' style="background-color:#fff;cursor: pointer;" value="<?php if (isset($invoice_details[0]['due_date'])) {
                                                echo $invoice_details[0]['due_date'];
                                                } else { echo date('Y-m-d');} ?>">
                                            <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <label for="due_date" class="error"></label>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                                    <label for="type_code" class="col-md-4">Type *</label>
                                    <div class="col-md-6">
                                        <select name="type" class="form-control" id="type_code" onchange="tds_input_change(), tcs_input_change()" required>
                                            <option value="" disabled selected>Select Invoice Type</option>
                                            <option value="Buyback" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "Buyback"){ echo "selected";}
                                                } ?>>Buyback</option>
                                            <option value="<?php echo BUYBACK_VOUCHER;?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == BUYBACK_VOUCHER){ echo "selected";}
                                                } ?>><?php echo BUYBACK_VOUCHER; ?></option>
                                            <option  value="Cash" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "Cash"){ echo "selected";}
                                                } ?>>Cash</option>
                                            <option value="<?php echo CREDIT_NOTE; ?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if(($invoice_details[0]['type'] == CREDIT_NOTE)){ echo "selected";}
                                                } ?>><?php echo CREDIT_NOTE ;?></option>
                                            <option value="<?php echo DEBIT_NOTE;?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if(($invoice_details[0]['type'] == DEBIT_NOTE)){ echo "selected";}
                                                } ?>><?php echo DEBIT_NOTE;?></option>
                                            
                                            <option value="FOC" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "FOC"){ echo "selected";}
                                                } ?>>FOC</option>
                                            <option value="<?php echo PARTNER_VOUCHER;?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == PARTNER_VOUCHER){ echo "selected";}
                                                } ?>><?php echo PARTNER_VOUCHER; ?></option>
                                            
                                            <option value="Parts" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "Parts"){ echo "selected";}
                                                } ?>>Parts</option>
                                            <option value="Liquidation" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "Liquidation"){ echo "selected";}
                                                } ?>>Liquidation</option>
                                            <option value="Stand" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == "Stand"){ echo "selected";}
                                                } ?>>Stand</option>
                                            <option value="<?php echo VENDOR_VOUCHER;?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == VENDOR_VOUCHER){ echo "selected";}
                                                } ?>><?php echo VENDOR_VOUCHER;?></option>
                                            
                                        </select>
                                        <label for="type_code" class="error"></label>
                                    </div>
                                    <?php echo form_error('type'); ?>
                                </div>
                                <div class="form-group">
                                    <?php
                                        $src = "";
                                        $required = true;
                                        if (isset($invoice_details[0]['invoice_file_excel']) && !empty($invoice_details[0]['invoice_file_excel'])) {
                                            //Path to be changed
                                            $src = S3_WEBSITE_URL."invoices-excel/" . $invoice_details[0]['invoice_file_excel'];
                                            $required = false;
                                        }
                                    ?>
                                    <label for="invoice_file_excel" class="col-md-4">Main Invoice Excel *</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control"  name="invoice_file_excel" id="invoice_file_excel" <?php if($required) { echo "required";}?> >
                                        <label for="invoice_file_excel" class="error"></label>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if ($src != "") { ?>
                                            <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php
                                        $src = "";
                                        $required = true;
                                        if (isset($invoice_details[0]['invoice_file_main']) && !empty($invoice_details[0]['invoice_file_main'])) {
                                            //Path to be changed
                                            $src = S3_WEBSITE_URL."invoices-excel/" . $invoice_details[0]['invoice_file_main'];
                                            $required = false;
                                        }
                                    ?>
                                    <label for="invoice_file_main" class="col-md-4">Main Invoice *</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control"  name="invoice_file_main" id="invoice_file_main" <?php if($required) { echo "required";}?> >
                                        <label for="invoice_file_main" class="error"></label>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if ($src != "") { ?>
                                            <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="invoice_detailed_excel" class="col-md-4">Detailed Invoice Excel</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control"  name="invoice_detailed_excel" id="invoice_detailed_excel" >
                                    </div>
                                    <div class="col-md-2">
                                        <?php
                                            if (isset($invoice_details[0]['invoice_detailed_excel'])) {
                                                if (!is_null($invoice_details[0]['invoice_detailed_excel'])) {
                                            
                                                    if (isset($invoice_details[0]['invoice_detailed_excel']) && !empty($invoice_details[0]['invoice_detailed_excel'])) {
                                                        $src = S3_WEBSITE_URL."invoices-excel/" . $invoice_details[0]['invoice_detailed_excel']; ?>
                                        <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                        <?php }
                                            }
                                            }
                                            ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="remarks" class="col-md-4">Remarks</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" cols="50" rows="4" id="remarks" name="remarks" placeholder="Remarks"><?php if (isset($invoice_details[0]['remarks'])){echo $invoice_details[0]['remarks'];}?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top:40px;">
                            <table class="table priceList table-striped table-bordered">
                                <thead >
                                    <tr >
                                        <th class="text-center">SNo</th>
                                        <th class="text-center">Description</th>
                                        <th class="text-center">Product/Service</th>
                                        <th class="text-center">HSN Code</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Taxable</th>
                                        <th class="text-center" colspan="2">CGST </th>
                                        <th class="text-center" colspan="2">SGST </th>
                                        <th class="text-center" colspan="2">IGST </th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center" colspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center"></th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center"></th>
                                        <th class="text-center" colspan="2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($invoice_breakup)) {
                                    foreach ($invoice_breakup as $key => $value) { ?>
                                    <tr class="template">
                                        <td id="<?php echo "sno_".$key; ?>" class="sno"><?php echo ($key +1);?></td>
                                        <td style="width:24%">
                                            <textarea id="<?php echo "description_".$key; ?>" style="width:100%" name="invoice[<?php echo $value['id']; ?>][description]" class="form-control" required="" ><?php echo $value['description'];?></textarea>
                                            <label for="<?php echo "description_".$key; ?>" class="error"></label>
                                        </td>
                                        <td style="width:10%">
                                            <!--<input id="<?php echo "productorservices_".$key; ?>" readonly type="text" name="invoice[<?php echo $value['id']; ?>][product_or_services]"  value="<?php echo $value['product_or_services'];?>" class="form-control col-md-1" required="" >-->
                                            <select class="form-control col-md-1" name="invoice[<?php echo $value['id']; ?>][product_or_services]" id="<?php echo "productorservices_".$key; ?>" required="" >
                                                <option selected disabled>Select Product/Service</option>
                                                <?php foreach($invoice_category as $data){ ?>
                                                <option value="<?php echo $data['category']; ?>" <?php if($value['product_or_services'] === $data['category']){ echo 'selected'; } ?> ><?php echo $data['category']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <label for="<?php echo "productorservices_".$key; ?>" class="error"></label>
                                        </td>
                                        <td>
                                            <input id="<?php echo "hsncode_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][hsn_code]"  value="<?php echo $value['hsn_code'];?>" class="form-control col-md-1 allowNumericWithOutDecimal" required="" >
                                            <label for="<?php echo "hsncode_".$key; ?>" class="error"></label>
                                        </td>
                                        <td>
                                            <input onkeyup="change_prices('<?php echo $key; ?>')" id="<?php echo "qty_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][qty]" value="<?php echo $value['qty'];?>" class="form-control quantity allowNumericWithOutDecimal" required="" >
                                            <label for="<?php echo "qty_".$key; ?>" class="error"></label>
                                        </td>
                                        <td>
                                            <input onkeyup="validateDecimal(this.id, this.value);change_prices('<?php echo $key; ?>')"  id="<?php echo "rate_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][rate]" value="<?php echo $value['rate'];?>" class="form-control rate allowNumericWithDecimal padding_space" required="" >
                                            <label for="<?php echo "rate_".$key; ?>" class="error"></label>
                                        </td>
                                        <td><input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "taxablevalue_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][taxable_value]" value="<?php echo $value['taxable_value'];?>" class="form-control taxable_value allowNumericWithDecimal padding_space" ></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('<?php echo $key; ?>')"  id="<?php echo "cgsttaxrate_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][cgst_rate]" value="<?php echo $value['cgst_tax_rate'];?>" class="form-control cgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input  id="<?php echo "cgsttaxamount_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][cgst_tax_amount]"  value="<?php echo $value['cgst_tax_amount'];?>" class="form-control cgst_tax_amount allowNumericWithDecimalpadding_space" readonly></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('<?php echo $key; ?>')"  id="<?php echo "sgsttaxrate_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][sgst_rate]" value="<?php echo $value['sgst_tax_rate'];?>" class="form-control sgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="<?php echo "sgsttaxamount_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][sgst_tax_amount]" value="<?php echo $value['sgst_tax_amount'];?>" class="form-control sgst_tax_amount allowNumericWithDecimal padding_space" readonly></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('<?php echo $key; ?>')"  id="<?php echo "igsttaxrate_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][igst_rate]" value="<?php echo $value['igst_tax_rate'];?>" class="form-control igst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="<?php echo "igsttaxamount_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][igst_tax_amount]" value="<?php echo $value['igst_tax_amount'];?>" class="form-control igst_tax_amount allowNumericWithDecimal padding_space" readonly></td>
                                        <td><input id="<?php echo "totalamount_".$key; ?>" type="number" name="invoice[<?php echo $value['id']; ?>][total_amount]" value="<?php echo $value['total_amount'];?>" class="form-control total_amount allowNumericWithDecimal padding_space" readonly></td>
                                        <td colspan="2" style="width:8%;text-align:center;">
                                            <input type="hidden" id="<?php echo "settle_qty_".$key; ?>" name="invoice[<?php echo $value['id']; ?>][settle_qty]" value="<?php echo (isset($value['settle_qty']) ? $value['settle_qty'] : '');?>" >
                                            <input type="hidden" id="<?php echo "is_settle_".$key; ?>" name="invoice[<?php echo $value['id']; ?>][is_settle]" value="<?php echo (isset($value['is_settle']) ? $value['is_settle'] : '');?>" >
                                            <input type="hidden" id="<?php echo "from_gst_number_".$key; ?>" name="invoice[<?php echo $value['id']; ?>][from_gst_number_id]" value="<?php echo (isset($value['from_gst_number']) ? $value['from_gst_number'] : '');?>" >
                                            <input type="hidden" id="<?php echo "to_gst_number_".$key; ?>" name="invoice[<?php echo $value['id']; ?>][to_gst_number_id]" value="<?php echo (isset($value['to_gst_number']) ? $value['to_gst_number'] : '');?>" >
                                            <input type="hidden" id="<?php echo "create_date_".$key; ?>" name="invoice[<?php echo $value['id']; ?>][create_date]" value="<?php if(isset($value['create_date'])){ echo $value['create_date'];  } ?>" >
                                            <button type="button" id="<?php echo "addButton_".$key; ?>" class="btn btn-default addButton" style="display:inline;"><i class="fa fa-plus"></i></button>&nbsp;
                                            <button type="button" id="<?php echo "removeButton_".$key; ?>" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                    <?php } }
                                    else { ?>
                                    <tr class="template">
                                        <td id="sno_0" class="sno">1</td>
                                        <td style="width:24%">
                                            <textarea id="description_0" style="width:100%" name="invoice[0][description]" class="form-control" required="" ></textarea>
                                            <label for="description_0" class="error"></label>
                                        </td>
                                        <td style="width:10%">
                                            <!--<input id="productorservices_0" readonly type="text" name="invoice[0][product_or_services]"  value="" class="form-control col-md-1" >-->
                                            <select class="form-control col-md-1" name="invoice[0][product_or_services]" id="productorservices_0" required="" >
                                                <option disabled>Select Product/Service</option>
                                                <?php foreach($invoice_category as $data){ ?>
                                                <option value="<?php echo $data['category']; ?>" ><?php echo $data['category']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <label for="productorservices_0" class="error"></label>
                                        </td>
                                        <td>
                                            <input id="hsncode_0" type="text" name="invoice[0][hsn_code]"  value="" class="form-control col-md-1 allowNumericWithOutDecimal" required="" >
                                            <label for="hsncode_0" class="error"></label>
                                        </td>
                                        <td>
                                            <input onkeyup="change_prices('0')" id="qty_0" type="number" name="invoice[0][qty]" value="" class="form-control quantity allowNumericWithOutDecimal" required="" >
                                            <label for="qty_0" class="error"></label>
                                        </td>
                                        <td>
                                            <input onkeyup="validateDecimal(this.id, this.value);change_prices('0')"  id="rate_0" type="number" name="invoice[0][rate]" value="" class="form-control rate allowNumericWithDecimal padding_space" required="" >
                                            <label for="rate_0" id="lbl_rate_0" class="error"></label>
                                        </td>
                                        <td><input onkeyup="change_prices('0')"  id="taxablevalue_0" type="number" name="invoice[0][taxable_value]" value="0.00" class="form-control taxable_value allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('0')"  id="cgsttaxrate_0" type="number" name="invoice[0][cgst_rate]" value="" class="form-control cgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="cgsttaxamount_0" type="number" name="invoice[0][cgst_tax_amount]"  value="0.00" class="form-control cgst_tax_amount allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('0')"  id="sgsttaxrate_0" type="number" name="invoice[0][sgst_rate]" value="" class="form-control sgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="sgsttaxamount_0" type="number" name="invoice[0][sgst_tax_amount]" value="0.00" class="form-control sgst_tax_amount allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input onkeyup="validateDecimal(this.id, this.value);change_prices('0')"  id="igsttaxrate_0" type="number" name="invoice[0][igst_rate]" value="" class="form-control igst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="igsttaxamount_0" type="number" name="invoice[0][igst_tax_amount]" value="0.00" class="form-control igst_tax_amount allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="totalamount_0" type="number" name="invoice[0][total_amount]" value="0.00" class="form-control total_amount allowNumericWithDecimal padding_space" readonly></td>
                                        <td colspan="2" style="width:8%;text-align:center;">
                                            <input type="hidden" id="from_gst_number_0" name="invoice[0][from_gst_number_id]" >
                                            <input type="hidden" id="to_gst_number_0" name="invoice[0][to_gst_number_id]" >
<!--                                            <input type="hidden" id="create_date_0" name="invoice[0][create_date]" value="<?php //if(isset($value['create_date'])){ echo $value['create_date'];  } ?>" >
                                            <input type="hidden" id="update_date_0" name="invoice[0][update_date]" value="<?php //if(isset($value['update_date'])){ echo $value['update_date'];  } ?>" >-->
                                            <button type="button" id="addButton_0" class="btn btn-default addButton" style="display:inline;"><i class="fa fa-plus"></i></button>&nbsp;
                                            <button type="button" id="removeButton_0" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <!-- The template for adding new field -->
                                    <tr class="template hide" id="Template">
                                        <td id="sno"></td>
                                        <td style="width:24%">
                                            <textarea id="description" style="width:100%" class="form-control" required="" ></textarea>
                                            <label for="description" class="error"></label>
                                        </td>
                                        <td style="width:10%">
                                            <select class="form-control col-md-1" id="productorservices" required="" >
                                                <option disabled>Select Product/Service</option>
                                                <?php foreach($invoice_category as $data){ ?>
                                                <option value="<?php echo $data['category']; ?>" ><?php echo $data['category']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <label for="productorservices" class="error"></label>
                                        </td>
                                        <td>
                                            <input id="hsncode" type="text"  value="" class="form-control col-md-1 allowNumericWithOutDecimal" required="" >
                                            <label for="hsncode" class="error"></label>
                                        </td>
                                        <td>
                                            <input id="qty" type="number" value="" required="" >
                                            <label for="qty" class="error"></label>
                                        </td>
                                        <td>
                                            <input id="rate" type="number" value="" class="form-control rate allowNumericWithDecimal padding_space" required="" >
                                            <label for="rate" class="error"></label>
                                        </td>
                                        <td><input id="taxablevalue" type="number" value="0.00" class="form-control taxable_value allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="cgsttaxrate" type="number" value="" class="form-control cgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="cgsttaxamount" type="number"  value="0.00" class="form-control cgst_tax_amount allowNumericWithDecimalpadding_space" readonly ></td>
                                        <td><input id="sgsttaxrate" type="number" value="" class="form-control sgst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="sgsttaxamount" type="number" value="0.00" class="form-control sgst_tax_amount allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="igsttaxrate" type="number" value="" class="form-control igst_tax_rate allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="igsttaxamount" type="number" value="0.00" class="form-control igst_tax_amount allowNumericWithDecimal padding_space" readonly ></td>
                                        <td><input id="totalamount" type="number" value="0.00" class="form-control total_amount allowNumericWithDecimal padding_space" readonly></td>
                                        <td colspan="2" style="width:8%;text-align:center;">
                                            <input type="hidden" id="from_gst_number" >
                                            <input type="hidden" id="to_gst_number" >
                                            <button type="button" id="addButton" style="display:inline;"><i class="fa fa-plus"></i></button>&nbsp;
                                            <button type="button" id="removeButton"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input id="total_quantity" type="number" value="0" name="total_quantity" class="form-control" readonly >
                                            <input type="hidden" id="parts_count" name="parts_count" value="0">
                                            <input type="hidden" id="num_bookings" name="num_bookings" value="0">
                                            <input type="hidden" id="packaging_quantity" name="packaging_quantity" value="0">
                                            <input type="hidden" id="penalty_bookings_count" name="penalty_bookings_count" value="0">
                                            <input type="hidden" id="credit_penalty_bookings_count" name="credit_penalty_bookings_count" value="0">
                                        </td>
                                        <td ></td>
                                        <td>
                                            <input id="total_taxablevalue" type="number" value="0.00" name="total_taxablevalue" class="form-control padding_space" readonly >
                                            <input type="hidden" id="parts_cost" name="parts_cost" value="0.00">
                                            <input type="hidden" id="total_service_charge" name="total_service_charge" value="0.00">
                                            <input type="hidden" id="warehouse_storage_charges" name="warehouse_storage_charges" value="0.00">
                                            <input type="hidden" id="miscellaneous_charges" name="miscellaneous_charges" value="0.00">
                                            <input type="hidden" id="packaging_rate" name="packaging_rate" value="0.00">
                                            <input type="hidden" id="courier_charges" name="courier_charges" value="0.00">
                                            <input type="hidden" id="credit_penalty_amount" name="credit_penalty_amount" value="0.00">
                                            <input type="hidden" id="penalty_amount" name="penalty_amount" value="0.00">
                                            <input type="hidden" id="upcountry_price" name="upcountry_price" value="0.00">
                                            <input type="hidden" id="upcountry_rate" name="upcountry_rate" value="0.00">
                                            <input type="hidden" id="micro_warehouse_charges" name="micro_warehouse_charges" value="0.00">
                                            <input type="hidden" id="call_center_charges" name="call_center_charges" value="0.00">
                                        </td>
                                        <td></td>
                                        <td><input id="total_cgst_amount" type="number" value="0.00" name="total_cgst_amount" class="form-control padding_space" readonly ></td>
                                        <td></td>
                                        <td><input id="total_sgst_amount" type="number" value="0.00" name="total_sgst_amount" class="form-control padding_space" readonly ></td>
                                        <td></td>
                                        <td><input id="total_igst_amount" type="number" value="0.00" name="total_igst_amount" class="form-control padding_space" readonly ></td>
                                        <td><input id="sub_amount_charge" type="number" value="0.00" name="sub_total_amount_charge" class="form-control padding_space" readonly ></td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td>(+) TCS Rate % </td>
                                        <td><input id="tcs_rate" type="number" onblur="calculate_total()" value="<?php if(isset($invoice_details[0]['tcs_rate'])){ echo $invoice_details[0]['tcs_rate']; }?>" name="tcs_rate" class="form-control padding_space" ></td>
                                        <td><input id="tcs_amount" type="number" value="<?php if(isset($invoice_details[0]['tcs_amount'])){ echo $invoice_details[0]['tcs_amount']; } ?>" name="tcs_amount" class="form-control padding_space" ></td>
                                        
                                        
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td>(-) TDS Rate %</td>
<!--                                        <td><Select id="tds_rate" onchange="calculate_total()" name="tds_rate" class="form-control padding_space" >
                                                <option value="0" <?php if( isset($invoice_details[0]['tds_rate']) && $invoice_details[0]['tds_rate'] == 0){ echo 'Selected';} ?> >0</option>
                                                <option value=".75" <?php if( isset($invoice_details[0]['tds_rate']) && $invoice_details[0]['tds_rate'] == 0){ echo 'Selected';} ?>>0.75</option>
                                                <option value="1.5" <?php if(isset($invoice_details[0]['tds_rate']) && $invoice_details[0]['tds_rate'] == 0){ echo 'Selected';} ?>>1.5</option>
                                                <?php if( isset($invoice_details[0]['tds_rate']) && ($invoice_details[0]['tds_rate'] > 0) && ($invoice_details[0]['tds_rate'] != 0.75 
                                                        || $invoice_details[0]['tds_rate'] !=1.5)){ ?>
                                                    <option value="<?php echo $invoice_details[0]['tds_rate'];?>" Selected><?php echo $invoice_details[0]['tds_rate'];?></option>
                                               <?php } ?>
                                            
                                            </Select>
                                        </td>-->
                                        <td><input id="tds_rate" type="number" onblur="calculate_total()" value="" name="tds_rate" class="form-control padding_space" ></td>
                                        <td><input id="tds_amount" type="number" value="0.00" name="tds_amount" class="form-control padding_space" ></td>
                                        
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td>Final Amount</td>
                                        <td></td>
                                        <td><input id="total_amount_charge" type="number" value="0.00" name="total_amount_charge" class="form-control padding_space" readonly >
                                            </td>
                                        
                                        
                                        <td colspan="2"></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <div class="text-center">
                                <!--<input type="submit" value="Submit" class="btn btn-primary btn-lg text-center">-->
                                <input type="submit" id="submitBtn" name="submitBtn" value="<?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>" <?php if(!$can_update_invoice){echo "style='display:none;'";} ?> class="btn btn-md btn-primary" />
                                <input type="hidden" id="confirmation" value="0">
                                <input type="hidden" id="is_igst" name="is_igst" value="<?php echo ((!empty($invoice_breakup[0]['igst_tax_amount']) && ($invoice_breakup[0]['igst_tax_amount'] != 0)) ? "1" : ((!empty($invoice_breakup[0]['sgst_tax_amount']) && ($invoice_breakup[0]['sgst_tax_amount'] != 0)) ? "0" : "2"));?>" >
                                <label for="invoice_update_time_over" class="cannot_update_invoice"><?php if(!$can_update_invoice){echo INVOICE_CANNOT_BE_UPDATED_AFTER_DEFINED_TIME;} ?></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="<?php echo base_url() ?>js/invoice_tag.js?v=<?=mt_rand()?>"></script>
<script>
    $("#to_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#due_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#vendor_partner_id").select2();
    $('#gst_number').select2({
        placeholder:'Select 247around GST Number',
        width : '100%'
    });
    $('#accounting').select2({
        placeholder:'Select Accounting Yes/No'
    });
    $('#vertical').select2({
        placeholder:'Select Vertical'
    });
    $('#category,#sub_category').select2();
    $('#type_code').select2({
        placeholder:'Select Invoice Type'
    });
    
    $('#productorservices_0').select2();
    var total_line_items = <?php if(!empty($invoice_breakup)){echo count($invoice_breakup);}else{echo 1;} ?>;
    for(var i = 1; i < total_line_items; i++){
        $('#productorservices_'+i).select2();
    }
    $("input:text, input:file, input:radio, select").on('change',function(){
        $("#submitBtn").attr('disabled',false);
        $('label.error').css('display','none');
    });
    
    $("input:text, textarea").on('input',function(){
        $("#submitBtn").attr('disabled',false);
        $('label.error').css('display','none');
    });
    
    $("#submitBtn").click(function(){
        $("#submitBtn").attr('disabled',true);
        $("#confirmation").val('1');
        
        var gst_number = $("#gst_number").val();
        var type_code = $("input[name='around_type']:checked").val();
        
        if((gst_number !== null) && (type_code !== undefined)) { 
            $(".quantity").each(function() {
                id = $(this).attr('id');
                index = id.split("_")[1];
                if(type_code === 'A') {
                    $('#from_gst_number_'+index).val(gst_number);
                    $('#to_gst_number_'+index).val('');
                }
                else {
                    $('#from_gst_number_'+index).val('');
                    $('#to_gst_number_'+index).val(gst_number);
                }
            });
        }
        $("#insertUpdateForm").submit();
    });
    
    var partIndex = 0;
    partIndex = '<?php echo isset($invoice_breakup) && count($invoice_breakup) > 0 ? count($invoice_breakup) : 0; ?>';
    $(function() {
        $('input[name="from_date"]').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            <?php if (isset($invoice_details[0]['from_date'])) { ?>
                startDate: '<?php echo $invoice_details[0]['from_date'];  ?>',
                endDate: '<?php echo $invoice_details[0]['to_date']; ?>'
            <?php } ?>
        });
        
        get_247around_wh_gst_number('247001');
        partner_vendor('<?php echo $vendor_partner; ?>');
        
        get_vertical('<?php echo base_url(); ?>');
        
        var vertical_input = $('#vertical_input').val();
        if(vertical_input != '') {
            $('#vertical option[value="'+vertical_input+'"]').prop('selected',true);
            $('#select2-vertical-container').text($("#vertical").find(':selected').text());
        }
        
        var category_input = $('#category_input').val();
        if(category_input != '') {
            $('#category option[value="'+category_input+'"]').prop('selected',true);
            $('#select2-category-container').text($("#category").find(':selected').text());
        }
        
        var sub_category_input = $('#sub_category_input').val();
        if(sub_category_input != '') {
            $('#sub_category option[value="'+sub_category_input+'"]').prop('selected',true);
            $('#select2-sub_category-container').text($("#sub_category").find(':selected').text());
        }
        
        control_type_code(0);
        calculate_total();
        $("#accounting").removeAttr('disabled');
        
        var from_gst_number = '<?php echo (isset($invoice_breakup[0]['from_gst_number']) ? $invoice_breakup[0]['from_gst_number'] : ''); ?>';
        var to_gst_number = '<?php echo (isset($invoice_breakup[0]['to_gst_number']) ? $invoice_breakup[0]['to_gst_number'] : ''); ?>';
        
        var type_code = '<?php echo (isset($invoice_details[0]['type_code']) ? $invoice_details[0]['type_code'] : ''); ?>';
        
        if(type_code === 'A') { 
            $('#gst_number option[value="'+from_gst_number+'"]').prop('selected',true);
            $('#select2-gst_number-container').text($("#gst_number").find(':selected').text());
        } else {
            $('#gst_number option[value="'+to_gst_number+'"]').prop('selected',true);
            $('#select2-gst_number-container').text($("#gst_number").find(':selected').text());
        }
        
        check_gst_tax_type();
        
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
        
        // Add button click handler
        $('#insertUpdateForm').on('click', '.addButton', function () {
            partIndex++;
            var $template = $('#Template'),
                $clone = $template
                        .clone()
                        .removeClass('hide')
                        .removeAttr('id')
                        .attr('data-book-index', partIndex)
                        .insertBefore($template);
    
            // Update the name attributes
            $clone
                .find('[id="sno"]').attr('id','sno_'+partIndex).attr('class', 'sno').end()
                .find('[id="description"]').attr('name', 'invoice[' + partIndex + '][description]').attr('id','description_'+partIndex).end()
                .find('[for="description"]').attr('for','description_'+partIndex).end()
                .find('[id="productorservices"]').attr('name', 'invoice[' + partIndex + '][product_or_services]').attr('id','productorservices_'+partIndex).end()
                .find('[for="productorservices"]').attr('for','productorservices_'+partIndex).end()
                .find('[id="hsncode"]').attr('name', 'invoice[' + partIndex + '][hsn_code]').attr('id','hsncode_'+partIndex).end()
                .find('[for="hsncode"]').attr('for','hsncode_'+partIndex).end()
                .find('[id="qty"]').attr('name', 'invoice[' + partIndex + '][qty]').attr('id','qty_'+partIndex).attr('class', 'form-control quantity allowNumericWithOutDecimal').attr('onkeyup', 'change_prices(' + partIndex + ')').end()
                .find('[for="qty"]').attr('for','qty_'+partIndex).end()
                .find('[id="rate"]').attr('name', 'invoice[' + partIndex + '][rate]').attr('id','rate_'+partIndex).attr('onkeyup', 'validateDecimal(this.id, this.value);change_prices(' + partIndex + ')').end()
                .find('[for="rate"]').attr('for','rate_'+partIndex).attr('id','lbl_rate_'+partIndex).end()
                .find('[id="taxablevalue"]').attr('name', 'invoice[' + partIndex + '][taxable_value]').attr('id','taxablevalue_'+partIndex).attr('onkeyup', 'change_prices(' + partIndex + ')').end()
                .find('[id="cgsttaxrate"]').attr('name', 'invoice[' + partIndex + '][cgst_rate]').attr('id','cgsttaxrate_'+partIndex).attr('onkeyup', 'validateDecimal(this.id, this.value);change_prices(' + partIndex + ')').end()
                .find('[id="cgsttaxamount"]').attr('name', 'invoice[' + partIndex + '][cgst_tax_amount]').attr('id','cgsttaxamount_'+partIndex).end()
                .find('[id="sgsttaxrate"]').attr('name', 'invoice[' + partIndex + '][sgst_rate]').attr('id','sgsttaxrate_'+partIndex).attr('onkeyup', 'validateDecimal(this.id, this.value);change_prices(' + partIndex + ')').end()
                .find('[id="sgsttaxamount"]').attr('name', 'invoice[' + partIndex + '][sgst_tax_amount]').attr('id','sgsttaxamount_'+partIndex).end()
                .find('[id="igsttaxrate"]').attr('name', 'invoice[' + partIndex + '][igst_rate]').attr('id','igsttaxrate_'+partIndex).attr('onkeyup', 'validateDecimal(this.id, this.value);change_prices(' + partIndex + ')').end()
                .find('[id="igsttaxamount"]').attr('name', 'invoice[' + partIndex + '][igst_tax_amount]').attr('id','igsttaxamount_'+partIndex).end()
                .find('[id="totalamount"]').attr('name', 'invoice[' + partIndex + '][total_amount]').attr('id','totalamount_'+partIndex).end()
                .find('[id="from_gst_number"]').attr('name', 'invoice[' + partIndex + '][from_gst_number_id]').attr('id','from_gst_number_'+partIndex).end()
                .find('[id="to_gst_number"]').attr('name', 'invoice[' + partIndex + '][to_gst_number_id]').attr('id','to_gst_number_'+partIndex).end()
                .find('[id="addButton"]').attr('id','addButton_'+partIndex).attr('class', 'btn btn-default addButton').end()
                .find('[id="removeButton"]').attr('id','removeButton_'+partIndex).attr('class', 'btn btn-default removeButton').end();
                rearrange_sno();
                check_gst_tax_type();
                $('#productorservices_'+partIndex).select2();
        })
    
        // Remove button click handler
        .on('click', '.removeButton', function () {
            if($('.removeButton').length > 1) {
                var $row = $(this).parents('.template'),
                index = $row.attr('data-part-index');
                $row.remove();
                rearrange_sno();
                calculate_total();
            }
        });
        
        $("#insertUpdateForm").on('submit', function(e) {
            e.preventDefault();
            $("#submitBtn").attr('disabled',true);
            var isvalid = $("#insertUpdateForm").valid();
            var flag = true;
            var id = index = '';
            if (isvalid) {
                $(".quantity").each(function() {
                    id = $(this).attr('id');
                    index = id.split("_")[1];
                    if($.trim($('#rate_'+index).val()) !== '') {
                        validateDecimal('rate_'+index,$('#rate_'+index).val());
    
                        if(Number($('#rate_'+index).val()) == 0){
                            showConfirmDialougeBox('Please enter rate', 'warning');
                            $('#rate_'+index).addClass('text-danger');
                            flag = false;
                            return false;
                        }
                    }
                    
                    if($("#is_igst").val() === '1') {
                        $("#cgsttaxrate_"+index).val("0");
                        $("#sgsttaxrate_"+index).val("0");
                        
                        if($.trim($('#igsttaxrate_'+index).val()) !== '') {
                            validateDecimal('igsttaxrate_'+index,$('#igsttaxrate_'+index).val());

                            if(Number($('#igsttaxrate_'+index).val()) == 0){
                                showConfirmDialougeBox('Please enter IGST Rate', 'warning');
                                $('#igsttaxrate_'+index).addClass('text-danger');
                                flag = false;
                                return false;
                            }
                        }
                    }
                    else if($("#is_igst").val() === '0') {
                        $("#igsttaxrate_"+index).val("0");
                        
                        if($.trim($('#cgsttaxrate_'+index).val()) !== '') {
                            validateDecimal('cgsttaxrate_'+index,$('#cgsttaxrate_'+index).val());

                            if(Number($('#cgsttaxrate_'+index).val()) == 0){
                                showConfirmDialougeBox('Please enter CGST Rate', 'warning');
                                $('#cgsttaxrate_'+index).addClass('text-danger');
                                flag = false;
                                return false;
                            }
                        }
                        
                        if($.trim($('#sgsttaxrate_'+index).val()) !== '') {
                            validateDecimal('sgsttaxrate_'+index,$('#sgsttaxrate_'+index).val());

                            if(Number($('#sgsttaxrate_'+index).val()) == 0){
                                showConfirmDialougeBox('Please enter SGST Rate', 'warning');
                                $('#sgsttaxrate_'+index).addClass('text-danger');
                                flag = false;
                                return false;
                            }
                        }
                    }
                });
                var c_status = $("#confirmation").val();
                       
                if((c_status !='')&& (c_status == '1')){
                    if(confirm('Are you sure to continue')){
                        //Serializing all For Input Values (not files!) in an Array Collection so that we can iterate this collection later.
                        var params = $('#insertUpdateForm').serializeArray();
                        
                        var vendor_partner_id = $("#vendor_partner_id").val();

                        //Getting Invoice PDF Files
                        var invoice_file_main = $("#invoice_file_main")[0].files;

                        //Getting Invoice Excel Files
                        var invoice_file_excel = $("#invoice_file_excel")[0].files;

                        //Getting Invoice Detailed Excel Files
                        var invoice_detailed_excel = $("#invoice_detailed_excel")[0].files;

                        //Declaring new Form Data Instance  
                        var formData = new FormData();

                        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
                        for (var i = 0; i < invoice_file_main.length; i++) {
                            formData.append('invoice_file_main', invoice_file_main[i]);
                        }

                        for (var i = 0; i < invoice_file_excel.length; i++) {
                            formData.append('invoice_file_excel', invoice_file_excel[i]);
                        }
                        
                        for (var i = 0; i < invoice_detailed_excel.length; i++) {
                            formData.append('invoice_detailed_excel', invoice_detailed_excel[i]);
                        }
                        
                        //Now Looping the parameters for all form input fields and assigning them as Name Value pairs. 
                        $(params).each(function (index, element) {
                            formData.append(element.name, element.value);
                        });

                        $.ajax({
                            method:"POST",
                            url:"<?php echo base_url();?>employee/invoice/process_insert_update_invoice/<?php echo $vendor_partner;?>/<?php echo (isset($invoice_details[0]['invoice_id'])?0:1);?>",
                            data:formData,
                            contentType: false,
                            processData: false,
                            beforeSend: function(){
                                // Handle the beforeSend event
                                $('#submitBtn').attr('disabled',true);
                                $('#submitBtn').html("<i class='fa fa-spinner fa-spin'></i> Processing...");
                                $("#insertUpdateForm")[0].reset();
                                $("#insertUpdateForm").find('input:text, input:file, select').val('');
                                $(".select2-selection__rendered").html('');
                                $('label.error').css('color','white');
                            },
                            success:function(response){
                                obj = JSON.parse(response);
                                if(obj.status){
                                    alert(obj.message);
                                    window.location="<?php echo base_url();?>employee/invoice/invoice_summary/<?php echo $vendor_partner;?>/"+vendor_partner_id;
                                }
                                else{
                                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                                    $('#error_msg').html(obj.message);
                                    $("#insertUpdateForm")[0].reset();
                                    $("#insertUpdateForm").find('input:text, input:file, select').val('');
                                    $(".select2-selection__rendered").html('');
                                }
                            },
                            complete: function() {
                                $('#submitBtn').attr('disabled',false);
                                $('#submitBtn').html("<?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>");
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
    
    });
    
    function rearrange_sno() {
        var count=0;
        var id;
        $('.sno').each(function() {
            id = $(this).attr('id');
            $("#"+id).text(++count);
        });
        partIndex = id.split("_")[1];
    }
    function from_calendar() {
        $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
    }
    function partner_vendor(vendor_partner){
        var vendor_partner_id = $("#vendor_partner_id").val();
        if(vendor_partner_id === null){
            <?php if (isset($invoice_details[0]['vendor_partner_id'])) { ?>
              vendor_partner_id = '<?php echo $invoice_details[0]['vendor_partner_id']; ?>';      
             <?php } ?>
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + vendor_partner,
            data: {vendor_partner_id: vendor_partner_id,invoice_flag: 0},
            async: false,
            success: function (data) {
                $("#vendor_partner_id").select2().html(data).change();
                if(vendor_partner_id !== null) {
                    $('#vendor_partner_id option[value="'+vendor_partner_id+'"]').prop('selected',true);
                    $('#select2-vendor_partner_id-container').text($("#vendor_partner_id").find(':selected').text());
                }
            }
        });
    }

    function fetch_invoice_id(){
        var gst_number = $("#gst_number").val();
        var vendor_partner_type = '<?php echo $vendor_partner; ?>';
        var vendor_partner_id =  $("#vendor_partner_id").val();
        var type_code = $("input[name='around_type']:checked").val();
        var type = $("#type_code option:selected").val();

        if(gst_number === null){ 
            alert("Please Select 247around GST Number");
            return false;

        } else if(vendor_partner_id === null){ 
            alert("Please Select Entity");
            return false;

        } else if(type_code === undefined){ 
            alert("Please Select Buyer/Seller");
            return false;
        } else if((type == '') || (type ==null)){
            alert("Please Select Invoice Type");
            $("#type_code").focus();
            return false;
        } else {
            $("#fetch_invoice_id").text("Fetching..");
            $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/fetch_invoice_id/' + vendor_partner_id + '/' + vendor_partner_type + '/' + type_code+"/"+type,
                success: function (data) {
                    alert(data);
                    $("#fetch_invoice_id").text("Fetch Invoice ID");
                    $(".panel-title").html(data);
                    $("#invoice_id_gen").val(data).removeAttr('readonly').trigger('change');
                }
            });

        } 
    } 
    function control_type_code(is_value){
        var radioValue = $("input[name='around_type']:checked").val();
        if(is_value === 1){
            $('#type_code option:eq(0)').prop('selected', true);
            $('#select2-type_code-container').text($("#type_code").find(':selected').text());
            $('#invoice_id_gen').val('').removeAttr('readonly');
        }
      
        if(radioValue === 'A'){
           
            document.getElementById("type_code").options[1].disabled = false;
            document.getElementById("type_code").options[2].disabled = false;
            document.getElementById("type_code").options[3].disabled = true;
            document.getElementById("type_code").options[4].disabled = true;
            document.getElementById("type_code").options[6].disabled = true;
               
        } else if(radioValue === 'B'){
           
            document.getElementById("type_code").options[1].disabled = true;
            document.getElementById("type_code").options[2].disabled = true;
            document.getElementById("type_code").options[3].disabled = false;
            document.getElementById("type_code").options[4].disabled = false;
            document.getElementById("type_code").options[6].disabled = false;
        }
        
        $('#type_code').select2({
            placeholder:'Select Invoice Type'
        });
    }
    
    function change_prices(id){
       var qty = Number($("#qty_"+ id).val());
       var rate  = Number($("#rate_"+id).val());
       
       var is_igst  = $("#is_igst").val();
       if(qty === 0 || qty === ""){
           qty = 1;
       }
       if(rate > 0){
           var actual_taxablevalue = qty * rate;
      
          $("#taxablevalue_"+id).val(actual_taxablevalue);
       }
       
       var taxablevalue  = Number($("#taxablevalue_"+id).val());
       
       var igsttaxrate = gst_amount = igsttaxamount = 0;
       var sgsttaxrate = s_gst_amount = cgsttaxrate = c_gst_amount = 0;
       var total_amount = 0;
       
       if(is_igst === '1'){
           igsttaxrate = Number($("#igsttaxrate_"+id).val());
           gst_amount = (taxablevalue * igsttaxrate)/100;
           $("#igsttaxamount_"+id).val(gst_amount);
           igsttaxamount = Number($("#igsttaxamount_"+id).val());
           total_amount = (taxablevalue + igsttaxamount);
           $("#totalamount_"+id).val(total_amount);
           
       } else if(is_igst === '0'){
            sgsttaxrate = Number($("#sgsttaxrate_"+id).val());
            s_gst_amount = (taxablevalue * sgsttaxrate)/100;
            $("#sgsttaxamount_"+id).val(s_gst_amount);
            
            cgsttaxrate = Number($("#cgsttaxrate_"+id).val());
            c_gst_amount = (taxablevalue * cgsttaxrate)/100;
            $("#cgsttaxamount_"+id).val(c_gst_amount);
            
            total_amount = (taxablevalue + s_gst_amount + c_gst_amount);
            $("#totalamount_"+id).val(total_amount);
       } else {
           $("#totalamount_"+id).val(taxablevalue);
       }
       
       calculate_total();
    } 
    
    function calculate_total(){
         var total_quantity = 0;
         var taxable_value = 0;
         var igst_amount = 0;
         var cgst_amount = 0;
         var sgst_amount = 0;
         var total_charge = 0;
         var parts_count = parts_cost = 0;
         var num_bookings = total_service_charge = 0;
         var packaging_quantity = penalty_bookings_count = credit_penalty_bookings_count = 0;
         var warehouse_storage_charges = miscellaneous_charges = packaging_rate = courier_charges = credit_penalty_amount = penalty_amount = upcountry_price = upcountry_rate = micro_warehouse_charges = call_center_charges = 0; 
         var id = index = '';
         var is_igst  = $("#is_igst").val();
         var tds_rate = 0;
         var tcs_rate =0;
         var tds_amount =0;
         var tcs_amount = 0;
        //loop for each line item added
        $(".quantity").each(function () {
            id = $(this).attr('id');
            index = id.split("_")[1];
            
            total_quantity += Number($("#qty_"+ index).val());
            var deduct_amount = 0;
            //Invoice category type that is selected
            var category_type = $("#productorservices_"+ index).val();
            if(category_type === 'Product') {
                parts_count += Number($("#qty_"+ index).val());
                parts_cost += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Service' || category_type === 'Annual Charges'){
                num_bookings += Number($("#qty_"+ index).val());
                total_service_charge += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Warehouse Charges'){
                warehouse_storage_charges += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Misc Charge'){
                miscellaneous_charges += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Packaging Charges'){
                packaging_quantity += Number($("#qty_"+ index).val());
                packaging_rate += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Courier'){
                courier_charges += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Credit Penalty'){
                credit_penalty_bookings_count += Number($("#qty_"+ index).val()); 
                credit_penalty_amount += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Debit Penalty' || category_type === 'Penalty Discount'){
                deduct_amount = 1;
                penalty_bookings_count += Number($("#qty_"+ index).val()); 
                penalty_amount += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Upcountry'){
                upcountry_rate += Number($("#rate_"+ index).val());
                upcountry_price += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Call Center Charges'){
                call_center_charges += Number($("#taxablevalue_"+ index).val());
            }else if(category_type === 'Micro Warehouse'){
                micro_warehouse_charges += Number($("#taxablevalue_"+ index).val());
            }
            if(deduct_amount == 0){
                //add amount in total taxable amount
                taxable_value += Number($("#taxablevalue_"+ index).val());
            }else{
                //deduct amount from total taxable value
                taxable_value -= Number($("#taxablevalue_"+ index).val());
            }
            if(is_igst === '1'){
                if(deduct_amount == 0){
                    //add amount in total igst amount
                    igst_amount += Number($("#igsttaxamount_"+index).val());
                }else{
                    //deduct amount from total igst value
                    igst_amount -= Number($("#igsttaxamount_"+index).val());
                }  
            } else if(is_igst === '0'){
                if(deduct_amount == 0){
                    //add amount in total cgst and total sgst amount
                    sgst_amount += Number($("#sgsttaxamount_"+index).val());
                    cgst_amount += Number($("#cgsttaxamount_"+index).val());
                }else{
                    //deduct amount from total cgst and total sgst value
                    sgst_amount -= Number($("#sgsttaxamount_"+index).val());
                    cgst_amount -= Number($("#cgsttaxamount_"+index).val());
                }
            } 
            
            if(deduct_amount == 0){
                //add amount in total amount
                total_charge += Number($("#totalamount_"+index).val());
            }else{
                //deduct amount from total value
                total_charge -= Number($("#totalamount_"+index).val());
            }
    
        });
        
        $("#total_quantity").val(total_quantity);
        $("#total_taxablevalue").val(taxable_value.toFixed(2));
        $("#parts_count").val(parts_count);
        $("#num_bookings").val(num_bookings);
        $("#parts_cost").val(parts_cost.toFixed(2));
        $("#total_service_charge").val(total_service_charge.toFixed(2));
        $("#packaging_quantity").val(packaging_quantity);
        $("#penalty_bookings_count").val(penalty_bookings_count);
        $("#warehouse_storage_charges").val(warehouse_storage_charges.toFixed(2));
        $("#miscellaneous_charges").val(miscellaneous_charges.toFixed(2));
        $("#packaging_rate").val(packaging_rate.toFixed(2));
        $("#courier_charges").val(courier_charges.toFixed(2));
        $("#credit_penalty_amount").val(credit_penalty_amount.toFixed(2));
        $("#penalty_amount").val(penalty_amount.toFixed(2));
        $("#upcountry_price").val(upcountry_price.toFixed(2));
        $("#upcountry_rate").val(upcountry_rate.toFixed(2));
        $("#micro_warehouse_charges").val(micro_warehouse_charges.toFixed(2));
        $("#call_center_charges").val(call_center_charges.toFixed(2));
        
       
        if(igst_amount > 0){
            $("#total_igst_amount").val(igst_amount.toFixed(2));
        } else if(cgst_amount > 0){
            $("#total_cgst_amount").val(cgst_amount.toFixed(2));
            $("#total_sgst_amount").val(sgst_amount.toFixed(2));
        }
        
         $("#sub_amount_charge").val(total_charge.toFixed(2));
        
        var tds_rate = $("#tds_rate").val();
        var t_service_charge = total_service_charge + warehouse_storage_charges + miscellaneous_charges 
                + micro_warehouse_charges + (packaging_quantity * packaging_quantity) 
                + courier_charges + credit_penalty_amount - penalty_amount + upcountry_price 
                + call_center_charges;
        
        var tcs_rate = $("#tcs_rate").val();
        var tcs_amount = (tcs_rate * total_charge) /100;
        $("#tcs_amount").val(tcs_amount.toFixed(2));

        var tds_amount = (tds_rate * t_service_charge) /100;
        
        $("#tds_amount").val(tds_amount);
        
        var total_f_c = total_charge - tds_amount + tcs_amount;
        
        $("#total_amount_charge").val(total_f_c.toFixed(2));
    }
    
    function get_247around_wh_gst_number(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/inventory/get_247around_wh_gst_number',
            async: false,
            data:{partner_id:partner_id},
            success: function (response) {
                $("#gst_number").html(response);
            }
        });
    }
    function validateDecimal(id,value) {
        var RE = /^\d+(?:\.\d{1,2})?$/
        if(($.trim(value) !== '') && !RE.test(value)){
           $('#lbl_'+id).text("Enter value upto 2 decimal places");
           $('#lbl_'+id).css("display","inline-block");
           $('#'+id).focus();
           $('#submitBtn').attr('disabled',true);
        }
        else {
            $('#lbl_'+id).text("");
            $('#submitBtn').attr('disabled',false);
        }
    }
    function check_gst_tax_type() {
        var gst_number = $("#gst_number").val();
        var vendor_partner_type = '<?php echo $vendor_partner; ?>';
        var vendor_partner_id =  $("#vendor_partner_id").val();
        var id = index = '';
        
        if((gst_number != null) && (vendor_partner_id != null)) {
            
            if(vendor_partner_id === '<?php echo VIDEOCON_ID;?>'){
                tcs_input_change();
            }
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/invoice/check_gst_tax_type',
                async: false,
                data:{gst_number:gst_number,vendor_partner_type:vendor_partner_type,vendor_partner_id:vendor_partner_id},
                success: function (response) {
                    $(".quantity").each(function () {
                        id = $(this).attr('id');
                        index = id.split("_")[1];
                        $("#is_igst").val("2");
                        $("#igsttaxrate_"+index).attr('readonly',true).removeAttr('required');
                        $("#igsttaxamount_"+index).removeAttr('required');
                        $("#cgsttaxrate_"+index).attr('readonly',true).removeAttr('required');
                        $("#cgsttaxamount_"+index).removeAttr('required');
                        $("#sgsttaxrate_"+index).attr('readonly',true).removeAttr('required');
                        $("#sgsttaxamount_"+index).removeAttr('required');
                        
                        if(response == true) {
                            $("#is_igst").val("0");
                            $("#cgsttaxrate_"+index).attr('readonly',false).attr('required','');
                            $("#cgsttaxamount_"+index).attr('required','');
                            $("#sgsttaxrate_"+index).attr('readonly',false).attr('required','');
                            $("#sgsttaxamount_"+index).attr('required','');
                        }
                        else {
                            $("#is_igst").val("1");
                            $("#igsttaxrate_"+index).attr('readonly',false).attr('required','');
                            $("#igsttaxamount_"+index).attr('required','');
                        }
                    });
                }
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
                 $("#submitBtn").attr('disabled',true);
                 $("#insertUpdateForm").submit();
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
    
    function check_invoice_id(id){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){
            
            if( invoice_id.indexOf('/') !== -1 ){
                $('#'+id).css('border','1px solid red');
                $('#submitBtn').attr('disabled',true);
                
                alert("Use '-' in place of '/'");
            }
            else{
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>check_invoice_id_exists/'+invoice_id,
                    data:{is_ajax:true},
                    success:function(res){
                        var obj = JSON.parse(res);
                        if(obj.status === true){
                            $('#'+id).css('border','1px solid red');
                            $('#submitBtn').attr('disabled',true);
                            alert('Invoice number already exists');
                        }else{
                            $('#'+id).css('border','1px solid #ccc');
                            $('#submitBtn').attr('disabled',false);
                            
                        }
                    }
                });
            }
        }
    }
    tds_input_change();
   // tcs_input_change();
    function tds_input_change(){
       var type = $("#type_code").val();
       var vendor_partner_type = '<?php echo $vendor_partner; ?>';
       if(vendor_partner_type === "vendor"){
           console.log(type);
           if(type != "FOC"){
                $("#tds_rate").val(0);
                $('#tds_rate').prop('readonly', true);
                $('#tds_amount').prop('readonly', true);
                calculate_total();
           }
       } else if(vendor_partner_type === "partner" && type !="Cash"){
            $("#tds_rate").val(0);
            $('#tds_rate').prop('readonly', true);
            $('#tds_amount').prop('readonly', true);
            calculate_total();
       }
        
    }
    
    function tcs_input_change(){
        var type = $("#type_code").val();
        var vendor_partner_type = '<?php echo $vendor_partner; ?>';
        var vendor_partner_id =  $("#vendor_partner_id").val();
       
        if(vendor_partner_id != null){
            if(vendor_partner_type == "partner" && vendor_partner_id == '<?php echo VIDEOCON_ID;?>' && type =="Parts"){
            
                $('#tcs_rate').prop('readonly', false);
                $('#tcs_amount').prop('readonly', false);
                calculate_total();
            } else {
                $("#tcs_rate").val(0);
                $('#tcs_rate').prop('readonly', true);
                $('#tcs_amount').prop('readonly', true);
                calculate_total();
            }
        }
        
    }
</script>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>