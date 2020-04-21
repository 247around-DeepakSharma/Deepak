<footer>
   <style>.our_services{color:#fff;text-decoration:none;padding-top:20px}.our_services:hover{color:#000;text-decoration:none;padding-top:20px}@media only screen and (max-width:768px){.our_services1{margin-top:15px}}</style>
   <div class=container style="margin-left:30px;" >
      <div class="row leader-mg-sm leader-pd-lg">
         <div class=footer-nav>
            <div class=col-md-12>
               <h3 class=quietText><strong>247AROUND</strong></h3>
               <div class=row>
                  <div class="col-md-2 col-sm-3 col-xs-3"><a href="<?php echo base_url() ?>about-us">About Us</a></div>
                  <div class="col-md-2 col-sm-3 col-xs-3"><a href="<?php echo base_url() ?>contact-us">Contact Us</a></div>
                  <div class="col-md-2 col-sm-3 col-xs-3"><a href="<?php echo base_url() ?>privacy">Privacy Policy</a></div>
                  <div class="col-md-2 col-sm-3 col-xs-3"><a href="<?php echo base_url() ?>terms">Terms</a></div>
                  <div class="col-md-2 col-sm-3 col-xs-3"><a href="<?php echo base_url() ?>faq">FAQ</a></div>
               </div>
            </div>
            <div class=col-md-12>
               <h3 class=quietText><strong>OUR SERVICES</strong></h3>
               <div class="row">
               		<div class="col-md-2 col-sm-3"><a href="<?php echo base_url() ?>ac-repair"class=our_services>AC Repair</a></div>
               		<div class="col-md-2 col-sm-3 our_services1"><a href="<?php echo base_url() ?>washing-machine-repair"class=our_services>Washing Machine Repair</a></div>
               		<div class="col-md-2 col-sm-3 our_services1"><a href="<?php echo base_url() ?>refrigerator-repair"class=our_services>Refrigerator Repair</a></div>
               		<div class="col-md-2 col-sm-3 our_services1"><a href="<?php echo base_url() ?>tv-repair"class=our_services>TV Repair</a></div>
              	 	<div class="col-md-2 col-sm-3 our_services1"><a href="<?php echo base_url() ?>microwave-oven-repair"class=our_services>Microwave Oven Repair</a></div>
            	</div>
			</div>
            <div class=col-md-12>
               <h3 class=quietText><strong>OUR LOCATIONS</strong></h3>
               <div class = "row" style="margin-left:-27px;">
					<div class="col-md-12 fine-print">
                
                    	<?php
                        	foreach($city as $value) {?>
                     		<div class="col-md-2 col-sm-3 col-xs-4 text-left locations"style=color:#fff> <?php echo $value['City'] ?> </div>
                     		<?php }
                        ?>
               		</div>
			   </div>
			</div>
             <div class="col-md-9 col-sm-9 col-xs-9"></div>
             <div class="col-md-2 col-sm-2 col-xs-4" style="text-align: right;padding-right: 34px;">
                   <a class="text-right locations_toogle" href="#" style="text-decoration: none;color: #fff;font-weight: bold"></a>
               </div>
            <div class="col-md-12 fine-print" style=margin-bottom:20px>
               <h3 class=quietText><strong>ASSOCIATED BRANDS</strong></h3>
               <div class="row" style="margin-left:-27px;">
                  <div class=col-md-12>
                     <?php
                        for($i = 0; $i < sizeof($brands); $i++)
                        { ?>
                     <div class="col-md-2 col-sm-3 col-xs-4 text-left brands"style=color:#fff> <?php echo $brands[$i]->brand_name; ?> </div>
                     <?php }
                        ?>
                  </div>
               </div>
			</div>
               <div class="col-md-9 col-sm-9 col-xs-9"></div>
               <div class="col-md-2 col-sm-2 col-xs-4" style="text-align: right;padding-right: 34px;">
                   <a class="text-right brands_toogle" href="#" style="text-decoration: none;color: #fff;font-weight: bold;"></a>
               </div>
            </div>
         </div>
      </div>
      <hr>
      <div class="row trailer-pd-sm">
         <div class="fine-print col-sm-9 col-xs-12">
            <p class="quietText" style="margin-left:40px;">Copyright © 2017 Blackmelon Advance Technology Co. Pvt. Ltd., All rights reserved.
         </div>
         <div class="col-xs-12 col-sm-3">
            <ul class="list-inline list-unstyled social">
               <li>
                  <a href="https://www.facebook.com/247around" target=_blank>
                     <div class=social-icon><i class="fa fa-facebook"aria-hidden=true></i></div>
                  </a>
               </li>
               <li>
                  <a href="https://twitter.com/247around" target=_blank>
                     <div class=social-icon><i class="fa fa-twitter"></i></div>
                  </a>
               </li>
               <li>
                  <a href="https://plus.google.com/u/0/101754605016984394512" target=_blank>
                     <div class=social-icon><i class="fa fa-google-plus"></i></div>
                  </a>
               </li>
               <li>
                  <a href="https://www.youtube.com/channel/UCuFZQ1BzjMBdZxFdZoiDW2Q" target=_blank>
                     <div class=social-icon><i class="fa fa-youtube"></i></div>
                  </a>
               </li>
            </ul>
         </div>
      </div>
   </div>
   <script>
        var brands_show_init = 24;
        var location_show_init = 24;
        
        hide_msg = "...Less";
        show_msg = "More...";

        $(".brands_toogle").html( show_msg );
        $(".brands:not(:lt("+brands_show_init+"))").hide();
        
        $(".locations_toogle").html( show_msg );
        $(".locations:not(:lt("+location_show_init+"))").hide();
        
        $(".brands_toogle").click(function (e) {
           e.preventDefault();
               if ($(".brands:eq("+brands_show_init+")").is(":hidden")) {
                   $(".brands:hidden").show();
                   $(".brands_toogle").html( hide_msg );
               } else {
                   $(".brands:not(:lt("+brands_show_init+"))").hide();
                   $(".brands_toogle").html( show_msg );
               }
        });
        
        $(".locations_toogle").click(function (e) {
           e.preventDefault();
               if ($(".locations:eq("+location_show_init+")").is(":hidden")) {
                   $(".locations:hidden").show();
                   $(".locations_toogle").html( hide_msg );
               } else {
                   $(".locations:not(:lt("+location_show_init+"))").hide();
                   $(".locations_toogle").html( show_msg );
               }
        });
      
   </script>
</footer>
