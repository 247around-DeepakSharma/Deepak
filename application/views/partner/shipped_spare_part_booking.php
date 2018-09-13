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
                    <h2>Spare Parts Shipped By <?php echo $this->session->userdata('partner_name'); ?>, Waiting For Confirmation From SF</h2>
                    <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>employee/partner/download_spare_part_shipped_by_partner_not_acknowledged"  class="btn btn-sm btn-primary">Download</a></div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped" id="shipped_spare_part_table">
                        <thead>
                            <tr>
                                <th class="text-center">S.N</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">Customer Name</th>
                                <th class="text-center">Shipped Parts</th>
                                <th class="text-center">Courier Name</th>
                                <th class="text-center">AWB</th>
                                <th class="text-center">Challan</th>
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
                                        <a style="color:blue;"  href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
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
                                        <?php  if(!empty($row['partner_challan_file'])) { ?> 
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $row['partner_challan_file']; ?>" target="_blank"><?php echo $row['partner_challan_number']?></a>
                                        <?php }
                                        else if(!empty($row['partner_challan_number'])) {
                                            echo $row['partner_challan_number'];
                                        }
?>
                                      </td>
                                    <td>
                                        <?php echo date("d-m-Y", strtotime($row['shipped_date'])); ?>
                                    </td>

                                    <td>
                                        <?php echo $row['remarks_by_partner']; ?>
                                    </td>

                                </tr>
                                <?php $sn_no++;
                            } ?>
                        </tbody>
                    </table>
                    <div class="custom_pagination" style="margin-left: 16px;" > 
                <?php if(isset($links)) { echo $links; } ?>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var table = $('#shipped_spare_part_table').DataTable(
            {
                    "pageLength": 50
                });
    </script>
