<style>
    .table>tbody>tr>td, .table>tbody>tr>th, 
    .table>tfoot>tr>td, .table>tfoot>tr>th, 
    .table>thead>tr>td, .table>thead>tr>th{
        padding: 4px;
    }
</style>
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
        <table class="table table-striped table-bordered table-hover text-center" style="font-size:12px">
            <thead>
            <th class="text-center">Month</th>
            <th class="text-center">Completed Booking</th>
            <th class="text-center">Cancelled booking</th>
        </thead>
        <tbody>
            <?php foreach ($bookings_count as $val) { ?> 
                <tr>
                    <td><?php echo $val['month']; ?></td>
                    <td><?php echo $val['completed']; ?></td>
                    <td><?php echo $val['cancelled']; ?></td>
                </tr>
            <?php } ?>
                <tr>
                    <td style="border-left:0px;border-right:0px;"></td>
                    <td style="border-left:0px;border-right:0px;"></td>
                    <td style="border-left:0px;border-right:0px;"></td>
                </tr>
                <tr>
                    <td rowspan='2' class="text-center" style="padding-top: 16px;">
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
        </tbody>
        </table> 
    </div>
</div>
