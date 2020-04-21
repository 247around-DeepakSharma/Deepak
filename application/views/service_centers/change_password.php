<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
</style>
<?php echo form_open('employee/service_centers/change_password'); ?>
                   

<div id="page-wrapper">
        <div class="panel-body">
            <div  class = "panel panel-info">
                <div class="panel-heading"><b>Change Password</b></div>
                <div class="panel-body form-horizontal">
                     <?php 
                        if ($this->session->userdata('success')) {
                            echo '<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
                            
                            $this->session->unset_userdata('success');
                        }
                    ?>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div  class="form-group">
                                <label for="old_password">Old Password</label>
                                <input  type="text" class="form-control" id="old_password" name="old_password" placeholder="">
                            </div>
                        </div>                    
                        <div class="col-md-3">
                            <div  class="form-group">
                                <label for="new_password">New Password</label>
                                <input  type="text" class="form-control" id="new_password" name="new_password" placeholder="" disabled="">
                            </div>
                        </div>                    
                        <div class="col-md-3">
                            <div  class="form-group">
                                <label for="retype_new_password">Reenter New Password</label>
                                <input  type="text" class="form-control" id="retype_new_password" name="retype_new_password" placeholder="" disabled="">
                            </div>
                        </div>                    
                        <div class="col-md-1">
                            <div  class="form-group">
                                <label for="retype_new_password"></label>
                                <input type="submit" name="change" value="Change" class="btn btn-primary form-control" disabled="" id="changePassword">
                            </div>
                        </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<style>
    .row, .col-md-3, .col-md-1 {
        margin-left: 1%;
    }
    
    label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 0px !important; 
    }
</style>
<?php 

//    echo"<pre>";print_r($this->session->userdata['service_center_id']);exit;

?>
<script>
    $(document).ready(function(){
        // set focus to old password field.
        $('#old_password').focus();
        // set change event on old password field.
        $('#old_password').on('change', function(){
            var old_password = $(this).val();
            if(old_password != '') {
                $.ajax({
                    url : '<?php echo base_url(); ?>employee/service_centers/change_password/',
                    method : 'post',
                    data : {old_password}
                }).fail(function(data) {
                    alert('Internal server error.');
                }).done(function(data) {
                    if($.trim(data) == '0') {
                        alert('Incorrect old password');
                        $('#new_password').attr('disabled','disabled');
                        $('#retype_new_password').attr('disabled','disabled');
                        $('#changePassword').attr('disabled','disabled');
                    } else {
                        $('#new_password').removeAttr('disabled');
                        $('#new_password').attr('required',true);
                        $('#retype_new_password').removeAttr('disabled');
                        $('#retype_new_password').attr('required',true);
                        $('#changePassword').removeAttr('disabled');
                    }
                });
            }
        });
        
        $('#new_password, #retype_new_password').on('change', function(){
            matchPasswords($('#new_password').val(), $('#retype_new_password').val());
        });
    });
    
    function matchPasswords(txtNewPassword, txtRetypeNewPassword) {
        if(txtNewPassword != '' && txtRetypeNewPassword != '') {
            if(txtNewPassword == txtRetypeNewPassword) {
                $('#changePassword').removeAttr('disabled');
                return '1';
            } else {
                $('#changePassword').attr('disabled','disabled');
                alert('Reenter password must be same as password.');
                return '0';
            }
        }
    }
</script>