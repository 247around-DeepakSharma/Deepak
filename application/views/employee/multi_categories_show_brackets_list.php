
<div role="tabpanel" class="tab-pane active" id="requested_brackets_list">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >                       
                            <table id="requested_brackets_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead>                                    
                                    <tr>
                                        <th class="text-center">S.N.</th>
                                        <th class="text-center" data-orderable="false">Order ID</th>
                                        <th class="text-center" data-orderable="false">Received From</th>                                   
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>                                        
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Date</th>                                        
                                        <th class="text-center" data-orderable="false">Action</th>                                        
                                    </tr>
                                </thead>
                                <tbody> 
                                </tbody>                                
                            </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div role="tabpanel" class="tab-pane" id="shipped_brackets_list">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >                       
                            <table id="shipped_brackets_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead>                                    
                                    <tr>
                                        <th class="text-center">S.N.</th>
                                        <th class="text-center" data-orderable="false">Order ID</th>
                                        <th class="text-center" data-orderable="false">Received From</th>                                   
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>                                        
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Date</th>                                        
                                        <th class="text-center" data-orderable="false">Action</th>                                        
                                    </tr>
                                </thead>
                                <tbody> 
                                </tbody>                                
                            </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div role="tabpanel" class="tab-pane " id="received_brackets_list">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >                       
                            <table id="received_brackets_list_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead>                                    
                                    <tr>
                                        <th class="text-center">S.N.</th>
                                        <th class="text-center" data-orderable="false">Order ID</th>
                                        <th class="text-center" data-orderable="false">Received From</th>                                   
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch & Above</th>                                        
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Date</th>                                        
                                        <th class="text-center" data-orderable="false">Action</th>                                        
                                    </tr>
                                </thead>
                                <tbody> 
                                </tbody>                                
                            </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
   

var requested_brackets_list_table;
var shipped_brackets_list_table;
var received_brackets_list_table;

    
    $(document).ready(function() {
        $("#requested_brackets_list_table").bind("DOMSubtreeModified", function() {
            $('#requested_brackets_list_table tr span.requested_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(255, 128, 128)");  });
        });
        $("#shipped_brackets_list_table").bind("DOMSubtreeModified", function() {
            $('#shipped_brackets_list_table tr span.shipped_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(255, 236, 139)");  });
        });
        
        $("#received_brackets_list_table").bind("DOMSubtreeModified", function() {
            $('#received_brackets_list_table tr span.received_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(76, 186, 144)");  });
        });

     requested_brackets_list_table = $('#requested_brackets_list_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 8, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9 ]
                    },
                    title: 'spare_cost_given'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_brackets_tab_details",
                type: "POST",
                data: function(d){
                    d.type= '0';
                    d.is_shipped= '0';
                    d.is_received= '0',
                    d.sf_role= $("#sf_role").val();
                    d.sf_id= $("#sf_id").val();
                    d.daterange= $("#daterange").val();
                }
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#requested_brackets_list_table tr span.requested_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(255, 128, 128)"); });
              $(".dataTables_filter").addClass("pull-right");
            }
        });

        

     shipped_brackets_list_table = $('#shipped_brackets_list_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 8, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9 ]
                    },
                    title: 'spare_cost_given'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_brackets_tab_details",
                type: "POST",
               data: function(d){
                    d.type= '0';
                    d.is_shipped= '1';
                    d.is_received= '0',
                    d.sf_role= $("#sf_role").val();
                    d.sf_id= $("#sf_id").val();
                    d.daterange= $("#daterange").val();
                }
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#shipped_brackets_list_table tr span.shipped_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(255, 236, 139)"); });
              $(".dataTables_filter").addClass("pull-right");
            }
        });

     received_brackets_list_table = $('#received_brackets_list_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 8, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9 ]
                    },
                    title: 'received_brackets_list'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_brackets_tab_details",
                type: "POST",
                data: function(d){
                    d.type= '0';
                    d.is_shipped= '1';
                    d.is_received= '1',
                    d.sf_role= $("#sf_role").val();
                    d.sf_id= $("#sf_id").val();
                    d.daterange= $("#daterange").val();
                }
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#received_brackets_list_table tr span.received_order').each(function(){ $(this).closest('tr').css("background-color", "rgb(76, 186, 144)"); });
              $(".dataTables_filter").addClass("pull-right");
            }
        });
        
       
    });
    
    function applyFilter(){
        received_brackets_list_table.ajax.reload(null, false);
        requested_brackets_list_table.ajax.reload(null, false);
        shipped_brackets_list_table.ajax.reload(null, false);
    }
      
    
</script>
