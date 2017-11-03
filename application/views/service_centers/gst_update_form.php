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
                                        <input  type="text" class="form-control" id="company_name" name="company_name" value = "<?php echo set_value("company_name");?>" placeholder="Company Name" required>
                                        <?php echo form_error('company_name'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('company_address') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">Company Address</label>
                                    <div class="col-md-8">
                                        
                                        <textarea class="form-control" name="company_address" id="company_address" required placeholder="Company Address"><?php echo set_value("company_address");?></textarea>
                                        <?php echo form_error('company_address'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('pan_number') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">Company PAN</label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" style="text-transform:uppercase"  id="pan_number" name="pan_number" value = "<?php echo set_value("pan_number");?>" placeholder="Company Pan Number" >
                                        <?php echo form_error('pan_number'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('is_gst') ) { echo 'has-error';} ?>">
                                    <label  for="company_name" class="col-md-4">GST Registration Done?</label>
                                    <div class="col-md-8">
                                        <label class="radio-inline">
                                            <input type="radio" onclick="is_gst_number(this);" name="is_gst" value="1" checked required>Yes
                                       </label> 
                                        <label class="radio-inline">
                                            <input type="radio" onclick="is_gst_number(this);" name="is_gst" value="0" required>No
                                        </label>
                                         <?php echo form_error('is_gst'); ?>
                                    </div>
                                </div>    
                                    
                                 <div  class="form-group <?php if( form_error('gst_number') ) { echo 'has-error';} ?>">
                                    <label  for="gst_no" class="col-md-4">Company GST No</label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" style="text-transform:uppercase"  id="gst_no" name="gst_number" value = "<?php echo set_value("gst_number");?>" placeholder="Company GST Number" >
                                        <?php echo form_error('gst_number'); ?>
                                    </div>
                                    
                                </div>
                                 <div  class="form-group <?php if( form_error('file') ) { echo 'has-error';} ?>">
                                    <label  for="gst_certificate_file" class="col-md-4">Company GST File</label>
                                    <div class="col-md-8">
                                        <input  type="file" class="form-control" id="gst_certificate_file" name="file" required>
                                        <?php echo form_error('file'); ?>
                                    </div>
                                    
                                </div>
                                <div  class="form-group <?php if( form_error('signature_file') ) { echo 'has-error';} ?>">
                                    <label  for="signature_file" class="col-md-4">Signature Image File</label>
                                    <div class="col-md-8">
                                        <input  type="file" class="form-control" id="signature_file" name="signature_file" >
                                        <?php echo form_error('signature_file'); ?>
                                    </div>
                                    
                                Scanned Image of Signature of Authorized Signatory (required for system-generated invoices)
                                
                                </div>
                                <div  class="form-group">
                                    
                                    <div class="col-md-8 col-md-offset-6">
                                        <input  type="submit" class="btn btn-md btn-info " >
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
            $("#gst_no").val("");
            $("#gst_certificate_file").val("");
            document.getElementById("gst_no").readOnly = true;
            document.getElementById("gst_certificate_file").readOnly = true;
        } else if(Number(ele.value) === 1){
            document.getElementById("gst_no").readOnly = false;
            document.getElementById("gst_certificate_file").readOnly = false;
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
                         required: true,
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
                         required: "Please Enter Company GST Number",
                         gstregex:"Please Enter Valid Company GST Number",
                         minlength:"Please Enter Valid Company GST Number",
                         maxlength:"Please Enter Valid Company GST Number"
                     }
                }
            });
        });
    });
    </script>