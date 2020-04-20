<!DOCTYPE html>
<html lang="en">
    <head>
        <title>247around - Confirm Booking</title>
        <?php include_once("header-close.php") ?>
    </head>
    <body>
        <?php include_once("body-open.php") ?>
        <?php include_once("body-navbar.php") ?>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
         <style>
            #confirm_form .form-group label.error {
                color:red;
                margin:4px 0 5px !important;
                width:auto !important;
            }
        </style>
        <div class="wrapper" id="atf-thoughts">
            <div class="container-fluid">
                <div class="row col-xs-12 col-sm-12 col-md-6 col-lg-7 service-head">
                    <!--<h1>About Us</h1>-->
                    <h1></h1>
                </div>
            </div>
        </div>
        <div class="wrapper particular-service">
            <div class="container">
                <div class="homepage-section-header">
                    <h1>Provide few more details so that we can confirm your booking :</h1>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <center>
                            <h4 <?php if (isset($empty_field)) { ?>style="text-align:center;color:red;" <?php } else { ?> style="text-align:center;" <?php } ?>   >
                                      <?php
                                          if (isset($empty_field)) {
                                              echo $empty_field;
                                          } else {
                                              echo "Please Enter All the input field";
                                          }
                                          ?>
                                  </h4>
                            <form class="form-horizontal" id="confirm_form" method="POST" action="<?php echo base_url(); ?>main/booking_verification">
                            <div class="form-group"> 
                                  <label  for="service" class="col-md-2 col-md-offset-2">Appliance *</label>
                                  <div class="col-md-2"> 
                                    <select class="form-control formStyle" name="brand" id="brands" style="padding-right: 0px;width: 100%;" required>
                                                <option selected disabled>Select Brand</option>
                                                <?php foreach($brand as $value){?> 
                                                    <option value="<?php echo $value['brand_name']?>"><?php echo $value['brand_name'] ?></option>
                                                <?php }?>
                                            </select>
                                  </div>
                                  <div class="col-md-2"> 
                                    <select class="form-control formStyle city" id="category" name="category" required style="width: 100%;">
                                                <option selected disabled>Select Category</option>
                                                
                                            </select>
                                  </div>
                                  <div class="col-md-2" id="capacity_div"> 
                                    <select class="form-control formStyle city" id="capacity" name="capacity" style="width: 100%;">
                                                <option selected disabled>Select Capacity</option>
                                                
                                            </select>
                                  </div>
                                </div>
                              <div  class="form-group">
                                  <label  for="booking_remarks" class="col-md-2 col-md-offset-2">Problem *</label>
                                  <div class="col-md-6">
                                      <textarea type="text" name="booking_remarks" class="form-control" placeholder="Please enter your brand, size / ton and problem description" required></textarea>
                                  </div>
                              </div>

                              <div  class="form-group">
                                  <label  for="address" class="col-md-2 col-md-offset-2">Address *</label>
                                  <div class="col-md-6">
                                      <textarea type="text" name="address" class="form-control" required></textarea>
                                  </div>
                              </div>

                                <div  class="form-group">
                                    <label  for="pincode" class="col-md-2 col-md-offset-2">Pincode *</label>
                                    <div class="col-md-2">
                                        <input type="text" name="pincode" class="form-control"required>
                                    </div>
                                     <label  for="pincode" class="col-md-2">Booking Date *</label>
                                     <div class="col-md-2">

                                        <input id="booking_date" class="form-control "  name="booking_date" type="date"
                                          value = "<?php if (date('H:m') > '12:00') {
                                            echo date("Y-m-d", strtotime('+1 days'));
                                            } else {
                                            echo date("Y-m-d", strtotime('+0 days'));
                                            } ?>" required readonly='true' style="background-color:#fff; width: 100%;">
                                    </div>
                                </div>

                                <div class="col-md-offset-1">
                                  <br/>
                                  <h4 <?php if (isset($invalid_otp)) { ?>style="text-align:center;color:red;" <?php } else { ?> style="text-align:center;" <?php } ?>   >
                                      <?php
                                          if (isset($invalid_otp)) {
                                              echo $invalid_otp;
                                          } else {
                                              echo "A OTP (One Time Password) has been sent to your mobile number, please enter it below:";
                                          }
                                          ?>
                                  </h4>
                                <input type="text" name="otp_number" class="form-control" style="width:130px;text-align: center; margin-top:15px;" />
                                <input type="hidden" name="request_verification_code" value="<?php
                                    if (isset($request_verification_code)) {
                                        echo $request_verification_code;
                                    }
                                    ?>" />
                                <h6 style="text-align:center;font-weight:bold;">
                                    <span>Time remaining: </span>
                                    <span id="countdown">3 </span>
                                    <span>minutes</span>
                                </h6>
                                <br/>
                                <br/>
                                <input type="hidden" name="service_id" id="service_id" value="<?php echo $service_id; ?>">
                                <input id="submit" type="submit" value="Confirm Booking" class="btn btn-lg btn-primary" style="background-color:#21768B; border-color:#21768B;"/>
                                <br/>
                                <span style="font-weight:bold;">By clicking this button, you agree to <a href="<?php echo base_url()?>terms" target="_blank">247around Terms & Conditions</a></span>
                            </form>
                            </div>
                        </center>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper downloads">
            <div class="container">
                <div class="row">
                    <div class="apps-section col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="app-head">
                            <h3>Get Services in Just One Click!</h3>
                        </div>
                        <div class="app-subhead">
                            <h5>Download our App Now</h5>
                        </div>
                        <div class="app-link"><a href="https://play.google.com/store/apps/details?id=com.handymanapp"><img src="<?php echo base_url() ?>website_assets/images/googleplay-getapp-icon.png" alt="Get 247around on Google Playstore"></a></div>
                        <div class="youtube-link"><a href="https://www.youtube.com/watch?v=lym_mBk7kZw&feature=youtu.be" target="_blank">Watch Video</a></div>
                    </div>
                    <div class="mobile-screen col-lg-6 col-md-6 hidden-sm hidden-xs">
                        <span class="mobile-demo"><img src="<?php echo base_url() ?>website_assets/images/247around-android-app.png" alt="247around Android App"></span>
                    </div>
                </div>
            </div>
        </div>
        <style type="text/css">
          .form-horizontal .has-feedback .form-control-feedback{display: none!important;}
          .has-feedback .form-control {padding-right: 0px!important;}
        </style>
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
            function countdown(elementName, minutes, seconds) {
                var element, endTime, hours, mins, msLeft, time;

                function twoDigits(n)
                {
                    return (n <= 9 ? "0" + n : n);
                }

                function updateTimer()
                {
                    msLeft = endTime - (+new Date);
                    if (msLeft < 1000) {
                        window.location.href = "<?php echo base_url(); ?>main/verify_booking";
                    } else {
                        time = new Date(msLeft);
                        hours = time.getUTCHours();
                        mins = time.getUTCMinutes();
                        element.innerHTML = (hours ? hours + ':' + twoDigits(mins) : mins) + ':' + twoDigits(time.getUTCSeconds());
                        setTimeout(updateTimer, time.getUTCMilliseconds() + 500);
                    }
                }

                element = document.getElementById(elementName);
                endTime = (+new Date) + 1000 * (60 * minutes + seconds) + 500;
                updateTimer();
            }

             countdown( "countdown", 5, 0 );

            //$("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});
            var dt = new Date();
            var hour = dt.getHours();
            var minutes = dt.getMinutes();
            if((parseInt(hour, 12) > 12) && (parseInt(minutes, 00)) > 00) {
               mindate = 1;
            } else {
                mindate = 0;
            }
            $("#booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: mindate,
                beforeShowDay: function(date) {
                    var day = date.getDay();
                    return [(day !== 0), ''];
                }
            });
            
            (function ($, W, D)
    {
        var JQUERY4U = {};
    
        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        $("#confirm_form").validate({
                            rules: {
                                booking_date: "required",
                                booking_remarks: {
                                    required: true,
                                    minlength: 25
                                },
                                address: {
                                    required: true,
                                    minlength: 25
                                },
                                pincode: {
                                    minlength: 6,
                                    maxlength: 6,
                                    number: true
                                },
                                otp_number: {
                                    number: true
                                }
                            },
                            messages: {
                                booking_date: "Booking Date is required",
                                booking_remarks: "Problem Description is reqired atleast 25 characters",
                                address: "Address is reqired atleast 25 characters",
                                pincode: "Pincode should be of 6 digits",
                                otp_number: "OTP is required"
                                
                            },
                            submitHandler: function (form) {
                                form.submit();
                            }
                        });
                    }
                }
    
        //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
            JQUERY4U.UTIL.setupFormValidation();
        });
    
    })(jQuery, window, document);
    
//            $(document).ready(function() {
//               $('#confirm_form').formValidation({
//                   framework: 'bootstrap',
//                   icon: {
//                       valid: 'glyphicon glyphicon-ok',
//                       invalid: 'glyphicon glyphicon-remove',
//                       validating: 'glyphicon glyphicon-refresh'
//                   },
//                   err: {
//                       container: 'tooltip'
//                       },
//                   fields: {
//                       booking_date: {
//                           validators: {
//                               notEmpty: {
//                                   message: 'Booking Date is required'
//                               },
//                               blank: {}
//                           }
//                       },
//
//                       booking_remarks: {
//                              validators: {
//                                  notEmpty: {
//                                      message: 'Problem description is required'
//                                  },
//                                  stringLength: {
//                                      min: 25,
//                                      message: 'Minimum 25 characters required',
//                                  },
//                                  blank: {}
//                              }
//                          },
//
//                          address: {
//                                 validators: {
//                                     notEmpty: {
//                                         message: 'Address is required'
//                                     },
//                                     stringLength: {
//                                         min: 25,
//                                         message: 'Minimum 25 characters required',
//                                     },
//                                     blank: {}
//                                 }
//                             },
//
//                       pincode: {
//                           // row: '.col-xs-8',
//                           validators: {
//                               notEmpty: {
//                                   message: 'Pincode is required'
//                               },
//                               stringLength: {
//                                   min: 6,
//                                   max: 6,
//                                   message: 'Pincode should be of 6 digits',
//                               },
//                               blank: {}
//                           }
//                       },
//                       otp_number: {
//                           // row: '.col-xs-8',
//                           validators: {
//                               notEmpty: {
//                                   message: 'OTP is required'
//                               },
//                               stringLength: {
//                                   min: 4,
//                                   max: 4,
//                                   message: 'OTP Number should be of 6 digits',
//                               },
//                               blank: {}
//                           }
//                       },
//                   }
//               });   //end $('#bookingForm').formValidation({)
//
//            });   //end $(document).ready(function()

        </script>
        <script type="text/javascript">
          $("#brands").select2();
          $("#category").select2();
          $("#capacity").select2();
          $(document).ready(function(){
                $('#brands').on('change',function(){
                    var brand = $(this).val();
                    var service_id = <?php echo $service_id; ?>;
                    var dataString = '&brand=' + brand + '&service_id=' + service_id;
                    if(brands){
                        $.ajax({
                            type:'POST',
                            url:"<?php echo base_url(); ?>get-Category-Service",
                            data:dataString,
                            success:function(response){
                                   $('#category').html(response);
                                   
                            }
                            
                        }); 
                    }
                });

                $('#category').on('change',function(){
                    var category = $(this).val();
                    var brand = $('#brands').val();
                    var service_id = <?php echo $service_id; ?>;
                    var dataString = '&category=' + category + '&service_id=' + service_id;
                    if(brands){
                        $.ajax({
                            type:'POST',
                            url:"<?php echo base_url(); ?>get-capacity",
                            data:dataString,
                            success:function(response){
                                if(response === 'empty'){
                                    $('#capacity_div').hide();
                                }else{
                                    $('#capacity').html(response);
                                    $('#capacity_div').show();
                                }
                                   
                            }
                            
                        }); 
                    }
                });
            });
        </script>
        
       


