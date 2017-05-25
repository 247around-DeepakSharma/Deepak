     <?php if ($bank_statement[0]['partner_vendor'] == 'partner') { ?>
         <p><h2>Bank Transactions - Partners</h2></p>
     <?php } else { ?>
         <p><h2>Bank Transactions - Service Centres</h2></p>
     <?php } ?>
     <br><br>
      <table class="table table-bordered  table-hover table-striped data"  >
        <thead>
          <tr>
             <th>No #</th>
             <th>Name</th>
             <th>Transaction Date</th>
             <th>Description</th>
             <th>Amt Received</th>         
             <th>Amt Paid</th>
             <th>Invoices</th>
             <th>Bank Name / Mode</th>
<!--             <th>Delete</th>-->
          </tr>
       </thead>
       
       <tbody>    
           <?php $count=1; $debit_amount=0; $credit_amount=0 ?>
           <?php foreach($bank_statement as $value){?>
               <tr id="<?php echo "row".$count;?>">
               <td><?php echo $count;$count++;?></td>
               <td><?php echo $value['name']; ?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo round($value['credit_amount'],0); $credit_amount += intval($value['credit_amount']); ?></td>       
               <td><?php echo round($value['debit_amount'],0);  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>   
<!--               <td><button onclick="delete_banktransaction(<?php echo $value['id']?>)" class="btn btn-sm btn-danger">Delete</button></td>                  -->
           <?php } ?>
           </tr>
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td></td>
             <td><?php echo round($credit_amount,0);?></td>
             <td><?php echo round($debit_amount,0);?></td>
             <td></td>
             <td></td>
       </tbody>
      </table>

<!--

<script type="text/javascript">
  function delete_banktransaction(transactional_id){     
    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/delete_banktransaction/'+ transactional_id,
          
          success: function (data) {
            if(data =="success"){
               alert('Transaction deleted, refresh this page please');
            }
         }
       });

   }
</script>
-->
