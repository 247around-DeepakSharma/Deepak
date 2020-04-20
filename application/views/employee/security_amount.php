<style>
    #invoice_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #invoice_list_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Security Amount Invoice List</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="service_centre_id">
                            <option value="0" selected>All</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="daterange" name="daterange">
                    </div>
                    <button class="btn btn-success col-md-2" id="get_invoice_data">Submit</button>
                </div>
            </div>
        <hr>
        
        
        <div class="model-table">
            <table class="table table-bordered table-hover table-striped" id="invoice_list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Service Centre</th>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>GST Charges</th>
                        <th>Basic Charges</th>
                        <th>Total Invoice Charges</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    var invoice_table;
    var time = moment().format('D-MMM-YYYY HH:mm:ss');
    $(function () {
        $('#daterange').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")); ?>',
            endDate: '<?php echo date('Y/m/d') ?>',
            minDate: '2015/01/01',
            maxDate: '2030/12/31',
            showDropdowns: true,
            dateLimit: 60
        });
    });
    
    $('#service_centre_id').select2();

    $(document).ready(function(){
        get_service_centre_list();
        //load invoice list when page is ready
        get_invoice_list();
    });
    
    $('#get_invoice_data').on('click',function(){
            //table is initialised, so reload it
            invoice_table.ajax.reload(null, false);    
    });
    
    function get_invoice_list(){
        invoice_table = $('#invoice_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                searchPlaceholder: "Service Center, Invoice ID"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Security_Amount_Invoice_List',
                    exportOptions: {
                       columns: [1,2,3,4,5,6],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/invoice/get_security_amount_data",
                "type": "POST",
                data: function(d){
                    d.service_centre_id = $('#service_centre_id').val();
                    var daterange = $('#daterange').val().split('-');
                    d.from_date = daterange[0];
                    d.to_date = daterange[1];
                }
            },
            "deferRender": true       
        });
    }
    
    //function to get service centre list in dropdown
    function get_service_centre_list()
    {
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/invoice/get_service_centre_list_dropdown',
           data: {}
         })
         .done (function(data) {
             $('#service_centre_id').append(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             alert("Something went wrong while loading service centre list!");
          })
    }
</script>