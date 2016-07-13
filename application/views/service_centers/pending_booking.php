<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Booking (<?php echo $count; ?>)</h2>
                </div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Booking Id</th>
                                    <th>User Name</th>
                                    <th>Phone No.</th>
                                    <th>Service Name</th>
                                    <th>Booking Date</th>
                                    <th>Age Of Booking</th>
                                    <th>Status</th>
                                    <th>247around Remarks</th>
                                    <th>View</th>
                                    <th>Cancel</th>
                                    <th>Complete</th>
                                    <th>Job Card</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                    <?php foreach($bookings as $key =>$row){?>
                                        <tr>
                                            <td>
                                                <?php echo $count; ?>
                                            </td>
                                            <td>
                                                <?=$row->booking_id?>
                                            </td>
                                            <td>
                                                <?=$row->customername;?>
                                            </td>
                                            <td>
                                                <?= $row->booking_primary_contact_no; ?>
                                            </td>
                                            <td>
                                                <?= $row->services; ?>
                                            </td>
                                            <td>
                                                <?= $row->booking_date; ?> /
                                                    <?= $row->booking_timeslot; ?>
                                            </td>
                                            <td> <?= $row->age_of_booking." day"; ?></td>
                                            <td>
                                                <?php echo $row->current_status; ?>
                                            </td>
                                            <td data-popover="true" style="position: absolute; border:0px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                                <div class="marquee">
                                                    <div><span><?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?></span></div>
                                                </div>
                                            </td>
                                            <td><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?=$row->booking_id?>" target='_blank' title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                       
                                        <td>
                                           <a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>                    
                                            <td>
                                                <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-success' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                            </td>
                                            <td><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm' download><i class="fa fa-download" aria-hidden="true"></i></a></td>

                                        </tr>
                                        <?php $count++; } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="text-right">
                     <a href="#">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
                     </div>-->
                </div>
            </div>

            <!-- end  col-md-12-->
        </div>
    </div>
</div>

<style type="text/css">
    .marquee {
        height: 100%;
        width: 100%;
        color: red;
        overflow: hidden;
        position: relative;
    }
    
    .marquee div {
        display: block;
        width: 100%;
        height: 50px;
        z-index: -1;
        position: absolute;
        overflow: hidden;
        animation: marquee 5s linear infinite;
    }
    
    .marquee span {
        float: left;
        width: 50%;
    }
    
    @keyframes marquee {
        0% {
            left: 0;
        }
        100% {
            left: -100%;
        }
    }

</style>
<script type="text/javascript">
    $('body').popover({
        selector: '[data-popover]',
        trigger: 'click hover',
        placement: 'auto',
        delay: {
            show: 50,
            hide: 100
        }
    });
</script>
