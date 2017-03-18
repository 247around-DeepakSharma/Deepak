<div class="container-fluid" style="margin-top: 20px;">
    <div class="table-responsive">          
        <table class="table table-bordered">
            <thead>
                <tr class="info">
                    <th>File</th>
                    <th>Uploaded By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($latest_file as $key=>$value){?>
                    <?php if($value['file_type'] =="Vendor-Pincode"){ ?>
                    <tr>
                        <td>Vendor Pincode Mapping</td>
                        <td><?php echo $value['full_name'] ?></td>
                        <td><a href="<?php echo base_url(); ?>BookingSummary/download_latest_file/pincode"><button class="btn btn-success">Download</button></a></td>
                    </tr>
                    <?php }?>
                    <?php if($value['file_type'] =="SF-Price-List"){ ?>
                    <tr>
                        <td>Service Price List</td>
                        <td><?php echo $value['full_name'] ?></td>
                        <td><a href="<?php echo base_url(); ?>BookingSummary/download_latest_file/price"><button class="btn btn-success">Download</button></a></td>
                    </tr>
                    <?php }?>
                    <?php if($value['file_type'] =="Partner-Appliance-Details"){ ?>
                    <tr>
                        <td>Partner Appliance Details</td>
                        <td><?php echo $value['full_name'] ?></td>
                        <td><a href="<?php echo base_url(); ?>BookingSummary/download_latest_file/appliance"><button class="btn btn-success">Download</button></a></td>
                    </tr>
                    <?php }?>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>