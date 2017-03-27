<div style="display:inline; height: 500px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:10px;width: 920px;padding: 8px;">
    <div style="border: 1px solid #ccc;padding:8px; width: 900px;">
        <img style='vertical-align:middle;width:50px;' src="https://aroundhomzapp.com/images/logo.png">
        <div style='vertical-align:middle; display:inline;font-weight:bold; font-size: 20px;margin-left:10px;'>
            Upcountry Approval Mail
        </div>
    </div>
    <div style="margin-left: 8px;">
        <p>Dear Partner, </p>
        <p>We request your approval for Booking ID <strong><?php echo $booking_id;?></strong> as upcountry distance has exceeded the threshold limit.</p>
        <p>Call Type: <?php echo $price_tags?></p>
      
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;">Customer Name - <?php echo $name; ?> </p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;">Mobile - <?php echo $booking_primary_contact_no; ?></p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:5px;">Appliance - <?php echo $appliance,", ".
                $appliance_brand.", ". $appliance_category.", ".$appliance_capacity; ?></p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:2px;">Address - <?php echo $booking_address; ?></p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">City - <?php echo $city; ?></p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">Pincode- <?php echo $booking_pincode; ?></p>
        <p style="max-width: 900px; word-wrap:break-word;margin-bottom: 1px;margin-top:1px;">State- <?php echo $state; ?></p>
        <p><?php echo $upcountry_distance; ?> KM (Rs. <?php echo $upcountry_distance* $partner_upcountry_rate; ?>) upcountry distance needs to be approved by your office by clicking on the <a href="<?php echo base_url();?>partner/upcountry_charges_approval/<?php echo $booking_id;?>/0">link.</a></p>
        <p><a href="<?php echo base_url();?>partner/reject_upcountry_charges/<?php echo $booking_id; ?>/0">Click Here</a> to reject this booking. Please note that this booking would be automatically cancelled by the system after 2 working days.</p>
        
        <br/><br/>
        <p>Regards,</p>
        <p>247Around Team</p>
    </div>
    
</div>