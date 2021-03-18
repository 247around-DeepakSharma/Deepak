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
            <div class="col-md-12">
                <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loader.gif" style="display: none;"></center>
            </div>
        </div>
         <?php
            if ($this->session->userdata('file_error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('file_error') . '</strong>
                    </div>';
            }
        ?> 
        <div class="row">
            <div class="col-lg-12">                
                <h1 class="page-header">
                    <b> Upload File</b>
                </h1>
                <section>
                    <div class="col-md-6">
                        <form class="form-horizontal" id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">                            
                            <input type="hidden" name="redirect_url" id="redirect_url" value="add_warranty">
                            <div class="form-group  <?php
                            if (form_error('excel')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="excel" class="col-md-3">Upload File</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="file" required="" accept=".xlsx, .xls, .csv">
                                    <?php
                                    if (form_error('file')) {
                                        echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-3">
                                    <input type="submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 18px;"><b>Download Sample File. Use this file to upload Part Warranty Data.</b></p>
                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/warranty-docs/warranty_plans_sample.xlsx" class="btn btn-info" target="_blank">Download Sample File</a>                    
                   </div>
                </section>
                <hr>
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
    $(document).ready(function () {
        upload_file_history();
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
                    d.file_type = '<?php echo PART_WARRANTY_DETAILS; ?>';
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
    
    function submitForm()  
    {        
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('file_type','<?php echo PART_WARRANTY_DETAILS ;?>');
        fd.append('redirect_url','employee/bulkupload/add_part_type_warranty');
        $.ajax({
            url: "<?php echo base_url() ?>upload_file",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false 
        }).done(function (data) {
            console.log(data);
        }); 
        alert('File validation is in progress, please wait....');
        return false;
    }
</script>
<?php if($this->session->userdata('file_error')){$this->session->unset_userdata('file_error');} ?>
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