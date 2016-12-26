<?php $offset = $this->uri->segment(4); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.7.1.custom.min.js"></script>

<div id="page-wrapper">
    <div class="">
        <div class="row">
            <?php
            $data = search_for_key($Bookings);
            $count = 1;
            ?>
            <?php if (isset($data['FollowUp'])) { ?>
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Queries </h2>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th  class="jumbotron">S.No.</th>
                            <th  class="jumbotron">Order ID</th>
                            <th  class="jumbotron">247around Booking ID</th>
                            <th  class="jumbotron">User Name</th>
                            <th  class="jumbotron">Mobile</th>
                            <th  class="jumbotron">City</th>
                            <th  class="jumbotron">Booking Date</th>
                            <th  class="jumbotron">Status</th>
                            <th  class="jumbotron">View</th>
                        </tr>
                    </thead>
                    <?php
                    if ($offset == 0) {
                        $offset = 1;
                    } else {
                        $offset = $offset + 1;
                    }
                    ?>
                    <?php
                    foreach ($Bookings as $key => $row) {
                        if ($row->current_status == "FollowUp") {
                            ?>
                            <tr <?php if ($row->internal_status == "Missed_call_confirmed") { ?> style="background-color:rgb(0,255,0); color:#000;"<?php } ?> >
                                <td><?php echo $count . '.'; ?></td>
                                <td><?php echo $row->order_id; ?></td>
                                <td><?php echo $row->booking_id ?></td>
                                <td><?= $row->customername; ?></td>
                                <td><?= $row->booking_primary_contact_no; ?></td>
                                <td><?= $row->city; ?></td>
                                <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                                <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                                <td>
                                    <?php
                                    echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "partner/booking_details/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $count++;
                            $offset++;
                        }
                    }
                    ?>
                </table>
            <?php } if (isset($data['Pending'])) { ?>
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Pending Bookings </h2>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th  class="jumbotron">S.No.</th>
                                <th  class="jumbotron">Order ID</th>
                                <th  class="jumbotron">247around Booking ID</th>
                                <th  class="jumbotron">User Name</th>
                                <th  class="jumbotron">Mobile</th>
                                <th  class="jumbotron">City</th>
                                <th  class="jumbotron">Booking Date</th>
                                <th  class="jumbotron">Status</th>
                                <th  class="jumbotron">View</th>
                            </tr>
                        </thead>
                        <?php
                        if ($offset == 0) {
                            $offset = 1;
                        } else {
                            $offset = $offset + 1;
                        }
                        ?>
                        <?php
                        foreach ($Bookings as $key => $row) {
                            if ($row->current_status == "Pending" || $row->current_status == "Rescheduled") {
                                ?>
                                <tr id="row_color<?php echo $count; ?>">
                                    <td><?php echo $count . '.'; ?></td>
                                    <td><?php echo $row->order_id ?></td>
                                    <td><?php echo $row->booking_id ?></td>
                                    <td><?= $row->customername; ?></td>
                                    <td><?= $row->booking_primary_contact_no; ?></td>
                                    <td><?= $row->city; ?></td>
                                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                                    <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                                    <td>
                                        <?php
                                        echo "<a class='btn btn-sm btn-primary' "
                                        . "href=" . base_url() . "partner/booking_details/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                                $offset++;
                            }
                        }
                        ?>
                        <input type="hidden" id="total_no_rows" value="<?php echo $count; ?>">
                    </table>
                <?php } if (isset($data['Completed'])) { ?>
                    <br><div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Completed Bookings </h2>
                    </div>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th  class="jumbotron">S.No.</th>
                                <th  class="jumbotron">Order ID</th>
                                <th  class="jumbotron">247around Booking ID</th>
                                <th  class="jumbotron">User Name</th>
                                <th  class="jumbotron">Mobile</th>
                                <th  class="jumbotron">City</th>
                                <th  class="jumbotron">Closed Date</th>
                                <th  class="jumbotron">Status</th>
                                <th  class="jumbotron">View</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($Bookings as $key => $row) {
                            if ($row->current_status == "Completed") {
                                ?>
                                <tr>
                                    <td><?php echo $count . '.'; ?></td>
                                    <td><?php echo $row->order_id ?></td>
                                    <td><?php echo $row->booking_id ?></td>
                                    <td><?= $row->customername; ?></td>
                                    <td><?= $row->booking_primary_contact_no; ?></td>
                                    <td><?= $row->city; ?></td>
                                    <td><?php echo date('d-m-y', strtotime($row->booking_date)); ?> </td>
                                    <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                                    <td>
                                        <?php
                                        echo "<a class='btn btn-sm btn-primary' "
                                        . "href=" . base_url() . "partner/booking_details/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                <?php } if (isset($data['Cancelled'])) { ?>
                    <br><div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Cancelled Bookings </h2>
                    </div>
                    <table class="table table-striped table-bordered table-hover" style="margin-bottom:50px;">
                        <thead>
                            <tr>
                                <th  class="jumbotron">S.No.</th>
                                <th  class="jumbotron">Order ID</th>
                                <th  class="jumbotron">247around Booking ID</th>
                                <th  class="jumbotron">User Name</th>
                                <th  class="jumbotron">Mobile</th>
                                <th  class="jumbotron">City</th>
                                <th  class="jumbotron">Closed Date</th>
                                <th  class="jumbotron">Cancellation Reason</th>
                                <th  class="jumbotron">Status</th>
                                <th  class="jumbotron">View</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($Bookings as $key => $row) {
                            if ($row->current_status == "Cancelled") {
                                ?>
                                <tr>

                                    <td><?php echo $count . '.'; ?></td>
                                    <td><?php echo $row->order_id ?></td>
                                    <td><?php echo $row->booking_id ?></td>
                                    <td><?= $row->customername; ?></td>
                                    <td><?= $row->booking_primary_contact_no; ?></td>
                                    <td><?= $row->city; ?></td>
                                    <td><?php echo date('d-m-y', strtotime($row->booking_date)); ?> </td>
                                    <td><?php echo $row->cancellation_reason; ?></td>
                                    <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                                    <td>
                                        <?php
                                        echo "<a class='btn btn-sm btn-primary' "
                                        . "href=" . base_url() . "partner/booking_details/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                <?php }  ?>
                 <?php if(isset($data['Pending']) || isset($data['Cancelled']) || isset($data['FollowUp']) || isset($data['Completed'])){} else { 
                     $this->session->set_flashdata('error', 'User Not Exist');
                     redirect(base_url() . 'employee/partner/get_user_form');
                 }

                  ?>
            </div>
        </div>
    </div>
</div>
</div>
