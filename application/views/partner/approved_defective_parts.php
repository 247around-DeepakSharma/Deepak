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
                    <h2>Received Spares By <?php echo $this->session->userdata('partner_name') ?></h2>
                     <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_received_spare_by_partner"  class="btn btn-sm btn-primary">Download</a></div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped" id="approved_defective_parts_table">
                        <thead>
                            <tr>
                                <th class="text-center">S.N</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">Received Spare Parts</th>
                                <th class="text-center">Received Date</th>
                                <th class="text-center">AWB</th>
                                <th class="text-center">Courier Name</th>
                                <th class="text-center">SF Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spare_parts as $key => $row) { ?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                        <a style="color:blue"  href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                    </td>

                                    <td>
                                        <?php echo $row['defective_part_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if (!is_null($row['received_defective_part_date'])) {
                                            echo date("d-m-Y", strtotime($row['received_defective_part_date']));
                                        } ?>
                                    </td>
                                    <td>
                                        <?php echo $row['awb_by_sf']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_sf']; ?>
                                    </td>
                                    <td>
                                <?php echo $row['remarks_defective_part_by_sf']; ?>
                                    </td>
                                </tr>
                                    <?php $sn_no++;
                                } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var table = $('#approved_defective_parts_table').DataTable();
    </script>
