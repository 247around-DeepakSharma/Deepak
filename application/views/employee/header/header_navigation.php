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
        <link rel="shortcut icon" href="<?php echo base_url();?>images/favicon.ico" />
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
        <script src="https://cdn.jsdelivr.net/jquery.marquee/1.3.1/jquery.marquee.min.js"></script>
        <style>
        .notification:hover {
            background: red;
        }

        .notification .badge {
            position: absolute;
            top: 0px;
            right: -2px;
            padding: 3px 6px;
            border-radius: 50%;
            background-color: red;
            color: white;
        }
        .notification { 
            background: none;
        }
        </style>
       
        <?php if(ENVIRONMENT === 'production') { ?> 
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID; ?>"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '<?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID; ?>');
            </script>

        <?php } ?>
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
                     <ul class="dropdown-menu" style="z-index:1003;">
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
                <li>
                    <a href="#" class="notification" onclick="read_dashboard_notification()">
                        <i class="fa fa-comments"></i>
                        <span class="badge" id="dashboard_notification_count">0</span>
                    </a>
                </li>
                <li>
                    <div class="dropdown">
                        <a class=" dropdown-toggle fa fa-bell" id="notification_holder"  data-toggle="dropdown" onclick="get_notifications(<?php echo $this->session->userdata('id'); ?>,'employee')"></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="notification_holder" id="notification_container" style="padding-top: 0px;margin-top: 18px;border: 1px solid #2c9d9c;
                                height: auto;max-height: 650px;overflow-x: hidden;"> 
                        <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
            <div style="width: 100%; background: #faebcc; box-shadow: 0 0px 3px 0 #faebcc; display: none" id="marquee_div">
                <div class="marquee"></div>
                <div style="text-align: right; margin-top: -19px; margin-right: 10px;"><i class="fa fa-times" aria-hidden="true" onclick="marquee_close()"></i></div>
            </div>
        </div>
        <!-- end export data Modal -->
        <div class="export_modal">
            <div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="main_modal_title">Export Serviceability Data</h4>
                            <hr>
                            <h5 class="modal-title" id="main_modal_title">Please Select Columns For Report</h5>
                            <h5 id="response_holder_csv" style="background: #5cb85c;color: #fff;text-align: center;padding: 8px;display: none;"></h5>
                        </div>
                        <div class="modal-body" id="main_modal_body">
                            <form>
                                <div class="checkbox"><label><input type="checkbox" name="appliace_opt" id="appliace_opt" value="" onclick="showHideApplianceForm(this.checked)">Appliance</label></div>
                                    <div id="appliance_id_holder" style="display:none;">
                                        <select class="form-control" id="modal_service_id" name="service_id[]"  multiple="multiple" required=""> </select>
                                        </div>
                                <div class="checkbox"><label><input type="checkbox" name="pincode_opt" id="pincode_opt" value="">Pincode</label></div>
                                <div class="checkbox"><label><input type="checkbox" name="city_opt" id="city_opt" value="">City</label></div>
                                <div class="checkbox"><label><input type="checkbox" name="state_opt" id="state_opt" value="">State</label></div>
                                <div class="modal-footer">
                                    <div class="text-right">
                                        <div class="btn btn-default" data-dismiss="modal">Cancel</div>
                                        <button type="button" class="btn btn-success" onclick="generate_csv_and_send_email()">Export</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- end export data Modal -->
        
        
        
    <!-- Start Dashboard Notification Modal -->
    <div class="export_modal">
        <div class="modal fade right" id="dashboard_notification" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="">
                        <div class="modal-header" style="background:#d9edf7">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="main_modal_title"><i class="fa fa-fw fa-desktop" style="margin: 10px;"></i>247around Notifications</h4>
                    </div>
                        <div class="modal-body" id="main_modal_body" style="height: 630px; overflow-y: auto;">
                        <table style="width: 100%; line-height: 25px;" id="dashboard_notification_table">
                            
                        </table>
                    </div>
                    <div class="modal-footer">
                        <div class="text-right">
                            <div class="btn btn-default" data-dismiss="modal">Cancel</div>
                         </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End -->
        
        
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
            
            function checkSpcialChar(event){
                var valid = ((event.which > 64 && event.which < 91) || (event.which > 96 && event.which < 123) || (event.which > 47 && event.which < 58) || event.which == 45 || event.which == 8 || event.which == 13);
                if (!valid) {
                    event.preventDefault();
                    return false;
                }
                else {
                    var initVal = $("#search_in").val();
                    var re = /[`~!@#$%^&*()_|+=?;:'",.<>\{\}\[\]\\\/]/gi;
                    var isSplChar = re.test(initVal);
                    if(isSplChar)
                    {
                        alert("Special characters are not allowed!!");
                        $("#search_in").focus();
                        return false;
                    }
                    else
                        return true;
                }
            }
            
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
            .notification_icon{ width: 45px;
                height: 45px;
                border-radius: 50%;
                box-shadow: 0 6px 10px 0 #666;
                font-size: 18px;
                line-height: 45px;
                text-align: center; 
            }
            .notification_icon_td{
                width: 65px;
                vertical-align: top;
                padding-top: 10px;
            }
        </style>
        
        <?php if(ENVIRONMENT === 'production') { ?> 
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID ; ?>"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', <?php echo _247AROUND_CRM_GGL_ANALYTICS_TAG_ID ; ?>);
            </script>

        <?php } ?>
        
        <script type="text/javascript" src="https://blackmelon.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/v7ee31/b/4/a44af77267a987a660377e5c46e0fb64/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=b41bd36b"></script>

        <div class="main_search">
            <form name="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/user/finduser" method="GET">
                <input type="search" id="search_in" class="search_in "name="search_value" placeholder="Booking ID/Phone Number" style="position: absolute; padding-left:10px; " onkeypress="return checkSpcialChar(event)">
            </form>
            <label class="search_fab " for="search_in"> <i class="fa fa-search" aria-hidden="true" ></i> </label>
            <?php if(!$saas_module) { ?>
                    <button type="button" class="search_fab"  id="partner_tollfree" data-toggle="modal" style="margin-left:90%;border: none;background-color: #1a8a2dd4">
    <i class="fa fa-phone" aria-hidden="true" style="padding-top: 0px;margin-top: 0px"></i> </button>
            <?php } ?>
        </div>
        <!-- Modal -->
         <div id="partner_tollfree_no_modal" class="modal fade" role="dialog">
             <div class="modal-dialog">

                 <!-- Modal content-->
                 <div class="modal-content">
                     <div class="modal-header well"  style="background-color: #2C9D9C;border-color: #2C9D9C;">
                         <button type="button" class="close btn-primary well" data-dismiss="modal"style="color:white;">&times;</button>
                         <h4 class="modal-title"style="color:white;text-align: center;">Partners Contacts</h4>
                     </div>
                     <div class="modal-body">

                     </div>
                     </div>


             </div>
         </div>

        
        <style>
            #partner_tollfree,
#partner_tollfree:focus,
#partner_tollfree:active{
	border:1px solid black;
	background:none;
	outline:none;
	padding:0;
}

            .nav .open>a, .nav .open>a:focus, .nav .open>a:hover {
    background-color: #2c9d9c;
}
    .navigation_li{
    font-size:14px;
    color: #fff;
    list-style: none;
    padding: 2px 0px 0px 42px;
    }
    .navigation_li a:hover {
    background: none;
}
.Normal{
    background: url(<?php echo base_url() ?>/images/norm_final.png) 7px 5px no-repeat;
}    
.Important{
    background: url(<?php echo base_url() ?>/images/n_imp_final.png) 7px 13px no-repeat;
}
.no_new_notification{
    color: #000;
        text-align: center;
    padding: 3px;
}
.navbar-top-links .dropdown-menu li a {
        margin-left: 14px;
        padding: 3px 15px;
        
        .notification {
            background-color: #555;
            color: white;
            text-decoration: none;
            padding: 15px 26px;
            position: relative;
            display: inline-block;
            border-radius: 2px;
        }

        .notification:hover {
            background: red;
        }

        .notification .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 5px 10px;
            border-radius: 50%;
            background-color: red;
            color: white;
        }
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
                function showHideApplianceForm(value){
                if(value){
                    document.getElementById('appliance_id_holder').style.display='block';
                }
                else{
                    document.getElementById('appliance_id_holder').style.display='block';
                }
            }
            function getMultipleSelectedValues(fieldName){
    fieldObj = document.getElementById(fieldName);
    var values = [];
    var length = fieldObj.length;
    for(var i=0;i<length;i++){
       if (fieldObj[i].selected == true){
           values.push(fieldObj[i].value);
       }
    }
   return values.toString();
}
function send_csv_request(appliance_opt,pincode_opt,state_opt,city_opt,service_id){
    if(!service_id){
        service_id = 'all';
    }
    document.getElementById('response_holder_csv').style.display='block';
    document.getElementById('response_holder_csv').innerHTML = 'Soon you will get requested report via Email';
    $.ajax({
    type: 'POST',
    url: '<?php echo base_url(); ?>employee/booking/download_serviceability_data',
    data: {appliance_opt: appliance_opt,pincode_opt: pincode_opt,state_opt: state_opt,city_opt: city_opt,service_id: service_id},
    success: function (response) {
    }
    });
}
            function generate_csv_and_send_email(){
                var appliance_opt = 0;
                var pincode_opt = 0;
                var city_opt = 0;
                var state_opt =0;
                if ($('#appliace_opt').is(":checked")){
                    appliance_opt = 1; 
                }
                if ($('#pincode_opt').is(":checked")){
                    pincode_opt = 1;
                }
                if ($('#city_opt').is(":checked")){
                    city_opt = 1;
                }
                if ($('#state_opt').is(":checked")){
                    state_opt = 1;
                }
                var service_id = getMultipleSelectedValues('modal_service_id');
                if(appliance_opt === 1 || pincode_opt === 1 || city_opt ===  1 || state_opt === 1){
                    send_csv_request(appliance_opt,pincode_opt,state_opt,city_opt,service_id);
                }
                else{
                     alert("Please Select atleast 1 option");
                }
                
            }
            $(document).ready(function(){
                $("#partner_tollfree").click(function(){
                    $("#partner_tollfree_no_modal").modal("show");
                    $.ajax({
                        type: 'post',
                        url: '<?php echo  base_url()?>employee/partner/get_partner_tollfree_numbers',
                        success: function (response) {
                            var result = JSON.parse(response);
                            var data="";
                            for(var element in result){
                                    var temp = "";
                                    if(result[element].paid_service_centers){ 
                                         temp = "<button style ='margin-left: 10px;height: 25px;padding: 2px 7px;float: right;' type='button' class='btn btn-sm btn-color'>\n\
                                <i class='fa fa-user fa-lg' aria-hidden='true'></i></button>";
                                    }
                                    data = data +  "<tr><td>"+result[element].partner+temp+"</td>";
                                    data +=  "<td>"+result[element].name+"</td>";
                                    data +=  "<td>"+result[element].contact+"<button style ='margin-left: 10px;height: 25px;padding: 2px 7px;float: right;' type='button' class='btn btn-sm btn-color' onclick='outbound_call("+result[element].contact+")'>\n\
                                <i class='fa fa-phone fa-lg' aria-hidden='true'></i></button></td></tr>";
                            }
                            var tb="<table class='table  table-bordered table-condensed ' id='partner_toll_free_table'>";
                            tb+='<thead>';
                            tb+='<tr>';
                            tb+='<th>Partner</th>';
                            tb+='<th>Name</th>';
                            tb+='<th>No.</th>';
                            tb+='</tr>';
                            tb+='</thead>';
                            tb+='<tbody>';
                            tb+=data;
                            tb+='</tbody>';
                            tb+='</table>';
                            $("#partner_tollfree_no_modal  .modal-body").html(tb);
                            $('#partner_toll_free_table').DataTable();
                            $('#partner_toll_free_table  th').css("background-color","#ECEFF1");
                            $('#partner_toll_free_table  tr:nth-child(even)').css("background-color","#FAFAFA");
                       }
                    });
                });
                $.ajax({
                        type: 'POST',
                        url: '<?php echo  base_url()?>employee/dashboard/get_dashboard_notification/<?php echo _247AROUND_EMPLOYEE_STRING; ?>',
                        success: function(data) {
                            data = JSON.parse(data);
                            var marquee = data.marquee_msg;
                            var marquee_html = "";
                            $("#dashboard_notification_count").text(data.notification);
                            if(marquee.length>0){
                                $("#marquee_div").show();
                                for(var i=0; i<marquee.length; i++){
                                   marquee_html +=  marquee[i]['message']+", ";
                                }
                                $(".marquee").text(marquee_html.slice(0,-2));
                                $('.marquee').marquee({
                                            duration: 30000,
                                                 gap: 100,
                                    delayBeforeStart: 0,
                                           direction: 'left',
                                          duplicated: false
                                });
                            }
                            else{ 
                                $("#marquee_div").hide();
                            }
                        }
                    });
            });
function outbound_call(phone_number){
        var confirm_call = confirm("Call Partner ?");
        if (confirm_call == true) {      
             $.ajax({
                type: 'POST',
                url: '<?php echo  base_url()?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                }
            });
        } else {
            return false;
        }
    }
    
    function read_dashboard_notification(){
        $.ajax({
            type: 'POST',
            url: '<?php echo  base_url()?>employee/dashboard/read_dashboard_notification',
            data : {entity_type : "<?php echo _247AROUND_EMPLOYEE_STRING; ?>", entity_id : "<?php echo $this->session->userdata('id'); ?>"},
            success: function(response) {
                response = JSON.parse(response);
                var html = "";
                var seen_style = "border-bottom: 1px solid #77777761;";
                if(response.length > 0){
                    for(var i=0; i<response.length; i++){
                        if(response[i]['seen'] == '0'){
                            seen_style += "font-weight:600";
                        }
                        var date = new Date(response[i]['create_date']);
                        var hours = date.getHours();
                        var minutes = date.getMinutes();
                        var seconds = date.getSeconds();
                        html += "<tr style='"+seen_style+"'>\n\
                                    <td class='notification_icon_td'><i class='notification_icon "+response[i]['icon']+"' aria-hidden='true' style='background-color:"+response[i]['color']+"'></i></td>\n\
                                    <td style='padding-top: 10px;'><div>"+response[i]['message']+"<p style='margin-top: 10px; font-size: 12px;'>"+$.datepicker.formatDate('dd M yy', new Date(date))+" "+hours+":"+minutes+":"+seconds+"</p></div></td>\n\
                                </tr>";
                    }
                    $("#dashboard_notification_count").text(0);
                }
                else{
                    html += "<tr><td>No New Notification Found...</td></tr>";
                }
                $("#dashboard_notification_table").html(html);
                $('#dashboard_notification').modal('show');
            }
        });
    }
    
    function marquee_close(){
         $("#marquee_div").hide();
    }
</script>
                
<style>
    #partner_toll_free_table_filter{
        padding-left: 30px;
    }
    #partner_toll_free_table_length{
        display: none;
    }
    .marquee {
        width: 98%;
        overflow: hidden;
        background: #faebcc;
        height: 25px;
        padding: 2px;
    }
</style>
                