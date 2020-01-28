<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
}
?>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Spare Parts Shipped</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Booking Id</th>
                                <th class="text-center">User Name</th>
                                <th class="text-center">Parts Shipped</th>
                                <th class="text-center">Courier Name</th>
                                <th class="text-center">AWB</th>
                                <th class="text-center">Shipped Date</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spare_parts as $key => $row) { ?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                        <a  href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id'])); ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                    </td>
                                    <td>
                                        <?php echo $row['name']; ?>
                                    </td>
    <!--                                    <td>
                                        <?php //echo $row['age_of_booking'];  ?>
                                    </td>-->
                                    <td>
                                        <?php echo $row['parts_shipped']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_partner']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['awb_by_partner']; ?>
                                    </td>
                                    <td>
                                        <?php echo date("d-M-Y", strtotime($row['shipped_date'])); ?>
                                    </td>

                                    <td>
                                        <?php echo $row['remarks_by_partner']; ?>
                                    </td>

                                </tr>
                                <?php $sn_no++;
                            } ?>
                        </tbody>
                    </table>
                    <div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
