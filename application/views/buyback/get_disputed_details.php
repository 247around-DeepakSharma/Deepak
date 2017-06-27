<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-bars"></i> Disputed Order Details <!--<small>Float left</small>-->
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="auto-settle-tab" data-toggle="tab" aria-expanded="false">Auto Settle( <span style="font-weight: bold;" id="in_tranist_record">0</span> )</a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content2" id="tat_breech-tab" role="tab" data-toggle="tab" aria-expanded="true">30 Days Tat Breech ( <span style="font-weight: bold;" id="in_delivered_record">0</span> )</a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content3" role="tab" id="unassigned" data-toggle="tab" aria-expanded="false">Vendor Rejected ( <span style="font-weight: bold;" id="in_unassigned_record">0</span> )</a>
                                </li>
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="auto-settle-tab">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var auto_settle;
    
    $(document).ready(function () {
        
        //datatables
        auto_settle = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 4},
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#in_tranist_record").text(response.recordsTotal);
          }
            
        });
        
    });
        
</script>