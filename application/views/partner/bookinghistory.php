<?php
$this->db_location = $this->load->database('default1', TRUE, TRUE);
$this->db = $this->load->database('default', TRUE, TRUE);
?>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.7.1.custom.min.js"></script>
<style type="text/css">
    table{
        width: 99%;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;    
        padding: 6px;
    }

    th{
        height: 50px;
        background-color: #4CBA90;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}


</style>
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
                <div class="panel panel-info" style="width:90%;margin:50px 0px 10px  50px;">
                    <div class="panel-heading"><center><span style="font-size: 120%;">Booking History: <b><?php echo ucfirst($data[0]['name']); ?></b></span></center></div>
                </div>


                <table>
                    <thead>
                        <tr>
                            <th>No.</th>

                            <th>Booking ID</th>

                            <th>Name</th>

                            <th>Appliance</th>

                            <th>Booking Date</th>

                            <th>Status</th>
                            <th>View</th>

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
                                    . "href=" . base_url() . "employee/partner/viewdetails/$row[booking_id]/" . $this->session->userdata('partner_id') . " target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
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