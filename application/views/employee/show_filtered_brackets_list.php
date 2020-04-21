<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" class="jumbotron" style="text-align: center;"><b>Requested Brackets</b></td>
            <td colspan="3" class="jumbotron" style="text-align: center;"><b>Shipped Brackets</b></td>
            <td colspan="3" class="jumbotron" style="text-align: center;"><b>Received Brackets</b></td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th class="jumbotron">S.N.</th>
            <th class="jumbotron" >Order ID</th>
            <th class="jumbotron" style="width:15%">Received From</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>-->
            <th class="jumbotron">Less Than 32 Inch</th>
            <th class="jumbotron">32 Inch & Above</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>-->
            <th class="jumbotron">Total</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>-->
            <th class="jumbotron">Less Than 32 Inch</th>
            <th class="jumbotron">32 Inch & Above</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>-->
            <th class="jumbotron">Total</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>-->
            <th class="jumbotron">Less Than 32 Inch</th>
            <th class="jumbotron">32 Inch & Above</th>
<!--            <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>-->
            <th class="jumbotron">Total</th>
            <th class="jumbotron" style="padding:1px;text-align: center">Date</th>
            <!--<th class="jumbotron" style="width:20%">Given To</th>-->
            <th style="text-align: center" colspan="2" class="jumbotron">Action</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($brackets as $key => $value) {
            $is_shipped = $value['is_shipped'];
            $is_received = $value['is_received'];
            $style = "";
            if ($is_shipped == 0 && $is_received == 0) {
                $style = 'style="background-color:#ff8080"';
            } elseif ($is_shipped == 1 && $is_received == 0) {
                $style = 'style="background-color:#FFEC8B"';
            } elseif ($is_shipped == 1 && $is_received == 1) {
                $style = 'style="background-color:#4CBA90"';
            }
            $date = "";
            if ($value['order_date'] > 0) {
                $date = $value['order_date'];
            }
            if ($value['shipment_date'] > 0) {
                $date = $value['order_date'];
            }
            if ($value['received_date'] > 0) {
                $date = $value['received_date'];
            }
            ?>		
            <tr <?php echo $style ?>>
                <td ><?php echo ($key + 1) . '.' ?></td>
                <td ><a href="<?php echo base_url() ?>employee/inventory/show_brackets_order_history/<?php echo $value['order_id'] ?>" target="_blank"><?php echo $value['order_id'] ?></a></td>
                <td style="text-align: center;">
                    <?php echo $order_received_from[$key]['owner_name'] . '<br>' ?>
    <?php echo $order_received_from[$key]['name'] ?>
                </td>
<!--                <td style="text-align: center;"><?php //echo $value['19_24_requested'] ?></td>-->
                <td style="text-align: center;"><?php echo ($value['26_32_requested'] + $value['19_24_requested']);?></td>
                <td style="text-align: center;"><?php echo ($value['36_42_requested'] + $value['43_requested']);?></td>
<!--                <td style="text-align: center;"><?php //echo $value['43_requested'] ?></td>-->
                <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_requested'] ?></strong></td>
<!--                <td style="text-align: center;"><?php //echo $value['19_24_shipped'] ?></td>-->
                <td style="text-align: center;"><?php echo ($value['26_32_shipped'] + $value['19_24_shipped']);?></td>
                <td style="text-align: center;"><?php echo ($value['36_42_shipped'] + $value['43_shipped']);?></td>
<!--                <td style="text-align: center;"><?php //echo $value['43_shipped'] ?></td>-->
                <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_shipped'] ?></strong></td>
<!--                <td style="text-align: center;"><?php echo $value['19_24_received'] ?></td>-->
                <td style="text-align: center;"><?php echo ($value['26_32_received'] + $value['19_24_received']);?></td>
                <td style="text-align: center;"><?php echo ($value['36_42_received'] + $value['43_received']);?></td>
<!--                <td style="text-align: center;"><?php //echo $value['43_received'] ?></td>-->
                <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_received'] ?></strong></td>
                <td style="text-align: center;"><?php
                    $old_date = $date;
                    $old_date_timestamp = strtotime($old_date);
                    $new_date = date('j M, Y g:i A', $old_date_timestamp);
                    echo $new_date;
                    ?></td>
                <!--<td><?php //echo $order_given_to[$key] ?></td>-->
                <td>
                    <a href="<?php echo base_url(); ?>employee/inventory/get_update_requested_form/<?php echo $value['order_id'] ?>" class="btn btn-sm btn-primary" title="Update Requested" <?php if ($value['is_shipped'] == 1 || $value['active'] == 0) {
                        echo 'disabled=TRUE';
                    } ?> > <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="<?php echo base_url(); ?>employee/inventory/get_update_shipment_form/<?php echo $value['order_id'] ?>" class="btn btn-sm btn-primary" title="Update Shipment" style="margin-bottom: 3px;" <?php if ($value['active'] == 0) {
                        echo 'disabled=TRUE';
                    } ?>>  <i class="fa fa-truck" aria-hidden="true"></i></a>&nbsp;
                    <a href="<?php echo base_url(); ?>employee/inventory/get_update_receiving_form/<?php echo $value['order_id'] ?>" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Update Receiving" <?php if ($value['is_shipped'] != 1 || $value['active'] == 0) {
                        echo 'disabled=TRUE';
                    } ?> > <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>&nbsp;
                    <a href="<?php echo base_url(); ?>employee/inventory/uncancel_brackets_request/<?php echo $value['order_id'] ?>" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Un-Cancel Request" <?php if ($value['active'] == 1) {
            echo 'disabled=TRUE';
        } ?> > <i class="fa fa-undo" aria-hidden="true"></i></a>&nbsp;
                </td>



            </tr>
<?php } ?>
    </tbody>
</table>