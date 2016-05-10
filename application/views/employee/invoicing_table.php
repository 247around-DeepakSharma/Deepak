            
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
         <th>Total Service Charges</th>
         <th>Total Additional Service Charges</th>
         <th>Parts cost</th>
         <th>Total Amount Collected</th>
         <th>Around Royalty</th>
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
       if(!empty($invoice_array)){ 
         foreach($invoice_array as $key =>$invoice) {?>

      <tr>
         <td><?php echo $count;?></td>
         <td><?php echo $invoice['invoice_id']; ?></td>
         <td>
         <?php
            if($invoice['type'] == 'A'){

               echo "Free of Cost (FOC)";

            }  else if($invoice['type'] == 'B'){

                  echo "Repair";
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
         <td>Total</td>
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
      </tr>
   </tbody>
   </tbody>
</table>
