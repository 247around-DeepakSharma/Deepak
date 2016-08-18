<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>
         247around Partner Portal
      </title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
      <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
      <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
      <script src="<?php echo base_url()?>js/jquery.js"></script>
      <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
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
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bookings <span class="caret"></span></a>
                     <ul class="dropdown-menu">
                         <li><a href="<?php echo base_url();?>partner/pending_queries">Pending Queries</a></li>
                         <li role="separator" class="divider"></li>
                        <li><a href="<?php echo base_url();?>partner/pending_booking">Pending Bookings</a></li>
                         <li role="separator" class="divider"></li>
                        <li><a href="<?php echo base_url();?>partner/closed_booking/Completed">Completed Bookings</a></li>
                         <li role="separator" class="divider"></li>
                        <li><a href="<?php echo base_url();?>partner/closed_booking/Cancelled">Cancelled Bookings</a></li>
                       
                     </ul>
                  </li>
                   <li>
                     <a data-toggle="modal" data-target="#myModal" style="cursor: pointer;">Add Booking</a>
                  </li>
               </ul>
               <ul class="nav navbar-nav navbar-right">
                 
                  <li>
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('partner_name'); ?> <b class="caret"></b></a>
                  </li>
                  <li><a href="<?php echo base_url()?>employee/partner/logout"><i class="fa fa-fw fa-power-off"></i></a></li>
               </ul>
            </div>
            <!-- /.navbar-collapse -->
         </div>
         <!-- /.container-fluid -->
      </nav>
      <style type="text/css">
      </style>
       <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Please Enter Mobile Number</h4>
        </div>
        <div class="modal-body">
           <form name="myForm" class="form-horizontal" id ="booking_form" action="#"  method="POST" enctype="multipart/form-data">
           <div class="form-group">
               <label for="customer phone number" class="col-md-4">Customer Phone Number</label>
               <div class="col-md-6">
                   <input type="text" class="form-control"  id="customer_phone_number" name="phone_number" value = "" required>
               </div>
              
           </div>
           </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" style="background-color:#2C9D9C; border-color: #2C9D9C; color: #fff;" onclick="call_booking_form()">Add Booking</button> 
          <button type="button" class="btn btn-default" dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
   </body>
</html>
<script type="text/javascript">
   function call_booking_form(){
   
   var phone_number  = $("#customer_phone_number").val();
    window.location.href = '<?php echo base_url() ?>partner/booking_form/'+ phone_number;
   
}
</script>