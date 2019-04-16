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
    
    #inventory_stock_table_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
    }
    .dataTables_length{
     width: 15%;   
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Inventory List</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-4">
                                        <select class="form-control" id="wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">Show All</label>
                                    </div>
                                    <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="inventory_stock_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Spare Type</th>
                                    <th>Spare Part Name</th>
                                    <th>Spare Part Number</th>
                                    <th>Spare Stock</th>
                                    <th>Spare Basic Price</th>
                                    <th>Spare GST Rate</th>
                                    <th>Spare Total Price</th>

                                     <th>Customer Total Price</th>
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

    var inventory_stock_table;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        $('#wh_id').select2({
            placeholder:"Select Warehouse"
        });
        get_vendor();
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var wh_id = $('#wh_id').val();
        if(wh_id){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Please Select Warehouse");
        }
    });
    
    function get_inventory_list(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7,8 ]
                    },
                    title: 'inventory_stock_table_'+time                    
                },
            ],
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
                url: "<?php echo base_url(); ?>employee/inventory/get_inventory_stocks_details",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    
                    d.is_show_all = entity_details.is_show_all_checked
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'receiver_entity_id': $('#wh_id').val(),
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'sender_entity_id': '<?php echo $this->session->userdata('partner_id')?>',
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'is_show_all_checked':$('#show_all_inventory:checked').val()
        };
        
        return data;
    }
    
    function get_vendor() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
            data:{partner_id:<?php echo $this->session->userdata('partner_id'); ?>},
            success: function (response) {
                $('#wh_id').html(response);
            }
        });
    }

</script>