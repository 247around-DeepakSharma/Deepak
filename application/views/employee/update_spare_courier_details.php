




<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
     Update Courier Details
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
        
        
     
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centers/process_update_spare_courier_details/<?php if(!empty($data)){ echo $data[0]['id'];} ?>"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Update Spare Parts Courier Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                <div class="form-group <?php if( form_error('shipped_parts') ) { echo 'has-error';} ?>">
                                    <label for="shipped_parts" class="col-md-4">Shipped Parts *</label>
                                    <div class="col-md-6">
                                       
                                        <input type="text" class="form-control" id="category" name="shipped_parts" value="<?php if(!empty($data)){ echo $data[0]['defective_part_shipped'];} ?>" placeholder="Enter Shipped Parts" required>
                                    <?php echo form_error('shippped_parts'); ?>
                                    </div>
                                   
                                </div>
                                <div class="form-group <?php if( form_error('courier_name') ) { echo 'has-error';} ?>">
                                    <label for="courier_name" class="col-md-4">Courier Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="courier_name" name="courier_name" value="<?php if(!empty($data)){ echo $data[0]['courier_name_by_sf'];} ?>" placeholder="Enter Courier Name" required>
                                    <?php echo form_error('courier_name'); ?>
                                    </div>
                                   
                                </div>
                                <div class="form-group <?php if( form_error('awb') ) { echo 'has-error';} ?>">
                                    <label for="awb" class="col-md-4">AWB *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="awb" name="awb" value="<?php if(!empty($data)){ echo $data[0]['awb_by_sf'];} ?>" placeholder="Enter AWB" required>
                                    <?php echo form_error('awb'); ?>
                                    </div>
                                   
                                </div>
                                <div class="form-group <?php if( form_error('courier_charge') ) { echo 'has-error';} ?>">
                                    <label for="courier_charge" class="col-md-4">Courier Charge *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="awb" name="courier_charge" value="<?php if(!empty($data)){ echo $data[0]['courier_charges_by_sf'];} ?>" placeholder="Enter Courier Charge" required>
                                    <?php echo form_error('courier_charge'); ?>
                                    </div>
                                   
                                </div>
                                <div class="form-group <?php if( form_error('defective_courier_receipt') ) { echo 'has-error';} ?>">
                                    <label for="defective_courier_receipt" class="col-md-4">Courier Invoice *</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control" id="defective_courier_receipt" name="defective_courier_receipt" >   
                                    <?php echo form_error('defective_courier_receipt'); ?>
                                    </div>
                                     
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($data[0]['defective_courier_receipt']) && !empty($data[0]['defective_courier_receipt'])) {
                                                    $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/misc-images/" . $data[0]['defective_courier_receipt'];
                                                    $image_src = base_url() . 'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src ?>" target="_blank"> <img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if (isset($data[0]['defective_courier_receipt']) && !empty($data[0]['defective_courier_receipt'])) { ?>
                                            <a href="javascript:void(0)" onclick="remove_image(<?php echo $data[0]['id'] ?>, '<?php echo $data[0]['defective_courier_receipt'] ?>', 'defective_courier_receipt')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                        
                                   
                                </div>
                                <div class="form-group <?php if( form_error('shipped_date') ) { echo 'has-error';} ?>">
                                    <label for="shipped_date" class="col-md-4">Shipped Date *</label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control" id="shipped_date" name="shipped_date" value = "<?php if(isset($data[0]['defective_part_shipped_date'])){ echo $data[0]['defective_part_shipped_date']; }?>" required>
                                    <?php echo form_error('shipped_date'); ?>
                                    </div>
                                   
                                </div>
                                <div class="form-group <?php if( form_error('remarks_by_sf') ) { echo 'has-error';} ?>">
                                    <label for="remarks_by_sf" class="col-md-4">Remarks by SF *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="remarks_by_sf" name="remarks_by_sf" value="<?php if(!empty($data)){ echo $data[0]['remarks_defective_part_by_sf'];} ?>" placeholder="Enter remarks by SF" required>
                                    <?php echo form_error('remarks_by_sf'); ?>
                                    </div>
                                   
                                </div>
                               
<!--                                <div class="form-group <?php //if( form_error('sf_challan_number') ) { echo 'has-error';} ?>">
                                    <label for="sf_challan_number" class="col-md-4">SF Challan Number *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="sf_challan_number" name="sf_challan_number" value="<?php //if(!empty($data)){ echo $data[0]['sf_challan_number'];} ?>" placeholder="Enter SF Challan Number" >
                                    <?php //echo form_error('sf_challan_number'); ?>
                                    </div>
                                   
                                </div>-->
                                
                                <div class="form-group <?php if( form_error('regenerate_challan_file') ) { echo 'has-error';} ?>">
                                    <label for="regenerate_challan_file" class="col-md-4">Regenerate Challan File *</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" class="form-control" id="sf_challan_number" name="regenerate_challan_file" value="1"  >
                                    <?php echo form_error('regenerate_challan_file'); ?>
                                    </div>
                                   
                                </div>
                               
                                <input type="hidden" name="booking_id" value="<?php if(!empty($data)){ echo $data[0]['booking_id'];} ?>">
                                <input type="hidden" name="sf_challan_number" value="<?php if(!empty($data)){ echo $data[0]['sf_challan_number'];} ?>">
                                <input type="hidden" name="partner_id" value="<?php if(!empty($data)){ echo $data[0]['partner_id'];} ?>">
                                <input type="hidden" name="entity_type" value="<?php if(!empty($data)){ echo $data[0]['entity_type'];} ?>">
                                <input type="hidden" name="service_center_id" value="<?php if(!empty($data)){ echo $data[0]['service_center_id'];} ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
   

            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
                    <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit"  />
 
            </div>

        </form>
    </div>
</div>

<script type="text/javascript">
    function remove_image(id, file_name, type) {
        var c = confirm('Do you want to permanently remove photo?');
        
        if (c) {
           $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/service_centers/remove_uploaded_image',
                data: {id: id, file_name: file_name, type: type},
                success: function (data) {
                    location.reload();
                }
            });
        } else {
        
            return false;
        }
    }
 
</script>