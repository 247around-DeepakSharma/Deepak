<script src="<?php echo base_url(); ?>js/base_url.js"></script>
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
                                <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="vendor_rejected" data-toggle="tab" aria-expanded="false">Vendor Rejected( <span style="font-weight: bold;" id="vendor_rejected_record">0</span> )</a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content2" id="claim_submitted" role="tab" data-toggle="tab" aria-expanded="true">Claim Submitted ( <span style="font-weight: bold;" id="claimed_record">0</span> )</a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content3" role="tab" id="cliam_settle" data-toggle="tab" aria-expanded="false">Claim Settled ( <span style="font-weight: bold;" id="claim_settle_record">0</span> )</a>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="cliam_settle"  >
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
<script type="text/javascript">
    var vendor_rejected;
    var claim_submitted;
    var claim_settled;
    $(document).ready(function () {
        
        //datatables
        vendor_rejected = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 11}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#vendor_rejected_record").text(response.recordsTotal);
          }
            
        });
        
         //datatables
//        claim_submitted = $('#datatable2').DataTable({
//            "processing": true, //Feature control the processing indicator.
//            "serverSide": true, //Feature control DataTables' server-side processing mode.
//            "order": [], //Initial no order.
//            "pageLength": 50,
//            // Load data for the table's content from an Ajax source
//            "ajax": {
//                "url": "<?php //echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
//                "type": "POST",
//                "data": {"status": 8},
//                
//            },
//            
//            //Set column definition initialisation properties.
//            "columnDefs": [
//                {
//                    "targets": [0,1], //first column / numbering column
//                    "orderable": false, //set not orderable
//                },
//            ],
//           "fnInitComplete": function (oSettings, response) {
//            
//            $("#claimed_record").text(response.recordsTotal);
//          }
//            
//        });
//        
//        claim_settled = $('#datatable3').DataTable({
//            "processing": true, //Feature control the processing indicator.
//            "serverSide": true, //Feature control DataTables' server-side processing mode.
//            "order": [], //Initial no order.
//            "pageLength": 50,
//            // Load data for the table's content from an Ajax source
//            "ajax": {
//                "url": "<?php //echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
//                "type": "POST",
//                "data": {"status": 9},
//                
//            },
//            
//            //Set column definition initialisation properties.
//            "columnDefs": [
//                {
//                    "targets": [0,1], //first column / numbering column
//                    "orderable": false, //set not orderable
//                },
//            ],
//           "fnInitComplete": function (oSettings, response) {
//            
//            $("#claim_settle_record").text(response.recordsTotal);
//          }
//            
//        });
//        
    });
        
</script>