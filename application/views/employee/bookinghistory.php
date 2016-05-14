
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
          <?php 
          if (isset($data)) { 
            ?>
                <center><h2>Booking History: <?php echo $data[0]['name'];?></h2></center>
                
              <table>
                <thead>
                    <tr>
                    <th>No.</th>

                    <th>Booking ID</th>
                   
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
             
              <?php $count = 1; if(isset($data[0]['booking_id'])){ ?>
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
                        . "href=" . base_url() . "employee/booking/viewdetails/$row[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>


                   
                    <td>
                    <?php 
                      if(substr($row['booking_id'],0,1) == "S"){ ?>

                          <a href="<?php echo  base_url();?>employee/booking/view/0/0/<?php echo $row['booking_id'] ?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>

                     <?php } else if(substr($row['booking_id'],0,1) == "Q") {?>

                          <a href="<?php echo base_url(); ?>employee/booking/view_pending_queries/0/0/<?php echo $row['booking_id']?>" class="btn btn-small btn-success btn-sm" title="More Action"><i class="fa fa-bars" aria-hidden="true"></i></a>
                            
                     <?php } ?>


                    </td>
                    <td>
                   <?php if ($row['current_status'] =='Cancelled' && strpos($row['booking_id'], "Q") !== FALSE) {?>

                          <a class="btn btn-small btn-danger btn-sm" href="<?php echo base_url(); ?>employee/booking/cancelled_booking_re_book/<?php echo $row['booking_id'];?>/<?php echo $data[0]['phone_number'];?>" title="More Action"><i class="fa fa-folder-open-o" aria-hidden="true"></i></a>

                     <?php } ?></td>
                    </tr>  
                     
                  
                    <?php 
                     } }?>

              </table>
              <?php 
            }
            ?>
             

                       
            </div>
            <div style="float:left;">
              <center>
              <form action="<?php echo base_url()?>employee/booking/addbooking" method="POST" style="padding-left:200px;float:left;">
              <div style="">
                    <input type="hidden" name="user_id" value="<?php if(isset($data[0]['user_id'])){echo $data[0]['user_id'];}?>">
                    <input type="hidden" name="home_address" value="<?php if(isset($data[0]['home_address'])){echo $data[0]['home_address'];}else{echo $data[0]['home_address'];}?>">
                      <input type="hidden" name="city" value="<?php if(isset($data[0]['city'])){echo $data[0]['city'];}?>">
                      <input type="hidden" name="state" value="<?php if(isset($data[0]['state'])){echo $data[0]['state'];}?>">
                    <input type="hidden" name="user_email" value="<?php if(isset($data[0]['user_email'])){echo $data[0]['user_email'];}?>">
                    <input type="hidden" name="phone_number" value="<?php if(isset($data[0]['phone_number'])){echo $data[0]['phone_number'];}?>">
                    <input type="hidden" name="alternate_phone_number" value="<?php if(isset($data[0]['alternate_phone_number'])){echo $data[0]['alternate_phone_number'];}?>">
                    <input type="hidden" name="pincode" value="<?php if(isset($data[0]['pincode'])){echo $data[0]['pincode'];}?>">
                    <input type="hidden" name="name" value="<?php if(isset($data[0]['name'])){echo $data[0]['name'];}?>">
                </div>
              <?php echo "<input type='submit' value='New Booking' class='btn btn-primary'></a>"?>
              </form>
              <form action="<?php echo base_url()?>employee/user/get_edit_user_form" method="POST" style="padding-left:100px;float:left;">
              <div>
                <input type="hidden" name="user_id" value="<?php if(isset($data[0]['user_id'])){echo $data[0]['user_id'];}?>">

                    <input type="hidden" name="home_address" value="<?php if(isset($data[0]['home_address'])){echo $data[0]['home_address'];}?>">
                    <input type="hidden" name="user_email" value="<?php if(isset($data[0]['user_email'])){echo $data[0]['user_email'];}?>">
                    <input type="hidden" name="phone_number" value="<?php if(isset($data[0]['phone_number'])){echo $data[0]['phone_number']; }?>">
                    <input type="hidden" name="alternate_phone_number" value="<?php if(isset($data[0]['alternate_phone_number'])){echo $data[0]['alternate_phone_number'];}?>">
                    <input type="hidden" name="pincode" value="<?php if(isset($data[0]['pincode'])){echo $data[0]['pincode'];}?>">
                    <input type="hidden" name="name" value="<?php if(isset($data[0]['name'])){echo $data[0]['name'];}?>">
              </div>
              <?php echo " <input type='Submit' value='User Details' class='btn btn-primary'></a>"?>
              </form>
              <div style="float:left;padding-left:100px;"><input id="appliance_toogle_button" type="Button" value="Appliance Details" class='btn btn-primary'></div>
              <div style="float:left;padding-left:100px;"></div>
              </center>

              <div id="for_user_page" class="pagination" style="float:left;"> <?php if(isset($links)){ echo $links; }?></div>

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