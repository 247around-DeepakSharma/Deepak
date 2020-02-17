<?php if(!empty($penalty_details)){ ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php if(!empty($remove_penalty_details)) { foreach($remove_penalty_details as $value) {?> 
        <h5 class="text-center text-danger">Penalty Removed For <b><?php echo $value['sf_name'];?></b> In Last Month = <?php echo $value['count'];?></h5>
        <?php }} ?>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center">Booking Id</th>
                        <th class="text-center">Penalty Date</th>
                        <th class="text-center">SF Name</th>
                        <th class="text-center">Remarks</th>
                        <th class="text-center">Penalty Remove Reason</th>
                        <th class="text-center">Remove Penalty</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php  foreach ($penalty_details as $key => $row) { ?>
                    <tr class="text-center">
                        <td>
                            <?php echo $row['booking_id']; ?>
                            <input type="hidden" name="booking_id[]" id="booking_id" value="<?php echo $row['booking_id']; ?>">
                        </td>
                        <td>
                            <?php echo date_format(date_create($row['create_date']),"d-M-Y");?>
                        </td>
                        <td><?php echo $row['name'];?></td>
                        <td><?php if(!empty($row['remarks'])){echo $row['remarks'];}else{ echo "No remarks Found";} ?></td>
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
<?php } else{
echo "penalty not found"; }
?>

<script>
    $(document).ready(function(){
        //variable to count number of checkbox checked
        var checkbox_selected = 0;
        $('input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == true){
                //checkbox is checked
                checkbox_selected++; 
            }
            else if($(this).prop("checked") == false){
                //checkbox is unchecked
                checkbox_selected--; 
            }

            if(checkbox_selected >=1)
            {
                $('#error_message').css('display','none');
            }
            else
            {
                $('#error_message').css('display','block');
            }
        });
    });
</script>