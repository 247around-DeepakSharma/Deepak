<style>
    #datatable1_info{
    display: none;
    }
    
    #datatable1_filter{
        text-align: right;
    }
    .select2-container .select2-selection--single{
        height: 34px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-lg-12">
            <div class="x_panel">
                <?php
                    if ($this->session->flashdata('file_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('file_error') . '</strong>
                    </div>';
                    }
                    
                    if ($this->session->flashdata('file_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('file_error') . '</strong>
                    </div>';
                    }
                    ?>
                <div class="x_title">
                    <h1 class="page-header">
                        <b> Upload Serviceable BOM File</b>
                    </h1>
                     <a class="btn btn-info btn-sm" style="float:right" target='_blank'  href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/Part_model_mapping_sample.xlsx'>Download Sample File</a>
                    <section>
                        <div class="col-md-6">
                            <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="fileinfo"  method="POST" enctype="multipart/form-data">
                                <input type="hidden" id="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>">
                                <div class="form-group  <?php if (form_error('excel')) {
                                    echo 'has-error';
                                    } ?>">
                                    <label for="excel" class="col-md-3">Upload File</label>
                                    <div class="col-md-9">
                                        <input type="file" class="form-control"  name="file" >
                                        <?php if (form_error('excel')) {
                                            echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                            } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <input type= "submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-12" style="margin-top:20px;">
                        <h3>File Upload History  </h3>
                        <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Download </th>
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
</div>
<script>
    
    var table;
    $('#service_id').select2();
    function submitForm() {
        
        if($('#partner_id').val()){
            var fd = new FormData(document.getElementById("fileinfo"));
                fd.append("label", "WEBUPLOAD");
                fd.append('partner_id',$('#partner_id').val());
                fd.append('file_type','<?PHP echo PARTNER_BOM_FILE; ?>');
                fd.append('redirect_url','partner/inventory/upload_bom_file');
                $.ajax({
                    url: "<?php echo base_url() ?>upload_file",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false 
                }).done(function () {
                    //console.log(data);
                });
                alert('File validation is in progress, please wait....');
            
        }else{
            alert("No Partner Selected");
            return false;
        }
        
    }
    
    $(document).ready(function () {
        show_upload_file_history();
    });
   
    function show_upload_file_history(){
        table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
            pageLength: 5,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function(d){
                    d.file_type = '<?PHP echo PARTNER_BOM_FILE ;?>',
                    d.partner_id = $("#partner_id").val();
                }
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3,4],
                    "orderable": false
                }
            ]
        });
    }
</script>
<?php  if ($this->session->flashdata('file_error')) {$this->session->unset_userdata('file_error');} ?>
<?php  if ($this->session->flashdata('file_success')) {$this->session->unset_userdata('file_success');} ?>