
<style type="text/css">
    table{
          width: 99%;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        vertical-align: center;
        padding: 1px;
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
      <div style="width:85%;margin:50px;">
        <h2 style="color:Blue;">Details:</h2>
        <div style="float:left;width:60%;height:950px;">
      	<p>
      		<b style="color:red;">Customer Details:-</b><br>
          <table>
      		<tr><td width="35%;">Customer name: </td><td><?php echo $query1[0]['name']; ?></td></tr>
      		<tr><td>Customer phone number: </td><td><?php echo $query1[0]['phone_number']; ?></td></tr>
          <tr><td>Alternate phone number: </td><td><?php echo $query1[0]['alternate_phone_number']; ?></td></tr>
      		<tr><td>Customer email address: </td><td><?php echo $query1[0]['user_email']; ?></td></tr>
          <tr><td>Home address: </td><td><?php echo $query1[0]['home_address'];?>,</td></tr>
          <tr><td>Home Pincode: </td><td><?php echo $query1[0]['pincode'];?>,</td></tr>
          </table>
          <br>
          <br><b style="color:red;">Booking Details:-</b><br><br>
          <table>
      		<tr><td width="35%;">Booking Id: </td><td><?php echo $query1[0]['booking_id']; ?></td></tr> 
          <tr><td width="35%;">Order Id: </td><td><?php if(!empty($query4)){ echo $query4[0]['order_id']; } ?></td></tr>
          <tr><td>Booking Type: </td><td><?php echo $query1[0]['type']; ?></td></tr>
      		<tr><td>Service name: </td><td><?php echo $query1[0]['services']; ?></td></tr>
          <tr><td>Source: </td><td><?php echo $query1[0]['source']; ?></td></tr>
          <tr><td>Number of appliances: </td><td><?php echo $query1[0]['quantity']; ?></td></tr>
          <tr><td>Booking date: </td><td><?php echo $query1[0]['booking_date']; ?></td></tr>
          <tr><td>Booking time slot: </td><td><?php echo $query1[0]['booking_timeslot']; ?></td></tr>
      		<tr><td>Booking address: </td><td><?php echo $query1[0]['booking_address'];?></td></tr>
          <tr><td>Booking Pincode: </td><td><?php echo $query1[0]['booking_pincode']; ?></td></tr>
          <tr><td>Booking Primary Contact No.: </td><td><?php echo $query1[0]['booking_primary_contact_no']; ?></td></tr>
          <tr><td>Booking Alternate Contact No.: </td><td><?php echo $query1[0]['booking_alternate_contact_no']; ?></td></tr>
          <tr><td>Booking Remarks: </td><td><?php echo $query1[0]['booking_remarks']; ?></td></tr>
          <tr><td>Query Remarks: </td><td><?php echo $query1[0]['query_remarks']; ?></td></tr>
          <tr><td>Booking current status: </td><td><?php echo $query1[0]['current_status']; ?></td></tr>
          <tr><td>Booking internal status: </td><td><?php echo $query1[0]['internal_status']; ?></td></tr>
          <tr><td>Booking closed date: </td><td><?php echo $query1[0]['closed_date']; ?></td></tr>
          <tr><td>Potential Value: </td><td><?php echo $query1[0]['potential_value']; ?></td></tr>
          <tr><td>Amount Due: </td><td><?php echo $query1[0]['amount_due']; ?></td></tr>
          <tr><td>Amount Paid: </td><td><?php echo $query1[0]['amount_paid']; ?></td></tr>
          <tr><td>Service charges: </td><td><?php echo $query1[0]['service_charge']; ?></td></tr>
          <tr><td>Additional service charges: </td><td><?php echo $query1[0]['additional_service_charge']; ?></td></tr>
          <tr><td>Parts cost: </td><td><?php echo $query1[0]['parts_cost']; ?></td></tr>
          <tr><td>Rating star: </td><td><?php echo $query1[0]['rating_stars']; ?></td></tr>
          <tr><td>Rating comments: </td><td><?php echo $query1[0]['rating_comments']; ?></td></tr>
          <tr><td>Vendor Rating star: </td><td><?php echo $query1[0]['vendor_rating_stars']; ?></td></tr>
          <tr><td>Vendor Rating comments: </td><td><?php echo $query1[0]['vendor_rating_comments']; ?></td></tr>
          <tr><td>Closing Remark: </td><td><?php echo $query1[0]['closing_remarks']; ?></td></tr>
          <tr><td>Cancellation Reason: </td><td><?php echo $query1[0]['cancellation_reason']; ?></td></tr>
          </table>
        </p>
        </div>

        <div style="float:left;width:40%;height:600px;">
        <p>
                <b style="color:red;">Appliance Details:-</b><br>
        	<?php for($i=0; $i<$query1[0]['quantity']; $i++) {?>
          <table>
        	<tr><td td width="35%;">Brand<?=$i+1;?> : </td><td><?php echo $query2[$i]['appliance_brand'];?></td></tr>
        	<tr><td>Model<?=$i+1;?> : </td><td><?php echo $query2[$i]['model_number'];?></td></tr>
        	<tr><td>Category<?=$i+1;?> : </td><td><?php echo $query2[$i]['appliance_category'];?></td></tr>
        	<tr><td>Capacity<?=$i+1;?> : </td><td><?php echo $query2[$i]['appliance_capacity'];?></td></tr>
            <tr><td>Description<?=$i+1;?> : </td><td><?php echo $query1[0]['description'];?></td></tr>
             
              <?php for($j = 0; $j < count($query2); $j++) {?>
                <tr><td>Selected services: </td><td><?php echo $query2[$j]['price_tags'];?></td></tr>
                <tr><td>Total price: </td><td><?php echo $query2[$j]['total_price'];?></td></tr>
              <?php } ?>
              
          </table>
        	<br>
        	<?php } ?>
        </p>
        </div>

        <div style="float:right;width:40%;">
          <p><br>
            <b style="color:red;">Service Centre Details:</b><br>
            <table>
            <tr><td>Service Centre Name: </td><td><?php if(isset($query3[0]['service_centre_name'])){echo $query3[0]['service_centre_name'];}?>
                </td>
            </tr>
            <tr><td>PoC Name: </td><td><?php if(isset($query3[0]['primary_contact_name'])){echo $query3[0]['primary_contact_name'];}?>
                </td>
            </tr>
            <tr><td>PoC Number: </td><td><?php if(isset($query3[0]['primary_contact_phone_1'])){echo $query3[0]['primary_contact_phone_1'];}?>
                </td>
            </tr>
            </table>
          </p>
        </div>

        <div style="float:right;width:40%;">
          <p><br>
            <b style="color:red;">Charges Collected By:</b><br>
            <table>
            <tr>
              <td>Service charges collected by: </td><td><?php echo $query1[0]['service_charge_collected_by']; ?>
              </td>
            </tr>
            <tr><td>Additional Service charges collected by: </td><td><?php echo $query1[0]['additional_service_charge_collected_by']; ?>
                </td>
            </tr>
            <tr><td>Parts cost collected by: </td><td><?php echo $query1[0]['parts_cost_collected_by']; ?>
                </td>
            </tr>
            </table>
          </p>
        </div>

      </div>
    </div>
  </div>
</div>