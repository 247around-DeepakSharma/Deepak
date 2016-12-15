
<?php foreach ($details as  $value) { ?>

<div style="display:inline; height: 318px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:10px;width: 320px;padding: 8px;">
    <div style="border: 1px solid #ccc;padding:8px; width: 300px;">
        <img style='vertical-align:middle;width:50px;' src="<?php echo base_url(); ?>images/logo.png">
        <div style='vertical-align:middle; display:inline;font-weight:bold; font-size: 20px;margin-left:10px;'>
            247Around Service Center
        </div>
    </div>
    <div style="margin-left: 8px;">
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;"><?php echo $value['vendor_name'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">C/o - <?php echo $value['owner_name'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:2px;">Address - <?php echo $value['address']." ".$value['district']." ".$value['state']." ". $value['pincode'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Phone - <?php echo $value['primary_contact_phone_1']." ". $value['primary_contact_phone_2'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Job No- <?php echo $value['booking_id'];?></p>
    </div>
     <div style="float:right;margin-left: 5px;">
       <span>From: MEPL</span><br/>
        <span>11C Udyog Kendra</span><br/>
        <span>GR. Noida - 201306</span><br/>
        <span>Ph:9810585974</span><br/>
        
    </div>
</div>
<?php } ?>


<script>

    window.print();

    setTimeout(function(){
        window.close();
    }, 1);
</script>
