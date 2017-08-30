<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<style>
    .dataTables_info {
        width: 100%;
        float: left;
    }
    a.dt-button {
        position: relative;
        display: inline-block;
        box-sizing: border-box;
        margin-right: 0.333em;
        padding: 0.5em 1em;
        font-size: inherit;
        border: 1px solid #2e6da4;
        border-radius: 2px;
        cursor: pointer;
        color: #f9f9f9;
        white-space: nowrap;
        overflow: hidden;
        background-color: #337ab7;
        background-image: none;
    }
    a.dt-button:hover:not(.disabled),a.dt-button.active:not(.disabled) {
        border: 1px solid #2e6da4;
        background-color: #143958!important;
        background-image: none!important;
    }
    div.dt-button-background{
        position: inherit;
    }
    .x_content .badge {
        font-size: 10px;
        font-weight: 400;
        line-height: 13px;
        padding: 2px 6px;
        position: absolute;
        right: -5px;
        top: -8px;
        background: #2a3f54!important;
        border-color: #172d44!important;
    }
</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-bars"></i> Vendor Rejected <!--<small>Float left</small>-->
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="vendor_rejected" data-toggle="tab" aria-expanded="false">Vendor Rejected<span style="font-weight: bold;" class="badge bg-green" id="vendor_rejected_record">0</span></a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content2" id="claim_submitted" role="tab" data-toggle="tab" aria-expanded="false">Claim Submitted<span style="font-weight: bold;" class="badge bg-green" id="claimed_record">0</span></a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content3" role="tab" id="claim_approved" data-toggle="tab" aria-expanded="false">Claim Approved<span style="font-weight: bold;" class="badge bg-green" id="claim_approved_record">0</span></a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content4" role="tab" id="claim_reject" data-toggle="tab" aria-expanded="false">Claim Rejected<span style="font-weight: bold;" class="badge bg-green" id="claim_reject_record">0</span></a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content5" role="tab" id="claim_debit_note_raised" data-toggle="tab" aria-expanded="false">Claim Waiting<span style="font-weight: bold;" class="badge bg-green"  id="claim_debit_note_raised_record">0</span></a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content6" role="tab" id="cliam_settle" data-toggle="tab" aria-expanded="false">Claim Settled<span style="font-weight: bold;" class="badge bg-green" id="claim_settle_record">0</span></a>
                                </li>
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="vendor_rejected">
                                    <table id="datatable1" class="table table-striped table-bordered" >
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                
                                <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="claim_submitted"  >
                                    <table id="datatable2" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="claim_approved"  >
                                    <table id="datatable3" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="claim_reject"  >
                                    <table id="datatable4" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_content5" aria-labelledby="claim_debit_note_raised"  >
                                    <table id="datatable5" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_content6" aria-labelledby="cliam_settle"  >
                                    <table id="datatable6" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Order ID</th>
                                                <th>Services</th>
                                                <th>City</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Exchange Value</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript">
    var vendor_rejected;
    var claim_submitted;
    var claim_approved;
    var claim_rejected;
    var claim_settled;
    var claim_debit_note_raised;
    var datatable_length_option = [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]];
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        
        //datatables
        vendor_rejected = $('#datatable1').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Rejected_All_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Rejected_Selected_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 11}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#vendor_rejected_record").text(response.recordsTotal);
          }
            
        });
        
         //datatables
        claim_submitted = $('#datatable2').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Rejected_All_Claim_Submitted_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Rejected_Selected__Claim_Submitted_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 12}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#claimed_record").text(response.recordsTotal);
          }
            
        });
        
        claim_approved = $('#datatable3').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Rejected_All_Claim_Approved_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Rejected_Selected__Claim_Approved_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 13},
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#claim_approved_record").text(response.recordsTotal);
          }
            
        });
        
        claim_rejected = $('#datatable4').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Claim_Rejected_All_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Claim_Rejected_Selected_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 14}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#claim_reject_record").text(response.recordsTotal);
          }
            
        });
        
        claim_debit_note_raised = $('#datatable5').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Rejected_All_Claim_Debit_Note_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Rejected_Selected_Claim_Debit_Note_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 19}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#claim_debit_note_raised_record").text(response.recordsTotal);
          }
            
        });
        
        claim_settled = $('#datatable6').DataTable({
            "pageLength": 50,
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            dom: 'Bfrtip',
            lengthMenu: datatable_length_option,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export All',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)'
                    },
                   title: 'Vendor_Rejected_All_Claim_Settled_Data_'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'Vendor_Rejected_Selected_Claim_Settled_Data_'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 15}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#claim_settle_record").text(response.recordsTotal);
          }
            
        });
        
    });
        
</script>