<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<?php //echo '<pre>';print_r($booking_details);echo '</pre>';exit;?>
<div  id="page-wrapper">
    <div class="row">
       
        <div class="panel panel-info clear">
            <div class="panel-heading"><center><strong>Booking Life Cycle Details</strong></center></div>
        </div>
        <div>
            <div class="panel panel-default">
            <div class="panel-heading"><strong>Booking Details</strong></div>
            </div>
            <div class="col-md-12">
                    <div class="container">
                <div class="col-md-6">
                    
                    <div class="form-group-space">
                        <label for="booking_id" class="col-md-4">Booking ID</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="phone_number" value = "<?php echo $booking_details[0]['booking_id']?>"  disabled>
                        </div>
                    </div>
                    
                    <div class="form-group-space">
                        <label for="name" class="col-md-4">Booking Date</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="name" value = "<?php echo $booking_details[0]['booking_date']?>"  disabled>
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-6">
                    
                    <div class="form-group-space">
                        <label for="service" class="col-md-4">Service</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control"  name="service" value = "<?php echo $this->service_model->getserviceid($booking_details[0]['service_id'])[0]['services']?>"  disabled>
                        </div>
                    </div>
                    <div class="form-group-space">
                        <label for="service" class="col-md-4">Booking Time Slot</label>
                        <div class="col-md-8">
                        <input type="text" class="form-control"  name="service" value = "<?php echo $booking_details[0]['booking_timeslot']?>"  disabled>
                        </div>
                    </div>
                    
                </div>
            
            </div>
            </div>
        </div>
        
        <div class="panel panel-default clear">
            <div class="panel-heading"><strong>State Transitions</strong></div>
        </div>  
        <div style="min-height:auto;max-height: 520px;overflow-y : scroll;margin-bottom: 30px;">
        <table  class="table table-striped table-bordered">
          <tr>
                <th>S.N</th>
          	<th>Old State</th>
          	<th>New State</th>
                <th>Old Reason</th>
          	<th>New Reason</th>
          	<th>Employee ID</th>
          	<th>Date</th>
          </tr>
          <?php foreach($data as $key =>$row){?>
          <tr>
            <td><?php echo ($key+1);?></td>
            <td><?php echo $row['old_state']; ?></td>
            <td><?php echo $row['new_state'];?></td>
            <td><?php echo $row['old_reason']; ?></td>
            <td><?php echo $row['new_reason'];?></td>
            <td><?php echo $this->employee_model->getemployeefromid($row['agent_id'])[0]['employee_id'];?></td>
            <td><?php 
                $old_date = $row['create_date'];
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
