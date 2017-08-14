<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
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
               <b> Upload Delivered Products for paytm Excel</b>
            </h1>
            
            <form class="form-horizontal" action="<?php echo base_url()?>employee/bookings_excel/upload_booking_for_paytm" method="POST" enctype="multipart/form-data">
                <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Delivered Products For Paytm</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
                   <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >    
               </div>
                
            </form>
            
             
            <div class="col-md-12" style="margin-top:20px;">
              <h3>File Upload History</h3>
              <table class="table table-bordered table-hover table-responsive">
                  <thead>
                      <tr>
                          <th>S.No.</th>
                          <th>Download</th>
                          <th>Uploaded By</th>
                          <th>Uploaded Date</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php $sn1 = 1;
                      foreach ($paytm_delivered as $value) { ?>
                          <tr>
                              <td><?php echo $sn1; ?></td>
                              <td><a href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $value['file_name']?>'><div><?php echo $value['file_name']?></div></a></td>
                              <td><?php echo $value['agent_name']; ?></td>
                              <td><?php echo date('d-F-Y' , strtotime($value['upload_date'])); ?></td>
                          </tr>
                        <?php $sn1++;} ?>
                  </tbody>
              </table>
          </div> 
         </div>
      </div>
   </div>
</div>

<script>
//$("input").tagsinput('services');
</script>

 <?php $this->session->unset_userdata('error'); ?>