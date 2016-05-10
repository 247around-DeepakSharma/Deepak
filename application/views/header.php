<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Test </title>
      <!-- Bootstrap Core CSS -->
      <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
      <!-- Custom CSS -->
      <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">

      <!-- Custom Fonts -->
      <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <script src="<?php echo base_url()?>js/jquery.js"></script>
      <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
      

 
      <link href="<?php echo base_url()?>css/select2.css" rel="stylesheet" />
      <link href="<?php echo base_url()?>css/select2-bootstrap.css" rel="stylesheet" />
      

   </head>
   <body>
      <div id="wrapper">
      <!-- Navigation -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
         <!-- Brand and toggle get grouped for better mobile display -->
         <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo base_url()?>">Hello </a>
         </div>
         <ul class="nav navbar-right top-nav">
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Hello <b class="caret"></b></a>
               <ul class="dropdown-menu">
                  <li>
                     <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                  </li>
                  <li>
                     <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                  </li>
                  <li>
                     <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="<?php echo base_url()?>admin/logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                  </li>
               </ul>
            </li>
         </ul>
         <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
         <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
               <li class="active">
                  <a href="<?php echo base_url()?>"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
               </li>
               <li>
                  <a href="<?php echo base_url()?>form/services"><i class="fa fa-fw fa-search"></i> All Services</a>
               </li>
               <li>
                  <a href="<?php echo base_url()?>form/user"><i class="fa fa-fw fa-edit"></i> All User Profile</a>
            </ul>
         </div>
         <!-- /.navbar-collapse -->
      </nav>