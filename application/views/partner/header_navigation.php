<?php
    $CI = & get_instance();
    $CI->load->library('email');
    $userdata = $CI->session->all_userdata();
    $partner_name = $this->session->userdata('partner_name');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>247around <?php echo $partner_name; ?> CRM</title>
        <link rel="shortcut icon" href="<?php echo base_url();?>images/favicon.ico" />
        <!-- Bootstrap -->
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- bootstrap-progressbar -->
        <link href="<?php echo base_url() ?>css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
        <!-- bootstrap-daterangepicker -->
        <link href="<?php echo base_url() ?>css/daterangepicker.css" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="<?php echo base_url() ?>css/dashboard_custom.min.css" rel="stylesheet">
        <!-- Sweet Alert Css -->
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <!-- Select2 CSS -->
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <!-- DataTable CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assest/DataTables/datatables.min.css"/>
        <!-- jQuery -->
        <script src="<?php echo base_url() ?>js/jquery.min.js"></script>
        <!-- moment.js -->
        <script src="<?php echo base_url() ?>js/moment.min.js"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?php echo base_url() ?>js/daterangepicker.js"></script>
        <!-- DateJS -->
        <script src="<?php echo base_url() ?>assest/DateJS/build/date.js"></script>
        <!-- Select2 JS -->
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <!-- sweet Alert JS -->
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url() ?>assest/DataTables/datatables.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <script src="<?php echo base_url() ?>js/partner.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.marquee/1.3.1/jquery.marquee.min.js"></script>
        <style>
            .right_col{
                min-height:700px!important;
            }
            .profile_pic {
                width: 100%;
                float: left;
            }
            .img-circle {
                border-radius: 0%;
            }
            .profile_info{
                width: 100%;
            }
            .img-circle.profile_img {
                background: #fff;
                z-index: 1000;
                position: inherit;
                margin: 0px;
                border: 1px solid rgba(52,73,94,.44);
                padding: 4px;
            }
            .select2-container--default .select2-selection--single {
                border-radius: 0px;
                border: 1px solid #ccc;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered{
                padding-top: 0px;
            }
            .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single{
                min-height: 34px;
            }
            .profile_details .profile_view{
                padding: 0px;
            }
            .nav-sm .main_container .top_nav{
                margin-left: 0px;
            }
            .nav-sm .container.body .col-md-3.left_col{
                margin-top: 60px;
                width: 40px;
            }
            .nav-md .container.body .col-md-3.left_col{
                margin-top: 60px;
                width: 206px;
            }
            .nav-md .container.body .right_col{
                margin-left: 206px;;
            }
            .nav-sm .container.body .right_col{
                margin-left: 40px;
            }
            .navbar-brand, .navbar-nav > li > a{
                line-height: 24px;
            }
            .right_col{
                min-height: 600px!important;
                margin-top: 50px!important;
            }
            .navbar{
                height: 45px;
            }
            .navbar-header{
                background: transparent;
                border: 1px solid #eee;
            }
            .navbar-brand {
                padding: 0px;
                height: 45px;
            }
            .navbar-brand>img {
                height: 100%;
                padding: 15px;
                width: auto;
            }
            .nav-sm .sidebar-footer{
                width: 40px;
            }
            .nav-sm .sidebar-footer a{
                width: 100%;
            }
            .nav-sm footer ,.nav-md footer{
                margin-left: 0px;
            }
            footer{
                padding: 20px;
            }
            .nav.side-menu > li:hover{
                background: #1ABB9C;
            }
            .side_menu_list_title{
                display: none;
            }
            .nav-md .sidebar-footer{
                width: 206px;
            }
            .sidebar-footer a{
                width: 100%;
            }
            .custom_pagination{
                float: right;
                background: #ddd;
                margin: 2px 0;
                padding: 4px;
                border-radius: 4px;
            }
            .custom_pagination a{
                border: 1px solid #ddd;
                padding: 6px 10px;
                line-height: 24px;
            }
            .custom_pagination > strong {
                color:#fff;
            }
            .nav-sm .nav.side-menu li a{
                font-size: 12px;
            }
            .navbar-brand > img{
                padding: 0px 2px;
            }
            .form-control{
                font-size: 13px;
            }
            ul.bar_tabs{
                background: transparent;
            }
            @media (max-width: 768px) {
                .navbar-fixed-top {
                  position: static;
                  bottom: 0;
                }
                .top_nav .navbar-right{
                    width: 94%;
                }
            }
            @media (max-width: 480px) {
                .navbar-header{
                    display: none;
                }
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
   
    <body class="nav-sm">        
        <div class="container body">
        <div class="main_container">
            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav class="navbar navbar-default navbar-fixed-top">
                        <div class="navbar-header">
                            <?php $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$this->session->userdata('partner_logo');?>
                            <a class="navbar-brand" href="#"><img src="<?php echo $src;?>" alt="partner_logo"></a>
                        </div>
                       
<!--                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>-->
                        <ul class="nav navbar-nav navbar-right">
<!--                             <li><div class="dropdown" style="float:right;margin: 16px 14px 0px 0px;">
                                    <a class=" dropdown-toggle fa fa-bell" id="notification_holder"  data-toggle="dropdown" onclick="get_notifications(<?php echo $this->session->userdata('partner_id'); ?>,'partner')"></a>
                                     <ul class="dropdown-menu" role="menu" aria-labelledby="notification_holder" id="notification_container" style="padding-top: 0px;margin-top: 34px;border: 1px solid #2c9d9c;
                                            height: auto;max-height: 650px;overflow-x: hidden;"> 
                                    <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                                    </ul>
                                </div>
                             </li>-->
                            <li>
                                <a href="#" class="notification" onclick="read_dashboard_notification()">
                                    <i class="fa fa-bell"></i>
                                    <span class="badge" id="dashboard_notification_count">0</span>
                                </a>
                            </li>
                            <li class="">
                                <a href="javascript:void(0);" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <?php echo $loginname; ?>                                     
                                    <span><i class="fa fa-angle-down"></i></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <?php
                                    foreach($right_nav['navData'] as $rightNavData){
                                        $rightNavLink = '#';
                                        if($rightNavData['link'] !=''){
                                            $rightNavLink = base_url().$rightNavData['link'];
                                        }
                                        ?>
                                        <li>
                                        <a href="<?php echo $rightNavLink ?>"><i class="<?php echo $rightNavData['title_icon']?>"></i> <strong><?php echo $rightNavData['title']?></strong></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="col-md-4">
                                <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>partner/search" onsubmit="return checkStringLength()">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="searched_text" placeholder="Search Booking ID or Mobile" name="searched_text" style="width: 130%;border-radius:25px 25px 25px 25px">
                                    </div> 
                                </form>
                            </li>
                        </ul>
                    </nav>
                    <div id="marquee_div">
                        <div class="marquee"></div>
                        <div style="text-align: right; margin-top: -19px; margin-right: 10px;"><i class="fa fa-times" aria-hidden="true" onclick="marquee_close()"></i></div>
                    </div>
                </div>
            </div>
             
            <div class="col-md-3 left_col menu_fixed">
                <div class="left_col scroll-view" style="width:100%;">
                    <br>
                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <?php                               
                               foreach($main_nav['parents'] as $index =>$p_id){
                               $link='';
                               if($main_nav['navData']["id_".$p_id]['link'] !=''){
                                    $link =  base_url().$main_nav['navData']["id_".$p_id]['link'];
                               }
                                if(!array_key_exists("id_".$p_id, $main_nav['navFlow'])){
                                    ?>
                                    <li><a href="<?php echo $link; ?>" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo $main_nav['navData']["id_".$p_id]['title']; ?>">
                                            <i class="<?php echo $main_nav['navData']["id_".$p_id]['title_icon']?>"></i><span class="side_menu_list_title"><?php echo $main_nav['navData']["id_".$p_id]['title']; ?>
                                            </span></a></li>
                                    <?php
                                }else if(trim($main_nav['navData']["id_".$p_id]['title']) == 'Inventory'){ ?> 
                                        <li>
                                        <a data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo $main_nav['navData']["id_".$p_id]['title']?>">
                                        <i class="<?php echo $main_nav['navData']["id_".$p_id]['title_icon']?>"></i> <span class="side_menu_list_title"><?php echo $main_nav['navData']["id_".$p_id]['title']?>
                                        </span><span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="display:none !important;">
                      
                                                <?php
                                                $t=0;
                                                foreach($main_nav['navFlow']["id_".$p_id] as $childID){                                                    
                                                    $childLink='';
                                                    if($main_nav['navData']["id_".$childID]['link'] !=''){
                                                        $childLink =  base_url().$main_nav['navData']["id_".$childID]['link'];
                                                    }
                                                    ?>
                                                <li><a href="<?php echo $childLink; ?>"><?php echo $main_nav['navData']["id_".$childID]['title']; ?></a></li>
                                                <?php }
                                                ?>

                                     </ul>
                                    </li>
                                    <?php } else {
                                    
                                    ?>     
                                    <li>
                                            <a data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo $main_nav['navData']["id_".$p_id]['title']?>">
                                            <i class="<?php echo $main_nav['navData']["id_".$p_id]['title_icon']?>"></i> <span class="side_menu_list_title"><?php echo $main_nav['navData']["id_".$p_id]['title']?>
                                            </span><span class="fa fa-chevron-down"></span></a>
                                             <ul class="nav child_menu" style="display:none !important;">
                                                <?php
                                                $t=0;
                                                foreach($main_nav['navFlow']["id_".$p_id] as $childID){                                                    
                                                    $childLink='';
                                                    if($main_nav['navData']["id_".$childID]['link'] !=''){
                                                        $childLink =  base_url().$main_nav['navData']["id_".$childID]['link'];
                                                    }
                                                    ?>
                                                <li><a href="<?php echo $childLink; ?>"><?php echo $main_nav['navData']["id_".$childID]['title']; ?></a></li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                }
                        
                                ?>
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->
                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer">
                        <a id="menu_toggle"><i class="fa fa-arrow-right"></i></a>
                    </div>
                    <!-- /menu footer buttons -->
            </div>
        </div>
    </div>
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
<style>
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
        padding: 3px 8px;
}
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
    .marquee {
        width: 98%;
        overflow: hidden;
    }
    #marquee_div {
        margin-top: 50px !important;
        margin-left: 40px;
        background: #faebcc;
        height: 25px;
        padding-top: 5px;
        box-shadow: 0 0px 3px 0 #faebcc;
        display: none;
    }
    .nav-sm .container.body .col-md-3.left_col {
        margin-top: 50px;
    }
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
                
                $( document ).ready(function() {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo  base_url()?>employee/dashboard/get_dashboard_notification/<?php echo _247AROUND_PARTNER_STRING; ?>',
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
                                          duplicated: false,
                                });
                            }
                            else{ 
                                $("#marquee_div").hide();
                            }
                        }
                    });
                });
                
                function read_dashboard_notification(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo  base_url()?>employee/dashboard/read_dashboard_notification',
                        data : {entity_type : "<?php echo _247AROUND_PARTNER_STRING; ?>", entity_id : "<?php echo $this->session->userdata('partner_id'); ?>"},
                        success: function(response) {
                            response = JSON.parse(response);
                            var html = "";
                            var seen_style = "";
                            if(response.length > 0){
                                for(var i=0; i<response.length; i++){
                                    seen_style = "border-bottom: 1px solid #77777761;";
                                    if(response[i]['seen'] == '0'){
                                        seen_style = "font-weight:600;";
                                    }
                                    var date = new Date(response[i]['create_date']);
                                    var month = date .getMonth() + 1;
                                    var day = date .getDate();
                                    var year = date .getFullYear();
                                    var hours = date.getHours();
                                    var minutes = date.getMinutes();
                                    var seconds = date.getSeconds();
                                    html += "<tr style='"+seen_style+"'>\n\
                                        <td class='notification_icon_td'><i class='notification_icon "+response[i]['icon']+"' aria-hidden='true' style='background-color:"+response[i]['color']+"'></i></td>\n\
                                        <td style='padding-top: 10px;'><div>"+response[i]['message']+"<p style='margin-top: 10px; font-size: 12px;'>"+day+"-"+month+"-"+year+" "+hours+":"+minutes+":"+seconds+"</p></div></td>\n\
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
