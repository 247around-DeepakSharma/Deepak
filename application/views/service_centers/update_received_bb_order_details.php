<div class="update_order_Details" style="margin-top: 20px;">
    <div class="container">
        <form action="<?php echo base_url();?>employee/service_centers/process_received_bb_order_update" method="post" id="bb_order_update" role='form' enctype="multipart/form-data">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h2>Product Condition</h2>
                        <?php if ($this->session->userdata('error')) {
                            echo '<br><br><div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                           <strong>' . $this->session->userdata('error') . '</strong>
                       </div>';
                        }
                        ?>
                <hr>
        </div>
        <hr>
        <div class="row">
            <?php if(!empty($physical_condition)){?>
            <div class="col-xs-12 col-sm-12 col-md-2" id="phy_con">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_physical_condition" name="order_physical_condition">
                            <option selected disabled>Select Physical Condition</option>
                            <?php foreach ($physical_condition as $value) { ?> 
                                <option value="<?php echo $value['physical_condition']; ?>"><?php echo $value['physical_condition']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_working_condition" name="order_working_condition">
                            <option selected disabled>Select Working Condition</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php } else{ ?>
             <div class="col-xs-12 col-sm-12 col-md-2">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_working_condition" name="order_working_condition">
                            <option selected disabled>Select Working Condition</option>
                            <?php foreach ($working_condition as $value) { ?> 
                                <option value="<?php echo $value['working_condition']; ?>"><?php echo $value['working_condition']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <hr>
        <div class="row">
            <div class="remarks">
                <div class="form-group form-inline">
                    <label for="remarks" class="col-md-2">Remarks:</label>
                    <textarea class="col-md-8" rows="3" id="remarks" name="remarks" required></textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="service_id" value="<?php echo $service_id; ?>" id="service_id">
                <input type="hidden" name="city" value="<?php echo $city; ?>">
                <input type="hidden" name="category" value="<?php echo $category; ?>" id="order_category">
                <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                <input type="submit" class="btn btn-success" id="submit" value="Submit">
            </div>
        </div>
       </form> 
    </div>
</div>
<script>
    $("#order_physical_condition").select2();
    $("#order_working_condition").select2();
</script>
<script>
    
    $('#order_physical_condition').on('change',function(){    
            var category = $('#order_category').val();
            var physical_condition = $(this).val();
            var service_id = $('#service_id').val();
            if(category){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_working_condition",
                    data:{'category':category,'service_id':service_id,'physical_condition':physical_condition},
                    success:function(response){
                        $('#order_working_condition').val('val', "");
                        $('#order_working_condition').val('Select Working Condition').change();
                        $('#order_working_condition').select2().html(response);
                    }
                            
                }); 
            }else{
                $('#order_working_condition').html('<option value="">Select Physical Condition First</option>'); 
            }
        });
</script>
<?php 
$this->session->unset_userdata('success');
$this->session->unset_userdata('error');
?>