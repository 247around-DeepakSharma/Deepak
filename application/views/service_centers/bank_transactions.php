<div id="page-wrapper">
<?php 
   // if(isset($bank_statement)) { ?>
     <p><h2>Bank Transactions</h2></p>
      <table class="table table-bordered  table-hover table-striped data"   id="datatable">
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
       
<!--       <tbody>
           
           <?php //$count=1; $debit_amount=0; $credit_amount=0; $tds_amount=0; ?>
           <?php //foreach($bank_statement as $value){?>
               
                <tr id="<?php //echo "row".$count;?>">
                   <td><?php // echo $count;$count++; if($value['is_advance'] ==1){?> <p id="advance_text">Advance</p><?php //}?></td>
               <td><?php //echo $value['transaction_date']; ?></td>
               <td><?php //echo $value['description']; ?></td>
               <td><?php //echo round($value['credit_amount'],0); if($value['is_advance'] ==0){ $credit_amount += intval($value['credit_amount']); } ?></td>       
               <td><?php //echo round($value['debit_amount'],0);  $debit_amount += intval($value['debit_amount']); ?></td>
               <td><?php //echo round($value['tds_amount'],0); $tds_amount += intval($value['tds_amount']); ?></td>
               <td><?php //echo $value['invoice_id']; ?></td>
               <td><?php //echo $value['bankname']; ?> / <?php //echo $value['transaction_mode']; ?></td>          
           <?php //} ?>
           
           <tr>
             <td><b>Total</b></td>
             <td></td>
             <td></td>
             <td><?php// echo round($credit_amount,0);?></td>
             <td><?php //echo round($debit_amount,0);?></td>
             <td><?php //echo round($tds_amount,0);?></td>
             <td></td>
              <td></td>
             </tr>
       </tbody>-->
      </table>
    
<?php //} ?>
 
</div>

<style>
#advance_text {
    -ms-transform: rotate(330deg); /* IE 9 */
    -webkit-transform: rotate(330deg); /* Safari */
    transform: rotate(330deg); /* Standard syntax */
    color: red;
    font-weight: bold;
}
    
    
</style>
<script>
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
                       columns: [1,2,3,4,5,6,7],
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
                url: "<?php echo base_url();?>employee/accounting/get_payment_summary_searched_data",
                type: "POST",
                data: function(d){

                         d.request_type = "sf_bank_transaction";
                         d.vendor_partner = 'vendor';
                         d.vendor_partner_id = '<?php echo $this->session->userdata('service_center_id'); ?>';
                    }

            },

            //Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [0,1,2,3,4,5,6,7], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              //$('#datatable tr span.satteled_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90")  });
            }

        });
    }
    

</script>