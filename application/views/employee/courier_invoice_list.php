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
            <h3 style="padding: 10px;"><strong>Download Courier Invoice</strong></h3>
            <hr class="new1"/>
            <section class="search_docket_number_div" style="padding: 10px;">
                <div class="row">
                    <div class="form-inline">
                        <div class="form-group col-md-2">
                            <label class="radio-inline"><input type="radio" name="download_invoice_type" value="awb_by_partner"> New Spare Part Sent To SF</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="radio-inline"><input type="radio" name="download_invoice_type" value="awb_by_sf"> Defective/New Part Return From SF</label>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="radio-inline"><input type="radio" name="download_invoice_type" value="awb_by_wh"> Defective/New Part Return To Partner From Warehouse</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="radio-inline"><input type="radio" name="download_invoice_type" value="msl"> MSL </label>
                        </div>
                        <div class="form-group col-md-2">
                            <a class="btn btn-success"  href="javascript:void(0);"  id="download_invoice_data">Download</a><span class="badge" title="download all spare data except requested spare"><i class="fa fa-info"></i></span>
                        </div>
                    </div>
                </div>
                <br>
            </section>
        </div>
    </div>
</div>
<script>
    
    
    $("#download_invoice_data").click(function(){
        var invoice_type = $("input[name='download_invoice_type']:checked").val();
         if(invoice_type == undefined || invoice_type == ''){
             alert("Please Select Courier Invoice.");
         }else{
              $('#download_invoice_data').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true)
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_courier_invoice_data',
                data: {invoice_type : invoice_type},
                success: function (data) {
                    $('#download_invoice_data').html("Download").attr('disabled',false);
                    var obj = JSON.parse(data); 
                    if(obj['status']){
                        window.location.href = obj['msg'];
                    }else{
                        alert('File Download Failed. Please Refresh Page And Try Again...')
                    }
                }
            });
         }
    });
  
</script>