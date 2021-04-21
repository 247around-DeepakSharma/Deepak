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
    .pull-right {
        padding: 5px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <div>
                        <h3>Inventory Stocks On Warehouse</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-2">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="" selected="">Select Partner</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <select class="form-control" id="stock_is">
                                            <option value="" disabled="" selected="">Select Stock Status</option>
                                            <option value="1">All  Stocks </option>
                                            <option value="0">Available Stocks </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" style="width: 70%;" id="part_number" name="part_number" value="" placeholder="Enter Part Number">
                                    </div>
                                    <button class="btn btn-success col-md-2" id="inventory_stock_on_warehouse">Search</button>
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
                                    <th>Warehouse Name</th>
                                    <th>Appliance</th>
                                    <th> Type</th>
                                    <th> Part Name</th>
                                    <th> Part Number</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                    <th>SF Basic Price</th>
                                    <th>GST Rate</th>
                                    <th> Total Price</th>
                                    <th>Customer Total Price</th>
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

    var inventory_stock_table;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        $('#stock_is').select2({
            placeholder:"Select Stock Status"
        });
        get_warehouse_inventory_stock_details();
        get_partner(); 
    });
    
    $('#inventory_stock_on_warehouse').on('click',function(){
        var stock_is = $('#stock_is').val();
        if(stock_is){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Select Stock Status");
        }

        var partner = $('#partner_id').val();
        if(partner){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Select Partner");
        }

        var part_number = $('#part_number').val();
        if(part_number){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Please fill Part Number");
        }

        
    });
    
    function get_warehouse_inventory_stock_details(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
            "processing": true,
            "serverSide": true,
             "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "dom": 'lBfrtip',
               "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Inventory Stock Pending On Warehouse',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6,7,8,9,10,11,12],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
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
                url: "<?php echo base_url(); ?>employee/inventory/search_inventory_stock_available_warehouse",
                type: "POST",
                data: function(d){
                    var entity_details = get_entity_details();
                    d.stock_is = entity_details.stock_is,
                    d.part_number = entity_details.part_number,        
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'sender_entity_id': $("#partner_id").val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'stock_is':$('#stock_is').val(),
            'part_number':$("#part_number").val(),
        };
        
        return data;
    }
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $("#partner_id").html(response);
                $('#partner_id').select2();
            }
        });
    }
    
</script>
<style>
    .dataTables_length {
    width: 12% !important;
}
</style>