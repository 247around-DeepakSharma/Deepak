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
        Appliance Table
        <button type="button" class="btn btn-primary" id="submit_btn" onClick="window.location.href = '<?php echo base_url();?>employee/service_centre_charges/add_new_appliance_name';return false;" style="float:right"   value="Add"  >Add</button><!--
        
-->        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Appliance</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($appliance_name as $key => $row)  
                            {  
                               ?>
                        <tr>
                            <td><?php echo $row->services;?></td>
                            
                         
                            
                            <td>
                                <button id='<?php echo "updatebtn".$key;?>' class="btn btn-primary" onclick="loadupdatemodel('<?php echo $key;?>')" 
                                        
                                        value="update" data-services="<?php echo $row->services; ?>" 
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
                <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/update_appliance_name"  method="POST" enctype="multipart/form-data">
                    <div class="panel panel-info" >
                        <div class="panel-heading">Update Appliance Name</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--                            <div class="col-md-6 col-md-offset-2">-->
                               
                                <div class="form-group <?php if( form_error('appliance') ) { echo 'has-error';} ?>">
                                    <label for="appliance" class="col-md-4">Appliance Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="appliance" name="appliance" value="<?php echo set_value('appliance'); ?>" placeholder="Enter Appliance Name" required>
                                    <?php echo form_error('appliance'); ?>
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
           
           var appliance = $("#updatebtn"+key).attr('data-services');
           var id = $("#updatebtn"+key).attr('data-id');
           
          
          
           
           //$("#service").val(service).change();
           $("#rowid").val(id);
           $("#appliance").val(appliance);
           $("#updatemyModal").modal('toggle');
           
       }
       
    
    </script>