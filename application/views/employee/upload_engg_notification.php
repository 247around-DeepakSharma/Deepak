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
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('<?php echo base_url(); ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
    }
</style>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <?php
            if (isset($error) && $error !== 0) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $error . '</strong>
               </div>';
            }
            ?>
            <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
            }
            ?>  
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
            }
            ?>
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
                Upload Engineer Notification Excel
            </h1>
            <div id="show_both">
  <!--              <form  action="<?php //echo base_url() ?>employee/service_centre_charges/upload_service_price_from_excel" method="POST" enctype="multipart/form-data">
                 <div class="form-group  <?php //if( form_error('file') ) { echo 'has-error';} ?>">
                    <label for="excel" class="col-md-3">Upload Service Price List:</label>
                    <div class="col-md-3">
                       <input type="file" class="form-control"  name="file" >
<?php //echo form_error('file');  ?>
                    </div>
                  <input class="col-md-2 btn btn-danger btn-sm" type= "submit"  value ="Upload" >  
                  
                 </div>
              </form>
                <a href="<?php //echo base_url();  ?>BookingSummary/download_latest_file/price" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>
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
                
            <div class="clear"></div>
            <hr style="margin-top:10px; margin-bottom:40px;">-->
                <div class="loader hide"></div>
                <form id="fileinfo"   name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group  <?php if (form_error('file')) {
    echo 'has-error';
} ?>">


                        <div class="col-md-3">
                            <input type="file" class="form-control"  id="fileInput"  name="file" required="">
<?php echo form_error('file'); ?>
                        </div>
                        <input type= "button"  class="col-md-2 btn btn-success btn-sm"  disabled="" id="smbtn" value ="Upload" > 
                        <a class="btn btn-primary btn-sm" style="float:right" target='_blank' href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/misc-images/engg_notify_sample_file.xlsx'>Download Sample File</a>
                    </div>
                </form>
    <!--            <a href="<?php //echo base_url();  ?>BookingSummary/download_latest_file/appliance" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>-->
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
    var table;
    var table1;
    $(document).ready(function () {
        upload_file_history();
    });

    function upload_file_history() {
        table1 = $('#datatable2').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            pageLength: 5,
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function (d) {
                    d.file_type = '<?php echo _247AROUND_ENGINEER_NOTIFICATIONS; ?>';
                }
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3, 4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
    }


$("#fileInput").change(function(){

        var f = this.files[0];
        var flag=false;
        var ext = this.value.match(/\.(.+)$/)[1];
        switch (ext) {
        case 'xlsx':
        case 'xls':
        case 'XLS':
        case 'XLSX':
             flag=true;
        }
        //here I CHECK if the FILE SIZE is bigger than 5 MB (numbers below are in bytes)
        if (f.size > 5242882 || f.fileSize > 5242882 || flag==false)
        {
           //show an alert to the user
           swal("Error!", "Allowed file size exceeded. (Max. 5 MB) and must be Excel", "error");
           //reset file upload control
           this.value = null;
        }else{
          $("#smbtn").removeAttr("disabled");
        }

});


    $("#smbtn").click(function (e) {
        e.preventDefault();
        $(".loader").removeClass('hide');
        var fd = new FormData(document.getElementById("fileinfo"));
        fd.append("label", "WEBUPLOAD");
        fd.append('file_type', '<?PHP echo _247AROUND_ENGINEER_NOTIFICATIONS; ?>');
        fd.append('redirect_url', 'employee/engineer/upload_engg_notification_excel');
        $.ajax({
            url: "<?php echo base_url() ?>upload_file",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false
        }).done(function (data) {
            console.log(data);
            $(".loader").addClass('hide');
            //var result = data.tirm();
            var result = $.trim(data.replace(' ', ''));

            if (result == 'success') {
                swal({title: "Sent", text: "Notifications has been sent !.", type: "success"},
                        function () {
                            $('#fileInput').val(null);
                            table1.ajax.reload(null, false);
                        }
                );

            } else {

                swal({title: "Error !", text: "Check header and extension  of your file !.", type: "error"},
                        function () {
                            $('#fileInput').val(null);
                            table1.ajax.reload(null, false);
                        }
                );


            }

            //  alert();
            //  location.reload();
        });

    });



</script>
<?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>
<?php if ($this->session->userdata('file_error')) {
    $this->session->unset_userdata('file_error');
} ?>