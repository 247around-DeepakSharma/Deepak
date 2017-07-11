<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="<?php echo base_url() ?>js/report.js"></script>
<style type="text/css">
    div.pager {
        text-align: center;
        margin: 1em 0;
    }
    div.pager span {
        display: inline-block;
        width: 1.8em;
        height: 1.8em;
        line-height: 1.8;
        text-align: center;
        cursor: pointer;
        background: #bce8f1;
        color: #fff;
        margin-right: 0.5em;
    }
    div.pager span.active {
        background: #c00;
    }
    table,th,td { border:1px solid black;}
    th:hover{
        cursor:pointer;
        background:#AAA;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="payment_history_report" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Sale/Purchase Invoice Summary Report</strong></h3>
            <hr>
            <section class="fetch_payment_history" style="padding-left:20px;">
                <div class="row">
                    <div class="row">
                        <div class="form-group" style="margin-left: 10px;">
                            <label class="radio-inline"><input type="radio" name="optradio" value="1" checked="">Without Challan Id</label>
                            <label class="radio-inline"><input type="radio" name="optradio" value="2">All</label>
                        </div>
                        <div class="form-inline" style="margin-left: 20px;">
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="type">Type:</label>
                                <select class="form-control" id="type">
                                    <option value="sales">Sales</option>
                                    <option value="purchase">Purchase</option>
                                    <option value="tds">TDS</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="daterange">Select Invoice Period</label>
                                <input type="text" class="form-control" id="daterange" name="daterange">
                            </div>
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="partner_service_center">Select Partner/Service Center</label>
                                <select class="form-control" id="partner_vendor">
                                    <option value="partner">Partner</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="stand">Stand</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="report_type">Report Type</label>
                                <select class="form-control" id="report_type">
                                    <option value="draft">Draft</option>
                                    <option value="final">Final</option>
                                </select>
                            </div>
                            <button class="btn btn-success" id="get_payment_history">Fetch</button>
                        </div>
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <div class="permission-error text-danger text-center" style="display:none;"><strong>Operation not permitted</strong> </div>
            <div class="data-error text-danger text-center" style="display:none;"><strong>No Data Found</strong> </div>
            <section class="payment_preview">

            </section>
            <hr>
            <section class="download_report" style="display: none;">
                <div class="row">
                    <div class="text-center">
                        <button class="btn btn-success" id="download_report">Download Report</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div> 
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('input[name="daterange"]').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
            endDate: '<?php echo date("Y/m/01") ?>',
            minDate: '2015/01/01',
            maxDate: '2030/12/31',
            showDropdowns: true,
            dateLimit: 60
        });
    });

    $(document).ready(function () {
        $('#get_payment_history').click(function () {
            var radio_box_val = $("input[type='radio']:checked").val();
            //console.log(radio_box_val);
            var type = $('#type').val();
            var partner_vendor = $('#partner_vendor').val();
            var report_type = $('#report_type').val();
            var daterange = $('#daterange').val().split('-');
            var from_date = daterange[0];
            var to_date = daterange[1]; 
            if ((type === 'tds' && partner_vendor === 'partner') || (type === 'tds' && partner_vendor === 'stand')) {
                $('.permission-error').show();
                $('.download_report').hide();
                $('.payment_preview').hide();
                $('.data-error').hide();
            } else {
                $('#loader').show();
                $.ajax({
                    method: 'POST',
                    url: '<?php echo base_url(); ?>employee/accounting/show_accounting_report',
                    data: {type: type, from_date: from_date, to_date: to_date, partner_vendor: partner_vendor,report_type:report_type,is_challan_data:radio_box_val},
                    success: function (response) {
                        //console.log(response);
                        if (response === "error") {
                            $('#loader').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').hide();
                            $('.download_report').hide();
                            $('.data-error').show();
                        } else {
                            $('#loader').hide();
                            $('.data-error').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').show();
                            $('.payment_preview').html(response);
                            $('.download_report').show();
                            table_pagination();
                        }


                    }
                });
            }
        });
    });

    $('#download_report').click(function (e) {
        var type = $('#type').val();
        var partner_vendor = $('#partner_vendor').val();
        var report_type = $('#report_type').val();
        var time = moment().format('D-MMM-YYYY');
        if (type === 'sales' && partner_vendor === 'partner') {
            filename = 'partner_sales_report_' + time;
        } else if (type === 'sales' && partner_vendor === 'vendor') {
            filename = 'vendor_sales_report_' + time;
        } else if (type === 'sales' && partner_vendor === 'stand') {
            filename = 'stand_sales_report_' + time;
        } else if (type === 'purchase' && partner_vendor === 'partner') {
            filename = 'partner_purchase_report_' + time;
        } else if (type === 'purchase' && partner_vendor === 'vendor') {
            filename = 'vendor_purchase_report_' + time;
        } else if (type === 'purchase' && partner_vendor === 'stand') {
            filename = 'stand_purchase_report_' + time;
        } else if (type === 'tds' && partner_vendor === 'partner') {
            filename = 'partner_tds_report_' + time;
        } else if (type === 'tds' && partner_vendor === 'vendor' && report_type === 'draft') {
            filename = 'vendor_tds_draft_report_' + time;
        } else if (type === 'tds' && partner_vendor === 'vendor' && report_type === 'final') {
            filename = 'vendor_tds_final_report_' + time;
        } else if (type === 'tds' && partner_vendor === 'stand') {
            filename = 'stand_tds_report_' + time;
        }

        e.preventDefault();

        //getting data from table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('payment_history_table');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');

        var a = document.createElement('a');
        a.href = data_type + ', ' + table_html;
        a.download = filename + '.xls';
        a.click();
    });

</script>