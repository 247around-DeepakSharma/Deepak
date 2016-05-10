<?php echo "Its View";?>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
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
               
            <h1 class="page-header">
                Upload Service Centre Charges Excel
            </h1>
            
            <form class="form-horizontal" action="<?php echo base_url()?>employee/service_centre_charges/add_service_centre_chrges_from_excel" method="POST" enctype="multipart/form-data">
                <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Charges Excel</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
               </div>
               
                <div class="form-group">
                  <div class="col-md-10">
                    <center>
                         <input type= "submit"  class="btn btn-danger btn-lg" value ="Upload" style="width:17%">                           </center>
                  </div>
               </div>
                
            </form>
             
         </div>
      </div>
   </div>
</div>

<script>
//$("input").tagsinput('services');
</script>