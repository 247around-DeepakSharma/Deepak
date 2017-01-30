<?php $offset = $this->uri->segment(6); ?>

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
                    console.log(response);
                   
                }
            });
        } else {
            return false;
        }

    }


</script>
<style type="text/css">
    table{

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

<div id="page-wrapper" >
    <div class="">
        <div class="row">
            <?php  if($this->uri->segment(3) == 'viewclosedbooking' ){ $status = $this->uri->segment(4);  ?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/viewclosedbooking/'.$status; ?>" <?php if($this->uri->segment(5) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/viewclosedbooking/'.$status.'/100/0'?>" <?php if($this->uri->segment(5) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/viewclosedbooking/'.$status.'/200/0'?>" <?php if($this->uri->segment(5) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/viewclosedbooking/'.$status.'/All/0'?>" <?php if($this->uri->segment(5) == 'All'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(5)){if($this->uri->segment(5) != 50 || $this->uri->segment(5) != 100 || $this->uri->segment(5) != 200 ||  $this->uri->segment(5) != "All" ){?>
                    <option value="" <?php if($this->uri->segment(5) == count($Bookings)){ echo 'selected';}?>><?php echo $this->uri->segment(5);?></option>
                    <?php } }?>
                </select>
            </div>
            <?php } ?>
            <div class="col-md-3 pull-right" style="margin-top:20px;">
                 <input type="search" class="form-control pull-right"  id="search" placeholder="search">
            </div>
            <div style="margin-left:10px;margine-right:5px;">
                 <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
                ?>  

                <h1 align="left"><?php  if(isset($status)){ echo $status." Bookings";} else { echo $Bookings[0]->current_status." Bookings"; $status =  $Bookings[0]->current_status; } ?></h1>
                <table >

                    <thead>
                    <tr>
                    <th >S No.</th>
                    <th width="150px;">
                    <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a></th>
                    <th width="125px;">User Name</th>
                    <th width="125px;">Phone No.</th>
                    <th width="125px;">Service Name</th>
                    <th width="170px;">Service Centre</th>
                    <th width="150px;">Service Centre City</th>
                    
                    <th width="125px;">Completion Date</th>
                    <th width="60px;">Call</th>
                    
                    <?php if($status != "Cancelled" ){?>
                    <th width="60px;">Edit</th>
                    <th width="60px;">Cancel</th>
                    <?php } else { ?>
                        <th width="60px;">Complete</th>
                    <?php  } ?>
                    <th width="60px;">Open</th>
                    <th width="60px;">View</th>
                    <?php if($status != "Cancelled" ){?>
                    <th width="60px;">Rate</th>
                    <?php } ?>
<<<<<<< Updated upstream
                    <th width="60px;">Penalty</th>
=======
                    <th width="60px;">Report</th>
>>>>>>> Stashed changes
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr>
                    <td><?=$row->id?>.</td>

                            <td><?php
				    echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';?>
    			    </td>

                    <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                    <td><?= $row->booking_primary_contact_no; ?></td>
                    <td><?= $row->services; ?></td>
                    <td><a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $row->assigned_vendor_id;?>"><?= $row->service_centre_name; ?></a></td>
                    <td><?=$row->city; ?></td>
                     <td><?php echo date("d-m-Y", strtotime($row->closed_date)); ?></td>

                    <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                     </td> 

                     <?php if($status != "Cancelled" ){?>

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
                    
                    <?php } else { ?>
                    <td><?php
                        echo "<a id='edit' class='btn btn-sm btn-success' "
                            . "href=" . base_url() . "employee/booking/get_complete_booking_form/$row->booking_id title='Edit'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    
                    <?php } ?>
                     <td><?php
                        echo "<a id='edit' class='btn btn-sm btn-warning' "
                            . "href=" . base_url() . "employee/booking/get_convert_booking_to_pending_form/$row->booking_id/$status title='Open' target='_blank'> <i class='fa fa-calendar' aria-hidden='true'></i></a>";
                        ?>
                    </td>

                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                        . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                     <?php if($status != "Cancelled" ) {?>
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
                    <?php } ?>
                    <td>
                        <?php
                        if (empty($row->penalty_amount) ){
                            echo "<a  id='edit' class='btn btn-sm' style='background:#A9A9A9' "
                            . "href=" . base_url() . "employee/vendor/get_escalate_booking_form/$row->booking_id/$status title='Report'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        }else{
                            echo "<a style='background:#A9A9A9' id='edit' class='btn btn-sm disabled' "
                            . "href=" . base_url() . "employee/vendor/get_escalate_booking_form/$row->booking_id/$status title='Report'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        }
                        ?>
                    </td>

                    </tr>
                    <?php
                    }?>

                </table>
                 <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
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
    $(document).ready(function() {
        $('table').filterTable({ // apply filterTable to all tables on this page
            inputSelector: '#input-filter' // use the existing input instead of creating a new one
        });
    });
</script>
<style>
    /* generic table styling */
    table { border-collapse: collapse; }
    td { padding: 5px; }

    td { border-bottom: 1px solid #ccc; }
    /* filter-table specific styling */
    td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
    /* special filter field styling for this example */
    .input-filter-container { position: absolute; top: 7em; right: 1em; border: 2px solid #66f; background-color: #eef; padding: 0.5em; }
</style>
<?php $this->session->unset_userdata('success'); ?>