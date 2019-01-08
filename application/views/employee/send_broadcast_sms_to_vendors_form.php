<div id="page-wrapper"> 
    <div class="container-fluid">
        <div class="row">

            <h1 class="page-header">
                <b> Broadcast SMS to All Vendors</b>
            </h1>
               <?php if($this->session->flashdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                        </button>
                    <strong>' . $this->session->flashdata('error') . '</strong>
                   </div>';
                }
                ?>
                <?php if($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                        </button>
                    <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
                }
                ?>
            <form class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_broadcast_sms_to_vendors" 
                  method="POST" enctype="multipart/form-data">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-2">Send mail:</label>
                        <div class="col-md-10">

                            <div class="checkbox" style="display: inline; margin-left: 8px;">
                                <label><input type="checkbox" id="vendor_owner" name="vendor_owner" checked>SF Owner</label>
                            </div>

                            <div class="checkbox" style="display: inline;">
                                <label><input type="checkbox" id="venor_poc" name="venor_poc">SF POC</label>
                            </div>

                        </div>
                    </div>
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
                    <div class="col-md-1">
                    
                    </div>
                    <div class="col-md-6">
                        <input type="submit" value="Send SMS" class="btn btn-primary" onclick="return(validate())" style="margin-bottom: 10px;">
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('file_error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success_msg');} ?>
<script>
	function validate()
	{
            if($('#venor_poc').is(":checked") == false && $('#vendor_owner').is(":checked") == false){
                alert("Please select SF Owner or SF POC.");
                return false;
            }
            if($("#mail_body").val()) {
            } else {
                alert("Please type your message.");
                return false;
            }
	}
</script>