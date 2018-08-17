<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
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
<!--                                            <option value="buyback">Buyback</option>-->
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
            <section class="payment_preview">

            </section>
        </div>
    </div>
</div> 
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
    var time = moment().format('D-MMM-YYYY');
    $(function () {
        $('input[name="daterange"]').daterangepicker({
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
                    data: {type: type, from_date: from_date, to_date: to_date, partner_vendor: partner_vendor,report_type:report_type,is_challan_data:radio_box_val,'invoice_by':invoice_by},
                    success: function (response) {
                        //console.log(response);
                        if (response === "error") {
                            $('#loader').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').hide();
                            $('.data-error').show();
                        } else {
                            $('#loader').hide();
                            $('.data-error').hide();
                            $('.permission-error').hide();
                            $('.payment_preview').show();
                            $('.payment_preview').html(response);
                            //table_pagination();
                            $('#payment_history_table').DataTable({
                                dom: 'Bfrtip',
                                pageLength: 50,
                                ordering: false,
                                buttons: [
                                    {
                                        extend: 'excel',
                                        text: 'Export',
                                        title: $('#partner_vendor option:selected').text().trim() +'_' + $('#type option:selected').text().trim()+'_report_'+time
                                    }
                                ]
                            });
                            
                        }


                    }
                });
            }
        });
    });

</script>
