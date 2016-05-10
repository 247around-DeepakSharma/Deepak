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
               <h1 class="page-header">
                <i class="fa fa-sign-in"></i>  Ads
            </h1>

              <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-share"></i> Ads
               </li>
            </ol>
   
            <form class="form-horizontal" action="<?php echo base_url()?>ads" method="POST"  enctype="multipart/form-data">
            	 <div class="form-group <?php if( form_error('ads') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">Ads </label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="ads" value = "<?php echo set_value('ads'); ?>" data-role="tagsinput"  >
                     <?php echo form_error('ads'); ?>
                  </div>
               </div>
                <div class="form-group <?php if( form_error('url') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">URL </label>
                  <div class="col-md-6">
                     <input type="url" class="form-control"  name="url" value = "<?php echo set_value('url'); ?>" data-role="tagsinput"  >
                     <?php echo form_error('url'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('file') ) { echo 'has-error';} ?>">
                           <label for="Advertise Photo" class="col-md-2">Advertise photo</label>
                           <div class="col-md-6">
                              <input  class="form-control"  type="file" name="file" >
                           </div>
                           <?php if( form_error('file') ) { echo 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB';} ?>
                        </div>
                <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
        
           </div>
       </div>
   </div>
</div>