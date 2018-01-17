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
                            <label class="control-label col-sm-3" for="l_32">Less than 32" Bracket</label>
                            <div class="col-sm-9"> 
                                <input type="number" class="form-control" id="l_32" name="l_32" placeholder='Less than 32" Bracket' required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="g_32">32" And Above Bracket</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="g_32" name="g_32" placeholder='32" And Above Bracket' required="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group"> 
                        <div class="col-sm-offset-3 col-sm-10">
                            <input type="submit" class="btn btn-success" id="inventory_stocks_form_submit_btn" name='submit_type' value="Submit">
                        </div>
                    </div>
                </form>

        </div>
    </div>
    <script>
    $('#sf_id').select2();
        
    $("#inventory_stocks_form_submit_btn").click(function(){
        $('#inventory_stocks_form_submit_btn').val('Processing...');
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