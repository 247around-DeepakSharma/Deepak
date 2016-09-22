<div id="page-wrapper">
    <div class="container-fluid">
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">
                <h3>Upload Vendor Pincode Mapping Excel </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                    <div  id="success"></div>
                        <?php if(isset($error) && $error !==0) {
                            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $error . '</strong>
                            </div>';
                            }
                            ?>
                        <?php if(isset($sucess) && $sucess !==0) {
                            echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $sucess . '</strong>
                            </div>';
                            }
                            ?>  
                        <form class="form-horizontal" action="<?php echo base_url()?>employee/vendor/process_pincode_excel_upload_form" method="POST" enctype="multipart/form-data">
                            <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                                <label for="excel" class="col-md-1">Pincode Excel</label>
                                <div class="col-md-4">
                                    <input type="file" class="form-control"  name="file" >
                                    <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extensions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                                </div>
                            </div>
                            <div class="form-group  <?php if( form_error('emailID') ) { echo 'has-error';} ?>">
                                <label for="excel" class="col-md-1">Email:</label>
                                <div class="col-md-4">
                                    <input type="email" class="form-control" id="email_id" name="emailID" >
                                    <?php echo form_error('emailID'); ?>
                                </div>
                            </div>
                            <div class="form-group  <?php if( form_error('emailID') ) { echo 'has-error';} ?>">
                                <label for="excel" class="col-md-1">Notes</label>
                                <div class="col-md-4">
                                    <textarea rows="4" id="notes" name ="notes" cols="50" ></textarea>
                                    <?php echo form_error('emailID'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <center>
                                        <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >
                                       <!--  <input type="hidden" value="https://s3.amazonaws.com/bookings-collateral/vendor-pincodes/<?php echo $pincode_mapping_file[0]['file_name']; ?>" id="fileUrl"></input> -->

                                        <a href="#"  onclick="download_pincode_file()" class="btn btn-primary btn-md">Download latest File</a> 
                                        <a href="#" onclick="sendEmail()" class="btn btn-success btn-md" >Send Email</a>
                                    </center>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  function sendEmail(){
    var postdata = {};
    postdata['email'] = $('#email_id').val();
    postdata['notes'] = $('#notes').val();
    postdata['fileUrl'] = $('#fileUrl').val();

    $.ajax({
          type: 'POST',
          url: '<?php echo base_url() ?>employee/vendor/send_email_with_latest_pincode_excel',
          data: postdata,
          success: function (data) {
            console.log(data);
            $("#success").html(data);   
                
          }
        });
  }

    function download_pincode_file(){
    var postdata = {};
    postdata['email'] = $('#email_id').val();
    postdata['notes'] = $('#notes').val();

    $.ajax({
          type: 'POST',
          url: '<?php echo base_url()?>employee/vendor/download_latest_pincode_excel',
          data: postdata,
          success: function (data) {
           
            $("#success").html(data);   
                  
          }
        });
  }
</script>
