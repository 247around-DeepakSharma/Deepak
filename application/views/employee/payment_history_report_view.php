<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.js'></script>
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
            <h3>
                <strong>Sale/Purchase Invoice Summary Report</strong>
                <button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#buyback_summary_report_model">Download Buyback Summary</button>
            </h3>
            <hr>
            <section class="fetch_payment_history" style="padding-left:20px;">
                <div class="row">
                    <div class="row">
                        <div class="form-group" style="margin-left: 31px;">
                            <label class="radio-inline"><input type="radio" name="challan_optradio" value="2" checked="">All</label>
                            <label class="radio-inline"><input type="radio" name="challan_optradio" value="1">Challans Not Tagged</label>
                        </div>
                        <div class="col-md-12" >
                            <div class="form-horizontal" style="margin-left: 20px;">
                                <div class="form-group">
                                    <label class="col-sm-2" for="type">Type:</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" id="type">
                                            <option value="A">Sales</option>
                                            <option value="B">Purchase</option>
                                            <option value="tds">TDS</option>
                                            <option value="buyback">Buyback</option>
                                            <option value="paytm">Paytm</option>
                                            <option value="advance_voucher">Advance Voucher</option>
                                        </select>
                                    </div>
                                    
                                    <label class="col-sm-3" for="partner_service_center">Select Partner/Service Center</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" id="partner_vendor">
                                            <option value="partner">Partner</option>
                                            <option value="vendor">Vendor</option>
                                        </select>
                                    </div>    
                                  </div>

                                <div class="form-group">
                                    <label class="col-sm-2" for="invoice_by">Invoice By</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" id="invoice_by">
                                            <option value="invoice_date">Invoice Date</option>
                                            <option value="period">Period</option>
                                        </select>
                                    </div>
                                    
                                    <label class="col-sm-3" for="daterange">Select Date Range</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="daterange" name="daterange">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-2" for="report_type">Report Type</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" id="report_type">
                                            <option value="draft">Draft</option>
                                            <option value="final">Final</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button class="btn btn-success" id="get_payment_history" style="margin-left: 16px;">Get Invoice Summary</button>
                                </div>
                            </div>    
                        </div>    
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <div class="permission-error text-danger text-center" style="display:none;"><strong>Operation not permitted</strong> </div>
            <div class="data-error text-danger text-center" style="display:none;"><strong>No Data Found</strong> </div>
            <button type="button" class='btn btn-primary' id='btn_download' style='display:none;float:right;'><i class="fa fa-file" aria-hidden="true"></i>&nbsp;Download Files</button>
            <section class="payment_preview">

            </section>
        </div>
    </div>
</div> 

<!-- end export data Modal -->
    <div class="export_modal">
        <div class="modal fade right" id="buyback_summary_report_model" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="<?php echo base_url() ?>employee/accounting/download_buyback_summary_report">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="main_modal_title">Download Buyback Summary Report</h4>
                    </div>
                    <div class="modal-body" id="main_modal_body">
                        <p>Select Date Range</p>
                        <input type="text" class="form-control" id="buyback_daterange" name="buyback_daterange">
                    </div>
                    <div class="modal-footer">
                        
                        <div class="text-right">
                            <div class="btn btn-default" data-dismiss="modal">Cancel</div>
                            <input type="submit" class="btn btn-success" value="Export" />
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- end export data Modal -->

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
    var time = moment().format('D-MMM-YYYY HH:mm:ss');
    $(function () {
        $('input[name="daterange"], #buyback_daterange').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
            endDate: '<?php echo date('Y-m-d', strtotime('last day of previous month')) ?>',
            minDate: '2015/01/01',
            maxDate: '2030/12/31',
            showDropdowns: true,
            dateLimit: 60
        });
    });

    $(document).ready(function () {
        $('#get_payment_history').click(function () {
            var radio_box_val = $("input[name='challan_optradio']:checked").val();
            //console.log(radio_box_val);
            var type = $('#type').val();
            var partner_vendor = $('#partner_vendor').val();
            var report_type = $('#report_type').val();
            var invoice_by = $('#invoice_by').val();
            var daterange = $('#daterange').val().split('-');
            var from_date = daterange[0];
            var to_date = daterange[1]; 
            if ((type === 'tds' && partner_vendor === 'partner') || (type === 'tds' && partner_vendor === 'stand') || (type === 'advance_voucher' && partner_vendor === 'vendor')) {
                $('.permission-error').show();
                $('.download_report').hide();
                $('.payment_preview').hide();
                $('#btn_download').hide();
                $('.data-error').hide();
            } else {
                $('#loader').show();
                $.ajax({
                    method: 'POST',
                    url: '<?php echo base_url(); ?>employee/accounting/show_accounting_report',
                    data: {type: type, from_date: from_date, to_date: to_date, partner_vendor: partner_vendor,report_type:report_type,is_challan_data:radio_box_val,'invoice_by':invoice_by},
                    success: function (response) {
                        //console.log(response);
                        if (response === "error") {
                            $('#loader').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').hide();
                            $('#btn_download').hide();
                            $('.data-error').show();
                        } else {
                            $('#loader').hide();
                            $('.data-error').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').show();
                            $('.payment_preview').html(response);
                            $('#btn_download').show();
                            //table_pagination();
                            $('#payment_history_table').DataTable({
                                dom: 'Bfrtip',
                                pageLength: 50,
                                "lengthMenu": [[10, 25, 50,100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                            });
                            
                        }


                    }
                });
            }
        });

        $('#btn_download').click(function () {
            create_zip("payment_history_table", "payment_history_details_table");
        });
    });

    function create_zip(summ_tableID, detail_tableID) {
        var zip = new JSZip();

        var tableSelect = tableHTML = '';
        var dataType = 'application/vnd.ms-excel';

        // Add Summary Report
        tableSelect = document.getElementById(summ_tableID);
        tableHTML = tableSelect.outerHTML;//.replace(/ /g, '%20');

        zip.file('Summarized_'+$('#partner_vendor option:selected').text().trim() + '_' + $('#type option:selected').text().trim() + '_Report_' + time + '.xls', tableHTML);

        // Add Details Report
        tableSelect = document.getElementById(detail_tableID);
        tableHTML = tableSelect.outerHTML;//.replace(/ /g, '%20');

        zip.file('Detailed_'+$('#partner_vendor option:selected').text().trim() + '_' + $('#type option:selected').text().trim() + '_Report_' + time + '.xls', tableHTML);

        // Generate the zip file asynchronously
        zip.generateAsync({type: "blob"})
            .then(function (content) {
                // Force down of the Zip file
                saveAs(content, "report" + time + ".zip");
            });
    }

    function saveAs(blob, filename) {
        var elem = window.document.createElement('a');
        elem.href = window.URL.createObjectURL(blob);
        elem.download = filename;
        elem.style = 'display:none;opacity:0;color:transparent;';
        (document.body || document.documentElement).appendChild(elem);
        elem.click();
        URL.revokeObjectURL(elem.href);
    }
</script>
