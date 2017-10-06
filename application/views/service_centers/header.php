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
        <!-- Sweet Alert Css -->
        <link href="<?php echo base_url() ?>css/sweetalert.css" rel="stylesheet">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <link href="<?php echo base_url()?>css/select2.min.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <!-- sweet Alert JS -->
        <script src="<?php echo base_url();?>js/sweetalert.min.js"></script>
        
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
        </style>
    </head>
    <body>
        <nav class="navbar navbar-custom">
            <!-- Modal -->
            <div id="contactusmodal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around Call Center - 9555000247</h4>
                        </div>       
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Calls & CRM Issues:</h4>
                        </div>
                       
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="container">
                                                  <h5><b>Mr. Devendra</b></h5> 
                                                  <p>Back Office Closure Champion</p>
                                                  <p>Delhi Office</p>
                                                  <p>8130572244 <span> <strong>|</strong> English and Hindi</span></p>
                                                  <p>booking@247around.com</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="container">
                                                  <h5><b>Mrs. Ranju</b></h5> 
                                                  <p>Back Office Closure</p>
                                                  <p>Delhi Office</p>
                                                  <p>8130572244 <span> <strong>|</strong> Bengali and Hindi</span></p>
                                                  <p>booking@247around.com</p>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Regional Contacts:</h4>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="long-card">
                                                <div class="container">
                                                  <h5><b>Mr. K Suresh</b></h5> 
                                                  <p>Escalation South India</p>
                                                  <p>Regional Service Head South India</p>
                                                  <p>Chennai Office <span>9840492171</span></p>
                                                  <p style="width:250px;word-wrap:break-word;">English, Tamil, Malayalam, Telugu and Kannada</p>
                                                  <p>suresh@247around.com</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="long-card">
                                                <div class="container">
                                                  <h5><b>Mr. Rajendra Oza</b></h5> 
                                                  <p>Escalation West India</p>
                                                  <p>Regional Service Head West India</p>
                                                  <p>Mumbai Office <span>9223274602</span></p>
                                                  <p>English, Hindi and Marathi</p>
                                                  <p>oza@247around.com</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="long-card">
                                                <div class="container">
                                                  <h5><b>Mr. Nilanjan Das</b></h5> 
                                                  <p>Escalation East India</p>
                                                  <p>Regional Service Head East India</p>
                                                  <p>Kolkata Office <span>9051159966</span></p>
                                                  <p> English, Hindi and Bengali</p>
                                                  <p>nilanjan@247around.com</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            <div class="long-card" style="margin-top:20px;">
                                                <div class="container">
                                                  <h5><b>Mr. Nitin Malhotra</b></h5> 
                                                  <p>Escalation North India</p>
                                                  <p>Director & CEO</p>
                                                  <p>Delhi Office 9810872244</p>
                                                  <p>English, Hindi and Punjabi</p>
                                                  <p>nits@247around.com</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Escalation Resolution Contact:</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="width:40%;">
                                        <div class="container">
                                            <h5><b>Mr. Jaidev Sharma</b></h5> 
                                            <p>Delhi Office</p>
                                            <p>9582528116 <span> <strong>|</strong> English and Hindi</span></p>
                                            <p>jaidevs@247around.com</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">TV Bracket Ordering:</h4>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="width:40%;">
                                        <div class="container">
                                            <h5><b>Ms. Vijaya</b></h5> 
                                            <p>Back Office Closure Champion</p>
                                            <p>Delhi Office</p>
                                            <p>0120-4540185 <span> <strong>|</strong> English and Hindi</span></p>
                                            <p>vijaya@247around.com,booking@247around.com</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">247around Invoices Related:</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card" style="width:40%;">
                                        <div class="container">
                                            <h5><b>Mr. Adil Akhtar</b></h5> 
                                            <p>Back Office Closure Champion</p>
                                            <p>Delhi Office</p>
                                            <p>9716960840 <span> <strong>|</strong> English and Hindi</span></p>
                                            <p>adila@247around.com</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
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
                        
                        <?php if($this->session->userdata('service_center_id') == '10'){ ?>
                        <li>
                            <a href="<?php echo base_url() ;?>employee/service_centers/show_brackets_list">Brackets</a>
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i ></i> Invoices <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo base_url() ?>service_center/invoices_details" ><i class="fa fa-fw fa-desktop"></i> <strong> Invoice Summary</strong></a>
                                </li>
                                <?php if($this->session->userdata('is_sf') === '1'){ ?>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="<?php echo base_url() ?>employee/service_centers/download_sf_charges_excel" ><i class="fa fa-fw fa-desktop"></i> <strong> Charges List</strong></a>
                                </li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php if($this->session->userdata('is_cp') === '1'){ ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Buyback <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url();?>service_center/buyback/bb_order_details">Buyback Order Details</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo base_url();?>service_center/buyback/show_bb_price_list">Buyback Charges List</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url();?>service_center/gst_details"  >GST</a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#contactusmodal"><i class="fa fa-phone"></i>&nbsp;Contact Us&nbsp;</a>
                        </li>
                        
                    </ul>
                    <?php $is_buyback = $this->uri->segment(2);?>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if($is_buyback != 'buyback'){ ?>
                            <li>
                                <form method="POST" class="navbar-form navbar-left" role="search" action="<?php echo base_url(); ?>service_center/search">
                                    <div class="form-group">
                                        <input style="width:118%" type="text" class="form-control pull-right" placeholder="Search Booking ID or Mobile" name="searched_text">
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

</script>
