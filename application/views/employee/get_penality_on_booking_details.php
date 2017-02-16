
<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title" >Penalty Removal Reason </h1>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center">Booking Id</th>
                        <th class="text-center">Penalty Date</th>
                        <th class="text-center">Penalty Remove Reason</th>
                        <th class="text-center">Remove Penalty</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php foreach ($penality_details as $key => $row) { ?>
                    <tr class="text-center">
                        <td>
                            <?php echo $row['booking_id']; ?>
                            <input type="hidden" name="booking_id[]" id="booking_id" value="<?php echo $row['booking_id']; ?>">
                        </td>
                        <td>
                            <?php echo date_format(date_create($row['create_date']),"d/m/Y");?>
                        </td>
                        <td>
                            <textarea rows="3" cols="40" name="penalty_remove_reason[]" placeholder="Enter Penalty removal reason" id="penalty_remove_reason"></textarea>
              
                        </td>
                        <td><input type="checkbox" name="id[]" value="<?php echo $row['id']; ?>"></td>
                        
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="status" id="status" value="<?php echo $status; ?>">

    </div>
</div>