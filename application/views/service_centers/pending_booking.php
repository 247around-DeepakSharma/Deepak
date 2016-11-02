<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
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
        <h2 style="margin-left:15px;margin-top:15px;">Pending Bookings</h2>
        <div class="col-md-6">
            <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active"><a href="#today_booking" aria-controls="today_booking" role="tab" data-toggle="tab">Today & Past Bookings</a></li>
                <?php if($this->session->userdata('is_update') == 1){ ?>
                <li role="presentation"><a href="#tomorrow_booking" aria-controls="tomorrow_booking" role="tab" data-toggle="tab">Future Bookings</a></li>
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
                                <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                    <thead >
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Booking Id</th>
                                            <th class="text-center">User</th>
<!--                                            <th class="text-center">Mobile</th>-->
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Pincode</th>
                                            <th class="text-center">Appliance</th>
                                            <th class="text-center">Booking Date</th>
                                            <th class="text-center">Age</th>
                                            <th class="text-center">Remarks</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>
                                            <th class="text-center">Update</th>
                                            <?php } else if($this->session->userdata('is_update') == 0){ ?>
                                            <th>Reschedule</th>

                                                <?php }?>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Cancel</th>
                                            <th class="text-center">Complete</th>
                                            <th class="text-center">JobCard</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Penalty</th>
                                            <?php } ?>
                                            <th  class="text-center">No of Reschedule</th>
                                            <th  class="text-center">No of Escalation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sn_no = 1; ?>
                                        <?php foreach($bookings[1] as $key =>$row){?>
                                        <tr  style="text-align: center;">
                                            <td>
                                                <?php echo $sn_no; ?>
                                            </td>
                                            <td >
                                                <?=$row->booking_id?>
                                            </td>
                                            <td>
                                                <?=$row->customername;?>
                                            </td>
<!--                                            <td>
                                                <?= $row->booking_primary_contact_no; ?>
                                            </td>-->
                                            
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;" data-html=true data-content="<?= $row->booking_address; ?> ">
                                                <?= $row->booking_address; ?> 
                                            </td>
                                            <td>
                                                <?= $row->booking_pincode; ?> 
                                            </td>
                                            <td>
                                                <?= $row->services; ?>
                                            </td>
                                            <td>
                                                <?= $row->booking_date; ?> /
                                                <?= $row->booking_timeslot; ?>
                                            </td>
                                            <td> <?= $row->age_of_booking." day"; ?></td>
                                            <td data-popover="true" style="position: absolute; border:0px; width: 10%" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                                <div class="marquee">
                                                    <div><span><?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?></span></div>
                                                </div>
                                            </td>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <td>
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
                        </td>
                        <td>
                        <?php if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php } ?>
                        </td>
                        <td>
                        <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo base64_encode($row->booking_id);?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                        </td>
                        <?php } else if($this->session->userdata('is_update') == 0){ ?>
                            <td>
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>

                            <?php } ?>
                        <td><a class='btn btn-sm btn-primary <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' href="<?php echo base_url();?>service_center/booking_details/<?php echo base64_encode($row->booking_id);?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                        <td><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo base64_encode($row->booking_id); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                        </td>
                        <td>
                        <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo base64_encode($row->booking_id);?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                        </td>
                        <td><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
                        <td><?php echo "Rs. ".$row->penalty; ?></td>
                        <?php } ?>
                        <td>
                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  echo $row->count_reschedule; ?></span></a>
                      
                        </td>
                        <td>
                             <a target='_blank' href="Javascript:void(0)" class='btn btn-sm btn-danger' title="Escalate"><i>
                                        <i class="fa fa-circle" aria-hidden="true"></i></i><span class=sup><?php  echo $row->count_escalation; ?></span></a>
                        </td>
                         </tr>
                        <?php $sn_no++; } ?>
                        </tbody>
                        </table>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
                        <div id="loading" class="loading" style="text-align: center;">
                        <input type= "submit" id="submit_button"  class="btn btn-danger btn-md submit_button" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Assigned Engineer" >
                        </div>
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
<!--                                            <th class="text-center">Mobile</th>-->
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Pincode</th>
                                            <th class="text-center">Appliance</th>
                                            <th class="text-center">Booking Date</th>
                                            <th class="text-center">Age</th>
                                            <th class="text-center">Remarks</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Engineer</th>
                                            <th class="text-center">Re-Assign</th>
                                            <th class="text-center">Update</th>
                                            <?php } else if($this->session->userdata('is_update') == 0){ ?>
                                            <th>Reschedule</th>

                                                <?php }?>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Cancel</th>
                                            <th class="text-center">Complete</th>
                                            <th class="text-center">JobCard</th>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <th class="text-center">Penalty</th>
                                            <?php } ?>
                                            <th  class="text-center">No of Reschedule</th>
                                            <th  class="text-center">No of Escalation</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bookings[2] as $key =>$row){?>
                                    <tr  style="text-align: center;">
                                            <td>
                                                <?php echo $sn_no; ?>
                                            </td>
                                            <td >
                                                <?=$row->booking_id?>
                                            </td>
                                            <td>
                                                <?=$row->customername;?>
                                            </td>
<!--                                            <td>
                                                <?= $row->booking_primary_contact_no; ?>
                                            </td>-->
                                            
                                            <td data-popover="true" style="position: absolute; border:0px; white-space:nowrap; overflow:hidden;text-overflow:ellipsis;max-width: 85px;" data-html=true data-content="<?= $row->booking_address; ?> ">
                                                <?= $row->booking_address; ?> 
                                            </td>
                                            <td>
                                                <?= $row->booking_pincode; ?> 
                                            </td>
                                            <td>
                                                <?= $row->services; ?>
                                            </td>
                                            <td>
                                                <?= $row->booking_date; ?> /
                                                <?= $row->booking_timeslot; ?>
                                            </td>
                                            <td> <?= $row->age_of_booking." day"; ?></td>
                                            <td data-popover="true" style="position: absolute; border:0px; width: 10%" data-html=true data-content="<?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?>">
                                                <div class="marquee">
                                                    <div><span><?php if(isset($row->admin_remarks)){ echo $row->admin_remarks;}?></span></div>
                                                </div>
                                            </td>
                                            <?php if($this->session->userdata('is_update') == 1){ ?>
                                            <td>
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
                        </td>
                        <td>
                        <?php if (!is_null($row->assigned_engineer_id)) { ?>  <button type="button"  class="btn btn-sm btn-success" onclick="edit_engineer(<?php echo $sn_no; ?>)"><i class="fa fa-user" aria-hidden='true'></i></button> <?php } ?>
                        </td>
                        <td>
                        <a class="btn btn-sm btn-primary <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } ?>" style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url(); ?>service_center/update_booking_status/<?php echo base64_encode($row->booking_id);?>" ><i class='fa fa-edit' aria-hidden='true'></i></a>
                        </td>
                        <?php } else if($this->session->userdata('is_update') == 0){ ?>
                            <td>
                              <button type="button"  class="btn btn-sm btn-success" onclick="setbooking_id('<?=$row->booking_id?>')" data-toggle="modal" data-target="#myModal" ><i class='fa fa-calendar' aria-hidden='true'></i></button>
                           </td>

                            <?php } ?>
                        <td><a class='btn btn-sm btn-primary <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' href="<?php echo base_url();?>service_center/booking_details/<?php echo base64_encode($row->booking_id);?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a></td>
                        <td><a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo base64_encode($row->booking_id); ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a>
                        </td>
                        <td>
                        <a href="<?php echo base_url(); ?>service_center/complete_booking_form/<?php echo base64_encode($row->booking_id);?>" class='btn btn-sm btn-success <?php if($this->session->userdata('is_update') == 1){ ?> <?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>
                        </td>
                        <td><a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename?> " class='btn btn-sm btn-warning btn-sm <?php if($this->session->userdata('is_update') == 1){ ?><?php if (is_null($row->assigned_engineer_id)) { ?>  disabled <?php } } ?>' download  ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
                        <td><?php echo "Rs. ".$row->penalty; ?></td>
                        <?php } ?>
                        <td>
                            <a target="_blank" id="edit" class='btn btn-sm btn-success' href="Javascript:void(0)"
                               title='Reschedule'><i><i class='fa fa-calendar' aria-hidden='true' ></i></i><span class='sup'><?php  echo $row->count_reschedule; ?></span></a>
                      
                        </td>
                        <td>
                             <a target='_blank' href="Javascript:void(0)" class='btn btn-sm btn-danger' title="Escalate"><i>
                                        <i class="fa fa-circle" aria-hidden="true"></i></i><span class=sup><?php  echo $row->count_escalation; ?></span></a>
                        </td>
                         </tr>
                    <?php $sn_no++; } ?>
                    </tbody>
                    </table>
                        <?php if($this->session->userdata('is_update') == 1){ ?>
                        <div id="loading" class="loading" style="text-align: center;">
                        <input type= "submit" id="submit_button"  class="btn btn-danger btn-md submit_button" style="background-color:#2C9D9C; border-color: #2C9D9C;" value ="Assigned Engineer" >
                        </div>
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
                                        <a class='btn btn-sm btn-primary' href="<?php echo base_url();?>service_center/booking_details/<?php echo base64_encode($row['booking_id']);?>"  title='View'><i class='fa fa-eye' aria-hidden='true'></i></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>service_center/acknowledge_delivered_spare_parts/<?php echo$row['booking_id']; ?>" style="width:23px;"><img src="<?php echo base_url(); ?>images/icon_receiving.png" style="width:23px;" /></a>
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
    
        $('#tomorrow_datatable').dataTable( {
            "pageLength": 50
        } );
    
        $('#spare_required_datatable').dataTable({
            "pageLength": 50
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
    .marquee {
    height: 100%;
    width: 100%;
    color: red;
    overflow: hidden;
    position: relative;
    }
    .marquee div {
    display: block;
    width: 100%;
    height: 22px;
    position: relative;
    overflow: hidden;
    animation: marquee 5s linear infinite;
    }
    .marquee span {
    width: 50%;
    }
    @keyframes marquee {
    0% {
    left: 0;
    }
    100% {
    left: -100%;
    }
    }
    
  .sup {
  position: relative;
  bottom: 1ex; 
 font-size: 100%;
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
        if(booking_date ==""){
          
           $("#error").text('Plese Enter Booking Date');
            return false;
        }

        if(remarks ==""){
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

</script>
