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
        <h3> RM Wise TAT Report (OOT) </h3>
        <hr>
        <div class="stocks_table">
            
            <table id="inventory_stock_table" class="table table-responsive table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Manager Name </th>
                            <th>Agent Name</th>
                            <th>SF Name</th>
                            <th>Part Count (OOT)</th>
                            <th>Amount (OOT)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
            </table>
             
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
                    title: 'rm_wise_tat_report',
                    exportOptions: {
                       columns: [0,1, 2,3,4,5],
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
                "url": "<?php echo base_url(); ?>employee/inventory/get_show_rm_wise_tat_report/",
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