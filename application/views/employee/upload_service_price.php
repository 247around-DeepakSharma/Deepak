<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
            <?php if(isset($error) && $error !==0) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $error . '</strong>
               </div>';
               }
               ?>
            <?php if(isset($sucess) && $sucess !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $sucess . '</strong>
               </div>';
               }
               ?>  
            <?php if($this->session->userdata('success')) {
               echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
               }
               ?>
          <?php if($this->session->flashdata('file_error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->flashdata('file_error') . '</strong>
               </div>';
               }
               ?>
            <h1 class="page-header">
               Upload Excel File
            </h1>
          <div id="show_both">
              <form  action="<?php echo base_url()?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Service Price List:</label>
                  <div class="col-md-3">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                <input class="col-md-2 btn btn-danger btn-sm" type= "submit"  value ="Upload" >  
                
               </div>
            </form>
              <a href="<?php echo base_url(); ?>BookingSummary/download_latest_file/price" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>
              <div class="col-md-12" style="margin-top:20px;">
                  <h3> Service Price List History</h3>
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
                            <?php $sn= 1;foreach ($SF_Price_List as $value){ ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><a href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $value['file_name']?>'><div><?php echo $value['file_name']?></div></a></td>
                                <td><?php echo $value['agent_name']; ?></td>
                                <td><?php echo $value['upload_date']; ?></td>
                            </tr>
                            <?php $sn++;} ?>
                        </tbody>
                    </table>
                </div>
              <!--          <div class="clear"></div>
            <form class="col-md-12" action="<?php echo base_url()?>employee/service_centre_charges/upload_tax_rate_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Tax Rate List:</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                <input type= "submit"  class="col-md-1 btn btn-danger btn-sm" value ="Upload" >                           
               </div>
            </form>-->
          <div class="clear"></div>
          <hr style="margin-top:10px; margin-bottom:40px;">
            <form action="<?php echo base_url()?>employee/service_centre_charges/upload_partner_appliance_details_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Partner Appliance Details:</label>
                  <div class="col-md-3">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                <input type= "submit"  class="col-md-2 btn btn-danger btn-sm" value ="Upload" > 
                
               </div>
            </form>
          <a href="<?php echo base_url(); ?>BookingSummary/download_latest_file/appliance" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>
          <div class="col-md-12" style="margin-top:20px;">
              <h3> Partner Appliance List History</h3>
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
                      foreach ($Partner_Appliance_Details as $value) { ?>
                          <tr>
                              <td><?php echo $sn1; ?></td>
                              <td><a href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $value['file_name']?>'><div><?php echo $value['file_name']?></div></a></td>
                              <td><?php echo $value['agent_name']; ?></td>
                              <td><?php echo $value['upload_date']; ?></td>
                          </tr>
                        <?php $sn1++;} ?>
                  </tbody>
              </table>
          </div>

          </div>
      </div>
   </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('file_error'); ?>