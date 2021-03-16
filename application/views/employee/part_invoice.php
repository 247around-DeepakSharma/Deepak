<style>
    #docket_number_table_filter{
        text-align: right;
    }
    hr.new1 {
        border-top: 2px dotted #e7e7e7;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_docket_number" style="border: 1px solid #e6e6e6; margin-top: 5px;">
            <h3 style="padding: 10px;"><strong>Download Part Invoice Summary</strong></h3>
            <hr class="new1"/>
            <section class="search_docket_number_div" style="padding: 10px;">
                <div class="row">
                    <div class="form-inline">

                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="shipment_daterange" name="shipment_daterange" style="width:100%;" placeholder="Select Invoice Date">
                        </div>
                        <div class="form-group col-md-2">
                            <a class="btn btn-success"  href="javascript:void(0);"  id="download_invoice_data">Download</a>
<!--                            <span class="badge" title="download all spare data except requested spare"><i class="fa fa-info"></i></span>-->
                        </div>
                    </div>
                </div>
                <br>
            </section>
        </div>
    </div>
</div>
<script>
    
     var time = moment().format('D-MMM-YYYY');
     
    $(function() {

        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end) {
            $('#shipment_daterange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
        }

        $('#shipment_daterange').daterangepicker({
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        
        $('input[name="shipment_daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('input[name="shipment_daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        cb(start, end);

    });
    
    $("#download_invoice_data").click(function(){
        var partner_id = $("#partner_id").val();
        var date_range = $("#shipment_daterange").val();
        

        $('#download_invoice_data').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true)
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>file_process/download_part_invoice_summary',
            data: {partner_id : partner_id, date_range : date_range},
            success: function (data) {
                $('#download_invoice_data').html("Download").attr('disabled',false);
                var obj = JSON.parse(data); 
                console.log(obj);
                if(obj['status']){
                    
                    window.location.href = obj['path'];
                }else{
                    alert('File Download Failed. Please Refresh Page And Try Again...')
                }
            }
        });
         
    });
  
</script>