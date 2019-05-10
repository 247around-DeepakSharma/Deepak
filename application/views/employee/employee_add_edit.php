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
                            echo "EDIT PROFILE";
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
                     

                <div class="row">     
                     <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('full_name')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="full_name" class="col-md-4">Full Name *</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="full_name" name="full_name" value = "<?php
                                if (isset($query[0]['full_name'])) {
                                    echo $query[0]['full_name'];
                                }
                                ?>" placeholder="Full Name" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 32">
                                        <?php echo form_error('full_name'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('phone')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="phone" class="col-md-4">Personal Phone *</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="phone" name="phone" value = "<?php
                                if (isset($query[0]['phone'])) {
                                    echo $query[0]['phone'];
                                }
                                ?>" placeholder="Phone" onkeypress="return (event.charCode > 47 && event.charCode < 58)">
                                        <?php echo form_error('phone'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('phone')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="exotel_phone" class="col-md-4">Exotel Phone</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="exotel_phone" name="exotel_phone" value = "<?php
                                if (isset($query[0]['exotel_phone'])) {
                                    echo $query[0]['exotel_phone'];
                                }
                                ?>" placeholder="Exotel Phone" onkeypress="return (event.charCode > 47 && event.charCode < 58)">
                                        <?php echo form_error('exotel_phone'); ?>
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
                </div>
                <div class="row">
                     <div class="col-md-6">
                         <div  class="form-group <?php
                        if (form_error('languages')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="languages" class="col-md-4">Language Known</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="languages" name="languages" value = "<?php
                                if (isset($query[0]['languages'])) {
                                    echo $query[0]['languages'];
                                }
                                ?>" placeholder="Language Known" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 32 || event.charCode == 44 || event.charCode == 38">
                                        <?php echo form_error('languages'); ?>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6">
                         <div  class="form-group <?php
                        if (form_error('office_centre')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="office_centre" class="col-md-4">Office Centre</label>
                            <div class="col-md-7">
                                <input  type="text" class="form-control" id="office_centre" name="office_centre" value = "<?php
                                if (isset($query[0]['office_centre'])) {
                                    echo $query[0]['office_centre'];
                                }
                                ?>" placeholder="Office Centre">
                                        <?php echo form_error('office_centre'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                        if (form_error('dept')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="dept" class="col-md-4">Department *</label>
                            <div class="col-md-7">
                                <select id="dept" class="form-control" name ="department" required="" onchange="get_role_on_department()">
                                    <option selected disabled>Select Department</option>
                                    <?php foreach ($employee_dept as $key => $value) { ?>
                                    <option value ="<?php echo $value['department']; ?>" <?php if(isset($query[0]['department']) && $query[0]['department'] == $value['department'] ){echo 'selected'; }?> ><?php echo $value['department']; ?></option>
                                    <?php  } ?>
                                </select>
                                        <?php echo form_error('dept'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('groups')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="groups" class="col-md-4">Role *</label>
                            <div class="col-md-7">
                                <select id="groups" class="form-control" name ="groups" required="">
                                    <option selected disabled>Select Role</option>
                                    <?php if(isset($employee_role)) {
                                        foreach ($employee_role as $key => $value) { ?>
                                    <option value ="<?php echo $value['role']; ?>" <?php if(isset($query[0]['groups']) && $query[0]['groups'] == $value['role'] ){echo 'selected'; }?> ><?php echo $value['role']; ?></option>
                                    <?php  } } ?>
                                </select>
                                        <?php echo form_error('groups'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('manager')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="manager" class="col-md-4">Manager</label>
                            <div class="col-md-7">
                                <select id='manager' name='manager' class="form-control"  >
                                                            <option value="" selected="" disabled="">Select Manager</option>
                                                            <?php foreach ($employee_list as $key => $value) { ?>
                                    <option value ="<?=$value['id']; ?>" <?php if(isset($manager) && $manager == $value['id'] ){echo 'selected'; }?> ><?php echo $value['full_name']; ?></option>
                                                            <?php  } ?>
                                                        </select>
                                <?php echo form_error('manager'); ?>
                                                    </div>
                                                 </div>
                                            </div>
                                        </div>
                                        <div class="row">
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('subordinate')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="subordinate" class="col-md-4">Subordinate</label>
                            <div class="col-md-7">
                                <select id='subordinate' name='subordinate[]' class="form-control subordinate" multiple="multiple" style="min-width:350px;"  >
                                                            <!--<option value="0" selected="" disabled="">Select Subordinate</option>-->
                                    <?php foreach ($employee_list as $key => $value) {
                                        if(isset($subordinate)) {
                                            foreach($subordinate as $sub_id) {
                                                $selected[$sub_id['employee_id']] = "selected";
                                            }
                                        }
                                        ?>
                                    <option value ="<?=$value['id']; ?>" <?=(isset($selected[$value['id']])?$selected[$value['id']]:'')?> ><?php echo $value['full_name']; ?></option>
                                                            <?php  } ?>
                                                        </select>
                                <?php echo form_error('subordinate'); ?>
                                                    </div>
                                                 </div>
                                            </div>
                                        </div>
                                    </div>
            <div class="panel-footer" align='center'>
                                <input type="Submit" value="<?php
                                    if (isset($query[0]['id'])) {
                                        echo "Update";
                                    } else {
                                        echo "Save";
                                    }
                                    ?>" class="btn btn-primary" >
                        </div>
                </form>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $('#groups').select2();
    $('#dept').select2();
    $('#manager').select2();
    
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
                                full_name: "required",
                                phone: {
                                    required: true,
                                    maxlength: 10,
                                    number:true
                                },
                                dept: "required",
                                groups: "required",
                                official_email: {
                                    //required: true,
                                    email: true
                                },
                                personal_email: {
                                    email: true
                                }
                            },
                            messages: {
                                full_name: "Please Enter Full Name",
                                dept: "Please select Department",
                                groups: "Please select Role",
                                //official_email: "Please enter Official email",
                                phone: {required: "Please enter Personal Phone",number:"Please enter numbers Only"}
                            },
                            submitHandler: function (form) {
                                if(validateForm())
                                    form.submit();
                            }
                        });
                    }
                };

        //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
            $(".subordinate").select2({
                    placeholder: "Select Subordinate",
                    allowClear: true
            });
            var error = "<?=$error?>";
            if(error != '')
                alert(error);
            JQUERY4U.UTIL.setupFormValidation();
        });

    })(jQuery, window, document);
    
    function validateForm() {
        if(($('#manager option:selected').val() == "") && (($('#subordinate option:selected').val() == "") || ($('#subordinate option:selected').val() == undefined)))
        {
            alert("Please add manager or subordinate!");
            return false;
        }
        return true;
    }
    
    function get_role_on_department() {
        var department = $('#dept').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/user/get_role_on_department',
            async:false,
            data:{'department' : department},
            success: function (response) {
                response=JSON.parse(response);
                if(response.length>0)
                {
                    var str='<option selected disabled>Select Role</option>';
                    for(var i=0;i<response.length;i++)
                    {
                        str+="<option value ='"+response[i]['role']+"'  >"+response[i]['role']+"</option>";
                    }
                }
                $('#groups').html(str);
            }
        });
    }
    
</script>