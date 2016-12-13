<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<div  id="page-wrapper">
    <div class="row">

        <div class="panel panel-info clear">
            <div class="panel-heading"><center><strong>Booking History</strong></center></div>
        </div>
        <div>
            <div class="col-md-12 " style="margin-bottom: 15px;">
                <div class="col-md-6">

                    <div class="form-group-space">
                        <label for="booking_id" class="col-md-4">Booking ID</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="phone_number" value = "<?php echo isset($booking_details[0]['booking_id'])?$booking_details[0]['booking_id']:''?>"  disabled>
                        </div>
                    </div>

                    <div class="form-group-space">
                        <label for="name" class="col-md-4">Booking Date</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="name" value = "<?php echo isset($booking_details[0]['booking_date'])?$booking_details[0]['booking_date']:''?>"  disabled>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group-space">
                        <label for="service" class="col-md-4">Service</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control"  name="service" value = "<?php echo isset($booking_details[0]['services'])?$booking_details[0]['services']:''?>"  disabled>
                        </div>
                    </div>
                    <div class="form-group-space">
                        <label for="service" class="col-md-4">Booking Time Slot</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="service" value = "<?php echo isset($booking_details[0]['booking_timeslot'])?$booking_details[0]['booking_timeslot']:''?>"  disabled>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <table  class="table table-striped table-bordered">
          <tr>
                <th class="jumbotron" style="text-align: center">S.N</th>
          	<th class="jumbotron" style="text-align: center">Old State</th>
          	<th class="jumbotron" style="text-align: center">New State</th>
                <th class="jumbotron" style="text-align: center">Remarks</th>
          	<th class="jumbotron" style="text-align: center">Agent</th>
          	<th class="jumbotron" style="text-align: center">Partner</th>
          	<th class="jumbotron" style="text-align: center">Date</th>
          </tr>
          <?php foreach($data as $key =>$row){?>
          <tr>
            <td><?php echo ($key+1).'.';?></td>
            <td><?php echo $row['old_state']; ?></td>
            <td><?php echo $row['new_state'];?></td>
            <td><?php echo $row['remarks']; ?></td>
            <td><?php echo $row['employee_id'];?></td>
            <td><?php echo $row['source'];?></td>
            <td><?php
                $old_date = $row['create_date'];
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('j F, Y g:i A', $old_date_timestamp);
                echo $new_date;?>
            </td>
          </tr>
          <?php } ?>
          </div>
        </table><hr>
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
          </div>
        </table>
</div>
</div>
