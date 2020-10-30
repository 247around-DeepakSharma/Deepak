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
                    ?>
                <h3 class="page-header">
                    <b> Create Bulk GST DebitNote</b>
                </h3>
                <a class="btn btn-primary btn-sm" style="float:right" target='_blank' href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/sample-file/bulkDebitNoteSampleFile.xlsx'>Download Sample File</a>
                <form class="form-horizontal"  id="fileinfo" action="javascript:void(0)" onsubmit="submitForm()" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    
                    
                    <div class="form-group  <?php if (form_error('excel')) {
                        echo 'has-error';
                        } ?>">
                        <label for="excel" class="col-md-1">Attach File</label>
                        <div class="col-md-4">
                            <input type="file" class="form-control"  name="file" >
                            <?php if (form_error('excel')) {
                                echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                } ?>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-2">
                            <input type= "hidden" id="upload_file_type" value ="<?php echo BULK_DEBIT_NOTE_TAG;?>" name="file_type">
                            <button type= "submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload" >Upload</button>
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
                                <th>Amount Paid</th>
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

    show_upload_file_history();
    
    
    function submitForm() {

        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append("file_type", "<?php echo BULK_DEBIT_NOTE_TAG;?>");
        fd.append("redirect_url", "");

            $.ajax({
                url: "<?php echo base_url() ?>file_upload/process_bulk_debit_note",
                type: "POST",
                data: fd,
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
                processData: false,
                contentType: false,
                success: function (response) {
                    var result = JSON.parse(response);
                    
                    if(result.message);
                    $('body').loadingModal('destroy');
                    table.ajax.reload(null, false);
                    
                }
            });
        return false;
    }
    
    
    
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
                    d.show_amt_paid = 1,
                    d.file_type = '<?php echo BULK_DEBIT_NOTE_TAG; ?>';
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