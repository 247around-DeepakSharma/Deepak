<style>
table, th, td {
  border: 1px solid #e2dddd;
  border-collapse: collapse;
}
th, td {
  padding:3px;
}

button {
  background-color: #337ab7; 
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
#part_not_received_remarks {
    float: left;
    width: 100%;
    height: auto;
    border: 1px solid #e2dddd;
}
</style>

  <?php
  if (!empty($spare_parts_list) || !empty($is_pod_exist)) {
      $i = 1;
      if(!empty($spare_parts_list)){ ?>
    <form name="part_not_received" id="part_not_received" onsubmit="return spare_parts_not_received();" method="post">
    <div class="col-md-12">
        <label>Please select the parts number you have not received <span style="color:red;">*</label>
        <br>
    <table style="width:100%">
    <tr>
        <th>S.No.</th>
        <th>Action</th>
        <th>Part Number</th>
        <th>Part Name</th> 
        <th>Auto Acknowledged Date</th>
        
    </tr>
       <?php foreach ($spare_parts_list as $value) { ?>
  <tr>
    <td> <?php echo $i; ?></td>
    <td><input type="checkbox" name="acknowledge_spare_data[]" class="auto_acknowledge_data" value='<?php echo json_encode($value); ?>'></td>
    <td><?php echo $value['parts_requested']; ?></td>
    <td><?php echo $value['part_number']; ?></td>
    <td><?php echo $value['acknowledge_date']; ?></td>
  </tr>
    <?php $i++; } ?>
  </table>
    </div>
    
<div class="col-md-12" style="margin-top: 2%;">
    <label>Remarks <span style="color:red;">*</label>
    <br>
        <textarea name="part_not_received_remarks" rows="5" cols="50" id="part_not_received_remarks" rows="4" placeholder="Kandly enter the reason for marking part not received. "></textarea>
    </div>
    <div class="row">
        <div class="col-md-12" style="margin-top: 3%;">
            <div class="col-md-6" style="margin-left: 50%;">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id;?>">
                <button type="submit" class="btn btn-primary" id="submit_button_id">Submit</button>
            </div>
        </div>
    </div>
</form>
    <?php } else {  ?>
        <div class="row">
            
        </div>
        <div class="row">
            <table style="width:100%">
            <tr>
                <th>S.No.</th>
                <th colspan="2">Action</th>
                <th>Part Number</th>
                <th>Part Name</th> 
            </tr>
            
           <?php 
           $i = 1; 
           foreach ($pod_spare_parts_list  as $value ) {
               
            ?>
            <tr>
                <td> <?php echo $i; ?></td>
                <td colspan="2"><a id='courier_pod_file' href="<?php echo S3_WEBSITE_URL; ?>courier-pod/<?php echo $value['courier_pod_file']; ?>" target="_blank">Courier POD</a></td>
                <td><?php echo $value['parts_requested']; ?></td>
                <td><?php echo $value['part_number']; ?></td>
            </tr>
            <?php $i++; } ?>
            </table>
            <br>
            <p style="color: red;">You cannot mark this booking as “Part Not Received”, kindly contact the RM/ASM.</p> 
        </div>
<?php } }else{ ?>
    <div class="row">
        <p style="text-align: center; font-size: 14px; font-weight: bold;">No data found.</p> 
    </div>
<?php } ?>




