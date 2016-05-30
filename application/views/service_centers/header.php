<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>
         Service Center
      </title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
      <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
      <script src="<?php echo base_url()?>js/jquery.js"></script>
      <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
      <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
      <style type="text/css">
         .navbar{
         min-height: 80px;
         }
      </style>
   </head>
   <body>
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
               <a class="navbar-brand" href="#">
               <img alt="Brand" src="<?php echo base_url()?>images/logo.jpg">
               </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
               <ul class="nav navbar-nav">
                  <li class="dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Booking <span class="caret"></span></a>
                     <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url();?>service_center/pending_booking">Pending Booking</a></li>
                        <!--<li role="separator" class="divider"></li>-->
                     </ul>
                  </li>
               </ul>
               <ul class="nav navbar-nav navbar-right">
                  <li style="margin-top: 11px;">
                     <!-- <form class="navbar-form navbar-left" role="search">-->
                     <div class="form-group">
                        <input type="text" class="form-control pull-right" placeholder="Search">
                     </div>
                     <!-- <button type="submit" class="btn btn-default">Submit</button>
                        </form>-->
                  </li>
                  <li>
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('user_name'); ?> <b class="caret"></b></a>
                  </li>
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