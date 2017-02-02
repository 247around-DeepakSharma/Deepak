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
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User/Phone</th>
                                        <th class="text-center" style="min-width:85px;" data-orderable="false">Address</th>
                                        <th class="text-center" data-orderable="false">Appliance</th>
                                        <th class="text-center" style="min-width:86px;">Booking Date</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th  class="text-center" >Escalation</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <?php } ?>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <th class="text-center"  data-orderable="false">Reschedule</th>
                                        <?php }?>
                                        <!--                                            <th class="text-center">View</th>-->
                                        <th class="text-center" data-orderable="false">Cancel</th>
                                        <th class="text-center" data-orderable="false">Complete</th>
                                        <th class="text-center" data-orderable="false">JobCard</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no = 1; ?>
                                    <?php foreach($bookings[1] as $key =>$row){?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no; if($row->is_upcountry == 1) { ?>
                                            <i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->upcountry_paid_by_customer;?>')" style='color: red;font-size: 28px; cursor: pointer' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a  target="_blank" 
                                                <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                            </a>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->customername."<br/>".$row->booking_primary_contact_no;?>
                                        </td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                            <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?= $row->booking_date."<br/>"; ?> 
                                            <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                        </td>
                                        <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                            <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                        </td>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;">
                                            <i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc + $row->upcountry_price); ?>
                                            <br/>
                                            <?php if($row->penalty > 0){ ?>
                                            <p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p>
                                            <?php } else { ?>
                                            <div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;white-space: initial;" data-html=true data-content="Time Left To Update Booking & Get Incentive" ></div>
                                            <?php } ?>
                                        </td>
                                        <!--                                            <td>
                                            <div  id= "<?php //echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php //if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php //} ?>>
                                                <select name="engineer[<?php //echo $row->booking_id; ?>]" id="<?php// echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php // if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php //} ?> style="width:100px;">
                                                    <option value="" >Select Engineer</option>
                                                    <?php //foreach ($engineer_details as $value) { ?>
                                                    <option <?php
                                                // if (!is_null($row->assigned_engineer_id)) {
                                                // if ($row->assigned_engineer_id == $value['id']) {
                                                //   echo "SELECTED";
                                                // }
                                                // }
                                                 ?> value="<?php// echo $value['id']; ?>" ><?php// echo $value['name']; ?></option>
                                                    <?php //} ?>
                                                </select>
                                            </div>
                                            <div id= "<?php// echo 'engineer_name_div' . $sn_no; ?>" 
                                            <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                <?php// foreach ($engineer_details as $value1) {
                                                // if($value1['id'] == $row->assigned_engineer_id ){
                                                //     echo $value1['name'];
                                                // }
                                                         
                                                   //  } ?>
                                            </p>
                                            </div>
                                            </td>-->
                                        <!--                        <td>
                                            <?php// if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php //echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php// } ?>
                                            </td>-->
                                        <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
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
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                        <!--                        <td>
                                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php // echo $row->count_reschedule; ?></span></a>
                                            
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
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User/Phone</th>
                                        <th class="text-center" style="min-width:85px;" data-orderable="false">Address</th>
                                        <th class="text-center" data-orderable="false" data-orderable="false">Appliance</th>
                                        <th class="text-center" style="min-width:86px;">Booking Date</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th  class="text-center">Escalation</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <?php } ?>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <th class="text-center" data-orderable="false">Reschedule</th>
                                        <?php }?>
                                        <!--                                            <th class="text-center">View</th>-->
                                        <th class="text-center" data-orderable="false">Cancel</th>
                                        <th class="text-center" data-orderable="false">Complete</th>
                                        <th class="text-center" data-orderable="false">JobCard</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no1 = 1 ; foreach($bookings[2] as $key =>$row){?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no1; if($row->is_upcountry == 1) { ?><i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>','<?php echo $row->upcountry_paid_by_customer; ?>')" style='color: red;font-size: 28px;cursor: pointer' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a  target="_blank" 
                                                <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                            </a>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->customername."<br/>".$row->booking_primary_contact_no;?>
                                        </td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                            <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?= $row->booking_date."<br/>"; ?> 
                                            <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                        </td>
                                        <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                            <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                        </td>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc  + $row->upcountry_price); ?>
                                        </td>
                                        <!--                                            <td>
                                            <div  id= "<?php// echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php //if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php //} ?>>
                                                <select name="engineer[<?php //echo $row->booking_id; ?>]" id="<?php //echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php//if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php// } ?> style="width:100px;">
                                                    <option value="" >Select Engineer</option>
                                                    <?php //foreach ($engineer_details as $value) { ?>
                                                    <option <?php
                                                //if (!is_null($row->assigned_engineer_id)) {
                                                // if ($row->assigned_engineer_id == $value['id']) {
                                                //  echo "SELECTED";
                                                // }
                                                // }
                                                ?> value="<?php //echo $value['id']; ?>" ><?php //echo $value['name']; ?></option>
                                                    <?php //} ?>
                                                </select>
                                            </div>
                                            <div id= "<?php// echo 'engineer_name_div' . $sn_no; ?>" 
                                            <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                <?php //foreach ($engineer_details as $value1) {
                                                // if($value1['id'] == $row->assigned_engineer_id ){
                                                 //    echo $value1['name'];
                                                 //}
                                                         
                                                    // } ?>
                                            </p>
                                            </div>
                                            </td>-->
                                        <!--                        <td>
                                            <?php //if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php //echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php //} ?>
                                            </td>-->
                                        <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
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
                                        <!--                        <td><a class='btn btn-sm btn-primary <?php //if($this->session->userdata('is_update') == 1){ ?> <?php //if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php //} } ?>' href="<?php//echo base_url();?>service_center/booking_details/<?php //echo urlencode(base64_encode($row->booking_id));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>-->
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                        <!--                        <td>
                                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  //echo $row->count_reschedule; ?></span></a>
                                            
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
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User/Phone</th>
                                        <th class="text-center" style="min-width:85px;"data-orderable="false">Address</th>
                                        <th class="text-center" data-orderable="false">Appliance</th>
                                        <th class="text-center" style="min-width:86px;" data-orderable="false">Booking Date</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th  class="text-center" >Escalation</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <?php } ?>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <th class="text-center" data-orderable="false">Reschedule</th>
                                        <?php }?>
                                        <!--                                            <th class="text-center">View</th>-->
                                        <th class="text-center" data-orderable="false">Cancel</th>
                                        <th class="text-center" data-orderable="false">Complete</th>
                                        <th class="text-center" data-orderable="false">JobCard</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no2 = 1 ; foreach($bookings[3] as $key =>$row){ 
                                        if($row->current_status== "Rescheduled"){?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no2; if($row->is_upcountry == 1) { ?><i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->upcountry_paid_by_customer; ?>')" style='color: red;font-size: 28px;cursor: pointer;' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a  target="_blank" 
                                                <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  style="pointer-events:none" <?php } } ?> 
                                                href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                            </a>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->customername."<br/>".$row->booking_primary_contact_no;?>
                                        </td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                            <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?php if (stristr($row->request_type, "Installation")) { if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  } } echo $row->request_type." ". $row->services; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?= $row->booking_date."<br/>"; ?> 
                                            <span style="color:#F26722; font-size:13px;"><?= $row->booking_timeslot; ?></span>
                                        </td>
                                        <td style="vertical-align: middle;"> <?= $row->age_of_booking." day"; ?></td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                            <?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>
                                        </td>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf ("%.2f",$row->earn_sc  + $row->upcountry_price); ?>
                                        </td>
                                        <!--                                            <td>
                                            <div  id= "<?php //echo 'assign_engineer_div' . $sn_no; ?>" class="form-group " <?php //if (!is_null($row->assigned_engineer_id)) { ?> style="display: none;" <?php// } ?>>
                                                <select name="engineer[<?php //echo $row->booking_id; ?>]" id="<?php //echo "engineer" . $sn_no ?>" class="form-control engineers_id" <?php //if (!is_null($row->assigned_engineer_id)) { ?> disabled <?php// } ?> style="width:100px;">
                                                    <option value="" >Select Engineer</option>
                                                    <?php// foreach ($engineer_details as $value) { ?>
                                                    <option <?php
                                                //  if (!is_null($row->assigned_engineer_id)) {
                                                //  if ($row->assigned_engineer_id == $value['id']) {
                                                 //   echo "SELECTED";
                                                 // }
                                                 // }
                                                  ?> value="<?php// echo $value['id']; ?>" ><?php //echo $value['name']; ?></option>
                                                    <?php //} ?>
                                                </select>
                                            </div>
                                            <div id= "<?php //echo 'engineer_name_div' . $sn_no; ?>" 
                                            <p style="font-weight: bold; text-align: center; color: #2C9D9C;">
                                                <?php //foreach ($engineer_details as $value1) {
                                                //if($value1['id'] == $row->assigned_engineer_id ){
                                                //     echo $value1['name'];
                                                //  }
                                                        
                                                   // } ?>
                                            </p>
                                            </div>
                                            </td>-->
                                        <!--                        <td>
                                            <?php //if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php //echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php// } ?>
                                            </td>-->
                                        <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
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
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                        <!--                        <td>
                                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php // echo $row->count_reschedule; ?></span></a>
                                            
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
                                    <th class="text-center" data-orderable="false">No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Serial Number</th>
                                    <th class="text-center" data-orderable="false">Parts</th>
                                    <th class="text-center" data-orderable="false">Shipped Date</th>
                                    <th class="text-center" data-orderable="false">View</th>
                                    <!--                                    <th class="text-center" data-orderable="false">Received</th>-->
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
                                    <!--                                    <td>
                                        <a href="<?php //echo base_url(); ?>service_center/acknowledge_delivered_spare_parts/<?php //echo $row['booking_id']; ?>/<?php //echo $this->session->userdata('service_center_id') ?>" style="width:23px; <?php //if(is_null($row['parts_shipped'])){ echo 'pointer-events:none';}?>"><img src="<?php// echo base_url(); ?>images/icon_receiving.png" style="width:23px;" /></a>
                                        </td>-->
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
<?php } ?>