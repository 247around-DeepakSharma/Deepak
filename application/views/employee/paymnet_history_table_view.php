<?php if ($payment_type == 'sales') { ?>
    <?php if ($partner_vendor == 'partner') { ?> 
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
                    <th>Total Amount Collected</th>
                    <th>Vat %</th>
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
                            <td><?php echo $value['InvoiceNo']; ?></td>
                            <td><?php echo $value['CompanyName']; ?></td>
                            <td><?php echo $value['State']; ?></td>
                            <td><?php echo $value['InvoiceDate']; ?></td>
                            <td><?php echo $value['FromDate']; ?></td>
                            <td><?php echo $value['ToDate']; ?></td>
                            <td><?php echo round($value['TotalServiceCharge'],0); ?></td>
                            <td><?php echo round($value['total_additional_service_charge'],0); ?></td>
                            <td><?php echo round($value['ServiceTax'],0); ?></td>
                            <td><?php echo round($value['Parts'],0); ?></td>
                            <td><?php echo round($value['VAT'],0); ?></td>
                            <td><?php echo round($value['ConveyanceCharges'],0); ?></td>
                            <td><?php echo round($value['Courier'],0); ?></td>
                            <td><?php echo round($value['TotalAmountCollected'],0); ?></td>
                            <td><?php echo round($value['VAT Rate'],0); ?></td>
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
                            <td><b><?php echo round($invoice_total[0]['total_sc'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_asc'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_st'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_pc'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_vat'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_up_cc'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_courier_charges'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['grand_total_amount_collected'],0); ?></b></td>
                            <td></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } else if ($partner_vendor == 'vendor' || $partner_vendor == 'buyback') { ?> 
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
                    <th>Around Royalty</th>
                    <th>Service Tax</th>
                    <th>Total Amount Collected</th>
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
                            <td><?php echo $value['InvoiceNo']; ?></td>
                            <td><?php echo $value['CompanyName']; ?></td>
                            <td><?php echo $value['State']; ?></td>
                            <td><?php echo $value['InvoiceDate']; ?></td>
                            <td><?php echo $value['FromDate']; ?></td>
                            <td><?php echo $value['ToDate']; ?></td>
                            <td><?php echo round($value['AroundRoyalty'],0); ?></td>
                            <td><?php echo round($value['ServiceTax'],0); ?></td>
                            <td><?php echo round($value['TotalAmountCollected'],0); ?></td>
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
                            <td><b><?php echo round($invoice_total[0]['total_AroundRoyalty'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_ServiceTax'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['grand_TotalAmountCollected'],0); ?></b></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } else if ($partner_vendor == 'stand') { ?> 
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>TIN</th>
                    <th>Invoice Date</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Parts</th>
                    <th>VAT</th>
                    <th>Total Amount</th>
                    <th>VAT %</th>
<!--                    <th>Item</th>-->
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
                            <td><?php echo $value['InvoiceNo']; ?></td>
                            <td><?php echo $value['CompanyName']; ?></td>
                            <td><?php echo $value['TINNo']; ?></td>
                            <td><?php echo $value['InvoiceDate']; ?></td>
                            <td><?php echo $value['FromDate']; ?></td>
                            <td><?php echo $value['ToDate']; ?></td>
                            <td><?php echo round($value['Parts'],0); ?></td>
                            <td><?php echo round($value['VAT'],0); ?></td>
                            <td><?php echo round($value['TotalAmount'],0); ?></td>
                            <td><?php echo round($value['VATRate'],0); ?></td>
<!--                            <td><?php //echo $value['Item']; ?></td>-->
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
                            <td><b><?php echo round($invoice_total[0]['total_Parts'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_VAT'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['grand_TotalAmount'],0); ?></b></td>
                            <td></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } ?>

<?php } else if ($payment_type == 'purchase') { ?> 
    <?php if ($partner_vendor == 'partner' || $partner_vendor == 'vendor' || $partner_vendor == 'buyback') { ?> 
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>State</th>
                    <th>Service Tax No</th>
                    <th>TIN</th>
                    <th>Invoice Date</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Service Charge</th>
                    <th>Additional Service Charge</th>
                    <th>Service Tax</th>
                    <th>Parts</th>
                    <th>VAT</th>
                    <th>Conveyance Charge</th>
                    <th>Courier</th>
                    <th>Misc Debit</th>
                    <th>Misc Credit</th>
                    <th>Total Amount</th>
                    <th>VAT %</th>
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
                            <td><?php echo $value['InvoiceNo']; ?></td>
                            <td><?php echo $value['CompanyName']; ?></td>
                            <td><?php echo $value['State']; ?></td>
                            <td><?php echo $value['ServiceTaxNo']; ?></td>
                            <td><?php echo $value['TINNo']; ?></td>
                            <td><?php echo $value['InvoiceDate']; ?></td>
                            <td><?php echo $value['FromDate']; ?></td>
                            <td><?php echo $value['ToDate']; ?></td>
                            <td><?php echo round($value['ServiceCharges'],0); ?></td>
                            <td><?php echo round($value['total_additional_service_charge'],0); ?></td>
                            <td><?php echo round($value['ServiceTax'],0); ?></td>
                            <td><?php echo round($value['Parts'],0); ?></td>
                            <td><?php echo round($value['VAT'],0); ?></td>
                            <td><?php echo round($value['ConveyanceCharges'],0); ?></td>
                            <td><?php echo round($value['Courier'],0); ?></td>
                            <td><?php echo round($value['MiscDebit'],0); ?></td>
                            <td><?php echo round($value['MiscCredit'],0); ?></td>
                            <td><?php echo round($value['TotalAmount'],0); ?></td>
                            <td><?php echo round($value['VATRate'],0); ?></td>
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
                            <td><b><?php echo round($invoice_total[0]['total_ServiceCharges'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_ac'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_ServiceTax'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_Parts'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_VAT'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_ConveyanceCharges'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_Courier'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_MiscDebit'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_MiscCredit'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['grand_TotalAmount'],0); ?></b></td>
                            <td></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } else if ($partner_vendor == 'stand') { ?>
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>Service Tax No</th>
                    <th>TIN</th>
                    <th>Invoice Date</th>
                    <th>Parts</th>
                    <th>Total Amount</th>
                    <th>VAT</th>
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
                            <td><?php echo $value['InvoiceNo']; ?></td>
                            <td><?php echo $value['CompanyName']; ?></td>
                            <td><?php echo $value['ServiceTaxNo']; ?></td>
                            <td><?php echo $value['TINNo']; ?></td>
                            <td><?php echo $value['InvoiceDate']; ?></td>
                            <td><?php echo round($value['Parts'],0); ?></td>
                            <td><?php echo round($value['TotalAmount'],0); ?></td>
                            <td><?php echo round($value['VAT'],0); ?></td>
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
                            <td><b><?php echo round($invoice_total[0]['total_Parts'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['grand_TotalAmount'],0); ?></b></td>
                            <td><b><?php echo round($invoice_total[0]['total_VAT'],0); ?></b></td>
                        </tr>
        <?php } ?>
            </tbody>
        </table>
    <?php } ?>

<?php } else if ($payment_type == 'tds') { ?> 
    <?php if ($partner_vendor == 'vendor' || $partner_vendor == 'buyback') { ?>
        <?php if ($report_type == 'draft') { ?>
            <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Company Name</th>
                        <th>Type</th>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>Type</th>
                        <th>Type Code</th>
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
                $sn = 1;
                foreach ($invoice_data as $key => $value) {
                    ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $value['company_name']; ?></td>
                                <td><?php echo $value['company_type']; ?></td>
                                <td><?php echo $value['invoice_id']; ?></td>
                                <td><?php echo $value['invoice_date']; ?></td>
                                <td><?php echo $value['type']; ?></td>
                                <td><?php echo $value['type_code']; ?></td>
                                <td><?php echo $value['name_on_pan']; ?></td>
                                <td><?php echo $value['pan_no']; ?></td>
                                <td><?php echo $value['owner_name']; ?></td>
                                <td><?php echo round($value['total_service_charge'],0); ?></td>
                                <td><?php echo round($value['total_additional_service_charge'],0); ?></td>
                                <td><?php echo round($value['service_tax'],0); ?></td>
                                <td><?php echo round($value['total_amount_collected'],0); ?></td>
                                <td><?php echo round($value['net_amount'],0); ?></td>
                                <td><?php echo round($value['tds_amount'],0); ?></td>
                                <td><?php echo round($value['tds_rate'],0); ?></td>
                                <td><?php echo round($value['amount_collected_paid'],0); ?></td>
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
                                <td><b><?php echo round($invoice_total[0]['total_sc'],0); ?></b></td>
                                <td><b><?php echo round($invoice_total[0]['total_asc'],0); ?></b></td>
                                <td><b><?php echo round($invoice_total[0]['total_st'],0); ?></b></td>
                                <td><b><?php echo round($invoice_total[0]['grand_tac'],0); ?></b></td>
                                <td><b><?php echo round($invoice_total[0]['total_net_amount'],0); ?></b></td>
                                <td><b><?php echo round($invoice_total[0]['total_tds_amount'],0); ?></b></td>
                                <td></td>
                                <td><b><?php echo round($invoice_total[0]['total_amount_collected_paid'],0); ?></b></td>
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