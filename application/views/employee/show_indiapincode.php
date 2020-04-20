<style>
    #inventory_master_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #inventory_master_list_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>India Pincode List</h3>
                </div>
<!--                <div class="col-md-6">
                    <a class="btn btn-success pull-right" style="margin-top: 10px;" id="add_master_list" title="Add Item"><i class="fa fa-plus"></i></a>
                </div>-->
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
        <div class="inventory-table">
            <table class="table table-bordered table-hover table-striped" id="inventory_master_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Pincode</th>
                        <th>Division</th>
                        <th>Area</th>
                        <th>Region</th>
                        <th>Taluk</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Edit</th>
 
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    <!--Modal start-->
    <div id="inventory_master_list_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action" style="display: inline-block;"> </h4> <span id="error_id" style="display: inline-block; margin-left: 100px;"></span>
                </div>
                <div class="modal-body">
                   
                    <form class="form-horizontal" id="master_list_details">
 
                     <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">Pincode*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="pincode" name="pincode">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">Division*</label>
                                    <div class="col-md-7 col-md-offset-1">                                        
                                        <input type="text" class="form-control" id="division" name="division">
                                    </div>
                                </div>
                            </div>
                        </div>               
   
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">Area*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="area" name="area">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">Region*</label>
                                    <div class="col-md-7 col-md-offset-1">                                        
                                        <input type="text" class="form-control" id="region" name="region">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                          <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">Taluk*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="taluk" name="taluk">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">District*</label>
                                    <div class="col-md-7 col-md-offset-1">                                        
                                        <input type="text" class="form-control" id="district" name="district">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden"  name="id" id="pinid"  />
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="part_number">State*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" id="state" name="state">
                                    </div>
                                </div>
                            </div>
<!--                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="type">District*</label>
                                    <div class="col-md-7 col-md-offset-1">                                        
                                        <input type="text" class="form-control" id="division" name="district">
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        
                        
            
                        
                        <div class="modal-footer">
                            <input type="hidden"  id="entity_type" name='entity_type' value="partner">                            
                            <input type="hidden"  id="inventory_id" name='inventory_id' value="">
                            <button type="submit" class="btn btn-success" id="master_list_submit_btn" name='submit_type' value="Submit">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <p class="pull-left text-danger">* These fields are required</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
</div>
<script>
 
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function(){
        get_indiapincode_list();
    });
    
 
    
    $(".allowNumericWithDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) || e.ctrlKey) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(".allowNumericWithOutDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46,8, 9, 27, 13, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) || e.ctrlKey) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    function get_indiapincode_list(){
        indiapincode_master_list_table = $('#inventory_master_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0,1, 2,3,4, 5,6]
                    },
                    title: 'india_pincode_list_'+time,
                   
                },
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/vendor/getIndiaPincodes",
                "type": "POST",
                data: function(d){

                }
            },
            "deferRender": true       
        });
    }
    
  
    
    $('#add_master_list').click(function(){
        $('#model_number_div').show();
        get_partner('entity_id');
        $('#service_id').val(null).trigger('change');
        $('#service_id').select2({
            placeholder: 'Select Appliance'
        });
        $("#master_list_details")[0].reset();
        $('#master_list_submit_btn').val('Add');
        $('#modal_title_action').html("Add New Inventory");
        $('#inventory_master_list_data').modal('toggle');
    });
    
    $('#entity_id, #service_id').on('change',function(){
        var service_id = $('#service_id').val();
        var entity_id = $('#entity_id').val();
        
        if(service_id && entity_id){
            
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_appliance_model_number',
                data:{partner_id:entity_id,entity_type: '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id:service_id},
                success:function(data){   
                    console.log(data);
                    $("#model_number_id").html(data);
                }
            });
            $("#model_number_id").select2();
        }
        
    });
    
  

    
    $(document).on("click", "#edit_master_details", function () {
        $('#model_number_div').hide();
        var form_data = $(this).data('id');
        $('#area').val(form_data.area);
        $('#pincode').val(form_data.pincode);
        $('#division').val(form_data.division);
        $('#taluk').val(form_data.taluk);
        $('#district').val(form_data.district);
        $('#state').val(form_data.state);
        $('#region').val(form_data.district);
        $('#pinid').val(form_data.id);
 
        $('#master_list_submit_btn').val('Edit');
        $('#modal_title_action').html("Edit Details");
        $('#inventory_master_list_data').modal('toggle');
         
    });
    
    $("#master_list_submit_btn").click(function(){
        event.preventDefault();
        var arr = {};
        var form_data = $("#master_list_details").serializeArray();
        if(!$('#pincode').val()){
            alert('Please Enter Pincode');
        }else if(!$('#area').val()){
            alert("Please Enter Area");
        }else if(!$('#taluk').val()){
            alert("Please Enter Taluk");
        }else if(!$('#region').val()){
            alert("Please Enter Region");
        }else if(!$('#division').val()){
            alert("Please Enter Division");
        }else if(!$('#district').val()){
            alert("Please Enter District");
        }else if(!$('#state').val()){
            alert("Please Enter State");
        }else{
            $('#master_list_submit_btn').attr('disabled',true).html("<i class = 'fa fa-spinner fa-spin'></i> Processing...");
            arr.name = 'submit_type';
            arr.value = $('#master_list_submit_btn').val();
            form_data.push(arr);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>employee/vendor/process_updateIndiaPincode',
                data : form_data,
                success:function(response){
                    
                    var data = JSON.parse(response);
                    console.log(response);
                    
                    if(data.response == '247'){
                        alert("Duplicate Entry ! Data already exist")
                        
                    }else{
                    
                    if(data.response === 'success'){
                        $('#inventory_master_list_data').modal('toggle');
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(data.msg);
                        indiapincode_master_list_table.ajax.reload();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(data.msg);
                        $('#error_id').html(data.msg).css('color','red');
                        indiapincode_master_list_table.ajax.reload();
                    }
                    
                    }

                    $('#master_list_submit_btn').attr('disabled',false).html('Submit');
                }
            });
        }

    });
 
 
 
 
    

 
</script>