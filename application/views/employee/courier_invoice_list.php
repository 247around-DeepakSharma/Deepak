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
                        <div class="form-group col-md-3">
                            <label class="control-label">Partner * </label>
                        </div>
                        <div class="form-group col-md-7">
                            <select class="form-control" id="partner_id">
                                <option value="" disabled="">Select Partner</option>
                            </select>
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
    
    get_partner();
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    }
    
    $("#download_invoice_data").click(function(){
        var partner_id = $("#partner_id").val();
         if(partner_id == undefined || partner_id == ''){
             alert("Please Select Partner.");
         }else{
              $('#download_invoice_data').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true)
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_courier_invoice_data',
                data: {partner_id : partner_id},
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