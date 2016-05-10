<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12">
            <?php if(isset($error) && $error !==0) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $error . '</strong>
               </div>';
               }
               ?>
            <?php if(isset($sucess) && $sucess !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $sucess . '</strong>
               </div>';
               }
               ?>  
            <?php if($this->session->userdata('success')) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
               }
               ?>
            <h1 class="page-header">
               Upload Excel File
            </h1>
            <form class="form-inline" action="<?php echo base_url()?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-6">Upload Service Price List</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
               </div>
               <input style="margin-left:60px;" type= "submit"  class="btn btn-danger btn-sm" value ="Upload" >                           
            </form>
            <form style="margin-top:40px;margin-bottom: 40px;" class="form-inline" action="<?php echo base_url()?>employee/service_centre_charges/upload_tax_rate_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-6">Upload Tax Rate List</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
               </div>
               <input style="margin-left:80px;" type= "submit"  class="btn btn-danger btn-sm" value ="Upload" >                           
            </form>
         </div>
      </div>
   </div>
</div>
<?php $this->session->unset_userdata('success'); ?>