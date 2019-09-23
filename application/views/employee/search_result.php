<?php $offset = $this->uri->segment(4); ?>
<!--<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
<script>
    $(function(){
      $('#dynamic_select').bind('change', function () {
          var url = $(this).val();
          if (url) {
              window.location = url;
          }
          return false;
      });
    });
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
       
        if (confirm_call == true) {
            
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);
                   
                }
            });
        } else {
            return false;
        }
    
    }
    
    $(document).ready(function()
    {
       $('.dialog').hide();
    
    });
    
    //Function to show the specific popup form
    function show(id)
    {
        var type = id.search("b_notes");
        var count = id.replace( /^\D+/g, '');
    
        if (type >= 0) {
            $('#bookingMailForm'+count).toggle(500);
        }
        else {
            $('#reminderMailForm'+count).toggle(500);
        }
    }
    
    //Function to send email to vendor using ajax
    function send_email_to_vendor(i)
    {
    
        var id = $("#booking_id"+i).val();
        var additional_note = $("#valueFromMyButton"+i).val();
    
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/bookingjobcard/send_mail_to_vendor/' + id + "/" + additional_note,
                success: function(response) {
                    var resAlert = response.search("Mail sent to Service Center successfully.");
    
                    if (resAlert >= 0)
                        alert("Mail sent to Service Center successfully.")
                    else
                        alert("Mail could not be sent, please try again.");
                }
        });
    
        $("#bookingMailForm"+i).toggle(500);
    }
    
    //Function to send reminder email to vendor
    function send_reminder_email_to_vendor(i)
    {
        var id = $("#booking_id"+i).val();
        var additional_note = $("#reminderMailButton"+i).val();
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/bookingjobcard/send_reminder_mail_to_vendor/' + id + "/" + additional_note,
                success: function(response) {
                    var resAlert = response.search("Reminder mail sent to Service Center successfully.");
    
                    if (resAlert >= 0)
                        alert("Reminder mail sent to Service Center successfully.")
                    else
                        alert("Reminder mail could not be sent, please try again.");
                }
        });
    
        $("#reminderMailForm"+i).toggle(500);
    }
      function form_submit(booking_id){
        $.ajax({
                type:"POST",
                data:{booking_id:booking_id},
                url:"<?php echo base_url() ?>employee/vendor/get_add_vendor_to_pincode_form",
                  success: function (data) {
                      $("#page-wrapper").html(data);
                  }
              });
          }
    
</script>
<style type="text/css">
    table{
    width: 99%;
    }
    th,td{
    border: 1px #f2f2f2 solid;
    text-align:center;
    vertical-align: center;
    padding: 2px;
    }
    
    
</style>
<!--Cancel Modal-->
<div id="penaltycancelmodal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" >
      <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_remove_penalty" method="POST">
          
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-align: center"><b>Penalty Removal Reason</b></h4>
          </div>
          <div class="modal-body">
              <span id="error_message" style="display:none;color: red;margin-bottom:10px;"><b>Please Select At Least 1 Booking</b></span>
              <div id="open_model"></div>
          </div>
          <div class="modal-footer">
              <input type="button" onclick="form_submit()" value="Submit" class="btn btn-info " form="modal-form" id="remove_penalty">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
          
      </form>
  </div>
</div>
<!-- end cancel model -->
<div id="page-wrapper">
    <div class="">
        <div class="row">
            <?php
            $data = search_for_key($Bookings);
            $count = 1;
            ?>
            <?php if(isset($data['FollowUp'])){ ?>
            <h1><b>Pending Queries</b></h1>
            <table >
                <thead>
                    <tr>
                        <th>S No.</th>
                        <th width="160px;">
                            <a href="<?php echo base_url();?>employee/booking/view_bookings_by_status/Pending/">Booking Id</a>
                        </th>
                        <th width="140px;">User Name</th>
                        <th width="125px;">Phone No.</th>
                        <th width="125px;">Service Name</th>
                        <th width="165px;">Potential Value</th>
                        <th width="165px;">Booking Date/Time</th>
                        <th width="100px;">Status</th>
                        <th width="100px;">City</th>
                        <th width="100px;">Vendor Status</th>
                        <th width="250px;">Query Remarks</th>
                        <?php if($c2c) { ?>
                              <th width="60px;">Call</th>
                              <th width="60px;">SMS</th>
                        <?php } ?>
                        
                        <th width="60px;">View</th>
                        <th width="60px;">Update</th>
                        <th width="60px;">Cancel</th>
                    </tr>
                </thead>
                <?php  if($offset ==0){ $offset = 1;} else { $offset = $offset+1; } ?>
                <?php  foreach($Bookings as $key =>$row){ if($row->current_status == "FollowUp") {
                       $sms_json =  json_encode(array(
                                        'phone_number'=>$row->phone_number, 
                                        'booking_id'=>$row->booking_id, 
                                        'user_id' => $row->user_id,
                                        'service' => $row->services,
                                        'request_type' => $row->request_type,
                                        'partner_id' => $row->partner_id,
                                        'booking_state' => $row->state
                                    ));
                ?>
                 <tr <?php if($row->internal_status == "Missed_call_confirmed"){ ?> style="background-color:rgb(162, 230, 162); color:#000;"<?php } ?> >
                    <td><?php echo $count; ?></td>
                    <input type="hidden" id="<?php echo "service_id_".$count; ?>"  value="<?php echo $row->service_id;?>"/>
                    <input type="hidden" id="<?php echo "pincode_".$count ; ?>" value="<?php echo $row->booking_pincode; ?>" />
                    <td><?= $row->booking_id; ?></td>
                    <td><a href="<?php echo base_url(); ?>employee/user/finduser?phone_number=<?php echo $row->phone_number; ?>"><?php echo $row->customername; ?></a></td>
                    <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?php echo $row->phone_number;?>"><?php echo $row->booking_primary_contact_no; ?></a></td>
                    <td><?= $row->services; ?></td>
                    <td><?= $row->potential_value; ?></td>
                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <td id="status_<?php echo $row->booking_id; ?>">
                        <?php
                            echo $row->current_status;
                            if ($row->current_status != $row->internal_status)
                                echo " (" . $row->internal_status . ")";
                            ?>
                    </td>
                    <td><?= $row->city; ?></td>
                    <td> 
                        
                        <select id="<?php  echo "av_vendor".$count; ?>" style="max-width:100px; display:none;">
                            <option>Vendor Available</option>
                        </select>
                        <a href="javascript:void(0)" style="color: red; display:none" id="<?php echo "av_pincode".$count ; ?>" onclick='form_submit("<?php echo $row->booking_id?>")'><?php print_r($row->booking_pincode); ?></a>
                         
                    </td>
                    <td><?= $row->query_remarks; ?></td>
                    <?php if($c2c) { ?>
                    <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-color"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                    </td>
                    <td><button type="button" json-data='<?php echo $sms_json; ?>' onclick="send_whtasapp_number(this)" class="btn btn-sm btn-color"><i class = 'fa fa-envelope-o fa-lg' aria-hidden = 'true'></i></button></td>
                    <?php } ?>
                    
                    <td>
                        <?php echo "<a class='btn btn-sm btn-color' "
                            . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            ?>
                    </td>
                    <td><?php
                        echo "<a class='btn btn-sm btn-color' href=".base_url()."employee/booking/get_edit_booking_form/$row->booking_id title='Update'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    
                    <td>
                        <?php
                            echo "<a class='btn btn-sm btn-color' href=".base_url()."employee/booking/get_cancel_form/$row->booking_id/FollowUp title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                            ?>
                    </td>
                </tr>
                <?php $count++; $offset++;
                    } } ?>
            </table>
            <?php } if(isset($data['Pending'])){ ?>
            <h1 align="left">
                <b>Pending Bookings</b>
                <?php
                    if(isset($booking_status) && $booking_status === 0){
                        echo "<small class='text-danger'>(Booking Cancelled By SF)</small>";
                    }else if($booking_status === 1){
                        echo "<small class='text-danger'>(Booking Completed By SF)</small>";
                    }
                ?>
            </h1>
            <?php
                if (isset($success) && $success !== 0) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $success . '</strong>
                   </div>';
                }
                ?>
            <div class="col-md-12">
                <table >
                    <thead>
                        <tr>
                            <th></th>
                            
                            <th>Booking Id</th>
                            <th>User Name</th>
                            <th>Phone No.</th>
                            <th>Service Name</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Service Center</th>
                            <?php if(isset($saas_module) && (!$saas_module)) { ?>
                            <th>Contacts</th>
                            <?php } if($c2c) { ?>
                                 <th>Call</th>
                            <?php } ?>
                            <th>View</th>
                            <th>Reschedule</th>
                            <th>Cancel</th>
                            <th>Complete</th>
                            <th>Job Card</th>
                            <th>Mail</th>
                            <th>Reminder Mail</th>
                            <th>Edit Booking</th>
                            <th>Re-assign</th>
                            <th>Escalate</th>
                            <th>Remove Penalty</th>
                            <th>Helper Document</th>
                        </tr>
                    </thead>
                    <?php if($offset == 0){ $offset = 1;}else { $offset = $offset+1; }  ?>
                    <?php foreach($Bookings as $key =>$row){ if($row->current_status == "Pending" || $row->current_status == "Rescheduled"){ ?>
                    <tr id="row_color<?php echo $count;?>">
                      
                        <td><input type="hidden" class="mail_to_vendor<?php echo $count;?>" id="mail_to_vendor<?php echo $count;?>" value="<?php echo $row->mail_to_vendor;?>"><?php if($row->is_upcountry == 1) { ?>.<i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row->assigned_vendor_id;?>','<?php echo $row->booking_id;?>', '<?php echo $row->amount_due;?>', '<?php echo $row->flat_upcountry;?>')" class="fa fa-road" aria-hidden="true"></i><?php } ?></td>
                        
                        <td>
                            <?php
                                if (is_null($row->booking_jobcard_filename)) {
                                    echo $row->booking_id;
                                } else {
                                    echo '<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                                }
                                ?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?= $row->booking_primary_contact_no; ?></a></td>
                        <td><?= $row->services; ?></td>
                        <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                        <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                        <td><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?=$row->assigned_vendor_id;?>" target="_blank"><?php if(!empty($row->service_centre_name)){ echo $row->service_centre_name." / ".$row->primary_contact_name." / ".$row->primary_contact_phone_1 ; } ?></a></td>
                        <?php if(isset($saas_module) && (!$saas_module)) { ?>
                        <td><button type="button" title = "Booking Contacts" class="btn btn-sm btn-color" data-toggle="modal" data-target="#relevant_content_modal" id ='<?php echo $row->booking_id ?>' onclick="show_contacts(this.id,1)">
                <span class="glyphicon glyphicon-user"></span></button></td>
                        <?php } if($c2c) { ?>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->phone_number; ?>)" class="btn btn-sm btn-color"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button></td>
                        <?php } ?>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                if (($row->current_status == 'Pending' || $row->current_status == 'Rescheduled') && empty($row->service_center_closed_date))
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-color disabled' "
                                  . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-color disabled' "
                                        . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id  title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                    echo "<a class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                                } else if ($row->current_status == 'Review')
                                {
                                    echo "<a class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/review_bookings/$row->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a class='btn btn-sm btn-color disabled' "
                                        . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                  echo "<a target='_blank' class='btn btn-sm btn-color' "
                                  . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                  echo "<a class='btn btn-sm btn-color disabled' "
                                    . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if(!is_null($row->assigned_vendor_id) && !is_null($row->booking_jobcard_filename)
                                    && ($row->mail_to_vendor==0))
                                {
                                    echo "<a  id='b_notes" . $count. "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
                                    echo "<div class='dialog' id='bookingMailForm".$count."'>";
                                    echo "<form class='mailform'>";
                                    echo "<textarea style='width:200px;height:80px;' id='valueFromMyButton".$count."' name='valueFromMyButton".$count."' placeholder='Enter Additional Notes'></textarea>";
                                    echo "<input type='hidden' id='booking_id".$count."' name='booking_id".$count."' value=$row->booking_id >";
                                    echo "<div align='center'>";
                                    echo "<a id='btnOK".$count."' class='btn btn-sm btn-success' onclick='send_email_to_vendor(".$count.");'>Ok</a>";
                                    echo "</div>";
                                    echo "</form>";
                                    echo "</div>";
                                }
                                else
                                {
                                    echo "<a class='btn btn-sm btn-color disabled'"
                                        . "href=" . base_url() . "employee/bookingjobcard/send_mail_to_vendor/$row->booking_id title='Mail'><i class='fa fa-envelope-o' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if(!is_null($row->assigned_vendor_id) && !is_null($row->booking_jobcard_filename)
                                  && ($row->mail_to_vendor))
                                {
                                    echo "<a id='r_notes" . $count . "' class='btn btn-sm btn-color' onclick='show(this.id)' title='Remainder Mail' ><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
                                    echo "<div class='dialog' id='reminderMailForm".$count."'>";
                                    echo "<form class='remindermailform'>";
                                    echo "<textarea style='width:200px;height:80px;' id='reminderMailButton".$count."' name='reminderMailButton".$count."' placeholder='Enter Additional Notes'></textarea>";
                                    echo "<input type='hidden' id='booking_id".$count."' name='booking_id".$count."' value=$row->booking_id >";
                                    echo "<div align='center'>";
                                    echo "<a id='btnOK".$count."' class='btn btn-sm btn-success' onclick='send_reminder_email_to_vendor(".$count.");'>Ok</a>";
                                    echo "</div>";
                                    echo "</form>";
                                    echo "</div>";
                                }
                                else
                                {
                                    echo "<a class='btn btn-sm btn-color disabled'"
                                        . "href=" . base_url() . "employee/bookingjobcard/send_reminder_mail_to_vendor/$row->booking_id Reminder Mail><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
                                }
                                ?>
                        </td>
                        <td><?php
                            echo "<a class='btn btn-sm btn-color'"
                                . "href=" . base_url() . "employee/booking/get_edit_booking_form/$row->booking_id title='Edit Booking'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                            ?></td>
                        <td>
                            <a target='_blank' href="<?php echo base_url();?>employee/vendor/get_reassign_vendor_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-color' title="Re- assign"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <a target='_blank' href="<?php echo base_url(); ?>employee/vendor/get_vendor_escalation_form/<?php echo $row->booking_id; ?>" <?php if($row->assigned_vendor_id == null){ echo "disabled"; }?> class='btn btn-sm btn-color' title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <a class='btn btn-sm btn-color col-md-4' style='margin-left:10px;padding-right: 17px;' onclick='get_penalty_details("<?php echo $row->booking_id; ?>","<?php echo $row->current_status; ?>","<?php echo $row->assigned_vendor_id;?>")'  href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-color" title="Helper Document" data-toggle="modal" data-target="#showBrandCollateral" onclick="get_brand_collateral('<?php echo $row->booking_id; ?>')"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php $count++; $offset++;
                        } }?>
                    <input type="hidden" id="total_no_rows" value="<?php echo $count;?>">
                </table>
                <?php } if(isset($data['Completed'])){ ?>
                <h1 style="margin-top: 50px;"><b>Completed Bookings</b></h1>
                <table >
                    <thead>
                        <tr>
                            <th></th>
                            <th width="150px;">
                                <a href="<?php echo base_url();?>employee/booking/view_bookings_by_status/Pending">Booking Id</a>
                            </th>
                            <th width="125px;">User Name</th>
                            <th width="125px;">Phone No.</th>
                            <th width="125px;">Service Name</th>
                            <th width="170px;">Service Centre</th>
                            <th width="150px;">Service Centre City</th>
                            <th width="125px;">Completion Date</th>
                            <?php if($c2c) { ?>
                            <th width="60px;">Call</th>
                            <?php } ?>
                            <th width="60px;">Edit</th>
                            <th width="60px;">Cancel</th>
                            <th width="60px;">Open</th>
                            <th width="60px;">View</th>
                            <th width="60px;">Rate</th>
                             <th width="60px;">Repeat</th>
                            <th width="160px;">Penalty</th>
                        </tr>
                    </thead>
                    <?php foreach($Bookings as $key =>$row){
                        if($row->current_status == "Completed"){ ?>
                    <tr>
                        <td><?php if($row->is_upcountry == 1) { ?>.<i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row->assigned_vendor_id;?>','<?php echo $row->booking_id;?>', '<?php echo $row->amount_due;?>', '<?php echo $row->flat_upcountry;?>')" class="fa fa-road" aria-hidden="true"></i><?php } ?></td>
                        <td><?php
                            echo '<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><?= $row->booking_primary_contact_no; ?></td>
                        <td><?= $row->services; ?></td>
                        <td><?php if(isset($row->service_centre_name)){ ?><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $row->assigned_vendor_id;?>"><?= $row->service_centre_name; } ?></a></td>
                        <td><?=$row->city; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($row->closed_date)); ?></td>
                        <?php if($c2c) { ?>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-color"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                        </td>
                        <?php } ?>
                        <td>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Cancel'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                ?>
                        </td>
                        <td><?php
                            echo "<a id='edit' class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$row->current_status title='Open' target='_blank'> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                            ?>
                        </td>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                $unreachableCount = $row->rating_unreachable_count;
                                $style = 'margin-top: 24px';
                                if($row->rating_unreachable_count == 0){
                                    $unreachableCount = "";
                                    $style = "margin-top: 10px";
                                }
                                if ($row->current_status == 'Completed' && empty($row->rating_stars ))
                                {
                                    
                                    echo "<a  style = '".$style."' class='btn btn-sm btn-color' "
                                            . "href=" . base_url() . "employee/booking/get_rating_form/$row->booking_id/$row->current_status title='Rate' target='_blank'><i class='fa fa-star-o' aria-hidden='true'>"
                                            . "</i></a><p style='text-align:center;color: red;'>$unreachableCount</p>";
                                }
                                else
                                {
                                    echo "<a style = '".$style."' class='btn btn-sm btn-color disabled' "
                                        . "href=" . base_url() . "employee/booking/get_rating_form/$row->booking_id title='Rate' target='_blank'><i class='fa fa-star-o' aria-hidden='true'></i></a>"
                                            . "<p style='text-align:center;color: red;'>$unreachableCount</p>";
                                }
                                ?>
                        </td>
                        <td>
                             <?php  if ($row->current_status =='Completed') {
                                            $today = strtotime(date("Y-m-d"));
                                            $closed_date = strtotime($row->closed_date);
                                            $completedDays = round(($today - $closed_date) / (60 * 60 * 24));
                                            if($completedDays < _247AROUND_REPEAT_BOOKING_ALLOWED_DAYS){
                                    ?>
                            <a style="background: #00695C;border-color: #00695C;" target="_blank" href="<?php echo base_url(); ?>employee/booking/get_repeat_booking_form/<?php echo $row->booking_id;?>" class="btn btn-small btn-success btn-sm" title="Create Repeat Booking"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            <?php
                                            }
                                 }
                                 ?>
                          </td>
                        <td>
                        <?php
                        echo "<a class='btn btn-sm btn-color col-md-4'"
                            . "href=" . base_url() . "employee/vendor/get_escalate_booking_form/$row->booking_id/$row->current_status title='Add Penalty'> <i class='fa fa-plus-square' aria-hidden='true'></i></a>";
                            // Case 0: When No Penalty has been Added for this Booking - Penalty can be Added in this case
                        if ($row->penalty_active == '' ){                            
                            
                             echo "<a class='btn btn-sm  btn-color col-md-4' style='margin-left:10px;cursor:not-allowed;opacity:0.5;' "
                            . "href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>";
                            
                        }else{
                            
                            // Case 1:Penalty to be Deducted - Penalty can be Removed Allowed in this case
                            if($row->penalty_active == 1){
                               
                            ?>  
                            <!-- <a class='btn btn-sm col-md-4' style='background:#FFEB3B;margin-left:10px' onclick='return assign_id("<?php //echo $row->booking_id?>","<?php //echo $row->current_status?>")' data-toggle='modal' data-target='#penaltycancelmodal' href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a> -->
                            <a class='btn btn-sm col-md-4 btn-color' style='margin-left:10px' onclick='get_penalty_details("<?php echo $row->booking_id?>","<?php echo $row->current_status?>","<?php echo $row->assigned_vendor_id;?>")'  href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>
                            <?php     
                            }
                            
                            //Case 2: Penalty has been Removed - No Action Permitted 
                            else if ($row->penalty_active == 0) {
                             echo "<a  class='btn btn-sm btn-color col-md-4' style='margin-left:10px;cursor:not-allowed;opacity:0.5;' "
                            . "href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>";
                                
                            }
            
                        }
                        ?>
                    </td>
                    </tr>
                    <?php
                        } }?>
                </table>
                <?php } if(isset($data['Cancelled'])){?>
                <h1 align="left" style="margin-top: 30px;"><b>Cancelled Bookings/Queries<b></b></h1>
                </b>
                <table style="margin-bottom:50px;">
                    <thead>
                        <tr>
                            
                            <th width="150px;">
                                <a href="<?php echo base_url();?>employee/booking/view_bookings_by_status/Pending">Booking Id</a>
                            </th>
                            <th width="110px;">User Name</th>
                            <th width="110px;">Phone No.</th>
                            <th width="110px;">Service Name</th>
                            <th width="110px;">Service Centre</th>
                            <th width="150px;">Service Centre City</th>
                            <th width="110px;">Completion Date</th>
                            <?php if($c2c) { ?>
                            <th width="60px;">Call</th>
                            <?php } ?>
                            <th width="60px;">Complete</th>
                            <th width="60px;">Open</th>
                            <th width="60px;">View</th>
                            <th width="160px;">Penalty</th>
                        </tr>
                    </thead>
                    <?php foreach($Bookings as $key =>$row){
                        if($row->current_status == "Cancelled"){ ?>
                    <tr>
                       
                        <td><?php
                            echo '<a href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><?= $row->booking_primary_contact_no; ?></td>
                        <td><?= $row->services; ?></td>
                        <td>
                            <?php if (substr($row->booking_id, 0, 2) != 'Q-') { ?>
                            <a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $row->assigned_vendor_id;?>"><?= $row->service_centre_name; ?></a>
                            <?php } ?>
                        </td>
                        <td><?=$row->city; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($row->closed_date)); ?></td>
                        <?php if($c2c) { ?>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-color"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                        </td>
                        <?php } ?>
                        <td>
                            <?php if (substr($row->booking_id, 0, 2) != 'Q-') { ?>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-color' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Edit'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                                } ?>
                        </td>
                        <td>
                        <?php if (substr($row->booking_id, 0, 2) == 'Q-') { if(($this->session->userdata('user_group') !== _247AROUND_ADMIN || $this->session->userdata('user_group') !== _247AROUND_DEVELOPER) && strtotime($row->closed_date) <= strtotime("-1 Months")){
                            echo "<a class='btn btn-sm btn-color' "
                        . "href=" . base_url() . "employee/booking/open_cancelled_query/$row->booking_id  title='open' disabled><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }else{
                            echo "<a class='btn btn-sm btn-color' "
                        . "href=" . base_url() . "employee/booking/open_cancelled_query/$row->booking_id  title='open'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }} else { if(($this->session->userdata('user_group') !== _247AROUND_ADMIN || $this->session->userdata('user_group') !== _247AROUND_DEVELOPER) && strtotime($row->closed_date) <= strtotime("-1 Months")){
                            echo "<a id='edit' class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$row->current_status title='Open' target='_blank' disabled> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }else{
                            echo "<a id='edit' class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$row->current_status title='Open' target='_blank'> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }} ?>
                        </td>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-color' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                        <?php
                        echo "<a class='btn btn-sm btn-color col-md-4'"
                            . "href=" . base_url() . "employee/vendor/get_escalate_booking_form/$row->booking_id/$row->current_status title='Add Penalty'> <i class='fa fa-plus-square' aria-hidden='true'></i></a>";
                            // Case 0: When No Penalty has been Added for this Booking - Penalty can be Added in this case
                        if ($row->penalty_active == '' ){                            
                            
                             echo "<a class='btn btn-sm  btn-color col-md-4' style='margin-left:10px;cursor:not-allowed;opacity:0.5;' "
                            . "href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>";
                            
                        }else{
                            
                            // Case 1:Penalty to be Deducted - Penalty can be Removed Allowed in this case
                            if($row->penalty_active == 1){
                               
                            ?>  
                            <!-- <a class='btn btn-sm col-md-4' style='background:#FFEB3B;margin-left:10px' onclick='return assign_id("<?php echo $row->booking_id?>","<?php echo $row->current_status?>")' data-toggle='modal' data-target='#penaltycancelmodal' href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a> -->
                            <a class='btn btn-sm btn-color col-md-4' style='margin-left:10px' onclick='get_penalty_details("<?php echo $row->booking_id?>","<?php echo $row->current_status?>","<?php echo $row->assigned_vendor_id;?>")'  href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>
                            <?php     
                            }
                            
                            //Case 2: Penalty has been Removed - No Action Permitted 
                            else if ($row->penalty_active == 0) {
                             echo "<a  class='btn btn-sm btn-color col-md-4' style='margin-left:10px;cursor:not-allowed;opacity:0.5;' "
                            . "href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>";
                                
                            }
            
                        }
                        ?>
                    </td>
                    </tr>
                    <?php
                        } } ?>
                </table>
                <?php } ?>
                <?php if(isset($data['Pending']) || isset($data['Cancelled']) || isset($data['FollowUp']) || isset($data['Completed'])){} else { ?><h1><b>Booking Not Found</b></h1><?php }?>
            </div>
        </div>
    </div>
</div>
</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="open_model1">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upcountry Call</h4>
            </div>
            <div class="modal-body" >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                <center><img id="loader_gif_contact" src="<?php echo base_url(); ?>images/loadring.gif"></center>
            </div>
        </div>
    </div>
    <!-- Helper Document Model -->
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
    <!-- End Helper Document Model -->

<script type="text/javascript">
        $(document).ready(function() {
        <?php if(isset($data['FollowUp_count'])){ ?>
        var total_booking = Number(<?php echo $data['FollowUp_count']; ?>);
        for(var c = 1; c<= total_booking; c++  ){
            var index = c;
            var  service_id = $("#service_id_"+ c).val();
            var pincode = $("#pincode_"+ c).val();
            if(pincode !==""){            
                get_vendor(pincode, service_id, index);
            } 
       
        }
        <?php } ?>
    });
    function open_upcountry_model(sc_id, booking_id, amount_due, flat_upcountry){
      
       if(sc_id){
           $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due+"/"+flat_upcountry,
            success: function (data) {
             $("#open_model1").html(data); 

             $('#myModal1').modal('toggle');

            }
          });
       }else{
            alert("Waitng for upcountry approval");
       }
    }
    function get_vendor(pincode, service_id, index){
        
        $.ajax({
                type:"POST",
                url:"<?php echo base_url()?>employee/vendor/get_vendor_availability/"+pincode+"/"+service_id,
                
                success: function(data){
                    //console.log(data);
                    if(data ===""){
                        
                        $("#av_pincode"+index).css("display",'inherit');
                    } else {
                        $("#av_vendor"+index).css("display",'inherit');
                        $("#av_vendor"+index).html(data);
                        
                    }
                    
                }
        });
        
    }
    
    function get_penalty_details(booking_id,status,sf_id){

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_penalty_details_data/' + booking_id+"/"+status,
            data: {sf_id:sf_id},
            success: function (data) {
                if(data === 'penalty not found'){
                    var html = "<div class='text-center text-danger'><strong>"+data+"</strong></div>"
                    $("#open_model").html(html);   
                    $('#penaltycancelmodal').modal('toggle');
                    $('#remove_penalty').hide();
                }else{
                    $("#open_model").html(data);   
                    $('#penaltycancelmodal').modal('toggle');
                    $('#remove_penalty').show();
                }
            }
          });
    }
    
    function form_submit() {
        
        var checkbox_val = [];
        $(':checkbox:checked').each(function(i){
          checkbox_val[i] = $(this).val();
        });
        if(checkbox_val.length === 0){
            $('#error_message').css('display','block');
            return false;
        }else{
            $("#cancellation_form").submit();
        }
    } 
    
    function show_contacts(bookingID,create_booking_contacts_flag){
            $("#relevant_content_modal .modal-body").html("");
            $("#loader_gif_contact").show();
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url()  ?>employee/service_centers/get_booking_contacts/'+bookingID,
                        data: {},
                        success: function (response) {
                            if(create_booking_contacts_flag){
                              create_booking_contacts(response);
                            }
                       }
                    });
                }


                         function create_booking_contacts(response){
        var data="";
        var result = JSON.parse(response);
        if(result.length > 0) {
            var j;
            for(var i=0;i<result.length;i++) {j=i+1;
                data =data +  "<tr><td>"+j+") </td><td>247around Account Manager <br>("+result[i].am_state+")</td><td>"+result[i].am+"</td><td>"+result[i].am_caontact+"</td></tr>";
            }
            data =data +  "<tr><td>"+(++j)+") </td><td>247around Regional Manager</td><td>"+result[0].rm+"</td><td>"+result[0].rm_contact+"</td></tr>";
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
            $("#loader_gif_contact").hide();
            $("#relevant_content_modal .modal-body").html(tb);
            $('#relevant_content_table').DataTable();
            $('#relevant_content_table  th').css("background-color","#ECEFF1");
            $('#relevant_content_table  tr:nth-child(even)').css("background-color","#FAFAFA");
            $("#relevant_content_modal").modal("show");
        }
    }
    
    function send_whtasapp_number(btn){
       var json = JSON.parse($(btn).attr("json-data"));
       //console.log(json);
        var confirm_sms = confirm("Send Whatsapp Number ?");
        if (confirm_sms == true) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/send_whatsapp_number/'+true,
                data:{phone_no:json.phone_number, booking_id:json.booking_id, user_id:json.user_id, service:json.service, partner_id:json.partner_id, booking_state:json.booking_state},
                success: function(response) {
                    //console.log(response);
                }
            });
        } else { 
            return false;
        }
    }
    
    function  get_brand_collateral(booking_id){
       $('#collatral_container').html('<center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>');
       $.ajax({
         type: 'POST',
         data: {booking_id: booking_id},
         url: '<?php echo base_url(); ?>employee/service_centers/get_learning_collateral_for_bookings/',
         success: function (data) {
             $('#collatral_container').html(data);
         }
       });
    }
</script>
