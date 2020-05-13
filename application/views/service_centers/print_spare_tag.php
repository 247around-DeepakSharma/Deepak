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
<div style="display:inline; height: 470px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:10px;width: 320px;padding: 8px;">
    <table>
        <tr>
            <th style="border-right:none;" > <img style='vertical-align:middle;width:30px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/logo.jpg"; ?>"></th>
            <th  style="border-left:none;" colspan="3"><h3><u><?php if(!empty($value['consumed_part_status'])){ echo $value['consumed_part_status']; } ?> Spare Tag</h3></strong></th>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Partner</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['public_name'])){     echo $value['public_name'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Booking Id</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['booking_id'])){     echo $value['booking_id'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Model Number</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['model_number'])){     echo $value['model_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Serial Number</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['serial_number'])){     echo $value['serial_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Part Number</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['part_number'])){     echo $value['part_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Part Name</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['part_name'])){     echo substr($value['part_name'], 0, 30);} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Qty</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['shipped_quantity'])){     echo $value['shipped_quantity'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;"><strong>Symptom</strong></td>
            <td colspan="3" style="font-size: 13px;"><strong><?php if(!empty($value['symptom'])){ echo $value['symptom'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size: 13px;border-right:none;"><strong>SF/Eng Sign</strong></td>
            <td style="font-size: 13px;border-left:none;">--------</td>
            <td style="font-size: 13px; border-right:none;"><strong>RM/ASM Auth</strong></td>
            <td style="font-size: 13px;border-left:none;">-----------</td>
        </tr>
    </table>
<br>
</div>
<?php } ?>
<br><br><br><br>
<script>
 $(window).load(function(){
    window.print();
    setTimeout(function(){
        window.close();
    }, 1);
 });
</script>
