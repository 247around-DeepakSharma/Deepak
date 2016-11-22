<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Brackets List</center></div>
        <div class="panel-body">

            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="4" class="jumbotron" style="text-align: center;"><b>Requested Brackets</b></td>
                        <td colspan="4" class="jumbotron" style="text-align: center;"><b>Shipped Brackets</b></td>
                        <td colspan="4" class="jumbotron" style="text-align: center;"><b>Received Brackets</b></td>
                        <td></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="width:20%">Received From</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="width:20%">Given To</th>
                        <th colspan="2" style="text-align: center;" class="jumbotron">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($brackets as $key=>$value){
                        $is_shipped = $value['is_shipped'];
                        $is_received = $value['is_received'];
                        $style="";
                        if($is_shipped == 0 && $is_received == 0){
                            $style='style="background-color:#ff8080"';
                        }elseif($is_shipped == 1 && $is_received == 0){
                            $style='style="background-color:#FFEC8B"';
                        }elseif($is_shipped == 1 && $is_received == 1){
                            $style='style="background-color:#4CBA90"';
                        }
                        ?>		
                    <tr <?php echo $style?>>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td ><a href="<?php echo base_url()?>employee/inventory/show_brackets_order_history/<?php echo $value['order_id']?>" target="_blank"><?php echo $order_received_from[$key]?></a></td>
                            <td style="text-align: center;"><?php echo $value['19_24_requested']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_requested']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_requested']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_requested']?></strong></td>
                            <td style="text-align: center;"><?php echo $value['19_24_shipped']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_shipped']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_shipped']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_shipped']?></strong></td>
                            <td style="text-align: center;"><?php echo $value['19_24_received']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_received']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_received']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_received']?></strong></td>
                            <td><?php echo $order_given_to[$key]?></td>
                            <td><a href="<?php base_url()?>get_update_shipment_form/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" title="Update Shipment">  <i class="fa fa-truck" aria-hidden="true"></i></a></td>
                            <td> <a href="<?php base_url()?>get_update_receiving_form/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" title="Update Receiving" <?php if($value['is_shipped'] != 1){echo 'disabled=TRUE';}?> > <i class="fa fa-shopping-cart" aria-hidden="true"></i></a></td>
                                


                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>