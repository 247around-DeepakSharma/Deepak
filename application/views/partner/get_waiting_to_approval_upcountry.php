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
        <?php
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('error') . '</strong>
                   </div>';
        }
        ?>
    <div class="row">
<?php } ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Approve/Reject Upcountry Charges</h2>
             <div class="pull-right"><a style="background: #2a3f54; border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_waiting_upcountry_bookings"  class="btn btn-sm btn-primary">Download</a></div>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <table class="table table-bordered table-hover table-striped" id="waiting_upcountry_charges_table" style=" z-index: -1;position: static;">
                <thead>
                    <tr>
                        <th class="text-center">S.N</th>
                        <th class="text-center">Booking ID</th>
                        <th class="text-center">Call Type</th>
                        <th class="text-center">Customer Name</th>
                        <th class="text-center">Appliance</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Capacity</th>
                        <th class="text-center">Address</th>
                        <th class="text-center">Upcountry Distance</th>
                        <th class="text-center">Upcountry Charges</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn_no = 1;
                    foreach ($booking_details as $key => $row) { ?>
                        <tr style="text-align: center;">
                            <td>
                                <?php echo $sn_no; ?>
                            </td>
                            <td>
                                <a style="color:blue;" href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                            </td>
                            <td>
                                <?php echo $row['request_type']; ?>
                            </td>

                            <td>
                                <?php echo $row['name']; ?>
                            </td>

                            <td>
                                <?php echo $row['services']; ?>
                            </td>
                            <td>
                                <?php echo $row['appliance_brand']; ?>
                            </td>
                            <td>
                                <?php echo $row['appliance_category']; ?>
                            </td>
                            <td>
                                <?php echo $row['appliance_capacity']; ?>
                            </td>
                            <td>
                                <?php echo $row['booking_address'] . ", " . $row['city'] . ", Pincode - " . $row['booking_pincode'] . ", " . $row['state']; ?>
                            </td>
                            <td>
                                <?php echo $row['upcountry_distance'] . " KM"; ?>
                            </td>
                            <td>
                                <?php echo sprintf("%0.2f",$row['upcountry_distance'] * $row['partner_upcountry_rate']); ?>
                            </td>
                            <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="border: none;background: none;position: inherit;z-index: 100;min-width: 70px;">
                                                    <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                                    <li style="color: #fff;">
                                                        <a href="<?php echo base_url() ?>partner/upcountry_charges_approval/<?php echo $row['booking_id'] ?>/1" class="btn btn-md btn-success" style="color:#fff;margin: 0px;padding: 5px 5.5px;">Approve</a></li>
                                                    <li style="color: #fff;margin-top:5px;">
                                                        <a style="color:#fff;margin: 0px;padding: 5px 11px;" href="<?php echo base_url() ?>partner/reject_upcountry_charges/<?php echo $row['booking_id'] ?>/1" class="btn btn-md btn-danger">Reject</a>
                                                    </li>
                                           </div>
                                                </ul>
                                            </div>
                                        </td>
                            
                        </tr>
                        <?php $sn_no++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>        
<div class="clearfix"></div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script>
    var table = $('#waiting_upcountry_charges_table').DataTable(
            {
                 "pageLength": 50
             }
                     );
    </script>
    <style>
.dropdown-backdrop{
    display: none;
}
        </style>