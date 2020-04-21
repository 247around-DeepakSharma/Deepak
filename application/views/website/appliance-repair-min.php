<!DOCTYPE html><html lang="en"><head><title><?php echo $appliance_details['title'];?></title><meta name="description" content="<?php echo $appliance_details['description'];?>"><meta name="keywords" content="<?php echo $appliance_details['keywords'];?>"> <?php include_once("header-close.php") ?></head><body> <?php include_once("website_assets/js/fb.js") ?> <?php include_once("body-open.php") ?> <?php include_once("body-navbar.php") ?><div class="wrapper" id="atf-<?php echo str_replace(" ", "-", strtolower($appliance_details['service']));?>-services"><div class="container"><div class="header-top"></div><div class="row formBox formSection"><div class="head-small visible-xs-block"><?php echo $appliance_details['h1_text'];?></div><div class="head-call visible-xs-block"> <a href="tel:9555000247"><i class="fa fa-phone phone-style"></i>9555000247</a></div><div class="header-home hidden-xs col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"><h1 class="light"><?php echo $appliance_details['main_headline'];?></h1></div><div class="col-md-12"><div class="row "><form id="bookingForm" method="post" action="<?php echo base_url() ?>main/verify_booking"><div class="form-group"><div class="row"><div class="col-md-2 col-md-offset-1"> <select class="form-control formStyle" name="service" id="appliance" style="padding-right: 0px; width: 100%;"><option selected disabled>Select Appliance</option> <?php foreach($services as $value){?><option value="<?php echo $value['services']?>"><?php echo $value['services'] ?></option> <?php }?> </select></div><div class="col-md-2 col-md-offset-0 form_margin"> <select class="form-control formStyle city" id="city" name="city" style="width:100%;"><option selected disabled>Select City</option> </select></div><div class="col-md-2 col-md-offset-0 form_margin"> <input type="text" pattern="^[A-Z a-z]*[aeiouAEIOU][A-Z a-z]*$" class="form-control" name="name" placeholder="Full Name" required /></div><div class="col-md-2 col-md-offset-0 form_margin"> <input type="text" pattern="[789][0-9]{9}" class="form-control" name="mobile" placeholder="Mobile Number" required /></div><div class="button-book col-md-2 col-md-offset-0 form_margin"> <button id="submitButton" type="submit" class="btn btn-book">Proceed</button></div></div><div class="row"><div class="header-home col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"><h5 style="color:white">Please check <a href="<?php echo base_url()?>charges" target="_blank" style="color: rgb(255,255,255)">Prices</a> to find out 247around service charges for all appliances.</h5></div></div></div></form></div></div></div></div></div><div class="wrapper particular-service"><div class="container-fluid"><div class="row"><div class="col-md-9"><div class="col-sm-3 col-md-12" ><div class="homepage-section-header"><h2><b><?php echo $appliance_details['h1_text'];?></b></h2></div></div><div class="col-md-12"><div class="hidden-xs col-sm-3 col-lg-3 col-md-3"> <?php $string = explode(".", $appliance_details['image'] ); ?> <img class="service-image" src="website_assets/images/<?php echo $string[0]."_177*133.".$string[1];?>" alt="<?php echo $appliance_details['alt_text'];?>"></div><div class="col-xs-12 col-sm-9 col-lg-9 col-md-9 maintext"> <?php echo $appliance_details['content'];?></div></div></div><div style="padding-top: 1cm;" class="mobile-screen hidden-xs fb-page col-md-3 col-sm-10 col-xs-10" data-href="https://www.facebook.com/247around/" data-tabs="timeline" data-width="350" data-height="600" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore "><blockquote cite="https://www.facebook.com/247around/"><a href="https://www.facebook.com/247around/">247around</a></blockquote></div></div></div><div class="cta-how"><div class="all-services how-cta"><span>Book Service</span></div></div><style type="text/css">@media (max-width: 768px){.blog_content_h2 h2{font-size:18px}</style><div class="container blog_content_h2" style="margin-top:45px;"> <?php for($i = 0; $i < $num_blogs; $i++) { ?><div class="col-md-3 col-lg-3 col-sm-6 col-xs-12"><div class="blog-title"><h4> <a href="<?php echo base_url().$blogs[$i]['url']; ?>"><?php echo $blogs[$i]['title']?></a></h4></div><p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo date_format(date_create($blogs[$i]['create_date']), 'jS F Y');?></p><hr><div class="blog-content"><div class="blog-content-img"> <img class="img-responsive" src="<?php echo base_url() ?>website_assets/images/<?php $string = explode(".", $blogs[$i]['file_input'] ); echo $string[0]."_314*252.".$string[1];?>" alt="<?php echo $blogs[$i]['alternate_text']?>"></div><hr><div class="blog-content-text"><p> <?php echo substr($blogs[$i]['content'], 0, 200) . " ..."; ?></p></div><div class="blog-read-more"> <a class="btn btn-primary" href="<?php echo base_url().$blogs[$i]['url']; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a></div></div><hr></div> <?php } ?></div><div class="cta-how"><div class="all-services how-cta"><span>Book Now !!!</span></div></div></div></div><style type="text/css">#bookingForm .form-control-feedback{right:15px}#bookingForm .selectContainer .form-control-feedback{right:25px}.form-group .select2-container .select2-selection--single{height:34px}.form-group .select2-container--default .select2-selection--single .select2-selection__arrow{display:none}.blog_content_h2{min-height:450px}.blog_content_h2 .blog-title{min-height:35px}.blog_content_h2 .blog-content-img{height:250px}.blog_content_h2 .blog-content-img img{max-width:100%;max-height:100%}.blog_content_h2 .blog-content-text{min-height:170px}.blog_content_h2 .blog-read-more{min-height:100px}</style> <script>$(document).ready(function(){$('#bookingForm').formValidation({framework:'bootstrap',icon:{valid:'glyphicon glyphicon-ok',invalid:'glyphicon glyphicon-remove',validating:'glyphicon glyphicon-refresh'},err:{container:'tooltip'},fields:{name:{validators:{notEmpty:{message:'Full Name is required'},blank:{}}},city:{validators:{notEmpty:{message:'City is required'},blank:{}}},mobile:{validators:{notEmpty:{message:'Mobile Number is required'},phone:{country:'IN',message:'Phone number is not valid'},stringLength:{min:10,max:10,message:'Mobile number should be of 10 digits',},blank:{}}},service:{validators:{notEmpty:{message:'Service is required'},blank:{}}},}});});</script> <script type="text/javascript">$(window).scroll(function(){if($(document).scrollTop()>100){$('nav').removeClass('navbar-personalised');}else{$('nav').addClass('navbar-personalised');}});$(window).scroll(function(){if($(document).scrollTop()>100){$('.navlist-add').removeClass('atf-links');}else{$('.navlist-add').addClass('atf-links');}});$(window).scroll(function(){if($(document).scrollTop()>100){$('.navVideo-add').removeClass('video-link');$('.navVideo-add').addClass('video-scrollLink');}else{$('.navVideo-add').removeClass('video-scrollLink');$('.navVideo-add').addClass('video-link');}});$(".how-cta").click(function(){$('html,body').animate({scrollTop:$("#"+"atf-<?php echo str_replace("", "-", strtolower($appliance_details['service']));?>-services").offset().top},1000);});$(document).ready(function(){$(".navbar-toggle").on("click",function(){$(this).toggleClass("active");});});</script> <script type="text/javascript">$("#appliance").select2({minimumResultsForSearch:Infinity});$("#city").select2();</script> <script type="text/javascript">$(document).ready(function(){$('#appliance').on('change',function(){var applianceID=$(this).val();if(applianceID){$.ajax({async:true,type:'POST',url:'<?php echo base_url(); ?>'+'get-city',data:'id='+applianceID,success:function(data){$('#city').val('val',"");$('#city').val('Select City').change();$('#city').select2().html(data);}});}else{$('#city').html('<option value="">Select Appliance First</option>');}});});</script>
