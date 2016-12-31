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
      <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
      <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
      <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
      <script src="<?php echo base_url();?>js/select2.min.js"></script>
      <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
     <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
     
      <style type="text/css">
         .navbar{
         min-height: 80px;
         }
         #datepicker{cursor:pointer;}
      </style>
   </head>
   <body>
      <nav class="navbar navbar-custom">

            <!-- Modal -->
            <div id="contactusmodal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around Call Completion Contacts:</h4>
                        </div>
                        <div class="modal-body">
                            <ul>
                                <li>Mr. Devendra – Back Office Closure Champion – Delhi Office – 8130572244 (English and Hindi) - booking@247around.com</li>
                                <li>Mrs. Ranju - Back Office Closure – Delhi Office – 8130572244 (Bengali and Hindi) - booking@247around.com</li>
                            </ul>
                        </div>

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around Regional Contacts:</h4>
                        </div>
                        <div class="modal-body">
                            <ul>
                                <li>Escalation South India – Mr. K Suresh – Regional Service Head South India – Based in Chennai Office – 9840492171 (English, Tamil, Malayalam, Telugu and Kannada) - suresh@247around.com</li>
                                <li>Escalation West India – Mr. Rajendra Oza – Regional Service Head West India – Based in Mumbai Office – 9223274602 (English, Hindi and Marathi) – oza@247around.com</li>
                                <li>Escalation East India – Mr. Nilanjan Das – Regional Service Head East India – Based in Kolkata Office – 9051159966 (English, Hindi and Bengali) – nilanjan@247around.com</li>
                                <li>Escalation North India – Mr. Nitin Malhotra – Director & CEO – Based in Delhi Office – 9810872244 (English, Hindi and Punjabi) – nits@247around.com</li>
                            </ul>
                        </div>

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around TV Bracket Ordering:</h4>
                        </div>
                        <div class="modal-body">
                            <ul>
                                <li>Ms. Vijaya – Delhi Office – 0120-4540185 (English and Hindi) – vijaya@247around.com, booking@247around.com</li>
                            </ul>
                        </div>

                        <div class="modal-header">
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
                  
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Spare Parts <span class="caret"></span></a>
                     <ul class="dropdown-menu">
                         <li><a href="<?php echo base_url();?>service_center/get_defective_parts_booking">Defective Parts</a></li>
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
                            <a href="<?php echo base_url() ?>service_center/add_engineer" ><i class="fa fa-fw fa-desktop"></i> <strong> Add Engineer</strong></a>
                        </li>
                         <li role="separator" class="divider" style="height: 2px;"></li>
                         <li>
                            <a href="<?php echo base_url() ?>service_center/get_engineers" ><i class="fa fa-fw fa-desktop"></i> <strong> View Engineers</strong></a>
                        </li>
                    </ul>
                </li>

                 <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i ></i> Invoices <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo base_url() ?>service_center/invoices_details" ><i class="fa fa-fw fa-desktop"></i> <strong> Invoice Summary</strong></a>
                        </li>
                        <li role="separator" class="divider"></li>
                         <li>
                            <a href="<?php echo base_url() ?>employee/service_centers/download_sf_charges_excel" ><i class="fa fa-fw fa-desktop"></i> <strong> Charges List</strong></a>
                        </li>
                         
 
                    </ul>
                </li>
                  <li>
                       <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#contactusmodal"><i class="fa fa-phone"></i>&nbsp;Contact Us&nbsp;</a>
                  </li>
                  <li class="dropdown">
                    <a class="dropdown-toggle" href="<?php echo base_url()?>employee/service_centers/show_vendor_details">
                    <i ></i> Profile <i class="fa fa-caret-down"></i>
                    </a>
                </li>
               </ul>
               
               <ul class="nav navbar-nav navbar-right">
                  
                  <li>
                      <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>service_center/search">
                      <div class="form-group">
                          <input style="width:118%" type="text" class="form-control pull-right" placeholder="Search Booking ID or Mobile" name="searched_text">
                     </div> 
<!--                      <button type="submit" class="btn btn-default">Submit</button>-->
                        </form>
                  </li>
                  <li>
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="verifyby"><i class="fa fa-user"></i> <?php echo $this->session->userdata('service_center_name'); ?> <b class="caret"></b></a>
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