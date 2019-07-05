<?php $offset = $this->uri->segment(5); ?>

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

        if (confirm_call === true) {

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

<div id="page-wrapper">
    <div class="">
        <div class="row">
            <!--Cancel Modal-->
            <div id="penaltycancelmodal" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg" >
                  <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_remove_penalty" method="POST">

                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title" style="text-align: center"><b>Remove Penalty</b></h4>
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
            <?php  if($this->uri->segment(3) == 'view' || $this->uri->segment(3) == 'view_all_pending_booking'){?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/view'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view/100/0'?>" <?php if($this->uri->segment(4) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/view/200/0'?>" <?php if($this->uri->segment(4) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/view/500/0'?>" <?php if($this->uri->segment(4) == 500){ echo 'selected';}?>>500</option>
                    <!--<option value="<?php echo base_url().'employee/booking/view/0/All'?>"<?php if($this->uri->segment(5) == "All"){ echo 'selected';}?> >All</option>-->

                </select>
            </div>
            <?php } if($this->uri->segment(3) == 'get_pending_booking_by_partner_id'){?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/get_pending_booking_by_partner_id'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/get_pending_booking_by_partner_id/100/0'?>" <?php if($this->uri->segment(4) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/get_pending_booking_by_partner_id/200/0'?>" <?php if($this->uri->segment(4) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/get_pending_booking_by_partner_id/500/0'?>" <?php if($this->uri->segment(4) == 500){ echo 'selected';}?>>500</option>
                    <!--<option value="<?php //echo base_url().'employee/booking/get_pending_booking_by_partner_id/0/All'?>"<?php if($this->uri->segment(5) == "All"){ echo 'selected';}?> >All</option>-->

                </select>
            </div>
            <?php } ?>
            <?php if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
               ?> 
            <div class="col-md-3 pull-right" style="margin-top:20px;">
                 <input type="search" class="form-control pull-right"  id="search" placeholder="search">
            </div>
            <div style="width:100%;margin-left:10px;margine-right:5px;">
                <h1 align="left">
                    <b><?php if(isset($search)){echo "Searched Booking";} else { echo "Pending Bookings"; } ?> <?php if(isset($Count)){ echo ' (' . $Count . ')'; } ?></b>
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
                    <th>S No.</th>
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
                    <th>Remove Penalty</th>


                    </tr>

                    </thead>

                    <?php $count = 1; if($offset == 0){ $offset = 1;}else { $offset = $offset+1; }  ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr id="row_color<?php echo $count;?>">
                    <td><input type="hidden" class="mail_to_vendor<?php echo $count;?>" id="mail_to_vendor<?php echo $count;?>" value="<?php echo $row->mail_to_vendor;?>"></td>
                    <td><?php echo $offset; if($row->is_upcountry == 1) { ?>.<i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row->assigned_vendor_id;?>','<?php echo $row->booking_id;?>', '<?php echo $row->amount_due;?>', '<?php echo $row->flat_upcountry;?>')" class="fa fa-road" aria-hidden="true"></i><?php } ?></td>

                            <td>
                            <?php
                            if (is_null($row->booking_jobcard_filename)) {
                                echo $row->booking_id;
                            } else {
                                echo '<a target="_blank" href="https://s3.amazonaws.com/'.BITBUCKET_DIRECTORY.'/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                            }
                            ?>
                        </td>
                    <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                    <td><a href="<?php echo base_url();?>employee/user/finduser?phone_number=<?=$row->phone_number;?>"><?= $row->booking_primary_contact_no; ?></a></td>
                    <td><?= $row->services; ?></td>
                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <td id="status_<?php echo $row->booking_id; ?>">
                            <div class="blink">
                                <?php if ($row->count_escalation > 0) { ?> <div class="esclate">Escalated</div>
                                <?php } ?>
                            </div>
                            <?php if ($row->count_escalation > 0) { ?>
                                <?php echo '<b>' . $row->count_escalation . " times</b><br>";
                            }
                            ?>
                        <?php echo $row->current_status; ?>
                        </td>
                    <td><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?=$row->assigned_vendor_id;?>" target="_blank"><?php if(!empty($row->service_centre_name)){ echo $row->service_centre_name." / ".$row->primary_contact_name." / ".$row->primary_contact_phone_1 ; } ?></a></td>

                     <td><button type="button" onclick="outbound_call(<?php echo $row->phone_number; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button></td>

    			   <td>
			    <?php echo "<a class='btn btn-sm btn-primary' "
			    . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
			    ?>
                        </td>

                        <td>
                        <?php
                        if (($row->current_status == 'Pending' || $row->current_status == 'Rescheduled') && empty($row->service_center_closed_date))
                        {
                            echo "<a target='_blank' id='edit' class='btn btn-sm btn-success' "
                            . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                        }
                        else
                        {
                            echo "<a id='edit' target='_blank' class='btn btn-sm btn-success disabled' "
                          . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true' ></i></a>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a id='edit' target='_blank' class='btn btn-sm btn-warning' "
                            . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                        }
                        else
                        {
                            echo "<a id='edit' target='_blank' class='btn btn-sm btn-warning disabled' "
                                . "href=" . base_url() . "employee/booking/get_cancel_form/$row->booking_id  title='Cancel'> <i class='fa fa-times' aria-hidden='true' ></i></a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if($row->assigned_vendor_id == ""){
                            echo "<a target='_blank' class='btn btn-sm btn-danger btn-sm disabled' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                        }else{
                            if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                            {
                                echo "<a target='_blank' class='btn btn-sm btn-danger btn-sm' "
                                . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                            } else if ($row->current_status == 'Review')
                            {
                                echo "<a target='_blank' class='btn btn-sm btn-danger btn-sm' "
                                . "href=" . base_url() . "employee/booking/review_bookings/$row->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true' ></i></a>";
                            }
                            else
                            {
                                echo "<a target='_blank' class='btn btn-sm btn-danger btn-sm disabled' "
                                    . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true' ></i></a>";
                            }
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
                        echo "<a target='_blank' class='btn btn-sm btn-info disabled' "
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
                            echo "<a target='_blank' class='btn btn-sm btn-primary'"
                                . "href=" . base_url() . "employee/booking/get_edit_booking_form/$row->booking_id title='Edit Booking'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                    ?></td>
                     <td>
                        <a target='_blank' href="<?php echo base_url();?>employee/vendor/get_reassign_vendor_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-success <?php if(is_null($row->assigned_vendor_id)){ echo 'disabled';} ?>' title="Re- assign"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                    </td>
                    <td>
                        <?php  
                            $b_date = date("Y-m-d", strtotime($row->booking_date));
                            $date1=date_create($b_date);
                            $date2=date_create(date("Y-m-d"));
                            $diff=date_diff($date2,$date1); 
                            $b_days = $diff->days;
                            if($diff->invert == 1){
                                $b_days = -$diff->days;
                            } 
                            $b_time = explode("-", $row->booking_timeslot);
                            $b_timeslot =  date("H", strtotime($b_time[0]));
                            
                           ?>
                        <a target='_blank' href="<?php echo base_url(); ?>employee/vendor/get_vendor_escalation_form/<?php echo $row->booking_id; ?>" 
                            <?php if($row->current_status != "Rescheduled") { if($row->assigned_vendor_id == null){ echo "disabled"; } else if($b_days >0){ echo "disabled";} 
                            else if($b_days ==0){ if($b_timeslot > date("H")){ echo "disabled";} } } ?>  
                           class='btn btn-sm btn-danger' title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
                        </td>
                        
                        <td>
                            <a class='btn btn-sm col-md-4' style='background:#FF9E80;margin-left:10px;padding-right: 17px;' onclick='get_penalty_details("<?php echo $row->booking_id; ?>","<?php echo $row->current_status; ?>")'  href='javascript:void(0)' title='Remove Penalty'> <i class='fa fa-times-circle' aria-hidden='true'></i></a>
                        </td>
                    

                </tr>
                <?php $count++; $offset++;
                }?>
                <input type="hidden" id="total_no_rows" value="<?php echo $count;?>">

                </table>
                <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
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
<script type="text/javascript">
    $("#search").keyup(function () {
    var value = this.value.toLowerCase().trim();

    $("table tr").each(function (index) {
        if (!index) return;
        $(this).find("td").each(function () {
            var id = $(this).text().toLowerCase().trim();
            var not_found = (id.indexOf(value) === -1);
            $(this).closest('tr').toggle(!not_found);
            return not_found;
        });
    });
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
    
    
    function get_penalty_details(booking_id,status){

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_penalty_details_data/' + booking_id+"/"+status,
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
</script>
<style type="text/css">
    .sup {
  position: relative;
  bottom: 1ex; 
  font-size: 100%;
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
    
    .esclate {
    width: auto;
    height: 17px;
    background-color: #F73006;
    color: #fff;
    /* transform: rotate(-26deg); */
    margin-left: 0px;
    font-weight: bold;
    margin-right: 0px;
    font-size: 12px;
}
</style>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>