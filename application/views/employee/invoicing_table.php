<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         <th>No #</th>
         <th>Invoice Id</th>
         <th>Type</th>
         <th>Invoice Excel File</th>
         <th>Invoice PDF File</th>
         <th>Invoicing Range</th>
         <th>Number of Bookings</th>
         <th>Service Charges</th>
         <th>Additional Service Charges</th>
         <th>Parts / Stands</th>
         <th>Total Amount Collected</th>
         <th>Around Royalty</th>
         <th>Amount to be Collected / Paid</th>
         <th>Rating</th>
         <th>Sent Date</th>
         <th>Send Email</th>
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
         <td><?php echo $invoice['invoice_id']; ?></td>
         <td>
         <?php
            if($invoice['type'] == 'A'){
               echo "Cash";
	       }  else if($invoice['type'] == 'B'){
              echo "Free of Cost (FOC)";
	       }

         ?></td>

         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_file_excel']; ?>"><?php echo $invoice['invoice_file_excel']; ?></a></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-pdf/<?php echo $invoice['invoice_file_pdf']; ?>"><?php echo $invoice['invoice_file_pdf']; ?></a></td>
         <td><?php echo date("jS F, Y", strtotime($invoice['from_date'])). " to ". date("jS F, Y", strtotime($invoice['to_date'])); ?></td>
         <td><?php echo $invoice['num_bookings'];  $sum_no_of_booking += $invoice['num_bookings']; ?></td>
         <td><?php echo $invoice['total_service_charge']; $sum_of_total_service_charges +=  $invoice['total_service_charge']; ?></td>
         <td><?php echo $invoice['total_additional_service_charge']; $sum_total_additional_service_charge += $invoice['total_additional_service_charge'];?></td>
         <td><?php echo $invoice['parts_cost']; $sum_total_parts_cost += $invoice['parts_cost']; ?></td>
         <td><?php echo $invoice['total_amount_collected']; $total_amount_collected += $invoice['total_amount_collected'];?></td>
         <td><?php echo $invoice['around_royalty']; $around_royalty += $invoice['around_royalty']; ?></td>
         <td><?php echo $invoice['amount_collected_paid']; $amount_collected_paid += $invoice['amount_collected_paid']; ?></td>
         <td><?php echo $invoice['rating']; ?></td>
         <td><?php echo date("jS F, Y", strtotime($invoice['create_date'])); ?></td>
         <?php  $count = $count+1;  ?>



         <td class="col-md-6">
          <form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/sendInvoiceMail/<?php echo $invoice['invoice_id'].'/'.$invoice['vendor_partner_id'].'/'.$invoice['from_date'].'/'.$invoice['to_date'].'/'.$invoice['vendor_partner']; ?>" >

            <input type="text" class="form-control"  name="email" >
            <input style ="margin-top:8px;" type="submit"  value="Send Mail" >
            </form>
         </td>


      </tr>
      <?php }} ?>

      <tr>
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php echo $sum_no_of_booking; ?></td>
         <td><?php echo $sum_of_total_service_charges; ?></td>
         <td><?php echo $sum_total_additional_service_charge; ?></td>
         <td><?php echo $sum_total_parts_cost; ?></td>
         <td><?php echo $total_amount_collected; ?></td>
         <td><?php echo $around_royalty; ?></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
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
             <th>Invoices</th>
             <th>Bank Name / Mode</th>
          </tr>
       </thead>
       <tbody>
           
           <?php $count=1; $debit_amount=0; $credit_amount=0 ?>
           <?php foreach($bank_statement as $value){?>
               <tr>
               <td><?php echo $count;$count++;?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo $value['credit_amount']; $credit_amount += intval($value['credit_amount']); ?></td>       
               <td><?php echo $value['debit_amount'];  $debit_amount += intval($value['debit_amount']); ?></td>
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
             <td></td>
       </tbody>
      </table>
    <br><br>  
    <h2><u>Final Summary</u></h2>
    <br>
    <?php 
        $final_settlement = 0;
        $final_settlement = $amount_collected_paid + $debit_amount - $credit_amount;
    ?>
    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0) echo $final_settlement; else echo 0; ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($final_settlement < 0) echo (0-$final_settlement); else echo 0; ?></h4></p>
    
    <?php } ?>
