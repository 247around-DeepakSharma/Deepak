<!-- top tiles -->
<div class="row tile_count">
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
        <span class="count_top">Completed Booking</span>
        <div class="count"><?php echo $completed_booking ;?></div>
        <?php if($completed_booking_percentage_change >= 0) { ?> 
        <span class="count_bottom" title="<?php echo $last_month_completed_booking; ?>"><i class="green"><i class="fa fa-sort-asc"></i><?php echo sprintf("%1\$.2f",($completed_booking_percentage_change)); ?>%</i> From last month</span>
        <?php } else { ?> 
        <span class="count_bottom" title="<?php echo $last_month_completed_booking; ?>"><i class="red"><i class="fa fa-desc"></i><?php echo sprintf("%1\$.2f",($completed_booking_percentage_change)); ?>%</i> From last month</span>
        <?php } ?>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
        <span class="count_top">Cancelled Booking</span>
        <div class="count"><?php echo $cancelled_booking ;?></div>
        <?php if($cancelled_booking_percentage_change >= 0) { ?> 
        <span class="count_bottom" title="<?php echo $last_month_cancelled_booking; ?>"><i class="green"><i class="fa fa-sort-asc"></i><?php echo sprintf("%1\$.2f",($cancelled_booking_percentage_change)); ?>%</i> From last month</span>
        <?php } else { ?> 
        <span class="count_bottom" title="<?php echo $last_month_cancelled_booking; ?>"><i class="red"><i class="fa fa-desc"></i><?php echo sprintf("%1\$.2f",($cancelled_booking_percentage_change)); ?>%</i> From last month</span>
        <?php } ?>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
        <span class="count_top">Installation Escalation</span>
        <div class="count"><?php echo sprintf("%1\$.2f",($escalation_percentage[0]['unique_installation_escalate_percentage'])); ?></div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
        <span class="count_top">Repair Escalation</span>
        <div class="count"><?php echo sprintf("%1\$.2f",($escalation_percentage[0]['unique_repair_escalate_percentage'])); ?></div>
    </div>
    <?php if (!empty($this->session->userdata('is_prepaid'))) { ?>
        <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top">Prepaid Amount ( Rs.)</span>
            <div class="count"><?php echo $prepaid_amount['prepaid_amount'];?></div>
        </div>
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