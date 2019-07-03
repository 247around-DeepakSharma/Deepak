<style>
    #datatable2_info{
    display: none;
    }
    
    #datatable2_filter{
        text-align: right;
    }
    .select2-container--default .select2-selection--single{
        height: 32px;
    }
</style>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
            <?php 
            if(!empty($this->session->flashdata('fail'))){ ?>
               
                 <div class="alert alert-danger">
                 <strong>Failed!</strong>  <?php echo $this->session->flashdata('fail'); ?>
                </div>
            <?php }else if(!empty($this->session->flashdata('details'))){ ?>
                   <div class="alert alert-success">
                   <strong>Success!</strong> Click See details button to see the details because some data may not be updated
                   <?php if(!empty($this->session->flashdata('fail')) || !empty($this->session->flashdata('details'))){ ?>
                             <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal">See details</a>
                       <?php  } ?>
                  </div>
           <?php  }
            ?>
            <h1 class="page-header">
               MSL Excel Upload
            </h1>
          <div id="show_both">
<!--              <form  action="<?php //echo base_url()?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
               <div class="form-group  <?php //if( form_error('file') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-3">Upload Service Price List:</label>
                  <div class="col-md-3">
                     <input type="file" class="form-control"  name="file" >
                     <?php //echo form_error('file'); ?>
                  </div>
                <input class="col-md-2 btn btn-danger btn-sm" type= "submit"  value ="Upload" >  
                
               </div>
            </form>
              <a href="<?php //echo base_url(); ?>BookingSummary/download_latest_file/price" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Download</th>
                                <th>Uploaded By</th>
                                <th>Uploaded Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
              
          <div class="clear"></div>
          <hr style="margin-top:10px; margin-bottom:40px;">-->





        <form method="POST"   enctype="multipart/form-data"  action="<?php echo base_url();  ?>file_upload/process_upload_file"  >
            <div class="row">
                
               <div class="col-md-2">
                    <div class="form-group">
                        <input type="file" required id="msl_excel" name="file"  accept=".xls,.xlsx" />
                    </div>
                </div>
                
                <input type="hidden" name="file_type" value="<?php echo MSL_TRANSFERED_BY_PARTNER_BY_EXCEL; ?>" />
                
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-small btn-primary" id="search"  >Upload</button>

                       
                    </div>
                </div>
            </div>
        </form>

 <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Excel Entries Not Updated</h4>
      </div>
      <div class="modal-body">
            <?php 
            if(!empty($this->session->flashdata('fail'))){
                echo $this->session->flashdata('fail');
            }else{
                echo $this->session->flashdata('details'); 
            }
            ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div> 

<!--            <a href="<?php //echo base_url(); ?>BookingSummary/download_latest_file/appliance" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>-->
            <div class="col-md-12" style="margin-top:20px;">
                <h3>File Upload History</h3>
                <table id="datatable2" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Download</th>
                            <th>Uploaded By</th>
                            <th>Uploaded Date</th>
                            <th>Status</th>
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
 
    var table1;
 

    $(document).ready(function () {

        upload_file_history();
 
        //datatables
       
        
    });
    
    function upload_file_history(){ 
        table1 = $('#datatable2').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
            pageLength: 5,
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function(d){
                    d.file_type = '<?php echo MSL_TRANSFERED_BY_PARTNER_BY_EXCEL; ?>';
                    d.partner_id = $("#partner_id").val();
                }
            },
             //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
    }
 
 
</script>
 