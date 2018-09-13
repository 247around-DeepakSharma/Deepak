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
            <h2>Defective Spares Pending on SF </h2>
            <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_sf_needs_to_send_parts"  class="btn btn-sm btn-primary">Download</a></div>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="sf_needs_to_send_table">
                    <thead>
                        <tr>
                            <th class="text-center">S.N</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Spare Details</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Challan</th>
                            <th class="text-center">Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spare_parts as $key => $row) { ?>
                            <tr style="text-align: center;">
                                <td>
                                    <?php echo $sn_no; ?>
                                </td>
                                <td>
                                    <a  style="color:blue" href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
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
                                    <?php echo $row['aging']; ?>
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
 var table = $('#sf_needs_to_send_table').DataTable(
          {
                    "pageLength": 50
                });
function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
}

</script>
    <style>
        .paging_simple_numbers{
    display: none;
}
        </style>