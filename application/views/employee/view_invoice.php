<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> Invoice Details </h1>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="#">
                <div class="row">
                    <h4 style="text-align:center"><b>247Around is - <?php if($invoice_details[0]['type_code'] == "A"){ echo "Seller"; }else{ echo "Buyer"; } ?></b></h4>
                    <div class="col-md-12" style="margin-top: 20px;">
                        <table class="table priceList table-striped table-bordered" style="">
                            <tbody>
                            <tr>
                                <td style="width: 185px;"><b>Invoice ID</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['invoice_id']; ?></td>
                                
                                <td style="width: 185px;"><b>Reference Invoice ID</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['reference_invoice_id']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Vendor/Partner Name</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['vendor_partner_name']; ?></td>
                                
                                <td style="width: 185px;"><b>Invoice Date</b></td>
                                <td style="width: 290px;"><?php echo date("jS M, Y", strtotime($invoice_details[0]['invoice_date'])); ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Period</b></td>
                                <td style="width: 290px;"><?php echo date("jS M, Y", strtotime($invoice_details[0]['from_date']))." to ".date("jS M, Y", strtotime($invoice_details[0]['to_date'])); ?></td>
                                
                                <td style="width: 185px;"><b>Due Date</b></td>
                                <td style="width: 290px;"><?php echo date("jS M, Y", strtotime($invoice_details[0]['due_date'])); ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Vertical</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['vertical']; ?></td>
                                
                                <td style="width: 185px;"><b>Category</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['category']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Sub Category</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['sub_category']; ?></td>
                                
                                <td style="width: 185px;"><b>Accounting</b></td>
                                <td style="width: 290px;"><?php if($invoice_details[0]['accounting'] == "1"){ echo "Yes"; }else{ echo "No"; }  ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Type</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['type']; ?></td>
                                
                                <td style="width: 185px;"><b>Type Code</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['type_code']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Number of Booking</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['num_bookings']; ?></td>
                                
                                <td style="width: 185px;"><b>Number of Parts</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['parts_count']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>GST Rate</b></td>
                                <td style="width: 290px;"><?php if (isset($invoice_details[0]['igst_tax_rate'])) {
                                        echo $invoice_details[0]['cgst_tax_rate'] + $invoice_details[0]['sgst_tax_rate'] + $invoice_details[0]['igst_tax_rate'];
                                        } else { echo DEFAULT_TAX_RATE;} ?></td>
                                
                                <td style="width: 185px;"><b>HSN Code</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['hsn_code']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Credit Penalty Booking Count</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['credit_penalty_bookings_count']; ?></td>
                                
                                <td style="width: 185px;"><b>Debit Penalty Booking Count</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['penalty_bookings_count']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Credit Penalty Amount</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['credit_penalty_amount']; ?></td>
                                
                                <td style="width: 185px;"><b>Debit Penalty Amount</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['penalty_amount']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Basic Service Charge</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['total_service_charge']; ?></td>
                                
                                <td style="width: 185px;"><b>Additional Charge</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['total_additional_service_charge']; ?></td>
                            </tr>
                             <tr>
                                <td style="width: 185px;"><b>Upcountry Distance</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['upcountry_distance']; ?></td>
                                
                                <td style="width: 185px;"><b>Upcountry Charges</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['upcountry_price']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Upcountry Booking Count</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['upcountry_booking']; ?></td>
                                
                                <td style="width: 185px;"><b>Parts Cost</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['parts_cost']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Packaging Rate</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['packaging_rate']; ?></td>
                                
                                <td style="width: 185px;"><b>Packaging Quantity</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['packaging_quantity']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Warehouse Storage Charge</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['warehouse_storage_charges']; ?></td>
                                
                                <td style="width: 185px;"><b>Miscellaneous Charge</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['miscellaneous_charges']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Courier Charges</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['courier_charges']; ?></td>
                                
                                <td style="width: 185px;"><b>Settle Amount</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['settle_amount']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Remarks</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['remarks']; ?></td>

                                <td style="width: 185px;"><b>GST Credit Note Remark</b></td>
                                <td style="width: 290px;"><?php echo $invoice_details[0]['gst_credit_note_remark']; ?></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Agent Name</b></td>
                                <td style="width: 290px;"><?php if(isset($agent_name)){ echo $agent_name; } ?></td>
                                <td style="width: 185px;"><b>Main Invoice Excel</b></td>
                                <td style="width: 290px;"><?php $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_file_excel']; ?><a href="<?php echo $src ?>" target="_blank">click Here</a></td>
                            </tr>
                            <tr>
                                <td style="width: 185px;"><b>Detailed Invoice Excel</b></td>
                                <td style="width: 290px;"><?php $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_detailed_excel']; 
                                                    ?><a href="<?php echo $src ?>" target="_blank">click Here</a></td>
                                
                                <td style="width: 185px;"><b>Main Invoice</b></td>
                                <td style="width: 290px;"><?php  $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/invoices-excel/" . $invoice_details[0]['invoice_file_main']; 
                                                         ?><a href="<?php echo $src ?>" target="_blank">click Here</a></td>
                            </tr>
                            
                           </tbody>
                        </table>
                       
                    </div>
                    
                    <?php if(!empty($invoice_breakup)){ ?> 
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
                                            <th class="text-center" colspan="2">IGST </th>
                                            <th class="text-center" colspan="2">SGST </th>
                                            <th class="text-center" colspan="2">CGST </th>
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
                                            <th class="text-center">Rate</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Rate</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_quantity = $total_taxablevalue = $total_igst_amount = $total_sgst_amount = $total_cgst_amount = $toatl_amount_charge = 0;
                                        foreach ($invoice_breakup as $key => $value) {
                                            $total_quantity = $total_quantity + $value['qty'];
                                            if($value['product_or_services'] == PENALTY_DISCOUNT || $value['product_or_services'] == DEBIT_PENALTY){
                                                //We need to subtract values in case of debit peanlty and penalty discount 
                                                $total_taxablevalue = $total_taxablevalue - $value['taxable_value'];
                                                $total_igst_amount = $total_igst_amount - $value['igst_tax_amount'];
                                                $total_sgst_amount = $total_sgst_amount - $value['sgst_tax_amount'];
                                                $total_cgst_amount = $total_cgst_amount - $value['cgst_tax_amount'];
                                                $toatl_amount_charge = $toatl_amount_charge - $value['total_amount'];
                                            }else{
                                                $total_taxablevalue = $total_taxablevalue + $value['taxable_value'];
                                                $total_igst_amount = $total_igst_amount + $value['igst_tax_amount'];
                                                $total_sgst_amount = $total_sgst_amount + $value['sgst_tax_amount'];
                                                $total_cgst_amount = $total_cgst_amount + $value['cgst_tax_amount'];
                                                $toatl_amount_charge = $toatl_amount_charge + $value['total_amount'];
                                            }   
                                        ?>
                                        <tr>
                                            <td><?php echo ($key +1);?></td>
                                            <td style="width:34%">
                                                <?php echo $value['description'];?>
                                            </td>
                                            <td><?php echo $value['product_or_services'];?></td>
                                            <td><?php echo $value['hsn_code'];?></td>
                                            <td><?php echo $value['qty'];?></td>
                                            <td><?php echo $value['rate'];?></td>
                                            <td><?php echo $value['taxable_value'];?></td>
                                            <?php if($value['igst_tax_amount'] != 0){ } else if($value['sgst_tax_amount'] != 0){ } else {?>
                                                <input type="hidden" id="is_igst" name="is_igst" value="2" >
                                            <?php }?>
                                            <td> 
                                                <?php echo $value['igst_tax_rate'];?>
                                            </td>
                                            <td><?php echo $value['igst_tax_amount'];?></td>
                                            <td>
                                                <?php echo $value['sgst_tax_rate'];?>
                                            </td>
                                            <td><?php echo $value['sgst_tax_amount'];?></td>
                                            <td><?php echo $value['cgst_tax_rate'];?></td>
                                            <td><?php echo $value['cgst_tax_amount'];?></td>
                                            <td><?php echo $value['total_amount'];?></td>
                                        </tr>
                                        <?php } ?>
                                         <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="total_quantity"><?php echo $total_quantity; ?></td>
                                            <td ></td>
                                            <td id="total_taxablevalue" ><?php echo $total_taxablevalue; ?></td>
                                            
                                            <td></td>
                                            <td id="total_igst_amount"><?php echo $total_igst_amount; ?></td>
                                            <td></td>
                                            <td id="total_sgst_amount"><?php echo $total_sgst_amount; ?></td>
                                            <td></td>
                                            <td id="total_cgst_amount"><?php echo $total_cgst_amount; ?></td>
                                            
                                            <td id="toatl_amount_charge"><?php echo $invoice_details[0]['total_amount_collected'] - $invoice_details[0]['tcs_amount']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12"></td>
                                            
                                            <td ><?php echo "(+) TCS Rate ".$invoice_details[0]['tcs_rate']. " %"?></td>
                                            <td ><?php echo $invoice_details[0]['tcs_amount']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12"></td>
                                            
                                            <td ><?php echo "(+) TDS Rate ".$invoice_details[0]['tds_rate']. " %"?></td>
                                            <td ><?php echo $invoice_details[0]['tds_amount']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12"></td>
                                            
                                            <td >Final Amount</td>
                                            <td ><?php echo $invoice_details[0]['total_amount_collected'] - $invoice_details[0]['tds_amount']; ?></td>
                                        </tr>
                                       
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    
                    
                </div>
            </form>
        </div>
    </div>
</div>
</div>

