<div id="page-wrapper"> 
    <div class="container-fluid">
        <div class="row">

            <h1 class="page-header">
                <b> Broadcast Email  </b>
            </h1>
            <form class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_broadcast_mail_to_vendors_form" 
                  method="POST" enctype="multipart/form-data">
                
                <div class="col-md-6">
                    
                    <div class="form-group">
                        <label for="subject" class="col-md-2">Send mail:</label>
                        <div class="col-md-10">

                            <div class="checkbox" style="display: inline; margin-left: 8px;">
                                <label><input type="checkbox" value="Owner" id="bcc_owner" name="bcc_owner" checked>SF Owner</label>
                            </div>

                            <div class="checkbox" style="display: inline;">
                                <label><input type="checkbox" value="POC" id="bcc_poc" name="bcc_poc">SF POC</label>
                            </div>
                            
                             <?php if(!empty($saas)){ ?>
                            <div class="checkbox" style="display: inline; margin-left: 8px;">
                                <label><input type="checkbox" value="partner_owner" id="bcc_owner" name="bcc_partner_owner">Partner Owner</label>
                            </div>

                            <div class="checkbox" style="display: inline;">
                                <label><input type="checkbox" value="partner_poc" id="bcc_poc" name="bcc_partner_poc">Partner POC</label>
                            </div>
                            
                             <?php }  ?>

                            <div class="checkbox" style="display: inline;">
                                <label><input type="checkbox" value="employee" id="bcc_poc" name="bcc_employee">Employee</label>
                            </div>
                            
                           
                                    

                        </div>
                    </div>
                    
                    <div class="form-group <?php if (form_error('mail_from')) {echo 'has-error';} ?>">
                        <label for="mail_from" class="col-md-2">From:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control"  id="mail_from" name="mail_from"
                                   placeholder="Enter From Email" required="">
                            <?php if(form_error('mail_from')){ ?> 
                            <div class="text-danger"><?php echo form_error('mail_from');?></div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if (form_error('mail_to')) {echo 'has-error';} ?>">
                        <label for="additional_mail_to" class="col-md-2">To:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control"  id="additional_mail_to" name="mail_to"
                                   placeholder="Enter additional TO email address, if any...">
                           <?php if(form_error('mail_to')){ ?> 
                            <div class="text-danger"><?php echo form_error('mail_to');?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group <?php if (form_error('mail_cc')) {echo 'has-error';} ?>">
                        <label for="additional_mail_cc" class="col-md-2">CC:</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control"  id="additional_mail_cc" name="mail_cc"
                                   placeholder="Enter additional CC email address, if any...">
                            <?php if(form_error('mail_cc')){ ?> 
                            <div class="text-danger"><?php echo form_error('mail_cc');?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group <?php if (form_error('subject')) {echo 'has-error';} ?>">
                        <label for="subject" class="col-md-2">Subject:</label>
                        <div class="col-md-10">
                            <textarea class="form-control"  id="subject" name="subject" value = 
                                      "<?php echo set_value('subject'); ?>"></textarea> 
                            <?php if(form_error('subject')){ ?> 
                            <div class="text-danger"><?php echo form_error('subject');?></div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php if (form_error('mail_file')) {echo 'has-error';} ?>">
                        <label for="mail_file" class="col-md-2">Select File to attach:</label>
                        <div class="col-md-10">
                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-6">
                    <div class="form-group <?php if (form_error('mail_body')) {echo 'has-error';} ?>">
                        <label for="mail_body" class="col-md-2">Message:</label>
                        <div class="col-md-10">
                            <textarea class="form-control"  id="mail_body" name="mail_body" value = 
                                      "<?php echo set_value('mail_body'); ?>" style="height:300px;"></textarea>
                                <?php if(form_error('mail_body')){ ?> 
                            <div class="text-danger"><?php echo form_error('mail_body');?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div>
                    <center>
                        <input type="submit" value="Send Mail" class="btn btn-primary" onclick="return(validate())" style="margin-bottom: 10px;">
                    </center>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
	function validate()
	{
            var from = $("#mail_from").val();
            var subject = $("#subject").val();
            var mailbody = $("#mail_body").val();
            var bcc_poc = $('#bcc_poc').val();
            var bcc_cc = $('#bcc_owner').val();
            if(from === "")
            {
                alert("Please enter From Email.");
		return false;
            }
            if(subject === "")
            {
                alert("Please enter subject.");
		return false;
            }
            if(mailbody === "")
            {
                alert("Please enter mail body.");
                return false;
            }
            if(document.getElementById('bcc_poc').checked || 
               document.getElementById('bcc_owner').checked ||
               document.getElementById('bcc_partner_owner').checked || 
               document.getElementById('bcc_partner_poc').checked ||
               document.getElementById('bcc_employee').checked) {
              } else {
                alert("Select atleast one checkbox");
                return false;
            }
	}
</script>