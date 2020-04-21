<style>
    #appliance_model_details_filter{
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
    
    #appliance_model_details_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
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
    
    .dataTables_length{
        width: 20%;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Download Part Master</h2>
                    <input type="hidden" id="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>">
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="filter_box">
                        <div class="row">
                            <div class="form-inline">
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="model_service_id">
                                        <option value="" disabled="">Select Appliance</option>
                                    </select>
                                </div>
                                <button class="btn btn-success col-md-2" id="part_master">Download</button>
                            </div>
                        </div>
                    </div>
                    </br>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    $('#model_service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    $(document).ready(function(){
        get_services('model_service_id');
    });
    
     
    
    
    function get_services(div_to_update){
        $.ajax({
            type:'GET',
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id: '<?php echo $this->session->userdata('partner_id')?>'},
            success:function(response){
                $('#'+div_to_update).html(response).find("#allappliance").remove();  
                $('#'+div_to_update).select2({
                    allowClear: true,
                    placeholder: 'Select Appliance'
                });
            }
        });
    }

    $('#part_master').click(function(){
            var partner_id = $("#partner_id").val();
            var service_id = $("#model_service_id").val();

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
