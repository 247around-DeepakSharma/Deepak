<div role="tabpanel" class="tab-pane active" id="spare_parts_requested">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                       
                            <table id="spare_parts_requested_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                       <th class="text-center" data-orderable="true">Age Of Requested</th>
<!--                                        <th class="text-center" data-orderable="false">Update</th>-->
                                        <th class="text-center" data-orderable="false">Cancel Part</th>
                                        <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
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
<div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Reject Parts</h4>
            </div>
            <div class="modal-body">
                <textarea rows="3" class="form-control" id="textarea" placeholder="Enter Remarks"></textarea>
                <input style="margin-top:20px; display: none" type="number" name="charge" class="form-control" id="charges" placeholder="Enter Courier Charge" />
            </div>
            <input type="hidden" id="url"></input>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="reject_parts()" id="reject_btn">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
         </div>
      </div>
   </div>

<div role="tabpanel" class="tab-pane" id="defective_part_shipped_by_SF_approved">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_shipped_by_SF_approved_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
					<th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        
<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                
                                
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div role="tabpanel" class="tab-pane" id="defective_part_shipped_by_SF">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_shipped_by_sf_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
		<th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Courier Name</th>
                                        <th class="text-center" data-orderable="false">AWB</th>
                                        <th class="text-center" data-orderable="false">Courier Charges</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                        
                                        <th class="text-center" data-orderable="false">Reject Courier</th>
                                        <th class="text-center" data-orderable="false">Approve Courier</th>
                                    </tr>
                                </thead>
                                
                                
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_pending">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_pending_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
                                        <th class="text-center" data-orderable="true">Age</th>

<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->

                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_rejected_by_partner">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_rejected_by_partner_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of shipped</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        
<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Parts Shipped</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="shipped">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="partner_shipped_part" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                    
                                        <th class="text-center" data-orderable="true">Age Of Shipped</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
<!--                                        <th class="text-center" data-orderable="false">Update</th>-->
<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                    </tr>
                                </thead>
                                
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="delivered">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="sf_received_part" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
           
                                        <th class="text-center" data-orderable="true">Age Of Delivered</th>
<!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var spare_parts_requested_table;
    var partner_shipped_part;
    var sf_received_part;
    var defective_part_pending_table;
    var defective_part_rejected_by_partner_table;
    $(document).ready(function() {
 

    //datatables
    spare_parts_requested_table = $('#spare_parts_requested_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 8, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9 ]
                    },
                    title: 'spare_parts_requested'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '0', status: '<?php echo SPARE_PARTS_REQUESTED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });

    
    partner_shipped_part = $('#partner_shipped_part').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 9, "desc" ]],//Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9 ]
                    },
                    title: 'partner_shipped_part'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '1', status: '<?php echo SPARE_SHIPPED_BY_PARTNER; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
        sf_received_part = $('#sf_received_part').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[12, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12]
                    },
                    title: 'sf_received_part'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '2', status: '<?php echo SPARE_DELIVERED_TO_SF; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
        defective_part_pending_table = $('#defective_part_pending_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[12, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12]
                    },
                    title: 'defective_part_pending'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '2', status: '<?php echo DEFECTIVE_PARTS_PENDING; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
        defective_part_rejected_by_partner_table = $('#defective_part_rejected_by_partner_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[9, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10 ]
                    },
                    title: 'defective_part_rejected'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '3', status: '<?php echo DEFECTIVE_PARTS_REJECTED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
        defective_part_shipped_by_sf_table = $('#defective_part_shipped_by_sf_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10 ]
                    },
                    title: 'defective_part_shipped_by_sf'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '4', status: '<?php echo DEFECTIVE_PARTS_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>', approved_defective_parts_by_admin:0}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
        
        defective_part_shipped_by_SF_approved_table = $('#defective_part_shipped_by_SF_approved_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[11, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9]
                    },
                    title: 'defective_part_shipped_by_SF_approved'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '5', status: '<?php echo DEFECTIVE_PARTS_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>', approved_defective_parts_by_admin:1}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
       
    });
</script>
