<style>
    #datatable1_info{
    display: none;
    }
    
    #datatable1_filter{
        text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
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
                       <strong>' . $this->session->flashdata('file_success') . '</strong>
                    </div>';
                    }
                    ?>
                <h1 class="page-header">
                    <b> Upload Engineer Incentive File</b>
                </h1>
                <form class="form-horizontal"  id="fileinfo"  name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group  <?php if (form_error('excel')) {
                        echo 'has-error';
                        } ?>">
                        <label for="excel" class="col-md-1">Upload File</label>
                        <div class="col-md-4">
                            <input type="file" class="form-control"  name="file" >
                            <?php if (form_error('excel')) {
                                echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                } ?>
                        </div>
                        <div class="col-md-4">
                            <input type= "submit" onclick="return submitForm();" class="btn btn-success btn-md" id="submit_btn" value ="Upload">
                        </div>
                    </div>
                </form>
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
                            <tr><td colspan="6">No data found</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    var table;
    var partner = '';
    var partner_id = '';
    var partner_source = '';
    var is_file_send_back = '';
    var read_column = '';
    var write_column = '';
    var revert_file_email = '';
    
    function submitForm() {
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('redirect_to','upload_engineer_incentive_file');
        fd.append('file_read_column',read_column);
        fd.append('file_write_column',write_column);
        fd.append('revert_file_email',revert_file_email);
        $.ajax({
            url: "<?php echo base_url() ?>employee/do_background_upload_excel/process_engineer_incentive_file",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false 
        }).done(function (data) {
            location.reload();
        });
        alert('File validation is in progress, please wait....');
        return false;
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
                   // d.file_source = '<?php echo ENGINEER_INCENTIVE_FILE_TYPE; ?>',
                    //d.partner_id= $("#partner_id").val(),
                    d.file_type = '<?php echo ENGINEER_INCENTIVE_FILE_TYPE; ?>';
                }
            },

            columnDefs: [
                {
                    "targets": [0, 1, 2, 3, 4],
                    "orderable": false
                }
            ]
        });
    }
</script>
<?php  
if ($this->session->flashdata('file_error')) {$this->session->unset_userdata('file_error');} 
if ($this->session->flashdata('file_success')) {$this->session->unset_userdata('file_success');}
?>