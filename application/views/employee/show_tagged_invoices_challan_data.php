<div class="page-wrapper" >
    <div class="tagged_invoice_challan_details" style="padding: 20px;">
        <h3>Tagged Invoices Details</h3>
        <table class="table table-bordered table-hover table-responsive">
        <thead>
            <tr>
                <th> S.No.</th>
                <th> Invoice Id</th>
                <th> Challan Tender Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sn = 1;
            foreach ($tagged_invoice_data as $key => $value) {
                ?>
                <tr> 
                    <td><?php echo $sn; ?></td>
                    <td> <?php echo $value['invoice_id']; ?> </td>
                    <td><?php echo $value['challan_tender_date'] ?></td>
                </tr>
                <?php
                $sn++;
            }
            ?>   
        </tbody>
    </table>
    </div>
</div>