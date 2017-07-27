<?php if(!empty($transaction_details)) { ?>  
<table class="table table-border table-striped table-responsive">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Invoice ID</th>
            <th>TDS Amount</th>
            <th>TDS Paid</th>
            <th>Transaction Mode</th>
            <th>Description</th>
            <th>Uploaded By</th>
        </tr>
    </thead>
    <tbody>
            <?php $invoices = explode(',', $transaction_details[0]['invoice_id']); 
                  $sn1 = 1; 
                  foreach($transaction_details as $transactions) { ?>
        <tr>
            <td><?php echo $sn1;?></td>
            <td> <?php if(!empty($invoices)){ ?> 
                    <table class="table table-border table-striped table-condensed table-responsive">
                        <?php foreach( $invoices as $val) { ?> 
                        <tr>
                            <td><a class="get_invoice_id_data" href="javascript:void(0)" data-id="<?php echo $val; ?>"><?php echo $val; ?></a></td>
                        </tr>
                        <?php } ?>
                    </table>
                <?php }else { ?> 
                    Invoice Id Not Available
                <?php } ?>
                
            </td>
            <td><?php echo $transactions['tds_amount'] ?></td>
            <td><?php echo $transactions['tds_paid'] ?></td>
            <td><?php echo $transactions['transaction_mode'] ?></td>
            <td><?php echo $transactions['description'] ?></td>
            <td><?php echo $transactions['agent_name'] ?></td>

        </tr>
            
<?php $sn1++;} ?>
        
    </tbody>
</table>
<script>
    $(document).ready(function(){
        $('.get_invoice_id_data').click(function(){
            var invoice_id = $.trim($(".get_invoice_id_data").attr("data-id"));
            get_invoice_data(invoice_id)
        });

        function get_invoice_data(invoice_id){
            if (invoice_id){
                    $.ajax({
                        method: 'POST',
                        data: {invoice_id: invoice_id},
                        url: '<?php echo base_url(); ?>employee/accounting/search_invoice_id',
                        success: function (response) {
                            //console.log(response);
                            $("#open_model").html(response);   
                            $('#invoiceDetailsModal').modal('toggle');

                        }
                    });
                }else{
                    console.log("Contact Developers For This Issue");
                }
        }
    });
</script>
<?php } else{ ?> 
<div class="text-center text-danger">No Data Found</div>
<?php } ?>
