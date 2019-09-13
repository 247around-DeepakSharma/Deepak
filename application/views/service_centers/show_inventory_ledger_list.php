<div id="page-wrapper" >
    <div>
        <h3>Inventory Ledger Details</h3>
        <hr>
        <div class="stocks_table">
            <table class="table table-responsive table-hover table-bordered table-striped" id="inventory_ledger">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Sender Name</th>
                        <th>Receiver Name</th>
                        <th>Spare Part Name</th>
                        <th>Spare Part Number</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Booking Id</th>
                        <th>Invoice Id</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brackets as $key => $value){?>
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo $value['sender'];?></td>
                        <td><?php echo $value['receiver'];?></td>
                        <td><?php echo $value['part_name'];?></td>
                        <td><?php echo $value['part_number'];?></td>
                        <td><?php echo $value['quantity'];?></td>
                        <td><?php  echo ($value['is_wh_ack']==1)  ? "Acknowledged" : "Not Acknowledged"; ?></td>
                        <td>
                            <a href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($value['booking_id'])) ; ?>">
                                <?php echo $value['booking_id'];?>
                            </a>
                        </td>
                        <td>
                            <?php echo $value['invoice_id'];?>
                        </td>
                        <td><?php echo date('d F Y H:i:s', strtotime($value['create_date'])) ; ?></td>
                    </tr>
                    <?php }?>
<!--                    <tr>
                        <th><b>Total Count <span class="badge"><i class="fa fa-info" title="Spare count calculated only for spare shipped by partner to wh and wh to sf only"></i></span></b></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><b><?php //if(isset($total_spare) && !empty($total_spare)) { echo $total_spare[0]['total_spare_from_ledger']; }?></b></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>-->
                </tbody>
            </table>
            <?php //if(!empty($links)){ ?><!--<div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;">--> <?php //if(isset($links)){echo $links;} ?><!--</div>--><?php //} ?>
        </div>
    </div>
</div>

<script>
 var time = moment().format('D-MMM-YYYY');
 $(document).ready(function() {
        $('#inventory_ledger').DataTable( {
            "processing": true,
            "serverSide": false,
            "pageLength": 50,
            dom: 'lBfrtip',
            "buttons": [
            {
            extend: 'excel',
            text: 'Export',
            exportOptions: {
              columns: [ 0, 1, 2,3,4, 5,6,7,8,9]
            },
            title: 'inventory_ledger_details_'+time,
            },
            ],
        } );
   } );
</script>