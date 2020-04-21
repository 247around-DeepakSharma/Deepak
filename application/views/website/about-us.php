<!DOCTYPE html>
<html lang="en">
  <head>
    <title>247Around</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/website_assets/css/font-awesome.min.css">

    <?php include_once("header-close.php") ?>
  </head>

  <body>
    <?php include_once("body-open.php") ?>

    <?php include_once("body-navbar.php") ?>

    <div class="wrapper" id="atf-about-us">
      <div class="container-fluid">
        <div class="row col-xs-12 col-sm-12 col-md-6 col-lg-7 service-head">
            <!--<h1>About Us</h1>-->
            <h1></h1>
        </div>
      </div>
    </div>

<div class="wrapper"  style=" border: 1px solid #ccc; padding: 0 40px;" >
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="section"  style=" border: 1px solid #ccc; margin: 10px auto; padding: 0 40px;">
                    <h2 class="section-title text-center">About Us</h2>
                    <p class="text-justify">
                        <strong>247around</strong> serves as an appliance buddy for all home appliance post-purchase needs 
                        i.e. from Installation to Disposal. One can book a service either through our Android 
                        App, website <a href="https://247around.com">247around.com</a>  or call center.
                    </p>
                    <p class="text-justify">
                        We cater to a market with 700 Million appliance population. 
                        150 Million appliances go for repairs every year. Quality 
                        repair at optimum price is a challenge here. At <strong>247around</strong> , 
                        we address this issue by qualified on ground team which has 
                        several years of experience and we enable our customers to do 
                        self-diagnostics and decipher the issue on our android app. 
                        You get to see reasons of failure and price estimates much 
                        before the engineer arrives at home, thus saving time and 
                        money and bringing transparency in the process.
                    </p>
                    <p class="text-justify">
                        Analytics combined with Artificial Intelligence and IOT form 
                        the technology backbone and eliminate key issues of quality 
                        in home appliance servicing and repair. With Technology assisted 
                        analytics based training, we believe our on-ground engineers take 
                        better repair decisions challenging traditional models. 247around 
                        is the first company to use analytics based approach which has 
                        found value proposition for customers.
                    </p>
                    <p class="text-justify">
                        <strong>247around</strong>  is part of Business World accelerator program 
                        <a href="https://inc42.com/buzz/meet-the-10-startups-that-are-part-of-businessworld-accelerators-first-batch/" target="_blank">BW|Accelerator</a> 
                        and Google Boot Camp.
                    </p>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="section" style=" border: 1px solid #ccc; margin: 10px auto;">
                    <div class="section-title">
                            <h2 class="text-center">Our Presence</h2>
                                <div class="section-map">
                                    <div id="map-chart" class="text-center"><img src="<?php echo base_url(); ?>website_assets/images/india-map.png"></div>
                                </div>
                            <div class="section-counter text-center">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-1 col-sm-1  ">
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-4">
                                            <h4>
                                                <span class="city-count counter"><?php echo $city_count; ?></span>
                                                Cities
                                            </h4>
                                        </div>

                                        <div class="col-md-3 col-sm-3 col-xs-4">
                                            <h4>
                                                <span class="partners-count counter"><?php echo $partner_count; ?></span>
                                                Partners
                                            </h4>
                                        </div>

                                        <div class="col-md-3 col-sm-3 col-xs-4">
                                            <h4>
                                                <span class="customers-count counter"><?php echo $users[0]['total_user']; ?></span>
                                                Customers
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="section" style=" border: 1px solid #ccc; margin: 10px auto;">
                    <div class="section-title">
                        <h2 class="text-center">Our Team</h2>
                        <div class="section-team">
                            <div class="container">
                                <div class="row">
                                    <?php foreach ($employee as $value){ ?> 
                                        <div class="col-md-3 col-sm-4 col-xs-12">
                                            <div class="card">
                                                <?php if(isset($value['image_link']) && file_exists($value['image_link'])){ ?>
                                                    <img src="<?php echo $value['image_link'];?>" alt="Avatar" style="width:100%;height:210px;">
                                                <?php }else{?>  
                                                    <img src="<?php echo base_url(); ?>website_assets/images/employee247.png" alt="Avatar" style="width:100%">
                                                <?php }?> 
                                                <?php if(isset($value['full_name'])){ ?>
                                                    <h6 class="text-center" style="font-size:16px;"><b><?php echo $value['full_name'];?></b></h6>
                                                <?php }?>  
                                                <?php if(isset($value['designation'])){ ?>
                                                    <h6 class="text-center" style="font-size:14px;"><b><?php echo $value['designation'];?></b></h6>
                                                <?php }else{?>
													<h6 style="color:#fff;">s </h6><?php } ?>
                                                <?php if(isset($value['linkedin_link'])){ ?>
                                                    <p class="text-center">
                                                        <a href="<?php echo $value['linkedin_link'];?>" style="font-size:20px;">
                                                          <i class="fa fa-linkedin-square" aria-hidden="true"></i>
                                                        </a>
                                                    </p>    
                                                <?php } else{?>
													<p class="text-center hide-fa">
                                                        <a href="javascript:void()" style="font-size:20px;color:#fff;">
                                                          <i class="fa" aria-hidden="true"></i>
                                                        </a>
                                                    </p>  
												<?php } ?>
                                                    
                                            </div>
                                        </div>        
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
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
        .highcharts-button{
            display: none!important;
        }
        .highcharts-credits{
            display: none;
        }
        .highcharts-legend-item{
            display: none;
        }
        .section-team .card {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            width: 92%;
            border-radius: 5px;
            margin: 10px 0;
            padding-bottom: 10px;
        }

        .section-team .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }

        .section-team .card img {
            border-radius: 5px 5px 0 0;
        }
        .section p{
                line-height: 1.5em;
                letter-spacing: 1px;
        }
        .section h2{
                line-height: 1.5em;
                letter-spacing: 1px;
        }
        .section-counter h4{ font-size: 14px;}
        
        .section-counter h4>span.counter {
            font-size: 30px;
            display: block;
            color: #333 !important;
            letter-spacing: 1px;
        }
        .section-team .card:hover{
            background-color: #E6E6E6;
            color: #202020;
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

