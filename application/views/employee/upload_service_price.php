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
              <form class="col-md-12" action="<?php echo base_url()?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Service Price List:</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                <input class="col-md-1 btn btn-danger btn-sm" type= "submit"  value ="Upload" >                           
               </div>
            </form>
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
            <form class="col-md-12" action="<?php echo base_url()?>employee/service_centre_charges/upload_partner_appliance_details_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Partner Appliance Details:</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                <input type= "submit"  class="col-md-1 btn btn-danger btn-sm" value ="Upload" >                           
               </div>
            </form>
          </div>
          
          <div id="only_price_excel">
              <form class="col-md-12" action="<?php echo base_url()?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Service Price List:</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                  <input type="hidden" name="flag" value="1" >
                <input class="col-md-1 btn btn-danger btn-sm" type= "submit"  value ="Upload" >                           
               </div>
            </form>
          </div>
          <div id="only_appliance_excel">
              <form class="col-md-12" action="<?php echo base_url()?>employee/service_centre_charges/upload_partner_appliance_details_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Partner Appliance Details:</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                     <?php echo form_error('file'); ?>
                  </div>
                  <input type="hidden" name="flag" value="1" >
                <input type= "submit"  class="col-md-1 btn btn-danger btn-sm" value ="Upload" >                           
               </div>
            </form>
          </div>
      </div>
   </div>
</div>
<script>
    var excel_data = "<?php echo $data ?>";
    if(excel_data === 'price_excel'){
        $('#show_both').hide();
        $('#only_price_excel').show();
        $('#only_appliance_excel').hide();
    }else if (excel_data === 'appliance_excel'){
        $('#show_both').hide();
        $('#only_price_excel').hide();
        $('#only_appliance_excel').show();
    }else{
        $('#show_both').show();
        $('#only_price_excel').hide();
        $('#only_appliance_excel').hide();
    }
     
</script>
<?php $this->session->unset_userdata('success'); ?>