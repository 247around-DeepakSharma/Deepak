
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="border-bottom: none;">
                    <b> Download Sale and Purchase MSL Invoice </b>
                </h1>
                <div class="x_panel" style="padding-top: 40px; padding-bottom: 30px;">
                    <section>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-3"> Partner*</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-info" disabled="disabled" id="inventory_ledger">Download</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   
    $(document).ready(function () {
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh : true},
            success:function(response){
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    });
    
    
    
    $('#partner_id').on('change',function(){
         var partner_id = $("#partner_id").val();
         if(partner_id != ''){
           $("#inventory_ledger").removeAttr("disabled");  
         }
       
    });
    
    
    
     $('#inventory_ledger').click(function(){
        var partner_id = $("#partner_id").val();
       
        if((partner_id!=null && partner_id!='')){
            $('#inventory_ledger').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_sale_purchage_invoice_data',
                data: { partner_id : partner_id },
                success: function (data) {
                    $('#inventory_ledger').html("Download").attr('disabled',false);
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
