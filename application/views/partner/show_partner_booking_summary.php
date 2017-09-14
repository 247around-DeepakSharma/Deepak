
<div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
        <table class="table table-striped table-bordered table-hover" style="font-size:13px">
            <thead>
            <th>Month</th>
            <th>Completed Booking</th>
            <th>Cancelled booking</th>
        </thead>
        <tbody>
            <?php foreach ($bookings_count as $val) { ?> 
                <tr>
                    <td><?php echo $val['month']; ?></td>
                    <td><?php echo $val['completed']; ?></td>
                    <td><?php echo $val['cancelled']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
        </table> 
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
        <table class="table table-striped table-bordered table-hover" style="font-size:13px;" >
            <tr>
                <td rowspan='2' class="text-center" style="padding: 25px;">
                    <strong>Escalation (%)</strong>
                </td>
                <td >
                    <strong>Installation</strong>
                </td>
                <td>
                    <strong>Repair</strong>
                </td>
            </tr>
            <tr>
                <td><?php echo round($escalation_percentage[0]['unique_installation_escalate_percentage'], 1) ?></td>
                <td><?php echo round($escalation_percentage[0]['unique_repair_escalate_percentage'], 1) ?></td>
            </tr>
        </table> 
    </div>
</div>
