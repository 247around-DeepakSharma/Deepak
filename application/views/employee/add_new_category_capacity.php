<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
     Add Appliance Category
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
        
     
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/process_add_new_category"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Add Appliance Category</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                
                                
                                
                                
                                <div class="form-group <?php if( form_error('service') ) { echo 'has-error';} ?>">
                                    <label for="service" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <select name="service" class="form-control" id="service"   required>
                                            <option selected disabled="">Please Select Appliance</option>
                                           <?php foreach ($services as $value) { ?>
                                            <option value="<?php echo $value->id;?>"  <?php if(set_value('service') == $value->id) {echo 'selected';} ?>><?php echo $value->services;?></option>
        
                                          <?php }?>
                                        </select>
                                        <?php echo form_error('service'); ?>
                                    </div>
                                    
                                </div>
                                                        
                                
                                <div class="form-group <?php if( form_error('category') ) { echo 'has-error';} ?>">
                                    <label for="category" class="col-md-4">Appliance Category *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo set_value('category'); ?>" placeholder="Enter Appliance Category" required>
                                    <?php echo form_error('category'); ?>
                                    </div>
                                   
                                </div>
                                
                                
                                
                                 <div class="form-group <?php if( form_error('capacity') ) { echo 'has-error';} ?>">
                                    <label for="capacity" class="col-md-4">Appliance Capacity *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="capacity" name="capacity" value="<?php echo set_value('capacity'); ?>" placeholder="Enter Capacity" required>
                                    <?php echo form_error('capacity'); ?>
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
