
<?php 
$this->db_location = $this->load->database('default1', TRUE,TRUE);
        $this->db = $this->load->database('default', TRUE,TRUE);
?>

<html>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
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
<script>
  $(document).ready(function()
    {
      $('#for_appliance').hide();
      $('#for_user').show();
      $('#for_user_page').show();
      $("#appliance_toogle_button").click(function()
        {
          $("#for_appliance").toggle();
          $("#for_user").toggle();
          $('#for_user_page').toggle();
          
      });
      
    });
</script>


<div id="page-wrapper" style="width:100%;"> 
   <div class="">
        <div class="row">
          <div id="for_user" style="width:90%;margin:50px;">

              <center><h2>Booking History: <?php echo $data[0]['name'];?></h2></center>
                
              <table>
                <thead>
                    <tr>
                    <th>No.</th>

                    <th>Booking Id</th>
                   
                    <th>Name</th>

                    <th>Appliance</th>

                    <th>Booking Date</th>

                    <th>Booking Timeslot</th>

                    <th>Status</th>
                    <th>View</th>
                    <th>Go To Booking </th>
                    <th>Un-Cancel</th>
                    
                  </tr>
          
              </thead>

              <?php if(isset($data[0]['booking_id'])){ $count = 1; ?>
                    <?php foreach($data as $key =>$row){?>
                    
                    <tr>

                    <td><?php echo $count; $count++;?>.</td>

                    <td><?=$row['booking_id'];?></td>

                    <td><?=$row['name'];?></td>

                    <td><?=$row['services'];?></td>

                    <td><?=$row['booking_date'];?></td>
                    
                    <td><?=$row['booking_timeslot'];?></td>

                    <td><?php echo $row['current_status'];  ?></td>

                    <td>
                        <?php echo "<a class='btn btn-sm btn-primary' "
                        . "href=" . base_url() . "employee/booking/viewdetails/$row[booking_id] target='_blank'title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>

                    <td>
                    <?php 
                      if(substr($row['booking_id'],0,1) == "S"){ ?>

                          <a href="<?php echo  base_url();?>employee/booking/view/0/0/<?php echo $row['booking_id'] ?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>

                     <?php } else if(substr($row['booking_id'],0,1) == "Q") {?>

                          <a href="<?php echo base_url(); ?>employee/booking/view_queries/<?php echo $row['current_status']; ?>/0/0/<?php echo $row['booking_id']?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>

                     <?php } ?>
                    </td>
                    <td>
                    <?php if ($row['current_status'] =='Cancelled' && strpos($row['booking_id'], "Q") !== FALSE) {?>

                          <a class="btn btn-small btn-danger btn-sm" href="<?php echo base_url(); ?>employee/booking/cancelled_booking_re_book/<?php echo $row['booking_id'];?>/<?php echo $data[0]['phone_number'];?>" title="uncancel"><i class="fa fa-folder-open-o" aria-hidden="true"></i></a>

                     <?php } ?></td>
                     
                    </tr>
                    <?php 
                     } }?>

              </table>
                       
            </div>
            <div style="float:left;">
              <center>
                <a class="btn btn-primary" href="<?php echo base_url(); ?>employee/booking/addbooking/<?php echo $data[0]['phone_number'];?>">New Booking</a>

              <a style="margin-left: 90px;"href="<?php echo base_url();?>employee/user/get_edit_user_form/<?php echo $data[0]['phone_number']?>" class='btn btn-primary'>User Details</a>

              <div style="float:left;margin-left:100px;"><input id="appliance_toogle_button" type="Button" value="Appliance Details" class='btn btn-primary'></div>

              <div style="float:left;margin-left:100px;"></div>
              </center>

              <?php if(!empty($links)) { ?><div id="for_user_page" class="pagination" style="float:left;"> <?php echo $links; ?></div> <?php } ?>

            </div>


            <div id="for_appliance" style="float:left;padding-top:20px;padding-left:20px;">
              <h2><b>Appliance Wallet:</b></h2>
              <div>
                <table>
                  <th>S. No.</th>
                  <th>Service</th>
                  <th>Brand</th>
                  <th>Category</th>
                  <th>Capacity</th>
                  <th>Model Number</th>
                  <th>Purchase Year</th>
                  <th>Book Now</th>

                  <?php $count = 1; ?>
                    <?php foreach($appliance_details as $key =>$row){?>
                  <tr>
                    <td><?php echo "$count"; $count++;?></td>
                    <td><?=$row['services']?></td>
                    <td><?=$row['brand']?></td>
                    <td><?=$row['category']?></td>
                    <td><?=$row['capacity']?></td>
                    <td><?=$row['model_number']?></td>
                    <td><?=$row['purchase_month']?>-<?=$row['purchase_year']?></td>
                    <td><?php 
                        echo "<a class='btn btn-small btn-primary btn-sm' href=".base_url()."employee/booking/get_appliance_booking_form/$row[id]>Book Now</a>";
                    ?></td>
                  </tr>
                  <?php } ?>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>              
</html>