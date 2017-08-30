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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>  </h1>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" method="POST" action="<?php echo base_url();?>employee/invoice/process_insert_update_invoice/<?php echo $vendor_partner;?>" enctype="multipart/form-data" >
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
                        <div class="col-md-6 <?php if( form_error('invoice_id') ) { echo 'has-error';} ?>">
                            <div class="form-group">
                                <label for="Vendor Partner" class="col-md-4">Invoice ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="invoice_id" id="invoice_id_gen" value="<?php if (isset($invoice_details[0]['invoice_id'])) {
                                        echo $invoice_details[0]['invoice_id'];
                                        } ?>" placeholder="Invoice ID" <?php if (isset($invoice_details[0]['invoice_id'])) { echo "readonly";} ?> required/>
                                </div>
                                <?php echo form_error('invoice_id'); ?>
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
                                <label for="From Date" class="col-md-4">Period</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="from_date" class="form-control" placeholder="Select Date" name="from_date" type="text" required readonly='true' style="background-color:#fff;" value="">
                                        <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="total service charges" class="col-md-4">Basic Service Charge*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="total_service_charge" value = "<?php if (isset($invoice_details[0]['total_service_charge'])) {
                                        echo $invoice_details[0]['total_service_charge'];
                                        } ?>" placeholder="Total Service Charge" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Parts Cost *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="parts_cost" value = "<?php if (isset($invoice_details[0]['parts_cost'])) {
                                        echo $invoice_details[0]['parts_cost'];
                                        } ?>" placeholder="Parts Cost" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Debit Penalty Amount *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="penalty_amount" value = "<?php if (isset($invoice_details[0]['penalty_amount'])) {
                                        echo $invoice_details[0]['penalty_amount'];
                                        } ?>" placeholder="Penalty Amount" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">credit Penalty Amount *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="credit_penalty_amount" value = "<?php if (isset($invoice_details[0]['credit_penalty_amount'])) {
                                        echo $invoice_details[0]['credit_penalty_amount'];
                                        } ?>" placeholder="Credit Penalty Amount" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Upcountry Charges *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="upcountry_price" value = "<?php if (isset($invoice_details[0]['upcountry_price'])) {
                                        echo $invoice_details[0]['upcountry_price'];
                                        } ?>" placeholder="Upcountry Charges" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Upcountry Distance *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="upcountry_distance" value = "<?php if (isset($invoice_details[0]['upcountry_distance'])) {
                                        echo $invoice_details[0]['upcountry_distance'];
                                        } ?>" placeholder="Upcountry Distance" >
                                </div>
                            </div>
                            <div class="form-group <?php if( form_error('gst_rate') ) { echo 'has-error';} ?>">
                                <label for="Parts Cost" class="col-md-4">GST Rate *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="gst_rate" value = "<?php if (isset($invoice_details[0]['parts_cost'])) {
                                        echo $invoice_details[0]['cgst_tax_rate'] + $invoice_details[0]['sgst_tax_rate'] + $invoice_details[0]['igst_tax_rate'];
                                        } ?>" placeholder="GST Rate" required >
                                </div>
                                 <?php echo form_error('gst_rate'); ?>
                            </div>
                            <div class="form-group">
                                <label for="invoice_file_main" class="col-md-4">Main Invoice</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="invoice_file_main" >
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
                            <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                                <label for="Type Code" class="col-md-4">Type</label>
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
                                        <option value="Stand" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Stand"){ echo "selected";}
                                            } ?>>Stand</option>
                                        <option value="Parts" <?php if (isset($invoice_details[0]['type'])) {
                                            if($invoice_details[0]['type'] == "Parts"){ echo "selected";}
                                            } ?>>Parts</option>
                                    </select>
                                </div>
                                <?php echo form_error('type'); ?>
                            </div>
                            <div class="form-group">
                                <label for="Number of Booking" class="col-md-4">Number of Booking</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="num_bookings" value = "<?php if (isset($invoice_details[0]['num_bookings'])) {
                                        echo $invoice_details[0]['num_bookings'];
                                        } ?>" placeholder="Number of Bookings">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="total additional service charges" class="col-md-4">Additional Charge*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="total_additional_service_charge" value = "<?php if (isset($invoice_details[0]['total_additional_service_charge'])) {
                                        echo $invoice_details[0]['total_additional_service_charge'];
                                        } ?>" placeholder="Total Addtional Service Charge" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Courier Charges *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="courier_charges" value = "<?php if (isset($invoice_details[0]['courier_charges'])) {
                                        echo $invoice_details[0]['courier_charges'];
                                        } ?>" placeholder="Courier Charges" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Debit Penalty Booking Count *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="penalty_bookings_count" value = "<?php if (isset($invoice_details[0]['penalty_bookings_count'])) {
                                        echo $invoice_details[0]['penalty_bookings_count'];
                                        } ?>" placeholder="Penalty Bookings Count" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Credit Penalty Booking Count *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="credit_penalty_bookings_count" value = "<?php if (isset($invoice_details[0]['credit_penalty_bookings_count'])) {
                                        echo $invoice_details[0]['credit_penalty_bookings_count'];
                                        } ?>" placeholder="Credit Penalty Bookings Count" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Upcountry Booking Count*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="upcountry_booking" value = "<?php if (isset($invoice_details[0]['upcountry_booking'])) {
                                        echo $invoice_details[0]['upcountry_booking'];
                                        } ?>" placeholder="Total Upcountry Booking" >
                                </div>
                            </div>
                            <div class="form-group <?php if( form_error('hsn_code') ) { echo 'has-error';} ?>">
                                <label for="HSN CODE" class="col-md-4">HSN Code</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="hsn_code" value = "<?php if (isset($invoice_details[0]['hsn_code'])) { echo $invoice_details[0]['hsn_code']; } ?>" placeholder="HSN Code" >
                                </div>
                                 <?php echo form_error('hsn_code'); ?>
                            </div>
                            <div class="form-group">
                                <label for="invoice_file_excel" class="col-md-4">Main Invoice Excel</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="invoice_file_excel" >
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
                        </div>
                        <div class="clearfix" ></div>
                        <div class="col-md-offset-5" style ="margin-top: 40px; margin-bottom: 20px;">
                            <?php if (isset($invoice_details[0]['invoice_id'])) {} else {?> <a href="javascript:void(0)" onclick="fetch_invoice_id()" class="btn btn-md btn-success" >Fetch Invoice ID</a><?php  } ?>
                            <input type="submit" value="<?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>" class="btn btn-md btn-primary" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#to_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#from_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#vendor_partner_id").select2();
    
    control_type_code(0);
    
     function from_calendar() {
           $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
       }
       
        $(document).ready(function(){
          partner_vendor('<?php echo $vendor_partner; ?>');   
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
            
            if(vendor_partner_id === null){ 
                alert("Please Select Entity");
                return false;
                
            } else if(type_code === undefined){ 
                alert("Please Select Buyer/Seller");
                return false;
            } else {
                $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/fetch_invoice_id/' + vendor_partner_id 
                        + '/' + vendor_partner_type + '/' + type_code,
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
</script>