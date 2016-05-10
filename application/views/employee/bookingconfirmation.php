<?php
$this->db_location = $this->load->database('default1', TRUE,TRUE);
        $this->db = $this->load->database('default', TRUE,TRUE);
?>

<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="width:1100px;margin:50px;">
        <h3 style="color:blue;">Please verify all the booking details-</h3>

        <form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/booking" >
        <table class="table table-striped table-bordered">

            <tr>
                <td>Booking Id</td>
                <td><?= $booking['booking_id']; ?></td>
            </tr>

            <tr>
                <td>Primary Contact Number</td>
                <td><?= $booking['booking_primary_contact_no']; ?></td>
            </tr>

            <tr>
                <td>Alternate Contact Number</td>
                <td><?= $booking['booking_alternate_contact_no']; ?></td>
            </tr>

            <tr>
                <td>Booking Source</td>
                <td><?= $booking_source['source'];?></td>
            </tr>
            
            <tr>
                <td>Service Name</td>
                <td><?=$result[0]['services'];?></td>
            </tr>

            <tr>
            	<td>Booking For Date</td>
                <td><?=$booking['booking_date'];?></td>
            </tr>

            <tr>
            	<td>Booking Timeslot</td>
                <td><?=$booking['booking_timeslot'];?></td>
            </tr>

            <tr>
            	<td>Booking Address</td>
                <td><?=$booking['booking_address'];?></td>
            </tr>

            <tr>
            	<td>Booking Pincode</td>
                <td><?=$booking['booking_pincode'];?></td>
            </tr>

            <tr>    
                <td>Booking Remark</td>
                <td><?=$booking['booking_remarks'];?></td>
            </tr>
            
            <tr>
                <td>Query Remark</td>
                <td><?=$booking['query_remarks'];?></td>
            </tr>
            
            <tr>
                <td><strong>No of appliances</strong></td>
                <td><strong><?=$booking['quantity'];?></strong></td>
            </tr>
            <tr>
                <td>Amount Due</td>
                <td><?=$booking['amount_due'];?></td>
            </tr>


            <div>
            <input type="hidden" name="service_id" value="<?php echo $booking['service_id'];?>">
            <input type="hidden" name="booking_date" value="<?php echo $booking['booking_date'];?>">
            <input type="hidden" name="user_id" value="<?php echo $booking['user_id'];?>">
            <input type="hidden" name="booking_timeslot" value="<?php echo $booking['booking_timeslot'];?>">
            <input type="hidden" name="appliance_brand1" value="<?php echo $booking['appliance_brand1'];?>">
            <input type="hidden" name="appliance_category1" value="<?php echo $booking['appliance_category1'];?>">
            <input type="hidden" name="appliance_capacity1" value="<?php echo $booking['appliance_capacity1'];?>">
            <input type="hidden" name="items_selected1" value="<?php echo $booking['items_selected1'];?>">
            <input type="hidden" name="total_price1" value="<?php echo $booking['total_price1'];?>">
            <input type="hidden" name="model_number1" value="<?php echo $booking['model_number1'];?>">
            <input type="hidden" name="appliance_tags1" value="<?php echo $booking['appliance_tags1'];?>">
            <input type="hidden" name="purchase_year1" value="<?php echo $booking['purchase_year1'];?>">
            <input type="hidden" name="potential_value" value="<?php echo $booking['potential_value'];?>">
            <input type="hidden" name="appliance_brand2" value="<?php echo $booking['appliance_brand2'];?>">
            <input type="hidden" name="appliance_category2" value="<?php echo $booking['appliance_category2'];?>">
            <input type="hidden" name="appliance_capacity2" value="<?php echo $booking['appliance_capacity2'];?>">
            <input type="hidden" name="items_selected2" value="<?php echo $booking['items_selected2'];?>">
            <input type="hidden" name="total_price2" value="<?php echo $booking['total_price2'];?>">
            <input type="hidden" name="model_number2" value="<?php echo $booking['model_number2'];?>">
            <input type="hidden" name="appliance_tags2" value="<?php echo $booking['appliance_tags2'];?>">
            <input type="hidden" name="purchase_year2" value="<?php echo $booking['purchase_year2'];?>">
            <input type="hidden" name="appliance_brand3" value="<?php echo $booking['appliance_brand3'];?>">
            <input type="hidden" name="appliance_category3" value="<?php echo $booking['appliance_category3'];?>">
            <input type="hidden" name="appliance_capacity3" value="<?php echo $booking['appliance_capacity3'];?>">
            <input type="hidden" name="items_selected3" value="<?php echo $booking['items_selected3'];?>">
            <input type="hidden" name="total_price3" value="<?php echo $booking['total_price3'];?>">
            <input type="hidden" name="model_number3" value="<?php echo $booking['model_number3'];?>">
            <input type="hidden" name="appliance_tags3" value="<?php echo $booking['appliance_tags3'];?>">
            <input type="hidden" name="purchase_year3" value="<?php echo $booking['purchase_year3'];?>">
            <input type="hidden" name="appliance_brand4" value="<?php echo $booking['appliance_brand4'];?>">
            <input type="hidden" name="appliance_category4" value="<?php echo $booking['appliance_category4'];?>">
            <input type="hidden" name="appliance_capacity4" value="<?php echo $booking['appliance_capacity4'];?>">
            <input type="hidden" name="items_selected4" value="<?php echo $booking['items_selected4'];?>">
            <input type="hidden" name="total_price4" value="<?php echo $booking['total_price4'];?>">
            <input type="hidden" name="model_number4" value="<?php echo $booking['model_number4'];?>">
            <input type="hidden" name="appliance_tags4" value="<?php echo $booking['appliance_tags4'];?>">
            <input type="hidden" name="purchase_year4" value="<?php echo $booking['purchase_year4'];?>">
            <input type="hidden" name="booking_remarks" value="<?php echo $booking['booking_remarks'];?>">
            <input type="hidden" name="query_remarks" value="<?php echo $booking['query_remarks'];?>">
            <input type="hidden" name="type" id="type" value="<?php echo $booking['type'];?>">
            <input type="hidden" name="source" value="<?php echo $booking['source'];?>">
            <input type="hidden" name="booking_address" value="<?php echo $booking['booking_address'];?>">
            <input type="hidden" name="booking_pincode" value="<?php echo $booking['booking_pincode'];?>">
            <input type="hidden" name="booking_primary_contact_no" value="<?php echo $booking['booking_primary_contact_no']; ?>">
            <input type="hidden" name="booking_alternate_contact_no" value="<?php echo $booking['booking_alternate_contact_no']; ?>">
            <input type="hidden" name="amount_due" value="<?php echo $booking['amount_due']; ?>">
            <input type="hidden" name="create_date" value="<?php echo $booking['create_date'];?>">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id'];?>">
            <input type="hidden" name="quantity" value="<?php echo $booking['quantity'];?>">
            <input type="hidden" name="current_status" value="<?php echo $booking['current_status'];?>">
            <input type="hidden" name="newbrand1" value="<?php echo $booking['newbrand1'];?>">
            <input type="hidden" name="newbrand2" value="<?php echo $booking['newbrand2'];?>">
            <input type="hidden" name="newbrand3" value="<?php echo $booking['newbrand3'];?>">
            <input type="hidden" name="newbrand4" value="<?php echo $booking['newbrand4'];?>">
            </div>

        </table>
        <div style="float:left;width:100%;">

        <?php for($i=1; $i<=$booking['quantity']; $i++) {?>
        <table class="table table-striped table-bordered" name="applience<?php echo $i;?>" style="height:300px;width:275px;float:left;">

            <tr>
                <td>Appliance Brand</td>
                <td><?=$booking['appliance_brand'.$i];?></td>
            </tr>
            <tr>
                <td>Appliance category</td>
                <td><?=$booking['appliance_category'.$i];?></td>
            </tr>
            <tr>
                <td>Appliance Capacity</td>
                <td><?=$booking['appliance_capacity'.$i];?></td>
            </tr>
            <tr>
                <td>Services Selected</td>
                <td><?=$booking['items_selected'.$i];?></td>
            </tr>
            <tr>
                <td>Total Price</td>
                <td><?=$booking['total_price'.$i];?></td>
            </tr>
        </table>
        <?php } ?>

        </div>


            <div><center><input type="Submit" value="<?php if ($booking['booking_id'] == '') {
                echo 'Save Query';
            } else {
                echo 'Save Booking';
            } ?>" class="btn btn-primary"></center></div>

        </form>
      </div>
    </div>
  </div>
</div>

