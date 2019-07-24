<html>
<head>
<style>
table {
}
tr{
    border: 1px solid;
}
td{
    border-right: 1px solid;
    text-align: center;
    padding: 6px 0px;
}
p{
    font-family: sans-serif;
    font-size: 13px;
}
#top_row{
    border-right: none;
    text-align: left;
}
</style>
</head>
<body>
<table style="width:100%;border-collapse: collapse;">
   
    <?php
    if($qr){
        ?>
    <tr style="border-bottom: none;">
        <td colspan="6" id="top_row" style="text-align: right;"><p style='font-family: sans-serif;font-size: 12px;'><b>Get 5% Discount When You Scan <br>QR Code & Pay Through Paytm App</b></p> 
   </td>
        </tr>
        <tr style="border-top:none;">
      <td colspan="1" id="top_row">
          <?php if($meta['main_company_logo']){ ?>
          <img style='padding: 0px 0px 0px 12px;width:80px;' src='<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>'>
          <?php } ?>     
      </td>
      <td colspan="4" id="top_row" style="text-align: center;"><p style='font-family: sans-serif;font-size: 22px;line-height: 30px; text-transform: uppercase;'><b><?php echo $meta['main_company_public_name']; ?> SERVICE JOB CARD</b></p></td>
 <td colspan="1" id="top_row" style="text-align: right;"><p><img style='padding: 0px 10px;width: 80px;' src=<?php echo $qr?>></p>
   </td>
   </tr>
    <?php
    }
    else{
        ?>
 <tr>
      <td colspan="2" id="top_row" style="width: 12%;">
          <?php if($meta['main_company_logo']){ ?>
          <img style='padding: 5px; width:100px;' src='<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>'>
          <?php } ?> 
      </td>
      <td colspan="4" id="top_row" style="text-align: left;"><p style='font-family: sans-serif;font-size: 22px;line-height: 24px; text-transform: uppercase;'><b><?php echo $meta['main_company_public_name']; ?> SERVICE JOB CARD</b></p></td>
      </tr>
  <?php
    }
     if($booking_details['parant_booking_serial_number']){ ?>
     <tr>
      <td colspan="3"><p><b>Parent Booking Serial Number</b></p></td>
      <td colspan="3"><p><?php echo $booking_details['parant_booking_serial_number']?></p></td>
      </tr>
  <?php  } ?>
  <tr>
    <td><p><b>Booking ID</b></p></td>
    <td colspan="2"><p><?php echo $booking_details[0]['booking_id']?></p></td>
    <td><p><b>Serial No</b></p></td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td><p><b>Name</b></p></td>
    <td colspan="2"><p><?php echo $booking_details[0]['name']?></p></td>
    <td><p><b>Mobile no</b></p></td>
    <td colspan="2"><p><?php echo $booking_details[0]['booking_primary_contact_no']."/".$booking_details[0]['booking_alternate_contact_no']?></p></td>
  </tr>
   <tr>
    <td><p><b>Appliance</b></p></td>
    <td colspan="2"><p><?php echo $booking_details[0]['services']?></p></td>
    <td><p><b>Order ID</b></p></td>
    <td colspan="2"><p><?php echo $booking_details[0]['order_id']?></p></td>
  </tr>
  <tr>
    <td colspan="1"><p><b>Address</b></p></td>
    <td colspan="5"><p><?php echo $booking_details[0]['booking_address'].",<br>".
                        $booking_details[0]['city'].",".$booking_details[0]['state'].",".$booking_details[0]['booking_pincode']?></p></td>
   
  </tr>
  <tr>
    <td><p><b>Category</b></p></td>
    <td ><p><b>Capacity</b></p></td>
    <td><p><b>Brand / Model No.</b></p></td>
    <td colspan="2"><p><b>Service Selected</b></p></td>
    <td><p><b>Amount (Rs.)</b></p></td>
  </tr>
  <?php
foreach($booking_unit_details as $data){
    ?>
  <tr>
    <td><p><?php echo $data['appliance_category']?></p></td>
    <td ><p><?php echo $data['appliance_capacity']?></p></td>
    <td><p><?php echo $data['appliance_brand']."/".$booking_unit_details[0]['model_number']?></p></td>
    <td colspan="2"><p><?php echo $data['price_tags']?></p></td>
    <td><p><?php echo round($data['customer_net_payable'])?></p></td>
  </tr>
  <?php
  }
  ?>
  <tr>
      <td><p style="font-size:12px;"><b>Service Date</b></p></td>
    <td ><p><?php echo date("d/M/Y", strtotime($booking_details[0]['booking_date']));?></p></td>
    <td><p><b>Time slot</b></p></td>
    <td><p><?php echo $booking_details[0]['booking_timeslot']?></p></td>
    <td><p><b>Upcountry</b></p></td>
        <td><p><?php echo round($meta['upcountry_charges'])?></p></td>
  </tr>
  <tr>
    <td><p style="font-size:12px;"><b>Purchase Date</b></p></td>
    <td><p><?php if(!empty($data['purchase_date'])) { echo date("d/M/Y", strtotime($data['purchase_date'])); } else { echo '-';};?></p></td>
    <td colspan="3"><p><b>Total</b></p></td>
    <td><p><?php echo "Rs. ". round($booking_details[0]['amount_due'])?></p></td>
  </tr>
  <tr>
    <td><p><b>Appliance</b></p></td>
    <td colspan="5"><p><?php echo $meta['appliance_description'];?></p></td>
  </tr>
  <tr>
    <td><p><b>Remarks</b></p></td>
    <td colspan="5">
        <?php
            $autoRemarks = "";
            if (strripos($booking_details[0]['request_type'], 'Out of Warranty') !== false) {
                $autoRemarks =  "If estimate not approved from customer, Rs. 300 visit charge to be taken <br>";
            }
        ?>
        <p><?php echo $autoRemarks.$booking_details[0]['booking_remarks']?></p>
    </td>
  </tr>
  <tr>
    <td><p><b>Symptom</b></p></td>
    <td colspan="5">
        <p><?php echo $symptom;?></p>
    </td>
  </tr>
  <?php
  if($booking_details[0]['parent_booking']){
  ?>
    <tr>
    <td><p><b>Repeat Remarks</b></p></td>
    <td colspan="5">
        <p><?php echo $booking_details[0]['repeat_reason']?></p>
    </td>
  </tr>
  <?php
  }
  ?>
  <tr>
    <td><p><b>S.N</b></p></td>
    <td ><p><b>Part No.</b></p></td>
    <td><p><b>Description</b></p></td>
    <td><p><b>Qty Pcs</b></p></td>
    <td><p><b>Unit Price</b></p></td>
    <td><p><b>Total</b></p></td>
  </tr>
   <tr>
    <td><p><b>1</b></p></td>
    <td ><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
  </tr>
  <tr>
    <td><p><b>2</b></p></td>
    <td ><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
    <td><p></p></td>
  </tr>
   <tr>
    <td><p><b>Name</b></p></td>
    <td colspan="2"><p></p></td>
    <td><p><b>Total</b></p></td>
    <td colspan="2"><p></p></td>
  </tr>
  <tr>
    <td><p><b>Signature</b></p></td>
    <td colspan="2"><p></p></td>
    <td><p><b>Service Charges</b></p></td>
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
       <td colspan="6"><p style="font-size: 13px;"><b><?php echo $meta['main_company_description']; ?></b></p>
    <p style="font-size: 14px;"><b><?php echo $meta['main_company_name']; ?></b></p></td>
  </tr>
</table>
 
</body>
</html>