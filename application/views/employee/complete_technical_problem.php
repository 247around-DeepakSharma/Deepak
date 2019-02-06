<div class = "container">
    <div class="panel-group">
        <div class="panel panel-info">
            <div class="panel-heading">
             <h4>   Completion Technical Problem List
                <a class="btn btn-primary pull-right btn-md" href ="<?php echo base_url();?>employee/booking_request/add_new_completion_technical_problem">Add Completion Technical Problem</a>
             </h4>
            </div>
            <div class="panel-body">
                <table class = "table table-condensed table-bordered table-striped table-responsive">
                    <tr>
                        <th>S No</th>
                        <th>Service</th>
                        <th>Request Type</th>
                        <th>Symptom</th>
                        <th>Update</th>
                    </tr>
                    <?php
                        if (!empty($data)) {
                            foreach ($data as $key => $row) {
                                ?>
                    <tr>
                        <td><?php echo ($key +1) ?></td>
                        <td><?php echo $row['services'];?>
                        <td><?php echo $row['service_category']; ?></td>
                        <td><?php echo $row['completion_request_symptom'];?></td>
                        <td><a href="#" class="btn btn-md btn-success">Update</a></td>
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