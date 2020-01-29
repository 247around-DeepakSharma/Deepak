<div class = "container"> 
    <div class="panel-group">
        <div class="panel panel-info">
            <div class="panel-heading">
                   Asset List
                <a class="btn btn-primary pull-right btn-md" href ="<?php echo base_url();?>employee/assets/add_assets">Add Asset</a>
            </div>
            <div class="panel-body">
                <table class = "table table-condensed table-bordered table-striped table-responsive">
                    
                    <tr>
                        <th>S No</th>
                        <th>Assets Name</th>
                        <th>Serial Number</th>
                        <th>Employee Name</th>
                        <th>Create Date</th>
                        <th>Action</th>
                        <th>History</th>
                    </tr>
                <?php
                if (!empty($fetch_data)) {
                    foreach ($fetch_data as $key => $row) {
                        ?>
                        <tr> 
                            <td><?php echo ($key +1) ?></td>
                            <td><?php echo $row['assets_name']; ?></td>
                            <td><?php echo $row['serial_number']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo date("d-M-Y", strtotime($row['create_date'])); ?></td>
                            <td><a class="btn btn-primary btn-sm" href ="<?php echo base_url();?>employee/assets/update_asset/<?php echo $row['id'];?>">Update</a></td>
                            <td> <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="gethistory('<?php echo $row['id'];?>')" >View</button</td>
                        </tr>

    <?php }
} else { ?>
                    <tr>
                        <td>"no data found"</td>
                    </tr>
                    <?php
                }
                ?>
            </div>
        </div>
    </div> 
</div>
</table> 
                 <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">History of assets assigned</h4>
        </div>
        <div class="modal-body">
            <table class = "table table-condensed table-bordered table-striped table-responsive" id="history_table">
                    
                 
           </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
</body>
</html>
<script>
    function gethistory(asset_id){
        $.ajax({
            url : "<?php echo base_url(); ?>employee/assets/assigned_history/"+asset_id,
            type : "POST",
            success : function(data) {
                console.log(data);
                $("#history_table").html(data);
                $('#myModal').modal('toggle');
            },
            error : function(data) {
                // do something
            }
        });
    }
    
</script>

