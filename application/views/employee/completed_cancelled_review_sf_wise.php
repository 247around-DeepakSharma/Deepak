<div class="right_col" role="main">
    <div class="row">
        <div class="clearfix"></div>        
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Bookings <?php echo $status; ?> By SF</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form method='post'>
                        <div class="table_filter" style="background: #5bc0de;padding: 10px;margin-bottom: 10px;border-radius: 5px;">
                            <div class="row">
                                <!--Partners Filter-->
                                <div class="col-md-3">
                                    <div class="item form-group">                                  
                                        <label for="" style="color:#fff">Partners</label>
                                        <select class="form-control filter_table" id="partner_id<?php echo '_'.$status; ?>" name="partners">
                                            <option value="" <?php if (empty($filters['partner_id'])) { echo 'selected'; }?>>
                                                All
                                            </option>
                                            <?php foreach ($partners as $val) { ?>
                                                <option value="<?php echo $val['id'] ?>" <?php if (isset($filters['partner_id'])) {
                                                    if ($filters['partner_id'] == $val['id']) {
                                                        echo 'selected';
                                                    }
                                                    } ?>><?php echo $val['public_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!--Appliance Filter-->
                                <div class="col-md-3">
                                    <div class="item form-group">
                                        <label for="" style="color:#fff">Appliance</label>
                                        <select class="form-control filter_table" id="service_id<?php echo '_'.$status; ?>" name="services">
                                            <option value="" <?php if (empty($filters['service_id'])) { echo 'selected'; } ?>>
                                                All
                                            </option>
                                            <?php foreach ($services as $val) { ?>
                                                <option value="<?php echo $val['id'] ?>" <?php if (isset($filters['service_id'])) {
                                                    if ($filters['service_id'] == $val['id']) {
                                                        echo 'selected';
                                                    }
                                                    } ?>><?php echo $val['services'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>                                        
                                    </div>
                                </div>
                                <!--Request Type Filter-->
                                <div class="col-md-3">
                                    <div class="item form-group">
                                        <label for="" style="color:#fff">Request Type</label>
                                        <select class="form-control filter_table" id="request_type<?php echo '_'.$status; ?>" name="request_type">
                                            <option value="" <?php if (empty($filters['request_type'])) { echo 'selected'; } ?>>
                                                All
                                            </option>
                                            <?php foreach ($request_types as $key => $val) { ?>
                                                <option value="<?php echo $key ?>" <?php if (isset($filters['request_type'])) {
                                                    if ($filters['request_type'] == $key) {
                                                        echo 'selected';
                                                    }
                                                    } ?>><?php echo $val ?>
                                                </option>
                                            <?php } ?>
                                        </select>                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="item form-group">                                        
                                        <label for="" style="color:#fff">In Warranty</label>
                                        <select class="form-control filter_table" id="free_paid<?php echo '_'.$status; ?>" name="free_paid">
                                            <option value="" <?php if (empty($filters['free_paid'])) { echo 'selected'; } ?>>
                                                All
                                            </option>
                                            <option value="Yes" <?php if (isset($filters['free_paid'])) {
                                                    if ($filters['free_paid'] == 'Yes') {
                                                        echo 'selected';
                                                    }
                                                } ?>>Yes (In Warranty)
                                            </option>
                                            <option value="No" <?php if (isset($filters['free_paid'])) {
                                                    if ($filters['free_paid'] == 'No') {
                                                        echo 'selected';
                                                    }
                                                } ?>>No (Out Of Warranty)
                                            </option>  
                                        </select>
                                    </div>
                                </div>
                                <!--States Filter-->
                                <div class="col-md-3">
                                    <div class="item form-group">                                  
                                        <label for="" style="color:#fff">State</label>
                                        <select class="form-control filter_table" id="state_code<?php echo '_'.$status; ?>" name="states">
                                            <option value="" <?php if (empty($filters['states'])) { echo 'selected'; } ?>>
                                                All
                                            </option>
                                            <?php foreach ($states as $val) { ?>
                                                <option value="<?php echo $val['id'] ?>" <?php if (isset($filters['states'])) {
                                                    if ($filters['states'] == $val['id']) {
                                                        echo 'selected';
                                                    }
                                                    } ?>><?php echo $val['state'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>                                        
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 21px;padding: 0px 1px;width: 100px;">
                                    <div class="item form-group">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <input class="btn btn-success" name="sf_review_data" id="sf_review_data<?php echo '_'.$status; ?>" type="button" value="Apply Filters" style="background: #405467;padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </form>
                    <div class="tab-content" style="margin-top: 10px;">
                        <div class="tab-pane fade in active" id="tab1">                            
                            <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_sf_table<?php echo '_'.$status; ?>">
                                <thead>
                                    <tr style="background: #405467;color: #fff;margin-top: 5px;">
                                        <th>S.no</th>
                                        <th>Service Centers</th>
                                        <th>State</th>                            
                                        <th>Day0</th>
                                        <th>Day1</th>
                                        <th>Day2</th>
                                        <th>Day3</th>
                                        <th>Day4</th>
                                        <th>Day5 - Day7</th>
                                        <th>Day8 - Day15</th>
                                        <th>> Day15</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="review_data<?php echo '_'.$status; ?>">
                                    <?php echo $list_data; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- END -->

<script>
    $('#request_type<?php echo '_'.$status; ?>').select2({
        allowClear: false
    });
    $('#service_id<?php echo '_'.$status; ?>').select2({
        allowClear: false
    });
    $('#free_paid<?php echo '_'.$status; ?>').select2({
        allowClear: false
    });
    $('#state_code<?php echo '_'.$status; ?>').select2({
        allowClear: false
    });
    $('#partner_id<?php echo '_'.$status; ?>').select2({
        allowClear: false
    });
    
    $("#sf_review_data<?php echo '_'.$status; ?>").click(function(){
        var data = {
            partners : $('#partner_id<?php echo '_'.$status; ?>').val(),
            services : $('#service_id<?php echo '_'.$status; ?>').val(),
            request_type : $('#request_type<?php echo '_'.$status; ?>').val(),
            free_paid : $('#free_paid<?php echo '_'.$status; ?>').val(),
            states : $('#state_code<?php echo '_'.$status; ?>').val(),
            is_ajax_request : 1
        };
        $.ajax({
            method:'POST',
            url:'<?php echo base_url()."employee/booking/review_bookings_by_status_sf_wise/".$status ?>',
            data: data,
            beforeSend: function () {
                $("#review_data<?php echo '_'.$status; ?>").html("<tr><td colspan=12><center><img src='<?php echo base_url(); ?>images/loadring.gif' style=></center></td></tr>");
            },
            success:function(response){
                if(response.indexOf('No Booking Found') != -1){
                    $("#review_data<?php echo '_'.$status; ?>").html("<tr><td colspan='12'>"+response+"</td></tr>");
                }
                else{
                    $("#review_data<?php echo '_'.$status; ?>").html(response);
                }
            }                            
        });
    });
    
    $('.btn-count').click(function(){
            var status = "<?php echo $status; ?>";
            // Partner Filter
            var partner_id = 0;
            if($('#partner_id<?php echo '_'.$status; ?>').val() != '') {
                partner_id = $('#partner_id<?php echo '_'.$status; ?>').val();
            }
            // State Filter
            var state_code = 0;
            if($('#state_code<?php echo '_'.$status; ?>').val() != ''){
                state_code = $('#state_code<?php echo '_'.$status; ?>').val();
            }
            // Request Type Filter
            var request_type = 0;
            if($('#request_type<?php echo '_'.$status; ?>').val() != ''){
                request_type = $('#request_type<?php echo '_'.$status; ?>').val();
            }            
            // Service Filter
            var service_id = 0;
            if($('#service_id<?php echo '_'.$status; ?>').val() != ''){
                service_id = $('#service_id<?php echo '_'.$status; ?>').val();
            }            
            // Free Paid Filter
            var free_paid = 0;
            if($('#free_paid<?php echo '_'.$status; ?>').val() != ''){
                free_paid = $('#free_paid<?php echo '_'.$status; ?>').val();
            }            
            
            var min_review_age = $(this).attr('data-review-age-min');
            var max_review_age = $(this).attr('data-review-age-max');            
            var sf_id = $(this).attr('data-sf');
            
            window.open(
                '<?php echo base_url();?>employee/booking/review_bookings_by_status/'+status+'/0/0/0/0/'+partner_id+'/'+state_code+'/'+request_type+'/'+min_review_age+'/'+max_review_age+'/0/0/'+service_id+'/'+free_paid+'/'+sf_id+'/1',
                '_blank' // <- This is what makes it open in a new window.
            );        
    });
    
    $("#tat_sf_table<?php echo '_'.$status; ?>").dataTable({
        "fnInitComplete": function (oSettings, response) {            
            $('.sf<?php echo '_'.$status; ?>').each(function(){
                var sf_id = $(this).attr('data-sf');
                $.ajax({
                    type: 'post',
                    url: '<?php echo base_url()  ?>penalty/get_sf_penalty_percentage/'+sf_id+'/<?php echo $status;  ?>/60/1',
                    success: function (response) {
                        $("#penalty<?php echo '_'.$status; ?>"+"_"+sf_id).html(response);
                    }
                });
                
                if('<?php echo $status;  ?>' == 'Completed'){                
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url()  ?>employee/booking/get_ow_completed_percentage/'+sf_id+'/60/1',
                        success: function (response) {
                            $("#status<?php echo '_'.$status; ?>"+"_"+sf_id).html(response);
                        }
                    });
                }
                
                if('<?php echo $status;  ?>' == 'Cancelled'){                
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url()  ?>employee/booking/get_cancelled_percentage/'+sf_id+'/60/1',
                        success: function (response) {
                            $("#status<?php echo '_'.$status; ?>"+"_"+sf_id).html(response);
                        }
                    });
                }
            });
        }
    });
   
</script>

<style>
    .dataTables_filter, .dataTables_paginate
    {
        float: right;
    }
</style>