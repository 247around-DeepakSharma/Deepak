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
        </div>
        <?php }?>
        
        <div class="panel-body">
            
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Name</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Full Name</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Phone</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Official Email</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Personal Email</th>
                        <?php if($session_data['user_group'] == _247AROUND_ADMIN || $session_data['user_group'] == _247AROUND_DEVELOPER){?>
                            <th class="jumbotron" style="padding:1px;text-align: center">Group</th>
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
                        }
                        ?>		
                    <tr>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td style="text-align: center;">
                                <?php if($session_data['user_group'] == _247AROUND_ADMIN || $session_data['user_group'] == _247AROUND_DEVELOPER) {?>
                                <a href="<?php base_url()?>update_employee/<?php echo $value['id']?>"><?php echo $value['employee_id']?>
                                </a>
                                <?php }else{
                                    echo $value['employee_id'];
                                } ?>
                            </td>
                            <td style="text-align: center;"><?php echo $value['full_name']?></td>
                            <td style="text-align: center;"><?php echo $value['phone']?></td>
                            <td style="text-align: center;"><?php echo $value['official_email']?></td>
                            <td style="text-align: center;"><?php echo $value['personal_email']?></td>
                            <?php if($session_data['user_group'] == _247AROUND_ADMIN || $session_data['user_group'] == _247AROUND_DEVELOPER) {?>
                            <td  <?php echo $style?>><b><?php echo $value['groups']?></b></td>
                            <td style="text-align: center">
                                <a href="<?php base_url()?>update_employee/<?php echo $value['id']?>" class="btn btn-sm btn-primary" title="Update Employee" > <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            </td>
                            <?php }?>
                                


                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>
<?php $this->session->unset_userdata('success')?>