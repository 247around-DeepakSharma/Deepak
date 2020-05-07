<?php foreach ($inventory_details as $key => $value) { ?>
    <?php if ($value['status'] === SPARE_PARTS_REQUESTED) { ?>
<div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/inventory/get_spare_parts','_blank')" style="cursor: pointer;">
            <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['status'])); ?></strong></div>
            <hr>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Count</div>
                <div class="count text-center"><?php echo $value['spare_count']; ?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Amount</div>
                <div class="count text-center"><?php echo round($value['spare_amount']); ?></div>
            </div>
        </div>
    <?php } else if ($value['status'] === DEFECTIVE_PARTS_PENDING) { ?>
        <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/inventory/get_spare_parts','_blank')" style="cursor: pointer;">
            <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['status'])); ?></strong></div>
            <hr>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Count</div>
                <div class="count text-center"><?php echo $value['spare_count']; ?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Amount</div>
                <div class="count text-center"><?php echo round($value['spare_amount']); ?></div>
            </div>
        </div>
    <?php } else if ($value['status'] === DEFECTIVE_PARTS_SHIPPED) { ?> 
        <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/inventory/get_spare_parts','_blank')" style="cursor: pointer;">
            <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['status'])); ?></strong></div>
            <hr>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Count</div>
                <div class="count text-center"><?php echo $value['spare_count']; ?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Amount</div>
                <div class="count text-center"><?php echo round($value['spare_amount']); ?></div>
            </div>
        </div>
    <?php } else if ($value['status'] === SPARE_OOW_EST_REQUESTED) { ?>
        <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/inventory/get_spare_parts','_blank')" style="cursor: pointer;">
            <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['status'])); ?></strong></div>
            <hr>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Count</div>
                <div class="count text-center"><?php echo $value['spare_count']; ?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Amount</div>
                <div class="count text-center"><?php echo round($value['spare_amount']); ?></div>
            </div>
        </div>
    <?php }else if($value['status'] === DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE){ ?>
        <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/inventory/get_spare_parts','_blank')" style="cursor: pointer;">
            <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['status'])); ?></strong></div>
            <hr>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Count</div>
                <div class="count text-center"><?php echo $value['spare_count']; ?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
                <div class="query_description">Amount</div>
                <div class="count text-center"><?php echo round($value['spare_amount']); ?></div>
            </div>
        </div>  
    <?php } ?>
<?php } ?>
<!-- warehouse data -->
<div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count">
    <div class="count_top text-center"><strong>Spare Need to shipped by Warehouse</strong></div>
    <hr>
    <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
        <div class="query_description">To SF</div>
        <div class="count text-center"><?php echo $shipped_spare_by_wh_to_sf; ?></div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
        <div class="query_description">To Partner</div>
        <div class="count text-center"><?php echo $shipped_spare_by_wh_to_partner; ?></div>
    </div>
</div>

<!-- brackets data -->
<div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" onclick="window.open('<?php echo base_url();?>employee/dashboard/brackets_snapshot_full_view','_blank')" style="cursor: pointer;">
    <div class="count_top text-center"><strong>SF has zero brackets</strong></div>
    <hr>
    <div class="col-md-12 col-sm-12 col-xs-12 sub_description1 text-center">
        <div class="query_description">Count</div>
        <div class="count text-center"><?php echo $brackets_count; ?></div>
    </div>
</div>