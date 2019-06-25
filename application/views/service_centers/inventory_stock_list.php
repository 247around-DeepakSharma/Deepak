<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }
    .alternate_spare_list{
        float: right;  
        font-size: 22px;
        color: #131212cc;
        padding: 5px;
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
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="service_center_id">
                                            <option value="">Select Service Center</option>
                                            <?php if(!empty($sf)) { foreach($sf as $sf_data) { ?>
                                            <option value="<?= $sf_data['id']; ?>"><?= $sf_data['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div> 
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="warehouse_id">
                                            <option value="">Select Warehouse</option>
                                            <?php if(!empty($wh)) { foreach($wh as $wh_data) { ?>
                                            <option value="<?= $wh_data['id']; ?>"><?= $wh_data['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div> 
                                    <div class="form-group col-md-2">
                                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">With Out of Stock</label>
                                    </div>
                                    <button class="btn btn-success btn-sm col-md-2" id="get_inventory_data">Submit</button>
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
                                    <th>No</th>
                                    <th>Appliance  <?php //echo $this->session->userdata('userType'); ?></th>
                                    <th> Type</th>
                                    <th>  Part Name</th>
                                    <th>  Part Number</th>
                                    <th>Description</th>
                                    <th>  Stock</th>
                                    <th>  Requested Parts</th>
                                    <th> SF Basic Price</th>
                                    <th>  GST Rate</th>
                                    <th>  Total Price</th>
                                    <th>  Customer  Total</th>
                                    <th>Alternate Parts</th>
                                    
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         
    </div>
</div>
<script>

    $('#service_center_id').select2({
        allowClear: true,
        placeholder :'Select Sevice Center'
    });
    $('#warehouse_id').select2({
        allowClear: true,
        placeholder :'Select Warehouse'
    });
    
    var inventory_stock_table;

    $(document).ready(function () {
        get_partner();
        get_inventory_list();
        
        $('#service_center_id').on('select2:selecting', function(){
            if($('#warehouse_id').val() != '') {
                $('#warehouse_id').val('').change();
            } 
            $('#show_all_inventory').prop("checked", false);
        });

        $('#warehouse_id').on('select2:selecting', function(){
            if($('#service_center_id').val() != '') {
                $('#service_center_id').val('').change();
            } 
            $('#show_all_inventory').prop("checked", false);
        });

        $('#show_all_inventory').on('click', function(){
            if($(this).prop("checked") == true) {
                $('#service_center_id').val('').change();
                $('#warehouse_id').val('').change();
            } 
        });
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_inventory_list(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
            "processing": true,
            "serverSide": true,
               "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
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
            "dom": 'lBfrtip',
            "ordering": false,
              "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>   Export',
                    pageSize: 'LEGAL',
                    title: 'Inventory List',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6,7,8,9],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_inventory_stocks_details",
                type: "POST",
                data: function(d){
                 //   console.log(d); 
                    var entity_details = get_entity_details();
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sf_id = entity_details.sf_id,
                    d.wh_id = entity_details.wh_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.is_show_all = entity_details.is_show_all_checked
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'receiver_entity_id': '<?php echo $this->session->userdata('service_center_id'); ?>',
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'sender_entity_id': $('#partner_id').val(),
            'sf_id': $('#service_center_id').val(),
            'wh_id': $('#warehouse_id').val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'is_show_all_checked':$('#show_all_inventory:checked').val()
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
    
</script>