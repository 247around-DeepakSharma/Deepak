<style>
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
    <form action="<?php echo base_url(); ?>employee/spare_parts/process_download_alternate_parts" method="post">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3><i class="fa fa-download" aria-hidden="true"></i> Download Alternate Spare Part List</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="form-group">
                <div class="form-inline">
                    <div class="form-group col-md-4">
                        <select class="form-control" id="partner_id" name="partner_id">
                            <option value="" disabled="">Select Partner</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="form-group col-md-4">
                <select class="form-control" id="inventory_service_id" name="service_id">
                    <option value="" disabled="">Select Appliance</option>
                </select>
            </div>
            <div class="form-group">
                <div class="form-group col-md-4">
                    <button type="Submit" class="btn btn-success col-md-2" onclick="return download_alternate_data()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    </form>
    <hr>
</div>

<script>
    var alternate_inventory_master_list_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    $('#inventory_service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    
    $(document).ready(function(){
        get_partner();
    });
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    }
    
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            get_services(partner_id);
        }else{
            alert('Please Select Partner');
        }
    });
    
    
    function get_services(partner_id){
        $.ajax({
            type:'GET',
            async: false,
            url:'<?php echo base_url();?>employee/booking/get_service_id_by_partner',
            data:{is_option_selected:true,partner_id:partner_id},
            success:function(response){
                $('#inventory_service_id').html(response);
            }
        });
    }
  
    function download_alternate_data(){
        if(!$("#partner_id").val()){
            alert("Please select partner");
            return false;
        }
        else if(!$("#inventory_service_id").val()){
            alert("Please select appliance");
            return false;
        }
        else{
            return true;
        }
    }
</script>