<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<style>
    .fa_custom{
        border: 1px solid #333;
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 10px;
    }
    .select2-container{
        width: 100%!important;
    }
    .form-inline .form-control{
        width: 100%;
    }
</style>
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
                        <?php
                        if ($this->session->userdata('error')) {
                            echo '<br><br><div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                           <strong>' . $this->session->userdata('error') . '</strong>
                       </div>';
                        }
                        ?>
                        <form class="form-horizontal" action="<?php echo base_url(); ?>buyback/buyback_process/process_report_issue_bb_order_details" method="post" id="bb_order_update" role='form' enctype="multipart/form-data">    
                            <div class="update_bb_order_details">
                                <div class="row">
                                    <div class="form-group form-inline">
                                        <label for="order_services" class="col-md-4">Select Product</label>
                                        <div class="col-md-6">
                                            <select class="form-control" id="order_services" name="services">
                                                <option selected disabled>Select Product</option>
                                                <?php foreach ($products as $value) { ?> 
                                                    <option value="<?php echo $value->id; ?>"><?php echo $value->services; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group form-inline">
                                        <label for="order_category" class="col-md-4">Select Category</label>
                                        <div class="col-md-6">
                                            <select class="col-md-6 form-control" id="order_category" name="category">
                                                <option selected disabled>Select Category</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group form-inline" id="phy_con">
                                        <label for="order_physical_condition" class="col-md-4">Select Physical Condition</label>
                                        <div class="col-md-6">
                                            <select class="col-md-6 form-control" id="order_physical_condition" name="order_physical_condition">
                                                <option selected disabled>Select Physical Condition</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group form-inline">
                                        <label for="order_working_condition" class="col-md-4">Select Working Condition</label>
                                        <div class="col-md-6">
                                            <select class="col-md-6 form-control" id="order_working_condition" name="order_working_condition">
                                                <option selected disabled>Select Working Condition</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group form-inline">
                                        <label for="order_brand" class="col-md-4">Select Brand</label>
                                        <div class="col-md-6">
                                            <select class="col-md-6 form-control" id="order_brand" name="order_brand">
                                                <option selected disabled>Select Brand</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="claimed_price">
                                        <div class="form-group form-inline">
                                            <label for="claimed_price" class="col-md-4">Claimed Price:</label>
                                            <div class="col-md-6">
                                                <input class="form-control"  id="claimed_price" name="claimed_price" onkeypress="return isNumberKey(event)" required>
                                                <span id="claimed_price_error" class="text-danger text-center" style="display:none;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="remarks">
                                        <div class="form-group form-inline">
                                            <label for="remarks" class="col-md-4">Remarks:</label>
                                            <div class="col-md-6">
                                                <textarea  class="form-control" rows="3" id="remarks" name="remarks" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="order_upload_file">
                                        <div class="form-group form-inline">
                                            <label for="order_files" class="col-md-4 info_tooltip">Upload Order Id Image:</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control" id="order_files" name="order_files" accept="image/*" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="order_upload_file">
                                        <div class="form-group form-inline">
                                            <label for="damaged_order_files" class="col-md-4 info_tooltip"><i class="fa fa-info info-labal text-info fa_custom" data-toggle="tooltip" title="You can upload multiple images"></i> &nbsp;Upload Damaged Product Images:</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control" id="damaged_order_files" name="damaged_order_files[]" accept="image/*" multiple >
                                            </div>
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
                                        <input type="hidden" name = "cp_basic_charge" value="<?php echo round($cp_basic_charge[0]['cp_basic_charge']); ?>" id="cp_basic_charge">
                                        <input type="submit" class="btn btn-success" id="submit" value="Submit">
                                    </div>
                                </div>
                            </div>    
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
    var cp_id = "<?php echo $cp_id; ?>";
    
    $("#order_services").select2();
    $("#order_category").select2();
    $("#order_physical_condition").select2();
    $("#order_working_condition").select2();
    $("#order_brand").select2();
    
    
    function get_bb_brand(){
        var service_id = $('#order_services').val();
        $.ajax({
            method: "POST",
            url: "<?php echo base_url(); ?>employee/service_centers/get_bb_order_brand",
            data:{'service_id':service_id,'cp_id':cp_id},
            success: function (response) {
                //console.log(response);
                $("order_brand").prop('required',true);
                $('#order_brand').val('val', "");
                $('#order_brand').html(response).change();;
            }
        });
    }
    
    $('#order_services').on('change',function(){    
            var product_service_id = $(this).val();
            if(product_service_id){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_category_size",
                    data:{'product_service_id':product_service_id,'cp_id':cp_id},
                    success:function(response){
                        //console.log(response);
                        $('#order_category').val('val', "");
                        $('#order_category').html(response).change();
                        $('#order_physical_condition').val('val', "");
                        $('#order_physical_condition').val('Select Physical Condition').change();
                        $('#phy_con').show();
                        get_bb_brand();
                    }
                            
                }); 
            }else{
                $('#order_category').html('<option value="">Select Category First</option>'); 
            }
        });
        
        $('#order_category').on('change',function(){    
            var category = $(this).val();
            var service_id = $('#order_services').val();
            
            if(category){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_physical_condition",
                    data:{'category':category,'service_id':service_id,'cp_id':cp_id},
                    success:function(response){
                        //console.log(response);
                        if(response === 'empty'){
                            $('#order_physical_condition').val('val', "");
                            $('#order_physical_condition').val('Select Physical Condition').change();
                            $('#phy_con').hide();
                        }else{
                            $('#order_physical_condition').val('val', "");
                            $('#order_physical_condition').html(response).change();
                        }
                        
                    }
                            
                }); 
            }else{
            //console.log("shbhsb");
               $('#order_physical_condition').val('val', "");
               $('#order_physical_condition').val('Select Physical Condition').change();
            }
        });
        
        $('#order_physical_condition').on('change',function(){    
            var category = $('#order_category').val();
            var physical_condition = $(this).val();
            var service_id = $('#order_services').val();
            
            if(category){
                $.ajax({
                    method:'POST',
                    url:"<?php echo base_url(); ?>employee/service_centers/get_bb_order_working_condition",
                    data:{'category':category,'service_id':service_id,'physical_condition':physical_condition,'cp_id':cp_id},
                    success:function(response){
                        //console.log(response);
                        $('#order_working_condition').val('val', "");
                        $('#order_working_condition').select2().html(response).change();
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
                    data:{'category':category,'physical_condition':physical_condition,'working_condition':working_condition,'brand':brand,'city':city,'order_id':order_id,'services': services, 'cp_id':cp_id},
                    success:function(response){
                        $('#partner_order_key').val(response);
                    }
                            
                }); 
            }
        });
        
        $("#claimed_price").blur(function() {
            var claimed_price = $(this).val();
            var final_claimed_price = Math.round(($('#cp_basic_charge').val() * .30),0);
            var alert_msg = 'Price You Entered Is Too Low. We can not accept entered amount for this order.Please Enter an amount greater than or equal to <b>Rs.' + final_claimed_price + '</b>';
            if(claimed_price < final_claimed_price){
                //alert(alert_msg);
                $('#claimed_price_error').html(alert_msg).show();
                $(this).css({'border-color' : '#a94442'});
                $('#submit').attr('disabled','disabled');
            }else{
                $('#claimed_price_error').html('').hide();
                $(this).css({'border-color' : '#ccc'});
                $('#submit').removeAttr('disabled');
            }
        });
        
        function isNumberKey(evt)
        {
           var charCode = (evt.which) ? evt.which : evt.keyCode;
           if (charCode !== 46 && charCode > 31 
             && (charCode < 48 || charCode > 57))
              return false;

           return true;
        }
        
</script>
<?php
$this->session->unset_userdata('success');
$this->session->unset_userdata('error');
?>