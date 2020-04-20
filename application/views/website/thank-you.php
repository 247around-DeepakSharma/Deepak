<!DOCTYPE html>
<html lang="en">
  <head>
      <title>Thank You</title>

    <?php include_once("header-close.php") ?>
  </head>

  <body>
    <?php include_once("body-open.php") ?>

    <?php include_once("body-navbar.php") ?>

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
          <div class="homepage-section-header"><h1><?php echo $title; ?></h1></div>
          <div class="row">
              <div class="col-xs-12 col-sm-12">
                  <h4 style="text-align:center;">
                      <?php echo $message; ?>
                  </h4>

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
            <div class="app-head"><h3>Get Services in Just One Click!</h3></div>
            <div class="app-subhead"><h5>Download our App Now</h5></div>
            <div class="app-link"><a href="https://play.google.com/store/apps/details?id=com.handymanapp"><img src="<?php echo base_url() ?>website_assets/images/googleplay-getapp-icon.png" alt="Get 247around on Google Playstore"></a></div>
            <div class="youtube-link"><a href="https://www.youtube.com/watch?v=lym_mBk7kZw&feature=youtu.be" target="_blank">Watch Video</a></div>
          </div>
          <div class="mobile-screen col-lg-6 col-md-6 hidden-sm hidden-xs">
              <span class="mobile-demo"><img src="<?php echo base_url() ?>website_assets/images/247around-android-app.png" alt="247around Android App"></span>
          </div>
        </div>
      </div>
    </div>
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
    </script>

