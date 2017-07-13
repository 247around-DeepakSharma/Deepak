<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                            Product Condition <!--<small>Float left</small>-->
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                         <?php if ($this->session->userdata('error')) {
                            echo '<br><br><div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                           <strong>' . $this->session->userdata('error') . '</strong>
                       </div>';
                        }
                        ?>
                        <div class="update_bb_order_details">
                            <form action="<?php echo base_url();?>buyback/buyback_process/process_received_bb_order_update" method="post" id="bb_order_update" role='form'>
                                <?php if (!empty($physical_condition)) { ?>
                                    <div class="row">
                                        <div class="physical_condition">
                                            <div class="form-group form-inline">
                                                <label for="order_physical_condition" class="col-md-3 col-sm-12 col-xs-12">Physical Condition:</label>
                                                <select class="form-control col-md-9 col-sm-12 col-xs-12" id="order_physical_condition" name="order_physical_condition">
                                                    <option selected disabled>Select Physical Condition</option>
                                                    <?php foreach ($physical_condition as $value) { ?> 
                                                        <option value="<?php echo $value['physical_condition']; ?>"><?php echo $value['physical_condition']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="working_condition">
                                            <div class="form-group form-inline">
                                                <label for="order_working_condition" class="col-md-3 col-sm-12 col-xs-12">Working Condition:</label>
                                                <select class="form-control col-md-9 col-sm-12 col-xs-12" id="order_working_condition" name="order_working_condition">
                                                    <option selected disabled>Select Working Condition</option>
                                                    <?php foreach ($working_condition as $value) { ?> 
                                                        <option value="<?php echo $value['working_condition']; ?>"><?php echo $value['working_condition']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="row">
                                        <div class="working_condition">
                                            <div class="form-group form-inline">
                                                <label for="order_working_condition" class="col-md-3 col-sm-12 col-xs-12">Working Condition:</label>
                                                <select class="form-control col-md-9 col-sm-12 col-xs-12" id="order_working_condition" name="order_working_condition">
                                                    <option selected disabled>Select Working Condition</option>
                                                    <?php foreach ($working_condition as $value) { ?> 
                                                        <option value="<?php echo $value['working_condition']; ?>"><?php echo $value['working_condition']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="remarks">
                                        <div class="form-group form-inline">
                                            <label for="remarks" class="col-md-3 col-sm-12 col-xs-12">Remarks:</label>
                                            <textarea class="col-md-9 col-sm-12 col-xs-12" rows="3" id="remarks" name="remarks" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>" id="service_id">
                                        <input type="hidden" name="city" value="<?php echo $city; ?>">
                                        <input type="hidden" name="category" value="<?php echo $category; ?>" id="order_category">
                                        <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                                        <input type="hidden" name="cp_id" value="<?php echo $cp_id; ?>" id="cp_id">
                                        <input type="submit" class="btn btn-success" id="submit" value="Submit" disabled="">
                                    </div>
                                </div>
                            </form>    
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<script>
    $("#order_physical_condition").select2();
    $("#order_working_condition").select2();
</script>-->
<script>
    
    $('#order_physical_condition').on('change',function(){    
            var category = $('#order_category').val();
            var physical_condition = $(this).val();
            var service_id = $('#service_id').val();
            var cp_id = $('#cp_id').val();
            if(category){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_working_condition",
                    data:{'category':category,'service_id':service_id,'physical_condition':physical_condition,'cp_id':cp_id},
                    success:function(response){
                        //console.log(response);
                        $('#order_working_condition').html(response);
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