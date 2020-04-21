<div id="page-wrapper" >
<div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
                 <?php if ($this->session->userdata('failed')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><?php echo $this->session->userdata('failed') ?></strong>
                    </div>
             <?php } ?>
        
           
                 <?php if ($this->session->userdata('success')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><?php echo $this->session->userdata('success') ?></strong>
                    </div>
             <?php } ?>
        
             <?php
//             
               $this->session->unset_userdata('success');
               $this->session->unset_userdata('failed');
//            
             ?>
        
<div class="panel panel-info" >
    <div 
        class="panel-heading">
        Entity Role Table  
        <button type="button" class="btn btn-primary" id="submit_btn" onClick="window.location.href = '<?php echo base_url();?>employee/service_centre_charges/add_new_entity_role';return false;" style="float:right"   value="Add"  >Add</button><!--
        
-->        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Entity Type</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Is Filter Applicable</th>
                            <th>Date</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($entity_role_data as $key => $row)  
                            {  //print_r($row);
                               ?>
                        <tr>
                            <td><?php echo $row->entity_type;?></td>
                            <td><?php echo $row->department;?></td>
                            <td><?php echo $row->role;?></td>
                            <td><?php if($row->is_filter_applicable==0)
                                echo "No";  
                                else echo "Yes";?></td>
                            <td><?php echo date("d-M-Y", strtotime($row->create_date));?></td>
                            <td>
                                <button id='<?php echo "updatebtn".$key;?>' class="btn btn-primary" onclick="loadupdatemodel('<?php echo $key;?>')" 

                                        value="update" data-entity_type="<?php echo $row->entity_type; ?>"  
                                        data-department="<?php echo $row->department; ?>"  
                                        data-role="<?php echo $row->role; ?>"  data-is_filter_applicable="<?php echo $row->is_filter_applicable; ?>" 
                                        data-id="<?php echo $row->id; ?>">Update</button>
                                
                            </td>
                        </tr>
                        <?php }  
                            ?>  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="updatemyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!--                                          <h4 class="modal-title">Add New Entity</h4>-->
            </div>
            <div class="modal-body">
                <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/update_entity_role"  method="POST" enctype="multipart/form-data">
                    <div class="panel panel-info" >
                        <div class="panel-heading">Update Entity</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--                            <div class="col-md-6 col-md-offset-2">-->
                                    <div class="form-group <?php if( form_error('entity_type') ) { echo 'has-error';} ?>">
                                        <label for="entity_type" class="col-md-4">Entity Type *</label>
                                        <div class="col-md-6">
                                            <select name="entity_type" class="form-control" id="entity_type" required>
                                                <option selected disabled>Select Entity Type</option>
                                                <option value="247around" <?php if(set_value('entity_type') == "247around") {echo 'selected';} ?>>247around</option>
                                                <option value="vendor" <?php if(set_value('entity_type') == "vendor") {echo 'selected';} ?>>Vendor</option>
                                                <option value="partner" <?php if(set_value('entity_type') == "partner") {echo 'selected';} ?>>Partner</option>
                                            </select>
                                            <?php echo form_error('entity_type'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('department') ) { echo 'has-error';} ?>">
                                        <label for="department" class="col-md-4">Department *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="department" name="department"  placeholder="Enter Department" >
                                            <?php echo form_error('department'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('role') ) { echo 'has-error';} ?>">
                                        <label for="role" class="col-md-4">Role *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="role" name="role" value="" placeholder="Enter Role" >
                                            <?php echo form_error('role'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('filter') ) { echo 'has-error';} ?>">
                                        <label for="filter" class="col-md-4">Filter *</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" class="form-control" id="filter" name="filter" value="1" >
                                            <?php echo form_error('filter'); ?>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="rowid" id="rowid" value="">
                                </div>
                                <!--                        </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                        <center>
                        <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    
    <script>
       function loadupdatemodel(key){
           var entity_type = $("#updatebtn"+key).attr("data-entity_type");
           var department = $("#updatebtn"+key).attr("data-department");
           var role = $("#updatebtn"+key).attr('data-role');
           var id = $("#updatebtn"+key).attr('data-id');
           var is_filter_applicable = $("#updatebtn"+key).attr("data-is_filter_applicable");
           
       
            
           $("#filter").val(1);
           $("#role").val(role);
           $("#rowid").val(id);
           $("#department").val(department);
           $("#entity_type").val(entity_type).change();;
           $("#updatemyModal").modal('toggle');
           
       }
    </script>