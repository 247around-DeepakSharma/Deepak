<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title" ><?php if (isset($data[0]['sf_upcountry_rate'])) { ?>Earned Upcountry Booking Details <?php } else {
    echo "Offered Upcountry Details";
}
?> <img style="width:137px; float: right;" src="<?php echo base_url(); ?>images/powered-by-google.png" /></h1>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center">Booking Id</th>
                    <th class="text-center">Rate</th>
                    <th class="text-center">Distance(Up & Down)</th>
                    <th class="text-center">Total Payout</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data) {
                    foreach ($data as $key => $row) { ?>
                        <tr class="text-center">
                            <td>
                                <?php echo $row['booking']; ?>
                            </td>
                            <td>
                                <?php if (isset($row['sf_upcountry_rate'])) {
                                    echo $row['sf_upcountry_rate'] . " PER KM";
                                } else if (isset($row['partner_upcountry_rate'])) {
                                    echo $row['partner_upcountry_rate'] . " PER KM";
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $row['upcountry_distance'] . " KM"; ?>
                            </td>
                            <td>
                                <i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row['upcountry_price']; ?>
                            </td>
                        </tr>
                    <?php }
                } ?>
            </tbody>
        </table>
    </div>
</div>