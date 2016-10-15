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
      <script src="<?php echo base_url()?>js/partner.js"></script>
       <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <link href="<?php echo base_url()?>css/style.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
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
                        <li>
                            <a href="<?php echo base_url(); ?>partner/get_user_form">Search User</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bookings <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                         <!-- <li><a href="<?php echo base_url();?>partner/pending_queries">Pending Queries</a></li>
                                <li role="separator" class="divider"></li> -->
                        <li><a href="<?php echo base_url();?>partner/pending_booking">Pending Bookings</a></li>
                                <li role="separator" class="divider"></li>
                        <li><a href="<?php echo base_url();?>partner/closed_booking/Completed">Completed Bookings</a></li>
                                <li role="separator" class="divider"></li>
                        <li><a href="<?php echo base_url();?>partner/closed_booking/Cancelled">Cancelled Bookings</a></li>

                            </ul>
                        </li>

                        <li>
                     <a href="<?php echo base_url();?>partner/invoices_details">Invoice</a>
                        </li>
                        <li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Add Booking</a>
					<ul class="dropdown-menu" role="menu">
						<li>
                          <form method="POST" class="navbar-form navbar-left" action ="<?php echo base_url(); ?>/partner/booking_form" role="add booking">
                              <div class="form-group">
                              <input type="text" id="phone_number" class="form-control" name="phone_number" placeholder="Phone Number">
                              </div>
                              <input type="submit" value="Add Booking" onclick="return submit_button()" class="btn btn-default btn-block submitBtn" />
                          </form>
                      	</li>
					</ul>
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
    function submit_button(){
        var phone = $("#phone_number").val();

            if(phone.length!=10){
                return false;

            }
            intRegex = /^[7-9]{1}[0-9]{9}$/;
            if(intRegex.test(phone))
            {
                return true;
            } else{
                return false;
            }


    }
</script>
