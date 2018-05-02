<div id="page-wrapper" >
    <div>
        <h3>Inventory Ledger Details</h3>
        <hr>
        <div class="stocks_table">
            <table class="table table-responsive table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Sender Name</th>
                        <th>Receiver Name</th>
                        <th>Spare Part Name</th>
                        <th>Quantity</th>
                        <th>Booking Id</th>
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
                        <td><?php echo $value['quantity'];?></td>
                        <td>
                            <a href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($value['booking_id'])) ; ?>">
                                <?php echo $value['booking_id'];?>
                            </a>
                        </td>
                        <td><?php echo date('d F Y H:i:s', strtotime($value['create_date'])) ; ?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
        </div>
    </div>
</div>