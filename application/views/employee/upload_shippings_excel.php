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
               <b> Upload Shipped Products Excel</b>
            </h1>
              <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="fileinfo"  method="POST" enctype="multipart/form-data">
          
                <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Shipped Products Excel</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
                   <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >    
               </div>
                
            </form>
             
         </div>
      </div>
   </div>
</div>

<script>
//$("input").tagsinput('services');
</script>
<script>
function submitForm() {
           
  var fd = new FormData(document.getElementById("fileinfo"));
  fd.append("label", "WEBUPLOAD");
  $.ajax({
      url: "<?php echo base_url()?>employee/do_background_upload_excel/upload_snapdeal_file/shipped",
      type: "POST",
      data: fd,
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
  }).done(function( data ) {
    console.log(data);
      
  });
  alert('File is under Process');
  //window.location.assign("<?php echo base_url(); ?>employee/user");
  //window.open('<?php echo base_url(); ?>employee/user');     
  
}
</script>