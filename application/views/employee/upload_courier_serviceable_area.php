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
    .modal-body{
        padding: 0px;
    }
</style>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
            <?php 
            if(!empty($this->session->userdata('fail'))){ ?>
               
                 <div class="alert alert-danger">
                 <strong>Failed!</strong> Click View Error button to see the details <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal"> View Error</a>
                </div>
            <?php }else if(!empty($this->session->userdata('details'))){ ?>
                   <div class="alert alert-dangers">
                   <strong>Success!</strong> Click See details button to see the details because some data may not be updated
                   <?php if(!empty($this->session->userdata('fail')) || !empty($this->session->userdata('details'))){ ?>
                             <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal">See details</a>
                       <?php  } ?>
                  </div>
           <?php  }
            ?>
            <h1 class="page-header">
               Upload Courier Serviceable Area
               <a href="<?php echo "https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/vendor-partner-docs/upload-courier-serviceable-area-sample.xlsx' ?>" name="download-sample-file" class="btn btn-primary" style="float:right;">Download Sample File</a>
            </h1>
          <div id="show_both">
              <form method="POST" id="upload_courier_serviceable_area_form"  enctype="multipart/form-data"  action="<?php echo base_url(); ?>file_upload/process_upload_file" data-preview="true" >
                  <div role="tabpanel" class="tab-pane active" id="onMsl">
                      <div class="row">
                          <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="x_panel" style="margin-top: 0px;">

                                  <div class="x_content">
                                      <div class="loader"></div>                            
                                      <div class="form-box">
                                          <div class="form-group">
                                             <label class="col-xs-3 control-label"> Upload Courier Serviceable Area File</label>
                                              <div class="col-md-4">
                                                  <div class="form-group">
                                                      <input type="file" class="form-control"  id="serviceable_excel" name="file" required  accept=".xls,.xlsx"/>
                                                  </div>
                                              </div>
                                              <input type="hidden" name="file_type" value="<?php echo UPLOAD_COURIER_SERVICEABLE_AREA_EXCEL_FILE; ?>" />
                                              <input type="hidden" name="redirect_url" id="redirect_url" value="employee/inventory/upload_courier_serviceable_area_file"> 
                                              <button type="submit" class="btn btn-small btn-success" id="button_upload_file">Upload</button> 
                                          </div>
                                      </div>
                                  </div>
                              </div>
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
                              if (!empty($this->session->userdata('fail'))) {
                                  echo $this->session->userdata('fail');
                              } else {
                                  echo $this->session->userdata('details');
                              }
                            ?>
                              <br>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                      </div>
                  </div>
              </div> 

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
<?php 
    $this->session->unset_userdata('fail');
    $this->session->unset_userdata('details');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
<script>

    var table1;
    $(document).ready(function () {
        upload_file_history();
        //datatables
        $("#upload_courier_serviceable_area_form").submit(function(){
            var file = $("#serviceable_excel").val();
            if(file == ''){
                alert("Please select courier serviceable area file.");
              return false;  
            }
            $('#button_upload_file').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        });       
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
                    d.file_type = '<?php echo UPLOAD_COURIER_SERVICEABLE_AREA_EXCEL_FILE; ?>';
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
       
    $("body").on("click", "#button_upload_file", function () {
        var allowedFiles = [".xls", ".xlsx"];
        var fileUpload = $("#serviceable_excel");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:()])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.val().toLowerCase())) {
            alert("Please upload files having extensions:(" + allowedFiles.join(', ') + ") only.");
            return false;
        }
    });     
    
</script>
 