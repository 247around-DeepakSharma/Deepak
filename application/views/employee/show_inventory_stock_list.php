<style>
    #inventory_stock_table_filter{
        text-align: right;
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
    }
    
    #modal_data .modal-lg {
        width: 100%!important;
    }
    hr{
        margin-top: 0px;
    }
</style>
<div id="page-wrapper" >
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p style="font-size:32px;">
                    <strong>
                        Service Center Inventory Stock
                    </strong>
                </p>
            </div>
            <div class="col-md-6">
                <div class="pull-right" style="margin-top:10px;">
                    <label class="checkbox-inline"><input type="checkbox" id="is_show_all" value="0" onclick="reload_data()">Show All</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="inventory_stock_list">
                <table id="inventory_stock_table" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>SF Name</th>
                            <th>Total Stocks</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
<script>
    
    var inventory_stock_table;
    
    $(document).ready(function(){
        
        inventory_stock_table = $('#inventory_stock_table').DataTable({
            "processing": true, 
            "serverSide": true,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 50,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'vendor_inventory_stock',
                    exportOptions: {
                       columns: [1, 2],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_inventory_stock_list/",
                "type": "POST",
                "data": function(d){
                    d.is_show_all =  $('#is_show_all').val();
                 }
            },
            "deferRender": true       
        });
        
    });
    
    function get_vendor_stocks(entity_id,entity_type){
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>employee/inventory/get_inventory_stock',
            data:{entity_id:entity_id,entity_type:entity_type},
            success:function(response){
                $("#open_model").html(response);   
                $('#modal_data').modal('toggle');
            }
        });
    }
    
    function reload_data(){
        if($('#is_show_all').is(":checked")){
            $('#is_show_all').val('1');
        }else if($('#is_show_all').is(":not(:checked)")){
            $('#is_show_all').val('0')
        }
        inventory_stock_table.ajax.reload();
    }

</script>