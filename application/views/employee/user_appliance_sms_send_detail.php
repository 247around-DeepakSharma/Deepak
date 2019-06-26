<?php if(!empty($sms_sent_details)){ ?>
<div style="margin-top:25px; margin-bottom: 50px;">
    <h3>Sms History <small> ( Booking ID not available) </small> </h3>
    <table  class="table table-striped table-bordered table-hover">
        <tr>
            <th class="jumbotron" style="text-align: center;width: 1%">S.N</th>
            <th class="jumbotron" style="text-align: center">Phone</th>
            <th class="jumbotron" style="text-align: center">Sms Tag</th>
            <th class="jumbotron" style="text-align: center;width:45%;">Content</th>
            <th class="jumbotron" style="text-align: center">Sent on Date</th>
        </tr>
        <?php foreach($sms_sent_details as $key =>$row){?>
        <tr>
            <td><?php echo ($key+1).'.';?></td>
            <td><?php echo $row['phone'];?></td>
            <td><?php echo $row['sms_tag']; ?></td>
            <td style="font-size: 90%;"><?php echo $row['content'];?></td>
            <td><?php
                $old_date = $row['created_on'];
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('j F, Y g:i A', $old_date_timestamp);
                echo $new_date;?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php } ?>
<?php if(!empty($appliance_details)){ ?>
<div id="for_appliance" class="col-md-12"  style="display:none;">
    <h2><b>Appliance Wallet:</b></h2>
    <div>
        <table class="table table-striped table-bordered table-hover">
        <tr>
            <th class="jumbotron">S. No.</th>
            <th class="jumbotron">Service</th>
            <th class="jumbotron">Brand</th>
            <th class="jumbotron">Category</th>
            <th class="jumbotron">Capacity</th>
            <th class="jumbotron">Model Number</th>
            <th class="jumbotron">Purchase Year</th>
            <th class="jumbotron">Book Now</th>
        </tr>
            <?php $count = 1; ?>
            <?php foreach($appliance_details as $key =>$row){?>
            <tr>
                <td><?php echo "$count"; $count++;?></td>
                <td><?=$row['services']?></td>
                <td><?=$row['brand']?></td>
                <td><?=$row['category']?></td>
                <td><?=$row['capacity']?></td>
                <td><?=$row['model_number']?></td>
                <td><?=$row['purchase_date']?></td>
                <td><?php 
                    echo "<a class='btn btn-small btn-primary btn-sm' href=".base_url()."employee/booking/get_appliance_booking_form/$row[id]>Book Now</a>";
                    ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<?php } ?>