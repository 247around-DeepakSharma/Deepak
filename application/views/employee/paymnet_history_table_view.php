<?php  if ($payment_type !== 'tds' && $payment_type !== BUYBACK && $payment_type !== 'paytm' && $payment_type !== 'advance_voucher') { 
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
                    <th>GST Number</th>
                    <th>Type</th>
                    <th>Type Code</th>
                    <th>Vertical</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>TDS Rate</th>
                    <th>TDS Amount</th>
                    <th>Parts Qty (Pcs)</th>
                    <th>Booking Qty (Pcs)</th>
                    <th>Parts Amount</th>
                    <th>Service Charge Income</th>
                    <th>Total Additional Service Charge</th>
                    <th>Miscellaneous Charges</th>
                    <th>Warehouse Charges</th>
                    <th>Conveyance Charge Income</th>
                    <th>Courier Charges Income</th>
                    <th>Discount Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $total_sc = $total_pc = $total_asc = 
                            $total_up_cc = $total_courier_charges = 
                            $grand_total_amount_collected = $grand_amount_collected = $t_parts_count = $num_bookings = $cgst = $sgst = $igst = $discount_amt = $tds_amount = $total_msc = $total_warehouse = 0;
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
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                            <td><?php echo $value['vertical']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['sub_category']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_amount_collected'] - $value['tds_amount']); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
                            <td><?php echo sprintf("%.2f", $value['amount_collected_paid'] - $value['tds_amount']); $grand_amount_collected += ($value['amount_collected_paid'] - $value['tds_amount']);?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['tds_rate']);?></td>
                            <td><?php echo sprintf("%.2f", $value['tds_amount']); $tds_amount += $value['tds_amount'];?></td>
                            <td><?php echo $value['parts_count'];  $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo $value['num_bookings']; $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo sprintf("%.2f", $value['parts_cost']); $total_pc += $value['parts_cost'];?></td>
                            <td><?php echo sprintf("%.2f", $value['total_service_charge']); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_additional_service_charge']); $total_asc += $value['total_additional_service_charge']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['miscellaneous_charges']); $total_msc += $value['miscellaneous_charges']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['warehouse_storage_charges']); $total_warehouse += $value['warehouse_storage_charges']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['upcountry_price']); $total_up_cc += $value['upcountry_price'];?></td>
                            <td><?php echo sprintf("%.2f", $value['courier_charges']); $total_courier_charges += $value['courier_charges']; ?></td>
                            <td><?php echo sprintf("%.2f", ($value['credit_penalty_amount'] - $value['penalty_amount'])); $discount_amt += ($value['credit_penalty_amount'] - $value['penalty_amount']);?></td>
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
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $grand_total_amount_collected); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $grand_amount_collected); ?></b></td>
<!--                            <td><b><?php //echo $num_bookings; ?></b></td>-->
<!--                            <td><b><?php //echo $debit_penalty; ?></b></td>
                            <td><b><?php //echo $credit_penalty; ?></b></td>-->
                            <td><b><?php echo round($cgst,0); ?></b></td>
                            <td><b><?php echo round($sgst,0); ?></b></td>
                            <td><b><?php echo round($igst,0); ?></b></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $tds_amount);?></b></td>
                            <td><b><?php echo $t_parts_count; ?></b></td>
                            <td><b><?php echo $num_bookings; ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_pc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_sc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_asc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_msc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_warehouse); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_up_cc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_courier_charges); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $discount_amt); ?></b></td>
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
                    <th>Reference Invoices</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Service Charges</th>
                    <th>Parts Qty</th>
                    <th>Booking Qty</th>
                    <th>Parts Amt</th>
<!--                    <th>From Date</th>
                    <th>To Date</th>-->
                    <th>GST Number</th>
                    <th>GST Registration Type</th>
                    <th>Type</th>
                    <th>Type Code</th>
                    <th>Vertical</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>GST Rate</th>
                    <th>TDS Rate</th>
                    <th>TDS Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $t_service_charges = $t_parts_cost = $t_parts_count = $t_ac = $t_acp = $num_bookings = $cgst = $sgst = $igst = $tds_amount = 0;
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
                            <td><?php echo $value['from_date']; ?></td>
                            <td><?php echo $value['to_date']; ?></td>
<!--                            <td><?php //echo round($value['penalty_amount'],0); $debit_penalty += $value['penalty_amount'];?></td>
                            <td><?php //echo round($value['credit_penalty_amount'],0); $credit_penalty += $value['credit_penalty_amount'];?></td>-->
<!--                            <td><?php //echo round($value['around_royalty'],0); $t_ar += $value['around_royalty'];?></td>-->
                            <td><?php echo sprintf("%.2f", $value['total_service_charge']); $t_service_charges += $value['total_service_charge'];?></td>
                            <td><?php echo round($value['parts_count'],0); $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo sprintf("%.2f", $value['parts_cost']); $t_parts_cost += $value['parts_cost'];?></td>
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['gst_reg_type']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                            <td><?php echo $value['vertical']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['sub_category']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_amount_collected']); $t_ac += $value['total_amount_collected']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['amount_collected_paid']); $t_acp += $value['amount_collected_paid']; ?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['tds_rate']);?></td>
                            <td><?php echo sprintf("%.2f", $value['tds_amount']); $tds_amount += $value['tds_amount'];?></td>
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
<!--                            <td><b><?php //echo $debit_penalty; ?></b></td>
                            <td><b><?php //echo $credit_penalty; ?></b></td>-->
<!--                            <td><b><?php //echo round($t_ar,0); ?></b></td>-->
                            <td><b><?php echo sprintf("%.2f", $t_service_charges); ?></b></td>
                            <td><b><?php echo $t_parts_count; ?></b></td>
                            <td><b><?php echo $num_bookings; ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $t_parts_cost); ?></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $t_ac); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $t_acp); ?></b></td>
                            <td><b><?php echo round($cgst,0); ?></b></td> 
                            <td><b><?php echo round($sgst,0); ?></b></td> 
                            <td><b><?php echo round($igst,0); ?></b></td> 
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $tds_amount); ?></b></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } ?>

<?php } else if ($payment_type == 'tds') { ?> 
    <?php if ($partner_vendor == 'vendor') { ?>
            <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Invoice No</th>
                        <th>Company Name</th>
                        <th>Public Name</th>
                        <th>Address</th>
                        <th>State</th>
                        <th>Invoice Date</th>
                        <th>Reference Invoices</th>
                        <th>Name on PAN</th>
                        <th>PAN</th>
                        <th>Owner Name</th>
                        <th>GST Number</th>
                        <th>GST Registration Type</th>
                        <th>Type</th>
                        <th>Type Code</th>
                        <th>Vertical</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Net Amount</th>
                        <th>CGST Tax Amount</th>
                        <th>SGST Tax Amount</th>
                        <th>IGST Tax Amount</th>
                        <th>GST Rate</th>
                        <th>TDS Rate</th>
                        <th>TDS Amount</th>
                        <th>Parts Qty</th>
                        <th>Booking Qty</th>
                        <th>Parts Amt</th>
                        <th>Service Charges</th>
                        <th>Total Additional Service Charge</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (isset($invoice_data)) { ?> 
                <?php
                $sn = 1; $t_parts_count = $num_bookings = $total_sc = $total_asc = $total_pc = $total_st = $grand_tac = $total_net_amount = $total_tds_amount = $amount_collected_paid = $cgst = $sgst = $igst = 0;
                foreach ($invoice_data as $key => $value) {
                    ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['invoice_id']; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['name']; ?></td>
                                <td><?php echo $value['address']; ?></td>
                                <td><?php echo $value['state']; ?></td>
                                <td><?php echo $value['invoice_date']; ?></td>
                                <td><?php echo $value['reference_invoice_id']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo $value['owner_name']; ?></td>
                                <td><?php echo $value['gst_no']; ?></td>
                                <td><?php echo $value['gst_taxpayer_type']; ?></td>
                                <td><?php echo $value['type']; ?></td>
                                <td><?php echo $value['type_code']; ?></td>
                                <td><?php echo $value['vertical']; ?></td>
                                <td><?php echo $value['category']; ?></td>
                                <td><?php echo $value['sub_category']; ?></td>
                                <td><?php echo sprintf("%.2f", $value['total_amount_collected']); $grand_tac += $value['total_amount_collected']; ?></td>
                                <td><?php echo sprintf("%.2f", $value['amount_collected_paid']); $amount_collected_paid += $value['amount_collected_paid']; ?></td>
                                <td><?php echo round($value['net_amount'],0); $total_net_amount += $value['net_amount']; ?></td>
                                <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                                <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                                <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                                <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                                <td><?php echo round($value['tds_rate'],0);  ?></td>
                                <td><?php echo sprintf("%.2f", $value['tds_amount']); $total_tds_amount += $value['tds_amount']; ?></td>
                                <td><?php echo $value['parts_count']; $t_parts_count += $value['parts_count'];?></td>
                                <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                                <td><?php echo sprintf("%.2f", $value['parts_cost']); $total_pc += $value['parts_cost']; ?></td>
                                <td><?php echo sprintf("%.2f", $value['total_service_charge']); $total_sc += $value['total_service_charge']; ?></td>
                                <td><?php echo sprintf("%.2f", $value['total_additional_service_charge']); $total_asc += $value['total_additional_service_charge']; ?></td>
                            </tr>
                    <?php $sn++;
                }
                if ($report_type == 'draft') { ?>
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
                                <td></td>
                                <td><b><?php echo sprintf("%.2f", $grand_tac); ?></b></td>
                                <td><b><?php echo sprintf("%.2f", $amount_collected_paid); ?></b></td>
                                <td><b><?php echo round($total_net_amount,0); ?></b></td>
                                <td><b><?php echo round($cgst,0); ?></b></td>
                                <td><b><?php echo round($sgst,0); ?></b></td>
                                <td><b><?php echo round($igst,0); ?></b></td>
                                <td></td>
                                <td></td>
                                <td><b><?php echo sprintf("%.2f", $total_tds_amount); ?></b></td>
                                <td><b><?php echo $t_parts_count; ?></b></td>
                                <td><b><?php echo $num_bookings; ?></b></td>
                                <td><b><?php echo sprintf("%.2f", $total_pc); ?></b></td>
                                <td><b><?php echo sprintf("%.2f", $total_sc); ?></b></td>
                                <td><b><?php echo sprintf("%.2f", $total_asc); ?></b></td>
                            </tr>
            <?php } } ?>
                </tbody>
            </table>
        <?php // } else if ($report_type == 'final') { ?> 
<!--            <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Company Name</th>
                        <th>Public Name</th>
                        <th>Type</th>
                        <th>Name on PAN</th>
                        <th>PAN</th>
                        <th>Service Charge</th>
                        <th>TDS Amount</th>
                        <th>TDS Rate</th>
                        <th>Vertical</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                    </tr>
                </thead>
                <tbody>-->
            <?php // if (isset($invoice_data)) { ?> 
                <?php
//                $sn = 1;
//                foreach ($invoice_data as $key => $value) {
                    ?>
<!--                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['name']; ?></td>
                                <td><?php echo $value['company_type']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo round($value['tds_taxable_amount'],0); ?></td>
                                <td><?php echo round($value['tds_amount'],0); ?></td>
                                <td><?php echo round($value['tds_rate'],0); ?></td>
                                <td><?php echo $value['vertical']; ?></td>
                                <td><?php echo $value['category']; ?></td>
                                <td><?php echo $value['sub_category']; ?></td>
                            </tr>-->
                    <?php // $sn++;
//                }
                ?>
            <?php // } ?>
<!--                </tbody>
            </table>-->
        <?php // }
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
        <th>Reference Invoices</th>
        <th>GST Number</th>
        <th>GST Registration Type</th>
        <th>Type</th>
        <th>Type Code</th>
        <th>Vertical</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>Total Amount</th>
        <th>Amount Paid</th>
        <th>GST amount</th>
        <th>GST Rate</th>
        <th>TDS Rate</th>
        <th>TDS Amount</th>
        <th>Parts Qty (Pcs)</th>
        <th>Booking Qty (Pcs)</th>
        <th>Parts Amount</th>
        <th>Service Charge Income</th>
        <th>Total Additional Service Charge</th>
        <th>Conveyance Charge Income</th>
        <th>Courier Charges Income</th>
        <th>Discount Paid</th>
    </thead>
    <tbody>
            <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $t_parts_count = $num_bookings = $total_sc = $total_pc = $total_asc = 
                            $total_up_cc = $total_courier_charges = 
                            $grand_total_amount_collected = $grand_amount_collected_paid = $num_bookings = $tax = $discount_amt = $total_tds_amount = 0;
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
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['gst_registration_type']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                            <td><?php echo $value['vertical']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['sub_category']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_amount_collected'] - $value['tds_amount']); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
                            <td><?php echo sprintf("%.2f", $value['amount_collected_paid'] - $value['tds_amount']); $grand_amount_collected_paid += ($value['amount_collected_paid'] - $value['tds_amount']);?></td>
                            <td><?php echo round($value['tax']); $tax += ($value['tax'])?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['tds_rate'],0);  ?></td>
                            <td><?php echo sprintf("%.2f", $value['tds_amount']); $total_tds_amount += $value['tds_amount']; ?></td>
                            <td><?php echo $value['parts_count']; $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo sprintf("%.2f", $value['parts_cost']); $total_pc += $value['parts_cost'];?></td>
                            <td><?php echo sprintf("%.2f", $value['total_service_charge']); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_additional_service_charge']); $total_asc += $value['total_additional_service_charge']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['upcountry_price']); $total_up_cc += $value['upcountry_price'];?></td>
                            <td><?php echo sprintf("%.2f", $value['courier_charges']); $total_courier_charges += $value['courier_charges']; ?></td>
                            <td><?php echo sprintf("%.2f", ($value['credit_penalty_amount'] - $value['penalty_amount'])) ; $discount_amt += ($value['credit_penalty_amount'] - $value['penalty_amount']);?></td>
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
                            <td><b><?php echo sprintf("%.2f", $grand_total_amount_collected); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $grand_amount_collected_paid); ?></b></td>
                            <td><b><?php echo round($tax,0);?></b></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $total_tds_amount); ?></b></td>
                            <td><b><?php echo $t_parts_count; ?></td>
                            <td><b><?php echo round($num_bookings,0); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_pc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_sc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_asc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_up_cc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_courier_charges); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $discount_amt);?></b></td>
                        </tr>
        <?php } ?>
        </tbody>
</table>
<?php } else if($payment_type == 'paytm'){  ?>
             <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>Invoice Date</th>
                    <th>Reference Invoices</th>
                    <th>GST Number</th>
                    <th>Type</th>
                    <th>Type Code</th>
                    <th>Vertical</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Total</th>
                    <th>Amount Paid</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>TDS Rate</th>
                    <th>TDS Amount</th>
                    <th>Parts Qty (Pcs)</th>
                    <th>Booking Qty (Pcs)</th>
                    <th>Service Charge Income</th>
                    <th>Total Additional Service Charge</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $t_parts_count = $num_bookings = $total_sc = $total_asc = $grand_total_amount_collected = $grand_amount_collected_paid = $cgst = $sgst = $igst = $tds_amount = 0;
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
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                            <td><?php echo $value['vertical']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['sub_category']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_amount_collected'] - $value['tds_amount']); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
                            <td><?php echo sprintf("%.2f", $value['amount_collected_paid'] - $value['tds_amount']); $grand_amount_collected_paid += ($value['amount_collected_paid'] - $value['tds_amount']);?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['tds_rate']);?></td>
                            <td><?php echo sprintf("%.2f", $value['tds_amount']); $tds_amount += $value['tds_amount'];?></td>
                            <td><?php echo $value['parts_count']; $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo sprintf("%.2f", $value['total_service_charge']); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_additional_service_charge']); $total_asc += $value['total_additional_service_charge']; ?></td>
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
                            <td><b><?php echo sprintf("%.2f", $grand_total_amount_collected); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $grand_amount_collected_paid); ?></b></td>
                            <td><b><?php echo round($cgst,0); ?></b></td>
                            <td><b><?php echo round($sgst,0); ?></b></td>
                            <td><b><?php echo round($igst,0); ?></b></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo sprintf("%.2f", $tds_amount); ?></b></td>
                            <td><b><?php echo $t_parts_count; ?></td>
                            <td><b><?php echo round($num_bookings,0); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_sc); ?></b></td>
                            <td><b><?php echo sprintf("%.2f", $total_asc); ?></b></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
 <?php  }else if($payment_type = "advance_voucher"){ ?> 
     
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Advance Voucher Invoice</th>
                    <th>Partner Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>Credit/Debit</th>
                    <th>Invoice Id</th>
                    <th>Invoice Date</th>
                    <th>Reference Invoices</th>
                    <th>GST Number</th>
                    <th>Type</th>
                    <th>Type Code</th>
                    <th>Vertical</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>TDS Rate</th>
                    <th>TDS Amount</th>
                    <th>Parts Qty (Pcs)</th>
                    <th>Booking Qty (Pcs)</th>
                    <th>Service Charge</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1;$grand_total_amount_collected = $grand_amount_collected_paid = $cgst = $sgst = $igst = $tds_amount = $t_parts_count = $num_bookings = $total_sc = 0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['advance_voucher']; ?></td>
                            <td><?php echo $value['partner_name']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['credit_debit']; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['reference_invoice_id']; ?></td>
                            <td><?php echo $value['gst_number']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                            <td><?php echo $value['type_code']; ?></td>
                            <td><?php echo $value['vertical']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['sub_category']; ?></td>
                            <td><?php echo sprintf("%.2f", $value['total_amount_collected']); $grand_total_amount_collected += $value['total_amount_collected'];?></td>
                            <td><?php echo sprintf("%.2f", $value['amount_collected_paid']); $grand_amount_collected_paid += $value['amount_collected_paid'];?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); $cgst += $value['cgst_tax_amount']; ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); $sgst += $value['sgst_tax_amount']; ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); $igst += $value['igst_tax_amount']; ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['tds_rate']);?></td>
                            <td><?php echo sprintf("%.2f", $value['tds_amount']); $tds_amount += $value['tds_amount'];?></td>
                            <td><?php echo $value['parts_count']; $t_parts_count += $value['parts_count'];?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo sprintf("%.2f", ($value['total_service_charge'] + $value['total_additional_service_charge'])); $total_sc += ($value['total_service_charge'] + $value['total_additional_service_charge']); ?></td>
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
                        <td><b><?php echo sprintf("%.2f", $grand_total_amount_collected); ?></b></td>
                        <td><b><?php echo sprintf("%.2f", $grand_amount_collected_paid); ?></b></td>
                        <td><b><?php echo round($cgst,0); ?></b></td>
                        <td><b><?php echo round($sgst,0); ?></b></td>
                        <td><b><?php echo round($igst,0); ?></b></td>
                        <td></td>
                        <td></td>
                        <td><b><?php echo sprintf("%.2f", $tds_amount); ?></b></td>
                        <td><b><?php echo $t_parts_count; ?></b></td>
                        <td><b><?php echo $num_bookings; ?></b></td>
                        <td><b><?php echo sprintf("%.2f", $total_sc); ?></b></td>
                    </tr>    
        <?php } ?>
            </tbody>
        </table>
<?php } ?>
     
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_details_table" style="display:none;">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice Id</th>
                    <th>Service/Product</th>
                    <th>Rate</th>
                    <th>Qty</th>
                    <th>Taxable Value</th>
                    <th>CGST Amount</th>
                    <th>SGST Amount</th>
                    <th>IGST Amount</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_details_data)) { ?> 
                    <?php
                    $sn = 1;
                    $total_qty = $total_taxable = $total_cgst = $total_sgst = $total_igst = $total_amount = 0;
                    foreach ($invoice_details_data as $key => $invoice_details) {
                        foreach ($invoice_details as $invoice) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $invoice['invoice_id']; ?></td>
                            <td><?php echo $invoice['product_or_services']; ?></td>
                            <td><?php echo round($invoice['rate']); ?></td>
                            <td><?php echo $invoice['qty'];$total_qty += $invoice['qty']; ?></td>
                            <td><?php echo sprintf("%.2f", $invoice['taxable_value']);$total_taxable += $invoice['taxable_value']; ?></td>
                            <td><?php echo round($invoice['cgst_tax_amount'],0);$total_cgst += $invoice['cgst_tax_amount']; ?></td>
                            <td><?php echo round($invoice['sgst_tax_amount'],0);$total_sgst += $invoice['sgst_tax_amount']; ?></td>
                            <td><?php echo round($invoice['igst_tax_amount'],0);$total_igst += $invoice['igst_tax_amount']; ?></td>
                            <td><?php echo sprintf("%.2f", $invoice['total_amount']);$total_amount += $invoice['total_amount']; ?></td>
                        </tr>
                        <?php $sn++;
                        }
                    }
                    ?>
                    <tr>
                        <td><b>Total</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b><?php echo $total_qty; ?></b></td>
                        <td><b><?php echo sprintf("%.2f", $total_taxable); ?></b></td>
                        <td><b><?php echo round($total_cgst,0); ?></b></td>
                        <td><b><?php echo round($total_sgst,0); ?></b></td>
                        <td><b><?php echo round($total_igst,0); ?></b></td>
                        <td><b><?php echo sprintf("%.2f", $total_amount); ?></b></td>
                    </tr>    
        <?php } ?>
            </tbody>
        </table>