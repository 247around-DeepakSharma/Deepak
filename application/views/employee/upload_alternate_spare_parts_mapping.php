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
                       <strong>' . $this->session->flashdata('file_error') . '</strong>
                    </div>';
                    }
                    ?>
                <h1 class="page-header">
                    <b> Upload Alternate Spare Parts Details File</b>
                </h1>
                <section>
                    <div class="col-md-6">
                        <form class="form-horizontal" onsubmit="return submit_Form();" id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data" action="file_upload/process_upload_file">
                            <div class="form-group <?php if( form_error('partner_id') ) { echo 'has-error';} ?>">
                                <label for="partner_id" class="col-md-3">Select Partner</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                                </div>
                                <?php echo form_error('partner_id'); ?>
                            </div>
                            <div class="form-group  <?php if (form_error('excel')) {
                                echo 'has-error';
                                } ?>">
                                <label for="excel" class="col-md-3">Upload File</label>
                                <div class="col-md-9">
                                    <input type="file" class="form-control" id="alternate_parts_file"  name="file" required="">
                                    <?php if (form_error('excel')) {
                                        echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                        } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">     
                                    <input type="text"  name="partner_id" value ="247133">
                                    <input type="text"   name="file_type" value ="<?php echo ALTERNATE_SPARE_PARTS_MAPPING ;?>">
                                    <input type="text"   name="redirect_url" value ="upload_alternate_spare_parts_file"> 
                                    <input type="submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 18px;"><b>Download Sample File. Use this file to upload Alternate Spare Parts details.</b></p>
                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/alternate_part_inventory_sample_file.xlsx" class="btn btn-info" target="_blank">Download Sample File</a>
                    </div>
                </section>
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>Alternate Spare Parts File Upload History</h3>
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
    function submitForm() {
        
        var partner_id = $('#partner_id').val();
        var alternate_parts_file = $("#alternate_parts_file").val();
        if(partner_id == '' || partner_id == null){
            alert("Please Select Partner ");
            return false;
        }
        if(alternate_parts_file ==''){
            alert("Please Choose File.");
            return false;
        }
        
        if(partner_id !='' && alternate_parts_file !=''){
                var fd = new FormData(document.getElementById("fileinfo"));
                fd.append("label", "WEBUPLOAD");
                fd.append('partner_id',$('#partner_id').val());                
                fd.append('file_type','<?php echo ALTERNATE_SPARE_PARTS_MAPPING ;?>');
                fd.append('redirect_url','upload_alternate_spare_parts_file');
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
                       
        }
        
    }
    
    $(document).ready(function () {
        show_upload_file_history();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh : true},
            success:function(response){
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
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
                    d.file_type = '<?PHP echo ALTERNATE_SPARE_PARTS_MAPPING ;?>';
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