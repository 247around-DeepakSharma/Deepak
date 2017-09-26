<?php $flag =0; if ($payment_type != 'tds') { 
    
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
                    <th>State</th>
                    <th>Invoice Date</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Total Service Charge</th>
                    <th>Total Additional Service Charge</th>
                    <th>Service Tax</th>
                    <th>Parts</th>
                    <th>VAT</th>
                    <th>Conveyance Charge</th>
                    <th>Courier</th>
                    <th>Number Of Bookings</th>
                    <th>Debit Penalty</th>
                    <th>Credit Penalty</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>Vat %</th>
                    <th>Total Amount Collected</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $total_sc = $total_pc = $total_asc = $total_st = 
                            $total_vat = $total_up_cc = $total_courier_charges = 
                            $grand_total_amount_collected = $num_bookings = $debit_penalty = $credit_penalty = 0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['from_date']; ?></td>
                            <td><?php echo $value['to_date']; ?></td>
                            <td><?php echo round($value['total_service_charge'],0); $total_sc +=$value['total_service_charge']; ?></td>
                            <td><?php echo round($value['total_additional_service_charge'],0); $total_asc += $value['total_additional_service_charge']; ?></td>
                            <td><?php echo round($value['service_tax'],0); $total_st +=$value['service_tax']; ?></td>
                            <td><?php echo round($value['parts_cost'],0); $total_pc += $value['parts_cost'];?></td>
                            <td><?php echo round($value['vat'],0); $total_vat += $value['vat']; ?></td>
                            <td><?php echo round($value['upcountry_price'],0); $total_up_cc += $value['upcountry_price'];?></td>
                            <td><?php echo round($value['courier_charges'],0); $total_courier_charges += $value['courier_charges']; ?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo round($value['penalty_amount'],0); $debit_penalty += $value['penalty_amount'];?></td>
                            <td><?php echo round($value['credit_penalty_amount'],0); $credit_penalty += $value['credit_penalty_amount'];?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php if($value['parts_cost'] != 0) { echo round(($value['vat']*100)/$value['parts_cost'],0); } ?></td>
                             <td><?php echo round($value['total_amount_collected'] - $value['tds_amount'],0); $grand_total_amount_collected += ($value['total_amount_collected'] - $value['tds_amount']);?></td>
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
                            <td><b><?php echo round($total_sc,0); ?></b></td>
                            <td><b><?php echo round($total_asc,0); ?></b></td>
                            <td><b><?php echo round($total_st,0); ?></b></td>
                            <td><b><?php echo round($total_pc,0); ?></b></td>
                            <td><b><?php echo round($total_vat,0); ?></b></td>
                            <td><b><?php echo round($total_up_cc,0); ?></b></td>
                            <td><b><?php echo round($total_courier_charges,0); ?></b></td>
                            <td><?php echo $num_bookings; ?></td>
                            <td><?php echo $debit_penalty; ?></td>
                            <td><?php echo $credit_penalty; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b><?php echo round($grand_total_amount_collected,0); ?></b></td>
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
                    <th>State</th>
                    <th>Invoice Date</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Number Of Bookings</th>
                    <th>Debit Penalty</th>
                    <th>Credit Penalty</th>
                    <th>Around Royalty</th>
                    <th>Service Tax</th>
                    <th>CGST Tax Amount</th>
                    <th>SGST Tax Amount</th>
                    <th>IGST Tax Amount</th>
                    <th>GST Rate</th>
                    <th>Total Amount Collected</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($invoice_data)) { ?> 
                    <?php
                    $sn = 1; $t_ar = $t_ac = $t_sc = $num_bookings = $debit_penalty = $credit_penalty = 0;
                    foreach ($invoice_data as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['state']; ?></td>
                            <td><?php echo $value['invoice_date']; ?></td>
                            <td><?php echo $value['from_date']; ?></td>
                            <td><?php echo $value['to_date']; ?></td>
                            <td><?php echo round($value['num_bookings'],0); $num_bookings += $value['num_bookings'];?></td>
                            <td><?php echo round($value['penalty_amount'],0); $debit_penalty += $value['penalty_amount'];?></td>
                            <td><?php echo round($value['credit_penalty_amount'],0); $credit_penalty += $value['credit_penalty_amount'];?></td>
                            <td><?php echo round($value['around_royalty'],0); $t_ar += $value['around_royalty'];?></td>
                            <td><?php echo round($value['service_tax'],0); $t_sc += $value['service_tax']; ?></td>
                            <td><?php echo round($value['cgst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['sgst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['igst_tax_amount'],0); ?></td>
                            <td><?php echo round($value['cgst_tax_rate'] + $value['sgst_tax_rate'] + $value['igst_tax_rate'],0); ?></td>
                            <td><?php echo round($value['total_amount_collected'],0); $t_ac += $value['total_amount_collected']; ?></td>
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
                            <td><?php echo $num_bookings; ?></td>
                            <td><?php echo $debit_penalty; ?></td>
                            <td><?php echo $credit_penalty; ?></td>
                            <td><b><?php echo round($t_ar,0); ?></b></td>
                            <td><b><?php echo round($t_sc,0); ?></b></td>
                             <td></td> <td></td> <td></td> <td></td>
                            <td><b><?php echo round($t_ac,0); ?></b></td>
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
                        <th>Type</th>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>Name on PAN</th>
                        <th>PAN</th>
                        <th>Owner Name</th>
                        <th>Service Charges</th>
                        <th>Total Additional Service Charge</th>
                        <th>Service Tax</th>
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
                $sn = 1; $total_sc = $total_asc = $total_st = $grand_tac = $total_net_amount = $total_tds_amount = $amount_collected_paid = 0;
                foreach ($invoice_data as $key => $value) {
                    ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['company_type']; ?></td>
                                <td><?php echo $value['invoice_id']; ?></td>
                                <td><?php echo $value['invoice_date']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo $value['owner_name']; ?></td>
                                <td><?php echo round($value['total_service_charge'],0); $total_sc += $value['total_service_charge']; ?></td>
                                <td><?php echo round($value['total_additional_service_charge'],0); $total_asc += $value['total_additional_service_charge']; ?></td>
                                <td><?php echo round($value['service_tax'],0); $total_st += $value['service_tax']; ?></td>
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
                                <td><b><?php echo round($total_sc,0); ?></b></td>
                                <td><b><?php echo round($total_asc,0); ?></b></td>
                                <td><b><?php echo round($total_st,0); ?></b></td>
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
<?php } ?>