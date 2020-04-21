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
                <?php if($this->session->userdata('error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
               }
               ?>
            <h1 class="page-header">
               Upload Excel File
            </h1>
          
            <form class="form-horizontal" action="<?php echo base_url()?>employee/partner_booking/upload_partner_booking" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Partner Booking</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
               </div>
               <div class="form-group  <?php if( form_error('partner') ) { echo 'has-error';} ?>">
                  <label for="Service" class="col-md-3">Select Partner</label>
                  <div class="col-md-4">
                     <select class="js-example form-control" name ="partner"  >
                        <option selected disabled>-----------Select Any One------------</option>
                           <?php 
                              foreach ($source as $sourcecode) {     
                              ?>
                        <option value = "<?php echo $sourcecode['partner_id']?>"  >
                           <?php echo $sourcecode['source'];?>
                        </option>
                        <?php } ?>
                     </select>
                     <?php echo form_error('partner'); ?>
                  </div>
               </div>
               <div class="col-md-offset-2 col-md-6">
                  <input style="margin-left:60px;" type= "submit"  class="btn btn-danger btn-sm" value ="Upload" >     
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<script>
   $(".js-example").select2();
</script>
<?php if($this->session->userdata('success')) {
        $this->session->unset_userdata('success'); 
      }  else if($this->session->userdata('error')){
           $this->session->unset_userdata('error');
      }
?>