<div id="page-wrapper" >
    <div  style="margin-top:20px;">
        <div><h1>Brackets History</h1></div>
        <div >
            <div class="span6 pull-right">
                <a href="<?php echo !empty($data[0]['invoice_id'])?'https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/invoices-excel/'.$data[0]['invoice_id']:''?>" class="btn btn-primary" <?php echo !empty($data[0]['invoice_id'])?'':'disabled'?>>Download Invoice</a>
            </div>
            <div class="span6">
                <a href="<?php echo !empty($brackets[0]['shipment_receipt'])?'https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/misc-images/'.$brackets[0]['shipment_receipt']:''?>" target="_blank" class="btn btn-primary" <?php echo !empty($brackets[0]['shipment_receipt'])?'':'disabled'?>>Download Docket File</a>
            </div>
           
            <div class="clear"></div>
            <table class="table table-rsponsive" style="margin-top: 10px;">
                <tr>
                    <td><b>Order ID</b></td>
                    <td><?php echo $order_id ?></td>
                </tr>
                <tr>
                    <td><b>Received From</b></td>
                    <td><?php echo isset($order_received_from)?$order_received_from:'' ?></td>
                </tr>
                <tr>
                    <td><b>Name</b></td>
                    <td><?php echo isset($primary_contact_name)?$primary_contact_name:'' ?></td>
                </tr>
                <tr>
                    <td><b>Contact Number</b></td>
                    <td><?php echo isset($phone_number)?$phone_number:'' ?></td>
                </tr>
                <tr>
                    <td><b>Address</b></td>
                    <td><?php echo isset($order_received_from_address)?$order_received_from_address:'' ?></td>
                </tr>
            </table>
            <div class="clear"></div>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">Action</th>
                        <th class="jumbotron">Less Than 32 Inch</th>
                        <th class="jumbotron">32 Inch & Above</th>
                        <th class="jumbotron">Total</th>
                        <th class="jumbotron">Order Date</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($data as $key => $value) {
                        if ($value['new_state'] == 'Brackets_Pending') {
                            ?>	
                            <tr>
                                <td><strong>Requested</strong></td>
                                <td><?php echo ($value['26_32_requested'] + $value['19_24_requested']); ?></td>
                                <td><?php echo ($value['36_42_requested'] + $value['43_requested']); ?></td>
                                <td><?php echo $value['total_requested'] ?></td>
                                <td><?php 
                                    $old_date = $value['order_date'];
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j F, Y g:i A', $old_date_timestamp);  
                                    echo $new_date;?>
                                </td>

                            </tr>
                        <?php } elseif ($value['new_state'] == 'Brackets_Shipped') { ?>
                            <tr>
                                <td><strong>Shipped</strong></td>
                                <td><?php echo ($value['26_32_shipped'] + $value['19_24_shipped']); ?></td>
                                <td><?php echo ($value['36_42_shipped'] + $value['43_shipped']); ?></td>
                                <td><?php echo $value['total_shipped'] ?></td>
                                <td><?php 
                                    $old_date = $value['shipment_date'];
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j F, Y g:i A', $old_date_timestamp);  
                                    echo $new_date;?>
                                </td>

                            </tr>

                        <?php } elseif ($value['new_state'] == 'Brackets_Received') { ?> 

                            <tr>
                                <td><strong>Received</strong></td>
                                <td><?php echo ($value['26_32_received'] + $value['19_24_received']); ?></td>
                                <td><?php echo ($value['36_42_received'] + $value['43_received']); ?></td>
                                <td><?php echo $value['total_received'] ?></td>
                                <td>
                                    <?php 
                                    $old_date = $value['received_date'];
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j F, Y g:i A', $old_date_timestamp);  
                                    echo $new_date;?>
                                </td>

                            </tr>

                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>    

        </div>
    </div>
</div>