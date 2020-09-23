
<div class="row">
    <div class="col-md-12" style="font-size: 18px;">
        <table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th>Auto Acknowledge Amount</th>
            <th>Manual Acknowledge Amount</th>
            <th>In transit  Amount</th>
            <th>Delivered Amount</th>
            <th>Disputed Amount</th>
            <th>Invoice Generated But<br> Not Settled </th>
            <th>Amount Overdue</th>
        </tr>
    </thead>
    <tbody>
        <td><?php echo sprintf("%01.0f",$total_charges['cp_auto_ack']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['cp_manual_ack']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['cp_transit']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['cp_delivered']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['cp_disputed']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['unbilled']) ?> </td>
        <td><?php echo sprintf("%01.0f",$total_charges['total_balance']) ?> </td>
    </tbody>
        </table>
    </div>
</div>
<hr>
<table class="table table-striped table-bordered table-hover hidden" style="font-size:13px">
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
