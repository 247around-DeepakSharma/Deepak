<div id="page-wrapper">
    <div class="col-md-12 page-header">
        <div class="col-md-4"><span style="font-size: 16px;"><b>Invoice</b></span></div>
    <div class="col-md-4"><span style="font-size: 16px;"><b>Un-Billed Amount</span></b>
        <br/><span data-toggle="modal" data-target="#myModal2" style="color:red; font-size: 16px; cursor: pointer;" onclick="unbilledBooking();"><b>Rs. <?php echo round($unbilled_amount,0);?> <i class="fa fa-info-circle" aria-hidden="true"></i></b></span></div>
    <div class="col-md-4">
        <span style="font-size: 16px;"><b><?php if($invoice_amount['final_amount'] > 0){ echo "Un-Settle Amount";} else { echo "Balanced Amount";}?></b>
        </span><br/><span style="<?php if($invoice_amount['final_amount'] > 0){ echo "color:red;";} else { echo "color:green;";}?> 
                                        font-size: 16px;"><b>Rs.<?php echo round($invoice_amount['final_amount'],0);?></b></span></div>
       
        
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped data" id="invoice_table">
            <thead>
                <tr >
                    <th>S.No</th>
                    <th>Invoice ID</th>
                    <th>Invoice Date</th>
                    <th>Invoice Period</th>
                    <th>Bookings/ Parts</th>
                    <th>Total Invoice</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($invoice_array)) {
                        foreach ($invoice_array as $key => $invoice) {
                            ?>
                <tr <?php if ($invoice['settle_amount'] == 1) { ?> style="background-color: #90EE90; " <?php } ?>>
                    <td><?php echo ($key + 1); ?></td>
                    <td>
                        <?php echo $invoice["invoice_id"]; ?>
                    </td>
                    <td><?php echo date("jS M, Y", strtotime($invoice['invoice_date'])); ?></td>
                    <td><?php echo date("jS M, Y", strtotime($invoice['from_date'])) . " to " . date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
                    <td><?php echo $invoice['num_bookings'] . "/" . $invoice['parts_count']; ?></td>
                    <td><?php echo $invoice['total_amount_collected']; ?></td>
                    <td>
                        <ul style=" list-style-type: none;">
                            <?php if(!empty($invoice['invoice_file_main'])) { ?><li style="display: inline; font-size: 30px;"><a title="Main File"  href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/invoices-excel/<?php echo $invoice['invoice_file_main']; ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></li><?php } ?>
                             <?php if(!empty($invoice['invoice_detailed_excel'])) { ?><li style="display: inline;font-size: 30px; margin-left: 10px;"><a  href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/invoices-excel/<?php echo $invoice['invoice_detailed_excel']; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a></li><?php } ?>
                        </ul>
                    </td>
                    <?php ?>
                </tr>
                <?php
                    }
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>
 <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Unbilled Booking</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover table-striped data" id="unbilled_table">
                    <thead>
                    <th>Booking ID</th>
                    <th>Charges</th>
                    </thead>
                    <tbody>
                        <?php foreach ($unbilled_data as $key => $value) { ?>              
                        <tr>
                            <td><?php echo $value['booking_id']; ?></td>
                            <td><?php echo $value['partner_net_payable']; ?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
         
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
<script>
    $(document).ready(function () {
        $('#invoice_table').DataTable({
         
    
        });
    });
    
</script>
<style>
    .dataTables_filter{
        display:none;
    }
</style>