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
                    <b> Upload File</b>
                </h1>
                <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group <?php if( form_error('partner_id') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-1">Select Partner</label>
                        <div class="col-md-4">
                            <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                        </div>
                        <?php echo form_error('partner_id'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('file_type') ) { echo 'has-error';} ?>" id="file_type" style="display:none;">
                        <label for="excel" class="col-md-1">Select File Type</label>
                        <div class="col-md-4">
                            <select name="file_type" class="form-control" id="file_type">
                                <option value="" disabled selected>Select File Type</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                        </div>
                         <?php echo form_error('file_type'); ?>
                    </div>
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
                            <input type= "submit"  class="btn btn-success btn-md" value ="Upload" >
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
    //$("input").tagsinput('services');
</script>
<script>
    
    var table;
    var partner = '';
    var partner_id = '';
    var file_type = '';
    var partner_source = '';
    
    function submitForm() {
        if( partner_id === '<?php echo SNAPDEAL_ID?>'){
            var type = $("#file_type :selected").val();
            file_type = partner + '-' + type;
            partner_source = partner + '-' + type + '-excel';
        }
        
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('file_type',file_type);
        fd.append('partner_id',partner_id);
        fd.append('partner_source',partner_source);
        fd.append('redirect_to','upload_partner_booking_file');
        $.ajax({
            url: "<?php echo base_url() ?>employee/do_background_upload_excel/process_upload_file",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false 
        }).done(function (data) {
            alert(data);
        });
        alert('File validation is in progress, please wait....');
    }
    
    $(document).ready(function () {
        show_upload_file_history();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            success:function(response){
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    });
    
    $('#partner_id').on('change',function(){
        partner = $("#partner_id :selected").text();
        partner_id = $(this).val();
        file_type = partner +'-Delivered';
        partner_source = partner+'-delivered-excel';
        
        if(partner_id === '<?php echo SNAPDEAL_ID ?>'){
            $('#file_type').show();
        }else{
            $('#file_type').hide();
            table.ajax.reload();
        }
    });
    
    $('#file_type').on('change',function(){
        var type = $("#file_type :selected").val();
        file_type = partner + '-' + type;
        table.ajax.reload();
    });
    
    function show_upload_file_history(){
        table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            pageLength: 5,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function(d){
                    d.file_type = file_type;
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