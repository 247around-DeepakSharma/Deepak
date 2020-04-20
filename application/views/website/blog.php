<!DOCTYPE html>
<html lang="en">
    <head>
        <title>247around - Appliance Repair Service At Your Doorstep</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <?php include_once("header-close.php") ?>
    </head>
    <body>
        <?php include_once("website_assets/js/fb.js") ?>
        <?php include_once("body-navbar.php") ?>
        <div class=" row">
        <?php include_once("body-open.php") ?>
        <div class="wrapper" id="atf-home">
            <div class="container">
                <div class="header-top"></div>
                <div class="row formBox formSection" style="background-color: rgba(10, 10, 10, 0);">
                    <div class="head-small">Appliance breakdown, don't let it stop your life !!!</div>
                    <div class="head-call visible-xs-block"><a href="tel:9555000247"><i class="fa fa-phone phone-style"></i>9555000247</a></div>
                    <div class="header-home hidden-xs col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                        <h1 class="light">Bringing Quality, Convenience &amp; Reliability To Home and Office Appliance Repair</h1>
                    </div>
                    <div class="col-md-12 col-md-offset-0">
                        <!--                        <div class="row">
                            <form id="bookingForm" method="post" action="<?php echo base_url() ?>main/book_service">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2 col-md-offset-1 ">
                                            <select class="form-control formStyle" id="city" name="city">
                                                <option selected disabled >Select City</option>
                                                <?php
                                //foreach ($city as  $value) { ?>
                                                <option value="<?php //echo $value['City']; ?>"><?php //echo $value['City']; ?></option>
                                                <?php //}
                                ?>
                                            </select>
                                        </div>
                                                            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-8 col-md-offset-2">
                                        <div class="col-md-2 col-md-offset-0 form_margin">
                                            <input type="text" pattern="^[A-Z a-z]*[aeiouAEIOU][A-Z a-z]*$" class="form-control" name="name" placeholder="Full Name" required />
                                        </div>
                                        <div class="col-md-2 col-md-offset-0 form_margin">
                                            <input type="text" pattern="[789][0-9]{9}" class="form-control" name="mobile" placeholder="Mobile Number" required />
                                        </div>
                                        <div class="col-md-2 col-md-offset-0 form_margin">
                                            <select class="form-control formStyle" name="service" style="padding-right: 0px;">
                                                formStyle
                                                <option value="" style="display: none">Select Appliance</option>
                                                <option value="Air Conditioner">Air Conditioner</option>
                                                <option value="Washing Machine">Washing Machine</option>
                                                <option value="Television">Television</option>
                                                <option value="Refrigerator">Refrigerator</option>
                                                <option value="Microwave">Microwave</option>
                                            </select>
                                        </div>
                                        <div class="button-book col-md-2 col-md-offset-0 form_margin">
                                            <button id="submitButton" type="submit" class="btn btn-book">Book Service</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="header-home col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                                            <h5 style="color:white">Use EXTRA50 coupon code on <a href="https://play.google.com/store/apps/details?id=com.handymanapp" target="_blank" style="color:white;padding-bottom:3px;border-bottom:1px solid;">Android App</a> to avail Rs. 50 discount on every booking !!!</h5>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            </div>-->
                        <div class="row ">
                            <form id="bookingForm" method="post" action="<?php echo base_url() ?>main/verify_booking">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2 col-md-offset-1">
                                            <select class="form-control formStyle" name="service" id="appliance" style="padding-right: 0px; width: 100%;">
                                                <option selected disabled>Select Appliance</option>
                                                <?php foreach($services as $value){?> 
                                                <option value="<?php echo $value['id']?>"><?php echo $value['services'] ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <div class="col-md-2  col-md-offset-0 form_margin">
                                            <select class="form-control formStyle city" id="city" name="city" style="width:100%;">
                                                <option selected disabled>Select City</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-md-offset-0 form_margin">
                                            <input type="text" pattern="^[A-Z a-z]*[aeiouAEIOU][A-Z a-z]*$" class="form-control" name="name" placeholder="Full Name" required />
                                        </div>
                                        <div class="col-md-2 col-md-offset-0 form_margin">
                                            <input type="text" pattern="[789][0-9]{9}" class="form-control" name="mobile" placeholder="Mobile Number" required />
                                        </div>
                                        <div class="button-book col-md-2 col-md-offset-0 form_margin">
                                            <button id="submitButton" type="submit" class="btn btn-book">Proceed</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="header-home col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                                            <h5 style="color:white">Please check <a href="<?php echo base_url()?>charges" target="_blank" style="color: rgb(255,255,255)">Prices</a> to find out 247around service charges for all appliances.</h5>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" >
            <div class="homepage-section-header">
                <h2>Select Your Appliance</h2>
            </div>
            <ul class="card-list">
                <li class="col-xs-12 col-sm-6 col-md-4">
                    <a href="<?php echo base_url()."blog/"?>television" class="page-link">
                        <div class="service-card">
                            <!--                    <div class="row">-->
                            <span><img src="website_assets/images/tv-repair-blog.png" width="100%" height="auto" alt="Television Repair and Service"></span>
                            <!--                    </div>-->
                            <span>
                                <h4>LCD / LED TV</h4>
                            </span>
                        </div>
                    </a>
                </li>
                <li class="col-xs-12 col-sm-6 col-md-4">
                    <a href="<?php echo base_url()."blog/"?>air-conditioner" class="page-link">
                        <div class="service-card">
                            <!--                    <div class="row">-->
                            <span><img src="website_assets/images/Air-Conditioner-Repair-1_314*252.jpg" width="100%" height="auto" alt="Air Conditioner Repair and Service"></span>
                            <!--                    </div>-->
                            <span>
                                <h4>Window / Split AC</h4>
                            </span>
                        </div>
                    </a>
                </li>
                <li class="col-xs-12 col-sm-6 col-md-4">
                    <a href="<?php echo base_url()."blog/"?>refrigerator" class="page-link">
                        <div class="service-card">
                            <!--                    <div class="row">-->
                            <img src="website_assets/images/refrigerators_314*252.jpg" width="100%" height="auto" alt="Refrigerator Repair and Service">
                            <span>
                                <h4>Refrigerator</h4>
                            </span>
                            <!--                    </div>-->
                        </div>
                    </a>
                </li>
                <li class="col-xs-12 col-sm-6 col-md-4">
                    <a href="<?php echo base_url()."blog/"?>washing-machine" class="page-link">
                        <div class="service-card">
                            <!--                    <div class="row">-->
                            <span><img src="website_assets/images/washing-machine-blog.png" width="100%" height="auto" alt="Washing Machine Repair and Service"></span>
                            <!--                    </div>-->
                            <span>
                                <h4>Washing Machine</h4>
                            </span>
                        </div>
                    </a>
                </li>
                <li class="col-xs-12 col-sm-6 col-md-4">
                    <a href="<?php echo base_url()."blog/"?>microwave-oven" class="page-link">
                        <div class="service-card">
                            <!--                    <div class="row">-->
                            <span><img src="website_assets/images/microwave-2_314*252.jpg" width="100%" height="auto" alt="Microwave Repair and Service"></span>
                            <!--                    </div>-->
                            <span>
                                <h4>Microwave</h4>
                            </span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <style type="text/css">
            /* Adjust feedback icon position */
            #bookingForm .form-control-feedback {
            right: 15px;
            }
            #bookingForm .selectContainer .form-control-feedback {
            right: 25px;
            }
            .form-group .select2-container .select2-selection--single {
            height: 34px;
            }
            .form-group .select2-container--default .select2-selection--single .select2-selection__arrow{
            display: none;
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
        <script type="text/javascript">
            $("#appliance").select2({
                 minimumResultsForSearch: Infinity
            });
            $("#city").select2();
        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                 $('#appliance').on('change',function(){
                     var applianceID = $(this).val();
                     if(applianceID){
                         $.ajax({
                             type:'POST',
                             url:'get-city',
                             data:'id='+applianceID,
                             success:function(html){
                                    $('#city').val('val', "");
                                    $('#city').val('Select City').change();
                                    $('#city').select2().html(html);
                             }
                             
                         }); 
                     }else{
                         $('#city').html('<option value="">Select Appliance First</option>'); 
                     }
                 });
             });
        </script>
