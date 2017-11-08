<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row tile_count" id="title_count">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
        </div>
    </div>
    <!-- /top tiles -->
</div>

<script>
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
    
    $(document).ready(function(){
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
</script>