<div class="clearfix"></div>
<div class="row" >
    <div class="col-md-12 col-sm-12 col-xs-12" >
        <div class="x_panel" style="height: auto;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title" style="border-bottom: 0px solid #FFF;">
                    <h2>
                    <i class="fa fa-bars"></i> Search Result:- <!--<small>Float left</small>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Tracking ID</th>
                                <th>Appliance</th>
                                <th>Category</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>SF Charge</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($list as $key => $value){ ?>
                            <tr>
                                <td><?php echo ($key +1);?></td>
                                <td><a href="<?php echo base_url();?>/service_center/buyback/view_bb_order_details/<?php echo $value->partner_order_id;?>">
                                    <?php echo $value->partner_order_id;?></a>
                                </td>
                                <td><?php echo $value->partner_tracking_id;?></td>
                                <td><?php echo $value->services;?></td>
                                <td><?php echo $value->category;?></td>
                                <td><?php echo $value->order_date;?></td>
                                <td><?php echo $value->delivery_date;?></td>
                                <td><?php if($value->current_status == "Pending"){ echo $value->internal_status; } else { echo $value->current_status."(<strong> ".$value->internal_status."</strong>)";};?></td>
                                <td><?php echo ($value->cp_basic_charge + $value->cp_tax_charge);?></td>
                                <td>
                                    <?php if($value->current_status == "Pending"){?>
                                    <div class='dropdown'>
                                        <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions
                                        <span class='caret'></span></button>
                                        <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                                            <li role='presentation'><a role='menuitem' tabindex='-1' onclick="showConfirmDialougeBox('<?php echo base_url();?>service_center/buyback/update_received_bb_order/<?php echo 
                                                rawurlencode($value->partner_order_id) . "/" . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" .
                                                        rawurlencode($value->assigned_cp_id)?>')" >Received</a></li>
                                            <li role='presentation'><a role='menuitem' tabindex='-1'  onclick="showConfirmDialougeBox('<?php echo base_url();?>service_center/buyback/update_not_received_bb_order/<?php echo 
                                                rawurlencode($value->partner_order_id) . "/" . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" . rawurlencode($value->assigned_cp_id); ?>')" >Not Received</a></li>
                                            <li role='presentation'><a role='menuitem' tabindex='-1' target='_blank'
                                                href="<?php echo base_url();?>service_center/buyback/update_order_details/<?php  echo rawurlencode($value->partner_order_id) . "/" 
                                                    . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" . rawurlencode($value->assigned_cp_id);?>" >
                                                Broken/Wrong Product</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php }
                                ?>
                        </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>