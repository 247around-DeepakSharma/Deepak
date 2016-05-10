<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>

<style type="text/css">
    table{
          width: 99%;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;
    }

    th{
        height: 50px;
        background-color: #4CBA90;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}


</style>

<div id="page-wrapper">
    <div class="">
        <div class="row">
            <div >
                <h1 align="left">SD Leads</h1>
                <table>
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Referred Date and Time</th>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Appliance</th>
                        <th>Brand</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>

                    </thead>

                    <?php foreach ($booking as $key => $row) { ?>

                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['Referred_Date_and_Time']; ?></td>
                                <td><?= $row['CRM_Remarks_SR_No']; ?></td>
                            <td><?= $row['Customer_Name']; ?></td>
                        <td><?= $row['Phone']; ?></td>
                        <td><?= $row['Product']; ?></td>
                        <td><?= $row['Brand']; ?></td>
                                <td><?= $row['Status_by_247around']; ?></td>

                        <td>
                            <?php echo "<a class='btn btn-sm btn-primary' "
                            . "href=" . base_url() . "employee/booking/viewdetails/" . $row['CRM_Remarks_SR_No'] . " target='_blank'>View</a>"; ?>
                        </td>
                    </tr>

                    <?php } ?>

                </table>

            </div>
        </div>
    </div>
</div>

