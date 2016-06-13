<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <meta name="format-detection" content="telephone=no">

      <title>Telecaller</title>
      <!-- Bootstrap Core CSS -->
      <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
      <!-- Custom CSS -->
      <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">

      <!-- Custom Fonts -->
      <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <script src="<?php echo base_url()?>js/jquery.js"></script>

      <!-- Bootstrap Core JavaScript -->
      <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
      <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
      <link href="<?php echo base_url()?>css/style.css" rel="stylesheet" />
      <script src="<?php echo base_url();?>js/select2.min.js"></script>
   </head>

   <body>
      <div id="wrapper">
      <!-- Navigation -->
      <!-- Navigation -->
      <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;background-color: lightgrey;">
         <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html"></a>
         </div>
         <!-- /.navbar-header -->

         <ul class="nav navbar-top-links navbar-left">
            <li>
               <a href="<?php echo base_url()?>employee/user"><i class="fa fa-fw fa-edit"></i>Find User</a>
            </li>
            <li class="dropdown ">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Queries  <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu  ">
                  <li >
		      <a  href="<?php echo base_url() ?>employee/booking/view_pending_queries"><i class="fa fa-fw fa-desktop"></i> <strong> Pending Queries</a></strong>
                  </li>
                  <li class="divider"></li>
                  <li <?php if($this->uri->uri_string()=='employee/signup_message'){ echo 'class="active"';}?>>
                     <a href="<?php echo base_url() ?>employee/booking/view_cancelled_queries"><i class="fa fa-fw fa-desktop"></i> <strong> Cancelled Queries</strong></a>
                  </li>
               </ul>
               <!-- /.dropdown-messages -->
            </li>
            <!-- /.dropdown -->

            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Bookings <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu ">
                  <li>
                     <a href="<?php echo base_url() ?>employee/booking/view"><i class="fa fa-fw fa-desktop"></i> <strong> View Pending Booking</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url() ?>employee/booking/viewcompletedbooking"><i class="fa fa-fw fa-desktop"></i> <strong>View Completed Booking</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url() ?>employee/booking/viewcancelledbooking"><i class="fa fa-fw fa-desktop"></i> <strong>View Cancelled Booking</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url() ?>employee/booking/get_assign_booking_form"><i class="fa fa-fw fa-desktop"></i> <strong>Assign Vendor</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/vendor/get_reassign_vendor_form"><i class="fa fa-fw fa-desktop"></i> <strong> Re-assign Vendor</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/new_booking/review_bookings"><i class="fa fa-fw fa-desktop"></i> <strong> Review Bookings</strong></a>
                  </li>

<!--
                  
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url() ?>employee/bookingjobcard"><i class="fa fa-fw fa-desktop"></i> <strong>Prepare Job Card </strong></a>
                  </li>
                  
-->
               </ul>
               <!-- /.dropdown-tasks -->
            </li>
            <!-- /.dropdown -->

            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Partners <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu ">
                  <li >
                     <a href="<?php echo base_url() ?>employee/bookings_excel"><i class="fa fa-fw fa-desktop"></i> <strong> Upload Snapdeal Products - Delivered</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url() ?>employee/bookings_excel/upload_shipped_products_excel"><i class="fa fa-fw fa-desktop"></i> <strong> Upload Snapdeal Products - Shipped</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url()?>employee/partner_booking"><i class="fa fa-fw fa-desktop "></i> <strong> Upload Paytm Booking</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url() ?>employee/bookings_excel/get_unassigned_bookings"><i class="fa fa-fw fa-desktop"></i> <strong> Pending Leads</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url() ?>employee/bookings_excel/get_all_sd_bookings"><i class="fa fa-fw fa-desktop"></i> <strong> All Leads</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li >
                     <a href="<?php echo base_url() ?>employee/invoice/invoice_partner_view"><i class="fa fa-fw fa-desktop"></i> <strong> Partner Invoices</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/invoice/show_all_transactions/partner"><i class="fa fa-fw fa-desktop "></i> <strong>Show All Transactions</strong></a>
                  </li>
               </ul>
               <!-- /.dropdown-alerts -->
            </li>
            <!-- /.dropdown -->

            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Service Centres <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu">
                  <li>
                     <a href="<?php echo base_url()?>employee/vendor/viewvendor" ><i class="fa fa-fw fa-desktop"></i> <strong> View Service Centres</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/vendor/vendor_availability_form"><i class="fa fa-fw fa-desktop "></i> <strong> Search Service Centre</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/vendor/get_pincode_excel_upload_form"><i class="fa fa-fw fa-desktop"></i> <strong> Upload Pincode Mapping Excel</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/vendor/get_broadcast_mail_to_vendors_form"><i class="fa fa-fw fa-desktop"></i> <strong> Send Broadcast Email</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/invoice"><i class="fa fa-fw fa-desktop "></i> <strong> Show Invoices</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/invoice/get_add_new_transaction"><i class="fa fa-fw fa-desktop "></i> <strong>Add New Transaction</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/invoice/show_all_transactions/vendor"><i class="fa fa-fw fa-desktop "></i> <strong>Show All Transactions</strong></a>
                  </li>
               </ul>
               <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->


            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Appliances <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu">
                  <li>
                     <a href="<?php echo base_url()?>employee/" ><i class="fa fa-fw fa-desktop"></i> <strong> List Brands</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url() ?>employee/booking/get_add_new_brand_form"><i class="fa fa-fw fa-desktop"></i> <strong> Add New Brand</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/service_centre_charges/show_pricing_tables" ><i class="fa fa-fw fa-desktop"></i> <strong> Show Prices</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/service_centre_charges/upload_excel_form"><i class="fa fa-fw fa-inr "></i> <strong> Upload Service Charges / Taxes Excel</strong></a>
                  </li>

               </ul>
               <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->

            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Reports <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu">
                   <li>
                     <a href="<?php echo base_url()?>employee/vendor/vendor_performance_view"><i class="fa fa-fw fa-desktop "></i> <strong>Vendor Performance</strong></a>
                  </li>

                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/user/get_user_count_view" ><i class="fa fa-fw fa-desktop"></i> <strong> Bookings</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/user/user_count" ><i class="fa fa-fw fa-desktop"></i> <strong> Users</strong></a>
                  </li>
               </ul>
               <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->

            <li class="dropdown">
               <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <i class="fa fa-fw fa-arrows-v"></i> Others <i class="fa fa-caret-down"></i>
               </a>
               <ul class="dropdown-menu">
                   <li>
                     <a href="<?php echo base_url()?>employee/"><i class="fa fa-fw fa-desktop "></i> <strong>Blogs</strong></a>
                  </li>

                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/partner_booking/get_upload_partners_cancelled_booking" ><i class="fa fa-fw fa-desktop"></i> <strong>Upload Quikr Excel</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/" ><i class="fa fa-fw fa-desktop"></i> <strong>Upload Snapdeal Excel</strong></a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>employee/" ><i class="fa fa-fw fa-desktop"></i> <strong>Upload Paytm Excel</strong></a>
                  </li>
               </ul>
               <!-- /.dropdown-user -->
            </li>
         </ul>

         <ul class="nav navbar-top-links navbar-right">
            <li>
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('employee_id'); ?> <b class="caret"></b></a>
            </li>
            <li><a href="<?php echo base_url()?>employee/login/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
         </ul>
         <!-- /.navbar-top-links -->
         <!-- /.navbar-static-side -->
      </nav>
