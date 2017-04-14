<?php if ($payment_type == 'sales') { ?>
    <?php if($partner_vendor == 'partner') { ?> 
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
                <?php if (isset($report_data)) { ?> 
                    <?php $sn = 1;
                    foreach ($report_data as $key => $value) {
                    ?>
                <tr>
                    <td><?php echo $sn; ?></td>
                    <td><?php echo $value['InvoiceNo']; ?></td>
                    <td><?php echo $value['CompanyName']; ?></td>
                    <td><?php echo $value['State']; ?></td>
                    <td><?php echo $value['InvoiceDate']; ?></td>
                    <td><?php echo $value['FromDate']; ?></td>
                    <td><?php echo $value['ToDate']; ?></td>
                    <td><?php echo $value['TotalServiceCharge']; ?></td>
                    <td><?php echo $value['ServiceTax']; ?></td>
                    <td><?php echo $value['Parts']; ?></td>
                    <td><?php echo $value['VAT']; ?></td>
                    <td><?php echo $value['ConveyanceCharges']; ?></td>
                    <td><?php echo $value['Courier']; ?></td>
                    <td><?php echo $value['TotalAmountCollected']; ?></td>
                    <td><?php echo $value['VAT Rate']; ?></td>
                </tr>
                    <?php $sn++;} ?>
                <?php } ?>
            </tbody>
        </table>
    <?php } else if($partner_vendor == 'vendor'){ ?> 
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
                <?php if (isset($report_data)) { ?> 
                    <?php $sn = 1;
                    foreach ($report_data as $key => $value) {
                    ?>
                <tr>
                    <td><?php echo $sn; ?></td>
                    <td><?php echo $value['InvoiceNo']; ?></td>
                    <td><?php echo $value['CompanyName']; ?></td>
                    <td><?php echo $value['State']; ?></td>
                    <td><?php echo $value['InvoiceDate']; ?></td>
                    <td><?php echo $value['FromDate']; ?></td>
                    <td><?php echo $value['ToDate']; ?></td>
                    <td><?php echo $value['AroundRoyalty']; ?></td>
                    <td><?php echo $value['ServiceTax']; ?></td>
                    <td><?php echo $value['TotalAmountCollected']; ?></td>
                </tr>
                    <?php $sn++;} ?>
                <?php } ?>
            </tbody>
        </table>
    <?php } else if($partner_vendor == 'stand'){ ?> 
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
                    <th>Item</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($report_data)) { ?> 
                    <?php $sn = 1;
                    foreach ($report_data as $key => $value) {
                    ?>
                <tr>
                    <td><?php echo $sn; ?></td>
                    <td><?php echo $value['InvoiceNo']; ?></td>
                    <td><?php echo $value['CompanyName']; ?></td>
                    <td><?php echo $value['TINNo']; ?></td>
                    <td><?php echo $value['InvoiceDate']; ?></td>
                    <td><?php echo $value['FromDate']; ?></td>
                    <td><?php echo $value['ToDate']; ?></td>
                    <td><?php echo $value['Parts']; ?></td>
                    <td><?php echo $value['VAT']; ?></td>
                    <td><?php echo $value['TotalAmount']; ?></td>
                    <td><?php echo $value['VATRate']; ?></td>
                    <td><?php echo $value['Item']; ?></td>
                </tr>
                    <?php $sn++;} ?>
                <?php } ?>
            </tbody>
        </table>
    <?php }?>
    
<?php } else if($payment_type == 'purchase'){ ?> 
    <?php if($partner_vendor == 'partner' || $partner_vendor == 'vendor') { ?> 
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
            <?php if (isset($report_data)) { ?> 
                <?php $sn = 1;
                foreach ($report_data as $key => $value) {
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
                <td><?php echo $value['ServiceCharges']; ?></td>
                <td><?php echo $value['ServiceTax']; ?></td>
                <td><?php echo $value['Parts']; ?></td>
                <td><?php echo $value['VAT']; ?></td>
                <td><?php echo $value['ConveyanceCharges']; ?></td>
                <td><?php echo $value['Courier']; ?></td>
                <td><?php echo $value['MiscDebit']; ?></td>
                <td><?php echo $value['MiscCredit']; ?></td>
                <td><?php echo $value['TotalAmount']; ?></td>
                <td><?php echo $value['VATRate']; ?></td>
            </tr>
                <?php $sn++;} ?>
            <?php } ?>
        </tbody>
    </table>
    <?php } else if($partner_vendor == 'stand'){ ?>
        <table class="table table-bordered table-hover table-responsive paginated" id="payment_history_table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Invoice No</th>
                    <th>Company Name</th>
                    <th>Service Tax No</th>
                    <th>TIN</th>
                    <th>Invoice Date</th>
                    <th>Total Amount</th>
                    <th>VAT</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($report_data)) { ?> 
                    <?php $sn = 1;
                    foreach ($report_data as $key => $value) {
                    ?>
                <tr>
                    <td><?php echo $sn; ?></td>
                    <td><?php echo $value['InvoiceNo']; ?></td>
                    <td><?php echo $value['CompanyName']; ?></td>
                    <td><?php echo $value['ServiceTaxNo']; ?></td>
                    <td><?php echo $value['TINNo']; ?></td>
                    <td><?php echo $value['InvoiceDate']; ?></td>
                    <td><?php echo $value['TotalAmount']; ?></td>
                    <td><?php echo $value['VAT']; ?></td>
                </tr>
                    <?php $sn++;} ?>
                <?php } ?>
            </tbody>
        </table>
    <?php }?>
    
<?php } ?>