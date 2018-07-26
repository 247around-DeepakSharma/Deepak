<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            Service Center
        </title>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="<?php echo base_url();?>images/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
        <!-- Sweet Alert Css -->
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <!-- DataTable CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assest/DataTables/datatables.min.css"/>
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <!-- sweet Alert JS -->
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        <!-- Datatable JS-->
        <script type="text/javascript" src="<?php echo base_url() ?>assest/DataTables/datatables.min.js"></script>
        
        <!-- Daterange picker-->
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <style type="text/css">
            .navbar{
            min-height: 80px;
            }
            #datepicker{cursor:pointer;}
            .card,.long-card {
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
                border-radius: 5px;
            }.long-card{
                min-height: 230px;
            }

            .card:hover {
                box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            }
            .card h5{
                font-size: 16px;
            }
            .long-card h5{
                font-size: 16px;
            }

            img {
                border-radius: 5px 5px 0 0;
            }

            .container {
                padding: 2px 16px;
            }
            .modal-title{
                color: #333;
                font-weight: 700;
            }
            .nt-badge{
                font-weight: bold;
                bottom: 14px;
                left: 70px;
                position: relative;
                background-color: green;
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
        <nav class="navbar navbar-custom">
            <!-- Modal -->
            <div id="contactUsModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div id="contactUsModalData"></div>
                        <div class="modal-header text-center" style="background-color:#E5E5FF;">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around Call Center - 9555000247</h4>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- Modal End -->
            <div class="container-fluid" style="padding-left:0px">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                    <img alt="Brand" src="<?php echo base_url()?>images/logo.jpg">
                    </a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <!--                   <li>
                            <a href="<?php echo base_url(); ?>service_center/get_search_form">Search</a>
                            </li>-->
                        <li>
                            <a href="<?php echo base_url();?>service_center/dashboard"  >Dashboard</a>
                        </li>
                        <?php if($this->session->userdata('is_sf') === '1'){ ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bookings <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url();?>service_center/pending_booking">Pending Bookings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url();?>service_center/completed_booking">Completed Bookings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url();?>service_center/cancelled_booking">Cancelled Bookings</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span style="font-weight: bold; left: 93px;" class="badge nt-badge defectivecount" >0</span>Spare Parts <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url();?>service_center/get_defective_parts_booking">Defective Parts (<span class="defectivecount"></span>)</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url();?>service_center/get_approved_defective_parts_booking">Approved Defective Parts</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i ></i> Engineers <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>service_center/add_engineer" > <strong> Add Engineer</strong></a>
                                </li>
                                <li role="separator" class="divider" style="height: 2px;"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>service_center/get_engineers" > <strong> View Engineers</strong></a>
                                </li>
                            </ul>
                        </li>
                        
                        <?php if($this->session->userdata('service_center_id') == '10'){ ?>
                        <li>
                            <a href="<?php echo base_url() ;?>employee/service_centers/show_brackets_list"><span style="font-weight: bold;" class="badge nt-badge" id="brackets_count" title="New Brackets Request">0</span>Brackets</a>
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i ></i> Invoices <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>service_center/invoices_details" > <strong> Invoice Summary</strong></a>
                                </li>
                                <?php if($this->session->userdata('is_sf') === '1'){ ?>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>service_center/customer_invoice_details" ><strong> Customer Invoice</strong></a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/service_centers/download_sf_charges_excel" ><strong> Charges List</strong></a>
                                </li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php if($this->session->userdata('is_cp') === '1'){ ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Buyback <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url();?>service_center/buyback/bb_order_details"> <strong>Buyback Order Details</strong></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url();?>service_center/buyback/show_bb_price_list"><strong>Buyback Charges List</strong></a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if($this->session->userdata('is_wh') == 1){ ?>
                        <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span style="font-weight: bold;" class="badge nt-badge" id="inventory_count" title="New Spare Request">0</span>Inventory <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url(); ?>service_center/inventory/inventory_list"><strong>Inventory List</strong></a></li>
                                    <li role="separator" class="divider"></li>
                                    <li class="dropdown dropdown-submenu">
                                        <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <strong>Outbound Processing</strong></a>     
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo base_url(); ?>service_center/inventory"><strong>Spare Details</strong></a></li>
                                            </li>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo base_url();?>service_center/get_shipped_parts_list"><strong> Spare Shipped To SF</strong></a>
                                            </li>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo base_url();?>service_center/defective_spare_parts"><strong> Defective Spare Shipped By SF</strong></a>
                                            </li>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo base_url(); ?>service_center/approved_defective_parts_booking_by_warehouse"><strong> Defective Spare Received </strong></a>
                                            </li>
                                            <li class="divider"></li>
                                        </ul>
                                    </li>
                                    <li class="divider"></li>
                                    <li class="dropdown dropdown-submenu">
                                        <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <strong>Inbound Processing</strong></a>     
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo base_url();?>service_center/acknowledge_spares_send_by_partner"><strong>Acknowledge Spares Send By Partner </strong></a>
                                            </li>
                                        </ul>
                                    </li>

                                </ul>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url();?>service_center/gst_details"  >GST</a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i ></i> Contacts <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                            <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#contactUsModal"><b>&nbsp;Contact Us&nbsp;</b></a>
                        </li>
                                <li role="separator" class="divider"></li>
                             <li>
                            <a href="<?php echo base_url();?>employee/service_centers/holiday_list"><b>&nbsp;Holiday List&nbsp;</b></a>
                        </li>
                            </ul>
                        </li>
                        
                    </ul>
                    <?php $is_buyback = $this->uri->segment(2);?>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if($is_buyback != 'buyback'){ ?>
                            <li>
                                <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>service_center/search" onsubmit="return checkStringLength()">
                                    <div class="form-group">
                                        <input style="width:118%" type="text" class="form-control pull-right" placeholder="Search Booking ID or Mobile" name="searched_text" id="searched_text">
                                    </div>
                                    <!--                      <button type="submit" class="btn btn-default">Submit</button>-->
                                </form>
                            </li>
                        <?php } else{ ?>
                            <li>
                                <a href="javascript:void(0)" style="width:110%;margin-top: -9px;">
                           
                                <input type="text" class="form-control" placeholder="Search Order/Tracking ID ..." onkeydown="search_order_id(this)" 
                                       style=" border-radius:25px 25px 25px 25px">
                            
                            </a>
                            </li>
                       <?php  } ?>
<!--                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('service_center_name'); ?> <b class="caret"></b></a>
                        </li>-->

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i ></i> <?php echo $this->session->userdata('service_center_name'); ?> <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="<?php echo base_url()?>employee/service_centers/show_vendor_details">
                                    <i ></i> Profile <i class="fa fa-caret-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>
<!--                        <li><div class="dropdown" style="float:right;margin: 15px 14px 0px 0px;">
                                    <a class=" dropdown-toggle fa fa-bell" id="notification_holder"  data-toggle="dropdown" onclick="get_notifications(<?php echo $this->session->userdata('service_center_id'); ?>,'vendor')"></a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="notification_holder" id="notification_container" style="padding-top: 0px;margin-top: 32px;border: 1px solid #2c9d9c;"> 
                                    <center><img id="loader_gif_escalation" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                                    </ul>
  </div></li>-->
                        <li><a href="<?php echo base_url()?>employee/service_centers/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
        <style type="text/css">
        </style>
    </body>
</html>

<script>
    get_defective_parts_count();
    $(document).ready(function(){
        
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).parent().siblings().removeClass('open');
            $(this).parent().toggleClass('open');
        });
                    
                    
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/get_contact_us_page',
            success: function (data) {
                    $("#contactUsModalData").html(data);   
            }
        });
        
        get_notification_details();
    });
    
    function search_order_id(ele){
        if(event.keyCode === 13 && ele.value !== '') {

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/service_centers/search_for_buyback',
                data: {search:ele.value},
                success: function (response) {
                 console.log(response);
                 $(".right_col").html(response);

                }
            });
        }
    }


    function showConfirmDialougeBox(url){
        swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function(){
                window.location.href = url;
            });
    }

    function checkStringLength() {
            var searched_text = $("#searched_text").val();
            var regex = new RegExp("^[a-zA-Z0-9- ]+$");
            if(regex.test(searched_text)){
                if(searched_text.length >= 9){
                    return true;
                }else{
                    alert("Enter Atleast 8 Character");
                    return false;
                }
            }else{
                alert("Special character not allowed");
                return false;
            }

    }
    
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
    
    function get_notification_details(){
        
        $.ajax({
            method:'GET',
            url:'<?php echo base_url();?>employee/inventory/get_sf_notification_data',
            success:function(data){
                var obj = JSON.parse(data);
                $('#brackets_count').html(obj.brackets);
                $('#inventory_count').html(obj.inventory);
            }
        });
    }
    

     <?php if($this->session->userdata('is_sf') === '1'){ ?>

    function get_defective_parts_count(){
       $.ajax({
            method:'GET',
            url:'<?php echo base_url();?>employee/service_centers/get_defective_parts_count',
            success:function(data){
                var obj = JSON.parse(data);
                $('.defectivecount').html(obj.count);
            }
        });
    }
     <?php } ?>


</script>
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
    .marginBottom-0 {margin-bottom:0;}
    .dropdown-submenu{position:relative;}
    .dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
    .dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
    .dropdown-submenu:hover>a:after{border-left-color:#555;}
    .dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}
</style>
