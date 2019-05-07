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
                            <th>Email Host</th>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="partner_id">Partner*</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="r_d_a_t">Referred Date and Time</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="r_d_a_t" name="r_d_a_t" placeholder="Referred Date and Time">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="sub_order_id">Order ID*</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sub_order_id" name="sub_order_id" placeholder="Order ID" required="">
                                </div>
                            </div>
                        </div>

                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="request_type">Request Type</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="request_type" name="request_type" placeholder="Request Type">
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="clearfix"></div>
                    <div class="row">



                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="brand">Brand</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand">
                                </div>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="model">Model</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="model" name="model" placeholder="Model">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div class="row">




                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="category">Category</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="category" name="category" placeholder="Category ">
                                </div>
                            </div>
                        </div>


                     <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="product">Product</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="product" name="product" placeholder="Product">
                                </div>
                            </div>   

                        </div>




 
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">



                                                <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="product_type">Product Description*</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="product_type" name="product_type" placeholder="Product Description" required="">
                                </div>
                            </div>
                        </div>


                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="customer_name">Customer Name*</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required="">
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="clearfix"></div>
                    <div class="row">



                                                <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="customer_address">Customer Address*</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Customer Address" required="">
                                </div>
                            </div>
                        </div>


                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="pincode">Pincode*</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode">
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="clearfix"></div>
                    <div class="row">


                                                <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="city">City</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="phone">Phone*</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required="">
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="clearfix"></div>
                    <div class="row">


                                                <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="email_id">Email ID</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="email_id" name="email_id" placeholder="Email ID">
                                </div>
                            </div>
                        </div>


                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="delivery_date">Delivery Date</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="delivery_date" name="delivery_date" placeholder="Delivery Date">
                                </div>
                            </div>
                        </div>


                    </div>




                        <div class="row">



                                                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="order_item_id">Order Item Id</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="order_item_id" name="order_item_id" placeholder="Order Item Id">
                                </div>
                            </div>
                        </div>

                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="spd">Service Before Date</label>
                                <div class="col-sm-8"> 
                                    <input type="text" class="form-control" id="spd" name="spd" placeholder="Service Before Date">
                                </div>
                            </div>
                        </div>


                  
                    </div>


<hr><hr>
                       <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="host">Email Host *</label>
                                <div class="col-sm-8"> 
                                    <input type="email" class="form-control" id="host" name="host" placeholder="Email Host">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="spd">File Type</label>
                                <div class="col-sm-8"> 
                                     <select class="form-control" id="filetype" name="filetype" >
                                         <option value="Delivered">Delivered</option>
                                          <option value="Shipped">Shipped</option>
                                     </select>
                                </div>
                            </div>
                        </div>
                    </div>



                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="host">Send File back</label>
                                <div class="col-sm-8"> 
                                    <select class="form-control" id="sendback" name="sendback" >
                                         
                                          <option value="0">No</option>
                                          <option value="1">Yes</option>
                                     </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="revertemail">Revert File Back Email </label>
                                <div class="col-sm-8"> 
                                     <input type="email" class="form-control" id="revertemail" name="revertemail" placeholder="Revert File Back Email">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="email_map_id" name="email_map_id"   value="" >

                    <div class="modal-footer">
                        <input type="hidden" class="btn btn-success" id="file_upload_header_mapping_id" name='file_upload_header_mapping_id' value="">
                        <input type="submit" class="btn btn-success" id="mapping_details_submit_btn" name='submit_type' value="Submit">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <p class="pull-left text-danger">* These Fields are required</p>
                    </div>
                </form>
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
            "lengthMenu": [[ 25, 50,100, -1], [ 25, 50, 100,"All"]],
            ordering: false,
            pageLength: 25,
            ajax: {
                url: "<?php echo base_url(); ?>employee/bookings_excel/get_file_upload_header_mapping_data",
                type: "POST",
            }
        });
      
    });
    
    $('#add_new_details').click(function(){
        get_partner();
        $("#mapping_details")[0].reset();
        $('#mapping_details_submit_btn').val('Add');
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


        $("#email_map_id").val(form_data.email_map_id)

        var options = "<option value='"+form_data.partner_id+"' selected='' readonly=''>"+form_data.public_name+"</option>";
        
        $('#partner_id').html(options);
        $('#r_d_a_t').val(form_data.referred_date_and_time);
        $("#request_type").val(form_data.category);
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
        $('#order_item_id').val(form_data.order_item_id);
        $('#spd').val(form_data.spd);


        $('#host').val(form_data.email_host);


var ty = form_data.file_type;


if (form_data.file_type!=null) {
  var filetype = ty.split("-")

if (filetype[1]=='Delivered') {
   $('#filetype').val("Delivered");
}else{

    $('#filetype').val("Shipped");   
}
  
}



 
        if (form_data.send_file_back==1) {

 
            $('#sendback').html('<option value="1" selected>Yes</option><option value="0">No</option>');
            // $('#sendback').text("Yes");

        }else{
           $('#sendback').html('<option value="1">Yes</option><option value="0" selected >No</option>');
            /// $('#sendback').val("0");
            // $('#sendback').text("No");


        }

        $('#revertemail').val(form_data.revert_file_to_email);
       ///  $('#spd').val(form_data.spd);





        $('#file_upload_header_mapping_id').val(form_data.id);
        $('#mapping_details_submit_btn').val('Save');
        $('#modal_title_action').html("Edit Details");
        $('#file_upload_header_mapping').modal('toggle');
           
    });
    
    $("#mapping_details_submit_btn").click(function(event){
        event.preventDefault();
        var arr = {};

  
  function isValidEmailAddress(emailAddress) {
        var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        return pattern.test(emailAddress);
 };

        var sendbackhost =$('#host').val(); 
       // var domain =  sendbackhost.split('.');

     //   alert(domain.length);
        
       // var regx = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);


// mystring = sendbackhost;
// yourstring = "bad & string";

 
//alert(sendbackhost.includes("."));
//alert(yourstring.match(validRegEx))


        var form_data = $("#mapping_details").serializeArray();
        if($('#partner_id').val() === "" || $('#partner_id').val() === undefined || $('#partner_id').val() === null){
            alert("Please select Partner");
        }else if($('#sub_order_id').val() === ""){
            alert("Please Fill  Order ID Field");
        }else if($('#product_type').val() === ""){
            alert("Please Fill Product Description Field");
        }else if($('#customer_name').val() === ""){
            alert("Please Fill Customer Name Field");
        }else if($('#customer_address').val() === ""){
            alert("Please Fill Customer Address Field");
        }else if($('#pincode').val() === ""){
            alert("Please Fill Pincode Field");
        }else if($('#phone').val() === ""){
            alert("Please Fill Phone Field");
        }else if($('#host').val() === ""){
            alert("Please Fill Email Host Field");
        }else if($('#filetype').val() === "" ){
            alert("Please Fill FileType Field");
        }else if($('#sendback').val() ==1 && $('#revertemail').val() ===""){
            alert("Please Fill Revert Back Email Field");
        }else if(sendbackhost.includes(".")===false){
            alert("Please enter valid domain");
        }
        else{
            arr.name = 'submit_type'
            arr.value = $('#mapping_details_submit_btn').val();
            form_data.push(arr);
        //    alert(form_data);
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
                        location.reload();
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