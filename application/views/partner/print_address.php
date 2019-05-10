 <script src="<?php echo base_url()?>js/jquery.js"></script>
<?php foreach ($details as  $value) { ?>

<div style="display:inline; height: 400px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:10px;width: 320px;padding: 8px;">
    <div style="border: 1px solid #ccc;padding:8px; width: 300px;">
        <?php if($value['main_company_logo']){ ?>
        <img style='vertical-align:middle;width:50px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['main_company_logo']; ?>">
        <?php } ?>
        <div style='vertical-align:middle; display:inline;font-weight:bold; font-size: 20px;margin-left:10px;'>
            <?php echo $value['main_company_public_name']." Service Center";  ?>
        </div>
    </div>
    <div style="margin-left: 8px;">
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;"><?php echo $value['vendor_name'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">C/o - <?php echo $value['owner_name'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:2px;">Address - <?php echo $value['address']." ".$value['sc_district']." ".$value['state']." ". $value['pincode'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">GST No - <?php echo $value['gst_no'];?></p>
        
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Phone - <?php echo $value['primary_contact_phone_1']." ". $value['primary_contact_phone_2'];?></p><br/>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Job No - <?php echo $value['booking_id'];?></p>
         <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Brand Name - <?php echo $value['brand_name'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Part Name - <?php echo $value['part_name'];?></p><br/>
        
    </div>
    <div style="float:right;margin-left: 5px;">
       <span>From: <?php echo $value['partner']['company_name'];?></span><br/>
       <span><?php echo $value['partner']['address'];?></span><br/>
       <span><?php echo $value['partner']['district']. " ".$value['partner']['state']." - ".$value['partner']['pincode'];?></span><br/>
       <span>Ph:<?php echo $value['partner']['primary_contact_phone_1'];?></span><br/>
        
    </div>
</div>
<?php } ?>


<script>
 $(window).load(function(){
    window.print();
    setTimeout(function(){
        window.close();
    }, 1);
 });
   


</script>
