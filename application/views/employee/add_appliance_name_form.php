<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
     Add Appliance Name
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
        
     
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/process_add_new_appliance_name"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Add Appliance Name</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                
                                
                                
                                
                                   
                                
                                <div class="form-group <?php if( form_error('appliance') ) { echo 'has-error';} ?>">
                                    <label for="appliance" class="col-md-4">Appliance Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="appliance" name="appliance" value="<?php echo set_value('appliance'); ?>" placeholder="Enter Appliance Name" required>
                                    <?php echo form_error('appliance'); ?>
                                    </div>
                                   
                                </div>
                                  <div class="form-group">
                                    <label class="col-md-4">Is Walk In</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" class=" form-check-input" id="" name="walk_in" value="1">
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
