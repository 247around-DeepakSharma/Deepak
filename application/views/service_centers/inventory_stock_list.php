<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
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
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
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
                                    <th>S.No</th>
                                    <th>Service</th>
                                    <th>Part Name</th>
                                    <th>Part Number</th>
                                    <th>Stock</th>
                                    <th>Serial Number</th>
                                    <th>Model Number</th>
                                    <th>Description</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Type</th>
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

    $(document).ready(function () {
        get_partner();
        get_inventory_list();
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
                url: "<?php echo base_url(); ?>employee/inventory/get_inventory_stocks_details_for_warehouse",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.entity_id = entity_details.entity_id,
                    d.entity_type = entity_details.entity_type
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'entity_id': $('#partner_id').val(),
            'entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>'
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