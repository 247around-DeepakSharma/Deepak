<script>
	function validate()
	{
	  var subject = $("#subject").val();
	  var mailbody = $("#mail_body").val();
    var bcc_poc = $('#bcc_poc').val();
    var bcc_cc = $('#bcc_owner').val();
	  if(subject == "")
	  {
		  alert("Please enter subject.");
		  return false;
	  }
	  if(mailbody == "")
	  {
		  alert("Please enter mail body.");
		  return false;
	  }
    if(document.getElementById('bcc_poc').checked || document.getElementById('bcc_owner').checked) {
      } else {
        alert("Select atleast one checkbox");
        return false;
    }
	}
</script>
<div id="page-wrapper"> 
  <div class="container-fluid">
    <div class="row">

      <h1 class="page-header">
        <b> Broadcast Email to All (active) Vendors</b>
      </h1>
      <form class="form-horizontal" action="<?php echo base_url()?>employee/vendor/process_broadcast_mail_to_vendors_form" 
      		  method="POST" enctype="multipart/form-data">

        <div class="form-group">
          <label for="subject" class="col-md-2">Send mail to:</label>
          <div class="col-md-6">
           <div class="checkbox" style="display: inline; margin-left: 8px;">
              <label><input type="checkbox" value="Owner" id="bcc_owner" name="bcc_owner" checked>Owners</label>
            </div>

            <div class="checkbox" style="display: inline;">
              <label><input type="checkbox" value="POC" id="bcc_poc" name="bcc_poc">Point of Contacts</label>
          </div>
            
          </div>
        </div>

      	<div class="form-group <?php if (form_error('mail_to')) {echo 'has-error'; } ?>">
          <label for="additional_mail_to" class="col-md-2">To:</label>
          <div class="col-md-6">
            <input type="text" class="form-control"  id="additional_mail_to" name="mail_to"
            	  placeholder="Enter additional TO email address, if any...">
            <?php echo form_error('mail_to'); ?>
          </div>
        </div>

        <div class="form-group <?php if (form_error('mail_cc')) {echo 'has-error'; } ?>">
          <label for="additional_mail_cc" class="col-md-2">CC:</label>
          <div class="col-md-6">
            <input type="text" class="form-control"  id="additional_mail_cc" name="mail_cc"
            	 placeholder="Enter additional CC email address, if any...">
            <?php echo form_error('mail_cc'); ?>
          </div>
        </div>

        <div class="form-group <?php if (form_error('subject')) {echo 'has-error'; } ?>">
          <label for="subject" class="col-md-2">Subject:</label>
          <div class="col-md-6">
            <textarea class="form-control"  id="subject" name="subject" value = 
            		"<?php echo set_value('subject'); ?>"></textarea> 
            <?php echo form_error('subject'); ?>
          </div>
        </div>

        <div class="form-group <?php if (form_error('mail_body')) {echo 'has-error'; } ?>">
          <label for="mail_body" class="col-md-2">Message:</label>
          <div class="col-md-6">
            <textarea class="form-control"  id="mail_body" name="mail_body" value = 
            		"<?php echo set_value('mail_body'); ?>" style="height:600px;"></textarea>
            <?php echo form_error('mail_body'); ?>
          </div>
        </div>

        <div class="form-group <?php if (form_error('mail_body')) {echo 'has-error'; } ?>">
          <label for="mail_body" class="col-md-2">Select File to attach:</label>
          <div class="col-md-6">
            <input type="file" name="fileToUpload" id="fileToUpload">
          </div>
        </div>

        <div>
          <center>
            <input type="submit" value="Send Mail" class="btn btn-primary" onclick="return(validate())">
          </center>
        </div>

      </form>
    </div>
  </div>
</div>
