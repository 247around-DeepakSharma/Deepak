<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid">
    <input type="hidden" value="" name="receiver_partner_id" id="receiver_partner_id">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
          <h2>Delivery On Challan Rejected By Partner</h2>
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
                                        <select class="form-control" id="partner_id2" name="partner_id" required="">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                        <div id="partner_err"></div>
                                </div>                              
                                <button type="submit" class="btn btn-success btn-sm col-md-2" id="partner_search_id2" style="margin-top: 22px;">Submit</button>                          
                                
                                   </form>                                
                            </div>
                            <div class="approved pull-right hide">
                                <div class="btn btn-info btn-sm send_all_spare pull-right" id="send_spare_to_partner" style="margin-top: 11px;" onclick="process_send_all_spare_on_challan();">Send spare to partner</div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="clearfix"></div>
                <hr>
                
                <?php if(!empty($spare_parts)) { ?>
                <div class="table-responsive">
                    <form target="_blank"  action="<?php echo base_url(); ?>employee/service_centers/process_partner_challan_file" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                    <table class="table table-bordered table-hover table-striped" id="defective_parts_reject_by_artner_on_challan">
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
                            <th class="text-center" colspan="2">Action</th>
<!--                             <th class="text-center">Download Challan<input type="checkbox" id="selectall_challan_file"></th>
                            <th class="text-center">
                                Send To Partner
                                <input type="checkbox" id="send_all">
                            </th> -->
                            <!--<th>Action</th>-->
                           </tr>
                        </thead>
                        <tbody>
                            <?php foreach($spare_parts as $key =>$row){?>
                            <tr style="text-align: center;" id="<?php echo 'spare_'.$row['id']; ?>">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
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
                                        <?php echo $row['part_number']; ?>
                                    </td>
                                    
                                     <td>
                                        <?php echo $row['model_number_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['defective_part_shipped_date'])){  echo date("d-M-Y",strtotime($row['defective_part_shipped_date'])); }  ?>
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
                                        <a href="javascript:void(0);" class="btn btn-primary justified btn-sm" data-spare_id="<?php echo $row['id']; ?>">Justified</a>
                                     </td>
<!--                                      <td>
                                             <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan()" name="download_challan[<?php //echo $row['defective_return_to_entity_id'];  ?>][]"  value="<?php //echo $row['id']?>" />
                                    </td>
 
                                    <td>
                                        
                                        <input type="checkbox" class="check_single_row" data-is_micro_wh ="<?php //echo $row['is_micro_wh'];?>" data-defective_return_to_entity_type ="<?php //echo $row['defective_return_to_entity_type']; ?>" data-defective_return_to_entity_id="<?php //echo $row['defective_return_to_entity_id'];?>" data-entity_type ="<?php //echo $row['entity_type']; ?>" data-service_center_id ="<?php //echo $row['service_center_id']; ?>" data-part_name ="<?php //echo $row['defective_part_shipped']; ?>" data-model="<?php //echo $row['model_number_shipped']; ?>" data-shipped_inventory_id = "<?php //echo $row['shipped_inventory_id']?>" data-booking_id ="<?php //echo $row['booking_id']?>" data-partner_id = "<?php //echo $row['partner_id']?>" data-spare_id = "<?php //echo $row['id']?>" data-booking_partner_id = "<?php //echo $row['booking_partner_id']?>">
                                    </td> -->
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-success resend btn-sm" data-spare_id="<?php echo $row['id']; ?>">Resend</a>
                                    </td>
                            </tr>
                            
                            <?php $sn_no++; } ?>
                        </tbody>
                        </table>

    </div>
  </div> 



                    </form>
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
    
   </div>

<script>
    
    $('.resend').on('click', function(){
        if(confirm('Are you sure you want to resend this part?')) {
            
            var spare_id = $(this).attr('data-spare_id');
            $.ajax({
                url : '<?php echo base_url(); ?>employee/inventory/resend_delivery_on_challan',
                method : "post",
                data:{spare_id}
            }).fail(function(data){
                alert(data);
            }).success(function(data){
               alert('Part has been resend successfully.'); 
               $('#spare_'+spare_id).hide();
            });
        } 
    });

    $('.justified').on('click', function(){
        if(confirm('Are you sure you want to proceed?')) {
            
            var spare_id = $(this).attr('data-spare_id');
            $.ajax({
                url : '<?php echo base_url(); ?>employee/inventory/justify_delivery_on_challan',
                method : "post",
                data:{spare_id}
            }).fail(function(data){
                alert(data);
            }).success(function(data){
               alert('Part has been accepted successfully.'); 
               $('#spare_'+spare_id).hide();
            });
        } 
    });

    $('#defective_parts_reject_by_artner_on_challan').DataTable({
        "pageLength": 100,
            dom: 'Bfrtip',
            // Configure the drop down options.
            "language": {                
                "searchPlaceholder": "Search by Any Column",
            },
            lengthMenu: [
                [ 25, 50,100, -1 ],
                [ '25', '50', '100', 'All' ]
            ],
            // Add to buttons the pageLength option.
            buttons: [
                'pageLength','excel',
            ],
    });
    
     $("#partner_search_id2").click(function(){         
         var partner_id = $("#partner_id2").val();
       
         if(partner_id==null){
            $("#partner_err").html('Please Select Partner.').css({'color':'red'});
            return false;
         }else{
             $("#partner_err").html('');
             load_view_send_to_partner('service_center/rejected_by_partner_on_challan', '#tabs-9',partner_id);
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
</script>
<script>
    
    $('#partner_id2').select2({
        placeholder:'Select Partner',
        allowClear:true
    });
    $('#courier_name_by_wh_id').select2({
        placeholder:'Select Courier Name',
        allowClear:true
    });
    
    $('document').ready(function(){
        get_partner_ack();
    });
    
    var postData = {};
    $("#defective_parts_shippped_date_id").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
//    $("#defective_parts_ewaybill_date_by_wh").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
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
    
   
    function get_partner_ack(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/service_centers/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                if(response === 'Error'){
                    
                } else {
                    $('#partner_id2').html(response);
                    var option_length = $('#partner_id2').children('option').length;
                    if(option_length == 2){
                        $("#partner_id2").change();   
                    }
                     <?php if(isset($filtered_partner)) { ?> 
                    $('#partner_id2').val('<?php echo $filtered_partner?>'); 
                    $('#partner_id2').trigger('change');
                    <?php } ?>
                }
                
               
            }
        });
    }
    
    function check_awb_exist_details(){
            var awb = $("#awb_by_wh_id").val();
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
                    url: '<?php echo base_url() ?>employee/service_centers/check_warehouse_shipped_awb_exist',
                    data:{awb:awb},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.code === 247){
                            alert("This AWB already used same price will be added");
                            $("#same_awb").css("display","block");
                            $('body').loadingModal('destroy');
                            $("#defective_parts_shippped_date_id").val(data.message[0].shipped_date);
                            $("#courier_name_by_wh_id").val(data.message[0].courier_name_by_partner).trigger('change');
                            $("#courier_price_id").val("0");
                            $("#courier_price_id").css("display","none");
                            if(data.message[0].courier_invoice_file){
                                $("#exist_courier_image").val(data.message[0].courier_invoice_file);
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
                            $("#courier_price_id").css("display","block");
                            $("#same_awb").css("display","none");
                            $("#exist_courier_image").val("");
                            $("#shipped_spare_parts_weight_in_kg").removeAttr("readonly");
                            $("#shipped_spare_parts_weight_in_gram").removeAttr("readonly");
                        }

                    }
                });
            }
            
        }
        
    function remove_select_all_challan(){
        $('#selectall_challan_file').prop('checked', false); 
        $('#send_all').prop('checked', false); 
        var d_m = $('.check_single_row:checked');
        if (d_m.length > 0) {
            $('.check_single_row').prop('checked', false);
        }
    }
    
    
    function check_checkbox(){

        var flag =0;

           if(flag === 0){
               var d_m = $('.checkbox_challan:checked');
               if(d_m.length > 0){
                   flag = 1;  
               }
           }





        if(flag ===0 ){
            alert("Please Select Atleast One Checkbox To Download Challan");
            return false;
        }else{

           var wh =  $("#warehouse_select").val();
            if (wh=="") {
                alert("Please select Partner");
            }else{

            $('#myModal22').modal('show');
            var partner_id= $("#partner_id").val();
            $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/service_centers/get_warehouse_partner_list',
            data:{partner:partner_id},
            success:function(response){
            
            console.log(response); 
            $("#warehouse_select").select2();
            $("#warehouse_select").html(response).change();      
               
            }


        });
        }
        }
    }
    
    $(".check_single_row").click(function(){
        $('#selectall_challan_file').prop('checked', false); 
        $('#send_all').prop('checked', false); 
        var d_m = $('.checkbox_challan:checked');
        if (d_m.length > 0) {
            $('.checkbox_challan').prop('checked', false);
        }
        
    });
    
    $("#send_all").click(function(){
        $('#selectall_challan_file').prop('checked', false); 
        var d_m = $('.checkbox_challan:checked');
        if (d_m.length > 0) {
            $('.checkbox_challan').prop('checked', false);
        }
        
    });
    
    
    $("#selectall_challan_file").click(function(){
        $('#send_all').prop('checked', false); 
        var d_m = $('.check_single_row:checked');
        if (d_m.length > 0) {
            $('.check_single_row').prop('checked', false);
        }
        
    });
        
    $('#selectall_challan_file').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".checkbox_challan").prop('checked', true);
        }
        else
        {
            $(".checkbox_challan").prop('checked', false);
        }
    });
    
     $("#shipped_spare_parts_weight_in_kg").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }
        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 1) {
                $(this).val('');
                return false;
            }
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
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

