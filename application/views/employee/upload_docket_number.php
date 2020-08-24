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
                <div class="alert alert-danger alert-dismissible" id="file_error_msg_div" role="alert" style="margin-top:15px; display: none">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                        <strong id="file_error_msg"></strong>
                    </div>
                <div class="alert alert-success alert-dismissible" id="file_success_msg_div" role="alert" style="margin-top:15px; display: none">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                        <strong id="file_success_msg"></strong>
                    </div>
                <h1 class="page-header">
                    <b> Upload File</b>
                </h1>
                <form class="form-horizontal"  id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data">
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
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <input type= "button" onclick="submitForm()"  class="btn btn-success btn-md" id="submit_btn" value ="Upload">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    var table;
    var read_column = '';
    var write_column = '';
    var revert_file_email = '';
    
    function submitForm() { 
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('redirect_to','upload_docket_number_file');
        fd.append('file_read_column',read_column);
        fd.append('file_write_column',write_column);
        fd.append('file_type','upload_docket_number');
        $.ajax({
            url: "<?php echo base_url() ?>file_upload/process_docket_number_file_upload",
            type: "POST",
            beforeSend: function(){
                $('body').loadingModal({
                position: 'auto',
                text: 'Loading Please Wait...',
                color: '#fff',
                opacity: '0.7',
                backgroundColor: 'rgb(0,0,0)',
                animation: 'wave'
                });
            },
            data: fd,
            processData: false,
            contentType: false 
        }).done(function (data) { 
            data = JSON.parse(data);
            if(data.status === true){
                $("#file_success_msg_div").show();
                $("#file_error_msg_div").hide(); 
                $("#file_success_msg").html(data.message);
            }
            else if(data.status === false){
               $("#file_error_msg_div").show();
               $("#file_success_msg_div").hide();
               $("#file_error_msg").html(data.message);
            }
            $('body').loadingModal('destroy');
            table.ajax.reload();
        });
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
                    d.file_type = '<?php echo DOCKET_NUMBER_FILE_TYPE; ?>';
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
