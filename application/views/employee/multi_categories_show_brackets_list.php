<div role="tabpanel" class="tab-pane active" id="requested_brackets_list">
    <div class="row">
        <div class="filter_brackets">
            <div class="filter_box">
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_role_1" name="sf_role">
                            <option selected disabled>Select Role</option>
                            <option value="order_received_from">Order Received From</option>
                            <option value="order_given_to">Order Given To</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_id_1" name="sf_id" required="">
                            <option selected="" disabled="">Select Service Center</option>
                        </select>
                        
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control valid" id="daterange_1" placeholder="Select Date" name="daterange">
                    </div>
                    <div class="col-sm-3">
                        <div class="btn btn-success" id="filter" onclick="applyFilter('1')">Filter</div>
                    </div>
                <span id="sf_err_1" style="padding-left:313px;"></span>
            </div>
        </div>
    </div>
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
                                        <th class="text-center" data-orderable="false">32 Inch and  Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>                                        
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
    <div class="row">
        <div class="filter_brackets">
            <div class="filter_box">
                    <div class="col-md-3">
                        <select class="form-control" id="sf_role_2" name="sf_role">
                            <option selected disabled>Select Role</option>
                            <option value="order_received_from">Order Received From</option>
                            <option value="order_given_to">Order Given To</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_id_2" name="sf_id" style="width:275px !important" required="" >
                            <option selected="" disabled="">Select Service Center</option>
                        </select>
                        
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control valid" id="daterange_2" placeholder="Select Date" name="daterange">
                    </div>
                    <div class="col-sm-3">
                        <div class="btn btn-success" id="filter" onclick="applyFilter('2')">Filter</div>
                    </div>
                <span id="sf_err_2" style="padding-left:313px;"></span>
            </div>
        </div>
    </div>
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
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>                                        
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
    <div class="row">
        <div class="filter_brackets">
            <div class="filter_box">
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_role_3" name="sf_role">
                            <option selected disabled>Select Role</option>
                            <option value="order_received_from">Order Received From</option>
                            <option value="order_given_to">Order Given To</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_id_3" name="sf_id" style="width:275px !important" required="">
                            <option selected="" disabled="">Select Service Center</option>
                        </select>
                        
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control valid" id="daterange_3" placeholder="Select Date" name="daterange">
                    </div>
                    <div class="col-sm-3">
                        <div class="btn btn-success" id="filter" onclick="applyFilter('3')">Filter</div>
                    </div>
                <span id="sf_err_3" style="padding-left:313px;"></span>
            </div>
        </div>
    </div>
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
                                        <th class="text-center" data-orderable="false">32 Inch and  Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>
                                        <th class="text-center" data-orderable="false">Total</th>
                                        <th class="text-center" data-orderable="false">Less Than 32 Inch</th>
                                        <th class="text-center" data-orderable="false">32 Inch and Above</th>                                        
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
$(function() {

      $('input[name="daterange"]').daterangepicker({
          autoUpdateInput: false,
          locale: {
              cancelLabel: 'Clear'
          }
      });

      $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      });

      $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });

    });
</script>
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
            lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
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
                    d.sf_role= $("#sf_role_1").val();
                    d.sf_id= $("#sf_id_1").val();
                    d.daterange= $("#daterange_1").val();
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
            lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
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
                    d.sf_role= $("#sf_role_2").val();
                    d.sf_id= $("#sf_id_2").val();
                    d.daterange= $("#daterange_2").val();
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
            lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
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
                    d.sf_role= $("#sf_role_3").val();
                    d.sf_id= $("#sf_id_3").val();
                    d.daterange= $("#daterange_3").val();
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
    
    function applyFilter(tab_id){    
        if(tab_id==1){            
            var sf_id = $("#sf_id_"+tab_id).val();
            var daterange = $("#daterange_"+tab_id).val(); 
            var sf_role = $("#sf_role_"+tab_id).val();
            if((sf_role==null) || (daterange=='' && sf_id==null)){
                $("#sf_err_"+tab_id).html("Role,Service Center and Date should not be blank.").css('color','red');
        }else{
            $("#sf_err_"+tab_id).html("");
             requested_brackets_list_table.ajax.reload(null, false); 
                  
         }
        
        }
        
        if(tab_id==2){            
            var sf_id = $("#sf_id_"+tab_id).val();            
            var daterange = $("#daterange_"+tab_id).val();        
            var sf_role = $("#sf_role_"+tab_id).val();            
            if((sf_role==null) || (daterange=='' && sf_id==null)){
                $("#sf_err_"+tab_id).html("Role,Service Center and Date should not be blank.").css('color','red');
        }else{
            $("#sf_err_"+tab_id).html("");
            shipped_brackets_list_table.ajax.reload(null, false);       
         }
        
        }
        
        if(tab_id==3){            
            var sf_id = $("#sf_id_"+tab_id).val();            
            var daterange = $("#daterange_"+tab_id).val();        
            var sf_role = $("#sf_role_"+tab_id).val();            
            if((sf_role==null) || (daterange=='' && sf_id==null)){
                $("#sf_err_"+tab_id).html("Role,Service Center and Date should not be blank.").css('color','red');
        }else{
            $("#sf_err_"+tab_id).html("");
              received_brackets_list_table.ajax.reload(null, false);       
         }
        
        }
        
//        var sf_id = $("#sf_id").val();
//        var daterange = $("#daterange").val(); 
//        var sf_role = $("#sf_role").val();
//        if((sf_role==null) || (daterange=='' && sf_id==null)){
//            $("#sf_err_"+tab_id).html("Role,Service Center and Date should not be blank.").css('color','red');
//        }else{
//            $("#sf_err").html("");
//            received_brackets_list_table.ajax.reload(null, false);            
//            requested_brackets_list_table.ajax.reload(null, false);
//            shipped_brackets_list_table.ajax.reload(null, false);
//        }
//        
    }
      
    
</script>
