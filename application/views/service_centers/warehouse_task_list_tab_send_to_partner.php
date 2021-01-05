<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid">
    <input type="hidden" value="" name="receiver_partner_id" id="receiver_partner_id">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
          <h2>Received Defective/Ok Parts</h2>
         <div class="panel panel-default">            
            <div class="panel-body">
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
                
                <div class="x_content_header">
                    <section class="fetch_inventory_data">
                        <div class="row">
                            <div class="form-inline">
                                <div class="form-group col-md-4">
                                    <form class="form-inline" action="#" method="POST">

                                        <label for="partner_id">Select Partner</label>
                                        <select class="form-control" id="partner_id_send_to_partner" name="partner_id" required="">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                        <div id="partner_err"></div>
                                </div>                              
                                <button type="submit" class="btn btn-success btn-sm col-md-2" id="partner_search" style="margin-top: 22px;">Submit</button>                          
                                
                                   </form>                                
                            </div>
                            <div class="approved pull-right">
                                <div class="btn btn-info btn-sm send_all_spare pull-right" id="send_spare_to_partner" style="margin-top: 11px;" onclick="process_send_all_spare();">Send spare to partner</div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="clearfix"></div>
                <hr>
                
                <?php if(!empty($spare_parts)) { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="defective_parts_send_to_partner">
                        <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">Defective Parts Shipped</th>
                            <th class="text-center">Appliance</th>
                            <th class="text-center">Parts Code</th>
                            <th class="text-center">Model</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">SF Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">
                                Send To Partner
                                <input type="checkbox" id="send_all">
                            </th>
                            <th class="text-center hidden">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                            <?php  foreach($spare_parts as $key =>$row){?>
                            <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                        <?php if (empty($this->session->userdata('warehouse_id'))) { ?>
                                        <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                        <?php } else { ?>
                                        <a  href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id'];?></a>
                                        <?php } ?>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['defective_part_shipped']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['services']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['shipped_part_number']; ?>
                                    </td>
                                    
                                     <td>
                                        <?php echo $row['model_number_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['defective_part_shipped_date'])){  echo date("d/m/Y",strtotime($row['defective_part_shipped_date'])); }  ?>
                                    </td>
                                    <td>
                                        <?php echo $row['vendor_name']; ?>
                                    </td>
                                   <td>
                                        <?php echo $row['awb_by_sf']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_sf']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['remarks_defective_part_by_sf']; ?>
                                     </td>

                                     <td><?php echo $row['shipped_quantity']?>
                                     	
                                     <input type="hidden" readonly="readonly" min="1" value="<?php echo $row['shipped_quantity']?>" data-shipping_quantity="<?php echo $row['shipped_quantity']?>" id="spare<?php echo $row['id']?>" name="shipping_quantity">

                                     </td>
                                    <td>
                                        
                                        <input type="checkbox" class="check_single_row" data-is_micro_wh ="<?php echo $row['is_micro_wh'];?>" data-defective_return_to_entity_type ="<?php echo $row['defective_return_to_entity_type']; ?>" data-defective_return_to_entity_id="<?php echo $row['defective_return_to_entity_id'];?>" data-entity_type ="<?php echo $row['entity_type']; ?>" data-service_center_id ="<?php echo $row['service_center_id']; ?>" data-part_name ="<?php echo $row['defective_part_shipped']; ?>" data-model="<?php echo $row['model_number_shipped']; ?>" data-shipped_inventory_id = "<?php echo $row['shipped_inventory_id']?>" data-booking_id ="<?php echo $row['booking_id']?>" data-partner_id = "<?php echo $row['partner_id']?>" data-spare_id = "<?php echo $row['id']?>" data-booking_partner_id = "<?php echo $row['booking_partner_id']?>">
                                    </td>
                                    <td class="hidden">
                                        <a href="javascript:void(0);" class="btn btn-warning" title="Reverse Defective/Ok Part Acknowledged By Warehouse" onclick="reverse_acknowledged_from_sf(<?php echo $row['id']; ?>)">Reverse</a>
                                    </td>
                            </tr>
                            <?php $sn_no++; } ?>
                        </tbody>
                        </table>
                    </div>
                <?php }else { ?>
                
                <div class="alert alert-danger">
                    <div class="text-center"><?php if(isset($filtered_partner)) { echo "No Data Found "; }else { echo "Please Select Partner";}?></div>
                </div>
                <?php } ?>
               </div>
            </div>
         </div>
      </div>
    
    <!-- courier Information when warehouse Shipped defective parts to partner -->
    <div class="courier_model">
        <div id="courier_model" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" style="width:100% !important;">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Please Provide Courier Details</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="courier_model_form" method="post" novalidate="novalidate">
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="awb_by_wh" class="col-md-4">AWB *</label>
                                    <div class="col-md-8">
                                        <input type="text" onblur="check_awb_exist()" class="form-control"  id="awb_by_wh" name="awb_by_wh" placeholder="Please Enter AWB" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                    <label for="courier_name_by_wh" class="col-md-4">Courier Name *</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="courier_name_by_wh" name="courier_name_by_wh" required="">
                                            <option selected="" disabled="" value="">Select Courier Name</option>
                                            <?php foreach ($courier_details as $value1) { ?> 
                                                <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('courier_name'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="courier_price_by_wh" class="col-md-4">Courier Price *</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control"  id="courier_price_by_wh" name="courier_price_by_wh" placeholder="Please Enter Courier Price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_date_by_wh" class="col-md-4">Courier Shipped Date *</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="defective_parts_shippped_date_by_wh" name="defective_parts_shippped_date_by_wh" placeholder="Please enter Shipped Date" required="true" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Large Box Count</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="shipped_spare_parts_boxes_count" name="shipped_spare_parts_boxes_count"  required="">
                                            <option selected="" value="">Select Boxes</option>
                                            <?php for ($i = 1; $i < 31; $i++) { ?>
                                                <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="shipped_spare_parts_boxes_count" class="col-md-4">Small Box Count</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="shipped_spare_parts_small_boxes_count" name="shipped_spare_parts_small_boxes_count"  required>
                                            <option selected="" value="">Select Small Boxes</option>
                                            <?php for ($i = 1; $i < 31; $i++) { ?>
                                            <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Weight *</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" style="width: 25%; display: inline-block;" id="shipped_spare_parts_weight_in_kg" name="spare_parts_shipped_kg" value="" placeholder="Weight" required=""> <strong> in KG</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="text" class="form-control" style="width: 25%; display: inline-block;" id="shipped_spare_parts_weight_in_gram"   value=""   name="spare_parts_shipped_gram" placeholder="Weight" required="">&nbsp;<strong>in Gram </strong> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Courier Pic *</label>
                                    <div class="col-md-8">
                                        <input type="hidden" class="form-control"  id="exist_courier_image" name="exist_courier_image" >
                                        <input type="file" class="form-control"  id="defective_parts_shippped_courier_pic_by_wh" name="defective_parts_shippped_courier_pic_by_wh" required>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="from_gst_number" class="col-md-4">From GST Number *</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="from_gst_number" required>
                                            <option selected disabled value="">Select from GST number</option>
                                            <?php
                                            foreach ($from_gst_number as $gst_numbers => $gst_number) {
                                            ?>
                                            <option value="<?php echo $gst_number['id']  ?>"><?php echo $gst_number['state']." - ".$gst_number['gst_number'] ?></option>
                                            <?php    
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="to_gst_number" class="col-md-4">To GST Number *</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="to_gst_number" required>
                                            <option selected disabled value="">Select To GST number</option>
                                            <?php
                                            if(!empty($to_gst_number)) {
                                                foreach ($to_gst_number as $gst_numbers => $gst_number) {
                                                ?>
                                                <option value="<?php echo $gst_number['id']  ?>"><?php echo $gst_number['state']." - ".$gst_number['gst_number'] ?></option>
                                                <?php    
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <h4 class="modal-title">E-Way Bill Details</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="eway_vehicle_number" class="col-md-4">Vehicle Number</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="eway_vehicle_number" name="eway_vehicle_number" placeholder="Please Enter Vehicle Number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="eway_bill_by_wh" class="col-md-4">E-Way Bill Number </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="eway_bill_by_wh" name="eway_bill_by_wh" placeholder="Please Enter E-Way Bill Number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_ewaybill_pic_by_wh" class="col-md-4">E-Way Bill File </label>
                                    <div class="col-md-8">
                                        <input type="hidden" class="form-control"  id="exist_ewaybill_image" name="exist_ewaybill_image" >
                                        <input type="file" class="form-control"  id="defective_parts_shippped_ewaybill_pic_by_wh" name="defective_parts_shippped_ewaybill_pic_by_wh" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="text-center">
                            <span id="same_awb" style="display:none">This AWB already used same price will be added</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submit_courier_form">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>

            </div>
          </div>
    </div>
   </div>

<script>
    $('#defective_parts_send_to_partner').DataTable({
       pageLength:100,
       dom: 'Bfrtip',
       "language": {                
                "searchPlaceholder": "Search by Any Column",
            },
        // Configure the drop down options.
        lengthMenu: [
            [ 100, 200,500, -1 ],
            [ '100', '200', '500', 'All' ]
        ],
        // Add to buttons the pageLength option.
        buttons: [
            'pageLength','excel',
        ]
    });
    
     $("#partner_search").click(function(){         
         var partner_id = $("#partner_id_send_to_partner").val();
         if(partner_id==null){
            $("#partner_err").html('Please Select Partner.').css({'color':'red'});
            return false;
         }else{
             $("#partner_err").html('');
             load_view_send_to_partner('service_center/send_to_partner_list', '#tabs-5',partner_id);
         }
         
     });
   
   function load_view_send_to_partner(url, tab,partner_id){
    
       //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>" + url,
            data: {is_ajax:true,partner_id:partner_id},
            success: function (data) {
                $(tab).html(data);                
                if(tab === '#tabs-2'){
                    //Adding Validation   
                    $("#selectall_address").change(function(){
                        var d_m = $('input[name="download_courier_manifest[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_manifest').prop('checked', false); 
                            $('#selectall_manifest').prop('checked', false); 
                        }
                    $(".checkbox_address").prop('checked', $(this).prop("checked"));
                    });
    
                    $("#selectall_manifest").change(function(){
                        var d_m = $('input[name="download_address[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_address').prop('checked', false); 
                            $('#selectall_address').prop('checked', false); 
                        }
                    $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
                    }); 
                }
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }
    
    function reverse_acknowledged_from_sf(spare_id) {
        if(confirm("Are you sure you want to reverse the parts acknowledged by warehouse?") == true) {
            $.ajax({
                method : 'POST',
                url : '<?php echo base_url(); ?>employee/service_centers/reverse_acknowledged_from_sf',
                data : {spare_id}
            }).done(function() {
                alert("Part has been reversed successfully.");
                load_view_send_to_partner('service_center/send_to_partner_list', '#tabs-5', $("#partner_id_send_to_partner").val());
            }).fail(function() {
                alert("Some error occured.");
            });
        } 
    }
</script>
<script>
    
    $('#partner_id_send_to_partner').select2({
        placeholder:'Select Partner',
        allowClear:true
    });
    
    var Around_GST_ID = 7;
    $(document).ready(function(){
        var sf_id = '<?php echo $sf_id?>';
        Around_GST_ID = ((sf_id == 804) ? 6 : 7 );
        $('#from_gst_number option[value="'+Around_GST_ID+'"]').prop('selected',true);
        get_partner_ack();
    });
    
    var postData = {};
    $("#defective_parts_shippped_date_by_wh").datepicker({
        dateFormat: 'dd/mm/yy',
         changeMonth: true,
         changeYear: true,
         maxDate: "0",
         minDate: -3

     });
//    $("#defective_parts_ewaybill_date_by_wh").datepicker({dateFormat: 'dd/mm/yy', changeMonth: true,changeYear: true});
    $('#send_all').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });
    
    function process_send_all_spare(){
        
        var tmp_arr = {};
        var flag = false;
        $(".check_single_row:checked").each(function (key) {
            tmp_arr[key] = {};
            tmp_arr[key]['inventory_id'] = $(this).attr('data-shipped_inventory_id');
            tmp_arr[key]['is_micro_wh'] = $(this).attr('data-is_micro_wh');
            tmp_arr[key]['booking_id'] = $(this).attr('data-booking_id');
            tmp_arr[key]['partner_id'] = $(this).attr('data-partner_id');
            tmp_arr[key]['defective_return_to_entity_id'] = $(this).attr('data-defective_return_to_entity_id');
            tmp_arr[key]['defective_return_to_entity_type'] = $(this).attr('data-defective_return_to_entity_type');
            tmp_arr[key]['spare_id'] = $(this).attr('data-spare_id');
            tmp_arr[key]['part_name'] = $(this).attr('data-part_name');
            tmp_arr[key]['service_center_id'] = $(this).attr('data-service_center_id');
            tmp_arr[key]['sent_entity_type'] = $(this).attr('data-entity_type');
            tmp_arr[key]['model'] = $(this).attr('data-model');
            tmp_arr[key]['booking_partner_id'] = $(this).attr('data-booking_partner_id');
            tmp_arr[key]['shipping_quantity'] = $("#spare"+$(this).attr('data-spare_id')).val();
            flag = true;
        });
        
        postData['data'] = JSON.stringify(tmp_arr);
        postData['sender_entity_id'] =  '<?php echo $sf_id?>';
        postData['sender_entity_type'] = '<?php echo _247AROUND_SF_STRING; ?>';
        postData['wh_name'] = '<?php echo $this->session->userdata('wh_name')?>';
        postData['receiver_partner_id'] = $("#partner_id_send_to_partner").val();
        
        if(flag){
            if(postData['receiver_partner_id']){
                $('#courier_model').modal('toggle');
            }else{
                alert("Please Select Partner");
            }
        }else{
            alert("Please Select At Least One Checkbox");
        }
    }
    
    $('#submit_courier_form').on('click',function(){
        $(".check_single_row").prop('checked', false);
        $("#send_spare_to_partner").attr('disabled',true);
        $('#submit_courier_form').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        postData['awb_by_wh'] = $('#awb_by_wh').val();
        postData['courier_name_by_wh'] = $('#courier_name_by_wh').val();
        postData['courier_price_by_wh'] = $('#courier_price_by_wh').val();
        postData['defective_parts_shippped_date_by_wh'] = $('#defective_parts_shippped_date_by_wh').val().split("/").reverse().join("-");
        postData['exist_courier_image'] = $('#exist_courier_image').val();
        postData['from_gst_number'] = $('#from_gst_number').val();
        postData['to_gst_number'] = $('#to_gst_number').val();
        postData['eway_bill_by_wh'] = $('#eway_bill_by_wh').val();
        postData['eway_vehicle_number'] = $('#eway_vehicle_number').val();
        postData['shipped_spare_parts_boxes_count'] = $('#shipped_spare_parts_boxes_count').val() || 0;
        postData['shipped_spare_parts_small_boxes_count'] = $('#shipped_spare_parts_small_boxes_count').val() || 0;
        var total_boxes = postData['shipped_spare_parts_boxes_count']+postData['shipped_spare_parts_small_boxes_count'];
        postData['shipped_spare_parts_weight_in_kg'] = $('#shipped_spare_parts_weight_in_kg').val();
        postData['shipped_spare_parts_weight_in_gram'] = $('#shipped_spare_parts_weight_in_gram').val();
        var exist_courier_image = $("#exist_courier_image").val();       
        
        //Declaring new Form Data Instance  
        var formData = new FormData();
                
        //Getting Files Collection
        var files = $("#defective_parts_shippped_courier_pic_by_wh")[0].files;
        
        var eway_files = $("#defective_parts_shippped_ewaybill_pic_by_wh")[0].files;
        
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < files.length; i++) {
            formData.append('file', files[i]);
        }
        
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < eway_files.length; i++) {
            formData.append('eway_file', eway_files[i]);
        }
        
        var is_exist_file = false;
        if(exist_courier_image){
            is_exist_file = true;
        }
        
        if(files.length >= 1){
            is_exist_file = true;
        }
        //Now Looping the parameters for all form input fields and assigning them as Name Value pairs. 
        $.each(postData, function(index, element) {
            formData.append(index, element);
        });
        if(!/^\d+(\.\d+)?$/g.test(postData['courier_price_by_wh'])){              //should be number only with one decimal 
            $('#submit_courier_form').html("Submit").attr('disabled',false);
            alert("Courier price should be numerical and should not contain alphabets and special characters except decimal.")
            return false;
        }


        let kg = $("#shipped_spare_parts_weight_in_kg").val();
        let gm = $("#shipped_spare_parts_weight_in_gram").val();
        let total = parseInt(kg)+parseInt(gm);
        if(!total){
        swal("Error !", "Sum of weight in KG and GM must be greater than 0");
        return false;
        }

        var courier_price= parseFloat(postData['courier_price_by_wh']);
        if(courier_price<0 || courier_price>2000){                              //should be in between 0 and 2000
            $('#submit_courier_form').html("Submit").attr('disabled',false);
            alert('Courier price should be in between 0 and 2000.');
            return false;
        }
        if(postData['awb_by_wh'] && postData['courier_name_by_wh'] && postData['courier_price_by_wh'] && postData['defective_parts_shippped_date_by_wh'] && is_exist_file && postData['from_gst_number'] && postData['to_gst_number'] && total_boxes > 0 && postData['shipped_spare_parts_weight_in_kg']  && postData['shipped_spare_parts_weight_in_gram']){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/send_defective_parts_to_partner_from_wh',
                data:formData,
                contentType: false,
                processData: false,
                success:function(response){
                    $("#send_spare_to_partner").attr('disabled',false);
                    $('#submit_courier_form').html('Submit').attr('disabled',false);
                    $('#courier_model').modal('toggle');
                    obj = JSON.parse(response);
                    if(obj.status){
                        $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(obj.message);
                        alert(obj.message);
                        window.location.reload();
                    }else{
                        $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(obj.message);
                    }
                }
            });
        }else{
            $("#send_spare_to_partner").attr('disabled',false);
            $('#submit_courier_form').html('Submit').attr('disabled',false);
            if(total_boxes == 0){
                alert("Minimum box count should be 1, Please select from Large or small box count.");
            }else{
                alert("Please enter all required field");
        }
        }
        
    });
    
    function get_partner_ack(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/service_centers/warehouse_ack_partner_list',
            data:{is_wh:true},
            success:function(response){
                if(response === 'Error'){
                    
                } else {
                    $('#partner_id_send_to_partner').html(response);
                    var option_length = $('#partner_id_send_to_partner').children('option').length;
                    if(option_length == 2){
                        $("#partner_id_send_to_partner").change();   
                    }
                     <?php if(isset($filtered_partner)) { ?> 
                    $('#partner_id_send_to_partner').val('<?php echo $filtered_partner?>'); 
                    $('#partner_id_send_to_partner').trigger('change');
                    <?php } ?>
                }
                
               
            }
        });
    }
    
    function check_awb_exist(){
            var awb = $("#awb_by_wh").val();
            if(awb){
                    $.ajax({
                    type: 'POST',
                    beforeSend: function(){

                        $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });

                        },
                    url: '<?php echo base_url() ?>employee/service_centers/check_wh_shipped_defective_awb_exist',
                    data:{awb:awb},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.code === 247){
                            alert("This AWB already used same price will be added");
                            $("#same_awb").css("display","block");
                            $('body').loadingModal('destroy');
                           
                            $("#defective_parts_shippped_date_by_wh").val(data.message[0].shipment_date);
                            $("#courier_name_by_wh").val(data.message[0].courier_name.toLowerCase()).attr('selected','selected');
                            $("#courier_name_by_wh").select2('destroy').attr("readonly", true);
                            $('#courier_name_by_wh').css('pointer-events','none');
                            $("#courier_price_by_wh").val("0");
                            $("#courier_price_by_wh").css("display","none");
                            if(data.message[0].courier_file){
                               
                                $("#exist_courier_image").val(data.message[0].courier_file);
                                $("#defective_parts_shippped_courier_pic_by_wh").css("display","none");
                            }
                            $('#shipped_spare_parts_boxes_count option[value="' + data.message[0]['box_count'] + '"]').attr("selected", "selected");
                            if (data.message[0]['box_count'] === 0) {
                                $('#shipped_spare_parts_boxes_count').val("");

                            } else {
                                $('#shipped_spare_parts_boxes_count').val(data.message[0]['box_count']).trigger('change');

                            }                            
                            var wt = Number(data.message[0]['billable_weight']);
                            if(wt > 0){
                            var wieght = data.message[0]['billable_weight'].split(".");
                                $("#shipped_spare_parts_weight_in_kg").val(wieght[0]).attr('readonly', "readonly");
                                $("#shipped_spare_parts_weight_in_gram").val(wieght[1]).attr('readonly', "readonly");
                            }

                        } else {

                            $('body').loadingModal('destroy');
                            $("#defective_parts_shippped_courier_pic_by_wh").css("display","block");
                            $("#courier_price_by_wh").css("display","block");
                            $("#same_awb").css("display","none");
                            $("#exist_courier_image").val("");
                            $("#shipped_spare_parts_weight_in_kg").removeAttr("readonly");
                            $("#shipped_spare_parts_weight_in_gram").removeAttr("readonly");
                            $("#courier_name_by_wh").select2();
                            $('#courier_name_by_wh').css('pointer-events', 'auto');
                        }

                    }
                });
            }
            
        }
    
    $("#shipped_spare_parts_weight_in_kg").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        }
    });
    
    $("#shipped_spare_parts_weight_in_gram").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        }
    });
    
    $('#shipped_spare_parts_weight_in_gram,#shipped_spare_parts_weight_in_kg').bind('keydown', function (event) {
        switch (event.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
                break;
            default:
                var regex = new RegExp("^[a-zA-Z0-9,]+$");
                var key = event.key;
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
                break;
        }
    });
</script>

<style>
.modal-dialog {
    width: 100% !important;
    margin: 30px auto;
}
</style>