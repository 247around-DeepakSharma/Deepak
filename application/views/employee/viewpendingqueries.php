
<?php  $offset = $this->uri->segment(4); ?>

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
            <?php  if($this->uri->segment(3) == 'view_queries' || $this->uri->segment(3) == 'finduser'){ $status = $this->uri->segment(4); ?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/view_queries'?>" <?php if($this->uri->segment(5) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status.'/0/100'?>" <?php if($this->uri->segment(6) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status.'/0/200'?>" <?php if($this->uri->segment(6) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status.'/0/All'?>" <?php if($this->uri->segment(6) == 'All'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(6)){if($this->uri->segment(6) != 50 || $this->uri->segment(6) != 100 || $this->uri->segment(6) != 200 ){?>
                    <option value="" <?php if($this->uri->segment(6) == count($Bookings)){ echo 'selected';}?>><?php echo $this->uri->segment(6);?></option>
                    <?php } }?>
                </select>
            </div>
            <?php } ?>
             <div class="input-filter-container"><label for="input-filter">Search:</label> <input type="search" id="input-filter" size="15" placeholder="search"></div>
            <div style="margin-left:10px;margine-right:5px;">
                <h1 align="left"><b><?php if($status == "FollowUp"){ echo "Pending Queries"; } else if($Bookings[0]->current_status == "FollowUp"){ echo "Pending Queries";} else { echo "Cancelled Queries"; } ?> </b></h1>
                <table >

                    <thead>
                    <tr>
                    <th>S No.</th>
                    <th width="160px;">
                    <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a></th>
                    <th width="140px;">User Name</th>
                    <th width="125px;">Phone No.</th>
                    <th width="125px;">Service Name</th>
                    <th width="165px;">Potential Value</th>
                    <th width="165px;">Booking Date/Time</th>
                    <?php if($status != "Cancelled"){?>
                    <th width="100px;">Status</th>
                     <?php } ?>
                    <th width="100px;">City</th>
                    <?php if($status != "Cancelled"){?>
                    <th width="100px;">Vendor Status</th>
                    <?php } ?>
                    <th width="250px;">Query Remarks</th>
                    <th width="60px;">Call</th>
                    <th width="60px;">View</th>
                     <?php if($status != "Cancelled"){?>
                    <th width="60px;">Update</th>

                    <th width="60px;">Cancel</th>
                    <?php } ?>
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr <?php if (isset($row->OrderID)) { if($row->OrderID !=null) { ?>
                                style="background-color:#EC8484"
                        <?php }  }?> >
                    <td><?=$row->id?>.</td>

                <td><?= $row->booking_id; ?></td>
                        <td><a target='_blank' href="<?php echo base_url(); ?>employee/user/finduser/0/0/<?php echo $row->phone_number; ?>"><?php echo $row->customername; ?></a></td>
                        <td><a target='_blank' href="<?php echo base_url();?>employee/user/finduser/0/0/<?php echo $row->phone_number;?>"><?php echo $row->booking_primary_contact_no; ?></a></td>
                    <td><?= $row->services; ?></td>
                    <td><?= $row->potential_value; ?></td>
                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <?php if($status !="Cancelled"){ ?>
                    <td id="status_<?php echo $row->booking_id; ?>">
                        <?php
                            echo $row->current_status;
                            if ($row->current_status != $row->internal_status)
                                echo " (" . $row->internal_status . ")";
                        ?>
                    </td>
                    <?php } ?>
                     <td><?= $row->city; ?></td>
                      <?php if($status !="Cancelled"){ ?>
                    <?php if($row->vendor_status =="Vendor Not Available"){ ?>

                          <td><p style="color: red;"><?php print_r($row->vendor_status); ?></p></td>

                    <?php } else { ?>

                    <td>

                    <select onchange="load_vendor_details(<?php echo $count; ?>)" id="vendor_avalilabe<?php echo $count;?>"  class="form-control" style="width:156px;">
                        <option selected disabled>Vendor Available</option>

                    <?php foreach ($row->vendor_status as  $value) { ?>
                     <option value="<?php echo $value['Vendor_ID']; ?>"><?php echo $value['Vendor_Name']; ?></option>

                    <?php  } ?>
                    </select>

                    </td>


                   <?php  } } ?>

                    <td><?= $row->query_remarks; ?></td>

                      <td><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                     </td> 

                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                        . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>
 <?php if($status !="Cancelled"){ ?>
                    <td><?php
                        echo "<a target='_blank' class='btn btn-small btn-success btn-sm' href=".base_url()."employee/booking/get_edit_booking_form/$row->booking_id title='Update'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        ?>
                    </td>

                    <td>
                        <?php
                        echo "<a target='_blank' class='btn btn-small btn-warning btn-sm' href=".base_url()."employee/booking/get_cancel_form/$row->booking_id/followup title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    <?php } ?>
                    </tr>
                    <?php $count++;
                    }?>

                </table>
                <?php if(!empty($links)){ ?><div class="pagination" style="float:left;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>js/jquery.filtertable.min.js"></script>

<script>

    $(document).ready(function() {
        $('table').filterTable({ // apply filterTable to all tables on this page
            inputSelector: '#input-filter' // use the existing input instead of creating a new one
        });
    });

    function load_vendor_details(div){
        var vendor_id = $("#vendor_avalilabe"+div).val();
        document.location.href= '<?php echo base_url(); ?>employee/vendor/viewvendor/'+vendor_id;
    }


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

