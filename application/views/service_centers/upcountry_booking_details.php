
<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Earned Upcountry Booking Details</h1>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
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
                <tbody>
                    <?php foreach ($data as $key => $row) { ?>
                    <tr class="text-center">
                        <td>
                            <?php echo $row['booking']; ?>
                        </td>
                        <td>
                            <?php echo $row['sf_upcountry_rate'] . " PER KM"; ?>
                        </td>
                        <td>
                            <?php echo $row['upcountry_distance'] . " KM"; ?>
                        </td>
                        <td>
                            <i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row['upcountry_price']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>