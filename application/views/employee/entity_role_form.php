<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
     Add New Entity
   </h1>
        
        
       
        
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
        
        
     
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/process_add_new_entity_role"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Add New Entity</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                
                                
                                
                                
                                <div class="form-group <?php if( form_error('entity_type') ) { echo 'has-error';} ?>">
                                    <label for="entity_type" class="col-md-4">Entity Type *</label>
                                    <div class="col-md-6">
                                        <select name="entity_type" class="form-control" id="entity_type" required="">
                                            <option selected disabled>Select Entity Type</option>
                                            <option value="247around" <?php if(set_value('entity_type') == "247around"){echo 'selected';} ?>>247around</option>
                                            <option value="vendor" <?php if(set_value('entity_type') == "vendor") {echo 'selected';} ?>>Vendor</option>
                                            <option value="partner" <?php if(set_value('entity_type') == "partner") {echo 'selected';} ?>>Partner</option>
                                        </select>
                                        <?php echo form_error('entity_type'); ?>
                                    </div>
                                   
                                </div> 
                                
                                <div class="form-group <?php if( form_error('department') ) { echo 'has-error';} ?>">
                                    <label for="department" class="col-md-4">Department *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="department" name="department" value="<?php echo set_value('department'); ?>" placeholder="Enter Department" required>
                                    <?php echo form_error('department'); ?>
                                    </div>
                                   
                                </div>
                                
                                
                                
                                 <div class="form-group <?php if( form_error('role') ) { echo 'has-error';} ?>">
                                    <label for="role" class="col-md-4">Role *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="role" name="role" value="<?php echo set_value('role'); ?>" placeholder="Enter Role" required>
                                    <?php echo form_error('role'); ?>
                                    </div>
                                   
                                </div>
                                
                                
                                
                                 <div class="form-group <?php if( form_error('filter') ) { echo 'has-error';} ?>">
                                    <label for="filter" class="col-md-4">Filter *</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" class="form-control" id="filter" name="filter" value="1">
                                    <?php echo form_error('filter'); ?>
                                    </div>
                                   
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          
   

            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
            <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
 
            </div>

        </form>
    </div>
</div>
