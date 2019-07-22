<div id="page-wrapper">
<p><h2>Invoices Generated</h2></p>
  <table class="table table-bordered  table-hover table-striped data"  id="datatable">
   <thead>
      <tr >
         <th>No</th>
         <th>Invoice ID</th>
         <th>Period</th>
         <th>Type</th>
         <th>Category</th>
         <th>Invoice Excel File</th>
         <th>Invoice Detailed File</th>
         <th>Number of Bookings</th>
         <th>TDS</th>
         <th>Amount Paid By 247Around</th>
         <th>Amount Paid By Partner</th>
      </tr>
   </thead>
<!--   <tbody>
      <?php/*
       $count = 1;
       $sum_no_of_booking =0;
       $total_amount_collected =0;
       $pay_247 = 0;
       $pay_sf= 0;
       $tds = 0;
       if(!empty($invoice_array)){
         foreach($invoice_array as $key =>$invoice) { */?>

      <tr <?php //if($invoice['settle_amount'] == 1){ ?> style="background-color: #90EE90; " <?php //} ?>>
         <td><?php //echo $count;?></td>
         <td><?php //echo $invoice['invoice_id'];?></td>
         <td><?php //echo date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
         <td><?php //echo $invoice['type']; ?></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php //echo $invoice['invoice_file_main']; ?>"><?php //echo $invoice['invoice_file_main']; ?></a></td>
         <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php //echo $invoice['invoice_detailed_excel']; ?>"><?php //echo $invoice['invoice_detailed_excel']; ?></a></td>
         <td><?php //echo //$invoice['num_bookings'];  $sum_no_of_booking += $invoice['num_bookings']; ?></td>
        
          <td><?php //echo $invoice['tds_amount'];$tds += abs($invoice['tds_amount']); ?></td>
         <td ><?php // if($invoice['amount_collected_paid'] < 0){ echo abs(round($invoice['amount_collected_paid'],0)); $pay_247 += $invoice['amount_collected_paid'];} else {echo "0.00"; } ?></td>
         <td ><?php //if($invoice['amount_collected_paid'] > 0){ echo round($invoice['amount_collected_paid'],0); $pay_sf += $invoice['amount_collected_paid']; } else {echo "0.00";} ?></td>

         <?php  //$count = $count+1;  ?>

      </tr>
      <?php// }} ?>

      <tr>
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php //echo $sum_no_of_booking;?></td>
         <td><?php //echo round($tds,0);?></td>
         <td><?php //echo abs(round($pay_247,0));?></td>
         <td><?php //echo abs(round($pay_sf,0));?></td> 
        
      </tr>
   </tbody>-->
   
</table>
<?php 
    if(isset($bank_statement)) { ?>
    <br>
     <p><h2>Bank Transactions</h2></p>
      <table class="table table-bordered  table-hover table-striped data"  >
        <thead>
          <tr>
             <th>No</th>
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
                   <td><?php  echo $count;$count++; if($value['is_advance'] ==1){?> <p id="advance_text">Advance</p><?php }?></td>
               <td><?php echo $value['transaction_date']; ?></td>
               <td><?php echo $value['description']; ?></td>
               <td><?php echo round($value['credit_amount'],0); if($value['is_advance'] ==0){ $credit_amount += intval($value['credit_amount']); } ?></td>       
               <td><?php echo round($value['debit_amount'],0);  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php echo round($value['tds_amount'],0); $tds_amount += intval($value['tds_amount']); ?></td>
               <td><?php echo $value['invoice_id']; ?></td>
               <td><?php echo $value['bankname']; ?> / <?php echo $value['transaction_mode']; ?></td>          
           <?php } ?>
           
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php echo round($credit_amount,0);?></td>
             <td><?php echo round($debit_amount,0);?></td>
             <td><?php echo round($tds_amount,0);?></td>
             <td></td>
              <td></td>
             </tr>
       </tbody>
      </table>
    
<?php } ?>
 <br>
 

    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0){ echo sprintf("%.2f",$final_settlement);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($final_settlement < 0){ echo abs(sprintf("%.2f",$final_settlement));} else {echo 0;} ?></h4></p>
</div>

<script>
    $("#datatable").bind("DOMSubtreeModified", function() {
        $('#datatable tr span.satteled_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
    });
    var invoice_table = null;
    $(document).ready(function () {
       loaddataTable();
    });
    
    function loaddataTable(){ 
        invoice_table = $('#datatable').DataTable({
         "processing": true, //Feature control the processing indicator.
         "serverSide": true, //Feature control DataTables' server-side processing mode.
         "order": [[ 1, "desc" ]], //Initial no order.
         "pageLength": 50,
          dom: 'lBfrtip',
         "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
          buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Invoice',
                    exportOptions: {
                       columns: [1,2,3,4,7,8,9,10],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
         // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url();?>employee/accounting/get_invoice_searched_data",
                type: "POST",
                data: function(d){

                         d.request_type = "sf_invoice_summary";
                         d.vendor_partner = 'vendor';
                         d.vendor_partner_id = '<?php echo $this->session->userdata('service_center_id'); ?>';
                         d.settle = '2';
                 }

            },

            //Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [0,1,2,3,4,5,6,7,8,9,10], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#datatable tr span.satteled_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
            }

        });
    }
    
   
</script>