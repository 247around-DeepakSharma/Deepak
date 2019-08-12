<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script type="text/javascript">
    $(function() {
        $('input[name="from_date"]').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            <?php if (isset($invoice_details[0]['from_date'])) { ?>
                startDate: '<?php if (isset($invoice_details[0]['from_date'])) { echo $invoice_details[0]['from_date'];}  ?>',
                endDate: '<?php if (isset($invoice_details[0]['from_date'])) { echo $invoice_details[0]['to_date'];} ?>'
            <?php } ?>
            
        });
    
    });
</script>
<div class='container' style="margin-top:20px;">
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>  </h1>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" method="POST" action="<?php echo base_url();?>employee/invoice/process_insert_update_invoice/<?php echo $vendor_partner;?>" enctype="multipart/form-data" >
                <input type="hidden" value="<?php if(isset($invoice_details[0]['vertical'])){ echo $invoice_details[0]['vertical'];  } ?>" id="vertical_input">
                <input type="hidden" value="<?php if(isset($invoice_details[0]['category'])){ echo $invoice_details[0]['category'];  } ?>" id="category_input">
                <input type="hidden" value="<?php if(isset($invoice_details[0]['sub_category'])){ echo $invoice_details[0]['sub_category'];  } ?>" id="sub_category_input">
                <input type="hidden" value="<?php if(isset($invoice_details[0]['accounting'])){ echo $invoice_details[0]['accounting'];  } ?>" id="accounting_input">
                <div class="row">
                    <div class="col-md-12 col-md-offset-3">
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="Around Type">247Around is</label>
                            <div class="col-md-9">
                                <label class="radio-inline">
                                <input onchange="control_type_code(1)" <?php if (isset($invoice_details[0]['type_code'])) {
                                    if($invoice_details[0]['type_code'] == "B"){ echo "checked";}
                                    } ?> data-val="true" data-val-required="247Around Type is required." id="around_type" name="around_type" type="radio" value="B" required>
                                Buyer
                                </label>
                                <label class="radio-inline">
                                <input onchange="control_type_code(1)" <?php if (isset($invoice_details[0]['type_code'])) {
                                    if($invoice_details[0]['type_code'] == "A"){ echo "checked";}
                                    } ?> id="around_type" name="around_type" type="radio" value="A">
                                Seller
                                </label>
                            </div>
                        </div>
                       
                    </div>
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="col-md-6 ">
                            <div class="form-group <?php if( form_error('invoice_id') ) { echo 'has-error';} ?>">
                                <label for="Vendor Partner" class="col-md-4">Invoice ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="invoice_id" id="invoice_id_gen" value="<?php if (isset($invoice_details[0]['invoice_id'])) {
                                        echo $invoice_details[0]['invoice_id'];
                                        } ?>" placeholder="Invoice ID" <?php if (isset($invoice_details[0]['invoice_id'])) { echo "readonly";} ?> required/>
                                </div>
                                <?php echo form_error('invoice_id'); ?>
                            </div>
                             <div class="form-group <?php if( form_error('reference_invoice_id') ) { echo 'has-error';} ?>">
                                <label for="Vendor Partner" class="col-md-4">Reference Invoice ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="reference_invoice_id" id="reference_invoice_id" value="<?php if (isset($invoice_details[0]['reference_invoice_id'])) {
                                        echo $invoice_details[0]['reference_invoice_id'];
                                        } ?>" placeholder="Reference Invoice ID" />
                                </div>
                                <?php echo form_error('reference_invoice_id'); ?>
                            </div>
                            <div class="form-group <?php if( form_error('vendor_partner_id') ) { echo 'has-error';} ?>">
                                <label for="Vendor Partner" class="col-md-4">Entity</label>
                                <div class="col-md-6">
                                    <select type="text" class="form-control"  id="vendor_partner_id" name="vendor_partner_id"  required>
                                    </select>
                                </div>
                                 <?php echo form_error('vendor_partner_id'); ?>
                            </div>
                           
                           
                            <div class="form-group" >
                                <label for="Due Date" class="col-md-4">Vertical*</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="vertical" id="vertical" onchange="get_category('<?php echo base_url(); ?>')" required>

                                    </select>
                                </div>
                            </div>
                             <div class="form-group" >
                                <label for="Due Date" class="col-md-4">Sub Category*</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="sub_category" id="sub_category" onchange="get_accounting(this);" required>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                                <label for="Type Code" class="col-md-4">Type*</label>
                                <div class="col-md-6">
                                    <select name="type" class="form-control" id="type_code">
                                        <option value="" disabled selected>Select Type</option>
                                        <option  value="Cash" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Cash"){ echo "selected";}
                                            } ?>>Cash</option>
                                        <option value="DebitNote" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "DebitNote"){ echo "selected";}
                                            } ?>>DebitNote</option>
                                        <option value="CreditNote" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "CreditNote"){ echo "selected";}
                                            } ?>>CreditNote</option>
                                        <option value="FOC" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "FOC"){ echo "selected";}
                                            } ?>>FOC</option>
                                        <option value="Buyback" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Buyback"){ echo "selected";}
                                            } ?>>Buyback</option>
                                        <option value="<?php echo BUYBACK_VOUCHER;?>" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == BUYBACK_VOUCHER){ echo "selected";}
                                            } ?>><?php echo BUYBACK_VOUCHER; ?></option>
                                         <option value="<?php echo PARTNER_VOUCHER;?>" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == PARTNER_VOUCHER){ echo "selected";}
                                            } ?>><?php echo PARTNER_VOUCHER; ?></option>
                                        <option value="Stand" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Stand"){ echo "selected";}
                                            } ?>>Stand</option>
                                        <option value="Parts" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Parts"){ echo "selected";}
                                            } ?>>Parts</option>
                                        <option value="Liquidation" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == LIQUIDATION){ echo "selected";}
                                            } ?>>Liquidation</option>
                                    </select>
                                </div>
                                <?php echo form_error('type'); ?>
                            </div>
                           
                            <div class="form-group">
                                <label for="Number of Booking" class="col-md-4">Number of Booking</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control"  name="num_bookings" value = "<?php if (isset($invoice_details[0]['num_bookings'])) {
                                        echo $invoice_details[0]['num_bookings'];
                                        } else { echo "0";} ?>" placeholder="Number of Bookings">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="total service charges" class="col-md-4">Basic Service Charge*</label>
                                <div class="col-md-6">
                                    <input type="number"   class="form-control"  name="total_service_charge" value = "<?php if (isset($invoice_details[0]['total_service_charge'])) {
                                        echo $invoice_details[0]['total_service_charge'];
                                        } else { echo "0";} ?>" placeholder="Total Service Charge" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Parts Cost *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="parts_cost" value = "<?php if (isset($invoice_details[0]['parts_cost'])) {
                                        echo $invoice_details[0]['parts_cost'];
                                        } else { echo "0";} ?>" placeholder="Parts Cost" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Debit Penalty Amount *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="penalty_amount" value = "<?php if (isset($invoice_details[0]['penalty_amount'])) {
                                        echo $invoice_details[0]['penalty_amount'];
                                        }else { echo "0";} ?>" placeholder="Penalty Amount" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">credit Penalty Amount *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="credit_penalty_amount" value = "<?php if (isset($invoice_details[0]['credit_penalty_amount'])) {
                                        echo $invoice_details[0]['credit_penalty_amount'];
                                        } else { echo "0";} ?>" placeholder="Credit Penalty Amount" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Upcountry Charges *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="upcountry_price" value = "<?php if (isset($invoice_details[0]['upcountry_price'])) {
                                        echo $invoice_details[0]['upcountry_price'];
                                        } else { echo "0";} ?>" placeholder="Upcountry Charges" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Upcountry Distance *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="upcountry_distance" value = "<?php if (isset($invoice_details[0]['upcountry_distance'])) {
                                        echo $invoice_details[0]['upcountry_distance'];
                                        } else { echo "0";} ?>" placeholder="Upcountry Distance" >
                                </div>
                            </div>
                            <div class="form-group <?php if( form_error('packaging_rate') ) { echo 'has-error';} ?>">
                                <label for="Parts Cost" class="col-md-4">Packaging Rate </label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="packaging_rate" value = "<?php if (isset($invoice_details[0]['packaging_rate'])) {
                                        echo $invoice_details[0]['packaging_rate'];
                                        } else { echo "0";} ?>" placeholder="Packaging Rate" required >
                                </div>
                                 <?php echo form_error('packaging_rate'); ?>
                            </div>
                            
                            <div class="form-group <?php if( form_error('warehouse_storage_charges') ) { echo 'has-error';} ?>">
                                <label for="Warehouse Storage Charge" class="col-md-4">Warehouse Storage Charge </label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="warehouse_storage_charges" value = "<?php if (isset($invoice_details[0]['warehouse_storage_charges'])) {
                                        echo $invoice_details[0]['warehouse_storage_charges'];
                                        } else { echo "0";} ?>" placeholder="Warehouse Storage Charge" required >
                                </div>
                                 <?php echo form_error('packaging_rate'); ?>
                            </div>
                            
                            <div class="form-group <?php if( form_error('gst_rate') ) { echo 'has-error';} ?>">
                                <label for="Parts Cost" class="col-md-4">GST Rate *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="gst_rate" value = "<?php if (isset($invoice_details[0]['igst_tax_rate'])) {
                                        echo $invoice_details[0]['cgst_tax_rate'] + $invoice_details[0]['sgst_tax_rate'] + $invoice_details[0]['igst_tax_rate'];
                                        } else { echo DEFAULT_TAX_RATE;} ?>" placeholder="GST Rate" required >
                                </div>
                                 <?php echo form_error('gst_rate'); ?>
                            </div>
                            <div class="form-group">
                                <label for="remarks" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" cols="50" rows="4" id="remarks" name="remarks" placeholder="remarks"><?php if (isset($invoice_details[0]['remarks'])){echo $invoice_details[0]['remarks'];}?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" >
                                <label for="Invoice Date" class="col-md-4">Invoice Date</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="invoice_date" class="form-control" placeholder="Select Date" name="invoice_date" type="text" required readonly='true' style="background-color:#fff;cursor: pointer;" value="<?php if (isset($invoice_details[0]['invoice_date'])) {
                                            echo $invoice_details[0]['invoice_date'];
                                            } else { echo date('Y-m-d');} ?>">
                                        <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" >
                                <label for="Due Date" class="col-md-4">Due Date</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="due_date" class="form-control" placeholder="Select Date" name="due_date" type="text" required readonly='true' style="background-color:#fff;cursor: pointer;" value="<?php if (isset($invoice_details[0]['due_date'])) {
                                            echo $invoice_details[0]['due_date'];
                                            } else { echo date('Y-m-d');} ?>">
                                        <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                             <div class="form-group" >
                                <label for="From Date" class="col-md-4">Period</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="from_date" class="form-control" placeholder="Select Date" name="from_date" type="text" required readonly='true' style="background-color:#fff;" value="">
                                        <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" >
                                <label for="Due Date" class="col-md-4">Category*</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="category" id="category" onchange="get_sub_category('<?php echo base_url(); ?>')" required>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Number of Parts" class="col-md-4">Number of Parts</label>
                                <div class="col-md-6">
                                    <input type="number"  class="form-control"  name="parts_count" value = "<?php if (isset($invoice_details[0]['parts_count'])) {
                                        echo $invoice_details[0]['parts_count'];
                                        } else { echo "0";} ?>" placeholder="Number of Parts">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="total additional service charges" class="col-md-4">Additional Charge*</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="total_additional_service_charge" value = "<?php if (isset($invoice_details[0]['total_additional_service_charge'])) {
                                        echo $invoice_details[0]['total_additional_service_charge'];
                                        } else { echo "0";} ?>" placeholder="Total Addtional Service Charge" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Courier Charges *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="courier_charges" value = "<?php if (isset($invoice_details[0]['courier_charges'])) {
                                        echo $invoice_details[0]['courier_charges'];
                                        } else {echo "0";} ?>" placeholder="Courier Charges" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Debit Penalty Booking Count *</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control"  name="penalty_bookings_count" value = "<?php if (isset($invoice_details[0]['penalty_bookings_count'])) {
                                        echo $invoice_details[0]['penalty_bookings_count'];
                                        } else { echo "0";} ?>" placeholder="Penalty Bookings Count" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Credit Penalty Booking Count *</label>
                                <div class="col-md-6">
                                    <input type="number" step=".01" class="form-control"  name="credit_penalty_bookings_count" value = "<?php if (isset($invoice_details[0]['credit_penalty_bookings_count'])) {
                                        echo $invoice_details[0]['credit_penalty_bookings_count'];
                                        } else { echo "0";} ?>" placeholder="Credit Penalty Bookings Count" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Upcountry Booking Count*</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control"  name="upcountry_booking" value = "<?php if (isset($invoice_details[0]['upcountry_booking'])) {
                                        echo $invoice_details[0]['upcountry_booking'];
                                        } else { echo "0";} ?>" placeholder="Total Upcountry Booking" >
                                </div>
                            </div>
                            <div class="form-group <?php if( form_error('hsn_code') ) { echo 'has-error';} ?>">
                                <label for="HSN CODE" class="col-md-4">HSN Code</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="hsn_code" value = "<?php if (isset($invoice_details[0]['hsn_code'])) { echo $invoice_details[0]['hsn_code']; } ?>" placeholder="HSN Code" >
                                </div>
                                 <?php echo form_error('hsn_code'); ?>
                            </div>
                            <div class="form-group <?php if( form_error('packaging_quantity') ) { echo 'has-error';} ?>">
                                <label for="Parts Cost" class="col-md-4">Packaging Quantity </label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="packaging_quantity" value = "<?php if (isset($invoice_details[0]['packaging_quantity'])) {
                                        echo $invoice_details[0]['packaging_quantity'];
                                        } else { echo "0";} ?>" placeholder="GST Rate" required >
                                </div>
                                 <?php echo form_error('packaging_quantity'); ?>
                            </div>
                            <div class="form-group <?php if( form_error('miscellaneous_charges') ) { echo 'has-error';} ?>">
                                <label for="miscellaneous Charge" class="col-md-4">Miscellaneous Charge </label>
                                <div class="col-md-6">
                                    <input type="number" step=".01"  class="form-control"  name="miscellaneous_charges" value = "<?php if (isset($invoice_details[0]['miscellaneous_charges'])) {
                                        echo $invoice_details[0]['miscellaneous_charges'];
                                        } else { echo "0";} ?>" placeholder="Miscellaneous Charge" required >
                                </div>
                                 <?php echo form_error('miscellaneous_charges'); ?>
                            </div>
                            <div class="form-group">
                                <label for="invoice_file_excel" class="col-md-4">Main Invoice Excel <?php if (!isset($invoice_details[0]['invoice_id'])) { echo "*"; } ?></label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="invoice_file_excel" id="invoice_file_excel" <?php if (!isset($invoice_details[0]['invoice_id'])) { echo "required"; } ?>  >
                                </div>
                                <div class="col-md-2">
                                    <?php
                                        if (isset($invoice_details[0]['invoice_file_excel'])) {
                                            if (!is_null($invoice_details[0]['invoice_file_excel'])) {
                                        
                                                if (isset($invoice_details[0]['invoice_file_excel']) && !empty($invoice_details[0]['invoice_file_excel'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_file_excel']; ?>
                                    <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                    <?php }
                                        }
                                        }
                                        ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_detailed_excel" class="col-md-4">Detailed Invoice Excel</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="invoice_detailed_excel" >
                                </div>
                                <div class="col-md-2">
                                    <?php
                                        if (isset($invoice_details[0]['invoice_detailed_excel'])) {
                                            if (!is_null($invoice_details[0]['invoice_detailed_excel'])) {
                                        
                                                if (isset($invoice_details[0]['invoice_detailed_excel']) && !empty($invoice_details[0]['invoice_detailed_excel'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_detailed_excel']; ?>
                                    <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                    <?php }
                                        }
                                        }
                                        ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_file_main" class="col-md-4">Main Invoice <?php if (!isset($invoice_details[0]['invoice_id'])) { echo "*"; } ?></label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="invoice_file_main" id="invoice_file_main" <?php if (!isset($invoice_details[0]['invoice_id'])) { echo "required"; } ?> >
                                </div>
                                <div class="col-md-2">
                                    <?php
                                        if (isset($invoice_details[0]['invoice_file_main'])) {
                                            if (!is_null($invoice_details[0]['invoice_file_main'])) {
                                        
                                                if (isset($invoice_details[0]['invoice_file_main']) && !empty($invoice_details[0]['invoice_file_main'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_file_main']; ?>
                                    <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                    <?php }
                                        }
                                        }
                                        ?>
                                </div>
                            </div>
                                
                                
                                
                        </div>
                        <div class="clearfix" ></div>
                        <div class="col-md-offset-5" style ="margin-top: 40px; margin-bottom: 20px;">
                            <?php if (isset($invoice_details[0]['invoice_id'])) {} else {?> <a href="javascript:void(0)" onclick="fetch_invoice_id()" class="btn btn-md btn-success" >Fetch Invoice ID</a><?php  } ?>
                            <input type="submit" id="submitform" value="<?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>" class="btn btn-md btn-primary" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>js/invoice_tag.js"></script>
<script>
    $("#to_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#due_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#from_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#vendor_partner_id").select2();
    
    control_type_code(0);
    
     function from_calendar() {
           $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
       }
       
        $(document).ready(function(){
          partner_vendor('<?php echo $vendor_partner; ?>');   
          get_vertical('<?php echo base_url(); ?>');
       });
      
    
     
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
             success: function (data) {
                 $("#vendor_partner_id").select2().html(data).change();
                 //$("#vendor_partner_id").html(data);
    
             }
         });
       }
       
       function fetch_invoice_id(){
            
            var vendor_partner_type = '<?php echo $vendor_partner; ?>';
            var vendor_partner_id =  $("#vendor_partner_id").val();
            var type_code = $("input[name='around_type']:checked").val();
            var type = $("#type_code").val();
    
            if(vendor_partner_id === null){ 
                alert("Please Select Entity");
                return false;
                
            } else if(type_code === undefined){ 
                alert("Please Select Buyer/Seller");
                return false;
            } else if(type ==null){
                alert("Please Invoice Type");
                return false;
            } else {
                $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/fetch_invoice_id/' + vendor_partner_id 
                        + '/' + vendor_partner_type + '/' + type_code+"/"+type,
                    success: function (data) {
                        alert(data);
                        $(".panel-title").html(data);
                        $("#invoice_id_gen").val(data);
                        
                    }
                });
                
            } 
       }
       
    function control_type_code(is_value){
        var radioValue = $("input[name='around_type']:checked").val();
        if(is_value === 1){
            $('#type_code option:eq(0)').prop('selected', true);
            $('#invoice_id_gen').val('');
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
    }
    
    $('#submitform').on('click', function() {
        var insert = "<?php echo !isset($invoice_details[0]['invoice_id'])?>";
        if(insert) {
            if($("#invoice_file_excel").val() === ''){ 
                alert("Please Select Main Invoice Excel File");
                $("#invoice_file_excel").focus();
                return false;
            }
            else if($("#invoice_file_main").val() === ''){ 
                alert("Please Select Main Invoice File");
                $("#invoice_file_main").focus();
                return false;
            }
        }
        var $this = $(this);
        $this.button('loading');
          setTimeout(function() {
             $this.button('reset');
         }, 5000);
      });

  
</script>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
