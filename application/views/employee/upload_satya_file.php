<style>
    #datatable1_filter,#datatable1_length,#datatable1_info{
    display: none;
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
                    ?>
                <h1 class="page-header">
                    <b> Upload Satya File</b>
                </h1>
                <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="fileinfo"  method="POST" enctype="multipart/form-data">
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
                        <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >
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
    //$("input").tagsinput('services');
</script>
<script>
    function submitForm() {
    
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        $.ajax({
            url: "<?php echo base_url() ?>employee/do_background_upload_excel/upload_satya_file",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false 
        }).done(function (data) {
            alert(data);
        });
        alert('File validation is in progress, please wait....');
    }
    
    var table;
    
    $(document).ready(function () {
        table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            pageLength: 5,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: {file_type: '<?php echo _247AROUND_SATYA_DELIVERED; ?>'}
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3],
                    "orderable": false
                }
            ]
        });
    });
</script>
<?php $this->session->unset_userdata('file_error'); ?>