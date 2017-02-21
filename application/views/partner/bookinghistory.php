<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.7.1.custom.min.js"></script>

<script>
    $(document).ready(function ()
    {
        $('#for_appliance').hide();
        $('#for_user').show();
        $('#for_user_page').show();
        $("#appliance_toogle_button").click(function ()
        {
            $("#for_appliance").toggle();
            $("#for_user").toggle();
            $('#for_user_page').toggle();

        });

    });
</script>


<div id="page-wrapper" style="width:100%;"> 
    <div class="">
        <div class="row">
            <div id="for_user">
                <div class="panel">
                    <div class="panel-heading"><h3><b>Booking History</b></h3></div>
                </div>


                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="jumbotron">No.</th>
                            
                            <th class="jumbotron">Order Id</th>

                            <th class="jumbotron">Booking ID</th>

                            <th class="jumbotron">Name</th>

                            <th class="jumbotron">Appliance</th>

                            <th class="jumbotron">Booking Date</th>

                            <th class="jumbotron">Status</th>
                            <th class="jumbotron">View</th>
                            <th class="jumbotron">More Action</th>

                        </tr>

                    </thead>

                    <?php
                    if (isset($data[0]['booking_id'])) {
                        $count = 1;
                        ?>
                        <?php foreach ($data as $key => $row) { ?>

                            <tr>

                                <td><?php
                                    echo $count;
                                    $count++;
                                    ?>.</td>
                                
                                <td><?= $row['order_id']; ?></td>
                                
                                <td><?= $row['booking_id']; ?></td>

                                <td><?= $row['customername']; ?></td>

                                <td><?= $row['services']; ?></td>

                                <td><?= $row['booking_date']; ?></td>

                                <td><?php echo $row['current_status']; ?></td>

                                <td>
                                    <?php
                                    echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "partner/booking_details/$row[booking_id]/" . $this->session->userdata('partner_id') . " target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                                </td>
                                <td>
                                            <?php
                                            switch ($row['current_status']) {
                                                case 'Pending':
                                                case 'Rescheduled':
                                                    $view = 'partner/pending_booking/0/0/' . $row['booking_id'];
                                                    break;

                                                case 'Cancelled':
                                                    $view = 'partner/closed_booking/Cancelled/0/' . $row['booking_id'];
                                                    break;

                                                case 'Completed':
                                                    $view = 'partner/closed_booking/Completed/0/' . $row['booking_id'];
                                                    break;

                                                default:
                                                    $view = 'partner/pending_booking/0/0/' . $row['booking_id'];
                                                    break;
                                            }
                                            ?>


                                            <a href="<?php echo base_url() . $view; ?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>


                                        </td>

                            </tr>
                            <?php
                        }
                    }
                    ?>

                </table>

            </div>
        </div>
        <div style="margin-left:35px;">
            <?php if (!empty($links)) { ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php
                    if (isset($links)) {
                        echo $links;
                    }
                    ?></div> <?php } ?>
        </div>

    </div>
</div>