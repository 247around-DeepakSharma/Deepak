<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            Welcome to 247around CRM
        </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url() ?>css/font-awesome/bootstrap.css" rel="stylesheet">
        <!-- custom css -->
        <link href="<?php echo base_url() ?>css/custom.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="<?php echo base_url() ?>css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo base_url() ?>js/jquery.js"></script>
        <script src="<?php echo base_url() ?>js/bootstrap.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <link href="<?php echo base_url() ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url() ?>css/sb-admin.css" rel="stylesheet">
        <style>
            body{
                text-align:center;
                background-color:#f2f2f2;
                float:none;
            }
            .pageWrap{
                width: 350px;
                text-align: center;
                height: auto;
                float: none;
                background-color: #E6E6E6;
                padding: 20px;
                margin-top:150px;
            }
        </style>
    </head>
</html>
<body>
    <div class="welcome_text">
        <nav class="navbar navbar-inverse nab-border">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php if($is_saas){ ?>
                    <a class="navbar-brand" href="#"><img src="<?php echo base_url()?>images/small_logo.png" style="display: inline;height: 100%;"></a>
                    <?php } 
                    else {
                    ?>
                     <a class="navbar-brand" href="#">247Around</a>
                    <?php } ?>
                </div>
                <!--                <div class="collapse navbar-collapse" id="myNavbar">
                                    <ul class="nav navbar-nav navbar-right">
                                        <li class="active"><a href="#"> Login</a></li>
                                    </ul>
                                </div>-->
            </div>
        </nav>
        <div class="container-fluid background_header ">
            <div class="row " style="margin-bottom: 40px; margin-top: 40px;">
                <div class="col-md-6 custom_top" style="text-align: center; ;color: #fff;" >
                    <p >Bringing Quality, Convenience & Reliability To Home and Office Appliance Repair</p>
                </div>
                <div class="col-md-5 login_tab"  style="background-color: #2C9D9C; margin-right: 20px;">
                    <div class="tab-content"  style="padding:20px; ">
                        <img src="<?php echo base_url() ?>images/logo.jpg" style="display: inline;">
                        <p style="display: inline; color: #fff;margin-left:33px;font-size: 22px; ">Welcome to  247around CRM</p>
                        <div class="tab-pane fade in active">
                            <?php
                            if ($this->session->userdata('error')) {
                                echo '<div class="col-sm-offset-1 col-sm-10 col-sm-offset-2 alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>' . $this->session->userdata('error') . '</strong>
                                    </div>';
                            }
                            ?>
                            <form class="form-horizontal" action="<?php echo base_url(); ?>employee/login/partner_login" style="margin-top:45px;" method="post" id="login_form">
                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-10 col-sm-offset-1">
                                        <div class = "input-group">
                                            <span class = "input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                            <input type = "text" class = "form-control" name="user_name" placeholder = "User Name">
                                        </div>
                                    </div>
                                    &nbsp;<span id="errmsg"></span>

                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-10 col-sm-offset-1">
                                        <div class = "input-group">
                                            <span class = "input-group-addon"><i class="fa fa-key" aria-hidden="true"></i></span>
                                            <input type = "password" class = "form-control" name="password" placeholder = "Password">
                                        </div>
                                    </div>
                                    &nbsp;<span id="errmsg1"></span>
                                </div>
                                <!--<a href="#" class="pull-right" style="color: #fff;margin-right: 77px;">Forgot password</a>-->
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="login_btn">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end md-4 -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid-->
        </div>
    </div>
    <style type="text/css">
        .navbar-brand{
            line-height: 49px;
        }
    </style>
    <div class="partner">
        <script type="text/javascript" src="<?php echo base_url() ?>js/jssor.slider.min.js"></script>
        <script>
            jssor_1_slider_init = function () {

                var jssor_1_options = {
                    $AutoPlay: true,
                    $Idle: 0,
                    $AutoPlaySteps: 4,
                    $SlideDuration: 3000,
                    $SlideEasing: $Jease$.$Linear,
                    $PauseOnHover: 100,
                    $SlideWidth: 300,
                    $Cols: 4

                };

                var jssor_1_slider = new $JssorSlider$("jssor_2", jssor_1_options);


                function ScaleSlider() {
                    var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
                    if (refSize) {
                        refSize = Math.min(refSize, 809);
                        jssor_1_slider.$ScaleWidth(refSize);
                    }
                    else {
                        window.setTimeout(ScaleSlider, 30);
                    }
                }
                ScaleSlider();
                $Jssor$.$AddEvent(window, "load", ScaleSlider);
                $Jssor$.$AddEvent(window, "resize", ScaleSlider);
                $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);

            };
        </script>
        <div class="container " style=" background: #F9F9F9!important ;margin:0px auto; width: 100%;">
            <div class="row">
                <div class="col-md-3 col-sm-6 text-center" style="margin-bottom:40px" >
                    <h2 style="font-family:verdana ; margin-top: 60px  ;font-weight:bold; margin-left:25px">Our Partners</h2>
                </div>
                <div class="col-md-7 col-sm-6">
                    <div id="jssor_2" style="position: relative;margin: 4% auto  ;  top: 0px; left: 0px; width: 980px; height: 100px; overflow: hidden; visibility: hidden;">
                        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
                            <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
                            <div style="position:absolute;display:block;background:url('../../images/loadring.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
                        </div>
                        <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 980px; height: 100px; overflow: hidden;">
                            <?php foreach ($partner_logo as $value) { $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['partner_logo'];?>
                                <div style="display: none;">
                                    <img data-u="images" src="<?php echo $src; ?>" alt="<?php echo $value['alt_text']; ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                        <a href="http://www.jssor.com" style="display:none">Slideshow Maker</a>
                    </div>
                    <script>
                        jssor_1_slider_init();
                    </script>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <div class="container custom_container">
        <div class="row">
            <div class="col-md-12" style="text-align: center;">
                <h3>A little bit about 247Around</h3>
            </div>
            <div class="col-md-12">
                <p class="description">247around is PAN India Multi-brand Appliance Care Platform. For Complete Peace Of Mind,
                    All Jobs That We Do Are Covered Under 1-3 Months Of Warranty. Call Us And Based On Your Pincode,
                    Our Nearest Service Center Engineer Will Be Assigned For The Job. Transparent Prices, Quality Repair.
                    We Cover Appliances Like Television, Washing Machine, Refrigerator, Microwave, Air Conditioner, Mixer,
                    Chimney, Geyser etc. We Provide Services Like Repair, Installation, Uninstallation, AMC, Extended Warranty and others.
                </p>
            </div>
            <div class="col-md-12" style="margin-bottom: 40px;">
                <hr/>
            </div>
            <div class="col-md-12" style="text-align: center;">
                <h3>Installation, Service & Repair</h3>
                 <!--<p style="color: #C2C2C2; font-size: 17px;font-weight: 600;">Short Description of Services</p>-->
            </div>

            <div class="col-md-12" style="margin-top: 50px;">
                <div class="row">
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-tv.png"/>
                            <span class="caption"><strong>Television</strong></span>
                        </div>
               <!--<span class="short_caption">Installation, Demo & Repair</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-refrigerator.png"/>
                            <span class="caption"><strong>Refrigerator</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-washing-mashine.png"/>
                            <span class="caption"><strong>Washing Machine</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-geyser.png"/>
                            <span class="caption"><strong>Geyser</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                </div>
            </div>

            <div class="col-md-12" style="margin-top: 50px;margin-bottom: 50px;">
                <div class="row">
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-ac.png"/>
                            <span class="caption"><strong>Air Conditioner</strong></span>
                        </div>
               <!--<span class="short_caption">Demo</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-microwave.png"/>
                            <span class="caption"><strong>Microwave</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-ro.png"/>
                            <span class="caption"><strong>Water purifier</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                    <div class="col-md-2 col-md-offset-1" style="text-align: center;">
                        <div class="item">
                            <img class="key_image" src="<?php echo base_url(); ?>images/repair-mixer.png"/>
                            <span class="caption"><strong>Mixer</strong></span>
                        </div>
              <!--<span class="short_caption">Demo</span>-->

                    </div>
                </div>
            </div><?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>

            <!-- end container -->
        </div>
    </div>

    <hr/>
    <div  style="background-color: #fff;">
        <div class="row">
            <div class="col-md-12" style="text-align: center;margin-top: 30px;margin-bottom: 30px;">
                Copyright@2015-<?php echo date("Y"); ?> Blackmelon Advance Technology Co. Pvt. Ltd., All rights reserved.
            </div>
        </div>

    </div>



</body>