<style>
    #brand_collateral_partner_length{
        width: 12%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <form action="<?php echo base_url() ?>employee/partner/download_all_brand_collateral" method="POST">
                        <h2>Brand Collateral</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <button type="submit" class="btn btn-success" style="margin-top: 10px;">Download All</button>
                        </ul>
                    </form>
                    <input type="hidden" id="partner_id" value="<?php echo $this->session->userdata('partner_id'); ?>">
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="filter_box">
                        <div class="row">
                            <div class="form-inline">
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="service_id" onchange="get_partner_brands()">
                                        <option value="" disabled="">Select Appliance1</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="brand" onchange="get_model()">
                                        <option value="" disabled="">Select Brand</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="model">
                                        <option value="" disabled="">Select Model</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <select class="form-control" id="request_type" required="" name="request_type[]" multiple="multiple" >
                                        <option disabled  >Select Service Category</option>
                                        <option value="Installation"  >Installation</option>
                                        <option value="Repair"  >Repair</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-success col-md-2" onclick="validform()">Search</button>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="inventory-table">
                        <table class="table table-bordered table-hover table-striped" id="brand_collateral_partner">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Document Type</th>
                                    <th>Model</th>
                                    <th>Category</th>
                                    <th>Capacity</th>
                                    <th>File</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script>

    $('#service_id').select2({
        allowClear: true,
        placeholder: 'Select Appliance'
    });
    $('#brand').select2({
        allowClear: true,
        placeholder: 'Select Brand'
    });
    $('#model').select2({
        allowClear: true,
        placeholder: 'Select Model'
    });
    
    $('#request_type').select2({
        allowClear: true,
        placeholder: 'Select Request Type'
    });
    
      var ad_table;
        ad_table = $('#brand_collateral_partner').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            "lengthMenu": [[10, 25, 50,100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Export',
                    pageSize: 'LEGAL',
                    title: 'partner_brand_collateral',
                    exportOptions: {
                       columns: [0,1,2,3,4,6,7],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/brandCollateralPartner",
                "type": "POST",
                "data": function(d){
                  d.partner_id = $("#partner_id").val();
                  d.service_id = $("#service_id option:selected").val();
                  d.brand = $("#brand option:selected").val();
                  d.model = $("#model option:selected").val();
                  d.request_type = $("#request_type").val();
               }
            },
            "columnDefs": [
                {
                    "targets": [0,5,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
        });
    
    
    $(document).ready(function(){
        get_services('service_id');
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
    
    function get_partner_brands(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_brands_from_service',
            data:{partner_id:$('#partner_id').val(), service_id:$('#service_id').val()},
            success:function(response){
              ///  alert(response);
                response = "<option disabled selected>Select Brand</option>"+response;
                $('#brand').html(response);
                 //get_partner_mapping_category();
               //  $("#service_id").select2();
            }
        });
    }
    
    function get_model(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner_with_brand',
            data: {service_id: $('#service_id').val(),partner_id:$('#partner_id').val(),brand: $('#brand').val()},
            success: function (data) {
                //First Resetting Options values present if any
                $('#model').html("<option selected disabled  >Select Model</option>");
                $('#model').append(data);
               // $('#model').trigger("change");
            }
        });
    }
    
    function validform(){
        var service =  $("#service_id option:selected").val();
        var brand =  $("#brand option:selected").val();
        var request_type =  $("#request_type").val();
        
        if(service==='Select Service')
        {
           alert("Please Select Service ");
           return false;
        }
        else if(brand==='Select Brand')
        {
           alert("Please Select Brand ");
           return false;
        }
        else if(brand==='Select Model')
        {
           alert("Please Select Model ");
           return false;
        }
        else if(request_type==null || request_type=='')
        {
           alert("Please Select Service Category ");
           return false;
        }
        else {
            ad_table.ajax.reload( function ( json ) {} );
        }
    }
  
  
</script>
