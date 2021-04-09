<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->
<script src="<?php echo base_url()?>js/croppie.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>css/croppie.css">

<div id="page-wrapper" >
    <div class="container">
        <div class="row">
            <div class="panel-body">
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>GST Update Form</b></div>
                    <div class="panel-body form-horizontal">
                        <form id="gst_form" action="<?php echo base_url(); ?>service_center/process_gst_update" method="POST" enctype="multipart/form-data">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-3">
                                <div  class="form-group <?php if( form_error('company_name') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">Company Name</label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="company_name" name="company_name" value = "<?php if(isset($company_name) && !empty($company_name)){ echo $company_name;}else{echo set_value("company_name");}?>" placeholder="Company Name" required>
                                        <?php echo form_error('company_name'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('company_address') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">Company Address</label>
                                    <div class="col-md-8">
                                        
                                        <textarea class="form-control" name="company_address" id="company_address" required placeholder="Company Address"><?php if(isset($company_address) && !empty($company_address)){ echo $company_address;}else{echo set_value("company_address");}?></textarea>
                                        <?php echo form_error('company_address'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('pan_number') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">Company PAN</label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" style="text-transform:uppercase"  id="pan_number" name="pan_number" value = "<?php if(isset($company_address) && !empty($company_pan_number)){ echo $company_pan_number;}else{echo set_value("pan_number");}?>" placeholder="Company Pan Number" >
                                        <?php echo form_error('pan_number'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('is_gst') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">GST Registration Done?</label>
                                    <div class="col-md-8">
                                        <label class="radio-inline">
                                            <input type="radio" onclick="is_gst_number(this);" name="is_gst" value="1" <?php if(isset($is_gst) && $is_gst == 1) { echo 'checked'; }?> required>Yes
                                       </label> 
                                        <label class="radio-inline">
                                            <input type="radio" onclick="is_gst_number(this);" name="is_gst" value="0" <?php if(isset($is_gst) && $is_gst !== 1) { echo 'checked'; }?> required>No
                                        </label>
                                         <?php echo form_error('is_gst'); ?>
                                    </div>
                                </div>    
                                    
                                 <div  class="form-group <?php if( form_error('gst_number') ) { echo 'has-error';} ?>">
                                    <label  for="gst_no" class="col-md-4">Company GST No</label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" style="text-transform:uppercase"  id="gst_no" name="gst_number" value = "<?php if(isset($company_gst_number) && !empty($company_gst_number)){ echo $company_gst_number;}else{echo set_value("gst_number");}?>" placeholder="Company GST Number" <?php if(isset($is_gst) && !empty($is_gst)) { echo 'required';}else { echo 'disabled';}?>>
                                        <?php echo form_error('gst_number'); ?>
                                    </div>
                                    
                                </div>
                                 <div  class="form-group <?php if( form_error('file') ) { echo 'has-error';} ?>">
                                    <label  for="gst_certificate_file" class="col-md-4">Company GST File</label>
                                    <div class="col-md-6">
                                        <input  type="file" class="form-control" id="gst_certificate_file" name="file" <?php if(isset($is_gst) && !empty($is_gst)) { echo 'required';}else { echo 'disabled';}?>>
                                        <?php echo form_error('file'); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if(isset($gst_certificate_file) && !empty($gst_certificate_file)) { ?> 
                                        <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $gst_certificate_file; ?>" title="View Image"><i class="fa fa-eye"></i></a>
                                        <?php }else{ ?> 
                                        <img src="<?php echo base_url();?>images/view_image.png" width="50%;" title="Image Not Uploaded">
                                        <?php }?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('signature_file') ) { echo 'has-error';} ?>">
                                    <label  for="signature_file" class="col-md-4">Signature Image File</label>
                                    <div class="col-md-6">
                                        <input  type="file"   class="form-control crop_image" id="signature_file" name="signature_file" <?php if(isset($signature_file) && empty($signature_file)) { echo 'required';}?>>
                                        <span id = "file_format" style = "color:red">(only upload file with  "png "jpg", "jpeg", "JPG", "JPEG", "bmp", "BMP", "gif", "GIF", and "PNG" extension)</span>
                                        <?php echo form_error('signature_file'); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if(isset($signature_file) && !empty($signature_file)) { ?> 
                                        <input type="hidden" name="is_signature_aval" id="is_signature_aval" value="1">
                                        <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $signature_file; ?>" title="View Image" style="font-size:22px;"><i class="fa fa-eye"></i></a>
                                        <?php }else{ ?>
                                        <input type="hidden" name="is_signature_aval" id="is_signature_aval" value="0">
                                        <img src="<?php echo base_url();?>images/view_image.png" width="50%;" title="Image Not Uploaded">
                                        <?php }?>
                                    </div>
                                </div>
                                Scanned Image of Signature of Authorized Signatory (required for system-generated invoices)
                            <input type="hidden" id="cropped_image_file" name="cropped_image">
                            <div id="uploadimageModal" class="modal" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Upload & Crop Image</h4>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-8 text-center">
                                                      <div id="image_demo" style="width:300px; margin-top:30px"></div>
                                                </div>
                                                <div class="col-md-4" style="padding-top:30px;">
                                                    <br />
                                                    <br />
                                                    <br/>
                                                      <div class="btn btn-success " id="crop_image">Crop & Upload Image</div>
                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <script>  
                            $(document).ready(function(){

                                $image_crop = $('#image_demo').croppie({
                                enableExif: true,
                                viewport: {
                                  width:250,
                                  height:100,
                                  type:'square' 
                                },
                                boundary:{
                                  width:300,
                                  height:300
                                }
                              });

                              $('#signature_file').on('change', function(){
                                var reader = new FileReader();
                                reader.onload = function (event) {
                                  $image_crop.croppie('bind', {
                                    url: event.target.result
                                  })
                                }
                                reader.readAsDataURL(this.files[0]);
                            
                                var size = this.files[0].size;
                                if (size > 5000000 || size < 10000) { 
                                alert("File must be between the size of 10KB to 5MB");
                                $('#signature_file').val('');
                                return false; 
                                                            
                                }                                 
                                $('#uploadimageModal').modal('show');
                                
                              }); 
                    
                             $('#crop_image').click(function(event){
                                  $image_crop.croppie('result', {
                                  type: 'canvas',
                                  size: 'viewport'
                                }).then(function(response){
                                  $.ajax({
                                    url:"<?php  echo base_url(); ?>employee/vendor/signature_file",
                                    type: "POST",
                                    data:{"image": response},
                                    success:function(data)
                                    {   
                                        var tmp  = "<?php  echo TMP_FOLDER; ?>";
                                        var data = JSON.parse(data);
                                        $("#cropped_image_file").val(data.filename);
                                         
                                      $('#uploadimageModal').modal('hide');
                                   }
                                });
                              });
                            });
                            });
                            </script>
                                <div  class="form-group">
                                    
                                    <div class="col-md-8 col-md-offset-6">
                                        <input  type="submit" class="btn btn-md btn-info "  id = "submit_btn">
                                    </div>
                                   
                                </div>
                                
                            </div>
                        </div>
                    </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
<style type="text/css">
    #gst_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .err1{
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
</style>
<script>
    
    var gstRegExp = /^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[a-zA-Z0-9]{1}/;
    
    function is_gst_number(ele){
        if(Number(ele.value) === 0){
            $("#gst_no").prop('disabled', true);
            $("#gst_certificate_file").prop('disabled', true);
            $("#signature_file").prop('required',true);
        } else if(Number(ele.value) === 1){
            $("#gst_no").prop('disabled', false);
            $("#gst_certificate_file").prop('disabled', false);
            $("#gst_no").prop('required', true);
            $("#gst_certificate_file").prop('required', true);
        }
            
    }
    
    $(document).ready(function () {

        $(function() {
             $.validator.addMethod('gstregex', function (value, element, param) {
                return this.optional(element) || gstRegExp.test( value);
            }, 'Please enter a non zero integer value!'); 

             $("#gst_form").validate({
                 rules: {
                     company_name: "required",
                     company_address: "required",
                     pan_number:{
                         required: true,
                         minlength:10,
                         maxlength:10
                     },
                     gst_number: {
                         gstregex:true,
                         minlength:15,
                         maxlength:15
                     }
                 },
                 messages: {
                     company_name: "Please Enter Company Name",
                     company_address: "Please Enter Company Address",
                     pan_number: {
                         required: "Please Enter Company Pan Number",
                         minlength:"Please Enter Valid Company Pan Number",
                         maxlength:"Please Enter Valid Company Pan Number"
                     },
                     gst_number:{
                         gstregex:"Please Enter Valid Company GST Number",
                         minlength:"Please Enter Valid Company GST Number",
                         maxlength:"Please Enter Valid Company GST Number"
                     }
                }
            });
        });
    });
    //Deepak Sharma 
    //This function use for validate file format
    $("#submit_btn").click(function (){
       var file = $("#signature_file").val();
       var file_ext = file.split('.').pop();
       if (file_ext == "png" || file_ext == "jpg" || file_ext == "jpeg" || file_ext == "JPG" || file_ext == "JPEG" || file_ext =="bmp" ||file_ext == "BMP" ||file_ext =="gif" || file_ext == "GIF" || file_ext == "PNG"){
          return true; 
       }
       else{
          $("#file_format").text("Invalid File Extension");
          return false;
       }
    });
    </script>