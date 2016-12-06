<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size: 130%;">Brackets History</center></div>
        <div class="panel-body">
            <div class="span6 pull-right">
                <a href="<?php echo !empty($invoice_id[0]['invoice_id'])?'https://s3.amazonaws.com/bookings-collateral/invoices-excel/'.$invoice_id[0]['invoice_id']:''?>" class="btn btn-primary" <?php echo !empty($invoice_id[0]['invoice_id'])?'':'disabled'?>>Download Invoice</a>
            </div>
           
            <div class="clear"></div>
            <div class="col-md-4 form-group" >
                <label class="label label-default" style="font-size:100%;">Order ID</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo $order_id ?>"/>
            </div>
            <div class="col-md-4 form-group" >
                <label class="label label-default" style="font-size:100%;">Received From</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo isset($order_received_from)?$order_received_from:'' ?>"/>
            </div>
            <div class="col-md-4 form-group">
                <label class="label label-default" style="font-size:100%;">Given To</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo isset($order_given_to)?$order_given_to:'' ?>"/>
            </div>
            <div class="clear"></div>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">Action</th>
                        <th class="jumbotron">19 to 24 inch</th>
                        <th class="jumbotron">26 to 32 inch</th>
                        <th class="jumbotron">36 to 42 inch</th>
                        <th class="jumbotron">Total</th>
                        <th class="jumbotron">Agent</th>
                        <th class="jumbotron">Partner</th>
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
                                <td><?php echo $value['19_24_requested'] ?></td>
                                <td><?php echo $value['26_32_requested'] ?></td>
                                <td><?php echo $value['36_42_requested'] ?></td>
                                <td><?php echo $value['total_requested'] ?></td>
                                <td><?php echo $value['agent_name'] ?></td>
                                <td><?php echo $value['partner_name'] ?></td>
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
                                <td><?php echo $value['19_24_shipped'] ?></td>
                                <td><?php echo $value['26_32_shipped'] ?></td>
                                <td><?php echo $value['36_42_shipped'] ?></td>
                                <td><?php echo $value['total_shipped'] ?></td>
                                <td><?php echo $value['agent_name'] ?></td>
                                <td><?php echo $value['partner_name'] ?></td>
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
                                <td><?php echo $value['19_24_received'] ?></td>
                                <td><?php echo $value['26_32_received'] ?></td>
                                <td><?php echo $value['36_42_received'] ?></td>
                                <td><?php echo $value['total_received'] ?></td>
                                <td><?php echo $value['agent_name'] ?></td>
                                <td><?php echo $value['partner_name'] ?></td>
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