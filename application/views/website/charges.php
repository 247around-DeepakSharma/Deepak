<!DOCTYPE html>
<html lang="en">
   <head>
      <title>247Around - Prices</title>
      <meta name="description" content="Great Services Always At Affordable Prices. Upto 3 Months Warranty On Every Service. Don't Compare, Let Us Serve You Once. 100% Satisfaction Guaranteed.">
      <meta name="keywords" content="appliance installation, service, repair">
      <meta content='width=device-width, initial-scale=1' name='viewport' />
      <?php include_once("header-close.php") ?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
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

      

   </head>
   <script type='text/javascript'>
      //<![CDATA[
        $(document).ready(function() {
          $('.filterable .btn-filter').click(function(){
          var $panel = $(this).parents('.filterable'),
          $filters = $panel.find('.filters input'),
          $tbody = $panel.find('.table tbody');
          if ($filters.prop('disabled') == true) {
              $filters.prop('disabled', false);
              $filters.first().focus();
          } else {
              $filters.val('').prop('disabled', true);
              $tbody.find('.no-result').remove();
              $tbody.find('tr').show();
          }
      });

          $('.filter').multifilter();
        });
      //]]>
   </script>
   <body>
      <?php include_once("body-open.php") ?>
      <?php include_once("body-navbar.php") ?>
      <script src='<?php echo base_url() ?>website_assets/js/multifilter.js'></script>
      <div class="wrapper" id="atf-air-conditioner-services">
         <div class="container-fluid">
            <div class="row col-xs-12 col-sm-12 col-md-6 col-lg-7 service-head">
               <h1></h1>
            </div>
         </div>
      </div>
      <div class="wrapper particular-service">
         <div class="container">
            <div class="homepage-section-header">
               <h2>Installation & Service Charges</h2>
            </div>
            <div class="row">
               <div class="panel panel-primary filterable">
                  <div class="panel-heading">
                     <h3 class="panel-title">Services</h3>
                     <div class="pull-right">
                        <button class="btn btn-default btn-sm btn-filter"><span class="glyphicon glyphicon-filter"></span>Filter</button>
                        <!-- <img class="service-image" src="website_assets/images//sample-icon.png"> -->
                     </div>
                  </div>
                  <table class="table ">
                     <thead>
                        <tr class="filters">
			    <th width="25%"><span style="display: none;">Product</span><input autocomplete='off' class='filter form-control' name='services' placeholder='Product' data-colo='product' disabled /></th>
			    <th width="15%"><span style="display: none;">Category</span><input autocomplete='off' class='filter form-control' name='category' placeholder='Category' data-colo='category' disabled/></th>
                           <th width="10%"><span style="display: none;">Capacity</span>
                              <input style="margin-top:15px;" autocomplete='off' class='filter form-control' name='capacity' placeholder='Capacity' data-colo='capacity' disabled />
                           </th>
                           <th width="40%"><span style="display: none;">Service Category</span><input  autocomplete='off' class='filter form-control' name='service category' placeholder='Service Category' data-colo='service category' disabled/></th>
                           <th width="10%"><span style="display: none;">Total</span>
                              <input autocomplete='off'  class='filter form-control' name='total' placeholder='Total' data-colo='total' disabled/>
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($charges as $row) { ?>
                        <tr>
                           <td width="25%"><?php echo $row->services; ?></td>
                           <td width="15%"><?php echo $row->category; ?></td>
                           <td width="10%"><?php echo $row->capacity; ?></td>
                           <td width="40%"><?php echo $row->service_category; ?></td>
                           <td width="10%"><?php echo "&#8377; " . $row->total_charges; ?></td>
                        </tr>
                        <?php } ?>
                     </tbody>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>






   </body>
</html>
