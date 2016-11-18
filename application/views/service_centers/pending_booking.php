<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <?php $booking_id = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : ''); ?>
        <?php
    if ($this->session->userdata('success')) {
    echo '<div class="alert alert-success alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('success') . '</strong>
                 </div>';
    }
    ?>
         <?php
                    if ($this->session->flashdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('error') . '</strong>
                    </div>';
                    }
                    ?>
      
        
        
        <div class="col-md-12" >
            <table class="table table-striped table-bordered table-hover" style="font-size:13px">
                <thead>
                <tr>
                    <th class="text-center">Booking Cancelled This Month</th>
                    <td class="text-center"><?php echo $cancel_booking[0]['cancel_booking']; ?></td>
               
                    <th class="text-center">Lost This Month</th>
                    <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",($eraned_details[0]['earned']/$eraned_details[0]['total_booking'])* $cancel_booking[0]['cancel_booking']); ?></td>
               
                    <th class="text-center">Booking Completed This Month</th>
                    <td class="text-center"><?php echo $eraned_details[0]['total_booking']; ?></td>
                
                    <th class="text-center">Service Center Earning</th>
                      <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$eraned_details[0]['earned']); ?></td>
                </tr>
                </thead>
                
            </table>
 
        </div>
        <div class="col-md-12"><h2>Pending Bookings</h2></div>
        <div class="col-md-10">
            <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active"><a href="#today_booking" aria-controls="today_booking" role="tab" data-toggle="tab"><?php if($booking_id == ''){ ?>Today Bookings<?php } else { echo "Searched Booking";} ?></a></li>
                <?php if($this->session->userdata('is_update') == 1){ ?>
                <li role="presentation"><a href="#tomorrow_booking" aria-controls="tomorrow_booking" role="tab" data-toggle="tab">Tomorrow Bookings</a></li>
                <li role="presentation"><a href="#rescheduled_booking" aria-controls="rescheduled_booking" role="tab" data-toggle="tab">Rescheduled Bookings</a></li>
                <li role="presentation"><a href="#spare_required" aria-controls="spare_required" role="tab" data-toggle="tab">Spare Required Bookings</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="today_booking">
        <div class="container-fluid">
            <div class="row" >
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body" >
                            <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
<!--                                <div class="pull-right">Red Bookings are Escalation, Call Customer Immediately !!!</div>-->
                                <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                    <thead >
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Booking Id</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center" style="min-width:85px;">Address</th>
                                            
                                            <th class="text-center" >Appliance</th>
                                            <th class="text-center" style="min-width:86px;">Booking Date</th>
                                            <th class="text-center">Age</th>
                                            <th class="text-center" >Call Center Remarks</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Service Center Earning</th>
<!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
  
                                            <?php } ?> 
                                            <th  class="text-center">Escalation</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>

                                            <th class="text-center" style="content: \f;">Update</th>
                                            <?php } ?>
                                                
                                            <?php if($this->session->userdata('is_update') == 0){ ?>
                                            
                                            <th>Reschedule</th>

                                            <?php }?>
<!--                                            <th class="text-center">View</th>-->
                                            <th class="text-center">Cancel</th>
                                            <th class="text-center">Complete</th>
                                            <th class="text-center">JobCard</th>
                                          
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sn_no = 1; ?>
                                        <?php foreach($bookings[1] as $key =>$row){?>
                                        <tr <?php if($row->count_escalation > 0){ ?> data-popover="true" data-html=true data-content="This is escalation booking" <?php } ?> style="text-align: center; <?php if($row->count_escalation > 0){ echo 'background-color:#F73006;color:black; font-weight:800px;';} ?>"  >
                                            <td style="vertical-align: middle;">
                                                <?php echo $sn_no; ?>
                                            </td>
                                          
                                            <td style="vertical-align: middle;">
                                                    <a  target="_blank" 
                                                        <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                        href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                                        <?php echo $row->booking_id; ?>
                                                    </a>
                                                </td>
                                              
                                       
                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?=$row->customername;?>
                                            </td>
                                            
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                                <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                            </td>

                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                            </td>
                                            <td >
                                                <?= $row->booking_date."<br/>"; ?> 
                                                <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                            </td>
                                            <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                              
                                               <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                               
                                            </td>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <td style="vertical-align: middle;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc); ?>
                                                <br/>
                                               <?php if($row->penalty > 0){ ?><p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p><?php } else { ?><div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;" data-html=true data-content="This is time left to update booking & get incentive" ></div><?php } ?>
                                            
                                            </td>
<!--                                            <td>
                                                <div  id= "<?php echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php } ?>>
                                                    <select name="engineer[<?php echo $row->booking_id; ?>]" id="<?php echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php } ?> style="width:100px;">
                                                        <option value="" >Select Engineer</option>
                                                        <?php foreach ($engineer_details as $value) { ?>
                                                        <option <?php
                                                            if (!is_null($row->assigned_engineer_id)) {
                                                            if ($row->assigned_engineer_id == $value['id']) {
                                                              echo "SELECTED";
                                                            }
                                                            }
                                                            ?> value="<?php echo $value['id']; ?>" ><?php echo $value['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div id= "<?php echo 'engineer_name_div' . $sn_no; ?>" 
                                                <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                    <?php foreach ($engineer_details as $value1) {
                                                        if($value1['id'] == $row->assigned_engineer_id ){
                                                            echo $value1['name'];
                                                        }
                                                                
                                                            } ?>
                                                </p>
                        </div>
                        </td>-->
<!--                        <td>
                        <?php if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php } ?>
                        </td>-->
                        
                        <?php } ?>
                        
                        <td style="vertical-align: middle;">
                             <?php  echo $row->count_escalation." times"; ?>
                        </td>
                        
                         <?php if($this->session->userdata('is_update') == 1){ ?>
                        <td style="vertical-align: middle;">
                        <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                        </td>
                        
                         <?php } ?>
                        
                        <?php if($this->session->userdata('is_update') == 0){ ?>
                            <td style="vertical-align: middle;">
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>

                            <?php } ?>
<!--                        <td><a class='btn btn-sm btn-primary <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>-->
                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;">
                        <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                       
<!--                        <td>
                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  echo $row->count_reschedule; ?></span></a>
                      
                        </td>-->
                        
                         </tr>
                        <?php $sn_no++; } ?>
                        </tbody>
                        </table>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
<!--                        <div id="loading" class="loading" style="text-align: center;">
                        <input type= "submit" id="submit_button"  class="btn btn-danger btn-md submit_button" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Assigned Engineer" >
                        </div>-->
                        <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('is_update') == 1){ ?>
<div role="tabpanel" class="tab-pane" id="tomorrow_booking">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form   id="form2" onsubmit="return submitForm('form2');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="tomorrow_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                   <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Booking Id</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center" style="min-width:85px;">Address</th>
                                            
                                            <th class="text-center" >Appliance</th>
                                            <th class="text-center" style="min-width:86px;">Booking Date</th>
                                            <th class="text-center">Age</th>
                                            <th class="text-center" >Call Center Remarks</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Service Center Earning</th>
<!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
  
                                            <?php } ?> 
                                            <th  class="text-center">Escalation</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>

                                            <th class="text-center" style="content: \f;">Update</th>
                                            <?php } ?>
                                                
                                            <?php if($this->session->userdata('is_update') == 0){ ?>
                                            
                                            <th>Reschedule</th>

                                            <?php }?>
<!--                                            <th class="text-center">View</th>-->
                                            <th class="text-center">Cancel</th>
                                            <th class="text-center">Complete</th>
                                            <th class="text-center">JobCard</th>
                                          
                                            
                                            
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no1 = 1 ; foreach($bookings[2] as $key =>$row){?>
                                     <tr <?php if($row->count_escalation > 0){ ?> data-popover="true" data-html=true data-content="This is escalation booking" <?php } ?> style="text-align: center; <?php if($row->count_escalation > 0){ echo 'background-color:#F73006;color:black; font-weight:800px;';} ?>"  >
                                            <td style="vertical-align: middle;">
                                                <?php echo $sn_no1; ?>
                                            </td>
                                          
                                            <td style="vertical-align: middle;">
                                                    <a  target="_blank" 
                                                        <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                        href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                                        <?php echo $row->booking_id; ?>
                                                    </a>
                                                </td>
                                              
                                       
                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?=$row->customername;?>
                                            </td>
                                            
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                                <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                            </td>

                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                            </td>
                                            <td >
                                                <?= $row->booking_date."<br/>"; ?> 
                                                <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                            </td>
                                            <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                              
                                               <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                               
                                            </td>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <td style="vertical-align: middle;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc); ?>
                                                <br/>
                                               <?php if($row->penalty > 0){ ?><p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p><?php } else { ?><div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;" data-html=true data-content="This is time left to update booking & get incentive" ></div><?php } ?>
                                            
                                            </td>
<!--                                            <td>
                                                <div  id= "<?php echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php } ?>>
                                                    <select name="engineer[<?php echo $row->booking_id; ?>]" id="<?php echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php } ?> style="width:100px;">
                                                        <option value="" >Select Engineer</option>
                                                        <?php foreach ($engineer_details as $value) { ?>
                                                        <option <?php
                                                            if (!is_null($row->assigned_engineer_id)) {
                                                            if ($row->assigned_engineer_id == $value['id']) {
                                                              echo "SELECTED";
                                                            }
                                                            }
                                                            ?> value="<?php echo $value['id']; ?>" ><?php echo $value['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div id= "<?php echo 'engineer_name_div' . $sn_no; ?>" 
                                                <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                    <?php foreach ($engineer_details as $value1) {
                                                        if($value1['id'] == $row->assigned_engineer_id ){
                                                            echo $value1['name'];
                                                        }
                                                                
                                                            } ?>
                                                </p>
                        </div>
                        </td>-->
<!--                        <td>
                        <?php if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php } ?>
                        </td>-->
                        
                        <?php } ?>
                        
                        <td style="vertical-align: middle;">
                             <?php  echo $row->count_escalation." times"; ?>
                        </td>
                        
                         <?php if($this->session->userdata('is_update') == 1){ ?>
                        <td style="vertical-align: middle;">
                        <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                        </td>
                        
                         <?php } ?>
                        
                        <?php if($this->session->userdata('is_update') == 0){ ?>
                            <td style="vertical-align: middle;">
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>

                            <?php } ?>
<!--                        <td><a class='btn btn-sm btn-primary <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>-->
                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;">
                        <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                       
<!--                        <td>
                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  echo $row->count_reschedule; ?></span></a>
                      
                        </td>-->
                        
                         </tr>
                    <?php $sn_no++;$sn_no1++; } ?>
                    </tbody>
                    </table>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
<!--                        <div id="loading" class="loading" style="text-align: center;">
                        <input type= "submit" id="submit_button"  class="btn btn-danger btn-md submit_button" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Assigned Engineer" >
                        </div>-->
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
    

    <div role="tabpanel" class="tab-pane" id="rescheduled_booking">
        <div class="container-fluid">
            <div class="row" >
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body" >
                             <form   id="form3" onsubmit="return submitForm('form3');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="future_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                 <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Booking Id</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center" style="min-width:85px;">Address</th>
                                            
                                            <th class="text-center" >Appliance</th>
                                            <th class="text-center" style="min-width:86px;">Booking Date</th>
                                            <th class="text-center">Age</th>
                                            <th class="text-center" >Call Center Remarks</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Service Center Earning</th>
<!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
  
                                            <?php } ?> 
                                            <th  class="text-center">Escalation</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>

                                            <th class="text-center" style="content: \f;">Update</th>
                                            <?php } ?>
                                                
                                            <?php if($this->session->userdata('is_update') == 0){ ?>
                                            
                                            <th>Reschedule</th>

                                            <?php }?>
<!--                                            <th class="text-center">View</th>-->
                                            <th class="text-center">Cancel</th>
                                            <th class="text-center">Complete</th>
                                            <th class="text-center">JobCard</th>
                                          
                                            
                                            
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no2 = 1 ; foreach($bookings[3] as $key =>$row){ 
                                        if($row->current_status== "Rescheduled"){?>
                               <tr <?php if($row->count_escalation > 0){ ?> data-popover="true" data-html=true data-content="This is escalation booking" <?php } ?> style="text-align: center; <?php if($row->count_escalation > 0){ echo 'background-color:#F73006;color:black; font-weight:800px;';} ?>"  >
                                            <td style="vertical-align: middle;">
                                                <?php echo $sn_no2; ?>
                                            </td>
                                          
                                            <td style="vertical-align: middle;">
                                                    <a  target="_blank" 
                                                        <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                        href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                                        <?php echo $row->booking_id; ?>
                                                    </a>
                                                </td>
                                              
                                       
                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?=$row->customername;?>
                                            </td>
                                            
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                                <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                            </td>

                                            <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                                <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                            </td>
                                            <td >
                                                <?= $row->booking_date."<br/>"; ?> 
                                                <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                            </td>
                                            <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                              
                                               <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                               
                                            </td>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <td style="vertical-align: middle;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc); ?>
                                                <br/>
                                               <?php if($row->penalty > 0){ ?><p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p><?php } else { ?><div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;" data-html=true data-content="This is time left to update booking & get incentive" ></div><?php } ?>
                                            
                                            </td>
<!--                                            <td>
                                                <div  id= "<?php echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php } ?>>
                                                    <select name="engineer[<?php echo $row->booking_id; ?>]" id="<?php echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php } ?> style="width:100px;">
                                                        <option value="" >Select Engineer</option>
                                                        <?php foreach ($engineer_details as $value) { ?>
                                                        <option <?php
                                                            if (!is_null($row->assigned_engineer_id)) {
                                                            if ($row->assigned_engineer_id == $value['id']) {
                                                              echo "SELECTED";
                                                            }
                                                            }
                                                            ?> value="<?php echo $value['id']; ?>" ><?php echo $value['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div id= "<?php echo 'engineer_name_div' . $sn_no; ?>" 
                                                <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                    <?php foreach ($engineer_details as $value1) {
                                                        if($value1['id'] == $row->assigned_engineer_id ){
                                                            echo $value1['name'];
                                                        }
                                                                
                                                            } ?>
                                                </p>
                        </div>
                        </td>-->
<!--                        <td>
                        <?php if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php } ?>
                        </td>-->
                        
                        <?php } ?>
                        
                        <td style="vertical-align: middle;">
                             <?php  echo $row->count_escalation." times"; ?>
                        </td>
                        
                         <?php if($this->session->userdata('is_update') == 1){ ?>
                        <td style="vertical-align: middle;">
                        <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                        </td>
                        
                         <?php } ?>
                        
                        <?php if($this->session->userdata('is_update') == 0){ ?>
                            <td style="vertical-align: middle;">
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>

                            <?php } ?>
<!--                        <td><a class='btn btn-sm btn-primary <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>-->
                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;">
                        <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                        </td>
                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                       
<!--                        <td>
                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  echo $row->count_reschedule; ?></span></a>
                      
                        </td>-->
                        
                         </tr>
                                    <?php $sn_no++;$sn_no1++; $sn_no2++;} } ?>
                    </tbody>
                    </table>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
<!--                        <div id="loading" class="loading" style="text-align: center;">
                        <input type= "submit" id="submit_button"  class="btn btn-danger btn-md submit_button" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Assigned Engineer" >
                        </div>-->
                                    <?php } ?>
                    </form>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        
<div role="tabpanel" class="tab-pane" id="spare_required">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                       
                        <table id="spare_required_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr >
                                    <th class="text-center">No</th>
                                    <th class="text-center">Booking Id</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">Parts</th>
                                    <th class="text-center">Shipped Date</th>
                                    
                                    <th class="text-center">View</th>
                                    <th class="text-center">Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn_no1 = 1; foreach($spare_parts_data as $key =>$row){?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no1; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['booking_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['model_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['serial_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['parts_requested']; ?>
                                    </td>
                                   
                                   
                                    <td>
                                        <?php if($row['shipped_date'] != "0000-00-00"){echo $row['shipped_date'];} ?>
                                    </td>
                                   
                                    <td>
                                        <a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>service_center/acknowledge_delivered_spare_parts/<?php echo $row['booking_id']; ?>" style="width:23px;"><img src="<?php echo base_url(); ?>images/icon_receiving.png" style="width:23px;" /></a>
                                    </td>
                                </tr>
                                <?php $sn_no1++; } ?>
                            </tbody>
                        </table>
<!--                        <div id="loading1" style="text-align: center;">
                            <input type= "submit" id="submit_button1"  class="btn btn-danger btn-md" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Update Booking" >
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php } ?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
         <h4 class="modal-title" id="myModalLabel">Send Reschedule Request</h4>
      </div>
      <div class="modal-body">
         <form name="myForm1" id="reschedule_form" class="form-horizontal" method="POST">
            <div class="form-group">
               <label for="name" class="col-sm-3">Booking Id </label>
               <div class="col-md-6">
                  <input type="text" name="booking_id"  class="form-control "  id="booking_id" readonly></input>
               </div>
            </div>
            <div class="form-group">
               <label for="name" class="col-sm-3">Booking Date </label>
               <div class="col-md-6">
                  <div class="input-group input-append date" >
                     <input type="text" id="datepicker" class="form-control "  style="z-index:9999; background-color:#fff;" name="booking_date" required readonly='true'>
                     <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
               </div>
            </div>
            
             <div class="form-group">
               <label for="name" class="col-sm-3">Reschedule Reason</label>
                <div class="col-md-6">
                   <textarea name="remarks" rows="5" class="form-control" id="remarks" placeholer="Plese Enter Reschedule Reason" ></textarea>
                </div>
                </div>
         </form>
         
         </div>
         <div class="col-md-12" style="margin-top: 5px; margin-bottom: 5px;">
         <p id="error" style="color: red"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="sendRescheduleRequest()">Save changes</button>
         </div>
      </div>
   </div>
</div>
<script>
    $(document).ready(function() {
    
        $('#today_datatable').dataTable( {
            "pageLength": 50
           
        } );
        
        $("#today_datatable_filter").html("Red Bookings are Escalations, Call Customer Immediately !!!");
        $("#today_datatable_filter").css("font-weight", "bold");
        
    
        $('#tomorrow_datatable').dataTable( {
            "pageLength": 50,
            "bFilter": false
        } );
    
        $('#spare_required_datatable').dataTable({
            "pageLength": 50,
            "bFilter": false
        });
        $('body').popover({
           selector: '[data-popover]',
           trigger: 'click hover',
           placement: 'auto',
           delay: {
               show: 50,
               hide: 100
           }
        });
    } );
    
    
    
     $('.engineers_id').select2();
    
    function edit_engineer(div) {
    // $("#assign_engineer_div"+div).show();
    $("#assign_engineer_div" + div).css("display", "block");
    $("#engineer_name_div" + div).hide();
    $("#engineer"+div).removeAttr("disabled");
    }
    
     function submitForm(form_id) {
        var html = "<img src='<?php echo base_url(); ?>images/loader.gif' />";
        $('.submit_button').hide();
        $('.loading').append(html);
        var fd = new FormData(document.getElementById(form_id));
        fd.append("label", "WEBUPLOAD");
        $.ajax({
           url: "<?php echo base_url() ?>employee/service_centers/assigned_engineers",
          type: "POST",
          data: fd,
          processData: false, // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        }).done(function (data) {
          //console.log(data);
          location.reload();


        });
        return false;
    }
    
    
    $(".ack_date").datepicker({dateFormat: 'yy-mm-dd'});
    
//    function submit_spare_form(){
//        var html = "<img src='<?php echo base_url(); ?>images/loader.gif' />";
//        $('#submit_button1').hide();
//        $('#loading1').append(html);
//        var fd = new FormData(document.getElementById(form_id));
//        fd.append("label", "WEBUPLOAD");
//        $.ajax({
//          url: "<?php echo base_url(); ?>service_center/acknowledge_delivered_spare_parts",
//          type: "POST",
//          data: fd,
//          processData: false, // tell jQuery not to process the data
//          contentType: false   // tell jQuery not to set contentType
//        }).done(function (data) {
//          //console.log(data);
//          location.reload();
//
//
//        });
//        return false;
//    
//    }
    
</script>
<style>
    .dataTables_filter, .dataTables_paginate{
    float:right;
    }
    @keyframes blink {
      50% { opacity: 0.0; }
    }
    @-webkit-keyframes blink {
      50% { opacity: 0.0; }
    }
    .blink {
      animation: blink 1s step-start 0s infinite;
      -webkit-animation: blink 1s step-start 0s infinite;
    }



</style>
<?php $this->session->unset_userdata('success'); ?>
<script type="text/javascript">
     $(function() { $( "#datepicker" ).datepicker({  minDate: new Date });});
   
   function setbooking_id(booking_id){
   
      $('#booking_id').val(booking_id);
   }

   function sendRescheduleRequest(){
        var booking_id = $('#booking_id').val();
        var booking_date = $('#datepicker').val();
        
        var remarks = $('#remarks').val();
        if(booking_date ===""){
          
           $("#error").text('Plese Enter Booking Date');
            return false;
        }

        if(remarks ===""){
           $("#error").text('Plese Enter Reschedule Reason');
            return false;
        }

         $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/save_reschedule_request',
            data: {booking_id: booking_id, booking_date: booking_date, reason_text: remarks},
            success: function (result) {

                //console.log(result);
                location.reload();
               
            }
         });
   }
   
    var date = new Date();
    var end = new Date();
    end.setHours(21);
    end.setMinutes(0);
    end.setMilliseconds(0);

    var _second = 1000;
    var _minute = _second * 60;
    var _hour = _minute * 60;
    var _day = _hour * 24;
    var timer;

    function showRemaining() {
        var now = new Date();
        var distance = end - now;
        if (distance < 0) {

            clearInterval(timer);
            $(".count_down").text('Grace Period');

            return;
        }
        //var days = Math.floor(distance / _day);
        var hours = Math.floor((distance % _day) / _hour);
        var minutes = Math.floor((distance % _hour) / _minute);
        //var seconds = Math.floor((distance % _minute) / _second);
        
        var remaining_hr = hours + ":"+ minutes + " hr Left";
       
        $(".countdown").text(remaining_hr);
        
   
    }
    
    if(date.getHours() >=10){ // Check the time
        console.log(date.getHours());
        timer = setInterval(showRemaining, 1000);
    }

    

</script>
