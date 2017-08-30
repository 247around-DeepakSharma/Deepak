<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>

<form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/get_add_new_transaction/<?php if(!empty($invoice_array)){ ?><?php echo $invoice_array[0]['vendor_partner'];?>/<?php echo $invoice_array[0]['vendor_partner_id']; } ?>" >
 <input type="text" name="selected_amount_collected" id="selected_amount_collected" value="" hidden />
         <input type="text" name="selected_tds" id="selected_tds" value="" hidden />
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         
         <th>Invoice Id</th>
         <th>Type</th>
         <th>Bookings</th>
         <th>Invoice Period</th>
         <th>Service Charges</th>
         <th>Additional Service Charges</th>
         <th>Parts / Stands</th>
         <th>TDS Amount</th>
         <th style="display:none;">Upcountry Charges</th>
         <th style="display:none;">Courier Charges</th>
         <th>Penalty</th>
         <th>GST Amount</th>
         <th>Amount to be Paid By 247Around</th>
         <th>Amount to be Paid By Partner</th>
         <th>Amount Paid</th> 
         <th>Select</th>
         <th>ReGenerate</th>
         <th>Update</th>
        
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
         
         <td><?php echo $invoice['invoice_id']; ?>
             <p style="margin-top:15px;">
                 <a  href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_file_main']; ?>">Main</a>
             </p> <p style="margin-top:15px;">
             <a  href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $invoice['invoice_detailed_excel']; ?>">Detail</a>
             </p> <p style="margin-top:15px;">
             <a  href="javascript:void(0);" class="get_invoice_payment_history" data-id="<?php echo $invoice['invoice_id'];?>">History</a>
             </p>
         </td>
        
         <td style="max-width: 56px; word-wrap:break-word;"><?php echo $invoice['type']; ?></td>
         <td ><?php echo $invoice['num_bookings']; ?></td>
         <td><?php echo date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
         
         <td><?php echo (round(($invoice['total_service_charge'] + $invoice['service_tax']),0)); $sum_of_total_service_charges +=  $invoice['total_service_charge'] + $invoice['service_tax']; ?></td>
         <td><?php echo round($invoice['total_additional_service_charge'],0); $sum_total_additional_service_charge += $invoice['total_additional_service_charge'];?></td>
         <td><?php echo (round(($invoice['parts_cost'] + $invoice['vat']),0)); $sum_total_parts_cost +=($invoice['parts_cost'] + $invoice['vat']); ?></td>
         <td id="<?php echo 'tds_'.$count; ?>"><?php echo round($invoice['tds_amount'],0); $sum_tds +=$invoice['tds_amount'];?></td>
         <td style="display:none;" id="<?php echo 'upcountry_'.$count; ?>"><?php if($invoice['type'] == "Cash" && $invoice['vendor_partner'] == "vendor") { echo "-".round($invoice['upcountry_price'],0);} else { echo round($invoice['upcountry_price'],0); } ?></td>
         <td style="display:none;" id="<?php echo 'courier_charges_'.$count; ?>"><?php echo round($invoice['courier_charges'],0); ?></td>
         <td id="<?php echo 'penalty_'.$count; ?>"><?php echo "-".round($invoice['penalty_amount'],0); ?></td>
         <td id="<?php echo 'gst_'.$count; ?>"><?php echo round($invoice['igst_tax_amount'] + $invoice['cgst_tax_amount'] + $invoice['sgst_tax_amount'],0); ?></td>
         <td id="<?php echo 'pay_247'.$count; ?>" ><?php  if($invoice['amount_collected_paid'] < 0){ echo round($invoice['amount_collected_paid'],0); $pay_by_247 += ($invoice['amount_collected_paid'] );} else {echo "0.00"; } ?></td>
         <td id="<?php echo 'pay_partner'.$count; ?>"><?php if($invoice['amount_collected_paid'] > 0){ echo round($invoice['amount_collected_paid'],0); $pay_by_partner += $invoice['amount_collected_paid'];} else {echo "0.00";} ?></td>
        
         <td id="<?php echo 'amount_paid_'.$count; ?>"><?php echo round($invoice['amount_paid'],0) ?></td>
         
        
         <td ><?php if($invoice['settle_amount'] == 0){ ?><input type="checkbox" class="form-control" name ="invoice_id[]" value="<?php echo $invoice['invoice_id'] ?>" id="<?php echo 'checkbox_'.$count; ?>" onclick="sum_amount()" />
             
             <input type="hidden" class ="in_disable" name="<?php echo "tds_amount[".$invoice['invoice_id']."] "; ?>" id="<?php echo "intdsAmount_".$count; ?>" value="<?php if($invoice['amount_paid'] > 0 ) { echo "0.00";} else { echo $invoice['tds_amount'];} ?>"/>
             <input type="hidden" class ="in_disable"    name="<?php echo "amount_collected[".$invoice['invoice_id']."] "; ?>" id="<?php echo "inAmountCollected_".$count; ?>" value="<?php if($invoice['amount_collected_paid'] > 0) {echo $invoice['amount_collected_paid'] - $invoice['amount_paid'];} else { echo $invoice['amount_collected_paid'] + $invoice['amount_paid'];}?>"/>
            
            <?php } ?>
            
        

         </td>
         <td>
             <?php if($invoice['vendor_partner'] == "vendor") { ?>
             <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" <?php if($invoice['amount_paid'] > 0 ) { echo "disabled"; } ?>>Action
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>employee/invoice/regenerate_invoice/<?php echo $invoice['invoice_id'];?>/final">Final</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo base_url();?>employee/invoice/regenerate_invoice/<?php echo $invoice['invoice_id'];?>/draft">Draft</a></li>
                </ul>
              </div>
             <?php } ?>
         </td>
         <td>
             <a href="<?php echo base_url()?>employee/invoice/insert_update_invoice/<?php echo $invoice['vendor_partner'];?>/<?php echo $invoice['invoice_id'];?>" <?php if($invoice['amount_paid'] > 0 ) { echo "disabled"; } ?> class="btn btn-sm btn-info" >Update</a>
         </td>
         

          <?php  $count = $count+1;  ?>
<!--         <td class="col-md-6">
          <form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/sendInvoiceMail/<?php //echo $invoice['invoice_id'].'/'.$invoice['vendor_partner_id'].'/'.$invoice['from_date'].'/'.$invoice['to_date'].'/'.$invoice['vendor_partner']; ?>" >

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
         <td><?php echo round($sum_of_total_service_charges,0); ?></td>
         <td><?php echo round($sum_total_additional_service_charge,0); ?></td>
         <td><?php echo round($sum_total_parts_cost,0); ?></td>
         <td><?php echo round($sum_tds,0); ?></td>
         <td></td>
         <td></td>
         <td><?php echo round($pay_by_247,0); ?></td>
         <td><?php echo round($pay_by_partner,0); ?></td>
         <td></td>
         <td id="final_amount_selected"></td>
         <td><input type="submit" class="form-control btn btn-sm btn-primary" value="Pay"></td>
          <td> </td>
        
       
         
      </tr>
   </tbody>
   </tbody>
</table>
         <!--Invoice Payment History Modal-->
    <div id="invoiceDetailsModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <div id="open_model"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<!-- end Invoice Payment History Modal -->

      </form>
  <script type="text/javascript">
     $(".in_disable").prop('disabled', true);
     
      function sum_amount(){
              var total_amount_collected = 0;
              var total_tds = 0;
              var total_amount_paid = 0;
              $(".in_disable").prop('disabled', true);
              $("input[type=checkbox]:checked").each(function(i) {
              div = this.id .split('_');
              $("#intdsAmount_"+div[1]).prop('disabled', false);
              $("#inAmountCollected_"+div[1]).prop('disabled', false);
              $("#inAmountPaid__"+div[1]).prop('disabled', false);
              var amount_paid = $('#amount_paid_'+ div[1]).text();
              var tds_amount = $('#tds_'+ div[1]).text();
              if(amount_paid > 0){
                  tds_amount = 0;
              } 
              var amount_partner_to_be_pay = $('#pay_partner'+ div[1]).text();
              if(amount_partner_to_be_pay > 0){
                  amount_paid = -Math.abs(amount_paid);
              }
              var pay = Number($('#pay_247'+ div[1]).text()) + Number(amount_partner_to_be_pay) + Number(amount_paid);
            
              
              total_amount_collected += Number(pay);
              total_tds += Number(tds_amount);
              
              //total_amount_paid += Number(Math.abs(amount_paid));
        
              });
              
              $('#selected_amount_collected').val(total_amount_collected.toFixed(2));
              $('#selected_tds').val(total_tds.toFixed(2));
              document.getElementById("final_amount_selected").innerHTML = Math.abs(total_amount_collected.toFixed(2));
             // document.getElementById("final_tds_selected").innerHTML = total_tds.toFixed(2);
              
      }
  </script>
  
  <script>
      $('.get_invoice_payment_history').on('click',function(){
          var invoice_id = $(this).attr('data-id');
          if(invoice_id){
              $.ajax({
                  type:"POST",
                  url: "<?php echo base_url(); ?>employee/invoice/get_invoice_payment_history",
                  data: {'invoice_id':invoice_id},
                  success:function(response){
                      //console.log(response);
                      $("#open_model").html(response);   
                      $('#invoiceDetailsModal').modal('toggle');
                  }
              });
          }
      });
  </script>
<?php } ?>
  
  <?php if($invoicing_summary['vendor_partner'] == "vendor") { ?>
      
      <br>
     <h2>Document Status</h2>
      <table class="table table-bordered  table-hover table-striped data"  >
          <thead>
          <tr>
             <th class="text-center">PAN</th>     
             <th class="text-center">Service Tax </th>
             <th class="text-center">VAT/TIN</th>
             <th class="text-center">CST</th>
             <th class="text-center">Bank Details Verification</th>
             <th class="text-center">Contract</th>
             <th class="text-center">Defective Parts Not Shipped by SF</th>
             <th class="text-center">Go to vendor Details</th>
          </tr>
       </thead>
       <tbody>
           <tr>
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['pan_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['service_tax_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['tin_no']) || !empty($invoicing_summary['tin_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['cst_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['is_verified']) && $invoicing_summary['is_verified'] == '1'){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['contract_file'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               <td class="text-center">
                 
                  <?php print_r($invoicing_summary['count_spare_part']);?>
               </td>
               <td class="text-center">
                 
                   <a href="<?php echo base_url()?>/employee/vendor/editvendor/<?php echo $invoicing_summary['id'] ?>" target="_blank" class="btn btn-sm btn-primary" >Click here</a>
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
<!--             <th colspan="2">Edit/Delete</th>-->
          </tr>
       </thead>
       
       <tbody>
           
           <?php $count=1; $debit_amount=0; $credit_amount=0; $tds_amount=0; ?>
           <?php foreach($bank_statement as $value){?>
           
               <tr id="<?php echo "row".$count;?>">
                   <td><?php  echo $count;$count++; if($value['is_advance'] ==1){?> <p id="advance_text">Advance</p><?php }?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo round($value['credit_amount'],0); if($value['is_advance'] ==0){ $credit_amount += intval($value['credit_amount']); } ?></td>       
               <td><?php echo round($value['debit_amount'],0);  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php echo round($value['tds_amount'],0); $tds_amount += intval($value['tds_amount']); ?></td>
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>   
<!--               <td>
               <a href="<?php //echo base_url();?>employee/invoice/update_banktransaction/<?php //echo $value['id'];?>" class="btn btn-sm btn-success">Edit</a>

               </td>   
               <td>
               <a href="<?php //echo base_url();?>employee/invoice/delete_banktransaction/<?php //echo $value['id'];?>/<?php //echo $value['partner_vendor'];?>/<?php //echo $value['partner_vendor_id']; ?>" class="btn btn-sm btn-danger">Delete</a>

               </td>                  -->
           <?php } ?>
           </tr>
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php echo round($credit_amount,0);?></td>
             <td><?php echo round($debit_amount,0);?></td>
             <td><?php echo round($tds_amount,0);?></td>
             <td></td>
             <td></td>
       </tbody>
      </table>
    <br><br>  
    <h2><u>Final Summary</u></h2>
    <br>
    <?php 
 
        $final_settlement = $invoicing_summary['final_amount'];
    ?>
    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0){ echo round($final_settlement,0);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($final_settlement < 0){ echo abs(round($final_settlement,0));} else {echo 0;} ?></h4></p>
    <hr/>
    <?php if(isset($unbilled_amount)){ ?> 
     <h2><u>Un-billed Amount (Invoice is not generated)</u></h2>
     <p><h4>Vendor has to pay to 247around = Rs. <?php if($unbilled_amount[0]['unbilled_amount'] >= 0){ echo round($unbilled_amount[0]['unbilled_amount'],0);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($unbilled_amount[0]['unbilled_amount'] < 0){ echo abs(round($unbilled_amount[0]['unbilled_amount'],0));} else { echo 0;} ?></h4></p>
    
    <?php } } ?>


<style>
#advance_text {
    -ms-transform: rotate(330deg); /* IE 9 */
    -webkit-transform: rotate(330deg); /* Safari */
    transform: rotate(330deg); /* Standard syntax */
    color: red;
    font-weight: bold;
}
    
    
</style>