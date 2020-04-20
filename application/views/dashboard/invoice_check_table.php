<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>Booking ID</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($data as  $value) { ?>
        <tr>
            <td><a href="<?php echo base_url();?>employee/user/finduser?search_value=<?php echo $value['booking_id'];?>"><?php echo $value['booking_id'];?></td>
            <td><?php echo $value['status'];?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>