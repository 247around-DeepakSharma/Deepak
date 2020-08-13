<style>
    .dataTables_filter{
        float: right;
    }
    .pagination{
        float: right;
    }
</style>
<br><br>
<div id="page-wrapper" >
    <div>
        <h3> Defective In Transit TAT Report (OOT) </h3>
        <hr>
        <div class="stocks_table">
            
            <table id="inventory_stock_table" class="table table-responsive table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Partner Name</th>
                            <th>Part Count </th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
            </table>
             
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
                    title: 'In_transit_defective_oot',
                    exportOptions: {
                       columns: [0,1, 2,3],
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
                "url": "<?php echo base_url(); ?>employee/inventory/get_show_in_def_transit_tat_report_data/",
                "type": "POST",
                "data": function(d){
                    d.is_show_all =  $('#is_show_all').val();
                 }
            },
            "deferRender": true       
        });
        
    });
    
 

</script>
    
    
</div>