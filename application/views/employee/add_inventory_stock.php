<style>
    .update-form{
        border:1px solid #e6e6e6;
        padding: 20px;
        border-radius: 5px;
    }
    .select2-container .select2-selection--single{
        height: 34px;
    }
</style>
<div id="page-wrapper" >
    <div class="row">
        <h1>Add Inventory Stock</h1>
        <hr>
        <div class="success_msg_div" style="display:none;">
            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"></span></strong>
            </div>
        </div>
        <div class="error_msg_div" style="display:none;">
            <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="error_msg"></span></strong>
            </div>
        </div>
        <div class="update-form col-md-6 col-md-offset-3">
            <form class="form-horizontal" id="inventory_stocks_form">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="sf_id">Service Center</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="sf_id" name="sf_id" required="">
                                    <option value="" selected="selected" disabled="">Select Service Center</option>
                                    <?php foreach ($sf as $val) { ?>
                                        <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="inventory">Inventory Type</label>
                            <div class="col-sm-9"> 
                                <select class="form-control" id="inventory" name="inventory" required="">
                                    <option value="" selected="selected" disabled="">Select Inventory Type</option>
                                    <?php foreach ($inventory as $val) { ?>
                                        <option value="<?php echo $val['part_number']; ?>"><?php echo $val['part_name']." (".$val['part_number'].")" ; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
<!--                        <div class="form-group">
                            <label class="control-label col-sm-3" for="l_32">Less than 32" Bracket</label>
                            <div class="col-sm-9"> 
                                <input type="number" class="form-control" id="l_32" name="l_32" placeholder='Less than 32" Bracket' required="">
                            </div>
                        </div>-->
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="quantity">Quantity</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="quantity" name="quantity" placeholder='quantity' required="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group"> 
                        <div class="col-sm-offset-3 col-sm-10">
                            <button type="submit" class="btn btn-success" id="inventory_stocks_form_submit_btn">Submit</button>
                        </div>
                    </div>
                </form>

        </div>
    </div>
    <script>
        
    var sf_id = '<?php echo $sf_id;?>';    
    $('#sf_id').select2();
    $('#inventory').select2();
    
    if(sf_id){
        $('#sf_id').val(sf_id);
        $('#sf_id').trigger('change');
    }
    
    $("#inventory_stocks_form_submit_btn").click(function(){
        $('#inventory_stocks_form_submit_btn').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        event.preventDefault();

        var form_data = $("#inventory_stocks_form").serializeArray();
        if($('#sf_id').val() === "" || $('#sf_id').val() === undefined || $('#sf_id').val() === null){
            alert("Please select Service Center");
        }else{
            $.ajax({
                type:'POST',
                url:'<?php echo base_url();?>employee/inventory/process_update_inventory_stock',
                data : form_data,
                success:function(response){
                    $('#inventory_stocks_form_submit_btn').html("Submit").attr('disabled',false);
                    var data = JSON.parse(response);
                    if(data.response === 'success'){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(500);});   
                        $('#success_msg').html(data.msg);
                        $('#inventory_stocks_form_submit_btn').val('Submit');
                        $("#inventory_stocks_form")[0].reset();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(500);});
                        $('#error_msg').html(data.msg);
                        $('#inventory_stocks_form_submit_btn').val('Submit');
                    }
                }
            });
        }

    });
    </script>
</div>