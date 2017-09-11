<?php if(!empty($payment_history)){ ?> 
<table class="table table-bordered  table-hover table-striped table-rsponsive">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Credit/Debit</th>
            <th>Amount</th>
            <th>TDS Amount</th>
            <th>Agent Name</th>
            <th>Transaction Date</th>
        </tr>
    </thead>
    <tbody>
        <?php $sn = 1; foreach ($payment_history as $value){ ?> 
        <tr>
            <td><?php echo $sn; ?></td>
            <td><?php echo $value['credit_debit']; ?></td>
            <td><?php echo $value['credit_debit_amount']; ?></td>
            <td><?php echo $value['tds_amount']; ?></td>
            <td><?php echo $value['full_name']; ?></td>
            <td><?php echo date('d-F-Y', strtotime($value['transaction_date'])); ?></td>
        </tr>
        <?php $sn++;} ?>
    </tbody>
</table>
<?php }else{ ?> 
<div class="text-center text-danger">No Data Found</div>
<?php } ?>