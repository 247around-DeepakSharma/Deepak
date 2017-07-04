<?php foreach ($query as $key => $value) { ?>
    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
        <span class="count_top" style="display:block;min-height: 60px;"><?php echo $value['description'] ?></span>
        <div class="count"><?php echo $data[$key][0]['count'] ?></div>
    </div>
<?php } ?>