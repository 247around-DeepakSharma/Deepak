<style>
    #header_mapping_table_data_filter{
    display: none;
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
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <h2 class="col-md-6 col-sm-12 col-xs-12"> Upload File Header Mapping</h2>
        
            <div class="col-md-6 col-sm-12 col-xs-12" style="margin-top: 20px;margin-bottom: 10px;">
                <a href="javascript:void(0);" class="btn btn-primary pull-right" id="add_new_details">Add New</a>
            </div>
        </div>
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
            <div class="col-md-12" style="margin-top:20px;">
                <table id="header_mapping_table_data" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Partner</th>
                            <th>Referred Date and Time</th>
                            <th>Sub Order Id</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Product</th>
                            <th>Product Type</th>
                            <th>Customer Name</th>
                            <th>Customer Address</th>
                            <th>Pincode</th>
                            <th>CITY</th>
                            <th>Phone</th>
                            <th>Email ID</th>
                            <th>Delivery Date</th>
                            <th>Agent Name</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--add/edit Details-->
<div id="file_upload_header_mapping" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title_action"> </h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" id="mapping_details">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="partner_id">Partner</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="r_d_a_t">Referred Date and Time:</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="r_d_a_t" name="r_d_a_t" placeholder="Referred Date and Time">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="sub_order_id">Sub Order ID:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="sub_order_id" name="sub_order_id" placeholder="Sub Order ID" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="brand">Brand:</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="model">Model</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="model" name="model" placeholder="Model">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="product">Product</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product" name="product" placeholder="Product">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="product_type">Product Type</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="product_type" name="product_type" placeholder="Product Type" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="customer_name">Customer Name</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="customer_address">Customer Address</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Customer Address" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="pincode">Pincode</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="city">CITY</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="city" name="city" placeholder="City">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="phone">Phone</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="email_id">Email ID</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="email_id" name="email_id" placeholder="Email ID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-sm-offset-1" for="delivery_date">Delivery Date</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" id="delivery_date" name="delivery_date" placeholder="Delivery Date">
                            </div>
                        </div>
                    </div>

                    <div class="form-group"> 
                        <div class="col-sm-offset-3 col-sm-10">
                            <input type="hidden" class="btn btn-success" id="file_upload_header_mapping_id" name='file_upload_header_mapping_id' value="">
                            <input type="submit" class="btn btn-success" id="mapping_details_submit_btn" name='submit_type' value="Submit">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
    <!-- end add/edit Details -->
<script>
    var table;
    
    $(document).ready(function () {
        table = $('#header_mapping_table_data').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            pageLength: 25,
            ajax: {
                url: "<?php echo base_url(); ?>employee/bookings_excel/get_file_upload_header_mapping_data",
                type: "POST",
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3,4,5,6,7,8,9,10,11,12,13,14,15],
                    "orderable": false
                }
            ]
        });
      
    });
    
    $('#add_new_details').click(function(){
        get_partner();
        $("#mapping_details")[0].reset();
        $('#mapping_details_submit_btn').val('add');
        $('#modal_title_action').html("Add New Details");
        $('#file_upload_header_mapping').modal('toggle');
    });
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            success:function(response){
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    }
    
    $(document).on("click", "#edit_mapping_details", function () {
        var form_data = $(this).data('id');
        var options = "<option value='"+form_data.partner_id+"' selected='' readonly=''>"+form_data.public_name+"</option>";
        $('#partner_id').html(options);
        $('#r_d_a_t').val(form_data.referred_date_and_time);
        $('#sub_order_id').val(form_data.sub_order_id);
        $('#brand').val(form_data.brand);
        $('#model').val(form_data.model);
        $('#product').val(form_data.product);
        $('#product_type').val(form_data.product_type);
        $('#customer_name').val(form_data.customer_name);
        $('#customer_address').val(form_data.customer_address);
        $('#pincode').val(form_data.pincode);
        $('#city').val(form_data.city);
        $('#phone').val(form_data.phone);
        $('#email_id').val(form_data.email_id);
        $('#delivery_date').val(form_data.delivery_date);
        $('#file_upload_header_mapping_id').val(form_data.id);
        $('#mapping_details_submit_btn').val('edit');
        $('#modal_title_action').html("Edit Details");
        $('#file_upload_header_mapping').modal('toggle');
           
    });
    
    $("#mapping_details_submit_btn").click(function(){
        event.preventDefault();
        var arr = {};

        var form_data = $("#mapping_details").serializeArray();
        if($('#partner_id').val() === "" || $('#partner_id').val() === undefined || $('#partner_id').val() === null){
            alert("Please select Partner");
        }else if($('#product_type').val() === ""){
            alert("Please Fill Product Type");
        }else if($('#customer_name').val() === ""){
            alert("Please Fill Customer Name");
        }else if($('#pincode').val() === ""){
            alert("Please Fill Pincode");
        }else if($('#phone').val() === ""){
            alert("Please Fill Phone");
        }else{
            arr.name = 'submit_type'
            arr.value = $('#mapping_details_submit_btn').val();
            form_data.push(arr);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url();?>employee/bookings_excel/process_file_upload_header_mapping',
                data : form_data,
                success:function(response){
                    $('#file_upload_header_mapping').modal('toggle');
                    var data = JSON.parse(response);
                    if(data.response === 'success'){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(500);});   
                        $('#success_msg').html(data.msg);
                        table.ajax.reload();
                    }else if(data.response === 'error'){
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(500);});
                        $('#error_msg').html(data.msg);
                        table.ajax.reload();
                    }
                }
            });
        }

    });
</script>