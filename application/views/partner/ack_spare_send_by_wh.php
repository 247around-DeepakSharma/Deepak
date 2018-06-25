<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
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
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Acknowledge Defective Spare Send By Warehouse</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <div class="approved">
                                <div class="btn btn-success ack_all_order" onclick="process_ack_all_order();" >Approve Order</div>
                            </div>
                        </li>
                    </ul>
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
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_spare_list">
                        <table id="inventory_spare_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Booking ID</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
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
    var tmp_arr = {};
    var postData = {};
    var flag = false;

    $(document).ready(function () {
        get_inventory_list();
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
                url: "<?php echo base_url(); ?>employee/inventory/get_defective_spare_send_by_wh_to_partner",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'receiver_entity_id': '<?php echo $this->session->userdata('partner_id')?>',
            'receiver_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>'
        };
        
        return data;
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
    
    function process_ack_all_order(){
        
        $(".check_single_row:checked").each(function (key) {
            tmp_arr[key] = {};
            tmp_arr[key]['inventory_id'] = $(this).attr('data-inventory_id');
            tmp_arr[key]['sender_entity_id'] = $(this).attr('data-sender_entity_id');
            tmp_arr[key]['sender_entity_type'] = $(this).attr('data-sender_entity_type');
            tmp_arr[key]['ledger_id'] = $(this).attr('data-ledger_id');
            tmp_arr[key]['booking_id'] = $(this).attr('data-booking_id');
            tmp_arr[key]['part_name'] = $(this).attr('data-part_name');
            flag = true;
        });
        
        postData['data'] = JSON.stringify(tmp_arr);
        postData['receiver_entity_id'] = '<?php echo $this->session->userdata('partner_id')?>';
        postData['receiver_entity_type'] = '<?php echo _247AROUND_PARTNER_STRING; ?>';
        postData['receiver_entity_name'] = '<?php echo $this->session->userdata('partner_name')?>';
        
        if(flag){
            showConfirmDialougeBox('Are you sure you want to continue ?', 'info');
        }else{
            showConfirmDialougeBox('Please Select At Least One Checkbox', 'warning');
        }
    }
    
    function showConfirmDialougeBox(title,type){
        if(type === 'info'){
            swal({
            title: title,
            type: type,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
            function(){
                 ajax_call();
            });
        }else{
            swal({
                title: title,
                type: type
            });
        }
    }
    
    function ajax_call(){
        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/inventory/process_ack_spare_send_by_wh',
            data:postData,
            success:function(response){
                obj = JSON.parse(response);
                if(obj.status){
                    swal("Thanks!", "Details updated successfully!", "success");
                    $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                    $('#success_msg').html(obj.message);
                    inventory_spare_table.ajax.reload();
                }else{
                    showConfirmDialougeBox(obj.message, 'warning');
                    $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                    $('#error_msg').html(obj.message);
                }
            }
        });
    }

</script>