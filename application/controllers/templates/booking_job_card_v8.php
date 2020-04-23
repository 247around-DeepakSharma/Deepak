<?php
include("booking_job_card_variables_v8.php");
?>
<html>
  <head>

  <style>

  table {
    font-family: sans-serif;
    width: 960px;
    margin: auto;
    border-collapse: collapse;
  }

  tr{

  border: 1px solid;


  }

  td{

  border-right: 1px solid;

  padding: 1%;

  }

  p{

  font-family: sans-serif;

  font-size: 13px;

  }

  #top_row{

  border-right: none;


  }

  </style>

  </head>



  <table>

  <tr>

    <td colspan="2" id="top_row" style="width: 12%;text-align: left;"><img style="padding: 5px;" src="https://aroundhomzapp.com/images/logo.jpg"></td>

    <td colspan="4" style="text-align: left;"><p style="font-family: sans-serif;font-size: 22px;line-height: 24px;"><b>247AROUND SERVICE JOB CARD</b></p></td>

    </tr>



  



  <tr>

  <td><p><b>Booking ID</b></p></td>

  <td colspan="2"><p><?php echo "$booking_id"?></p></td>

  <td><p><b>Serial No</b></p></td>

  <td colspan="2"></td>

  </tr>

  <tr>

  <td><p><b>Name</b></p></td>

  <td colspan="2"><p><?php echo "$name"?></p></td>

  <td><p><b>Mobile no</b></p></td>

  <td colspan="2"><p><?php echo "$booking_primary_contact_no"?> / <?php echo "$booking_alternate_contact_no"?></p></td>

  </tr>

  <tr>

  <td><p><b>Appliance</b></p></td>

  <td colspan="2"><p><?php echo "$services"?></p></td>

  <td><p><b>Order ID</b></p></td>

  <td colspan="2"><p><?php echo "$order_id"?></p></td>

  </tr>

  <tr>

  <td colspan="1"><p><b>Address</b></p></td>

  <td colspan="5"><p><?php echo "$booking_address"?>,

                      <?php echo "$city"?>,<br><?php echo "$state"?>,<?php echo "$booking_pincode"?></p></td>
  </tr>


  <tr>

  <td><p><b>Category</b></p></td>

  <td ><p><b>Capacity</b></p></td>

  <td><p><b>Brand / Model No.</b></p></td>

  <td colspan="2"><p><b>Service Selected</b></p></td>

  <td><p><b>Amount (Rs.)</b></p></td>

  </tr>
  <tr>
              <td><?php echo "$appliance_category"?>
              <td><?php echo "$appliance_capacity"?>
              <td><?php echo "$appliance_brand"?> / <?php echo "$model_number"?>
              <td colspan="2"><?php echo "$price_tags"?>
              <td><?php echo "$customer_net_payable"?>
          </tr>
  

  



  <tr>

    <td><p style="font-size:12px;"><b>Date of Service</b></p></td>

  <td ><p><?php echo "$booking_date"?></p></td>

  <td><p><b>Time slot</b></p></td>

  <td><p><?php echo "$booking_timeslot"?></td>

  <td><p><b>Upcountry Charges</b></p></td>

      <td><p><?php echo "$upcountry_charges"?>  </p></td>

  </tr>

  <tr>

  <td colspan="3"></td>

  <td colspan="2"><p><b>Total</b></p></td>

  <td><p><?php echo $customer_net_payable+$upcountry_charges;?></p></td>

  </tr>

  <tr>

  <td><p><b>Appliance Description</b></p></td>

  <td colspan="5"><p><?php echo "$appliance_description"?></p></td>

  </tr>

  <tr>

  <td><p><b>Booking Remarks</b></p></td>

  <td colspan="5"><p><?php echo "$booking_remarks"?></p></td>

  </tr>
  <tr>

  <td><p><b>Engineer Comments</b></p></td>

  <td colspan="5"><p></p></td>

  </tr>

  <tr>

  <td><p><b>S.No.</b></p></td>

  <td ><p><b>Part No.</b></p></td>

  <td><p><b>Description</b></p></td>

  <td><p><b>Qty Pcs</b></p></td>

  <td><p><b>Unit Price</b></p></td>

  <td><p><b>Total</b></p></td>

  </tr>
  <?php
      foreach ($record as $info) {
        echo "<tr><td align="."\"center\""."><b>".$i++.
              "<td align="."\"center\"".">$info[part_no]</td>
              <td align="."\"center\">$info[description]
              <td align="."\"center\">$info[qty]
              <td align="."\"center\">$info[unit_price]
              <td align="."\"center\">".$info["unit_price"]*$info["qty"]."
          </tr>";
          $total+=$info["unit_price"]*$info["qty"];
      }
    ?>


  <tr>

  <td><p><b>Customer Name</b></p></td>

  <td colspan="2"><p></p></td>

  <td><p><b>Total</b></p></td>

  <td colspan="2"><p><?php echo "$total";?></p></td>

  </tr>

  <tr>

  <td height=50px><p><b>Customer Signature</b></p></td>

  <td colspan="2"><p></p></td>

  <td><p><b>Service Charge</b></p></td>

  <td colspan="2"><p></p></td>

  </tr>

  <tr>

  <td><p><b>Customer <br> Comments</b></p></td>

  <td colspan="2"><p></p></td>

  <td><p><b>Service Tax</b></p></td>

  <td colspan="2"><p></p></td>

  </tr>

  <tr>

  <td><p><b>Rating(1-5)</b></p></td>

  <td colspan="2"><p></p></td>

  <td><p><b>Net Total</b></p></td>

  <td colspan="2"><p></p></td>

  </tr>

  <tr>

     <td colspan="6"><p style="font-size: 13px; text-align: center;"><b>Book Appliance Service from Qualified Engineers on "247AROUND" App / On Phone - 9555000247 / On

  Website - www.247around.com</b></p>

  <p style="font-size: 14px; text-align: center;"><b>Blackmelon Advance Technology Co. Pvt. Ltd.</b></p></td>

  </tr>

  </table>
