<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }
    #inventory_spare_table_filter{
        text-align: right;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Inventory Send By Partner To Warehouse</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
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
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                                <div class="approved pull-right">
                                    <div class="btn btn-info acknowledge_all_spare" onclick="process_acknowledge_spare();" id="ack_spare">Acknowledge spare received</div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_spare_list">
                        <table id="inventory_spare_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>No</th>
				    <th>Booking ID</th>
                                    <th>Appliance</th>
                                    <th>Invoice ID</th>
                                    <th>Spare Type</th>
                                    <th>Spare Part Name</th>
                                    <th>Spare Part Number</th>
                                    <th>Spare Quantity</th>
                                    <th>Courier Name</th>
                                    <th>Courier AWB Number</th>
                                    <th>
                                        Acknowledge
                                        <input type="checkbox" id="ack_all">
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal start-->
        <div id="modal_data" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="open_model"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal end -->
    </div>
</div>
<script>

    var inventory_spare_table;

    $(document).ready(function () {
        $('#wh_id').select2({
            placeholder:"Select Warehouse"
        });
        get_partner();
        get_vendor();
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        var wh_id = $('#wh_id').val();
        if(partner_id && wh_id){
            inventory_spare_table.ajax.reload();
        }else{
            alert("Please Select All Field");
        }
    });
    
    function get_inventory_list(){
        inventory_spare_table = $('#inventory_spare_table').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            "order": [],
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_spare_send_by_partner_to_wh",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'sender_entity_id': $('#partner_id').val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'receiver_entity_id': $('#wh_id').val(),
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>'
        };
        
        return data;
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    }
    
    function get_vendor() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{'is_wh' : 1},
            success: function (response) {
                console.log('response');
                $('#wh_id').html(response);
            }
        });
    }
    
    $('#ack_all').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });
    
    function process_acknowledge_spare(){
        var tmp_arr = {};
        var postData = {};
        var flag = false;
        $(".check_single_row:checked").each(function (key) {
            tmp_arr[key] = {};
            tmp_arr[key]['inventory_id'] = $(this).attr('data-inventory_id');
            tmp_arr[key]['quantity'] = $(this).attr('data-quantity');
            tmp_arr[key]['ledger_id'] = $(this).attr('data-ledger_id');
            tmp_arr[key]['part_name'] = $(this).attr('data-part_name');
            tmp_arr[key]['part_number'] = $(this).attr('data-part_number');
            tmp_arr[key]['booking_id'] = $(this).attr('data-booking_id');
            tmp_arr[key]['invoice_id'] = $(this).attr('data-invoice_id');
            flag = true;
        });
        
        postData['data'] = JSON.stringify(tmp_arr);
        postData['sender_entity_id'] =  $('#partner_id').val();
        postData['sender_entity_type'] = '<?php echo _247AROUND_PARTNER_STRING; ?>';
        postData['receiver_entity_id'] = $('#wh_id').val();;
        postData['receiver_entity_type'] = '<?php echo _247AROUND_SF_STRING; ?>';
        postData['sender_entity_name'] = $('#partner_id option:selected').text();
        postData['receiver_entity_name'] = $('#wh_id option:selected').text();
        
        if(flag){
            $('#ack_spare').html("<i class='fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/process_acknowledge_spare_send_by_partner_to_wh',
                data:postData,
                success:function(response){
                    $('#ack_spare').html("Acknowledge spare received").attr('disabled',false);
                    obj = JSON.parse(response);
                    if(obj.status){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(obj.message);
                        inventory_spare_table.ajax.reload();
                    }else{
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(obj.message);
                    }
                }
            });
        }else{
            alert("Please Select At Least One Checkbox");
        }
    }

</script>
