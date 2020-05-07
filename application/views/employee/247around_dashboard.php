<style>
    .col-md-3 {
        width: 23%;
    }
    .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
    .tile_stats_count{color:#333;border: 1px solid #e5e5e5;margin: 2px;}
    .tile_stats_count:hover{
        border: 1px solid #ccc;
        box-shadow: 0 0 10px #ccc;
        background: #fff;
    }
    .tile_stats_count, ul.quick-list li {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }
    .tile_stats_count:before{
        content: "";
        height:0px;
    }
    .tile_stats_count hr {
        margin-top: 0px; 
        margin-bottom: 10px;
        border: 0;
        border-top: 1px solid #eee;
    }
    .tile_stats_count .count {
        font-size: 24px!important;
    }
    .tile_stats_count .count_top{
        min-height: 38px;
    }
    .tile_stats_count .query_description{
        min-height: 30px;
    }
</style>
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