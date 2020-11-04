<head>
<title>Font Awesome Icons</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
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
<?php
    $i = 0;
    foreach ($details as $value) {
  
        if ($i % 2 == 0 && $i != 0) {
    ?>
    <div class="pagebreak"> </div>
<?php  }   ?>
<div style="display:inline; float:left; margin-left: 20px;margin-top:10px;width: 320px;">
    <div style="height: 440px; border: 1px solid #ccc; padding: 8px;">
    <table>
        <tr>
            <th style="border-right:none;" > <img style='vertical-align:middle;width:30px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/logo.jpg"; ?>"></th>
            <th  style="border-left:none;" colspan="3"><h3><u><?php if(!empty($value['spare_tag'][0]['consumed_part_status'])){ echo $value['spare_tag'][0]['consumed_part_status']; } ?> Spare Tag</h3></strong></th>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Partner</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['public_name'])){     echo $value['spare_tag'][0]['public_name'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Booking Id</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['booking_id'])){     echo $value['spare_tag'][0]['booking_id'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Model Number</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['model_number'])){     echo $value['spare_tag'][0]['model_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Serial Number</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['serial_number'])){     echo $value['spare_tag'][0]['serial_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Part Number</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['part_number'])){     echo $value['spare_tag'][0]['part_number'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Part Name</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['part_name'])){     echo substr($value['spare_tag'][0]['part_name'], 0, 30);} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Qty</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['shipped_quantity'])){     echo $value['spare_tag'][0]['shipped_quantity'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;"><strong>Symptom</strong></td>
            <td colspan="3" style="font-size:  12px;"><strong><?php if(!empty($value['spare_tag'][0]['symptom'])){ echo $value['spare_tag'][0]['symptom'];} ?></strong></td>
        </tr>
        <tr>
            <td style="font-size:  12px;border-right:none;"><strong>SF/Eng Sign</strong></td>
            <td style="font-size:  12px;border-left:none;">--------</td>
            <td style="font-size:  12px; border-right:none;"><strong>RM/ASM Auth</strong></td>
            <td style="font-size:  12px;border-left:none;">-----------</td>
        </tr>
    </table>
    </div>
    <br>
    <br>
    <span>-------<i class="fa fa-scissors">-------------------------------------------------</i></span>
    <br>
    <br>
   <div style="float:left;border: 1px solid #ccc; margin-top: 20px; padding:8px; width: 300px; height: 420px;">
    <div style="border: 1px solid #ccc;">
        <?php if($meta['main_company_logo']){ ?>
        <img style='vertical-align:middle;width:50px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>">
        <?php } ?>
        
        <div style='vertical-align:middle; display:inline;font-weight:bold; font-size: 20px;margin-left:10px;'>
            <?php echo $meta['main_company_public_name']." Service Center"; ?>
        </div>
    </div>
    <div style="margin-left: 8px;">
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;">To, </p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;"><?php if(isset($value['print_addrres'][$i]['company_name'])){ echo $value['print_addrres'][$i]['company_name']; } ?></p>
        <?php if(!empty($value['primary_contact_name']) ){ ?>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">C/o - <?php echo $value['print_addrres'][$i]['primary_contact_name'];?></p>
        <?php } ?>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:2px;"><?php if(isset($value['print_addrres'][$i]['address'])){ echo $value['print_addrres'][$i]['address']; }  ?> " "<?php if(isset($value['print_addrres'][$i]['district'])){ echo $value['print_addrres'][$i]['district']; }  ?> " "<?php if (isset($value['print_addrres'][$i]['state'])){ echo $value['print_addrres'][$i]['state']; } ?>" " <?php if(isset($value['print_addrres'][$i]['pincode'])){ echo  $value['print_addrres'][$i]['pincode']; }  ?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Phone - <?php if(isset($value['print_addrres'][$i]['primary_contact_phone_1'])){ echo $value['print_addrres'][$i]['primary_contact_phone_1']; }?>" "<?php if(isset($value['print_addrres'][$i]['primary_contact_phone_2'])){ echo $value['print_addrres'][$i]['primary_contact_phone_2']; } ?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Job No- <?php echo $value['print_addrres'][$i]['booking_id'];?></p>
    </div>
     <div style="float:right;margin-left: 5px;">
       <span>From:  <?php echo $value['print_addrres'][$i]['vendor']['company_name'];?></span><br/>
       <span>C/o - <?php echo $value['print_addrres'][$i]['vendor']['primary_contact_name'];?></span><br/>
       <span> <?php echo $value['print_addrres'][$i]['vendor']['address'];?></span><br/>
       <span><?php echo $value['print_addrres'][$i]['vendor']['district']. " ". $value['print_addrres'][$i]['vendor']['state']. " - ".$value['print_addrres'][$i]['vendor']['pincode']; ?> </span><br/>
       <span>Ph: <?php echo $value['print_addrres'][$i]['vendor']['primary_contact_phone_1']; ?></span><br/>
        
    </div>
</div>
</div>
<style>
@media print {
.pagebreak {
    clear: both;
    page-break-after: always;
}
}
</style>
<?php $i++; } ?>
<script>
 $(window).load(function(){
    window.print();
    setTimeout(function(){
        window.close();
    }, 1);
 });
</script>
