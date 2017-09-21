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
                            Claim Raised Order
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable1" class="table table-striped table-bordered" >
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Order ID</th>
                                    <th>Services</th>
                                    <th>City</th>
                                    <th>Order Date</th>
                                    <th>Current Status</th>
                                    <th>Internal Status</th>
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
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript">
    var table_data = "";
    var datatable_length_option = [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]];
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        
        //datatables
        table_data = $('#datatable1').DataTable({
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
                   title: 'claim_raised_order_data'+time
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export selected',
                    exportOptions: {
                        modifier: {
                            selected: true
                        }
                    },
                    title: 'claim_raised_order_data'+time
                }
            ],
            select: {
                style: 'multi'
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 20}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6,7],
                    "orderable": false
                }
            ]
            
        });   
    });
        
</script>