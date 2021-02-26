<!-- top tiles -->
<div class="row tile_count">
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top">Completed Booking</span>
        <div class="count"><?php echo $completed_booking ;?></div>
        <span class="count_bottom" title="<?php echo $last_month_completed_booking; ?>"><?php echo $last_month_completed_booking; ?> in last month</span>
        <?php //if($completed_booking_percentage_change >= 0) { ?> 
<!--        <span class="count_bottom" title="//<?php //echo $last_month_completed_booking; ?>"><i class="green"><i class="fa fa-sort-asc"></i><?php //echo sprintf("%1\$.2f",($completed_booking_percentage_change)); ?>%</i> From last month</span>-->
       <?php //} else { ?> 
<!--        <span class="count_bottom" title="//<?php //echo $last_month_completed_booking; ?>"><i class="red"><i class="fa fa-desc"></i><?php //echo sprintf("%1\$.2f",($completed_booking_percentage_change)); ?>%</i> From last month</span>-->
        <?php //} ?>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top">Cancelled Booking</span>
        <div class="count"><?php echo $cancelled_booking ;?></div>
        <span class="count_bottom" title="<?php echo $last_month_cancelled_booking; ?>"><?php echo $last_month_cancelled_booking; ?> in last month</span>
        <?php //if($cancelled_booking_percentage_change >= 0) { ?> 
<!--        <span class="count_bottom" title="<?php //echo $last_month_cancelled_booking; ?>"><i class="green"><i class="fa fa-sort-asc"></i><?php //echo sprintf("%1\$.2f",($cancelled_booking_percentage_change)); ?>%</i> From last month</span>-->
        <?php //} else { ?> 
<!--        <span class="count_bottom" title="<?php //echo $last_month_cancelled_booking; ?>"><i class="red"><i class="fa fa-desc"></i><?php //echo sprintf("%1\$.2f",($cancelled_booking_percentage_change)); ?>%</i> From last month</span>-->
        <?php //} ?>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top">Installation Escalation</span>
        <div class="count"><?php echo sprintf("%1\$.2f",($escalation_percentage[0]['unique_installation_escalate_percentage'])); ?> %</div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top">Repair Escalation</span>
        <div class="count"><?php echo sprintf("%1\$.2f",($escalation_percentage[0]['unique_repair_escalate_percentage'])); ?> %</div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top">Pincode Covered</span>
        <div class="count"><?php echo $pincode_covered; ?></div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
        <span class="count_top"> Avg Rating</span>
        <div class="count"><?php echo $avg_rating; ?></div>
    </div>
    <?php if ($this->session->userdata('is_prepaid') == 1) { ?>
    <a href="<?php echo base_url();?>payment/details" target="_blank">
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="width: 14%;">
            <span class="count_top">Balance Amount</span>
            <div class="count" style="text-decoration: underline;color:red;"><?php echo $prepaid_amount['prepaid_amount'];?></div>
        </div>
        </a>
    <?php } ?>
</div>
<?php  if(!empty($prepaid_amount['prepaid_msg'])){ ?>
<script src="<?php echo base_url(); ?>js/around_notify.js"></script>
<script>
    
    $(document).ready(function(){
        $.notify({
            message: '<?php echo $prepaid_amount['prepaid_msg']; ?>'

            },{
                type: 'danger',
                placement: {
			from: "bottom",
			align: "center"
		}
            }
        );
    });
</script>
<?php } ?>
<!-- /top tiles -->