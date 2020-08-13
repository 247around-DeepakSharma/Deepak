
<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading ">
                Update Invoice (<?php echo $invoice_details[0]['invoice_id'];?>)
            </div>
            <div class="panel-body">
                <form enctype="multipart/form-data"  class="form-horizontal" method="post" action="<?php echo base_url();?>employee/invoice/update_invoice_with_breakup/<?php echo $vendor_partner;?>/<?php echo $invoice_details[0]['vendor_partner_id'];?>/<?php echo $invoice_breakup[0]['invoice_id'];?>">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['vertical'])){ echo $invoice_details[0]['vertical'];  } ?>" id="vertical_input">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['category'])){ echo $invoice_details[0]['category'];  } ?>" id="category_input">
                    <input type="hidden" value="<?php if(isset($invoice_details[0]['sub_category'])){ echo $invoice_details[0]['sub_category'];  } ?>" id="sub_category_input">
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
                                <div class="form-group" >
                                    <label for="Invoice Date" class="col-md-4">Invoice Date</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="invoice_date" class="form-control" placeholder="Select Date" name="invoice_date" type="text" required readonly='true' style="pointer-events: none;" value="<?php if (isset($invoice_details[0]['invoice_date'])) {
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
                                            <option value="<?php echo DEBIT_NOTE; ?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == DEBIT_NOTE){ echo "selected";}
                                                } ?>><?php echo DEBIT_NOTE;?></option>
                                            <option value="<?php echo CREDIT_NOTE; ?>" <?php if (isset($invoice_details[0]['type'])) {
                                                if($invoice_details[0]['type'] == CREDIT_NOTE){ echo "selected";}
                                                } ?>><?php echo CREDIT_NOTE;?></option>
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
                                                if($invoice_details[0]['type'] == "Liquidation"){ echo "selected";}
                                                } ?>>Liquidation</option>
                                        </select>
                                    </div>
                                    <?php echo form_error('type'); ?>
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
                                <div class="form-group">
                                    <label for="remarks" class="col-md-4">Remarks</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" cols="50" rows="4" id="remarks" name="remarks" placeholder="remarks"><?php if (isset($invoice_details[0]['remarks'])){echo $invoice_details[0]['remarks'];}?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
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
                                    <label for="Due Date" class="col-md-4">Vertical*</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="vertical" id="vertical" onchange="get_category('<?php echo base_url(); ?>')" required>
                                          
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="Due Date" class="col-md-4">Category*</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="category" id="category" onchange="get_sub_category('<?php echo base_url(); ?>')" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="Due Date" class="col-md-4">Sub Category*</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="sub_category" id="sub_category" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label for="Due Date" class="col-md-4">Accounting</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="accounting" id="accounting">
                                            <option selected disabled>select Yes/No</option>
                                            <option value="1" <?php if($invoice_details[0]['accounting'] === '1'){ echo 'selected'; } ?>>Yes</option>
                                            <option value="0" <?php if($invoice_details[0]['accounting'] === '0'){ echo 'selected'; } ?>>No</option>
                                        </select>
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
                                        <?php if($invoice_breakup[0]['igst_tax_amount'] != 0){ ?>
                                        <th class="text-center" colspan="2">IGST </th>
                                        <?php } else if($invoice_breakup[0]['sgst_tax_amount'] != 0){ ?>
                                        <th class="text-center" colspan="2">SGST </th>
                                        <th class="text-center" colspan="2">CGST </th>
                                        <?php }  ?>
                                        <th class="text-center">Total</th>
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
                                        <?php if($invoice_breakup[0]['sgst_tax_amount'] != 0){ ?>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Amount</th>
                                         <?php }  ?>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoice_breakup as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo ($key +1);?></td>
                                        <td style="width:34%">
                                            <textarea id="<?php echo "description_".$key; ?>" style="width:100%" name="invoice[<?php echo $value['id']; ?>][description]" class="form-control"><?php echo $value['description'];?></textarea>
                                        </td>
                                        <td><input id="<?php echo "productorservices_".$key; ?>" readonly type="text" name="invoice[<?php echo $value['id']; ?>][product_or_services]"  value="<?php echo $value['product_or_services'];?>" class="form-control col-md-1" ></td>
                                        <td><input id="<?php echo "hsncode_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][hsn_code]"  value="<?php echo $value['hsn_code'];?>" class="form-control col-md-1" ></td>
                                        <td><input onkeyup="change_prices('<?php echo $key; ?>')" id="<?php echo "qty_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][qty]" value="<?php echo $value['qty'];?>" class="form-control quantity"></td>
                                        <td><input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "rate_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][rate]" value="<?php echo $value['rate'];?>" class="form-control rate"></td>
                                        <td><input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "taxablevalue_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][taxable_value]" value="<?php echo $value['taxable_value'];?>" class="form-control taxable_value" ></td>
                                        <?php if($value['igst_tax_amount'] != 0){ ?>
                                        <td> <input   id="is_igst" type="hidden" name="is_igst" value="1" >
                                            <input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "igsttaxrate_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][igst_tax_rate]" value="<?php echo $value['igst_tax_rate'];?>" class="form-control igst_tax_rate">
                                        </td>
                                        <td><input id="<?php echo "igsttaxamount_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][igst_tax_amount]" value="<?php echo $value['igst_tax_amount'];?>" class="form-control igst_tax_amount" readonly></td>
                                        <?php }else if($value['sgst_tax_amount'] != 0){ ?>
                                        <td><input  id="is_igst" type="hidden" name="is_igst" value="0" >
                                            <input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "sgsttaxrate_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][sgst_tax_rate]" value="<?php echo $value['sgst_tax_rate'];?>" class="form-control sgst_tax_rate">
                                        </td>
                                        <td><input id="<?php echo "sgsttaxamount_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][sgst_tax_amount]" value="<?php echo $value['sgst_tax_amount'];?>" class="form-control sgst_tax_amount" readonly></td>
                                        <td><input onkeyup="change_prices('<?php echo $key; ?>')"  id="<?php echo "cgsttaxrate_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][cgst_tax_rate]" value="<?php echo $value['cgst_tax_rate'];?>" class="form-control cgst_tax_rate"></td>
                                        <td><input  id="<?php echo "cgsttaxamount_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][cgst_tax_amount]"  value="<?php echo $value['cgst_tax_amount'];?>" class="form-control cgst_tax_amount" readonly></td>
                                        <?php } else { ?>
                                        <input type="hidden" id="is_igst" name="is_igst" value="2" >
                                        <?php } ?>
                                        <td><input id="<?php echo "totalamount_".$key; ?>" type="text" name="invoice[<?php echo $value['id']; ?>][total_amount]" value="<?php echo $value['total_amount'];?>" class="form-control total_amount" readonly></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="total_quantity"></td>
                                        <td ></td>
                                        <td id="total_taxablevalue" ></td>
                                        <?php if($invoice_breakup[0]['igst_tax_amount'] != 0){ ?>
                                        <td></td>
                                        <td id="total_igst_amount"></td>
                                        <?php } else if($invoice_breakup[0]['sgst_tax_amount'] != 0){ ?>
                                        <td></td>
                                        <td id="total_sgst_amount"></td>
                                        <td></td>
                                        <td id="total_cgst_amount"></td>
                                        <?php }  ?>
                                        <td id="toatl_amount_charge"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <input type="submit" value="submit" class="btn btn-primary btn-lg text-center">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>js/invoice_tag.js"></script>
<script>
    $("#to_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#due_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
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
    control_type_code(0);
    function from_calendar() {
           $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
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
    
    calculate_total();
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
       if(is_igst === '1'){
           var igsttaxrate = Number($("#igsttaxrate_"+id).val());
           var gst_amount = (taxablevalue * igsttaxrate)/100;
           $("#igsttaxamount_"+id).val(gst_amount);
           var igsttaxamount = Number($("#igsttaxamount_"+id).val());
           var total_amount = (taxablevalue + igsttaxamount);
           $("#totalamount_"+id).val(total_amount);
           
       } else if(is_igst === '0'){
            var sgsttaxrate = Number($("#sgsttaxrate_"+id).val());
            var s_gst_amount = (taxablevalue * sgsttaxrate)/100;
            $("#sgsttaxamount_"+id).val(s_gst_amount);
            
            var cgsttaxrate = Number($("#cgsttaxrate_"+id).val());
            var c_gst_amount = (taxablevalue * cgsttaxrate)/100;
            $("#cgsttaxamount_"+id).val(c_gst_amount);
            
            var total_amount = (taxablevalue + s_gst_amount + c_gst_amount);
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
         var total_charge = 0
         var is_igst  = $("#is_igst").val();
        
         $(".quantity").each(function (i) {
    
            var qty = Number($("#qty_"+ i).val());
            var taxable = Number($("#taxablevalue_"+ i).val());
            total_quantity +=qty;
            taxable_value += taxable;
            if(is_igst === '1'){
                igst_amount += Number($("#igsttaxamount_"+i).val());
                
            } else if(is_igst === '0'){
              
                sgst_amount += Number($("#sgsttaxamount_"+i).val());
                cgst_amount += Number($("#cgsttaxamount_"+i).val());
    
            } 
            
            total_charge += Number($("#totalamount_"+i).val());
    
        });
        
        $("#total_quantity").html("<b>"+total_quantity + "</b>");
        $("#total_taxablevalue").html("<b>"+taxable_value.toFixed(2) + "</b>");
       
        if(igst_amount > 0){
            $("#total_igst_amount").html("<b>"+igst_amount.toFixed(2) + "</b>");
        } else if(cgst_amount > 0){
            $("#total_cgst_amount").html("<b>"+cgst_amount.toFixed(2) + "</b>");
            $("#total_sgst_amount").html("<b>"+sgst_amount.toFixed(2) + "</b>");
        }
        $("#toatl_amount_charge").html("<b>"+total_charge.toFixed(2) + "</b>");
    }
    
    get_vertical('<?php echo base_url(); ?>');
    
</script>