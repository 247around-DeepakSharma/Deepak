<?php
$this->db_location = $this->load->database('default1', TRUE, TRUE);
$this->db = $this->load->database('default', TRUE, TRUE);
?>
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
            <div id="for_user" style="width:90%;margin:50px;">
                <div class="panel" style="width:90%;margin:50px 0px 10px  50px;background-color: #2C9D9C; border-color: #2C9D9C;color:#fff">
                    <div class="panel-heading"><center><span style="font-size: 120%;"><b><?php echo ucfirst($data[0]['name']); ?></b></span></center></div>
                </div>


                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="jumbotron">No.</th>

                            <th class="jumbotron">Booking ID</th>

                            <th class="jumbotron">Name</th>

                            <th class="jumbotron">Appliance</th>

                            <th class="jumbotron">Booking Date</th>

                            <th class="jumbotron">Status</th>
                            <th class="jumbotron">View</th>

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

                                <td><?= $row['booking_id']; ?></td>

                                <td><?= $row['name']; ?></td>

                                <td><?= $row['services']; ?></td>

                                <td><?= $row['booking_date']; ?></td>

                                <td><?php echo $row['current_status']; ?></td>

                                <td>
                                    <?php
                                    echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/partner/booking_details/$row[booking_id]/" . $this->session->userdata('partner_id') . " target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
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