<style>
    td, th{
        padding: 8px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    
    table, th, td {
        border: 1px solid black;
    }
</style>    
<table style="width: 100%;">
    <?php if(count($transaction_details) > 0){ ?>
    <tr>
        <th>S.No</th>
        <th><?php echo ucfirst($vendor_partner_type); ?> Name</th>
        <th>Transaction Date</th>
        <th>Description</th>
        <th>Credit/Debit</th>
        <th>Amount</th>
        <th>TDS Deducted</th>
        <th>Invoices</th>
        <th>Transaction Mode</th>
    </tr>                  
    <?php $i = 1; foreach($transaction_details as $data){ ?>
    <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo $data['name']; ?></td>
        <td><?php echo date("d-M-Y", strtotime($data['transaction_date'])); ?></td>
        <td><?php echo $data['description']; ?></td>
        <td><?php echo $data['credit_debit']; ?></td>
        <td><?php if($data['credit_debit'] == 'Credit'){ echo $data['credit_amount']; } else { echo $data['debit_amount']; } ?></td>
        <td><?php echo $data['tds_amount']; ?></td>
        <td><?php echo $data['invoice_id']; ?></td>
        <td><?php echo $data['transaction_mode']; ?></td>
    </tr> 
    <?php $i++; } }else{ ?>
    <tr>
        <td colspan="6">Data Not Found.</td>
    </tr>
    <?php } ?>             
</table>

