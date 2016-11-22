<?php $offset = $this->uri->segment(4); ?>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
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
    
    //For row color to check if mail is sent to vendor
    $(document).ready(function()
    {
        var count = $('#total_no_rows').val();
        //alert(count);
        for(var i=1; i<=count; i++)
        {
        var mailsend = $('.mail_to_vendor'+i).val();
        if(mailsend== 0)
        {
            $('#row_color'+i).css("background-color", "#FFEC8B");
        }
        }
    });
    
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
    th{
    height: 50px;
    background-color: #4CBA90;
    color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
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
                            <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a>
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
                        <th width="60px;">Call</th>
                        <th width="60px;">View</th>
                        <th width="60px;">Update</th>
                        <th width="60px;">Cancel</th>
                    </tr>
                </thead>
                <?php  if($offset ==0){ $offset = 1;} else { $offset = $offset+1; } ?>
                <?php  foreach($Bookings as $key =>$row){ if($row->current_status == "FollowUp") { ?>
                 <tr <?php if($row->internal_status == "Missed_call_confirmed"){ ?> style="background-color:rgb(162, 230, 162); color:#000;"<?php } ?> >
                    <td><?php echo $count; ?></td>
                    <input type="hidden" id="<?php echo "service_id_".$count; ?>"  value="<?php echo $row->service_id;?>"/>
                    <input type="hidden" id="<?php echo "pincode_".$count ; ?>" value="<?php echo $row->booking_pincode; ?>" />
                    <td><?= $row->booking_id; ?></td>
                    <td><a href="<?php echo base_url(); ?>employee/user/finduser/0/0/<?php echo $row->phone_number; ?>"><?php echo $row->customername; ?></a></td>
                    <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?php echo $row->phone_number;?>"><?php echo $row->booking_primary_contact_no; ?></a></td>
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
                    <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                    </td>
                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                            . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            ?>
                    </td>
                    <td><?php
                        echo "<a class='btn btn-small btn-success btn-sm' href=".base_url()."employee/booking/get_edit_booking_form/$row->booking_id title='Update'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    
                    <td>
                        <?php
                            echo "<a class='btn btn-small btn-warning btn-sm' href=".base_url()."employee/booking/get_cancel_form/$row->booking_id/FollowUp title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                            ?>
                    </td>
                </tr>
                <?php $count++; $offset++;
                    } } ?>
            </table>
            <?php } if(isset($data['Pending'])){ ?>
            <h1 align="left">
                <b>Pending Boookings</b>
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
                            <th>Call</th>
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
                        </tr>
                    </thead>
                    <?php if($offset == 0){ $offset = 1;}else { $offset = $offset+1; }  ?>
                    <?php foreach($Bookings as $key =>$row){ if($row->current_status == "Pending" || $row->current_status == "Rescheduled"){ ?>
                    <tr id="row_color<?php echo $count;?>">
                        <td><input type="hidden" class="mail_to_vendor<?php echo $count;?>" id="mail_to_vendor<?php echo $count;?>" value="<?php echo $row->mail_to_vendor;?>"></td>
                        
                        <td>
                            <?php
                                if (is_null($row->booking_jobcard_filename)) {
                                    echo $row->booking_id;
                                } else {
                                    echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                                }
                                ?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?= $row->booking_primary_contact_no; ?></a></td>
                        <td><?= $row->services; ?></td>
                        <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                        <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                        <td><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?=$row->assigned_vendor_id;?>" target="_blank"><?php if(!empty($row->service_centre_name)){ echo $row->service_centre_name." / ".$row->primary_contact_name." / ".$row->primary_contact_phone_1 ; } ?></a></td>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->phone_number; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button></td>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-primary' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-success' "
                                    . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-success disabled' "
                                  . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-warning' "
                                    . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a id='edit' class='btn btn-sm btn-warning disabled' "
                                        . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id  title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                    echo "<a class='btn btn-sm btn-danger btn-sm' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                                } else if ($row->current_status == 'Review')
                                {
                                    echo "<a class='btn btn-sm btn-danger btn-sm' "
                                    . "href=" . base_url() . "employee/booking/review_bookings/$row->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                    echo "<a class='btn btn-sm btn-danger btn-sm disabled' "
                                        . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                                {
                                  echo "<a target='_blank' class='btn btn-sm btn-info' "
                                  . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true' ></i></a>";
                                }
                                else
                                {
                                  echo "<a class='btn btn-sm btn-info disabled' "
                                    . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if(!is_null($row->assigned_vendor_id) && !is_null($row->booking_jobcard_filename)
                                    && ($row->mail_to_vendor==0))
                                {
                                    echo "<a  id='b_notes" . $count. "' class='btn btn-sm btn-success' onclick='show(this.id)' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
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
                                    echo "<a class='btn btn-sm btn-success disabled'"
                                        . "href=" . base_url() . "employee/bookingjobcard/send_mail_to_vendor/$row->booking_id title='Mail'><i class='fa fa-envelope-o' aria-hidden='true' ></i></a>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if(!is_null($row->assigned_vendor_id) && !is_null($row->booking_jobcard_filename)
                                  && ($row->mail_to_vendor))
                                {
                                    echo "<a id='r_notes" . $count . "' class='btn btn-sm btn-warning' onclick='show(this.id)' title='Remainder Mail' ><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
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
                                    echo "<a class='btn btn-sm btn-warning disabled'"
                                        . "href=" . base_url() . "employee/bookingjobcard/send_reminder_mail_to_vendor/$row->booking_id Reminder Mail><i class='fa fa-clock-o' aria-hidden='true'></i></a>";
                                }
                                ?>
                        </td>
                        <td><?php
                            echo "<a class='btn btn-sm btn-primary'"
                                . "href=" . base_url() . "employee/booking/get_edit_booking_form/$row->booking_id title='Edit Booking'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                            ?></td>
                        <td>
                            <a target='_blank' href="<?php echo base_url();?>employee/vendor/get_reassign_vendor_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-success' title="Re- assign"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <a target='_blank' href="<?php echo base_url(); ?>employee/vendor/get_vendor_escalation_form/<?php echo $row->booking_id; ?>" <?php if($row->assigned_vendor_id == null){ echo "disabled"; }?> class='btn btn-sm btn-danger' title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
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

                            <th width="150px;">
                                <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a>
                            </th>
                            <th width="125px;">User Name</th>
                            <th width="125px;">Phone No.</th>
                            <th width="125px;">Service Name</th>
                            <th width="170px;">Service Centre</th>
                            <th width="150px;">Service Centre City</th>
                            <th width="125px;">Completion Date</th>
                            <th width="60px;">Call</th>
                            <th width="60px;">Edit</th>
                            <th width="60px;">Cancel</th>
                            <th width="60px;">Open</th>
                            <th width="60px;">View</th>
                            <th width="60px;">Rate</th>
                        </tr>
                    </thead>
                    <?php foreach($Bookings as $key =>$row){
                        if($row->current_status == "Completed"){ ?>
                    <tr>
                        
                        <td><?php
                            echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><?= $row->booking_primary_contact_no; ?></td>
                        <td><?= $row->services; ?></td>
                        <td><?php if(isset($row->service_centre_name)){ ?><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $row->assigned_vendor_id;?>"><?= $row->service_centre_name; } ?></a></td>
                        <td><?=$row->city; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($row->closed_date)); ?></td>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                        </td>
                        <td>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-success' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Cancel'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-danger' "
                                    . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                                ?>
                        </td>
                        <td><?php
                            echo "<a id='edit' class='btn btn-sm btn-warning' "
                                . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$row->current_status title='Open' target='_blank'> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                            ?>
                        </td>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-primary' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($row->current_status == 'Completed' && empty($row->rating_stars ))
                                {
                                    echo "<a class='btn btn-sm btn-danger' "
                                            . "href=" . base_url() . "employee/booking/get_rating_form/$row->booking_id/$row->current_status title='Rate' target='_blank'><i class='fa fa-star-o' aria-hidden='true'></i></a>";
                                }
                                else
                                {
                                    echo "<a class='btn btn-sm btn-danger disabled' "
                                        . "href=" . base_url() . "employee/booking/get_rating_form/$row->booking_id title='Rate' target='_blank'><i class='fa fa-star-o' aria-hidden='true'></i></a>";
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
                                <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a>
                            </th>
                            <th width="125px;">User Name</th>
                            <th width="125px;">Phone No.</th>
                            <th width="125px;">Service Name</th>
                            <th width="170px;">Service Centre</th>
                            <th width="150px;">Service Centre City</th>
                            <th width="125px;">Completion Date</th>
                            <th width="60px;">Call</th>
                            <th width="60px;">Complete</th>
                            <th width="60px;">Open</th>
                            <th width="60px;">View</th>
                        </tr>
                    </thead>
                    <?php foreach($Bookings as $key =>$row){
                        if($row->current_status == "Cancelled"){ ?>
                    <tr>
                       
                        <td><?php
                            echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';?>
                        </td>
                        <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                        <td><?= $row->booking_primary_contact_no; ?></td>
                        <td><?= $row->services; ?></td>
                        <td>
                            <?php if (substr($row->booking_id, 0, 2) != 'Q-') { ?>
                            <a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $row->assigned_vendor_id;?>"><?= $row->service_centre_name; ?></a>
                            <?php } ?>
                        </td>
                        <td><?=$row->city; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($row->closed_date)); ?></td>
                        <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                        </td>
                        <td>
                            <?php if (substr($row->booking_id, 0, 2) != 'Q-') { ?>
                            <?php
                                echo "<a id='edit' class='btn btn-sm btn-success' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Edit'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                                } ?>
                        </td>
                        <td>
                        <?php if (substr($row->booking_id, 0, 2) == 'Q-') { ?>
                        <?php echo "<a class='btn btn-sm btn-warning' "
                        . "href=" . base_url() . "employee/booking/open_cancelled_query/$row->booking_id  title='open'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
    ?>

                        <?php } else { ?>

                        <?php
                            echo "<a id='edit' class='btn btn-sm btn-warning' "
                                . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$row->current_status title='Open' target='_blank'> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                            ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?php echo "<a class='btn btn-sm btn-primary' "
                                . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
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
    
    function get_vendor(pincode, service_id, index){
        
        $.ajax({
                type:"POST",
                url:"<?php echo base_url()?>employee/vendor/get_vendor_availability/"+pincode+"/"+service_id,
                
                success: function(data){
                    console.log(data);
                    if(data ===""){
                        
                        $("#av_pincode"+index).css("display",'inherit');
                    } else {
                        $("#av_vendor"+index).css("display",'inherit');
                        $("#av_vendor"+index).html(data);
                        
                    }
                    
                }
        });
        
    }
    
</script>