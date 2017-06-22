<style>
    .thumbnail .image {
        height: 100%;
        overflow: hidden;
    }
    .view .tools{
        margin: 66px 0 0;
    }
</style>
<div class="right_col" role="main">

    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2><i class="fa fa-bars"></i> Review Images</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="min-height:500px!important;">
                        <div class="review_image">
                            <div class="row">
                                <?php
                                if (!empty($image_list)) {
                                    foreach ($image_list as $val) {
                                        $url = "http://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/misc-images/" . $val['image_name'];
                                        ?>
                                        <div class="col-md-4">
                                            <div class="thumbnail">
                                                <div class="image view view-first">
                                                    <img style="width: 100%; display: block;" src="<?php echo $url ?>" alt="<?php echo $val['image_name']?>" />
                                                    <div class="mask" style="height: 100%;">
                                                        <div class="tools tools-bottom">
                                                            <a href="<?php echo $url;?>"><i class="fa fa-link"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>