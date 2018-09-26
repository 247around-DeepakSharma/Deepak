<?php  if ($payment_type !== 'tds' && $payment_type !== BUYBACK) { 
    $flag =0;
    if($payment_type == "B"){
        $flag = 1;
        
    } else if($payment_type == "A"){
        if($partner_vendor == "vendor"){
            $flag = 0;
        } else if($partner_vendor == "partner"){
            $flag = 1;
        }
    }
    ?>
    <?php if ($flag == 1 ) { ?> 
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th> S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>Invoice Date</th>
                    <th>Reference Invoices</th>
<!--                    <th>From Date</th>
                    <th>To Date</th>-->
                    <th>Parts Qty (Pcs)</th>
                    <th>Parts Amount</th>
                    <th>Service Charge Income</th>
                    <th>Total Additional Service Charge</th>
                    <th>Conveyance Charge Income</th>
                    <th>Courier Charges Income</th>
<!--                    <th>Number Of Bookings</th>-->
<!--                    <th>Debit Penalty</th>
                    <th>Credit Penalty</th>-->
                    <th>Discount Paid</th>
                    <th>TDS Amount</th>
                    <th>TDS Rate</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>Total</th>
                    <th>GST Number</th>
                    <th>Category</th>
                    <th>Type Code</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $total_sc = $total_pc = $total_asc = $total_st = 
                            $total_vat = $total_up_cc = $total_courier_charges = 
                            $grand_total_amount_collected = $num_bookings = $debit_penalty = $credit_penalty = $cgst = $sgst = $igst = $discount_amt = $tds_amount = 0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['reference_invoice_id']; ?></td>
<!--                            <td><?php //echo $value['from_date']; ?></td>
                            <td><?php //echo $value['to_date']; ?></td>-->
                            <td><?php echo $value['parts_count']; ?></td>
                            <td><?php echo round($value['parts_cost'],0); $total_pc += $value['parts_cost'];?></td>
                            <td><?php echo round($value['total_service_charge'],0); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo round($value['total_additional_service_charge'],0); $total_asc += $value['total_additional_service_charge']; ?></td>
                            <td><?php echo round($value['upcountry_price'],0); $total_up_cc += $value['upcountry_price'];?></td>
                            <td><?php echo round($value['courier_charges'],0); $total_courier_charges += $value['courier_charges']; ?></td>
<!--                            <td><?php //echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>-->
<!--                            <td><?php //echo round($value['penalty_amount'],0); $debit_penalty += $value['penalty_amount'];?></td>
                            <td><?php //echo round($value['credit_penalty_amount'],0); $credit_penalty += $value['credit_penalty_amount'];?></td>-->
                            <td><?php echo round(($value['credit_penalty_amount'] - $value['penalty_amount']),0) ; $discount_amt += ($value['credit_penalty_amount'] - $value['penalty_amount']);?></td>
                            <td><?php echo round($value['tds_amount']); $tds_amount += $value['tds_amount'];?></td>
                            <td><?php echo round($value['tds_rate']);?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['total_amount_collected'] - $value['tds_amount'],0); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                        </tr>
                        <?php $sn++;
                    }
                    ?>
                        <tr>
                            <td><b>Total</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
<!--                            <td></td>
                            <td></td>-->
                            <td></td>
                            <td><b><?php echo round($total_pc,0); ?></b></td>
                            <td><b><?php echo round($total_sc,0); ?></b></td>
                            <td><b><?php echo round($total_asc,0); ?></b></td>
                            <td><b><?php echo round($total_up_cc,0); ?></b></td>
                            <td><b><?php echo round($total_courier_charges,0); ?></b></td>
<!--                            <td><b><?php //echo $num_bookings; ?></b></td>-->
<!--                            <td><b><?php //echo $debit_penalty; ?></b></td>
                            <td><b><?php //echo $credit_penalty; ?></b></td>-->
                            <td><b><?php echo round($discount_amt,0);?></b></td>
                            <td><b><?php echo round($tds_amount);?></b></td>
                            <td></td>
                            <td><b><?php echo round($cgst,0); ?></b></td>
                            <td><b><?php echo round($sgst,0); ?></b></td>
                            <td><b><?php echo round($igst,0); ?></b></td>
                            <td></td>
                            <td><b><?php echo round($grand_total_amount_collected,0); ?></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } else if ($flag == 0 ) { ?> 
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th> S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>Invoice Date</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Number Of Bookings</th>
<!--                    <th>Debit Penalty</th>
                    <th>Credit Penalty</th>-->
               <!-- <th>Around Royalty</th>-->
                    <th>Service Charges</th>
                    <th>Parts Qty</th>
                    <th>Parts Amt</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>GST Rate</th>
                    <th>GST Number</th>
                    <th>GST Registration Type</th>
                    <th>Total Amount Collected</th>
                    <th>Reference Invoices</th>
                    <th>Category</th>
                    <th>Type Code</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $t_service_charges = $t_parts_cost = $t_parts_count = $t_ac = $t_sc = $num_bookings = $debit_penalty = $credit_penalty = $cgst = $sgst = $igst =0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['from_date']; ?></td>
                            <td><?php echo $value['to_date']; ?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
<!--                            <td><?php //echo round($value['penalty_amount'],0); $debit_penalty += $value['penalty_amount'];?></td>
                            <td><?php //echo round($value['credit_penalty_amount'],0); $credit_penalty += $value['credit_penalty_amount'];?></td>-->
<!--                            <td><?php //echo round($value['around_royalty'],0); $t_ar += $value['around_royalty'];?></td>-->
                            <td><?php echo round($value['total_service_charge'],0); $t_service_charges += $value['total_service_charge'];?></td>
                            <td><?php echo round($value['parts_count'],0); $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo round($value['parts_cost'],0); $t_parts_cost += $value['parts_cost'];?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['gst_reg_type']; ?></td>
                            <td><?php echo round($value['total_amount_collected'],0); $t_ac += $value['total_amount_collected']; ?></td>
                            <td><?php echo $value['reference_invoice_id']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                        </tr>
                        <?php $sn++;
                    }
                    ?>
                        <tr>
                            <td><b>Total</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo $num_bookings; ?></b></td>
<!--                            <td><b><?php //echo $debit_penalty; ?></b></td>
                            <td><b><?php //echo $credit_penalty; ?></b></td>-->
<!--                            <td><b><?php echo round($t_ar,0); ?></b></td>-->
                            <td><b><?php echo round($t_service_charges,0); ?></b></td>
                            <td><b><?php echo $t_parts_count; ?></b></td>
                             <td><b><?php echo $t_parts_cost; ?></b></td>
                            <td><b><?php echo $cgst; ?></b></td> 
                            <td><b><?php echo $sgst; ?></b></td> 
                            <td><b><?php echo $igst; ?></b></td> 
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo round($t_ac,0); ?></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } ?>

<?php } else if ($payment_type == 'tds') { ?> 
    <?php if ($partner_vendor == 'vendor') { ?>
        <?php if ($report_type == 'draft') { ?>
            <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Company Name</th>
                        <th>Public Name</th>
                        <th>Address</th>
                        <th>State</th>
                        <th>Type</th>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>Category</th>
                        <th>Type Code</th>
                        <th>Reference Invoices</th>
                        <th>Name on PAN</th>
                        <th>PAN</th>
                        <th>GST Number</th>
                        <th>GST Registration Type</th>
                        <th>Owner Name</th>
                        <th>Parts Qty</th>
                        <th>Parts Amt</th>
                        <th>Service Charges</th>
                        <th>Total Additional Service Charge</th>
                        <th>Total Amount</th>
                        <th>Net Amount</th>
                        <th>TDS Amount</th>
                        <th>TDS Rate</th>
                        <th>Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (isset($invoice_data)) { ?> 
                <?php
                $sn = 1; $total_sc = $total_asc = $total_pc = $total_st = $grand_tac = $total_net_amount = $total_tds_amount = $amount_collected_paid = 0;
                foreach ($invoice_data as $key => $value) {
                    ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['name']; ?></td>
                                <td><?php echo $value['address']; ?></td>
                                <td><?php echo $value['state']; ?></td>
                                <td><?php echo $value['company_type']; ?></td>
                                <td><?php echo $value['invoice_id']; ?></td>
                                <td><?php echo $value['invoice_date']; ?></td>
                                <td><?php echo $value['type']; ?></td>
                                <td><?php echo $value['type_code']; ?></td>
                                <td><?php echo $value['reference_invoice_id']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo $value['gst_no']; ?></td>
                                <td><?php echo $value['gst_taxpayer_type']; ?></td>
                                <td><?php echo $value['owner_name']; ?></td>
                                <td><?php echo $value['parts_count']; ?></td>
                                <td><?php echo round($value['parts_cost'],0); $total_pc += $value['parts_cost']; ?></td>
                                <td><?php echo round($value['total_service_charge'],0); $total_sc += $value['total_service_charge']; ?></td>
                                <td><?php echo round($value['total_additional_service_charge'],0); $total_asc += $value['total_additional_service_charge']; ?></td>
                                <td><?php echo round($value['total_amount_collected'],0); $grand_tac += $value['total_amount_collected']; ?></td>
                                <td><?php echo round($value['net_amount'],0); $total_net_amount += $value['net_amount']; ?></td>
                                <td><?php echo round($value['tds_amount'],0); $total_tds_amount += $value['tds_amount']; ?></td>
                                <td><?php echo round($value['tds_rate'],0);  ?></td>
                                <td><?php echo round($value['amount_collected_paid'],0); $amount_collected_paid += $value['amount_collected_paid']; ?></td>
                            </tr>
                    <?php $sn++;
                }
                ?>
                            <tr>
                                <td><b>Total</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?php echo $total_pc; ?></td>
                                <td><b><?php echo round($total_sc,0); ?></b></td>
                                <td><b><?php echo round($total_asc,0); ?></b></td>
                                <td><b><?php echo round($grand_tac,0); ?></b></td>
                                <td><b><?php echo round($total_net_amount,0); ?></b></td>
                                <td><b><?php echo round($total_tds_amount,0); ?></b></td>
                                <td></td>
                                <td><b><?php echo round($amount_collected_paid,0); ?></b></td>
                            </tr>
            <?php } ?>
                </tbody>
            </table>
        <?php } else if ($report_type == 'final') { ?> 
            <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Company Name</th>
                        <th>Public Name</th>
                        <th>Type</th>
                        <th>Name on PAN</th>
                        <th>PAN</th>
                        <th>TDS Amount</th>
                        <th>TDS Rate</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (isset($invoice_data)) { ?> 
                <?php
                $sn = 1;
                foreach ($invoice_data as $key => $value) {
                    ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['name']; ?></td>
                                <td><?php echo $value['company_type']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo round($value['tds_amount'],0); ?></td>
                                <td><?php echo round($value['tds_rate'],0); ?></td>
                            </tr>
                    <?php $sn++;
                }
                ?>
            <?php } ?>
                </tbody>
            </table>
        <?php }
    } ?>
<?php }else if ($payment_type == 'buyback') {?>
<table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
    <thead>
        <th>S.No.</th>
        <th>Invoice No</th>
        <th>Company Name</th>
        <th>Address</th>
        <th>State</th>
        <th>Invoice Date</th>
        <th>Parts Qty (Pcs)</th>
        <th>Parts Amount</th>
        <th>Number of Bookings</th>
        <th>Service Charge Income</th>
        <th>Total Additional Service Charge</th>
        <th>Conveyance Charge Income</th>
        <th>Courier Charges Income</th>
        <th>Discount Paid</th>
        <th>GST amount</th>
        <th>Total</th>
        <th>GST Number</th>
        <th>GST Registration Type</th>
        <th>Category</th>
        <th>Type Code</th>
    </thead>
    <tbody>
            <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $total_sc = $total_pc = $total_asc = $total_st = 
                            $total_vat = $total_up_cc = $total_courier_charges = 
                            $grand_total_amount_collected = $num_bookings = $debit_penalty = $credit_penalty = $tax= $discount_amt = 0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['parts_count']; ?></td>
                            <td><?php echo round($value['parts_cost'],0); $total_pc += $value['parts_cost'];?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo round($value['total_service_charge'],0); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo round($value['total_additional_service_charge'],0); $total_asc += $value['total_additional_service_charge']; ?></td>
                            <td><?php echo round($value['upcountry_price'],0); $total_up_cc += $value['upcountry_price'];?></td>
                            <td><?php echo round($value['courier_charges'],0); $total_courier_charges += $value['courier_charges']; ?></td>
                            <td><?php echo round(($value['credit_penalty_amount'] - $value['penalty_amount']),0) ; $discount_amt += ($value['credit_penalty_amount'] - $value['penalty_amount']);?></td>
                            <td><?php echo round($value['tax']); $tax += ($value['tax'])?></td>
                            <td><?php echo round($value['total_amount_collected'] - $value['tds_amount'],0); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['gst_registration_type']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                        </tr>
                        <?php $sn++;
                    }
                    ?>
                        <tr>
                            <td><b>Total</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo round($total_pc,0); ?></b></td>
                            <td><b><?php echo round($num_bookings,0); ?></b></td>
                            <td><b><?php echo round($total_sc,0); ?></b></td>
                            <td><b><?php echo round($total_asc,0); ?></b></td>
                            <td><b><?php echo round($total_up_cc,0); ?></b></td>
                            <td><b><?php echo round($total_courier_charges,0); ?></b></td>
                            <td><?php echo round($discount_amt,0);?></td>
                            <td><?php echo round($tax,0);?></td>
                            <td><b><?php echo round($grand_total_amount_collected,0); ?></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
        <?php } ?>
        </tbody>
</table>
<?php } ?>