<!DOCTYPE html>
<html>
<head>
    <title>247around - Appliance Repair Service At Your Doorstep</title>
    <meta name="description" content="We love to talk directly so please fill this form for your queries or feedback and we will get back to you very soon.">
    <meta name="keywords" content="appliance installation, service, repair">
    
    <?php include_once("header-close.php") ?>
    </head>
<body>
<?php include_once("body-open.php") ?>

    <?php include_once("body-navbar.php") ?>
     <?php include_once("website_assets/js/fb.js") ?>
     <div class="wrapper" id="atf-contact-us"> 
      <div class="container-fluid">
        <div class="row col-xs-12 col-sm-12 col-md-6 col-lg-7 service-head">
            
            <h1></h1>
        </div>
<div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <p style="text-align:center;color: #fff;font-weight:500;font-size:42px;font-family:lucida">We would love to hear from you!!</p>
</div>
      </div>
    </div>

<div class="container">
<br><br>
<div class="container col-md-9 col-sm-12 col-xs-12" >
    <div class="row">
        <div class="col-md-10">
            <div class="well well-sm">
                <form class="form-horizontal" method="post" action="<?php echo base_url() ?>main/contact-query">
                    <fieldset>
                        <legend class="text-center header" style="font-size:30px; font-family:Tahoma">Contact Us</legend>
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <input id="fname" name="fullname" type="text" pattern="^[A-Z a-z]*[aeiouAEIOU][A-Z a-z]*$" placeholder="Full Name *" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <input id="email" name="email" type="email" placeholder="Email Address *" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <input id="phone" name="phone" type="text" pattern="[789][0-9]{9}" placeholder="Mobile No *" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <select class="form-control" id="reason" name="reason" required>
                                    <option selected="" disabled="" value="">Select Reason</option>
                                    <option value="<?php echo HELP_WITH_APPLIANCE ?>"><?php echo HELP_WITH_APPLIANCE ?></option>
                                    <option value="<?php echo SATISFIED_WITH_SERVICE ?>"><?php echo SATISFIED_WITH_SERVICE ?></option>
                                    <option value="<?php echo FEEDBACK_SUGGESTIONS ?>"><?php echo FEEDBACK_SUGGESTIONS ?></option>
                                    <option value="<?php echo OTHER ?>"><?php echo OTHER ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <textarea class="form-control" id="message" name="message" placeholder="Enter your message for us here, We will get back to you within 2 business days. *" rows="7" required></textarea>
                            </div>
                        </div>
			<div class="col-md-12 text-center">
				<h5 class="text_center">Please check <a href="<?php echo base_url();?>charges" target="_blank">Prices</a> to find out 247around service charges for all appliances.</h5>
			</div>

                        <div class="form-group">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-12 col-xs-12">
<h3>247around for Business</h3>
<hr>
<!--<pre>-->
    Please drop an email to
    sales at 247around dot com for 
    your business queries, our business development team will contact you shortly.
<!--
            or
    Call +91-9999999999.
-->
    
<!--</pre>-->
<hr>
<h6><strong>BLACKMELON ADVANCE TECHNOLOGY COMPANY PRIVATE LIMITED</strong></h6>
2nd Floor,B-30 Sector 59
Noida, 201301
Uttar Pradesh
<div class="fb-send"  data-href="https://www.facebook.com/247around"></div>
</div>
</div>


<!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/website_assets/js/bootstrap.min.js"></script>
    <script src="website_assets/js/formValidation.min.js"></script>
    <script src="website_assets/js/bootstrap.min.js"></script>-->
    
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




