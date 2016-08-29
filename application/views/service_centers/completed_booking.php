<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> <?php echo $status." Booking" ?>  (<?php echo $count; ?>)</h2>
                </div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>247Around Booking Id</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>Service Name</th>
                                    <th>Booking Date</th>
                                    <th>Closing Remarks</th>
                                    <th>View</th>
                                   
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
                                                <?php echo $row['booking_id']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['customername'];?>
                                            </td>
                                            <td>
                                                <?php echo $row['booking_primary_contact_no']; ?>
                                            </td>
                                            <td>
                                                <?php echo  $row['services']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['booking_date']; ?> /
                                                    <?php echo $row['booking_timeslot']; ?>
                                            </td>
                                        
                                             <td data-popover="true" style="position: absolute; border:0px; width: 12%" data-html=true data-content="<?php echo $row['closing_remarks'];?>">
                                                <div class="marquee">
                                                    <div><span><?php echo $row['closing_remarks'];?></span></div>
                                                </div>
                                            </td>
                                            <td><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?php echo $row['booking_id']?>" target='_blank' title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                

                                        </tr>
                                        <?php $count++; } ?>
                            </tbody>
                        </table>
                    </div>
                     <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
                   
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
        height: 22px;
    
        position: relative;
        overflow: hidden;
        animation: marquee 5s linear infinite;
    }
    
    .marquee span {
        
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
