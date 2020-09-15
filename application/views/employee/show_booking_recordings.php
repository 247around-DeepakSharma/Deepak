<div class="booking_recordings_div">   
    <h1 style='font-size:24px;'>Call Recordings</h1>
    <div class="table-responsive">
        <table  class="table table-striped table-bordered">
            <tr>
                <th class="jumbotron" >S.N</th>
                <th class="jumbotron" >Date</th>
                <th class="jumbotron" >Agent Name</th>
                <th class="jumbotron" >Agent profile</th>
                <th class="jumbotron" >Recording</th>
            </tr>
            <?php foreach ($data as $key => $row) { ?>
                <tr>
                    <td><?php echo ($key + 1) . '.'; ?></td>                    
                    <td><?php echo date("d-M-Y", strtotime($row['create_date'])); ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['groups']; ?></td>
                    <td><a href="<?php echo $row['recording_url']; ?>" target="_blank"><span class="fa fa-microphone fa-2x"></span></a></td>                    
                </tr>
            <?php } ?>
        </table>
    </div>
</div>