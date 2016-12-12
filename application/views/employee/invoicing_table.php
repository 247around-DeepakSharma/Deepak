<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>

<form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/get_add_new_transaction/<?php if(!empty($invoice_array)){ ?><?php echo $invoice_array[0]['vendor_partner'];?>/<?php echo $invoice_array[0]['vendor_partner_id']; } ?>" >
 <input type="text" name="selected_amount_collected" id="selected_amount_collected" value="" hidden />
         <input type="text" name="selected_tds" id="selected_tds" value="" hidden />
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         <th>No #</th>
<!--         <th>Invoice Id</th>-->
          <th>Main Invoice File</th>
         <th>Detailed Invoice File</th>
         <th>Type</th>
         <th>Invoicing Range</th>
         <th>Service Charges</th>
         <th>Additional Service Charges</th>
         <th>Parts / Stands</th>
         <th>TDS Amount</th>
         <th>Amount to be Pay By 247Around</th>
         <th>Amount to be Pay By Partner</th>
         <th>Paid Amount</th> 
         <th>Checkbox</th>
         <th>Update</th>
<!--         <th>Send Email</th>-->
      </tr>
   </thead>
   <tbody>
   
      <?php
       $count = 1;
       $sum_no_of_booking = 0;
       $sum_of_total_service_charges = 0;
       $sum_total_additional_service_charge = 0;
       $sum_total_parts_cost = 0;
       $sum_tds = 0;
       $pay_by_247 =0;
       $pay_by_partner = 0;
      

       if(!empty($invoice_array)){

         foreach($invoice_array as $key =>$invoice) {?>

      <tr <?php if($invoice['settle_amount'] == 1){ ?> style="background-color: #90EE90; " <?php } ?>>
         <td><?php echo $count;?></td>
<!--         <td><?php //echo $invoice['invoice_id']; ?></td>-->
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_file_excel']; ?>"><?php echo $invoice['invoice_file_excel']; ?></a></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_detailed_excel']; ?>"><?php echo $invoice['invoice_detailed_excel']; ?></a></td>
        
         <td style="max-width: 56px; word-wrap:break-word;"><?php echo $invoice['type']; ?></td>
         <td><?php echo date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
         
         <td><?php echo ($invoice['total_service_charge'] + $invoice['service_tax']); $sum_of_total_service_charges +=  $invoice['total_service_charge'] + $invoice['service_tax']; ?></td>
         <td><?php echo $invoice['total_additional_service_charge']; $sum_total_additional_service_charge += $invoice['total_additional_service_charge'];?></td>
         <td><?php echo ($invoice['parts_cost'] + $invoice['vat']); $sum_total_parts_cost +=($invoice['parts_cost'] + $invoice['vat']); ?></td>
         <td id="<?php echo 'tds_'.$count; ?>"><?php echo $invoice['tds_amount']; $sum_tds +=$invoice['tds_amount'];?></td>
         <td id="<?php echo 'pay_247'.$count; ?>" ><?php  if($invoice['amount_collected_paid'] < 0){ echo $invoice['amount_collected_paid']; $pay_by_247 += ($invoice['amount_collected_paid'] );} else {echo "0.00"; } ?></td>
         <td id="<?php echo 'pay_partner'.$count; ?>"><?php if($invoice['amount_collected_paid'] > 0){ echo $invoice['amount_collected_paid']; $pay_by_partner += $invoice['amount_collected_paid'];} else {echo "0.00";} ?></td>
        
         <td id="<?php echo 'amount_paid_'.$count; ?>"><?php echo $invoice['amount_paid'] ?></td>
         
        
         <td ><?php if($invoice['settle_amount'] == 0){ ?><input type="checkbox" class="form-control" name ="invoice_id[]" value="<?php echo $invoice['invoice_id'] ?>" id="<?php echo 'checkbox_'.$count; ?>" onclick="sum_amount()" />
         <?php } ?>
        

         </td>
         <td>
             <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Update
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>employee/invoice/regenerate_invoice/<?php echo $invoice['invoice_id'];?>/final">Final</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo base_url();?>employee/invoice/regenerate_invoice/<?php echo $invoice['invoice_id'];?>/draft">Draft</a></li>
                </ul>
              </div>
         </td>

          <?php  $count = $count+1;  ?>
<!--         <td class="col-md-6">
          <form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/sendInvoiceMail/<?php echo $invoice['invoice_id'].'/'.$invoice['vendor_partner_id'].'/'.$invoice['from_date'].'/'.$invoice['to_date'].'/'.$invoice['vendor_partner']; ?>" >

            <input type="text" class="form-control"  name="email" >
            <input style ="margin-top:8px;" type="submit"  value="Send Mail" >
            </form>
         </td>-->


      </tr>
      <?php }} ?>


      <tr>
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         
         
         <td><?php echo $sum_of_total_service_charges; ?></td>
         <td><?php echo $sum_total_additional_service_charge; ?></td>
         <td><?php echo $sum_total_parts_cost; ?></td>
         <td><?php echo $sum_tds; ?></td>
         <td><?php echo $pay_by_247; ?></td>
         <td><?php echo $pay_by_partner; ?></td>
         <td id="final_amount_selected"></td>
         <td id ="final_tds_selected"></td>
        
         <td> <input type="submit" class="form-control btn btn-sm btn-primary" value="Pay"></td>
         
      </tr>
   </tbody>
   </tbody>
</table>

      </form>
  <script type="text/javascript">
     
     
      function sum_amount(){
              var total_amount_collected = 0;
              var total_tds = 0;
              var total_amount_paid = 0;
              $("input[type=checkbox]:checked").each(function(i) {
              div = this.id .split('_');
            
              var tds_amount = $('#tds_'+ div[1]).text();
              var pay = Number($('#pay_247'+ div[1]).text()) + Number($('#pay_partner'+ div[1]).text());
              
              total_amount_collected += Number(pay);
              total_tds += Number(tds_amount);
              
              //total_amount_paid += Number(Math.abs(amount_paid));
        
              });
              
              $('#selected_amount_collected').val(total_amount_collected.toFixed(2));
              $('#selected_tds').val(total_tds.toFixed(2));
              document.getElementById("final_amount_selected").innerHTML = Math.abs(total_amount_collected.toFixed(2));
              document.getElementById("final_tds_selected").innerHTML = total_tds.toFixed(2);
              
      }
  </script>
<?php }  ?>
  
  <?php if(isset($vendor_details)) { ?>
      
      <br>
     <h2>Document Status</h2>
      <table class="table table-bordered  table-hover table-striped data"  >
          <thead>
          <tr>
             <th class="text-center">PAN</th>     
             <th class="text-center">Service Tax </th>
             <th class="text-center">VAT/TIN</th>
             <th class="text-center">CST</th>
             <th class="text-center">Contract</th>
          </tr>
       </thead>
       <tbody>
           <tr>
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($vendor_details[0]['pan_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($vendor_details[0]['service_tax_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($vendor_details[0]['vat_no']) || !empty($vendor_details[0]['tin_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($vendor_details[0]['cst_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($vendor_details[0]['contract_file'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               
               
               
           </tr>
       </tbody>
      </table>
      
 <?php }?>
  
    <?php if(isset($bank_statement)) { ?>
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
             <th colspan="2">Edit/Delete</th>
          </tr>
       </thead>
       
       <tbody>
           
           <?php $count=1; $debit_amount=0; $credit_amount=0 ?>
           <?php foreach($bank_statement as $value){?>
           
               <tr id="<?php echo "row".$count;?>">
               <td><?php echo $count;$count++;?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo $value['credit_amount']; $credit_amount += intval($value['credit_amount']); ?></td>       
               <td><?php echo $value['debit_amount'];  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php echo $value['tds_amount'];?></td>
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>   
               <td>
               <a href="<?php echo base_url();?>employee/invoice/update_banktransaction/<?php echo $value['id'];?>" class="btn btn-sm btn-success">Edit</a>

               </td>   
               <td>
               <a href="<?php echo base_url();?>employee/invoice/delete_banktransaction/<?php echo $value['id'];?>/<?php echo $value['partner_vendor'];?>/<?php echo $value['partner_vendor_id']; ?>" class="btn btn-sm btn-danger">Delete</a>

               </td>                  
           <?php } ?>
           </tr>
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php echo $credit_amount;?></td>
             <td><?php echo $debit_amount;?></td>
             <td></td>
             <td></td>
             <td></td>
       </tbody>
      </table>
    <br><br>  
    <h2><u>Final Summary</u></h2>
    <br>
    <?php 
       
        $final_settlement = $pay_by_247 + $pay_by_partner + $debit_amount - $credit_amount;
    ?>
    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0){ echo round($final_settlement,2);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($final_settlement < 0){ echo abs(round($final_settlement,2));} else {echo 0;} ?></h4></p>
    
    <?php } ?>

    