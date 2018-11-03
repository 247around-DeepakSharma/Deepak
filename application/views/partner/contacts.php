<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
                 <div id="container" style="margin: 0px 10px;" class="form_container">
                  <div class="panel-heading" style=" ">
                                <h1 style="color: #000;font-size: 20px;margin: 0px;"><b>Contact Persons</b></h1>  
                                <button class="btn" onclick="show_add_contact_form()" style="background-color:#4b9c7a;;color: #fff;margin-bottom: 10px;float: right;margin-top: -27px;">Add Contacts</button>
<!--                                <div class="clone_button_holder" style="float:right;margin-top: -31px;">
                                    
                                    <button class="clone btn btn-sm btn-info">Add</button>
                            <button class="remove btn btn-sm btn-info">Remove</button>
                                    </div>-->
                        </div> 
                 <form style="display:none;" name="contact_form" class="form-horizontal" id ="contact_form" action="<?php echo base_url() ?>employee/partner/process_partner_contacts" method="POST" enctype="multipart/form-data" onsubmit="return process_contact_persons_validations()">
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo $this->session->userdata('partner_id');?>>
                    
                        <div class="clonedInput panel panel-info " id="clonedInput1">
                        <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                            <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Name *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-contact-name"  name="contact_person_name[]" id="contact_person_name_1" value = "" placeholder="Enter Name">
                                            </div>
                                        </div>
                                       <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Email *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_email[]" id="contact_person_email_1" value = "" placeholder="Enter Email">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Contact Number *</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_contact[]" id="contact_person_contact_1" value = "" placeholder="Enter Contact">
                                            </div>
                                        </div>
                                         <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Alternate Email </label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_alt_email[]" id="contact_person_alt_email_1" value = "" placeholder="Alternative Email">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Alternate Contact Number</label>
                                            <div class="col-md-6">
                                                <input  type="text" class="form-control input-model"  name="contact_person_alt_contact[]" id="contact_person_alt_contact_1" value = "" placeholder="Alternative Contact">
                                            </div>
                                        </div> 
                                        <div class="form-group "> 
                                            <input type="hidden" value="" id="checkbox_value_holder_1" name="checkbox_value_holder[]">
                                              <div class="col-md-6"> 
                                                  <label><b>Create Login</b></label><input style="margin-left: 167px;" type="checkbox" value="" id="login_checkbox_1" name="login_checkbox[]" checked="">
                                            </div>   
                                                  </div>
                                </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Department *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control"  id="contact_person_department_1" name="contact_person_department[]" onChange="getRoles(this.value,this.id)" >
                                                    <option value="" disabled="" selected="">Select Department</option>
                                                <?php
                                                foreach ($department as $value){
                                                ?> 
                                                     <option value="<?php echo $value['department'] ?>"> <?php echo $value['department'] ?></option>
                                                <?php
                                                }
                                                ?>
                                                            </select>  
                                          </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Role *</label>
                                            <div class="col-md-6">
                                                <select disabled="" type="text" class="form-control"  id="contact_person_role_1" name="contact_person_role[]" onChange="getFilters(this.value,this.id)" >
                                                    <option value = "">Select Roles</option>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="form-group ">
                                            <input type="hidden" value="" id="states_value_holder_1" name="states_value_holder[]">
                                            <label for="service_name" class="col-md-4">States <button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #f7a35c;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="Applicable only for roles, where state filter is required eg - Area Sales Manager">?</button> </label>
    <div class="col-md-6" id="add_state">
                                                <div class="filter_holder" id="filter_holder_1">
                                                    <select multiple="" class=" form-control contact_person_states" name ="contact_person_states[0][]" id="contact_person_states_1" disabled="">
                                                      <option value = "">Select States</option>
                                                <?php
                                                    foreach ($select_state as $state) {
                                                        ?>
                                                <option value = "<?php echo $state['state'] ?>">
                                                    <?php echo $state['state']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                                  </div>
                                            </div>
                                        </div> 
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Permanent Address</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-model"  name="contact_person_address[]" id="contact_person_address_1" value = "" placeholder="Enter Address"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Correspondence Address</label>
                                            <div class="col-md-6">
                                                <textarea  type="text" rows="1" class="form-control input-model"  name="contact_person_c_address[]" id="contact_person_c_address_1" value = "" placeholder="Enter Address"></textarea>
                                            </div>
                                        </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                               <div class="form-group " style="text-align:center">
                                   <input type="submit" class="btn btn-primary" value="Save Contacts" style='background-color: #4b9c7a; border-color: #4b9c7a;'>
                    </div>
                        </div>
                    <div class="cloned"></div>
                </form>
                  <?php
                    if(!empty($contact_persons)){
                        ?>
                <div id="exist_documents">
                    <table class="table">
                        <thead>
                            <tr style="background: #4b9c7a;color: #fff;">
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Is Login</th>
                                <th>Permanent Address</th>
<!--                                <th>Alt Email</th>
                                <th>Alt Contact</th>
                                <th>Correspondence Address</th>-->
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                                foreach($contact_persons as $value){
                                    $index ++;
                                ?>
                            <tr>
                                <td><?php echo $index; ?></td>
                                <td><?php echo $value['name'] ?></td>
                                <td><?php echo $value['department'] ?></td>
                                <td><?php echo $value['role'] ?></td>
                                <td><?php echo $value['official_email'] ?></td>
                                <td><?php echo $value['official_contact_number'] ?></td>
                                <td><?php if($value['login_agent_id']){echo "Yes"; } else { echo "No"; } ?></td>
                                <td><?php echo $value['permanent_address'] ?></td>
<!--                                 <td><?php echo $value['alternate_email'] ?></td>
                                <td><?php echo $value['alternate_contact_number'] ?></td>
                                <td><?php echo $value['correspondence_address'] ?></td>-->
                                <td><button type="button" class="btn btn-info btn-sm" onclick="create_edit_form(this.value)" data-toggle="modal"  id="edit_button" value='<?=json_encode($value)?>'><i class="fa fa-edit"></i></button>
                                    <a  class="btn btn-danger btn-sm" href="<?php echo base_url();?>employee/partner/delete_partner_contacts/<?php echo $value['id'];?>/<?php echo  $value['entity_id']?>" title="Delete" onclick="return confirm('Are you sure you want to delete this contact?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                  }
                                  ?>
                                </table>
            </div>
                                <?php
                                    }
                                    ?>
             </div>
                </div>
            </div>
        </div>
    </div>
</div>    
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="    background-color: #4b9c7a;color: #Fff;text-align: center;margin: 0px;border-color: #4b9c7a;">
                <button type="button" class="close btn-primary well" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Contact</h4>
            </div>
            <div class="modal-body">
                <form name="edit_contact_form" action="<?php echo base_url().'employee/partner/edit_partner_contacts'?>" class="form-horizontal" id ="edit_contact_form" method="POST" enctype="multipart/form-data" onsubmit="return edit_contact_persons_validations()">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <input type="hidden" id="contact_id" name="contact_id" value=""/>
                                    <label for="service_name" class="col-md-4 vertical-align">Name *</label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control input-contact-name"  name="contact_person_name" id="contact_person_name" value = "" placeholder="Enter Name">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Email *</label>
                                    <div class="col-md-6">
                                        <input  type="email" class="form-control input-model"  name="contact_person_email" id="contact_person_email" value = "" placeholder="Enter Email">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Contact Number*</label>
                                    <div class="col-md-6">
                                        <input  type="number" class="form-control input-model"  name="contact_person_contact" id="contact_person_contact" value = "" placeholder="Enter Contact">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Alternate Email </label>
                                    <div class="col-md-6">
                                        <input  type="email" class="form-control input-model"  name="contact_person_alt_email" id="contact_person_alt_email" value = "" placeholder="Alternative Email">
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Alternate Contact No.</label>
                                    <div class="col-md-6">
                                        <input  type="number" class="form-control input-model"  name="contact_person_alt_contact" id="contact_person_alt_contact" value = "" placeholder="Alternative Contact">
                                    </div>
                                </div> 
                                <div class="clear"></div>
                                <div class="form-group "> 
                                    <input type="hidden" value="" id="checkbox_value_holder" name="checkbox_value_holder">
                                    <div class="col-md-6"> 
                                        <label><b>Create Login</b></label><input style="margin-left: 33%;padding-top: 5%;" type="checkbox" value="" id="login_checkbox" name="login_checkbox">
                                    </div>   
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Department *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="contact_person_department" name="contact_person_department" onChange="getEditRole(this.value)">
                                            <option value="" disabled="">Select Department</option>
                                            <?php
                                            foreach ($department as $values) {
                                                ?> 
                                                <option value="<?php echo $values['department'] ?>"> <?php echo $values['department'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select> 
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Role *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="contact_person_role" name="contact_person_role" onChange="getFilters(this.value,'edit')" >
                                            
                                        </select>
                                        <!--<input type="text"value="<?php// echo $value['role'] ?>" onclick="{$('#contact_person_role').removeClass('hidden');this.addClass('hidden');}"/>-->
                                    </div>                                                                <div class="clear"></div>
                                </div> 
                                <div class="form-group ">
                                    <input type="hidden" value="" id="states_value_holder" name="states_value_holder">
                                    <label for="service_name" class="col-md-4 vertical-align">States </label>
                                    <div class="col-md-6">
                                        <div class="filter_holder" id="filter_holder">
                                            <select multiple="" class=" form-control contact_person_states well well-lg" name ="contact_person_states[]" id="contact_person_states">
                                                <!--<option value = "" disabled>Select States</option>-->
                                                <?php
                                                foreach ($select_state as $value) {
                                                    ?>
                                                    <option value = "<?php echo $value['state']?>" >
                                                                <?php echo $value['state']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div> 
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Permanent Address</label>
                                    <div class="col-md-6">
                                        <textarea  type="text" rows="2" class="form-control input-model"  name="contact_person_address" id="contact_person_address" value = "" placeholder="Enter Address"></textarea>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="form-group ">
                                    <label for="service_name" class="col-md-4 vertical-align">Correspondence Address</label>
                                    <div class="col-md-6">
                                        <textarea  type="text" rows="2" class="form-control input-model"  name="contact_person_c_address" id="contact_person_c_address" value = "" placeholder="Enter Address"></textarea>
                                        <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $this->session->userdata('partner_id');?>>
                                        <input type="hidden" id="agentid" name="agentid" value="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <input type="submit" value="Update" class=" btn btn-primary" style="background: #4b9c7a;float:right;">
            </form>
        </div>
    </div>

</div>
    </div>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<!--Modal ends-->
<script>
    $('.contact_person_states').select2({
        placeholder: "Select State",
        allowClear: true
    });
     function show_add_contact_form(){
        $('#contact_form').toggle();  
    }
   function getFilters(role,id){
       if(id == 'edit'){
           containerID = "contact_person_states";
       }
        else{
            divID = id.split("_")[3];
            containerID = "contact_person_states_"+divID;
        }
        var data = {role:role};
        url =  '<?php echo base_url(); ?>employee/partner/get_partner_roles_filters';
        sendAjaxRequest(data,url,"POST").done(function(response){
            if(response == 1){
                $("#"+containerID).prop('disabled', false);
            }
            else{
                $("#"+containerID).prop('disabled', true);
            }
        });
    }
    function edit_contact_persons_validations(){
            name = $("#contact_person_name").val();
            email = $("#contact_person_email").val();
            contact = $("#contact_person_contact").val();
            department = $("#contact_person_department").val();
            role = $("#contact_person_role").val();
            states = getMultipleSelectedValues("contact_person_states");
            if(name && email && contact && department && role){ 
                $('#states_value_holder').val(states);
                 return true;
            }
            else{
                alert('Please add all mendatory fields');
                return false;
            }
    }
      function process_contact_persons_validations(){
        var div_count = $('.input-contact-name').length;
        for(var i=1;i<=div_count;i++){
            name = $("#contact_person_name_"+i).val();
            email = $("#contact_person_email_"+i).val();
            contact = $("#contact_person_contact_"+i).val();
            department = $("#contact_person_department_"+i).val();
            role = $("#contact_person_role_"+i).val();
            states = getMultipleSelectedValues("contact_person_states_"+i);
            if(name && email && contact && department && role){ 
                $('#checkbox_value_holder_'+i).val($('#login_checkbox_'+i).is(':checked'));
                $('#states_value_holder_'+i).val(states);
            }
            else{
                alert('Please add all mendatory fields');
                return false;
            }
        }
        return true;
    }
    function getMultipleSelectedValues(fieldName){
    fieldObj = document.getElementById(fieldName);
    var values = [];
    var length = fieldObj.length;
    for(var i=0;i<length;i++){
       if (fieldObj[i].selected == true){
           values.push(fieldObj[i].value);
       }
    }
   return values.toString();
}
function getEditRole(department){
        $("#contact_person_states").val('').change();
        $("#contact_person_states").prop("disabled", true);
        var data = {department:department};
        url =  '<?php echo base_url()?>employee/partner/get_partner_roles/'+department;
        sendAjaxRequest(data,url,"POST").done(function(response){
            $("#contact_person_role").html(response);
        });
    }
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    function getRoles(department,id){
        divID = id.split("_")[3];
        var data = {department:department};
        url =  '<?php echo base_url(); ?>employee/partner/get_partner_roles/'+department;
        sendAjaxRequest(data,url,"POST").done(function(response){
            $("#contact_person_role_"+divID).prop('disabled', false);
            $("#contact_person_role_"+divID).html(response);
        });
    }
    function create_edit_form(json){
        var value = JSON.parse(json);
        var data="";
        if(value.state){
            var states=value.state;
            var states = states.split(',');
            var Values = new Array();
            $("#contact_person_states").prop("disabled", false);
            for(var element in states){
                var state=states[element];
                $('#contact_person_states option[value="'+state+'"]').select2().attr("selected", "selected");
                Values.push(state);
            }
            $("#contact_person_states").val(Values).trigger('change');
        }
        else{
            $("#contact_person_states").val('').change();
             $("#contact_person_states").prop("disabled", true);
        }
        if(value.login_agent_id){
          $("#checkbox_value_holder").val(true);
          $( "#login_checkbox" ).prop( "checked", true );
        }
        else{
          $("#checkbox_value_holder").val(false);
          $( "#login_checkbox" ).prop( "checked", false );
        }
        $("#contact_id").val(value.id);
        $("#contact_person_name").val(value.name);
        $("#contact_person_email").val(value.official_email);
        $("#contact_person_contact").val(value.official_contact_number);
        $("#contact_person_alt_email").val(value.alternate_email);
        $("#contact_person_alt_contact").val(value.alternate_contact_number);
        data = "<option value = '' disabled>Select Roles</option><option value = "+value.role_id+" selected>"+value.role+"</option>";
        $("#contact_person_role").html(data);
        $('select[name="contact_person_department"]').find('option[value='+value.department+']').attr("selected",true);
        $("#contact_person_address").val(value.permanent_address);
        $("#contact_person_c_address").val(value.correspondence_address);
        if(value.agentid){
            $("#login_checkbox").prop('checked',true);
            $("#login_checkbox_holder").val(true);
            $("#agentid").val(value.agentid);
        }
        $("#myModal").modal("show");
    }
    </script>
    <style>
        .select2-container{
            width: 192px !important;
        }
        #add_state .select2-container{
            width: 307px !important;
        }
    </style>
    