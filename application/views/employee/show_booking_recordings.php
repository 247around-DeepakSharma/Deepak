<div class="booking_recordings_div">   
    <h1 style='font-size:24px;'>Call Recordings</h1>
    <div class="table-responsive">
        <table  class="table table-striped table-bordered">
            <tr>
                <th class="jumbotron" >S.No</th>
                <th class="jumbotron" >Date</th>
                <th class="jumbotron" >Agent Name</th>
                <th class="jumbotron" >Agent Profile</th>
                <th class="jumbotron" >Recording</th>
            </tr>
            <?php if(empty($data)) { ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No Recording Available</td>
                </tr>
            <?php } ?>
            <?php foreach ($data as $key => $row) { ?>
                <tr>
                    <td><?php echo ($key + 1) . '.'; ?></td>                    
                    <td><?php echo date("d-M-Y", strtotime($row['create_date'])); ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['display_name']; ?></td>
                    <td><a href="<?php echo $row['recording_url']; ?>" target="_blank"><span class="fa fa-microphone fa-2x" style="display: block;float:left;" ></span></a></td>                    
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
