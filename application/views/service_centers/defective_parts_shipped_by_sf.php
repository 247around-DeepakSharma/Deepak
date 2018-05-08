<?php if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
} ?>
<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
        }
        ?>
    <div class="row">
<?php } ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Defective Parts Shipped By SF</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
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
                            <th colspan="2" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spare_parts as $key => $row) { ?>
                            <tr style="text-align: center;">
                                <td>
                                    <?php echo $sn_no; ?>
                                </td>
                                <td>
                                    <a  style="color:black" href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                </td>
                                <td>
                                    <?php echo $row['name']; ?>
                                </td>
    <!--                                    <td>
                                    <?php //echo $row['age_of_booking'];  ?>
                                </td>-->
                                <td>
                                    <?php echo $row['defective_part_shipped']; ?>
                                </td>
                                <td>
                                    <?php echo $row['courier_name_by_sf']; ?>
                                </td>
                                <td>
                                    <?php echo $row['awb_by_sf']; ?>
                                </td>
                                <td>
                                    <?php if (!is_null($row['defective_part_shipped_date'])) {
                                        echo date("d-m-Y", strtotime($row['defective_part_shipped_date']));
                                    } ?>
                                </td>

                                <td>
                                <?php echo $row['remarks_defective_part_by_sf']; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['defective_part_shipped'])) { ?>
                                        <div >
                                            <a onclick="return confirm_received()" class="btn btn-sm btn-primary" id="defective_parts" href="<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $row['booking_id']; ?>/<?php echo $row['partner_id']; ?>" <?php echo empty($row['defective_part_shipped']) ? 'disabled="disabled"' : '' ?>>Received</a></td></div>
                                            <?php } ?>
                                <td>
                                    <?php if (!empty($row['defective_part_shipped'])) { ?>
                                        <div class="dropdown" >
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-danger" type="button" data-toggle="dropdown">Reject
                                                <span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="right: 0px;left: auto;">
                                                <?php foreach ($internal_status as $value) { ?>
                                                    <li><a href="<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $row['booking_id']; ?>/<?php echo urlencode(base64_encode($row['partner_id'])); ?>/<?php echo urlencode(base64_encode($value->status)); ?>"><?php echo $value->status; ?></a></li>
                                                    <li class="divider"></li>
                                                <?php } ?>

                                            </ul>

                                        </div>
                                    <?php } ?>
                                </td>


                            </tr>
                        <?php $sn_no++;
                    } ?>
                    </tbody>
                </table>
                <div class="custom_pagination" style="margin-left: 16px;" > 
                    <?php
                        if (isset($links)) {
                            echo $links;
                        }
                    ?>
                </div>
        </div>
    </div>
</div>
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
}

</script>