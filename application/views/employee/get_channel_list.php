<div class = "container"> 
    <div class="panel-group">
        <div class="panel panel-info">
            <div class="panel-heading">
                   Channel List 
                <a class="btn btn-primary pull-right btn-md" href ="<?php echo base_url();?>employee/partner/add_channel">Add Channel</a>
            </div>
            <div class="panel-body">
                <table class = "table table-condensed table-bordered table-striped table-responsive">
                    
                    <tr>
                        <th>S No</th>
                        <th>Partner Name</th>
                        <th>Channel Name</th>
                        
                        <th>Create Date</th>
                        <th>Action</th>
                        
                    </tr>
                <?php
                if (!empty($fetch_data)) {
                    foreach ($fetch_data as $key => $row) {
                        ?>
                        <tr> 
                            <td><?php echo ($key +1) ?></td>
                            <td><?php echo $row['public_name'];?>
                            <td><?php echo $row['channel_name']; ?></td>
                            <td><?php echo date('d-m-y', strtotime($row['create_date'])); ?></td>
                            <td> <a class="btn btn-primary btn-sm" href ="<?php echo base_url();?>employee/partner/update_channel/<?php echo $row['id'];?>">Update</a></td>
                        </tr>

    <?php }
} else { ?>
                    <tr>
                        <td>"no data found"</td>
                    </tr>
                    <?php
                }
                ?>
                    </table> 
            </div>
        </div>
    </div> 
</div>

                 
</body>
</html>

