<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>

<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
            }
            ?> 
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective/Ok Parts Need To Be Shipped</h1>
                </div>
                <div class="panel-body">
                    <div class="right_col" role="main">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_content">
                                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                            <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                                                <li role="presentation" class="active">
                                                    <a id="update_parts" href="#tabs-1" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url(); ?>service_center/update_defective_parts_pending_bookings">
                                                        Update Defective/Ok Parts
                                                    </a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url(); ?>service_center/get_defective_parts_pending_bookings">
                                                        Send Defective/Ok Parts
                                                    </a>
                                                </li>
                                            </ul>
                                            <div id="myTabContent" class="tab-content">
                                                <div class="tab-pane" id="tabs-1"></div>
                                                <div class="tab-pane" id="tabs-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if (isset($links)) echo $links; ?></div>
</div>



<script type="text/javascript">

    $('#myTabs a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr("data-url");

        var href = this.hash;
        $(this).tab('show');
        load_view(url, href);
    });

    function load_view(url, tab) {
        //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");

        $.ajax({
            type: "POST",
            url: url,
            data: {is_ajax: true},
            success: function (data) {
                $(tab).html(data);
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }
    
    $(window).load(function(){
       $('#myTabs a').trigger('click'); 
    });
</script>
