<style>
    #partner_royalty_file_upload_table_info{
    display: none;
    }
    
    #partner_royalty_file_upload_table_filter{
        text-align: right;
    }
    .select2-container--default .select2-selection--single{
        height: 32px;
    }
</style>
<div id="page-wrapper">
   <div class="container-fluid">
        <div class="row">
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
            <div class="">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-download"></i> Download Booking File to Calculate Royalty</h2>
                    </div>
                    <div class="panel-body">
                    <form action="<?php echo base_url(); ?>employee/user_invoice/download_bookings_for_partner_royalty"  method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="col-md-4">
                            <select class="form-control" id="partner_id" required="" name="partner_id">
                                <option value="" disabled="">Select Partner</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" placeholder="Slect Booking Close Date" class="form-control" id="close_date" value="" name="close_date"/>
                        </div>
                        <div class="col-md-3">
                            <input type= "submit" onclick="return download_booking_file()" class="form-control btn btn-primary btn-sm" value ="Download" > 
                        </div>
                    </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
       <div class="row">
            <br>
            <div class="">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-upload"></i> Upload Partner Royalty File </h2>
                    </div>
                    <div class="panel-body">
                        <form id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <select class="form-control" id="upload_partner_id" required="" name="upload_partner_id">
                                        <option value="" disabled="">Select Partner</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-6" style="padding: 0px;">
                                    <input type="text" class="form-control" id="invoice_id" placeholder="Enter Invoice Id">
                                </div>
                                <div class="col-md-6" style="padding-right: 0px;">
                                    <input type="file" class="form-control"  name="file" >
                                    <?php echo form_error('file'); ?>
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <input type= "button" onclick="submitForm();"  class="form-control btn btn-success btn-sm" value ="Upload" > 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>  
       <div class="row">
           <br>
            <div class="">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-history"></i> File Upload History </h2>
                    </div>
                    <div class="panel-body">
                        <table id="partner_royalty_file_upload_table" class="table table-striped table-bordered table-hover" style="width: 100%;">
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
   </div>
</div>
<script>
    var partner_royalty_file_upload_table;
    $('#partner_id, #upload_partner_id').select2({
        placeholder:'Select Partner',
        allowClear:true
    });
    
    $(document).ready(function () {
        $('input[name="close_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                 cancelLabel: 'Clear'
            }
        });
        
        $('input[name="close_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));  
        });
        
        $('input[name="close_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val("");
        });
        
        get_partner_details();
        
        partner_royalty_file_upload_table = $('#partner_royalty_file_upload_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
            pageLength: 5,
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: {file_type: '<?php echo PARTNER_ROYALTY_FILE_TYPE; ?>'}
            },
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
    });
    
    function download_booking_file(){
        var partner_id = $("#partner_id").val();
        var close_date = $("#close_date").val();
        if(!partner_id){
            alert("Please Select Partner");
            return false;
        }
        else if(!close_date){
            alert("Please Select Close Date");
            return false;
        }
        else{ 
            return true;
        }
    }
    
    function submitForm() { 
        if($('#invoice_id').val() && $('#upload_partner_id').val()){
            var fd = new FormData(document.getElementById("fileinfo"));
                fd.append('partner_id',$('#upload_partner_id').val());
                fd.append('invoice_id',$('#invoice_id').val());
                fd.append('file_type','<?PHP echo PARTNER_ROYALTY_FILE_TYPE; ?>');
                $.ajax({
                    url: "<?php echo base_url() ?>file_upload/process_partner_royalty_file_upload",
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
                }).done(function (response) {
                    data = JSON.parse(response);
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
                    partner_royalty_file_upload_table.ajax.reload();
                });
        }else{
            alert("Please Select Partner and Enter Invoice Id");
            return false;
        }
    }
    
    function get_partner_details(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            success: function (response) {
                $('#partner_id').html(response);
                $("#upload_partner_id").html(response);
            }
        });
    }

</script>
