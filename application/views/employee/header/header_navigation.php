<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="format-detection" content="telephone=no">
        <title>247around</title>    
        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
        <!-- bootstrap-daterangepicker -->
        <link href="<?php echo base_url()?>css/daterangepicker.css" rel="stylesheet">
        <!-- Animate CSS -->
        <link href="<?php echo base_url()?>css/animate.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" rel="stylesheet">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <!-- Load jQuery UI Main CSS-->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <!-- Load jqgrid -->
        <script type='text/javascript' src='<?php echo base_url()?>js/jquery.jqGrid.js'></script>
        <link rel='stylesheet' type='text/css' href='<?php echo base_url()?>css/ui.jqgrid.css' />
        <link rel='stylesheet' type='text/css' href='https://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css' />
        <script type='text/javascript' src='<?php echo base_url()?>js/grid.locale-en.js'></script>
        <!-- Load jQuery UI Main JS  -->
        <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url()?>css/style.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <!-- Loading Form js -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
        <!-- Loading Noty script library -->
        <script type="text/javascript" src="<?php echo base_url()?>js/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src='https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js'></script>
        <script src='//cdn.datatables.net/buttons/1.2.1/js/buttons.flash.min.js'></script>
        <script src='//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
        <script src='//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>
        <script src='//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
        <script src='//cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js'></script>
        <script src='//cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js'></script>
        <script src='https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js'></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?php echo base_url()?>/js/moment.min.js"></script>
        <script src="<?php echo base_url()?>js/daterangepicker.js"></script>
        <link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        <script src="<?php echo base_url();?>js/jquery.loading.js"></script>
    </head>
    <body>
        <div id="wrapper">
        <!-- Navigation -->
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-custom" role="navigation" style="margin-bottom: 0;">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html"></a>
            </div>
            <ul class="nav navbar-top-links navbar-left">
             <?php 
             foreach($main_nav['parents'] as $index =>$p_id){
                $link='';
                if($main_nav['navData']["id_".$p_id]['link'] !=''){
                    $link =  base_url().$main_nav['navData']["id_".$p_id]['link'];
                }
                if(!array_key_exists("id_".$p_id, $main_nav['navFlow'])){
                    ?>
                    <li><a href="<?php echo $link?>"><?php echo $main_nav['navData']["id_".$p_id]['title']?></a>
                    <?php
                    } 
                    else{
                    ?>
                 <li class="dropdown "><a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $link?>"><?php echo $main_nav['navData']["id_".$p_id]['title']?> <i class="fa fa-caret-down"></i></a>
                     <ul class="dropdown-menu">
                         <?php
                         $t=0;
                     foreach($main_nav['navFlow']["id_".$p_id] as $childID){
                         $childLink='';
                            if($main_nav['navData']["id_".$childID]['link'] !=''){
                            $childLink =  base_url().$main_nav['navData']["id_".$childID]['link'];
                            }
                         if(!array_key_exists("id_".$childID, $main_nav['navFlow'])){
                         ?>
                         <li >
                            <a  href="<?php echo $childLink?>"><i class="fa fa-fw fa-desktop"></i> <strong> <?php echo $main_nav['navData']["id_".$childID]['title']?></a></strong>
                         <?php
                         }
                         else{
                         ?>
                          <li class="dropdown dropdown-submenu">
                            <a href="<?php echo $childLink?>" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-fw fa-desktop "></i> <strong><?php echo $main_nav['navData']["id_".$childID]['title']?></strong></a>     
                            <ul class="dropdown-menu">
                                 <?php
                                 foreach($main_nav['navFlow']["id_".$childID] as $subchildID){
                                    $subChildLink='';
                                    if( $main_nav['navData']["id_".$subchildID]['link'] !=''){
                                    $subChildLink =  base_url(). $main_nav['navData']["id_".$subchildID]['link'];
                                    }
                                 ?>
                                    <li><a href="<?php echo $subChildLink?>"><i class="fa fa-fw fa-desktop"></i> <strong> <?php echo  $main_nav['navData']["id_".$subchildID]['title']?></strong></a>
                                    </li>
                                    <li class="divider"></li>
                                 <?php
                                 }
                                 ?>
                                    </ul>
                                    <?php
                             }
                             ?>
                          </li>
                          <li class="divider"></li>
                          <?php                        
                          if(($main_nav['navData']["id_".$p_id]['title'] == 'Reports') && $t==0){
                              $t++;
                          ?>
                          <li>
                            <a href="" data-toggle="modal" data-target="#sidebar-right" id="export_data"><i class="fa fa-fw fa-desktop"></i> <strong>Download serviceability Report</strong></a>
                        </li>
                        <li class="divider"></li>
                             <?php   
                          }
                     }
                     ?>
                             </ul>
                             <?php
                     }
                     ?>
                     </li>
                <?php
                }
                ?>
            <!-- /.navbar-header -->
            </ul>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('employee_id'); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php
                    foreach($right_nav['navData'] as $rightNavData){
                        $rightNavLink = '#';
                        if($rightNavData['link'] !=''){
                            $rightNavLink = base_url().$rightNavData['link'];
                        }
                    ?>
                        <li>
                            <a href="<?php echo $rightNavLink ?>"><i class="fa fa-fw fa-desktop "></i> <strong><?php echo $rightNavData['title']?></strong></a>
                        </li>
                        <li class="divider"></li>
                        <?php
                    }
                        ?>
                    </ul>
                </li>
                <li><a href="<?php echo base_url()?>employee/login/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
                                <div class="dropdown" style="float:right;margin: 15px 14px 0px 0px;">
                                    <a class=" dropdown-toggle fa fa-bell" id="notification_holder"  data-toggle="dropdown" onclick="get_notifications(<?php echo $this->session->userdata('id'); ?>,'employee')"></a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="notification_holder" id="notification_container" style="padding-top: 0px;margin-top: 18px;border: 1px solid #2c9d9c;"> 
                                    <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                                    </ul>
  </div>
            </ul>
        </nav>
        
        <!-- end export data Modal -->
        <div class="export_modal">
            <div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="main_modal_title">Export Serviceability Data</h4>
                        </div>
                        <div class="modal-body" id="main_modal_body">
                            <form action="<?php echo base_url();?>employee/booking/download_serviceability_data" method="post" target="_blank">
                                <div class="form-group">
                                    <select class="form-control" id="modal_service_id" name="service_id[]" multiple="multiple" required=""> 
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="radio-inline"><input type="radio" name="pincode_optradio" value="0" checked="">Without Pincode</label>
                                    <label class="radio-inline"><input type="radio" name="pincode_optradio" value="1">With Pincode</label>
                                </div>
                                <div class="modal-footer">
                                    <div class="text-right">
                                        <div class="btn btn-default" data-dismiss="modal">Cancel</div>
                                        <input type="submit" class="btn btn-success" value="Export">
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
         <!-- end export data Modal -->
        <script type="text/javascript">
            
            $("#modal_service_id").select2({
                placeholder: "Select Appliance",
                allowClear: true
            });
            (function($){
            $(document).ready(function(){
                    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        $(this).parent().siblings().removeClass('open');
                        $(this).parent().toggleClass('open');
                    });
                });
                
                $('#export_data').click(function(){
                    $.ajax({
                        type: 'GET',
                        url: '<?php echo base_url()?>employee/booking/get_service_id',
                        success: function (response) {
                         $("#modal_service_id").html(response);

                       }
                    });
                });
            })(jQuery);
        </script>
        <style type="text/css">
            
            .export_modal .select2-container{width:100%!important;}
            .export_modal .select2-search__field{width:100%!important;}
            /* MODAL FADE LEFT RIGHT BOTTOM */
            .export_modal .modal.fade:not(.in).left .modal-dialog {
                -webkit-transform: translate3d(-25%, 0, 0);
                transform: translate3d(-25%, 0, 0);
            }
            .export_modal .modal.fade:not(.in).right .modal-dialog {
                -webkit-transform: translate3d(25%, 0, 0);
                transform: translate3d(25%, 0, 0);
            }
            .export_modal .modal.fade:not(.in).bottom .modal-dialog {
                -webkit-transform: translate3d(0, 25%, 0);
                transform: translate3d(0, 25%, 0);
            }
            .export_modal .modal.right .modal-dialog {
                position:absolute;
                top:0;
                right:0;
                margin:0;
            }
            .export_modal .modal.right .modal-content {
                min-height:100vh;
                border:0;
                border-radius: 0px;
            }
            .export_modal .modal.right .modal-footer {
                position: fixed;
                left: 0;
                right: 0;
            }
            .export_modal .modal-header .close {
                margin-top: -2px;
                position: absolute;
                top: 4px;
                left: -30px;
                background-color: #183247;
                width: 30px;
                height: 30px;
                opacity: 1;
                color: #fff;
            }
            .marginBottom-0 {margin-bottom:0;}
            .dropdown-submenu{position:relative;}
            .dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
            .dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
            .dropdown-submenu:hover>a:after{border-left-color:#555;}
            .dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}
        </style>
        
        <script type="text/javascript" src="https://blackmelon.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/v7ee31/b/4/a44af77267a987a660377e5c46e0fb64/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=b41bd36b"></script>

  <div class="main_search">
            <form name="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/user/finduser" method="GET">
                <input type="search" id="search_in" class="search_in "name="search_value" placeholder="Booking ID/Phone Number" style="position: absolute; padding-left:10px; ">
            </form>
            <label class="search_fab " for="search_in"> <i class="fa fa-search" aria-hidden="true" ></i> </label>

        </div>
        
        <style>
            .nav .open>a, .nav .open>a:focus, .nav .open>a:hover {
    background-color: #2c9d9c;
}
    .navigation_li{
    font: normal 16px/16px Century Gothic;
    color: #fff;
    list-style: none;
    padding: 15px 0px 6px 42px;
    }
    .navigation_li a:hover {
    background: none;
}
.Normal{
    background: url(<?php echo base_url() ?>/images/logo_small.png) 7px 3px no-repeat;
}    
.Important{
    background: url(<?php echo base_url() ?>/images/i.png) 7px 3px no-repeat;
}
.no_new_notification{
    color: #000;
        text-align: center;
    padding: 3px;
}
            </style>
            <script>
                function get_notifications(entity_id,entity_type){
                    $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>push_notification/get_notifications',
                    data: {entity_id: entity_id,entity_type: entity_type},
                    success: function (response) {
                        $("#notification_container").html(response);
                    }
                    });
                }
                </script>
