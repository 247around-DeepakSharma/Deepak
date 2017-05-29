<div id="page-wrapper">
<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         <th>No #</th>
         <th>Period</th>
         <th>Invoice Excel File</th>
         <th>Invoice Detailed File</th>
         <th>Number of Bookings</th>
         <th>Service Charges</th>
         <th>Additional Service Charges</th>
         <th>Parts / Stands</th>
         <th>Total</th>
<!--         <th>Around Royalty</th>
         <th>Amt Paid by Partner / Paid by 247around</th>-->
<!--         <th>Sent Date</th>-->
         
      </tr>
   </thead>
   <tbody>
      <?php
       $count = 1;
       $sum_no_of_booking = 0;
       $sum_of_total_service_charges = 0;
       $sum_total_additional_service_charge = 0;
       $sum_total_parts_cost = 0;
       $total_amount_collected =0;
       $around_royalty = 0;
       $amount_collected_paid = 0;
       if(!empty($invoice_array)){
         foreach($invoice_array as $key =>$invoice) {?>

      <tr>
         <td><?php echo $count;?></td>
         <td><?php echo date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS F, Y", strtotime($invoice['to_date'])); ?></td>
        
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_file_main']; ?>"><?php echo $invoice['invoice_file_main']; ?></a></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-pdf/<?php echo $invoice['invoice_detailed_excel']; ?>"><?php echo $invoice['invoice_detailed_excel']; ?></a></td>
         <td><?php echo $invoice['num_bookings'];  $sum_no_of_booking += $invoice['num_bookings']; ?></td>
         <td><?php echo round($invoice['total_service_charge'],0); $sum_of_total_service_charges +=  $invoice['total_service_charge']; ?></td>
         <td><?php echo round($invoice['total_additional_service_charge'],0); $sum_total_additional_service_charge += $invoice['total_additional_service_charge'];?></td>
         <td><?php echo round($invoice['parts_cost'],0); $sum_total_parts_cost += $invoice['parts_cost']; ?></td>
         <td><?php echo round($invoice['total_amount_collected'],0); $total_amount_collected += $invoice['total_amount_collected'];?></td>
<!--         <td><?php echo round($invoice['around_royalty'],0); $around_royalty += $invoice['around_royalty']; ?></td>
         <td><?php// echo $invoice['amount_collected_paid']; $amount_collected_paid += $invoice['amount_collected_paid']; ?></td>-->
<!--         <td><?php //echo date("jS F, Y", strtotime($invoice['create_date'])); ?></td>-->
         <?php  $count = $count+1;  ?>

      </tr>
      <?php }} ?>

<!--      <tr>
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php //echo $sum_no_of_booking; ?></td>
         <td><?php //echo $sum_of_total_service_charges; ?></td>
         <td><?php //echo $sum_total_additional_service_charge; ?></td>
         <td><?php //echo $sum_total_parts_cost; ?></td>
         <td><?php// echo $total_amount_collected; ?></td>
         <td><?php //echo $around_royalty; ?></td>
         <td></td>
         <td></td>
        
      </tr>-->
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
             <th>Amt Received</th>         
<!--             <th>Amt Paid To <?php //echo $this->session->userdata('partner_name');?></th>-->
             <th>Invoices</th>
             <th>Bank Name / Mode</th>
          </tr>
       </thead>
       
       <tbody>
           
           <?php $count=1; $debit_amount=0; $credit_amount=0 ?>
           <?php foreach($bank_statement as $value){?>
               <tr id="<?php echo "row".$count;?>">
               <td><?php echo $count;$count++;?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo round($value['credit_amount'],0); $credit_amount += intval($value['credit_amount']); ?></td>       
<!--               <td><?php //echo $value['debit_amount'];  $debit_amount += intval($value['debit_amount']); ?></td>-->
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>   
           <?php } ?>
           </tr>
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php echo round($credit_amount,0);?></td>
             <td><?php echo round($debit_amount,0);?></td>
             <td></td>
             </tr>
       </tbody>
      </table>
    
<?php } ?>
    </div>
