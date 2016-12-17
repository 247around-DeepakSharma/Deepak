<div class='container' style="margin-top:20px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>  </h1>
        </div>
        <div class="panel-body">
            
                <form class="form-horizontal" method="POST" action="<?php echo base_url();?>employee/invoice/process_insert_update_invoice/<?php echo $vendor_partner;?>" >
                         <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Vendor Partner" class="col-md-4">Entity</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control"  id="vendor_partner_id" name="vendor_partner_id"  required>
                                                    <option></option>
                                                </select>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="invoice id" class="col-md-4">Invoice ID</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="invoice_id" value = "<?php if (isset($invoice_details[0]['invoice_id'])) { echo $invoice_details[0]['invoice_id']; }?>" placeholder="Invoice Id" <?php if (isset($invoice_details[0]['invoice_id'])) { echo "readonly"; }?>>
                                            </div>
                                        </div>
                                       <div class="form-group" >
                                            <label for="From Date" class="col-md-4">To Date</label>
                                            <div class="col-md-6">
                                                <div class="input-group input-append date">
                                                    <input id="to_date" class="form-control" placeholder="Select Date" name="to_date" type="text" required readonly='true' style="background-color:#fff;cursor: pointer;" value="<?php if (isset($invoice_details[0]['to_date'])) {
                                                    echo $invoice_details[0]['to_date'];
                                                    } ?>">
                                                    <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="total service charges" class="col-md-4">Total Service Charge*</label>
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
                                            <label for="total additional service charges" class="col-md-4">Additional Charge*</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="total_service_charge" value = "<?php if (isset($invoice_details[0]['total_additional_service_charge'])) {
                                                    echo $invoice_details[0]['total_additional_service_charge'];
                                                    } ?>" placeholder="Total Addtional Service Charge" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label for="total additional service charges" class="col-md-4">Around Royalty*</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="around_royalty" value = "<?php if (isset($invoice_details[0]['around_royalty'])) {
                                                    echo $invoice_details[0]['around_royalty'];
                                                    } ?>" placeholder="Around Royalty" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label for="amount_collected_paid" class="col-md-4">Amount Collected Paid*</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="amount_collected_paid" value = "<?php if (isset($invoice_details[0]['amount_collected_paid'])) {
                                                    echo $invoice_details[0]['amount_collected_paid'];
                                                    } ?>" placeholder="Amount Collected Paid" >
                                            </div>
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
                                                                $src = "https://s3.amazonaws.com/bookings-collateral/misc-images/" . $invoice_details[0]['invoice_file_excel']; ?>
                                                            <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                               
                                          
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Type Code" class="col-md-4">Type Code</label>
                                            <div class="col-md-6">
                                                <select name="type_code" class="form-control">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="D">D</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Number of Booking" class="col-md-4">Number of Booking</label>
                                            <div class="col-md-6">
                                                 <input type="text" class="form-control"  name="num_bookings" value = "<?php if (isset($invoice_details[0]['num_bookings'])) {
                                                    echo $invoice_details[0]['num_bookings'];
                                                    } ?>" placeholder="Number of Bookings">
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label for="From Date" class="col-md-4">From Date</label>
                                            <div class="col-md-6">
                                                <div class="input-group input-append date">
                                                    <input id="from_date" class="form-control" placeholder="Select Date" name="from_date" type="text" required readonly='true' style="background-color:#fff;" value="<?php if (isset($invoice_details[0]['from_date'])) {
                                                    echo $invoice_details[0]['from_date'];
                                                    } ?>">
                                                    <span class="input-group-addon add-on" onclick="from_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Service Tax" class="col-md-4">Service Tax*</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="service_tax" value = "<?php if (isset($invoice_details[0]['service_tax'])) {
                                                    echo $invoice_details[0]['service_tax'];
                                                    } ?>" placeholder="Service Tax">
                                            </div>
                                            
                                        </div>
                                        <div class="form-group">
                                            <label for="vat" class="col-md-4">VAT*</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="vat" value = "<?php if (isset($invoice_details[0]['vat'])) {
                                                    echo $invoice_details[0]['vat'];
                                                    } ?>" placeholder="VAT" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Total Amount Collected" class="col-md-4">Total Amount Collected* </label>
                                            <div class="col-md-6">
                                               <input type="text" class="form-control"  name="total_amount_collected" value = "<?php if (isset($invoice_details[0]['total_amount_collected'])) {
                                                    echo $invoice_details[0]['total_amount_collected'];
                                                    } ?>" placeholder="Total Amount Collected" >
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label for="Total Amount Collected" class="col-md-4">TDS* </label>
                                            <div class="col-md-6">
                                               <input type="text" class="form-control"  name="tds_amount" value = "<?php if (isset($invoice_details[0]['tds_amount'])) {
                                                    echo $invoice_details[0]['tds_amount'];
                                                    } ?>" placeholder="TDS" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Amount Paid" class="col-md-4">Amount Paid* </label>
                                            <div class="col-md-6">
                                               <input type="text" class="form-control"  name="amount_paid" value = "<?php if (isset($invoice_details[0]['amount_paid'])) {
                                                    echo $invoice_details[0]['amount_paid'];
                                                    } ?>" placeholder="Amount Paid" >
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
                                                                $src = "https://s3.amazonaws.com/bookings-collateral/misc-images/" . $invoice_details[0]['invoice_detailed_excel']; ?>
                                                            <a href="<?php echo $src ?>" target="_blank">click Here</a>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                               
                                          
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-offset-4">
                                    <label class="radio-inline"><input type="radio"  name="settle_amount" <?php if (isset($invoice_details[0]['settle_amount'])) {
                                if ($invoice_details[0]['settle_amount'] == "1") {
                                    echo "checked";
                                }
                                } ?> value="1">Amount Settle</label>
                            <label class="radio-inline"><input type="radio" name="mail_sent" <?php if (isset($invoice_details[0]['mail_sent'])) {
                                if ($invoice_details[0]['mail_sent'] == "1") {
                                    echo "checked";
                                }
                                } ?> value="1">Mail Sent</label>
                            <label class="radio-inline"><input type="radio" name="sms_sent" <?php if (isset($invoice_details[0]['sms_sent'])) {
                                if ($invoice_details[0]['sms_sent'] == "1") {
                                    echo "checked";
                                }
                                } ?> value="1">SMS Sent</label>
                                        
                                    </div>
                                    <div class="col-md-offset-5" style ="margin-top: 20px; margin-bottom: 20px;">
                                        <input type="submit" value="Update Invoice" class="btn btn-md btn-primary" />
                                    </div>
                                </div>
                            </div>
                    
                </form>

        </div>
    </div>
</div>
<script>
 $("#to_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
 $("#from_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
  function from_calendar() {
        $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
    }
    
     $(document).ready(function(){
       partner_vendor('<?php echo $vendor_partner; ?>');   
    });
   

  
  function partner_vendor(vendor_partner){
     
      var vendor_partner_id = $("#vendor_partner_id").val();
      if(vendor_partner_id === ''){
          <?php if (isset($invoice_details[0]['vendor_partner_id'])) { ?>
            vendor_partner_id = '<?php echo $invoice_details[0]['vendor_partner_id']; ?>';      
           <?php } ?>
      }

    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + vendor_partner,
          data: {vendor_partner_id: vendor_partner_id,invoice_flag: 0},
          success: function (data) {

              $("#vendor_partner_id").html(data);

          }
      });
    }
</script>