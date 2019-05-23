<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }
    .alternate_spare_list{
        float: right;  
        font-size: 22px;
        color: #131212cc;
        padding: 5px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Spare Parts Price List</h3>
                    <div class="clearfix"></div>
                </div>
                <br>
                <div class="x_content">
                     <?php if(empty($model_number_id)){ ?>
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="inventory_service_id">
                                            <option value="" disabled="">Select Appliance</option>
                                        </select>
                                    </div>
                                    
                                     <div class="form-group col-md-3">
                                        <select class="form-control" id="model_number_id" name="model_number_id" required="">
                                            <option value="" selected="" disabled="">Please Select Model</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success btn-sm col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <br>
                    <br>
                    <?php }else{ ?> 
                        <input type="hidden" id="model_number_id" value="<?php echo $model_number_id; ?>">
                    <?php } ?>
                        <div class="clearfix"></div>
                    <div class="inventory_stock_list">
                        <table id="serviceable_bom_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Description</th>
<!--                                    <th>HSN</th>
                                    <th>Basic Price</th>
                                    <th>GST Rate</th>-->
                                    <th>SF Price(including GST)</th>
                                    <th>SF Margin</th>
                                    <th>Customer Price (including GST)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
         
    </div>
</div>
<script>

    var serviceable_bom_table;

    $(document).ready(function () {
        get_partner();
        var partner_id = '<?php echo $partner_id; ?>';
        get_services('inventory_service_id',partner_id);
        get_inventory_list();
    });
    
    $('#inventory_service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    $('#model_number_id').select2({
        allowClear: true,
        placeholder: 'Select Model Number'
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            serviceable_bom_table.ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_inventory_list(){
        serviceable_bom_table = $('#serviceable_bom_table').DataTable({
            "processing": true,
            "serverSide": true,
               "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            "order": [],
            "pageLength": 100,
            "dom": 'lBfrtip',
            "ordering": false,
              "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>   Export',
                    pageSize: 'LEGAL',
                    title: 'Spare Parts Price List',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6,7,8],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_serviceable_bom_details",
                type: "POST",
                data: function(d){
                    var entity_details = get_entity_details();
                    d.partner_id = entity_details.partner_id,
                    d.service_id = entity_details.service_id,
                    d.model_number_id = entity_details.model_number_id                    
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'partner_id': '<?php echo $partner_id; ?>',
            'service_id' : '<?php echo $service_id; ?>',   
            'model_number_id' : $("#model_number_id").val(),
            
        };
        
        return data;
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#partner_id').html(response);
                $("#partner_id [value='<?php echo $partner_id; ?>']").attr('selected', 'selected');
                $('#partner_id').select2();
                
            }
        });
    }
    
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            get_services('inventory_service_id',partner_id);
        }else{
            alert('Please Select Partner');
        }
    });
    
    
    function get_services(div_to_update,partner_id){
        
        $.ajax({
            type:'GET',
            async: false,
            url:'<?php echo base_url();?>employee/partner/get_partner_specific_appliance',
            data:{is_option_selected:true,partner_id:partner_id},
            success:function(response){
                $('#'+div_to_update).html(response);
                $('#inventory_service_id').val('<?php echo $service_id; ?>').change();
                get_model_number_list('<?php echo $partner_id; ?>','<?php echo $service_id; ?>');
            }
        });
    }
    
    
    $('#inventory_service_id').on('change',function(){
        var service_id = $('#inventory_service_id').val();
        var entity_id = $('#partner_id').val();
        
        get_model_number_list()
     
    });
    
    function get_model_number_list(entity_id,service_id){
       
        if(service_id && entity_id){
            
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_appliance_model_number',
                data:{partner_id:entity_id,entity_type: '<?php echo _247AROUND_PARTNER_STRING ; ?>', service_id:service_id},
                success:function(data){   
                    $("#model_number_id").html(data);
                }
            });
            
        }
    }
    
</script>
