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
            <?php  if($this->uri->segment(3) == 'view' || $this->uri->segment(3) == 'view_all_pending_booking'
                        || $this->uri->segment(3) == 'service_center_sorted_booking'){?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/view'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view/0/100'?>" <?php if($this->uri->segment(5) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/view/0/200'?>" <?php if($this->uri->segment(5) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/view_all_pending_booking'?>" <?php if($this->uri->segment(3) == 'view_all_pending_booking'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(5)){if($this->uri->segment(5) != 50 || $this->uri->segment(5) != 100 || $this->uri->segment(5) != 200 ){?>
                    <option value="" <?php if($this->uri->segment(5) == count($Bookings)){ echo 'selected';}?>><?php echo $this->uri->segment(5);?></option>
                    <?php } }?>
                </select>
            </div>
            <?php } ?>
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
                    <th>
                    <a href="<?php echo base_url();?>employee/booking/status_sorted_booking">Status</a>
                    </th>
                    <th>
                       <a href="<?php echo base_url();?>employee/booking/service_center_sorted_booking">Service Center</a>
                    </th>
		            <th>Call</th>
                    <th>View Jobcard</th>
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

                    <?php $count = 1; ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr id="row_color<?php echo $count;?>">
                    <td><input type="hidden" class="mail_to_vendor<?php echo $count;?>" id="mail_to_vendor<?php echo $count;?>" value="<?php echo $row->mail_to_vendor;?>"></td>
                    <td><?=$row->id?>.</td>

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
                    <td><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?=$row->assigned_vendor_id;?>"><?php if(!empty($row->service_centre_name)){ echo $row->service_centre_name." / ".$row->primary_contact_name." / ".$row->primary_contact_phone_1 ; } ?></a></td>
                            <td><a class="btn btn-sm btn-info"
				   href="<?php echo base_url(); ?>employee/booking/call_customer/<?= $row->phone_number; ?>"
    				   title = "call" onclick = "return confirm('Call Customer ?');">
    				    <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
    				</a>
			    </td>
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
                            . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }
                        else
                        {
                            echo "<a id='edit' class='btn btn-sm btn-success disabled' "
                          . "href=" . base_url() . "employee/booking/get_reschedule_booking_form/$row->booking_id title='Reschedule'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a id='edit' class='btn btn-sm btn-warning' "
                            . "href=" . base_url() . "employee/booking/get_cancel_booking_form/$row->booking_id title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                        }
                        else
                        {
                            echo "<a id='edit' class='btn btn-sm btn-warning disabled' "
                                . "href=" . base_url() . "employee/booking/get_cancel_booking_form/$row->booking_id  title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a class='btn btn-sm btn-danger btn-sm' "
                            . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>";
                        } else if ($row->current_status == 'Review')
                        {
                            echo "<a class='btn btn-sm btn-danger btn-sm' "
                            . "href=" . base_url() . "employee/new_booking/review_bookings/$row->booking_id title='Complete'><i class='fa fa-eye-slash' aria-hidden='true'></i></a>";
                        }
                        else
                        {
                            echo "<a class='btn btn-sm btn-danger btn-sm disabled' "
                                . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Complete'><i class='fa fa-thumbs-up' aria-hidden='true'></i></a>";
                        }
                        ?>
                    </td>

                    <td>
                      <?php
                      if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                      {
                        echo "<a class='btn btn-sm btn-info' "
                        . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true'></i></a>";
                      }
                      else
                      {
                        echo "<a class='btn btn-sm btn-info disabled' "
                          . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id title='Job Card'> <i class='fa fa-file-pdf-o' aria-hidden='true'></i></a>";
                      }
                      ?>
                    </td>

                    <td>
                        <?php

                        if(!is_null($row->assigned_vendor_id) && !is_null($row->booking_jobcard_filename)
                            && ($row->mail_to_vendor==0))
                        {
                            echo "<a id='b_notes" . $count. "' class='btn btn-sm btn-success' onclick='show(this.id)' title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
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
                                . "href=" . base_url() . "employee/bookingjobcard/send_mail_to_vendor/$row->booking_id title='Mail'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>";
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
                        <a href="<?php echo base_url();?>employee/vendor/get_reassign_vendor_form/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-success' title="Re- assign"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                    </td>
                    <td>
                                <a href="<?php echo base_url(); ?>employee/vendor/get_vendor_escalation_form/<?php echo $row->booking_id; ?>" <?php if($row->assigned_vendor_id == null){ echo "disabled"; }?> class='btn btn-sm btn-danger' title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
                        </td>

                </tr>
                <?php $count++;
                }?>
                <input type="hidden" id="total_no_rows" value="<?php echo $count;?>">

                </table>
                <?php if(!empty($links)){ ?><div class="pagination" style="float:left;"> <?php if(isset($links)){echo $links;} ?></div> <?php }  ?>
                </div>

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
            var not_found = (id.indexOf(value) == -1);
            $(this).closest('tr').toggle(!not_found);
            return not_found;
        });
    });
});
</script>

