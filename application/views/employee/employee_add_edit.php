<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;">
            <div class="panel-heading" style="font-size:130%;">
                <b>
                    <center><?php
                        if (isset($id)) {
                            $action = "employee/user/process_edit_employee";
                            echo "EDIT EMPLOYEE";
                        } else {
                            $action = "employee/user/process_add_employee";
                            echo "ADD EMPLOYEE";
                        }
                        ?></center>
                </b>
            </div>
            <div class="panel-body">
                
                <form name="employee_add_edit" class="form-horizontal" id ="employee_add_edit" action="<?php echo base_url().''.$action?>" method="POST">
                     <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                    ?>">
                     
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('employee_id')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="employee_id" class="col-md-4">Name</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="employee_id" name="employee_id" value = "<?php
                                if (isset($query[0]['employee_id'])) {
                                    echo $query[0]['employee_id'];
                                }
                                ?>" placeholder="Name">
                                        <?php echo form_error('employee_id'); ?>
                            </div>
                        </div>
                    </div>
                     
                     <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('full_name')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="full_name" class="col-md-4">Full Name</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="employee_id" name="full_name" value = "<?php
                                if (isset($query[0]['full_name'])) {
                                    echo $query[0]['full_name'];
                                }
                                ?>" placeholder="Full Name">
                                        <?php echo form_error('full_name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('password')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="employee_password" class="col-md-4">Password</label>
                            <div class="col-md-7">
                                <input  type="password" class="form-control" id="employee_password" name="employee_password" value = "<?php
                                if (isset($query[0]['employee_password'])) {
                                    echo $query[0]['employee_password'];
                                }
                                ?>" >
                                        <?php echo form_error('employee_password'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('clear_password')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="clear_password" class="col-md-4">Clear Password</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="clear_password" name="clear_password" value = "<?php
                                if (isset($query[0]['clear_password'])) {
                                    echo $query[0]['clear_password'];
                                }
                                ?>" placeholder="Clear Password">
                                        <?php echo form_error('clear_password'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('phone')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="phone" class="col-md-4">Phone</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="phone" name="phone" value = "<?php
                                if (isset($query[0]['phone'])) {
                                    echo $query[0]['phone'];
                                }
                                ?>" placeholder="Phone">
                                        <?php echo form_error('phone'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('official_email')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="official_email" class="col-md-4">Official Email</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="official_email" name="official_email" value = "<?php
                                if (isset($query[0]['official_email'])) {
                                    echo $query[0]['official_email'];
                                }
                                ?>" placeholder="Official Email">
                                        <?php echo form_error('official_email'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div  class="form-group <?php
                        if (form_error('personal_email')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="personal_email" class="col-md-4">Personal Email</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="personal_email" name="personal_email" value = "<?php
                                if (isset($query[0]['personal_email'])) {
                                    echo $query[0]['personal_email'];
                                }
                                ?>" placeholder="Personal Email">
                                        <?php echo form_error('personal_email'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('groups')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="groups" class="col-md-4">Groups</label>
                            <div class="col-md-7">
                                <select id="groups" class="form-control" name ="groups">
                                                <option selected disabled>Select Groups</option>
                                                <option value ="admin" <?php if($query[0]['groups'] == _247AROUND_ADMIN ){echo 'selected';}?> >ADMIN</option>
                                                <option value ="regionalmanager" <?php if($query[0]['groups'] == _247AROUND_RM ){echo 'selected';}?> >Regional Managers</option>
                                                <option value ="closure" <?php if($query[0]['groups'] == _247AROUND_CLOSURE ){echo 'selected';}?> >Closure</option>
                                                <option value ="callcenter" <?php if($query[0]['groups'] == _247AROUND_CALLCENTER ){echo 'selected';}?> >Call-Center</option>
                                             
                                </select>
                                        <?php echo form_error('groups'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div style="margin-left:48%;">
                                <input type="Submit" value="<?php
                                    if (isset($query[0]['id'])) {
                                        echo "Update";
                                    } else {
                                        echo "Save";
                                    }
                                    ?>" class="btn btn-primary" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //Select 2 in groups dropdown
    $('#groups').select2();
    $('.select2-selection').css('background-color', '#FF8080');
    
    (function ($, W, D)
    {
        var JQUERY4U = {};

        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        //form validation rules
                        $("#employee_add_edit").validate({
                            rules: {
                                employee_id: "required",
                                employee_password: "required",
                                clear_password: "required",
                                phone: {
                                    required: true,
                                    minlength: 10
                                },
                                groups: "required",
                                official_email: {
                                    required: true,
                                    email: true
                                },
                                personal_email: {
                                    email: true
                                }
                            },
                            messages: {
                                employee_id: "Please enter Name",
                                employee_password: "Please enter Password",
                                clear_password: "Please Select Clear Password",
                                groups: "Please select Groups",
                                official_email: "Please enter Official email"
                            },
                            submitHandler: function (form) {
                                form.submit();
                            }
                        });
                    }
                };

        //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
            JQUERY4U.UTIL.setupFormValidation();
        });

    })(jQuery, window, document);



</script>