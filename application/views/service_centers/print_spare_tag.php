<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

</style>
<script src="<?php echo base_url()?>js/jquery.js"></script>
<?php foreach ($details as  $value) { ?>
    <table style="width:100%">
        <tr>
            <th style="border-right:none; height:90px;" > <img style='vertical-align:middle;width:50px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/logo.jpg"; ?>"></th>
            <th  style="border-left:none;" colspan="3" width="70%"><h2><u><?php if(!empty($value['consumed_part_status'])){ echo $value['consumed_part_status']; } ?> Spare Tag</h2></strong></th>
        </tr>
        <tr>
            <td>Partner</td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['public_name'])){     echo $value['public_name'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Booking Id</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['booking_id'])){     echo $value['booking_id'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Model Number</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['model_number'])){     echo $value['model_number'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Serial Number</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['serial_number'])){     echo $value['serial_number'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Part Number</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['part_number'])){     echo $value['part_number'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Part Name</strong></td>
            <td colspan="3" width="70%" style="height:40px;"><strong><?php if(!empty($value['part_name'])){     echo $value['part_name'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Qty</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['shipped_quantity'])){     echo $value['shipped_quantity'];} ?></strong></td>
        </tr>
        <tr>
            <td><strong>Symptom</strong></td>
            <td colspan="3" style="height:40px;"><strong><?php if(!empty($value['symptom'])){     echo $value['symptom'];} ?></strong></td>
        </tr>
        <tr>
            <td style="border-right:none; height:55px;"><strong>SF/Eng Sign</strong></td>
            <td style="border-left:none; border-right:none; height:55px;">-----------------------------</td>
            <td style="border-right:none; border-left:none; height:55px;"><strong>RM/ASM Auth</strong></td>
            <td style="border-left:none; height:55px;">-----------------------------</td>
        </tr>
    </table>
<br><br>
<?php } ?>
<script>
 $(window).load(function(){
    window.print();
    setTimeout(function(){
        window.close();
    }, 1);
 });
</script>
