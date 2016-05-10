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

</script>
<style type="text/css">
    table{
          width: 99%;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;    
        padding: 6px;
    }
    
    th{
        height: 50px;
        background-color: #4CBA90;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}


</style>

<div id="page-wrapper" style="width:140%;">
    <div class="">
        <div class="row">

            <?php  if($this->uri->segment(3) == 'view' || $this->uri->segment(3) == 'viewbooking'){?>
            <div class="pagination">
                <select id="dynamic_select">
                    
                    
                    <option value="<?php echo base_url().'employee/booking/view'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view/0/100'?>" <?php if($this->uri->segment(5) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/viewbooking'?>" <?php if($this->uri->segment(3) == 'viewbooking'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(5)){if($this->uri->segment(5) !=30 || $this->uri->segment(5) != 50 || $this->uri->segment(5) != 100  ){?>
                    <option value="" <?php if($this->uri->segment(5) == count($result)){ echo 'selected';}?>><?php echo $this->uri->segment(5);?></option>
                    <?php } }?>
                </select>
            </div>
            <?php } ?>



            <div style="width:100%;margin-left:10px;margine-right:5px;">
                <h1 align="left"><b>Sorted Bookings (By Date)</b></h1>
                <table >

                    <thead>
                    <tr>
                    <th>S No.</th>
                    <th>
                    <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a></th>
                    <th>User Name</th>
                    <th>Phone No.</th>
                    <th>Service Name</th>
                    <th>
                    <a href="<?php echo base_url();?>employee/booking/date_sorted_booking">Booking Date</a>
                    </th>
                    <th>
                    <a href="<?php echo base_url();?>employee/booking/status_sorted_booking">Status</a>
                    </th>
                    <th>View</th>
                    <th>Reschedule</th>
                    <th>Cancel</th>
                    <th>Complete</th>
                    <th>Job Card</th>
                    <th>Service Center</th>
                    <th>Contact Name</th>
                    <th>Contact No.</th>
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr>

                    <td><?=$row->id?>.</td>

                    <td>
                        <?php
                        if (is_null($row->booking_jobcard_filename)) 
                        {
                            echo "<a href=" . base_url() . "employee/booking/jobcard/$row->booking_id>$row->booking_id</a>";
                        } 
                        else 
                        {
                            echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                        }
                        ?>
                    </td>

                    <td><a href="<?php echo base_url();?>employee/user/user_details/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                    <td><?= $row->booking_primary_contact_no; ?></td>
                    <td><?= $row->services; ?></td>
                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>
                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                            . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id>View</a>";?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a id='edit' class='btn btn-sm btn-success' "
                                . "href=" . base_url() . "employee/booking/reschedulebooking/$row->booking_id>Reschedule</a>";
                        } 
                        elseif($row->current_status =='FollowUp')
                        {
                            echo "<a class='btn btn-small btn-success btn-sm' href=".base_url()."employee/booking/followup/$row->booking_id>Confirm</a>";
                        }

                        else {
                            echo "<a id='edit' class='btn btn-sm btn-success disabled' "
                        . "href=" . base_url() . "employee/booking/reschedulebooking/$row->booking_id>Reschedule</a>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a id='edit' class='btn btn-sm btn-warning' "
                            . "href=" . base_url() . "employee/booking/cancelbooking/$row->booking_id>Cancel</a>";
                        }
                        elseif($row->current_status =='FollowUp')
                        {
                            echo "<a class='btn btn-small btn-warning btn-sm' href=".base_url()."employee/booking/editquery/$row->booking_id>Edit</a>";
                        }
                        else 
                        {
                            echo "<a id='edit' class='btn btn-sm btn-warning disabled' "
                                    . "href=" . base_url() . "employee/booking/cancelbooking/$row->booking_id>Cancel</a>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a class='btn btn-sm btn-danger btn-sm' "
                                . "href=" . base_url() . "employee/booking/completebooking/$row->booking_id>Complete</a>";
                        }
                        elseif($row->current_status =='FollowUp')
                        {
                            echo "<a class='btn btn-small btn-danger btn-sm' href=".base_url()."employee/booking/cancelfollowup/$row->booking_id>Cancel</a>";
                        }
                        else
                        {
                            echo "<a class='btn btn-sm btn-danger btn-sm disabled' "
                                    . "href=" . base_url() . "employee/booking/completebooking/$row->booking_id>Complete</a>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($row->current_status == 'Pending' || $row->current_status == 'Rescheduled')
                        {
                            echo "<a class='btn btn-sm btn-info' "
                            . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id>Job Card</a>";
                        }
                        else
                        {
                            echo "<a class='btn btn-sm btn-info disabled' "
                            . "href=" . base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/$row->booking_id>Job Card</a>";
                        }
                        ?>
                    </td>
                    <td><?=$row->service_centre_name;?></td>
                    <td><?=$row->primary_contact_name;?></td>
                    <td><?=$row->primary_contact_phone_1;?></td>
                </tr>
                    <?php
                    }?>

                </table>
                <div class="pagination" style="float:left;"> <?php echo $links; ?></div>
            </div>
        </div>
    </div>
</div>

