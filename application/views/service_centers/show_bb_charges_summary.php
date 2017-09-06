<div class="row">
    <div class="col-md-12" style="font-size: 18px;">
        <b>Charges Summary</b>
        <div class="pull-right">
            Total Amount : <b><?php echo $total_charges ; ?> Rs. </b>
        </div>
    </div>
</div>
<hr>
<table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th class="text-center">Month</th>
            <th class="text-center">Number</th>
            <th class="text-center">Amount ( Rs. )</th>
        </tr>
        <tr>
            <td class="text-center"><strong><?php echo date('M', strtotime($delivered_charges[0][0]->month)); ?></strong></td>
            <td class="text-center"><?php echo ($delivered_charges[0][0]->total_delivered_order + $in_transit_charges[0][0]->total_inTransit_order); ?></td>
            <td class="text-center"><?php echo ($delivered_charges[0][0]->cp_delivered_charge + $in_transit_charges[0][0]->cp_in_transit_charge); ?></td>
        </tr>
        <tr>
            <td class="text-center"><strong><?php echo date('M', strtotime($delivered_charges[1][0]->month)); ?></strong></td>
            <td class="text-center"><?php echo ($delivered_charges[1][0]->total_delivered_order + $in_transit_charges[1][0]->total_inTransit_order); ?></td>
            <td class="text-center"><?php echo ($delivered_charges[1][0]->cp_delivered_charge + $in_transit_charges[1][0]->cp_in_transit_charge); ?></td>
        </tr>
        <tr>
            <td class="text-center"><strong><?php echo date('M', strtotime($delivered_charges[2][0]->month)); ?></strong></td>
            <td class="text-center"><?php echo ($delivered_charges[2][0]->total_delivered_order + $in_transit_charges[2][0]->total_inTransit_order); ?></td>
            <td class="text-center"><?php echo ($delivered_charges[2][0]->cp_delivered_charge + $in_transit_charges[2][0]->cp_in_transit_charge); ?></td>
        </tr>
    </thead>
</table>