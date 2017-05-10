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
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <style type="text/css">
            .navbar{
                min-height: 80px;
            }
        </style>
    </head>
    <body>
        <?php
        $CI = & get_instance();
        $CI->load->library('email');
        $userdata = $CI->session->all_userdata();
        $partner_name = $this->session->userdata('partner_name');
        ?>
        <div id="contactussfmodal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">247around Point of Contacts:</h4>
                    </div>

                    <div class="modal-header">
                        <h4 class="modal-title"> First Escalation Point</h4>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <li>Vikas Singh - escalations@247around.com – 9910043586</li>

                        </ul>
                    </div>
                    <div class="modal-header">
                        <h4 class="modal-title"> Second Escalation Point</h4>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <li>South India – Mr. K Suresh – Regional Service Head South India – Based in Chennai Office – 9840492171 (English, Tamil, Malayalam, Telugu and Kannada) - suresh@247around.com</li>
                            <li>West India – Mr. Rajendra Oza – Regional Service Head West India – Based in Mumbai Office – 9223274602 (English, Hindi and Marathi) – oza@247around.com</li>
                            <li>East India – Mr. Nilanjan Das – Regional Service Head East India – Based in Kolkata Office – 9051159966 (English, Hindi and Bengali) – nilanjan@247around.com</li>
                            <li>North India – Mr. Nitin Malhotra – Director & CEO – Based in Delhi Office – 9810872244 (English, Hindi and Punjabi) – nits@247around.com</li>
                        </ul>
                    </div>
                    <div class="modal-header">
                        <h4 class="modal-title">Technical – CRM Related</h4>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <li>Anuj Aggarwal – Director & CTO -  anuj@247around.com - 8826423424</li>
                        </ul>
                    </div>
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
                                <li><a href="<?php echo base_url(); ?>partner/download_partner_summary/<?php echo $this->session->userdata('partner_id'); ?>">Download Summary Report</a></li>
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
                            <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#contactussfmodal"><i class="fa fa-phone"></i>&nbsp;Contact Us&nbsp;</a>
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

    </body>
</html>
<style type="text/css">
    .submitBtn{
        margin-top:5px;
        background:#2C9D9C;
        color:#fff;
    }

    .submitBtn:hover{
        background:#DADADA;
    }
</style>
<script type="text/javascript">
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
    function checkStringLength(){
         var searched_text = $("#searched_text").val();
         if (searched_text.length < 9) {
             alert("Enter Atleast 8 Character For Booking ID");
            return false;
        }
        
    }
</script>
