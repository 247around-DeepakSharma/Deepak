
<div class="row">
    <div class="col-md-12" style="font-size: 18px;">
        <div class="text-center" >
            Amount Overdue: Rs. 
            <?php if($total_charges >= 0){ ?>
                    <b class="text-success bb_counter"><?php echo $total_charges; ?></b>
            <?php }else if($total_charges < 0){ ?>
                    <b class="text-danger bb_counter"><?php echo $total_charges; ?></b>
            <?php } ?>
        </div>
    </div>
</div>
<hr>
<table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th >Order In-Transit In <?php echo date("M", strtotime($in_transit_charges[2][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $in_transit_charges[2][0]->total_inTransit_order; ?></td>
            <th >In-Transit Amount In <?php echo date("M", strtotime($in_transit_charges[2][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <span class="bb_counter"><?php echo $in_transit_charges[2][0]->cp_in_transit_charge; ?></span></td>
            <th >Order Delivered In <?php echo date("M", strtotime($delivered_charges[2][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $delivered_charges[2][0]->total_delivered_order; ?></td>
            <th>Delivered Amount In <?php echo date("M", strtotime($delivered_charges[2][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <span class="bb_counter"><?php echo $delivered_charges[2][0]->cp_delivered_charge; ?></span></td>
        </tr>
        <tr>
            <th >Order In-Transit In <?php echo date("M", strtotime($in_transit_charges[1][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $in_transit_charges[1][0]->total_inTransit_order; ?></td>
            <th>In-Transit Amount In <?php echo date("M", strtotime($in_transit_charges[1][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <span class="bb_counter"><?php echo $in_transit_charges[1][0]->cp_in_transit_charge; ?></span></td>
            <th >Order Delivered In <?php echo date("M", strtotime($delivered_charges[1][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $delivered_charges[1][0]->total_delivered_order; ?></td>
            <th>Delivered Amount In <?php echo date("M", strtotime($delivered_charges[1][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i><span class="bb_counter"> <?php echo $delivered_charges[1][0]->cp_delivered_charge; ?></span></td>
        </tr>
        <tr>
            <th >Order In-Transit In <?php echo date("M", strtotime($in_transit_charges[0][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $in_transit_charges[0][0]->total_inTransit_order; ?></td>
            <th >In-Transit Amount In <?php echo date("M", strtotime($in_transit_charges[0][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <span class="bb_counter"><?php echo $in_transit_charges[0][0]->cp_in_transit_charge; ?></span></td>
            <th >Order Delivered In <?php echo date("M", strtotime($delivered_charges[0][0]->month)); ?></th>
            <td class="text-center bb_counter"><?php echo $delivered_charges[0][0]->total_delivered_order; ?></td>
            <th>Delivered Amount In <?php echo date("M", strtotime($delivered_charges[0][0]->month)); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <span class="bb_counter"><?php echo $delivered_charges[0][0]->cp_delivered_charge; ?></span></td>
        </tr>
    </thead>
</table>
