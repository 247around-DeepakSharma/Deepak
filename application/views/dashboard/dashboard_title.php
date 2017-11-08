<?php foreach ($data as $key => $value) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count">
        <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['main_description'])); ?></strong></div>
        <hr>
        <?php if(isset($value['data']['query2'])){ ?>
            <div class="col-md-6 col-sm-6 col-xs-12 sub_description1 text-center">
        <?php }else{ ?>
            <div class="col-md-12 col-sm-6 col-xs-12 sub_description1 text-center">
        <?php } ?>
            <div class="query_description"><?php echo ucwords(str_replace("_", " ", $value['data']['query1']['description'])); ?></div>
            <div class="count text-center"><?php echo $value['data']['query1']['query_data']; ?></div>
        </div>
        <?php if(isset($value['data']['query2'])) { ?>
        <div class="col-md-6 sub_description2 text-center">
            <div class="query_description"><?php echo ucwords(str_replace("_", " ", $value['data']['query2']['description'])); ?></div>
            <div class="count text-center"><?php echo $value['data']['query2']['query_data']; ?></div>
        </div>
        <?php } ?>
        
            </div>
    </div>
<?php } ?>