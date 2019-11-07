<?php $sn = 1; foreach ($data as $key => $value) { ?>
<div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count" <?php if(isset($value['data']['query1']['booking_ids']) && !empty($value['data']['query1']['booking_ids'])){ ?>  onClick=show_dashboard_modal('<?php echo $value['data']['query1']['booking_ids'];?>') <?php }?>>
        <div class="count_top text-center"><strong><?php echo ucwords(str_replace("_", " ", $value['main_description'])); ?></strong></div>
        <div class="count_top text-center" style="color:#e9540c;font-size: 18px;"><strong><?php echo ucwords(str_replace("_", " ", $value['ownership'])); ?></strong></div>
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
<script>
    function show_dashboard_modal(modal_data){
        var modal_body = modal_data.split(',');
        var html = "<table class='table table-bordered table-hover table-responsive'><thead><th>Booking Id</th></thead><tbody>";
        $(modal_body).each(function(index,value){
            html += "<tr><td>";
            html += "<a href='/employee/user/finduser?search_value="+value+"' target='_blank'>"+value+"</a>";
            html += "</td></tr>";
        });
        html += "</tbody></table>";
        $('#open_model').html(html);
        $('#modalDiv').modal('show'); 
    }
</script>