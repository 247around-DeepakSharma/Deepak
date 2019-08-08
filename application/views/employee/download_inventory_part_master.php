
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="border-bottom: none;">
                    <b> Download Part Master</b>
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
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-3"> Appliance*</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="service_id" required="" name="service_id">
                                        <option value="" selected="" disabled="">Select Appliance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-info" disabled="disabled" id="part_master">Download</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   
    $('#service_id').select2();
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
        get_appliance();
    });
    
    $('#service_id').on('change',function(){
         var service_id = $("#service_id").val();
         if(service_id != ''){
           $("#part_master").removeAttr("disabled");  
         }
       
    });
    
    function get_appliance(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            $.ajax({
                type: 'GET',
                url: '<?php echo base_url() ?>employee/partner/get_partner_specific_appliance',
                data:{is_option_selected:true,partner_id:partner_id,is_not_all_services:true},
                success: function (response) {
                    if(response){
                        $('#service_id').html(response);
                    }else{
                        console.log(response);
                    }
                }
            });
        }else{
            alert('Please Select Partner');
        }
    }
    
    

     $('#part_master').click(function(){
        var partner_id = $("#partner_id").val();
        var service_id = $("#service_id").val();
        
        if((partner_id!=null && partner_id!='') && (service_id!=null && service_id!='')){
            $('#part_master').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_inventory_part_master_data',
                data: {partner_id : partner_id, service_id : service_id},
                success: function (data) {
                    $('#part_master').html("Download").attr('disabled',false);
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
