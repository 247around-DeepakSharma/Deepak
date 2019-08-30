<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;">
            <div class="panel-heading" style="font-size:130%;">
                <b>
                    <center>RM MAPPING</center>
                </b>
            </div>
            <div class="panel-body">
                
                <form name="rm_state_mapping" class="form-horizontal" id ="rm_state_mapping" action="<?php echo base_url().'employee/user/process_rm_state_mapping'?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('rm_asm')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="rm_asm" class="col-md-4">RM/ASM *</label>
                            <div class="col-md-7">
                                <select id="rm_asm" class="form-control" name ="rm_asm" required onchange="getAssignStates()">
                                    <option selected disabled>Select RM/ASM</option>
                                    <?php foreach ($employee_rm as $key => $value) { ?>
                                    <option value ="<?php echo $value['id']; ?>"> <?php echo $value['full_name']; ?></option>
                                    <?php  } ?>
                                </select>
                                        <?php echo form_error('rm_asm'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div  class="form-group <?php
                        if (form_error('state_name')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="state_name" class="col-md-4">State *</label>
                            <div class="col-md-7">
                                <select id='state_name' name='state_name[]' class="form-control state_name" multiple="multiple" style="min-width:350px;"  required>
                                    <?php foreach ($state as $key => $value) {?>
                                    <option value ="<?=$value['state']; ?>"  ><?php echo $value['state']; ?></option>
                                         <?php  } ?>                  
                                                        </select>
                                <?php echo form_error('state_name'); ?>
                                                    </div>
                                                 </div>
                                            </div>
                                        </div>
                                    </div>
            <div class="panel-footer" align='center'>
                                <input type="Submit" value="Update" class="btn btn-primary" >
                        </div>
                </form>
            </div>
        </div>
    </div>
<?php if(!empty($msg)) { ?>
<script>
    alert('<?php echo $msg; ?>');
    </script>
<?php } ?>
<script type="text/javascript">
    $('#rm_asm').select2();
    //$('#state_name').select2();
    
    (function ($, W, D)
    {
        var JQUERY4U = {};
        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        //form validation rules
                        $("#rm_state_mapping").validate({
                            rules: {
                                
                                rm_asm: "required",
                                state_name: "required",
                                
                            },
                            messages: {
                              
                                rm_asm: "Please select rm/asm",
                                state_name: "Please assign state(s)"
                                
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
            $(".state_name").select2({
                    placeholder: "Select State",
                    allowClear: true
            });
            var error = "<?=$error?>";
            if(error != '')
                alert(error);
            JQUERY4U.UTIL.setupFormValidation();
        });

    })(jQuery, window, document);
    
    function validateForm() {
       if( $('#state_name option:selected').val() == "" || $('#state_name option:selected').val() == undefined)
        {
            alert("Please assign State(s)");
            return false;
        }
        return true;
    }
    //get AssignStates for existing
    //Pranjal
    function getAssignStates()  {
        var rmid = $('#rm_asm').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/user/get_rm_state',
            async:false,
            data:{'rmid' : rmid},
            success: function (response) {
               
                response=JSON.parse(response);
                values=[];
                if(response.length>0)
                {
                    
                    for(var i=0;i<response.length;i++)
                    {
                        values.push(response[i]['state']);
                    }
                    //console.log(values);
                     
                }
                $('#state_name').val(values).change();
               
            }
        });
    }
</script>