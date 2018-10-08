<div id="page-wrapper" >
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >Vendor GST Detail</div>
            <div class="panel-body">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Vendor</th>
                            <th>Owner Detail</th>
                            <th>PoC Detail</th>
                            <th>RM Detail</th>
                            <th>Vendor Status</th>
                            <th>Amount till last FY</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php  
                $total_fy_amount = 0;
                $total_amount = 0;
                $i=1;
                foreach ($data as $value){ 
                $total_fy_amount = $total_fy_amount + $value->fy_amount; 
                $total_amount = $total_amount + $value->total_amount;
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><a href="<?php echo base_url(); ?>employee/invoice/invoice_summary/vendor/<?php echo $value->vendor_partner_id; ?>" target="_blank"><?php  echo $value->name; ?></a></td>
                    <td><?php echo $value->owner_name.' / '.$value->owner_phone_1; ?></td>
                    <td><?php echo $value->primary_contact_name.' / '.$value->primary_contact_phone_1; ?></td>
                    <td><?php echo $value->rm_name.' / '.$value->phone; ?></td>
                    <td><?php echo $value->status; ?></td>
                    <td><?php echo $value->fy_amount; ?></td>
                    <td><?php echo $value->total_amount; ?></td>
                </tr>
           <?php } ?>
                <tr style="font-weight: bold;"><td>Total</td><td></td><td></td><td></td><td></td><td></td><td><?php echo round($total_fy_amount, 2);  ?></td><td><?php echo round($total_amount, 2); ?></td></tr>
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
