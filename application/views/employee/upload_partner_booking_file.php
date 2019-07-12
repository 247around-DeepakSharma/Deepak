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
                <div class="alert alert-info alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong> If partner name is missing in dropdown, Please contact tech team it needs to be mapped first.</strong>
                </div>
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
                            <select name="partner_source" class="form-control" id="partner_source">
                                <option value="" disabled selected>Select File Type</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
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
                            <input type= "hidden" id="upload_file_type" value ="" name="file_type">
                            <input type= "submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload" disabled="">
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
                                <th>Revert File</th>
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

   <!-- This model is used to view revert file detail -->
    <div id="revert_file_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

       <!-- Modal content-->
       <div class="modal-content">
        <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
           <h4 class="modal-title" align="center">Revert File Detail</h4>
            </div>
           <div class="modal-body" style="min-height: 110px;">
               <div class="">
                <table class="table table-bordered table-condensed">
                   <thead><th>Subject</th><th>From</th><th>To</th><th>Cc</th><th>Action</th></thead>
                    <tbody></tbody>
                </table>
               </div>
           </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         </div>
       </div>

     </div>
   </div>
    <!-- End -->

<script>
    //$("input").tagsinput('services');
</script>
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
        if( partner_id === '<?php echo SNAPDEAL_ID?>'){
            var type = $("#partner_source :selected").val();
            file_type = partner + '-' + type;
            partner_source = partner + '-' + type + '-excel';
        }
        
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('partner_id',partner_id);
        fd.append('partner_source',partner_source);
        fd.append('redirect_to','upload_partner_booking_file');
        fd.append('is_file_send_back',is_file_send_back);
        fd.append('file_read_column',read_column);
        fd.append('file_write_column',write_column);
        fd.append('revert_file_email',revert_file_email);
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
       // show_upload_file_history();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_booking_file_upload_partner',
            success:function(response){
                $('#partner_id').html(response).change();
                $('#partner_id').select2();
            }
        });
    });
    
    $('#partner_id').on('change',function(){
        
        partner = $("#partner_id :selected").text();
        partner_id = $(this).val();
        
         get_partner_file_details();
      //  show_upload_file_history();
      //  table.ajax.reload();
        
        if(partner_id === '<?php echo SNAPDEAL_ID ?>' || partner_id === '<?php echo PAYTM_ID ?>'){
            $('#file_type').show();
        }else{
            $('#file_type').hide();
        }
    });
    
    $('#file_type').on('change',function(){
        var type = $("#file_type :selected").val();
        var file_type = partner + '-' + type;
        $('#upload_file_type').val(file_type);
        if(table == null){ 
            show_upload_file_history();
        }
        else{ 
            table.ajax.reload();
        }
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
                    d.file_source = 'partner_file_upload',
                    d.partner_id= $("#partner_id").val(),
                    d.file_type = get_upload_file_type();
                }
            },

            columnDefs: [
                {
                    "targets": [0, 1, 2, 3,4,5],
                    "orderable": false
                }
            ]
        });
    }






    
    function get_partner_file_details(){
        if(partner_id){



           // alert();


            $.ajax({
                type:'POST',
                url:'<?php echo base_url();?>employee/partner/get_partner_file_details',
                data:{partner_id:partner_id},
                dataType:'json',
                success:function(res){
                    if(res.msg === 'success'){
                        $('#upload_file_type').val(res.data.file_type);
                        partner_source = res.data.file_type+"-excel";
                        read_column = res.data.order_id_read_column;
                        write_column = res.data.booking_id_write_column;
                        is_file_send_back = res.data.send_file_back;
                        revert_file_email = res.data.revert_file_to_email;
                        $('#submit_btn').attr('disabled',false);
                        if(table == null){ 
                            show_upload_file_history();
                        }
                        else{
                            table.ajax.reload();
                        }
                    }else if(res.msg === 'failed'){
                        alert("Select Correct Partner");
                    }else{
                        alert("Select Correct Partner");
                    }
                }
            });
        }
    }
    
    function get_upload_file_type(){
        var upload_file_type = $('#upload_file_type').val();
        return upload_file_type;
    }
    
    function view_revert_file(id){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/upload_booking_file/get_revert_file_details',
            data:{id:id},
            dataType:'json',
            success:function(res){
                var html = "<tr><td style='word-break: break-all'>"+res[0]['revert_file_subject']+"</td>\n\
                                <td style='word-break: break-all'>"+res[0]['revert_file_from']+"</td>\n\
                                <td style='word-break: break-all'>"+res[0]['revert_file_to']+"</td>\n\
                                <td style='word-break: break-all'>"+res[0]['revert_file_cc']+"</td>\n\
                                <td><a class='btn btn-primary btn-xs' target='_blank' href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/"+res[0]['revert_file_name']+"'>Download</a></td>\n\
                            </tr>";
                $("#revert_file_model").find("table tbody").html(html);
            }
        });
    }
</script>
<?php  if ($this->session->flashdata('file_error')) {$this->session->unset_userdata('file_error');} ?>