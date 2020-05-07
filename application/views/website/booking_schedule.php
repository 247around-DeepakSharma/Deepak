<!DOCTYPE html>
<html lang="en">
    <head>
        <title>247around - Appliance Repair Service At Your Doorstep, Best Quality</title>
        <meta name="description" content="Multi-Brand Appliance Installation, Service & Repair At Your Doorstep. Book Service In Just 1-click on Android App 247around or Call 9555000247.">
        <meta name="keywords" content="appliance installation, service, repair">
        <?php include_once("header-close.php") ?>
    </head>
    <body>
        <!-- facebook script for adding social plugin -->
        <?php include_once("website_assets/js/fb.js") ?>
        <?php include_once("body-open.php") ?>
        <?php include_once("body-navbar.php") ?>
        <div class="bg_home"><img src="<?php echo base_url(); ?>website_assets/images/home-background.png" ></div>
        <div class="wrapper" id="atf-home" style="height: 500px;">
            <div class="container">
                <div class="header-top"></div>
                <div class="row formBox formSection" style="background-color: rgba(10, 10, 10, 0); margin-top: 54px;">
                    <div class="row head-small">Appliance breakdown, don't let it stop your life !!!</div>
                    <div class="head-call visible-xs-block"><a href="tel:9555000247"><i class="fa fa-phone phone-style"></i>9555000247</a></div>
                    <div class="row header-home hidden-xs col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                        <h1 class="col-md-12  light">Bringing Quality, Convenience &amp; Reliability To Home and Office Appliance Repair</h1>
                    </div>
                    <!--          <div class="col-lg-6 col-md-8 col-lg-offset-3 col-md-offset-2 col-sm-12 col-xs-12">-->
                </div>
            </div>
        </div>
        <div class="wrapper downloads" >
            <div class="container" >
                <div class="row ">
                    <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url(); ?>process_schedule_booking/<?php echo $booking_details[0]['booking_id']; ?>"  method="POST" style="margin-top:60px;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="col-md-3"> Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_name" name="user_name" value = "<?php echo $booking_details[0]['name']; ?>" placeholder="" readonly required>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 27px;">
                                    <label for="name" class="col-md-3"> Address</label>
                                    <div class="col-md-6">
                                        <textarea type="text" class="form-control" id="customer_address" rows="3" name="booking_address" ><?php echo $booking_details[0]['booking_address']; ?></textarea>  
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="col-md-3"> Mobile</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_phone_number" name="booking_primary_contact_no" value = "<?php echo $booking_details[0]['booking_primary_contact_no']; ?>" >
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 27px;">
                                    <label for="name" class="col-md-3"> City</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_city" name="city" value = "<?php echo $booking_details[0]['city']; ?>" required> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="col-md-3"> Alternate No.</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_alternate_phone_number" name="booking_alternate_contact_no" value = "<?php echo $booking_details[0]['booking_alternate_contact_no']; ?>" >
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 27px;">
                                    <label for="name" class="col-md-3"> Pincode</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_pincode" name="booking_pincode" value = "<?php echo $booking_details[0]['booking_pincode']; ?>" required> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 15px;">
                                <div class="form-group">
                                    <label for="name" class="col-md-1"> Product</label>
                                    <div class="col-md-10">
                                        <input  type="text" class="form-control"  id="description" name="product_description"  value="<?php echo $appliance_details[0]['description']; ?>" readonly="readonly"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8" style="margin-top: 15px;">
                                <div class="form-group">
                                    <label for="name" class="col-md-2"> Charges</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" value="<?php echo $cost; ?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" style="margin-top: 15px;">
                                <div class="form-group">
                                    <label for="name" class="col-md-3"> Booking Date</label>
                                    <div class="col-md-6">
                                        <?php if($flag == "0"){ ?>
                                        <input type="date" class="form-control"  id="booking_date" min="<?php echo date("Y-m-d") ?>" name="booking_date" value = "<?php echo  date("Y-m-d", strtotime("+1 day")); ?>" required>
                                        <?php } else { ?>
                                        <input type="text" class="form-control" value="<?php echo $booking_details[0]['booking_date']; ?>"></input>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 15px;">
                                <div class="form-group">
                                    <label for="name" class="col-md-1"> Remarks</label>
                                    <div class="col-md-10">
                                        <textarea  type="text" class="form-control" rows="3" id="problem_description" name="problem_description"  placeholder="Please Enter Your Remarks"><?php echo $booking_details[0]['query_remarks'] ?></textarea> 
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  col-md-12" >   
                            <?php if($flag == "0"){ ?>     
                                <input type="submit" class=" btn btn-primary btn-md col-md-offset-5"  value = "Confirm Installation"  > 
                            <?php } else { ?>
                            <p class="col-md-offset-3">Your booking is scheduled, If you want any change then call our call center 9555000247.</p>
                            <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="wrapper downloads">
            <div class="container">
                <div class="contianer apps-section col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <div class="app-head">
                        <h3>Get Services in Just One Click!</h3>
                    </div>
                    <div class="app-subhead">
                        <h5>Download our App Now and Get Rs 50 Off</h5>
                    </div>
                    <div class="app-link"><a href="https://play.google.com/store/apps/details?id=com.handymanapp"><img src="<?php echo base_url(); ?>website_assets/images/googleplay-getapp-icon_130x65.png" alt="Get 247around on Google Playstore"></a></div>
                    <div class="youtube-link"><a href="https://www.youtube.com/watch?v=lym_mBk7kZw&feature=youtu.be" target="_blank">Watch Video</a></div>
                    <div class="mobile-screen fb-page" style="margin:40px auto auto 30px;" data-href="https://www.facebook.com/247around/" data-width="400" data-height="470" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>
                </div>
                <div class="mobile-screen col-lg-7 col-md-7 hidden-sm hidden-xs">
                    <span class="mobile-demo"><img src="<?php echo base_url(); ?>website_assets/images/247around-android-app.png" alt="247around Android App"></span>
                </div>
                <br>
            </div>
        </div>
        <div class="why-247around">
            <div class="container">
                <div class="homepage-section-header">
                    <h2>Why 247around</h2>
                </div>
                <div class="row">
                    <div class="why-description col-xs-12 col-sm-12 col-md-12">
                        <div class="col-md-3 col-sm-6 col-xs-12 how-section-item">
                            <img src="<?php echo base_url(); ?>website_assets/images/247around-quality-of-service.png" alt="247around - Quality of Service and Customer Satisfaction">
                            <h4>
                                <span>Quality</span>
                            </h4>
                            <p class="how-description">Get appliances repaired by authorized service engineers. <strong>Quality Ensured.</strong></p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 how-section-item">
                            <img src="<?php echo base_url(); ?>website_assets/images/247around-competitive-price.png" alt="247around - Competitive Price of Services & Repair">
                            <h4>
                                <span>Competitive Price</span>
                            </h4>
                            <p class="how-description">Expect certified professionals at reasonable prices. <strong>No Hidden Charges.</strong></p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 how-section-item">
                            <img src="<?php echo base_url(); ?>website_assets/images/247around-convenience-in-booking.png" alt="247around - Convenience - Book in One Click">
                            <h4>
                                <span>Convenience</span>
                            </h4>
                            <p class="how-description">Book through App, Website or by Directly calling us. <strong>Super Easy !</strong></p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 how-section-item">
                            <img src="<?php echo base_url(); ?>website_assets/images/247around-reliable.png" alt="247around Trust - Verified Professionals">
                            <h4>
                                <span>Reliability</span>
                            </h4>
                            <p class="how-description">Get up to 3 months warranty on all our services. <strong>Peace of Mind.</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="services">
            <div class="container">
                <div class="homepage-section-header">
                    <h2>Installations, Service &amp; Repair</h2>
                </div>
                <div >
                    <ul class="card-list">
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="<?php echo base_url()?>tv-repair" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-tv.png" alt="Television Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>TV</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="<?php echo base_url()?>refrigerator-repair" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-refrigerator.png" alt="Refrigerator Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Refrigerator</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="<?php echo base_url()?>washing-machine-repair" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-washing-mashine.png" alt="Washing Machine Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Washing Machine</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="<?php echo base_url()?>ac-repair" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-ac.png" alt="Air Conditioner Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>AC</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="<?php echo base_url()?>microwave-oven-repair" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-microwave.png" alt="Microwave Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Microwave</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-ro.png" alt="Water Purifier (RO) Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Water Purifier</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-geyser.png" alt="Water Heater (Geyser) Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Geyser</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-12 col-sm-6 col-md-3">
                            <a href="" class="page-link">
                                <div class="service-card">
                                    <div class="row">
                                        <span><img src="<?php echo base_url(); ?>website_assets/images/repair-mixer.png" alt="Food Processor (Mixer) Repair and Service"></span>
                                    </div>
                                    <span>
                                        <h4>Food Processor</h4>
                                    </span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="wrapper how-section">
            <div class="container">
                <div class="how-subhead">
                    <h5>Appliance Repair Just One Click/Call Away</h5>
                </div>
                <div class="how-head">
                    <h2>How it Works</h2>
                </div>
                <div class="how-para">
                    <p>You can hire a professional for your desired service in 3 simple steps.</p>
                </div>
                <div class="row">
                    <ul class="steps-list">
                        <li class="steps-card col-md-4 col-xs-12">
                            <div class="col-xs-4 col-md-3">
                                <img src="<?php echo base_url(); ?>website_assets/images/247around-submit-request_57x50.png" alt="247around - Submit Your Request">
                            </div>
                            <div class="col-xs-8 col-md-9 col-sm-8">
                                <h4>1. Check Prices</h4>
                                <p>We maintain complete <br>transparency in <a href="<?php echo base_url()?>charges">Prices</a></p>
                            </div>
                        </li>
                        <li class="steps-card col-md-4 col-xs-12">
                            <div class="col-xs-4 col-md-3">
                                <img src="<?php echo base_url(); ?>website_assets/images/247around-submit-request_57x50.png" alt="247around - Check Prices">
                            </div>
                            <div class="col-xs-8 col-md-9 col-sm-8">
                                <h4>2. Book Service</h4>
                                <p>Fill in the form<br>and we'll contact you!</p>
                            </div>
                        </li>
                        <li class="steps-card col-md-4 col-xs-12">
                            <div class="col-xs-4 col-md-3">
                                <img src="<?php echo base_url(); ?>website_assets/images/247around-submit-request_57x50.png" alt="247around - Service at Your Doorstep">
                            </div>
                            <div class="col-xs-8 col-md-9 col-sm-8">
                                <h4>3. At Your Doorstep</h4>
                                <p>The technician arrives<br>at your doorstep within hours.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="cta-how">
                    <div class="all-services" id="how-cta"><span>Book Service</span></div>
                </div>
            </div>
        </div>
        <style type="text/css">
            /* Adjust feedback icon position */
            #bookingForm .form-control-feedback {
            right: 15px;
            }
            #bookingForm .selectContainer .form-control-feedback {
            right: 25px;
            }
        </style>
        <script>
            $(document).ready(function() {
               $('#bookingForm').formValidation({
                   framework: 'bootstrap',
                   icon: {
                       valid: 'glyphicon glyphicon-ok',
                       invalid: 'glyphicon glyphicon-remove',
                       validating: 'glyphicon glyphicon-refresh'
                   },
                   err: {
                       container: 'tooltip'
                       },
                   fields: {
                       name: {
                           validators: {
                               notEmpty: {
                                   message: 'Full Name is required'
                               },
                               blank: {}
                           }
                       },
                       city: {
                           validators: {
                               notEmpty: {
                                   message: 'City is required'
                               },
                               blank: {}
                           }
                       },
                       mobile: {
                           // row: '.col-xs-8',
                           validators: {
                               notEmpty: {
                                   message: 'Mobile Number is required'
                               },
                               phone: {
                                   country: 'IN',
                                   message: 'Phone number is not valid'
                               },
                               stringLength: {
                                   min: 10,
                                   max: 10,
                                   message: 'Mobile number should be of 10 digits',
                               },
                               blank: {}
                           }
                       },
                       service: {
                           // row: '.col-xs-4',
                           validators: {
                               notEmpty: {
                                   message: 'Service is required'
                               },
                               blank: {}
                           }
                       },
                   }
               });   //end $('#bookingForm').formValidation({)
            
            });   //end $(document).ready(function()
        </script>
        <script type="text/javascript">
            $(window).scroll(function() {
              if ($(document).scrollTop() > 100) {
                $('nav').removeClass('navbar-personalised');
              } else {
                $('nav').addClass('navbar-personalised');
              }
            });
            
            $(window).scroll(function() {
              if ($(document).scrollTop() > 100) {
                $('.navlist-add').removeClass('atf-links');
              } else {
                $('.navlist-add').addClass('atf-links');
              }
            });
            
            $(window).scroll(function() {
              if ($(document).scrollTop() > 100) {
                $('.navVideo-add').removeClass('video-link');
                $('.navVideo-add').addClass('video-scrollLink');
              } else {
                $('.navVideo-add').removeClass('video-scrollLink');
                $('.navVideo-add').addClass('video-link');
              }
            });
            
             $("#how-cta").click(function() {
                    $('html,body').animate({
                        scrollTop: $("#atf-home").offset().top},
                        1000);
                  });
            
            $(document).ready(function () {
                    $(".navbar-toggle").on("click", function () {
                        $(this).toggleClass("active");
                    });
                  });
        </script>