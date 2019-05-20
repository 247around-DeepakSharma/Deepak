 <script src="<?php echo base_url()?>js/jquery.js"></script>
<?php foreach ($details as  $value) { ?>
<div style="display:inline; height: 360px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:10px;width: 320px;padding: 8px;">
    <div style="border: 1px solid #ccc;padding:8px; width: 300px;">
        <?php if($meta['main_company_logo']){ ?>
        <img style='vertical-align:middle;width:50px;' src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>">
        <?php } ?>
        <div style='vertical-align:middle; display:inline;font-weight:bold; font-size: 20px;margin-left:10px;'>
          <?php echo $meta['main_company_public_name']." Service Center"; ?>
        </div>
    </div>
    <div style="margin-left: 8px;">
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;">To, </p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;"><?php echo $value['company_name'];?></p>
        <?php if(!empty($value['primary_contact_name']) ){ ?>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">C/o - <?php echo $value['primary_contact_name'];?></p>
        <?php } ?>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:2px;">Address - <?php echo $value['address']." ".$value['district']." ".$value['state']." ". $value['pincode'];?></p>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Phone - <?php echo $value['primary_contact_phone_1']." ". $value['primary_contact_phone_2'];?></p>
        <?php if(isset($value['booking_id'])){ ?>
        <p style="max-width: 280px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Job No- <?php echo $value['booking_id'];?></p>
        <?php } ?>
    </div>
     <div style="float:right;margin-left: 5px;">
       <span>From: <?php echo $value['vendor']['company_name'];?></span><br/>
       <span>C/o - <?php echo $value['vendor']['primary_contact_name'];?></span><br/>
       <span><?php echo $value['vendor']['address'];?></span><br/>
       <span><?php echo $value['vendor']['district']. " ". $value['vendor']['state']. " - ".$value['vendor']['pincode']; ?> </span><br/>
       <span>Ph:<?php echo $value['vendor']['primary_contact_phone_1'];?></span><br/>        
    </div>  
    <?php if(isset($value['total_quantity'])){ ?>
    <div style="float:left;margin-left: 5px;">
        <br/><span><strong>Total Spares</strong> : <?php echo $value['total_quantity']; ?></span>
    </div> 
    <?php } ?>
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
