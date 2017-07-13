<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center><b>ADD/EDIT LOGIN</b></center></div>
        <div style='border-radius: 5px;background: #EEEEEE;margin-top: 10px;margin-bottom: 10px;width:330px;' class='col-md-6'><b>NOTE:</b> <i>Select Checkbox to Add/Edit .</i></div>
        <div class="panel-body">
            
             <?php
                    if ($this->session->userdata('login_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:-10px;width:40%;margin-left:32%;margin-bottom:5px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('login_error').'</strong>
                    </div>';
                    }
                    ?>
            <?php
                    if ($this->session->userdata('login_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:-10px;width:40%;margin-left:32%;margin-bottom:5px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('login_success').'</strong>
                    </div>';
                    }
                    ?>
            
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="jumbotron" style="text-align: center">S.N.</th>
                        <th class="jumbotron" style="text-align: center">USERNAME</th>
                        <th class="jumbotron" style="text-align: center">PASSWORD</th>
                        <th class="jumbotron" style="text-align: center">RE-TYPE PASSWORD</th>
                    </tr>
                </thead>
                <tbody>

                <form name="myForm" class="form-horizontal" id ="brackets"  action='<?php echo base_url() ?>employee/partner/process_partner_login_details_form' method="POST" enctype="form-data">
                    <input type="hidden" name = "partner_id" value="<?php echo $login['partner_id']?>"/>
                    <?php for ($i = 0; $i < 5; $i++) { ?>		
                        <tr>
                            <td> 
                                <b>Login <?php echo ($i+1)?>:</b>
                                <input type="checkbox" style="zoom: 1.5" name='choice[]' value='<?php echo ($i) ?>' id="check_<?php echo $i ?>" onchange="return validate(this.id)"/>
                            </td>
                            <td>
                                <input type='text' name='username[]' id="username_<?php echo $i?>" disabled="" class = "form-control" value="<?php echo isset($login[$i]['user_id'])?$login[$i]['user_id']:''?>" />
                            </td>
                            <td>
                                <input type='password' name='password[]' id="password_<?php echo $i?>" class = "form-control" disabled="" value="<?php echo isset($login[$i]['clear_password'])?$login[$i]['clear_password']:''?>" />
                            </td>
                            <td>
                                <input type='password' name='retype_password[]' id="retype_password_<?php echo $i?>" class = "form-control" disabled="" value="<?php echo isset($login[$i]['clear_password'])?$login[$i]['clear_password']:''?>" onchange="return check_password(this.id)"/>
                            </td>
                        <input type="hidden" name = "id[]"  value="<?php echo isset($login[$i]['agent_id'])?$login[$i]['agent_id']:''?>"/>
                        
                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

            <center>
                <input type="submit" id="submitform" class="btn btn-info " value="Save"/>
                <a href="<?php echo base_url()?>employee/partner/viewpartner" class="btn btn-info"/>Cancel</a>
            </center>
            </form>   
        </div>
    </div>
</div>
<script type="text/javascript">
   
 
 //Adding Validation
 function validate(id) {
        var id = id.split("_")[1];
        if ($('#check_' + id).is(':checked')) {
            $("#username_"+id).attr('required', true);
            $("#username_"+id).attr('disabled', false);
            $("#password_"+id).attr('required', true);
            $("#password_"+id).attr('disabled', false);
            $("#retype_password_"+id).attr('required', true);
            $("#retype_password_"+id).attr('disabled', false);
        } else {
            $("#username_"+id).attr('required', false);
            $("#username_"+id).attr('disabled', true);
            $("#password_"+id).attr('required', false);
            $("#password_"+id).attr('disabled', true);
            $("#retype_password_"+id).attr('required', false);
            $("#retype_password_"+id).attr('disabled', true);
        }
    }
    
   function check_password(id){
       var id = id.split("_")[2];
       var password = $('#password_'+id).val();
       var retype_password = $('#retype_password_'+id).val();
       if(password !== retype_password){
           alert('Please Enter Correct Password for Login '+ (parseInt(id)+1));
           return false;
       }
   }
 
 

</script>
<?php $this->session->unset_userdata('login_error'); ?>
<?php $this->session->unset_userdata('login_success'); ?>
