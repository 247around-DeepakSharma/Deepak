<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
         
               <?php if(isset($sucess) && $sucess !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $sucess . '</strong>
               </div>';
               }
               ?>
               
               <?php foreach ($services as $data) ?>
            <h1 class="page-header">
             <img src="https://d28hgh2xpunff2.cloudfront.net/service-320x252/<?php if(isset($data['service_image'])) { echo $data['service_image'] ;} ?>" class="img-circle  "  style="width:80px; height:80px;">  <?php if (isset($data['services'])) {echo $data['services']; }?>
            </h1>
            
            <form class="form-horizontal" action="<?php echo base_url()?>service/updateService/<?php if(isset($data['id'])) { echo $data['id'] ;} ?>/<?php echo $off;?>/<?php echo $page; ?>" method="POST" enctype="multipart/form-data">
               <div class="form-group <?php if( form_error('services') ) { echo 'has-error';} ?>">
                  <label for="service" class="col-md-2">Services</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="services" value = "<?php if (isset($data['services'])) {echo $data['services']; }?>"   >
                     <?php echo form_error('services'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('distance') ) { echo 'has-error';} ?>">
                  <label for="distance" class="col-md-2">Distance</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="distance" value = "<?php if (isset($data['distance'])) {echo $data['distance']; }?>"  >
                     <?php echo form_error('distance'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('keywords') ) { echo 'has-error';} ?>">
                  <label for="distance" class="col-md-2">Keywords</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="keywords" value = "<?php if (isset($data['keywords'])) {echo $data['keywords']; }?>" data-role="tagsinput">
                     <?php echo form_error('keywords'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('service_image') ) { echo 'has-error';} ?>">
                  <label for="service photo" class="col-md-2">Service Icon</label>
                  <div class="col-md-6">
                     <input type="file" class="form-control"  name="service_image" >
                      <?php if( form_error('service_image') ) { echo 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg","JPG","JPEG","bmp","BMP","GIF","PNG". Maximum file size is 2MB';} ?>
                  </div>
               </div>
                <!--<div class="form-group  <?php if( form_error('image') ) { echo 'has-error';} ?>">
                  <label for="service photo" class="col-md-2">Image</label>
                  <div class="col-md-6">
                     <input type="file" class="form-control"  name="image" >
                      <?php if( form_error('image') ) { echo 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg","JPG","JPEG","bmp","BMP","GIF","PNG". Maximum file size is 2MB';} ?>
                  </div>
               </div> -->
               <div class="form-group">
                  <div class="col-md-10">
                     <center><a href="<?php echo base_url()?>service/viewservices/<?php echo $off;?>/<?php echo $page;?>" class="btn btn-success btn-lg" style="margin: 2%;
                                 margin-left: -17%;">Back</a><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:17%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>



    <script src="<?php echo base_url();?>js/bootstrap-tagsinput.js"></script>
    <script src="<?php echo base_url();?>js/bootstrap-tagsinput-angular.js"></script>
   <link href="<?php echo base_url()?>css/bootstrap-tagsinput.css" rel="stylesheet">