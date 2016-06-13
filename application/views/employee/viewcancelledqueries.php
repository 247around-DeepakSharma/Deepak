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
        <?php  if($this->uri->segment(3) == 'view_cancelled_queries' || $this->uri->segment(3) == 'view_all_cancelled_queries'){?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/view_cancelled_queries'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view_cancelled_queries/0/100'?>" <?php if($this->uri->segment(5) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/view_cancelled_queries/0/200'?>" <?php if($this->uri->segment(5) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/view_all_cancelled_queries'?>" <?php if($this->uri->segment(3) == 'view_all_cancelled_queries'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(5)){if($this->uri->segment(5) != 50 || $this->uri->segment(5) != 100 || $this->uri->segment(5) != 200 ){?>
                    <option value="" <?php if($this->uri->segment(5) == count($Bookings)){ echo 'selected';}?>><?php echo $this->uri->segment(5);?></option>
                    <?php } }?>
                </select>
            </div>
            <?php } ?>
            <div style="margin-left:10px;margine-right:5px;">
                <h1 align="left" ><b>Cancelled Queries</b></h1>
                <table >

                    <thead>
                    <tr>
                    <th >S No.</th>
                    <th width="150px;">
                    <a href="<?php echo base_url();?>employee/booking/view">Booking Id</a></th>
                    <th width="150px;">User Name</th>
                    <th width="125px;">Phone No.</th>
                    <th width="150px;">Service Name</th>
                    <th width="180px;">Booking Date/Time</th>
                    <th width="100px;">Status</th>
                    <th width="250px;">Query Remarks</th>
                    <th width="80px;">Call</th>
                    <th width="80px;">View</th>
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr>
                    <td><?=$row->id?>.</td>

                            <td>
                            <?php
                            if (is_null($row->booking_jobcard_filename)) {
                                echo "<a href=" . base_url() . "employee/booking/jobcard/$row->booking_id>$row->booking_id</a>";
                            } else {
                                echo '<a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/' . $row->booking_jobcard_filename . '">' . $row->booking_id . '</a>';
                            }
                            ?>
                        </td>

                    <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?=$row->phone_number;?>"><?=$row->customername;?></a></td>
                    <td><?= $row->booking_primary_contact_no; ?></td>
                    <td><?= $row->services; ?></td>
                    <td><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <td id="status_<?php echo $row->booking_id; ?>"><?php echo $row->current_status; ?></td>            
                    <td><?= $row->query_remarks; ?></td>
                        
                    <td>
                        <a class="btn btn-sm btn-info"
				   href="<?php echo base_url(); ?>employee/booking/call_customer/<?= $row->booking_primary_contact_no; ?>/pending_queries"
    				   title = "call" onclick = "return confirm('Call Customer ?');">
    				    <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
    				    </a>
                    </td>
                    
                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                        . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank'  title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    </tr>
                    <?php
                    }?>

                </table>
                <?php if(!empty($links)){ ?><div class="pagination" style="float:left;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
            </div>
        </div>
    </div>
</div>

