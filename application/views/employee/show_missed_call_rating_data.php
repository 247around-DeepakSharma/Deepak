<div class="page-wrapper" >
    <div class="missed_call_rating_history" style="padding: 20px;">
        <h3>Missed Call Rating</h3>
        <?php if (!empty($missed_call_rating_data)) { ?>
            <table class="table table-bordered table-hover table-responsive">
                <thead>
                    <tr>
                        <th> S.No.</th>
                        <th> Name</th>
                        <th> Phone Number</th>
                        <th> Status</th>
                        <th> Date</th>
                        <th>Call</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sn = 1;
                    foreach ($missed_call_rating_data as $key => $value) {
                        ?>
                        <tr> 
                            <td><?php echo $sn; ?></td>
                            <td> <?php echo $value['name']; ?> </td>
                            <td><?php echo $value['from_number'] ?></td>
                            <td><?php if($value['rating'] === 'good_rating'){?> 
                                <img src="<?php echo base_url(); ?>images/smile.png">
                                <?php } else if($value['rating']=== 'bad_rating'){ ?> 
                                <img src="<?php echo base_url(); ?>images/angry.png">
                                <?php } ?></td>
                            <td><?php echo $value['create_date'] ?></td>
                            <td><button type="button" onclick="outbound_call(<?php echo $value['from_number'] ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button></td>
                            <td><a class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/user/finduser?phone_number=<?php echo $value['from_number'] ?>"><i class="fa fa-bars" aria-hidden="true"></i></a></td>
                        </tr>
                        <?php
                        $sn++;
                    }
                    ?>   
                </tbody>
            </table>
        <?php } else { ?>
            <div><p class="text-danger text-center"><strong>No Data Found</strong></p></div>
                    <?php } ?>
    </div>
</div>
<script>
     function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

    }
</script>