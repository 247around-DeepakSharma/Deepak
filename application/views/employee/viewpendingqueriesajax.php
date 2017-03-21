<?php $offset = $this->uri->segment(7);  ?>
<table class="table table-bordered table-hover table-striped">

    <thead>
        <tr>
            <th>S No.</th>
            <th >Booking Id</th>

            <th>User Name</th>
            <th >Phone No.</th>
            <th >Service Name</th>

            <th class="hide_div" >Booking Date/Time</th>
            <?php if ($status != "Cancelled") { ?>
                <th class="hide_div" >Status</th>
            <?php } ?>
            <th class="hide_div">City</th>

            <th class="hide_div">Query Remarks</th>

            <?php if ($status != "Cancelled") { ?>
                <?php if ($p_av == PINCODE_NOT_AVAILABLE) { ?>
                    <th class="hide_div">Pincode</th>
                <?php } else { ?>
                    <th class="hide_div">Vendor Status</th>
                <?php }
            } ?>
            <th class="hide_div" >Call</th>
            <th class="hide_div">View</th>
<?php if ($status != "Cancelled") { ?>
                <th class="hide_div">Update</th>

                <th class="hide_div">Cancel</th>
            <?php } if ($status == "Cancelled") { ?>
                <th class="hide_div">Un-Cancel</th>
<?php } ?>

        </tr>

    </thead>
    <?php $count = 1;
    if ($offset == 0) {
        $offset = 1;
    } else {
        $offset = $offset + 1;
    } ?>
<?php foreach ($Bookings as $key => $row) { ?>

        <tr <?php if ($row->internal_status == "Missed_call_confirmed") { ?> style="background-color:rgb(162, 230, 162); color:#000;"<?php } ?> >
            <td><?php echo $offset; ?></td>

            <td><?= $row->booking_id; ?></td>
        <input type="hidden" id="<?php echo "service_id_" . ($key + 1); ?>"  value="<?php echo $row->service_id; ?>"/>
        <input type="hidden" id="<?php echo "pincode_" . ($key + 1); ?>" value="<?php echo $row->booking_pincode; ?>" />
        <td><a href="<?php echo base_url(); ?>employee/user/finduser/0/0/<?php echo $row->phone_number; ?>"><?php echo $row->customername; ?></a></td>
        <td class="hide_div"><a href="<?php echo base_url(); ?>employee/user/finduser/0/0/<?php echo $row->phone_number; ?>"><?php echo $row->booking_primary_contact_no; ?></a></td>
        <td class ="display_mobile" ><p onclick="call_on_phone('<?php echo $row->phone_number; ?>')" ><?php echo $row->phone_number; ?></p></td>
        <td><?= $row->services; ?></td>

        <td class="hide_div"><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
            <?php if ($status != "Cancelled") { ?>
            <td class="hide_div" id="status_<?php echo $row->booking_id; ?>">
            <?php
            echo $row->current_status;
            if ($row->current_status != $row->internal_status)
                echo " (" . $row->internal_status . ")";
            ?>
            </td>
        <?php } ?>
        <td class="hide_div"><?= $row->city; ?></td>


        <td class="hide_div"><?= $row->query_remarks; ?></td>
    <?php if ($status != "Cancelled") {
        if ($p_av == PINCODE_NOT_AVAILABLE) { ?>
                <td class="hide_div"><a href="javascript:void(0)" style="color: red;" onclick='form_submit("<?php echo $row->booking_id ?>")'><?php print_r($row->booking_pincode); ?></a></td>
        <?php } else if ($p_av == PINCODE_ALL_AVAILABLE || $p_av == PINCODE_AVAILABLE) { ?>
                <td class="hide_div">

                    <select id="<?php echo "av_vendor" . ($key + 1); ?>" style="max-width:100px;">
                        <option>Vendor Available</option>

                    </select>

                    <a href="javascript:void(0)" style="color: red; display:none" id="<?php echo "av_pincode" . ($key + 1); ?>" onclick='form_submit("<?php echo $row->booking_id ?>")'><?php print_r($row->booking_pincode); ?></a>

                </td>
                <?php }
            } ?>

        <td class="hide_div"><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
        </td>

        <td class="hide_div">
    <?php
    echo "<a class='btn btn-sm btn-primary' "
    . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    ?>
        </td>
            <?php if ($status != "Cancelled") { ?>
            <td class="hide_div" ><?php
            echo "<a class='btn btn-small btn-success btn-sm' href=" . base_url() . "employee/booking/get_edit_booking_form/$row->booking_id title='Update'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
            ?>
            </td>

            <td class="hide_div">
            <?php
            echo "<a class='btn btn-small btn-warning btn-sm' href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id/FollowUp title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
            ?>
            </td>
    <?php } if ($status == "Cancelled") { ?>
            <td class="hide_div">
        <?php
        echo "<a class='btn btn-sm btn-warning' "
        . "href=" . base_url() . "employee/booking/open_cancelled_query/$row->booking_id title='open'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
        ?>
            </td>
    <?php } ?>
    </tr>
    <?php $count++;
    $offset++;
}
?>

</table>
<?php if (!empty($links)) { ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if (isset($links)) {
        echo $links;
    } ?></div> <?php } ?>