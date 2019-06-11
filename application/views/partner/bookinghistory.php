<div class="right_col" role="main">
    <div class="row" id="for_user">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Booking History</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="booking_history_table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Order Id</th>
                                    <th>Booking ID</th>
                                    <th>Name</th>
                                    <th>Appliance</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                    <th>View</th>
                                    <th>More Action</th>
                                    <th>Repeat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (isset($data[0]['booking_id'])) {
                                        $count = 1;
                                        ?>
                                <?php foreach ($data as $key => $row) { ?>
                                <tr>
                                    <td> <?php echo $count;$count++;?>.</td>
                                    <td><?= $row['order_id']; ?></td>
                                    <td><?= $row['booking_id']; ?></td>
                                    <td><?= $row['customername']; ?></td>
                                    <td><?= $row['services']; ?></td>
                                    <td><?= $row['booking_date']; ?></td>
                                    <td><?php echo $row['partner_internal_status']; ?></td>
                                    <td>
                                        <?php
                                            echo "<a class='btn btn-sm btn-primary' "
                                            . "href=" . base_url() . "partner/booking_details/$row[booking_id]/". " target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                            ?>
                                    </td>
                                    <td>
                                        <?php
                                            switch ($row['partner_internal_status']) {
                                                case 'Pending':
                                                case 'Rescheduled':
                                                    $view = 'partner/pending_booking/' . $row['booking_id'];
                                                    break;

                                                case 'Cancelled':
                                                    $view = 'partner/closed_booking/Cancelled/0/' . $row['booking_id'];
                                                    break;

                                                case 'Completed':
                                                    $view = 'partner/closed_booking/Completed/0/' . $row['booking_id'];
                                                    break;

                                                default:
                                                    $view = 'partner/pending_booking/' . $row['booking_id'];
                                                    break;
                                            }
                                            ?>
                                        <a href="<?php echo base_url() . $view; ?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>
                                    </td>
                                    <?php
                                    if($row['partner_internal_status'] == _247AROUND_COMPLETED){
                                        $today = strtotime(date("Y-m-d"));
                                        $closed_date = strtotime($row['closed_date']);
                                        $completedDays = round(($today - $closed_date) / (60 * 60 * 24));
                                        if($completedDays < _PARTNER_REPEAT_BOOKING_ALLOWED_DAYS){
                                            echo "<td><a class='btn btn-sm btn-primary' "
                                                . "href=" . base_url() . "employee/partner/get_repeat_booking_form/$row[booking_id]/". " target='_blank'title='view'><i class='fa fa-plus' aria-hidden='true'></i></a></td>";
                                        }
                                        else{
                                            echo "<td></td>";
                                       }
                                }
                                else{
                                    echo "<td></td>";
                                }
                                ?>
                                </tr>
                                <?php
                                    }
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(!empty($data)){ ?> 
                        <form  action="<?php echo base_url()."partner/booking_form"; ?>" method="POST"> 
                            <input type="hidden" name="phone_number" value="<?php echo $data[0]['phone_number']; ?>">

                            <?php $disabled = ""; //if(empty($this->session->userdata('status'))){
                               // $disabled = "disabled";
                           // }?>
                            <input type="submit" value="New Booking" <?php echo $disabled; ?>  class=" btn btn-md btn-primary col-md-offset-4">
                            <p class="col-md-offset-4" style="color:red;"><?php if(empty($this->session->userdata('status'))){echo PREPAID_LOW_AMOUNT_MSG_FOR_PARTNER; }?></p>
                        </form>
                        <?php }?>
                    <div style="margin-left:35px;">
                        <?php if (!empty($links)) { ?>
                        <div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php
                            if (isset($links)) {
                                echo $links;
                            }
                            ?></div>
                        <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
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