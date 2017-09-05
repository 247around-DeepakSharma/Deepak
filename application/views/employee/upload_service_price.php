<style>
    #datatable1_filter,#datatable1_length,#datatable1_info,
    #datatable2_filter,#datatable2_length,#datatable2_info{
        display: none;
    }
</style>
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
            <?php if($this->session->userdata('error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
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
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Download</th>
                                <th>Uploaded By</th>
                                <th>Uploaded Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
              
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
                <h3>File Upload History</h3>
                <table id="datatable2" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Download</th>
                            <th>Uploaded By</th>
                            <th>Uploaded Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

          </div>
      </div>
   </div>
</div>
<script>
    var table;

        $(document).ready(function () {

            //datatables
            table = $('#datatable1').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: '<?php echo _247AROUND_SF_PRICE_LIST; ?>'}
                },
                //Set column definition initialisation properties.
                columnDefs: [
                    {
                        "targets": [0,1,2,3], //first column / numbering column
                        "orderable": false //set not orderable
                    }
                ]
            });
        });
        
        var table1;

        $(document).ready(function () {

            //datatables
            table1 = $('#datatable2').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: '<?php echo _247AROUND_PARTNER_APPLIANCE_DETAILS; ?>'}
                },
                //Set column definition initialisation properties.
                columnDefs: [
                    {
                        "targets": [0,1,2,3], //first column / numbering column
                        "orderable": false //set not orderable
                    }
                ]
            });
        });
</script>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<?php $this->session->unset_userdata('file_error'); ?>