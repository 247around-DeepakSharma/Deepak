<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>

<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
            }
            ?> 
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h1 class="panel-title" >Courier Lost Spare Parts Details</h1>
                </div>
                <div class="panel-body">
                    <?php //echo "<pre>";print_r($spare_parts);exit; ?>
                    <table id="delivered_parts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Booking Id</th>
                                <th class="text-center">User<br>Name</th>
                                <th class="text-center">Requested<br>Part Number</th>
                                <th class="text-center">Requested<br>Part Name</th>                                
                                <th class="text-center">Requested<br>Part Type</th>
                                <th class="text-center">Requested<br>Quantity</th>
                                <th class="text-center">Shipped<br>Part Number</th>
                                <th class="text-center">Shipped<br>Part Name</th>                                
                                <th class="text-center">Shipped<br>Part Type</th>
                                <th class="text-center">Shipped<br>Quantity</th>
                                <th class="text-center">AWB Number<br>By Partner</th>
                                <th class="text-center">Courier Charges<br>By Partner</th>
                                <th class="text-center">Remarks<br>By Partner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($spare_parts)) { foreach($spare_parts as $sno => $spare_part) { ?>
                            <tr> 
                                <td><?php echo ++$sno; ?></td>
                                <td>
                                    <a href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($spare_part['booking_id'])); ?>"  title='View'><?php echo $spare_part['booking_id']; ?></a>
                                </td>
                                 <td>
                                    <?php echo $spare_part['name']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['part_number']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['parts_requested']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['parts_requested_type']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['quantity']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['shipped_part_number']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['parts_shipped']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['shipped_parts_type']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['shipped_quantity']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['awb_by_partner']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['courier_price_by_partner']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['remarks_by_partner']; ?>
                                </td>
                            </tr>
                            <?php } } else { ?>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if (isset($links)) echo $links; ?></div>
</div>
