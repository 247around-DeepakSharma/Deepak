<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
                     <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
             <?php if($this->session->userdata('error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error'). '</strong>
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
               <b> Upload Booking File</b>
            </h1>
            
            <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="fileinfo"  method="POST" enctype="multipart/form-data">
                <div class="form-group <?php if( form_error('file_type') ) { echo 'has-error';} ?>">
                    <label for="excel" class="col-md-1">Select File Type</label>
                    <div class="col-md-4">
                        <select name="file_type" class="form-control">
                            <option value="" disabled selected>Select File Type</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                     <?php echo form_error('file_type'); ?>
                </div>
                 <div class="form-group <?php if( form_error('partner_id') ) { echo 'has-error';} ?>">
                    <label for="excel" class="col-md-1">Select Partner</label>
                    <div class="col-md-4">
                        <select name="partner_id" class="form-control">
                            <option value="" disabled selected>Select Partner</option>
                            <option value="247030">Jeeves</option>
<!--                            <option value="3">Paytm</option>
                            <option value="247027">Paytm Mall</option>
                            <option value="1">Sanpdeal</option>-->

                        </select>
                    </div>
                    <?php echo form_error('partner_id'); ?>
                </div>
                <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Delivered Products For Paytm</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('file') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
                  
               </div>
                 <input type= "submit"  class="btn btn-primary btn-md col-md-offset-2" value ="Upload" >    
            </form>
             
         </div>
      </div>
   </div>
</div>

<script>
function submitForm() {

  var fd = new FormData(document.getElementById("fileinfo"));
  fd.append("label", "WEBUPLOAD");
  $.ajax({
      url: "<?php echo base_url()?>employee/upload_booking_file",
      type: "POST",
      data: fd,
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
  }).done(function( data ) {
     //console.log(data);
    //alert(data);
    //location.reload();

  });
    alert('File upload will continue in the background...');
    //return false;
  //window.open('<?php echo base_url(); ?>employee/user');
}
</script>

 <?php $this->session->unset_userdata('error'); ?>