<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            247around Partner Portal
        </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url() ?>css/sb-admin.css" rel="stylesheet">
        <script src="<?php echo base_url() ?>js/jquery.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <script src="<?php echo base_url() ?>js/bootstrap.min.js"></script>
        <script src="<?php echo base_url() ?>js/partner.js"></script>
        <link href="<?php echo base_url() ?>css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url() ?>css/style.css" rel="stylesheet" />
        <script src="<?php echo base_url(); ?>js/select2.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

        <script src="<?php echo base_url()?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url()?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>

        <script src="<?php echo base_url();?>js/jquery.loading.js"></script>
        <style type="text/css">
            .navbar{
                min-height: 80px;
            }
            #datepicker{cursor:pointer;}
            .card,.long-card {
                width: 33%;
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
                border-radius: 5px;
                display: inline-block;
            }
            .card:hover {
                box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            }
            .long-card:first-child {
                margin-top: 10px;
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
            .submitBtn{
                margin-top:5px;
                background:#2C9D9C;
                color:#fff;
            }

            .submitBtn:hover{
                background:#DADADA;
            }
            .marginBottom-0 {margin-bottom:0;}
            .dropdown-submenu{position:relative;}
            .dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
            .dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
            .dropdown-submenu:hover>a:after{border-left-color:#555;}
            .dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}
        </style>
    </head>
    <body>
        <?php
        $CI = & get_instance();
        $CI->load->library('email');
        $userdata = $CI->session->all_userdata();
        $partner_name = $this->session->userdata('partner_name');
        ?>
        <div id="contactUsModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div id="contactUsModalData"></div>
                   
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>    

        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar" style="border: 1px solid #fff;"></span>
                        <span class="icon-bar" style="border: 1px solid #fff;"></span>
                        <span class="icon-bar" style="border: 1px solid #fff;"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url() ?>partner/home">
                        <img alt="Brand" src="<?php echo base_url() ?>images/logo.jpg">
                    </a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?php echo base_url(); ?>partner/get_user_form">Advanced Search</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bookings <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                         <!-- <li><a href="<?php echo base_url(); ?>partner/pending_queries">Pending Queries</a></li>
                                <li role="separator" class="divider"></li> -->
                                <li><a href="<?php echo base_url(); ?>partner/home">Pending Bookings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>partner/closed_booking/Completed">Completed Bookings</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>partner/closed_booking/Cancelled">Cancelled Bookings</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown dropdown-submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Download Section</a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo base_url(); ?>partner/download_partner_summary/<?php echo $this->session->userdata('partner_id'); ?>">Summary Report</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>partner/serviceability_list">Serviceability List</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php echo base_url(); ?>partner/download_sf_list_excel">Service Center List</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Spare Bookings <span class="caret"></span></a>
                            <ul class="dropdown-menu">


                                <li><a href="<?php echo base_url(); ?>partner/get_spare_parts_booking">Pending Spares On <?php echo $partner_name ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>partner/get_shipped_parts_list">Shipped Spares by <?php echo $partner_name ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>partner/get_waiting_defective_parts">Shipped Spares by SF</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>partner/get_approved_defective_parts_booking">Received Spares by <?php echo $partner_name ?></a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?php echo base_url(); ?>partner/invoices_details">Invoice</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Add Booking</a>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <form method="POST" class="navbar-form navbar-left" action ="<?php echo base_url(); ?>partner/search" role="add booking">
                                        <div class="form-group">
                                            <input type="text" id="phone_number" class="form-control" name="searched_text" placeholder="Phone Number">
                                        </div>
                                        <input type="submit" value="Add Booking" onclick="return submit_button()" class="btn btn-default btn-block submitBtn" />
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#contactUsModal"><i class="fa fa-phone"></i>&nbsp;Contact Us&nbsp;</a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">

                        <li>
                            <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>partner/search" onsubmit="return checkStringLength()">
                                <div class="form-group">
                                    <input style="width:125%" type="text" class="form-control pull-right" id="searched_text" placeholder="Search Booking ID or Mobile" 
                                           name="searched_text" >
                                </div> 
                            </form>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $this->session->userdata('partner_name'); ?><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>employee/partner/show_partner_edit_details_form">Edit Details</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url(); ?>employee/partner/reset_partner_password">Reset Password</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url() ?>employee/partner/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
        <!-- Trigger the modal with a button -->


        <!-- Modal -->
<script type="text/javascript">
    
    (function($){
        $(document).ready(function(){
            $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                $(this).parent().siblings().removeClass('open');
                $(this).parent().toggleClass('open');
            });
        });
                
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_contact_us_page/<?php echo $this->session->userdata('partner_id') ?>',
            success: function (data) {
                $("#contactUsModalData").html(data);   
            }
        });
    })(jQuery);
    
    function submit_button() {
        var phone = $("#phone_number").val();

        if (phone.length !== 10) {
            return false;

        }
        intRegex = /^[7-9]{1}[0-9]{9}$/;
        if (intRegex.test(phone))
        {
            return true;
        } else {
            return false;
        }
    }
    
    function checkStringLength() {
        var searched_text = $("#searched_text").val();
        if (searched_text.length < 9) {
            alert("Enter Atleast 8 Character For Booking ID");
            return false;
        }

    }
</script>
    </body>
</html>