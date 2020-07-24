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
                                    <option value ="<?php echo $value['id']; ?>"> <?php echo $value['full_name']; ?>(<?php echo $value['groups']; ?>)</option>
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
                                <select id='state_name' name='state_name[]' class="form-control state_name" multiple="multiple" style="min-width:350px;"  required onchange="bring_district_from_state()">
                                    <?php foreach ($state as $key => $value) {?>
                                    <option value ="<?=$value['state']; ?>"  ><?php echo $value['state']; ?></option>
                                         <?php  } ?>                  
                                                        </select>
                                <?php echo form_error('state_name'); ?>
                                                    </div>
                                                 </div>
                                            </div>
                                        </div>
                    
                    <div class="container1">
   <div class="row">
      <div class="col-md-12">
         <div class="bd-example" data-example-id="">
            <div id="accordion" role="tablist" aria-multiselectable="true">
               
               
            </div>
         </div>
      </div>
   </div>
</div>
                                    </div>
            <div class="panel-footer" align='center'>
                                <input type='button' onclick='submitmapping()' class="btn btn-primary" value='Update' id='submit_mapping_rm_asm'>
                                
                        </div>
                </form>
            </div>
        </div>
    

    
    
</div>
    <style>



h2{float:left; width:100%; color:#fff; margin-bottom:30px; font-size: 14px;}
h2 span{font-family: 'Libre Baskerville', serif; display:block; font-size:45px; text-transform:none; margin-bottom:20px; margin-top:30px; font-weight:700}
h2 a{color:#fff; font-weight:bold;}


.card {
    -moz-box-direction: normal;
    -moz-box-orient: vertical;
    background-color: #fff;
    border-radius: 0.25rem;
    display: flex;
    flex-direction: column;
    position: relative;
    margin-bottom:1px;
    border:none;
}
.card-header:first-child {
    border-radius: 0;
}
.card-header {
    background-color: #f7f7f9;
    margin-bottom: 0;
    padding: 20px 1.25rem;
    border:none;
    
}
.card-header a i{
    float:left;
    font-size:25px;
    padding:5px 0;
    margin:0 25px 0 0px;
    color:#195C9D;
}
.card-header i{
    float:right;        
    font-size:30px;
    width:1%;
    margin-top:8px;
    margin-right:10px;
}
.card-header a{
    width:85%;
    float:left;
    color:#565656;
}
.card-header p{
    margin:0;
}

.card-header h3{
    margin:0 0 0px;
    font-size:20px;
    font-family: 'Slabo 27px', serif;
    font-weight:bold;
    color:#3fc199;
}
.card-block {
    -moz-box-flex: 1;
    flex: 1 1 auto;
    padding: 20px;
    color:#232323;
    box-shadow:inset 0px 4px 5px rgba(0,0,0,0.1);
    border-top:1px soild #000;
    border-radius:0;
}
    </style>
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
    function bring_district_from_state()
    {
        var state_name = $("#state_name").val();
        var agent_ID = $("#rm_asm").val();
        var data_string = "state_name="+state_name+"&agent_ID="+agent_ID;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/user/bring_district_from_state',
            data:{'state_name' : state_name, 'agent_ID' : agent_ID},
            success: function (response) {
               //alert(response);
               $("#accordion").html(response);
               $('[data-toggle=\"tooltip\"]').tooltip();
               
            }
        });
    }
    function selectall(m)
    {
        if($("#selectall"+m).prop("checked") == true){
                $(".myselectall"+m).each(function() {
                    $(this).prop('checked',true);
                });
            }
            else if($("#selectall"+m).prop("checked") == false){
                $(".myselectall"+m).each(function() {
                    $(this).prop('checked',false);
                });
            }
    }
    function submitmapping()
    {
        var state_name = $("#state_name").val();
        var agent_ID = $("#rm_asm").val();
        var submit = true;
        if(agent_ID == '' || agent_ID == null)
        {
            alert('Please select Agent');
            submit = false;
            return false;
        }
        if(state_name == '' || state_name == null)
        {
            alert('Please select States');
            submit = false;
            return false;
        }
        
        var dataString = $("#rm_state_mapping").serialize();
        if(submit)
        {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/user/rm_asm_district_mapping',
                data:dataString,
                beforeSend: function()
                {
                    $("#submit_mapping_rm_asm").val('Updating...');
                    $("#submit_mapping_rm_asm").prop('disabled',true);
                },
                success: function (response) {
                   
                    var data = JSON.parse(response);
                    alert(data.message);
                    $("#submit_mapping_rm_asm").val('Update');
                    $("#submit_mapping_rm_asm").prop('disabled',false);               
                }
            });
        }
    }
</script>