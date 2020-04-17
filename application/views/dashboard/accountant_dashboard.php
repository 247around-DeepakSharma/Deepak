<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<style>
    .collape_icon {
        font-size: 18px;
        color: #4b5561 !important;
        float:right;
    }
    tr[id^='arm_table_'],
    tr[id^='arm_open_call_table_']{
        background-color:#5997aa !important;
    }
    .sub-table{
        width:98%;
        margin:auto;
    }
    table.sub-table thead{
        background:#8cc6ab;
    }
</style>
<!-- page content -->
<div class="right_col ngCloak" role="main" ng-app="admin_dashboard">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
    <hr>
    <!-- Booking Report Start-->
    
</div>
<!-- /page content -->
<!-- Chart Script -->
<script>
    var post_request = 'POST';
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
        
        //top count data
        get_query_data();       
    });
    
    
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    
    function get_query_data(){
        $('#loader_gif_title').fadeIn();
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/execute_title_query';
        
        sendAjaxRequest(data,url,post_request).done(function(response){
            $('#loader_gif_title').hide();
            $('#title_count').html(response);
        });
    }
</script>t>
<style>
.text_warning{
        color:red;
    }
    [ng\:cloak], [ng-cloak], .ng-cloak {
  display: none !important;
}
select option:empty { display:none }
.select2-container--default{
        width: 154px !important;
}
.select2-selection--multiple{
        border: 1px solid #ccc !important;
    border-radius: 0px !important;
}
</style>
