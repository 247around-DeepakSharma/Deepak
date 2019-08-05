<div role="tabpanel" class="tab-pane active" id="today_booking">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" style="overflow-x: auto;">
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
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" style="min-width:86px;">Booking Date</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th class="text-center" data-orderable="false">Brands</th>
                                        <th  class="text-center" >Escalation</th>
                                        <?php if($is_engineer_app){ ?>
                                        <th  class="text-center" >Assign Engineer</th>
                                        <?php }
                                        if(isset($saas_module) && (!$saas_module)) { ?>
                                        <th class="text-center" data-orderable="false">Edit Request Type</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Helper <br> Document</th>
                                        <th class="text-center" data-orderable="false">Warranty <br> Checker</th>
                                        <th class="text-center" data-orderable="false">Check Spare Part Price</th>
                                        <th class="text-center" data-orderable="false">Contacts</th> 
                                       <th class="text-center" data-orderable="false">Send Email</th>                                                            
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Complete</th>
                                        <th class="text-center" data-orderable="false">Cancel</th>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <th class="text-center"  data-orderable="false">Reschedule</th>
                                        <?php }?>
                                        <th class="text-center" data-orderable="false">JobCard</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php $sn_no = 1; ?>
                                    <?php foreach($bookings[1] as $key =>$row){
                                        ?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no; if($row->is_upcountry == 1) { ?>
                                            <i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->amount_due;?>', '<?php echo $row->flat_upcountry;?>')" style='color: red;font-size: 28px; cursor: pointer' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;     max-width: 200px;">
                                            <a  target="_blank" href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                                
                                            </a>
                                            
                                                <?php if($row->count_reschedule > 0){ ?>
                                                 <br/>
                                                <span style="color:#F26722; font-size:13px;"><?php echo $row->count_reschedule; ?> times rescheduled</span>
                                                <?php } ?>
                                           
                                                <?php if($row->is_bracket == 1){ ?>
                                                <br/>
                                                <img src="<?php echo base_url(); ?>images/Bracket.png" style="width:30%"/>
                                                <?php }?>
                                            
                                               <?php if($row->booking_files_purchase_invoice){ ?>
                                                <br/>
                                                <a target='_blank' href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $row->booking_files_purchase_invoice; ?>"  title = 'Purchase Invoice Verified' aria-hidden = 'true'><img src="<?php echo base_url(); ?>images/varified.png" style="width:20%"/></a>
                                               <?php } ?>
                                                <br/>
                                                <a href="javascript:void(0)" data-popover="true" style="border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html="true" data-content="<?php if(isset($row->part_cancel_reason)){ echo implode('<br>',explode(',',$row->part_cancel_reason));}?>" > <img style="width: 83%;" id="<?php echo 'spare_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 83%;" id="<?php echo 'spare_delivered_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 89%;" id="<?php echo 'spare_cost_given_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <script> $(document).ready(function(){ load_cancelled_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');
                                                         load_delivered_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');load_spare_cost_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');
                                                });
                                                
                                                </script>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->customername."<br/>".$row->booking_primary_contact_no;?>
                                        </td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                            <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?php if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  }  echo $row->request_type." ". $row->services; ?>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->partner_internal_status;?>
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
                                            <?php if($row->is_penalty == 1 ){ ?>
                                            <p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p>
                                            <?php } else { ?>
                                            <div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;white-space: initial;" data-html=true data-content="Time Left To Update Booking & Get Incentive" ></div>
                                            <?php } ?>
                                        </td>                                        
                                        <?php } ?>
                                            <td style="vertical-align: middle;"> <?=  strtoupper($row->appliance_brand); ?></td>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
                                            <?php  echo $row->count_escalation." times"; ?>
                                        </td>
                                        <?php if($is_engineer_app){ ?>
                                        <td style="vertical-align: middle;"><select id="engineer_<?php echo $sn_no; ?>" class="engineer_select" service-id="<?php echo $row->service_id; ?>" engineer-id="<?php echo $row->assigned_engineer_id; ?>" booking-id="<?php echo $row->booking_id; ?>"></select>
                                            <a href='<?php echo base_url(); ?>service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>
                                        </td>
                                        <?php }
                                        if(isset($saas_module) && (!$saas_module)) { ?>
                                        <td style="vertical-align: middle;">
                                            <a target="_blank" href="<?php echo base_url(); ?>service_center/get_sf_edit_booking_form/<?php echo urlencode(base64_encode($row->booking_id))?>" style="width: 36px;background: #795b95;border: #795b95;" class="btn btn-sm btn-primary"  title="Edit Request Type"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        </td>
                                        <?php } ?>
                                        <td style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-warning btn-sm" data-toggle="modal" data-target="#showBrandCollateral" onclick="get_brand_collateral(<?php echo "'".$row->booking_id."'" ?>)"><i class="fa fa-file-text-o" aria-hidden="true" ></i></button></td>
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/warranty/<?= $row->partner_id ?>/<?= $row->service_id ?>/<?= $row->appliance_brand ?>" target="_blank" class='btn btn-sm btn-success' title='Warranty Checker'><i class='fa fa-certificate' aria-hidden='true'></i></a></td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>service_center/inventory/inventory_list_by_model/<?php echo $row->partner_id; ?>/<?php echo $row->service_id; ?>/<?php echo $row->booking_id; ?>" target="_blank"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Relevant  Contact" id ="<?php echo $row->booking_id?>"  onclick="show_contacts(this.id,1,'<?php echo $row->partner_id; ?>')"><i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row->booking_id?>',0)"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>                                        
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary <?php if ((is_null($row->assigned_engineer_id) && $is_engineer_app == '1') || !empty($row->service_center_closed_date)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                                        </td>
                                        <?php } ?>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <td style="vertical-align: middle;">
                                            <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                                        </td>
                                        <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id) && $is_engineer_app == '1') { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>                                        
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/jobcards-pdf/<?php echo $row->booking_jobcard_filename; ?> " class='btn btn-sm btn-warning btn-sm' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <?php $sn_no++; } ?>
                                </tbody>
                            </table>
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
                                        <th class="text-center" data-orderable="false">Appliance</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" style="min-width:86px;">Booking Date</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th  class="text-center">Brands</th>
                                        <th  class="text-center">Escalation</th>
                                        <?php if($is_engineer_app){ ?>
                                        <th  class="text-center">Assign Engineer</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Send Email</th> 
                                        <th class="text-center" data-orderable="false">Contacts</th>
                                        <th class="text-center" data-orderable="false">Check Spare Part Price</th>
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
                                         <th class="text-center" data-orderable="false">Helper <br> Document</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no1 = 1 ; foreach($bookings[2] as $key =>$row){?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no1; if($row->is_upcountry == 1) { ?><i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>','<?php echo $row->amount_due; ?>', '<?php echo $row->flat_upcountry; ?>')" style='color: red;font-size: 28px;cursor: pointer' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a  target="_blank" href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                            </a>
                                             
                                                <?php if($row->count_reschedule > 0){ ?>
                                                <br/>
                                                <span style="color:#F26722; font-size:13px;"><?php echo $row->count_reschedule; ?> times rescheduled</span>
                                                <?php } ?>
                                                
                                                <?php if($row->is_bracket == 1){ ?>
                                                <br/>
                                                <img src="<?php echo base_url(); ?>images/Bracket.png" style="width:30%"/>
                                                <br/>
                                                <?php }?>
                                                
                                                
                                                <a href="javascript:void(0)" data-popover="true" style="border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html="true" data-content="<?php if(isset($row->part_cancel_reason)){ echo implode('<br>',explode(',',$row->part_cancel_reason));}?>"  > <img id="<?php echo 'spare_tomorrow'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif"  style="width:84%"/></a>
                                                <a href="javascript:void(0)" > <img style="width: 83%;" id="<?php echo 'spare_delivered_tomorrow'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 89%;" id="<?php echo 'spare_cost_given_tomorrow'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <script> $(document).ready(function(){ load_cancelled_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'tomorrow');
                                                         load_delivered_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'tomorrow');load_spare_cost_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'tomorrow');
                                                });
                                                
                                                
                                                </script>
                                                <?php if($row->booking_files_purchase_invoice){ ?>
                                                <br/>
                                                <a target='_blank' href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $row->booking_files_purchase_invoice; ?>"  title = 'Purchase Invoice Verified' aria-hidden = 'true'><img src="<?php echo base_url(); ?>images/varified.png" style="width:30%"/></a>
                                               <?php } ?>
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
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->partner_internal_status;?>
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
                                        <td style="vertical-align: middle;"> <?= $row->appliance_brand; ?></td>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
                                            <?php  echo $row->count_escalation." times"; ?>
                                        </td>
                                         <?php if($is_engineer_app){ ?>
                                        <td style="vertical-align: middle;">
                                            <select id="engineer_<?php echo $sn_no; ?>" class="engineer_select" service-id="<?php echo $row->service_id; ?>" engineer-id="<?php echo $row->assigned_engineer_id; ?>" booking-id="<?php echo $row->booking_id; ?>"></select>
                                            <a href='<?php echo base_url(); ?>service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>
                                        </td>
                                         <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row->booking_id?>',0)"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Relevant  Content" id ="<?php echo $row->booking_id?>"  onclick="show_contacts(this.id,1,'<?php echo $row->partner_id; ?>')"><i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>service_center/inventory/inventory_list_by_model/<?php echo $row->partner_id; ?>/<?php echo $row->service_id; ?>/<?php echo $row->booking_id; ?>" target="_blank"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        </td>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id) && $is_engineer_app == '1') { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
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
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id) && $is_engineer_app == '1') { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                       <td style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-warning btn-sm" data-toggle="modal" data-target="#showBrandCollateral" onclick="get_brand_collateral(<?php echo "'".$row->booking_id."'" ?>)"><i class="fa fa-file-text-o" aria-hidden="true" ></i></button></td>                                        
                        <td>
                                          <!--     <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
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
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" style="min-width:86px;" data-orderable="false">Booking Date</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th  class="text-center" >Brands</th>
                                        <th  class="text-center" >Escalation</th>
                                        <?php if($is_engineer_app){ ?>
                                        <th  class="text-center">Assign Engineer</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Send Email</th> 
                                        <th class="text-center" data-orderable="false">Contacts</th>
                                        <th class="text-center" data-orderable="false">Check Spare Part Price</th>
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
                                        <th class="text-center" data-orderable="false">Helper <br> Document</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no2 = 1 ; foreach($bookings[3] as $key =>$row){ 
                                        if($row->current_status== "Rescheduled"){?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no2; if($row->is_upcountry == 1) { ?><i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->amount_due; ?>', '<?php echo $row->flat_upcountry; ?>')" style='color: red;font-size: 28px;cursor: pointer;' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a  target="_blank" href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                            </a>
                                             
                                                <?php if($row->count_reschedule > 0){ ?>
                                                <br/>
                                                <span style="color:#F26722; font-size:13px;"><?php echo $row->count_reschedule; ?> times rescheduled</span>
                                                <?php } ?>
                                               
                                                <?php if($row->is_bracket == 1){ ?>
                                                 <br/>
                                                <img src="<?php echo base_url(); ?>images/Bracket.png" style="width:30%"/>
                                                 <br/>
                                                <?php }?>
                                               
                                                <a href="javascript:void(0)" data-popover="true" style="width:10px;border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html="true" data-content="<?php if(isset($row->part_cancel_reason)){ echo implode('<br>',explode(',',$row->part_cancel_reason));}?>" > <img id="<?php echo 'spare_rescheduled'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif"  style="width:84%"/></a>
                                                <a href="javascript:void(0)" > <img style="width: 83%;" id="<?php echo 'spare_delivered_rescheduled'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 89%;" id="<?php echo 'spare_cost_given_rescheduled'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <script> $(document).ready(function(){ load_cancelled_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'rescheduled');
                                                         load_delivered_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'rescheduled');load_spare_cost_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'rescheduled');
                                                });
                                                
                                                </script>
                                                <?php if($row->booking_files_purchase_invoice){ ?>
                                                <br/>
                                                <a target='_blank' href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $row->booking_files_purchase_invoice; ?>"  title = 'Purchase Invoice Verified' aria-hidden = 'true'><img src="<?php echo base_url(); ?>images/varified.png" style="width:30%"/></a>
                                               <?php } ?>
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
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->partner_internal_status;?>
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
                                            <td><?php echo $row->appliance_brand;?></td>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
                                            <?php  echo $row->count_escalation." times"; ?>
                                        </td>
                                         <?php if($is_engineer_app){ ?>
                                        <td style="vertical-align: middle;">
                                            <select id="engineer_<?php echo $sn_no; ?>" class="engineer_select" service-id="<?php echo $row->service_id; ?>" engineer-id="<?php echo $row->assigned_engineer_id; ?>" booking-id="<?php echo $row->booking_id; ?>"></select>
                                            <a href='<?php echo base_url(); ?>service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>
                                        </td>
                                         <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row->booking_id?>',0)"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Relevant  Content" id ="<?php echo $row->booking_id?>"  onclick="show_contacts(this.id,1,'<?php echo $row->partner_id; ?>')"><i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>service_center/inventory/inventory_list_by_model/<?php echo $row->partner_id; ?>/<?php echo $row->service_id; ?>/<?php echo $row->booking_id; ?>" target="_blank"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        </td>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?> <?php if ($is_engineer_app == '1') { ?>  disabled <?php }  ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
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
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id) && $is_engineer_app == '1') { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  //disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                            <td style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-warning btn-sm" data-toggle="modal" data-target="#showBrandCollateral" onclick="get_brand_collateral(<?php echo "'".$row->booking_id."'" ?>)"><i class="fa fa-file-text-o" aria-hidden="true" ></i></button></td>                                        
<!--                                                                    <td>-->
                                       <!--     <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
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
<div role="tabpanel" class="tab-pane" id="inprogress_bookings">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" style="overflow-x: auto;">
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <!--                                <div class="pull-right">Red Bookings are Escalation, Call Customer Immediately !!!</div>-->
                            <table id="inprogress_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User/Phone</th>
                                        <th class="text-center" style="min-width:85px;" data-orderable="false">Address</th>
                                        <th class="text-center" data-orderable="false">Appliance</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" style="min-width:86px;">Booking Date</th>
                                        <th class="text-center">Age</th>
                                        <th class="text-center" data-orderable="false">Call Center Remarks</th>
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Service Center Earning</th>
                                        <!--                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>-->
                                        <?php } ?> 
                                        <th class="text-center" data-orderable="false">Brands</th>
                                        <th  class="text-center" >Escalation</th>
                                        <?php if($is_engineer_app){ ?>
                                        <th  class="text-center" >Assign Engineer</th>
                                        <?php }
                                        if(isset($saas_module) && (!$saas_module)) { ?>
                                        <th class="text-center" data-orderable="false">Edit Request Type</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Helper <br> Document</th>
                                        <th class="text-center" data-orderable="false">Warranty <br> Checker</th>
                                        <th class="text-center" data-orderable="false">Check Spare Part Price</th>
                                        <th class="text-center" data-orderable="false">Contacts</th> 
                                       <th class="text-center" data-orderable="false">Send Email</th>                                                            
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <?php } ?>
                                        <th class="text-center" data-orderable="false">Complete</th>
                                        <th class="text-center" data-orderable="false">Cancel</th>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <th class="text-center"  data-orderable="false">Reschedule</th>
                                        <?php }?>
                                        <th class="text-center" data-orderable="false">JobCard</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php $sn_no = 1; ?>
                                    <?php foreach($bookings[4] as $key =>$row){
                                        ?>
                                    <tr  style="text-align: center;"  >
                                        <td style="vertical-align: middle;">
                                            <?php echo $sn_no; if($row->is_upcountry == 1) { ?>
                                            <i data-popover="true" data-html=true data-content="Click on it to display upcountry details" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->amount_due;?>', '<?php echo $row->flat_upcountry;?>')" style='color: red;font-size: 28px; cursor: pointer' class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;     max-width: 200px;">
                                            <a  target="_blank" href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row->booking_id));?>"  title='View'>
                                            <?php echo $row->booking_id; ?>
                                                
                                            </a>
                                            
                                                <?php if($row->count_reschedule > 0){ ?>
                                                 <br/>
                                                <span style="color:#F26722; font-size:13px;"><?php echo $row->count_reschedule; ?> times rescheduled</span>
                                                <?php } ?>
                                           
                                                <?php if($row->is_bracket == 1){ ?>
                                                <br/>
                                                <img src="<?php echo base_url(); ?>images/Bracket.png" style="width:30%"/>
                                                <?php }?>
                                            
                                               <?php if($row->booking_files_purchase_invoice){ ?>
                                                <br/>
                                                <a target='_blank' href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/<?php echo $row->booking_files_purchase_invoice; ?>"  title = 'Purchase Invoice Verified' aria-hidden = 'true'><img src="<?php echo base_url(); ?>images/varified.png" style="width:20%"/></a>
                                               <?php } ?>
                                                <br/>
                                                <a href="javascript:void(0)" data-popover="true" style="border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 125px;" data-html="true" data-content="<?php if(isset($row->part_cancel_reason)){ echo implode('<br>',explode(',',$row->part_cancel_reason));}?>" > <img style="width: 83%;" id="<?php echo 'spare_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 83%;" id="<?php echo 'spare_delivered_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <a href="javascript:void(0)" > <img style="width: 89%;" id="<?php echo 'spare_cost_given_today'.$key; ?>" src="<?php echo base_url(); ?>images/loader.gif" /></a>
                                                <script> $(document).ready(function(){ load_cancelled_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');
                                                         load_delivered_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');load_spare_cost_status('<?php echo $row->booking_id;?>', '<?php echo $key;?>', 'today');
                                                });
                                                
                                                </script>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->customername."<br/>".$row->booking_primary_contact_no;?>
                                        </td>
                                        <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;vertical-align: middle;" data-html=true data-content="<?= $row->booking_address.", ".$row->booking_pincode; ?> ">
                                            <?php echo $row->booking_address.", ".$row->booking_pincode; ?> 
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?php if($row->amount_due > 0){ ?> <span style="font-weight:bold">Paid </span> <?php } else { ?> <span style="font-weight:bold">Free </span><?php  }  echo $row->request_type." ". $row->services; ?>
                                        </td>
                                        <td style="max-width: 100px; word-wrap:break-word;vertical-align: middle;">
                                            <?=$row->partner_internal_status;?>
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
                                            <?php if($row->is_penalty == 1 ){ ?>
                                            <p class="incentive" style="color:#F26722;font-size: 14px;">Incentive Lost</p>
                                            <?php } else { ?>
                                            <div class="countdown blink" data-popover="true" style="white-space:nowrap;color:#F26722; font-size:13px; overflow:hidden;text-overflow:ellipsis;white-space: initial;" data-html=true data-content="Time Left To Update Booking & Get Incentive" ></div>
                                            <?php } ?>
                                        </td>                                        
                                        <?php } ?>
                                            <td style="vertical-align: middle;"> <?=  strtoupper($row->appliance_brand); ?></td>
                                        <td style="vertical-align: middle;">
                                            <div class="blink">
                                                <?php if($row->count_escalation > 0){ ?> 
                                                <div class="esclate">Escalated Booking</div>
                                                <?php } ?>
                                            </div>
                                            <?php  echo $row->count_escalation." times"; ?>
                                        </td>
                                        <?php if($is_engineer_app){ ?>
                                        <td style="vertical-align: middle;"><select id="engineer_<?php echo $sn_no; ?>" class="engineer_select" service-id="<?php echo $row->service_id; ?>" engineer-id="<?php echo $row->assigned_engineer_id; ?>" booking-id="<?php echo $row->booking_id; ?>"></select>
                                            <a href='<?php echo base_url(); ?>service_center/add_engineer' class='btn btn-info btn-sm' target='_blank'><i class='fa fa-user' aria-hidden='true'></i></a>
                                        </td>
                                        <?php }
                                        if(isset($saas_module) && (!$saas_module)) { ?>
                                        <td style="vertical-align: middle;">
                                            <a target="_blank" href="<?php echo base_url(); ?>service_center/get_sf_edit_booking_form/<?php echo urlencode(base64_encode($row->booking_id))?>" style="width: 36px;background: #795b95;border: #795b95;" class="btn btn-sm btn-primary"  title="Edit Request Type"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        </td>
                                        <?php } ?>
                                        <td style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-warning btn-sm" data-toggle="modal" data-target="#showBrandCollateral" onclick="get_brand_collateral(<?php echo "'".$row->booking_id."'" ?>)"><i class="fa fa-file-text-o" aria-hidden="true" ></i></button></td>
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/warranty/<?= $row->partner_id ?>/<?= $row->service_id ?>/<?= $row->appliance_brand ?>" target="_blank" class='btn btn-sm btn-success' title='Warranty Checker'><i class='fa fa-certificate' aria-hidden='true'></i></a></td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>service_center/inventory/inventory_list_by_model/<?php echo $row->partner_id; ?>/<?php echo $row->service_id; ?>/<?php echo $row->booking_id; ?>" target="_blank"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Relevant  Contact" id ="<?php echo $row->booking_id?>"  onclick="show_contacts(this.id,1,'<?php echo $row->partner_id; ?>')"><i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row->booking_id?>',0)"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>                                        
                                        <?php if($this->session->userdata('is_update') == 1){ ?>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-sm btn-primary <?php if ((is_null($row->assigned_engineer_id) && $is_engineer_app == '1') || !empty($row->service_center_closed_date)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo urlencode(base64_encode($row->booking_id));?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                                        </td>
                                        <?php } ?>
                                        <?php if($this->session->userdata('is_update') == 0){ ?>
                                        <td style="vertical-align: middle;">
                                            <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                                        </td>
                                        <?php } ?>
                                        <td style="vertical-align: middle;">
                                            <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id) && $is_engineer_app == '1') { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                                        </td>
                                        <td style="vertical-align: middle;"><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                                        </td>                                        
                                        <td style="vertical-align: middle;"><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/jobcards-pdf/<?php echo $row->booking_jobcard_filename; ?> " class='btn btn-sm btn-warning btn-sm' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    </tr>
                                    <?php $sn_no++; } ?>
                                </tbody>
                            </table>
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
                                    <th class="text-center" data-orderable="false">Part Warranty Status</th>
                                    <th class="text-center" data-orderable="false">Partner/Warehouse</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Serial Number</th>
                                    <th class="text-center" data-orderable="false">Parts</th>                                
                                    <th class="text-center" data-orderable="false">Shipped Date</th>
                                    <th class="text-center" data-orderable="false">Age</th>
                                    <th class="text-center" data-orderable="false">View</th>
                                    <th class="text-center" data-orderable="false">Receive</th>
                                    <th class="text-center" data-orderable="false">Send Email</th> 
                                    <th class="text-center" data-orderable="false">Contacts</th>
                                    <th class="text-center" data-orderable="false">Update</th>
                                    <th class="text-center" data-orderable="false">Cancel&nbsp;Part</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn_no1 = 1; 
                                foreach($spare_parts_data as $key =>$row){
                                 ?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no1; ?>
                                    </td>
                                    <td>
                                        
                                        <a  target="_blank" href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'>
                                            <?php echo $row['booking_id']; ?>
                                                
                                            </a>
                                    </td>
                                    <td>
                                        <?php echo (($row['part_warranty_status'] == 1)? "In Warranty": "Out Of Warranty"); ?>
                                    </td>
                                    <td>
                                        <?php echo $row['entity_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['model_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['serial_number']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['parts_requested']; ?>
                                    </td>
                                    <td>
                                        <?php if($row['shipped_date'] != "0000-00-00"){echo $row['shipped_date'];} ?>
                                    </td>                                    
                                    <td>
                                        <?php
                                                if (!empty($row['age_of_request'] > 1)) {
                                                    echo $row['age_of_request'] . " days";
                                                } else {
                                                    echo $row['age_of_request'] . " day";
                                                }
                                        ?>
                                    </td>                                    
                                    <td>
                                        <a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a>
                                    </td>
                                      <td>
                                        <?php if(!is_null($row['parts_shipped'])){ ?>  <a onclick="return confirm('Are you sure?')" class="btn btn-sm btn-success" href="<?php echo base_url(); ?>service_center/acknowledge_delivered_spare_parts/<?php echo $row['booking_id']; ?>/<?php echo $this->session->userdata('service_center_id') ?>/<?php echo $row['id']; ?>/<?php echo $row['partner_id']; ?>" style="<?php if(is_null($row['parts_shipped'])){ echo 'pointer-events:none;';}?>">Receive</a> <?php } else { echo "Part Shipment Pending";} ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo$row['booking_id'];?>',0)"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a style="width: 36px;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Contacts" id ="<?php echo $row['booking_id'];?>"  onclick="show_contacts(this.id,1,'<?php echo $row['partner_id']; ?>')"><i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                        <?php if(($row['status'] == SPARE_PART_ON_APPROVAL && ( $row['part_warranty_status'] == SPARE_PART_IN_WARRANTY_STATUS || $row['part_warranty_status'] == SPARE_PART_IN_OUT_OF_WARRANTY_STATUS ))){ ?>
                                           <a class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_spare_parts_required/<?php echo urlencode(base64_encode($row['id'])); ?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                                        <?php } ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                        <?php if($row['status'] == SPARE_PARTS_REQUESTED || $row['status'] == SPARE_PART_ON_APPROVAL || $row['status'] == SPARE_OOW_EST_GIVEN || $row['status'] == SPARE_OOW_EST_REQUESTED){ ?>
                                           <button type="button" data-keys="spare_parts_cancel" data-booking_id="<?php echo $row['booking_id']; ?>" data-url="<?php echo base_url(); ?>employee/inventory/update_action_on_spare_parts/<?php echo $row['id'] . '/' . $row['booking_id'] . '/CANCEL_PARTS';  ?>" class="btn btn-sm btn-danger open-adminremarks" data-toggle="modal" data-target="#cancelSpareModal"><i class='fa fa-times' aria-hidden='true'></i></button>
                                        <?php } ?>
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
<div id="showBrandCollateral" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Brand Collateral</h4>
      </div>
        <div class="modal-body" id="collatral_container">
             <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php } ?>
<?php if($this->session->userdata('is_engineer_app') == 1){ ?>
<div role="tabpanel" class="tab-pane" id="bookings_on_approval">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="review_html">
                            
                        </div>
                           <div id="loading1" style="text-align: center;">
                               <img src="<?php echo base_url();?>images/loader.gif" style="width:60px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div id="relevant_content_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="background-color:  #2C9D9C;border-color: #2C9D9C;">
                <button type="button" class="close btn-primary well"  data-dismiss="modal"style="color: white;">&times;</button>
                <h4 class="modal-title"style="color: white;background-color: #2c9d9c;border-color: #2c9d9c;border: 0px; text-align: center;">Contacts</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>


    </div>
</div>
<div id="send_email_form" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="background-color:  #2C9D9C;border-color: #2C9D9C;">
                <button type="button" class="close btn-primary well"  data-dismiss="modal"style="color: white;">&times;</button>
                <p class="modal-title"style="color: white;background-color: #2c9d9c;border-color: #2c9d9c;border: 0px; text-align: center; font-size:18px;" id="email_title"></p>
            </div>
            <div class="modal-body">
                <div id="form_container">
                <form action="" method="post">
                    <input type="hidden" value="" id="internal_email_booking_id">
                    <input type="hidden" value="" id="internal_email_booking_vendor">  
                    <div class="form-group">
                    <label for="subject">To : </label>
                    <input type="text" class="form-control" id="internal_email_booking_to">
                    </div>
                    <div class="form-group">
                    <label for="subject">CC: </label>
                    <input type="text" class="form-control" id="internal_email_booking_cc">
                    </div>
                    <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="internal_email_booking_subject">
                    </div>
                    <div class="form-group">
                    <label for="text">Message</label>
                    <textarea class="form-control" rows="5" id="internal_email_booking_msg"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-default" style="color: #fff;background-color: #2c9d9c;border-color: #2c9d9c;float:right;" onclick="send_booking_internal_conversation_email()">Send Email</button>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                    </form>
                    </div>
                        <div id="msg_container" style="text-align: center;display: none;">
                     <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
            </div>
        </div>


    </div>
</div>
<!-- Cancel Spare Modal Starts -->
<div id="cancelSpareModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="background-color:  #2C9D9C;border-color: #2C9D9C;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title" style="color: white;text-align: center;"></h4>
            </div>
            <div class="modal-body">
                <h4 style="padding: 3px;font-size: 1em;display: block;" id="status_label" class="modal-title">Spare Cancel Reason *</h4>
                <div id="part_warranty_option" style="padding-bottom: 20px; display: block;">
                    <select class="form-control" id="spare_cancel_reason" name="spare_cancel_reason" value="">
                    </select>
                </div>
                <h4 style="padding: 3px;font-size: 1em;" id="remarks_label" class="modal-title">Remarks *</h4>
                <textarea rows="3" class="form-control" id="textarea" placeholder="Enter Remarks"></textarea>
                <input style="margin-top:20px; display: none" type="number" name="charge" class="form-control" id="charges" placeholder="Enter Courier Charge" />
            </div>
            <input type="hidden" id="url"></input>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" style="color: #fff;background-color: #2c9d9c;border-color: #2c9d9c;" onclick="return reject_parts()" id="reject_btn">Cancel</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Cancel Spare Modal Ends -->
<?php if($this->session->userdata('is_engineer_app') == 1){ ?>
<script>
    get_review_table();
    function get_review_table(){
        console.log("data");
        $.ajax({
            type: "POST",
            processData: false,
            contentType: false,
            url: "<?php echo base_url() ?>service_center/review",
            success: function (data) {
                $("#loading1").css("display", "none");
                $("#review_html").html(data);
                console.log(data);
                
            }
          });
    }
    
</script>

<?php } ?>
<script>
     function show_contacts(bookingID,create_booking_contacts_flag,partner_id){
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url()  ?>employee/service_centers/get_booking_contacts/'+bookingID,
                        data: {},
                        success: function (response) {
                            if(create_booking_contacts_flag){
                              create_booking_contacts(response,partner_id);
                            }
                            else{
                                var result = JSON.parse(response);
                                var am_email='';
                                for(var i=0;i<result.length;i++) {
                                    am_email += result[i].am_email+",";
                                }
                                $("#internal_email_booking_vendor").val(result[0].assigned_vendor_id);
                                $("#internal_email_booking_to").val(am_email);
                                $("#internal_email_booking_cc").val(result[0].service_center_email);
                                $("#internal_email_booking_subject").val(result[0].partner+"- Query From SF For - "+bookingID);
                            }
                       }
                    });
                }
                function create_email_form(booking_id,create_booking_contacts_flag){
                    $("#internal_email_booking_subject").prop('disabled', true);
                    $("#internal_email_booking_cc").prop('disabled', true);
                    $("#email_title").html("Send Email For Booking "+booking_id);
                    $("#send_email_form").modal("show");
                    $("#internal_email_booking_id").val(booking_id);
                    show_contacts(booking_id,create_booking_contacts_flag,'');
                }
                function send_booking_internal_conversation_email(){ 
                    var to = $("#internal_email_booking_to").val();
                    var cc = $("#internal_email_booking_cc").val();
                    var booking_vendor = $("#internal_email_booking_vendor").val();
                    var booking_id = $("#internal_email_booking_id").val();
                    var subject = $("#internal_email_booking_subject").val();
                    var msg = $(" #internal_email_booking_msg").val();
                    document.getElementById("msg_container").style.display='block';
                    document.getElementById("form_container").style.display='none';
                    if(booking_id && subject && msg){
                        $.ajax({
                           type: 'post',
                           url: '<?php echo base_url()  ?>employee/service_centers/process_booking_internal_conversation_email',
                           data: {'booking_id':booking_id,'subject':subject,'msg':msg,'to':to,'cc':cc,'booking_vendor':booking_vendor},
                           success: function (response) {
                                $("#msg_container").html(response);
                                $("#internal_email_booking_to").val("");
                                $("#internal_email_booking_cc").val("");
                                $("#internal_email_booking_id").val("");
                                $("#internal_email_booking_subject").val("");
                                $("#internal_email_booking_msg").val("");
                                location.reload();
                          }
                       });
                    }
                    else{
                        alert("Subject Or Message should not be blank ");
                        return false;
                    }
                }
    function create_booking_contacts(response,partner_id){
        var data="";
        var result = JSON.parse(response);
        /*if(partner_id == '<?php echo VIDEOCON_ID ?>'){
            data =data +  "<tr><td>1) </td><td>247around Account Manager <br>(Gujarat, Rest of maharashtra)</td><td> Adil Akhtar</td><td>9205732247</td></tr>";
            data =data +  "<tr><td>2) </td><td>247around Account Manager <br>(Tamil Nadu, Andhra Pradesh, Uttar Pradesh, Uttarakhand, Kerala, Orissa, West Bengal, Jharkhand)</td><td> Amit Tyagi</td><td>7303653247</td></tr>";
            data =data +  "<tr><td>3) </td><td>247around Account Manager <br>(Delhi, Haryana, Punjab, J&K, Madhya Pradesh, Karnataka)</td><td> Sakshi</td><td>9810948247</td></tr>";
            data =data +  "<tr><td>4) </td><td>Brand POC</td><td>"+result[0].partner_poc+"</td><td>"+result[0].poc_contact+"</td></tr>";
        }
        else{
            data =data +  "<tr><td>1) </td><td>247around Account Manager</td><td>"+result[0].am+"</td><td>"+result[0].am_caontact+"</td></tr>";
            data =data +  "<tr><td>2) </td><td>Brand POC</td><td>"+result[0].partner_poc+"</td><td>"+result[0].poc_contact+"</td></tr>";
        }*/
        if(result.length > 0) {
            var j;
            for(var i=0;i<result.length;i++) {j=i+1;
                data =data +  "<tr><td>"+j+") </td><td>247around Account Manager <br>("+result[i].am_state+")</td><td>"+result[i].am+"</td><td>"+result[i].am_caontact+"</td></tr>";
            }
            data =data +  "<tr><td>"+(++j)+") </td><td>Brand POC</td><td>"+result[0].partner_poc+"</td><td>"+result[0].poc_contact+"</td></tr>";
            var tb="<table class='table  table-bordered table-condensed ' >";
            tb+='<thead>';
            tb+='<tr>';
            tb+='<th class="jumbotron col-md-1">SNo.</th> ';
            tb+='<th class="jumbotron col-md-6">Role</th>';
            tb+='<th class="jumbotron  col-md-5">Name</th>';
            tb+='<th class="jumbotron  col-md-5">Contact</th>';
            tb+='</tr>';
            tb+='</thead>';
            tb+='<tbody>';
            tb+=data;
            tb+='</tbody>';
            tb+='</table>';
            $("#relevant_content_modal .modal-body").html(tb);
            $('#relevant_content_table').DataTable();
            $('#relevant_content_table  th').css("background-color","#ECEFF1");
            $('#relevant_content_table  tr:nth-child(even)').css("background-color","#FAFAFA");
            $("#relevant_content_modal").modal("show");
        } else {
            $("#relevant_content_modal .modal-body").html('No contacts.');
            $("#relevant_content_modal").modal("show");
        }
    }
    
    function getBookingEngineers(){
        $(".engineer_select").each(function(){  
            var service_id = $(this).attr("service-id");
            var engineer_id = $(this).attr("engineer-id");
            var id = $(this).attr("id");
            if(service_id){
                $.ajax({
                    type: 'post',
                    url: '<?php echo base_url()  ?>employee/engineer/get_service_based_engineer',
                    data: {'service_id':service_id, 'engineer_id':engineer_id, 'service_center_id':<?php echo $this->session->userdata('service_center_id'); ?>},
                    success: function (response) {
                        response = JSON.parse(response);
                        if(response.status){
                            $("#"+id).html(response.html);
                            //$("#"+id).css("display", "inline");
                            //$("#"+id).parent().find("a").css("display", "none");
                            $("#"+id).select2();
                        }
                        /*
                        else{
                            $("#"+id).parent().find("a").remove();
                            $("#"+id).parent().append(response.html);
                            //$("span").attr("aria-labelledby", "select2-"+id+"-container").css("display", "none");
                            $("#"+id).css("display", "none");
                        }
                        */
                   }
                });
            }
        });
    }
    
    $(document).ready(function(){
        getBookingEngineers();
    });
    
    $(".engineer_select").change(function(){
        var booking_id = $(this).attr("booking-id");
        if (confirm('Are you sure to assign this engineer for Booking Id '+booking_id)) {
            var engineer = {};
            engineer[booking_id] = $(this).val();
            $.ajax({
                type: 'post',
                url: '<?php echo base_url()  ?>employee/service_centers/assigned_engineers',
                data: {engineer:engineer},
                success: function (response) {
                    //console.log(response);
                    location.reload();
               }
            });
        } 
    });
    
    setInterval(function(){ getBookingEngineers(); }, 30000);
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
        var keys = $(this).data('keys'); 
        
        if(keys == 'spare_parts_cancel'){                      
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_cancellation_reasons/sf_spare_parts',
                success: function (data) {
                    $("#spare_cancel_reason").html(data); 
                }
            });
        }
        $('#modal-title').text(booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        $("#charges").css("display","none");
        $("#charges").val(0);

    });
    
    function reject_parts(){
      var remarks =  $('#textarea').val();
      var courier_charge = $('#charges').val();
      var reason = $.trim($('#spare_cancel_reason option:selected').text());
      var cancel_id = $('#spare_cancel_reason option:selected').val();
      
      if($.trim(reason) === "") {
          alert("Please Enter Spare Cancellation Reason");
          return false;
      }
      
      if(remarks !== ""){
        $('#reject_btn').attr('disabled',true);
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{ remarks:remarks,courier_charge:courier_charge, spare_cancel_reason:reason, spare_cancel_id:cancel_id },
            success: function(data){
                $('#reject_btn').attr('disabled',false);
                if(data === "Success"){
                    $('#cancelSpareModal').modal('hide');
                    alert("Updated Successfully");
                    location.reload();
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } else {
          alert("Please Enter Remarks");
      }
    }
   
    </script>