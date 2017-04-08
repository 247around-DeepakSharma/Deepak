<div class='container' style="margin-top:20px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php if (isset($invoice_details[0]['invoice_id'])) { echo "Update Invoice"; } else { echo "Insert Invoice";}?>  </h1>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" method="POST" action="<?php echo base_url();?>employee/invoice/process_insert_update_invoice/<?php echo $vendor_partner;?>" enctype="multipart/form-data" >
                <div class="row">
                    <div class="col-md-12">
                    <div class="col-md-6 col-md-offset-4 <?php if( form_error('invoice_id') ) { echo 'has-error';} ?>">
                        <div class="form-group">
                               
                               <div class="col-md-6">
                                   <input type="text" class="form-control" name="invoice_id" id="invoice_id_gen" value="<?php if (isset($invoice_details[0]['invoice_id'])) {
                               echo $invoice_details[0]['invoice_id'];
                               } ?>" placeholder="Invoice ID" <?php if (isset($invoice_details[0]['invoice_id'])) { echo "readonly";;} ?> required/>
                               </div>
                            <?php echo form_error('invoice_id'); ?>
                           </div>
                    </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="col-md-6">
                           
                            <div class="form-group">
                                <label for="Vendor Partner" class="col-md-4">Entity</label>
                                <div class="col-md-6">
                                    <select type="text" class="form-control"  id="vendor_partner_id" name="vendor_partner_id"  required>
                                    </select>

                                </div>
                            </div>
                            
                             <input type="hidden" name="new_invoice_id_flag" id="new_invoice_id_flag" value="0"/>
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
                                <label for="total service charges" class="col-md-4">Basic Service Charge*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="total_service_charge" value = "<?php if (isset($invoice_details[0]['total_service_charge'])) {
                                        echo $invoice_details[0]['total_service_charge'];
                                        } ?>" placeholder="Total Service Charge" >
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
                                <label for="Parts Cost" class="col-md-4">Parts Cost *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="parts_cost" value = "<?php if (isset($invoice_details[0]['parts_cost'])) {
                                        echo $invoice_details[0]['parts_cost'];
                                        } ?>" placeholder="Parts Cost" >
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="Parts Cost" class="col-md-4">Total Upcountry Booking *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="upcountry_booking" value = "<?php if (isset($invoice_details[0]['upcountry_booking'])) {
                                        echo $invoice_details[0]['upcountry_booking'];
                                        } ?>" placeholder="Total Upcountry Booking" >
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
                                <label for="Parts Cost" class="col-md-4">Total Penalty Booking *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="penalty_bookings_count" value = "<?php if (isset($invoice_details[0]['penalty_bookings_count'])) {
                                        echo $invoice_details[0]['penalty_bookings_count'];
                                        } ?>" placeholder="Penalty Bookings Count" >
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
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_file_excel']; ?>
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
                                    <textarea class="form-control" cols="50" rows="5" id="remarks" name="remarks" placeholder="remarks"><?php if (isset($invoice_details[0]['remarks'])){echo $invoice_details[0]['remarks'];}?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Type Code" class="col-md-4">Type Code</label>
                                <div class="col-md-6">
                                    <select name="type" class="form-control" id="type_code">
                                        <option value="Cash" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "Cash"){ echo "selected";}
                                            } ?>>Cash</option>
                                        <option value="BuybackCash" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "BuybackCash"){ echo "selected";}
                                            } ?>>BuybackCash</option>
                                        <option value="CreditNote" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "CreditNote"){ echo "selected";}
                                            } ?>>CreditNote</option>
                                        <option value="DebitNote" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "DebitNote"){ echo "selected";}
                                            } ?>>DebitNote</option>
                                        <option value="FOC" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "FOC"){ echo "selected";}
                                            } ?>>FOC</option>
                                         <option value=">BuybackFOC" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "BuybackFOC"){ echo "selected";}
                                            } ?>>BuybackFOC</option>
                                        <option value="Stand" <?php if (isset($invoice_details[0]['type_code'])) {
                                            if($invoice_details[0]['type'] == "Stand"){ echo "selected";}
                                            } ?>>Stand</option>
                                    </select>
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
                                <label for="Service Tax" class="col-md-4">Service Tax*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="service_tax" value = "<?php if (isset($invoice_details[0]['service_tax'])) {
                                        echo $invoice_details[0]['service_tax'];
                                        } ?>" placeholder="Service Tax">
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
                            <div class="form-group">
                                <label for="vat" class="col-md-4">VAT*</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="vat" value = "<?php if (isset($invoice_details[0]['vat'])) {
                                        echo $invoice_details[0]['vat'];
                                        } ?>" placeholder="VAT" >
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
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Courier Charges *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="courier_charges" value = "<?php if (isset($invoice_details[0]['courier_charges'])) {
                                        echo $invoice_details[0]['courier_charges'];
                                        } ?>" placeholder="Courier Charges" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vat" class="col-md-4">Penalty Amount *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="penalty_amount" value = "<?php if (isset($invoice_details[0]['penalty_amount'])) {
                                        echo $invoice_details[0]['penalty_amount'];
                                        } ?>" placeholder="Penalty Amount" >
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
                            <div class="col-md-offset-4">
                                <label class="checkbox-inline">
                                <input type="checkbox" checked data-toggle="toggle" name="sms_sent"> SMS Sent
                              </label>
                              <label class="checkbox-inline">
                                <input type="checkbox" data-toggle="toggle" name="mail_sent"> Mail Sent
                              </label>
                             
                                        
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
    $("#from_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#vendor_partner_id").select2();
    $("#type_code").select2();
    
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
            var from_date = $("#from_date").val();
            var type_code = $("#type_code").val();
            
            if(from_date !==""){
                $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/fetch_invoice_id/' + vendor_partner_id 
                        + '/' + vendor_partner_type + '/' + from_date + '/' + type_code,
                    success: function (data) {
                        alert(data);
                        $(".panel-title").html(data);
                        $("#invoice_id_gen").val(data);
                        $("#new_invoice_id_flag").val('1');
                    }
                });
                
            }
        
        
       }
</script>