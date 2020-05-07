<style>
    .fa-passwd-reset > .fa-key {
        font-size: 1.15rem;
      }
</style>
<script>
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

    }
    
   
    function login_to_employee(employee_id){
        var c = confirm('Login to Employee CRM?');
        if(c){
            $.ajax({
                url:'<?php echo base_url()."employee/login/allow_log_in_to_employee/" ?>'+employee_id,
                success: function (data) {
                    window.open("<?php echo base_url().'employee/dashboard'?>",'_blank');
                }
            });
            
        }else{
            return false;
        }
    }
    
    function reset_password(employee_id){
        var c = confirm("Are you sure, you want to reset password ?");
        if(c){
            $.ajax({
                url:'<?php echo base_url()."employee/user/reset_password/" ?>'+employee_id,
                success: function (data) {
                    obj=JSON.parse(data);
                    alert(obj.message);
                    location.reload();
                }
            });
            
        }else{
            return false;
        }
    }
    </script>
<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Employee List</center></div>
        <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    else if($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                ?>
        
        <?php if($session_data['user_group'] == _247AROUND_ADMIN ||  $session_data['user_group'] == _247AROUND_DEVELOPER){?>
        <div class="col-md-12" style="font-size: 80%;">
            <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #4CBA90;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;"> 
                    <span>ADMIN</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #EEEEEE;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                    <span >CALLCENTER</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #D9EDF7;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                    <span >CLOSURE</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #FF8080;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                    <span >R M</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #286090;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                    <span >DEVELOPER</span>
                </div>
            </div>
             <div class="col-md-3">
                <div class="col-md-4">
                    <div style="background-color: #f0ad4e;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                    <span >A M</span>
                </div>
            </div>
        </div>
        <?php }?>
        
        <div class="panel-body">
            
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <?php if($session_data['user_group'] == _247AROUND_ADMIN || $session_data['user_group'] == _247AROUND_DEVELOPER){?>
                        <th class="jumbotron" style="padding:1px;text-align: center">Name</th>
                        <?php }?>
                        <th class="jumbotron" style="padding:1px;text-align: center">Full Name</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Personal Phone</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Exotel Phone</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Official Email</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Personal Email</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Group</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Manager</th>
                        
                        <?php if($session_data['user_group'] == _247AROUND_ADMIN){?>
                           <th class="jumbotron" style="padding:1px;text-align: center">CRM Login</th>
                           <th class="jumbotron" style="padding:1px;text-align: center">Action</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($data as $key=>$value){
                        $style="";
                        if($value['groups'] == _247AROUND_ADMIN){
                            $style='style="background-color:#4CBA90;text-align:center"';
                        }else if($value['groups'] == _247AROUND_CALLCENTER){
                            $style='style="background-color:#EEEEEE;text-align:center"';
                        }else if($value['groups'] == _247AROUND_CLOSURE){
                            $style='style="background-color:#D9EDF7;text-align:center"';
                        }else if($value['groups'] == _247AROUND_RM){
                            $style='style="background-color:#FF8080;text-align:center"';
                        }else if($value['groups'] == _247AROUND_DEVELOPER){
                            $style='style="background-color:#286090;text-align:center"';
                        }else if($value['groups'] == _247AROUND_AM){
                            $style='style="background-color:#f0ad4e;text-align:center"';
                        }
                        ?>      
                    <tr>
                            <td ><?php echo ($key+1).'.'?></td>
                            <?php if($session_data['user_group'] == _247AROUND_ADMIN || $session_data['user_group'] == _247AROUND_DEVELOPER) {?>
                            <td style="text-align: center;">
                                <a href="<?php base_url()?>update_employee/<?php echo $value['id']?>"><?php echo $value['employee_id']?>
                                </a>
                                <?php }?>
                            </td>
                            <td style="text-align: center;"><?php echo $value['full_name']?></td>
                            <td style="text-align: center;"><?php echo $value['phone']?>
                                <?php if(!empty($value['phone']) && !empty($c2c)){
                                    ?>
                                
                            <button type="button" onclick="outbound_call(<?php echo $value['phone'] ?>)" class="btn btn-sm btn-info pull-right"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button></td>
                                <?php } ?>
                            <td style="text-align: center;"><?php echo $value['exotel_phone']?></td>
                            <td style="text-align: center;"><?php echo $value['official_email']?></td>
                            <td style="text-align: center;"><?php echo $value['personal_email']?></td>
                           
                            <td  <?php echo $style?>><b><?php echo $value['groups']?></b></td>
                            <td  style="text-align: center;"><b><?php echo ((isset($value['manager'][0]['full_name']))?$value['manager'][0]['full_name']:'');?></b></td>
                            <?php if($session_data['user_group'] == _247AROUND_ADMIN) {?>
                            <td style="text-align: center;"><a href="javascript:void(0)" class="btn btn-md btn-success" onclick='return login_to_employee(<?php echo $value['id']?>)'  <?php echo ($value['active'] == 0)?'disabled=""':'' ?> title="<?php echo strtolower($value['id']) . " / " . strtolower($value['employee_id']);  ?>">Login</a></td>
                            <td style="text-align: center">
                                <a href="<?php base_url()?>update_employee/<?php echo $value['id']?>" class="btn btn-sm btn-primary" title="Update Employee" > <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href="<?php base_url()?>deactive_employee/<?php echo $value['id']; ?>" class="btn btn-sm btn-warning" title="Deactive Employee" > <i class="fa fa-check-square" aria-hidden="true"></i></a>
                                <a href="javascript:void(0)" class="btn btn-sm" title="Reset Password" onclick="return reset_password(<?php echo $value['id']; ?>)" style="background-color: #D9EDF7" ><span class="fa-passwd-reset fa-stack"><i class="fa fa-undo fa-stack-2x"></i><i class="fa fa-key fa-stack-1x"></i></span></a>
                            </td>
                            <?php }?>
                                


                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');}else if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>