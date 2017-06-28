<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<div class="right_col" role="main">
<!--        <div class="page-title">
    <div class="title_left">
        <h3>Order Details</h3>
    </div>
    </div>-->
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel" style="height: auto;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title">
                    <h2>
                        <i class="fa fa-bars"></i> Order Details <!--<small>Float left</small>-->
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="intransit-tab" data-toggle="tab" aria-expanded="false">In-Transit( <span style="font-weight: bold;" id="in_tranist_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content2" id="delivered-tab" role="tab" data-toggle="tab" aria-expanded="true">Delivered ( <span style="font-weight: bold;" id="in_delivered_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content3" role="tab" id="unassigned" data-toggle="tab" aria-expanded="false">Un-Assigned Order ( <span style="font-weight: bold;" id="in_unassigned_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content4" role="tab" id="others" data-toggle="tab" aria-expanded="false">Others ( <span style="font-weight: bold;" id="in_others_record">0</span> )</a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="intransit-tab">
                                <table id="datatable1" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order ID</th>
                                            <th>Service Name</th>
                                            <th>City</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Exchange Value</th>
                                            <th>SF Charge</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="delivered-tab">
                                <table id="datatable2" class="table table-striped table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order ID</th>
                                            <th>Service Name</th>
                                            <th>City</th>
                                            <th>Order Date</th>
                                            <th>Delivery date</th>
                                            <th>Status</th>
                                            <th>Exchange Value</th>
                                            <th>SF Charge</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="unassigned">
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
                            <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="others">
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
<script type="text/javascript">
    var in_transit;
    var delivered;
    var unassigned;
    var others;
    
    $(document).ready(function () {
        
        //datatables
        in_transit = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 0},
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#in_tranist_record").text(response.recordsTotal);
          }
            
        });
    
    
    
        //datatables
        delivered = $('#datatable2').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 1}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,7,8], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#in_delivered_record").text(response.recordsTotal);
            }
        });
    
        //datatables
        unassigned = $('#datatable3').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 2}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {
           
                $("#in_unassigned_record").text(response.recordsTotal);
            }
        });
    
    
    //datatables
        others = $('#datatable4').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 3}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {
           
                $("#in_others_record").text(response.recordsTotal);
            }
        });
    
    
    });
    
    
</script>