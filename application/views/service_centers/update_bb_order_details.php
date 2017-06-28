<style>
    .col-md-3 {
    width: 22%;
}
.info-labal{
    font-size: 10px;
    border: 1px solid #e6e6e6;
    border-radius: 50%;
    padding: 2px 4px;
}
</style>
<div class="update_order_Details" style="margin-top: 20px;">
    <div class="container">
        <form action="<?php echo base_url();?>process_report_issue_bb_order_details" method="post" id="bb_order_update" role='form' enctype="multipart/form-data">
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
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_services" name="services">
                            <option selected disabled>Select Product</option>
                            <?php foreach ($products as $value) { ?> 
                                <option value="<?php echo $value->id; ?>"><?php echo $value->services; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_category" name="category">
                            <option selected disabled>Select Category</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-2" id="phy_con">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_physical_condition" name="order_physical_condition">
                            <option selected disabled>Select Physical Condition</option>
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
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div>
                    <div class="form-group">
                        <select class="form-control" id="order_brand" name="order_brand">
                            <option selected disabled>Select Brand</option>
                        </select>
                    </div>
                </div>
            </div>
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
        <div class="row">
            <div class="order_upload_file">
                <div class="form-group form-inline">
                    <label for="order_files" class="col-md-2 info_tooltip"><i class="fa fa-info info-labal text-danger" data-toggle="tooltip" title="You can upload multiple images"></i> &nbsp;Upload Order Id Image:</label>
                    <input type="file" class="form-control col-md-8" id="order_files" name="order_files[]" accept="image/*" multiple required></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="order_upload_file">
                <div class="form-group form-inline">
                    <label for="damaged_order_files" class="col-md-2 info_tooltip"><i class="fa fa-info info-labal text-danger" data-toggle="tooltip" title="You can upload multiple images"></i> &nbsp;Upload Damaged Product Images:</label>
                    <input type="file" class="form-control col-md-8" id="damaged_order_files" name="damaged_order_files[]" accept="image/*" multiple required></textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                <input type="hidden" name="city" value="<?php echo $city; ?>">
                <input type="hidden" name="partner_order_key" value="" id="partner_order_key">
                <input type="submit" class="btn btn-success" id="submit" value="Submit">
            </div>
        </div>
       </form> 
    </div>
</div>
<script>
    $(document).ready(function() {
    $('.info_tooltip').tooltip();
});
</script>
<script>
    var order_id = "<?php echo $order_id; ?>";
    var service_id = "<?php echo $service_id; ?>";
    var city = "<?php echo $city; ?>";
    
    $("#order_services").select2();
    $("#order_category").select2();
    $("#order_physical_condition").select2();
    $("#order_working_condition").select2();
    $("#order_brand").select2();

    $(document).ready(function () {
        $.ajax({
            method: "POST",
            url: "<?php echo base_url(); ?>employee/service_centers/get_bb_order_brand",
            data:{'service_id':service_id},
            success: function (response) {
                //console.log(response);
                $("order_brand").prop('required',true);
                $('#order_brand').val('val', "");
                $('#order_brand').val('Select Brand').change();
                $('#order_brand').select2().html(response);
            }
        });
    });
        
        
        $('#order_services').on('change',function(){    
            var product_service_id = $(this).val();
            
            if(product_service_id){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_category_size",
                    data:{'product_service_id':product_service_id},
                    success:function(response){
                        $('#order_category').val('val', "");
                        $('#order_category').val('Select Category').change();
                        $('#order_category').select2().html(response);
                    }
                            
                }); 
            }else{
                $('#order_category').html('<option value="">Select Category First</option>'); 
            }
        });
        
        
        $('#order_category').on('change',function(){    
            var category = $(this).val();
            
            if(category){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_physical_condition",
                    data:{'category':category,'service_id':service_id},
                    success:function(response){
                        if(response === 'empty'){
                            $('#order_physical_condition').val('val', "");
                            $('#order_physical_condition').val('Select Physical Condition').change();
                            $('#phy_con').hide();
                        }else{
                            $('#order_physical_condition').val('val', "");
                            $('#order_physical_condition').val('Select Physical Condition').change();
                            $('#order_physical_condition').select2().html(response);
                        }
                        
                    }
                            
                }); 
            }else{
                $('#order_physical_condition').html('<option value="">Select Category First</option>'); 
            }
        });
        
        $('#order_physical_condition').on('change',function(){    
            var category = $('#order_category').val();
            var physical_condition = $(this).val();
            
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
        
        $('#order_brand').on('change',function(){
            var services = $('#order_services').val();
            var category = $('#order_category').val();
            var physical_condition = $('#order_physical_condition').val();
            if(physical_condition === null){
                physical_condition = '';
            }
            var working_condition = $('#order_working_condition').val();
            var brand = $(this).val();
            
            if(category === '' && working_condition === '' && brand === ''){
                alert('Please Select All Field');
            }else if(category === null && physical_condition === null && working_condition === null && brand === null){
                console.log("initiate");
            }else{
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/check_bb_order_key",
                    data:{'category':category,'physical_condition':physical_condition,'working_condition':working_condition,'brand':brand,'city':city,'order_id':order_id,'services': services},
                    success:function(response){
                        $('#partner_order_key').val(response);
                    }
                            
                }); 
            }
        });
        
</script>
<?php 
$this->session->unset_userdata('success');
$this->session->unset_userdata('error');
?>