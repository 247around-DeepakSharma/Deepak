<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>
    #datatable1_wrapper{
        margin-top: 20px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_center/spare_parts/0/1">
                                    Pending Spares
                                </a>
                            </li>
                            <!-- <li role="presentation">
                                <a href="#tabs-3" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_center/defective_spare_parts/0/1">
                                    Shipped Spares by SF
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-5" role="tab" data-toggle="tab" aria-expanded="true">
                                    Pending Spare Quotes
                                </a>
                            </li>-->
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="tabs-first"></div>                                                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>

<script type="text/javascript">    
    
    function load_view(url, tab) {
        //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>" + url,
            data: {is_ajax:true},
            success: function (data) {
                $(tab).html(data);
               
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }
    
    
   
    
    $(document).ready(function () {
        
        load_view('service_center/requested_spare_on_sf/0/1', '#tabs-first');        
        
    });
    
</script>