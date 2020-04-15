<!DOCTYPE html>
<html>

     <script type="text/javascript" src="website_assets/js/jssor.slider.min.js"></script>

    <script>
        jssor_1_slider_init = function() {

            var jssor_1_options = {
              $AutoPlay: true,
              $Idle: 0,
              $AutoPlaySteps: 4,
              $SlideDuration: 3000,
              $SlideEasing: $Jease$.$Linear,
              $PauseOnHover: 100,
              $SlideWidth: 300,
              $Cols:4

            };

            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);


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


<div class="container" style="margin-top:40px; margin-left:15px">
<div class="col-md-3 col-xs-hidden col-sm-6" style="margin-bottom:40px" >
<h2 style="font-family:verdana ; font-weight:bold ">Featured in</h2>
</div>
<div class="col-md-9 col-xs-hidden col-sm-6">
    <div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 980px; height: 100px; overflow: hidden; visibility: hidden;">

        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
            <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
            <div style="position:absolute;display:block;background:url('website_assets/images/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
        </div>

        <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 980px; height: 100px; overflow: hidden;">
             <div style="display: none;">
                 <a href="https://yourstory.com/2017/05/247around-startup/" target="_blank"><img data-u="images" src="website_assets/images/yourstory.png"  alt="Your Story"/></a>
            </div>

            <div style="display: none;">
		<a href="http://inc42.com/tag/247around/" target="_blank"><img data-u="images" src="website_assets/images/inc42.png" alt="Inc42 - How An iPad Turned Colleagues Into Cofounders Of A Startup That Takes Care Of Your Home Appliances - 247around"/></a>
            </div>

            <div style="display: none;">
		<a href="http://techcircle.vccircle.com/2015/06/02/meet-10-startups-selected-by-bw-accelerate-for-its-mentorship-programme/" target="_blank"><img data-u="images" src="website_assets/images/techcircle.png" alt="Vccircle - Business World Accelerator 1st Batch Startups - 247around" height="65" width="250"/></a>
            </div>

            <div style="display: none;">
                 <a href="http://www.digitalmarket.asia/2015/06/bw-accelerate-announces-first-batch-of-start-ups/" target="_blank"><img data-u="images" src="website_assets/images/digitalmarketasia.png" alt="Digital Market Asia - Business World Accelerator 1st Batch Startups - 247around" /></a>
            </div>

            <div style="display: none;">
                 <a href="http://nternet.in/meet-10-startups-selected-by-bw-accelerate-for-its-mentorship-programme/" target="_blank"><img data-u="images" src="website_assets/images/nternet_in.png" alt="Nternet.in - Business World Accelerator 1st Batch Startups - 247around" /></a>
            </div>

            <div style="display: none;">
                 <a href="http://www.iamwire.com/2015/06/business-world-accelerate-announces-ten-startups-batch/117388" target="_blank"><img data-u="images" src="website_assets/images/iamwire.png"  alt="Iamwire - Business World Accelerator 1st Batch Startups - 247around"/></a>
            </div>

            <div style="display: none;">
                 <a href="https://www.digifire.in/2016/02/26/interview-with-nitin-malhotra-co-founder-at-247around/" target="_blank"><img data-u="images" src="website_assets/images/digifire.png"  alt="Digifire - Nitin Malhotra Interview"/></a>
            </div>
            <div style="display: none;">
                 <a href="http://tvj.co.in/2683-paytm-and-usha-shriram-partner-with-247around-com" target="_blank"><img data-u="images" src="website_assets/images/TVJ-Journal-Repair.png"  alt="TV Veopar Journal"/></a>
            </div>
            
        </div>
        <a href="http://www.jssor.com" style="display:none">Slideshow Maker</a>
    </div>
    <script>
        jssor_1_slider_init();
    </script>
    </div>
    </div>

</body>
</html>
