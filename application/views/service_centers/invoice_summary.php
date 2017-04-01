<div id="page-wrapper">
<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         <th>No #</th>
         <th>Invoice ID</th>
         <th>Period</th>
         <th>Type</th>
         <th>Invoice Excel File</th>
         <th>Invoice Detailed File</th>
         <th>Number of Bookings</th>
         <th>TDS</th>
         <th>Amount Paid By 247Around</th>
         <th>Amount Paid By Partner</th>
      </tr>
   </thead>
   <tbody>
      <?php
       $count = 1;
       $sum_no_of_booking =0;
       $total_amount_collected =0;
       $pay_247 = 0;
       $pay_sf= 0;
       $tds = 0;
       if(!empty($invoice_array)){
         foreach($invoice_array as $key =>$invoice) {?>

      <tr <?php if($invoice['settle_amount'] == 1){ ?> style="background-color: #90EE90; " <?php } ?>>
         <td><?php echo $count;?></td>
         <td><?php echo $invoice['invoice_id'];?></td>
         <td><?php echo date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
         <td><?php echo $invoice['type']; ?></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_file_excel']; ?>"><?php echo $invoice['invoice_file_excel']; ?></a></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_detailed_excel']; ?>"><?php echo $invoice['invoice_detailed_excel']; ?></a></td>
         <td><?php echo $invoice['num_bookings'];  $sum_no_of_booking += $invoice['num_bookings']; ?></td>
        
          <td><?php echo $invoice['tds_amount'];$tds += abs($invoice['tds_amount']); ?></td>
         <td ><?php  if($invoice['amount_collected_paid'] < 0){ echo abs(round($invoice['amount_collected_paid'],0)); $pay_247 += $invoice['amount_collected_paid'];} else {echo "0.00"; } ?></td>
         <td ><?php if($invoice['amount_collected_paid'] > 0){ echo round($invoice['amount_collected_paid'],0); $pay_sf += $invoice['amount_collected_paid']; } else {echo "0.00";} ?></td>

         <?php  $count = $count+1;  ?>

      </tr>
      <?php }} ?>

      <tr>
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php echo $sum_no_of_booking;?></td>
         <td><?php echo round($tds,0);?></td>
         <td><?php echo abs(round($pay_247,0));?></td>
         <td><?php echo abs(round($pay_sf,0));?></td>
        
      </tr>
   </tbody>
   </tbody>
</table>
<?php } 
    if(isset($bank_statement)) { ?>
    <br>
     <p><h2>Bank Transactions</h2></p>
      <table class="table table-bordered  table-hover table-striped data"  >
        <thead>
          <tr>
             <th>No #</th>
             <th>Transaction Date</th>
             <th>Description</th>
             <th>Amt Received from Vendor</th>         
             <th>Amt Paid to Vendor</th>
             <th>TDS Deducted</th>
             <th>Invoices</th>
             <th>Bank Name / Mode</th>
          </tr>
       </thead>
       
       <tbody>
           
           <?php $count=1; $debit_amount=0; $credit_amount=0; $tds_amount=0; ?>
           <?php foreach($bank_statement as $value){?>
               <tr id="<?php echo "row".$count;?>">
               <td><?php echo $count;$count++;?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo $value['credit_amount']; $credit_amount += intval($value['credit_amount']); ?></td>       
               <td><?php echo $value['debit_amount'];  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php echo $value['tds_amount']; $tds_amount += intval($value['tds_amount']); ?></td>
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>   
           <?php } ?>
           </tr>
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php echo $credit_amount;?></td>
             <td><?php echo $debit_amount;?></td>
             <td><?php echo $tds_amount;?></td>
             <td></td>
              <td></td>
             </tr>
       </tbody>
      </table>
    
<?php } ?>
 <br>
 
<?php 
       
    $final_settlement = $pay_247 + $pay_sf + $debit_amount - $credit_amount;
    ?>
    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0){ echo round($final_settlement,0);} else { echo 0;} ?></h4></p>
    <p ><h4 style="margin-bottom: 30px;">247around has to pay to vendor = Rs. <?php if($final_settlement < 0){ echo abs(round($final_settlement,0));} else {echo 0;} ?></h4></p>
    </div>

