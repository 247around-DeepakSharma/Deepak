<div id="page-wrapper">
    <div class="container-fluid">        
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-header">
                    Partner Appliance List   
                    <div class="pull-right" title="Add Product Mapping" style="padding-right:5px;"><a class="btn btn-primary" href="<?php echo  base_url()?>employee/service_centre_charges/add_new_category" target="_blank">Add Product Mapping</a></div>
                    <div class="pull-right" title="Add Capacity" style="padding-right:5px;"><a class="btn btn-primary" href="<?php echo  base_url()?>capacity" target="_blank">Add Capacity</a></div>
                    <div class="pull-right" title="Add Category" style="padding-right:5px;"><a class="btn btn-primary" href="<?php echo  base_url()?>category" target="_blank">Add Category</a></div>
                </h1>                                                      
            </div>            
             
        </div>
        <form id="applianceInfo" name="applianceInfo"  method="POST" action="map_partner_appiances">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-control" id="partner_id" required name="partner_id">
                        <option value="" disabled="" selected>Select Partner</option>
                        <?php
                        foreach ($partners as $key => $partner) {
                            echo "<option value='" . $partner['partner_id'] . "'>" . $partner['source'] . "</option>";
                        }
                        ?>
                    </select>              
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="service_id" required name="service_id">
                        <option value="" disabled="" selected>Select Product</option>
                        <?php
                        foreach ($services as $service) {
                            echo "<option value='" . $service->id . "'>" . $service->services . "</option>";
                        }
                        ?>
                    </select>          
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="status" required name="status" style="height:28px;">
                        <option value="" disabled="" selected>Select Status</option>
                        <?php
                        foreach ($status as $key => $rec) {
                            echo "<option value='" . $key . "'>" . $rec . "</option>";
                        }
                        ?>
                    </select>          
                </div>
                <div class="col-md-2">
                    <input type="button" name="Show" id="Show" class="btn btn-success" value="Show">
                </div>
            </div>
        </form>        
        <div class="x_panel" style="height: auto;">
            <table id="partner_appliance_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Category</th>
                        <th>Capacity</th>
                        <th>Is Mapped</th>
                    </tr>
                </thead>
                <tbody>                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var ad_table;
    ad_table = $('#partner_appliance_list').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "ordering": false, //Initial no order.
        "pageLength": 50,
        "deferLoading": 0,
        "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<span class="fa fa-file-excel-o"></span> Export',
                pageSize: 'LEGAL',
                title: 'partner_appliance_details',
                exportOptions: {
                    modifier: {
                        // DataTables core
                        order: 'index', // 'current', 'applied', 'index',  'original'
                        page: 'current', // 'all',     'current'
                        search: 'none'     // 'none',    'applied', 'removed'
                    }
                }
            }
        ],
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url(); ?>employee/service_centre_charges/map_partner_appiances",
            "type": "POST",
            "data": function (d) {
                d.partner_id = $("#partner_id option:selected").val();
                d.service_id = $("#service_id option:selected").val();
                d.status = $("#status option:selected").val();
            },
        },
        "columnDefs": [
            {
                "targets": [0, 1, 2, 3], //first column / numbering column
                "orderable": false //set not orderable
            }
        ],
    });

    $('#partner_id').select2({
        placeholder: 'Select Partner',
        allowClear: true
    });

    $('#service_id').select2({
        placeholder: 'Select Appliance',
        allowClear: true
    });

    $('#Show').click(function () {
        ad_table.ajax.reload(function (json) {
        });
    });

    function addMapping(configId, partnerId)
    {
        if (!confirm("Are you sure, You want to Map this Configuration to partner ?"))
        {
            return false;
        }
        
        $.post('<?php echo base_url(); ?>employee/service_centre_charges/map_appliance_configuration', {configId: configId, partnerId: partnerId}, function (data) {
            data = $.trim(data);
            if (data != '')
            {
                $("#row" + configId).html("<i class='fa fa-check-circle fa-2x text-success' onClick='deleteMapping("+data+", "+configId+", "+partnerId+")'></i>");
            }
        });
    }
    
    function deleteMapping(mappingId, configId, partnerId)
    {
        if (!confirm("Are you sure, You want to Un-map this Configuration ?"))
        {
            return false;
        }
        
        $.post('<?php echo base_url(); ?>employee/service_centre_charges/unmap_appliance_configuration', {mappingId: mappingId}, function (data) {
            data = $.trim(data);
            if (data != '')
            {
                $("#row" + configId).html("<i class='fa fa-times-circle  fa-2x text-danger' onClick='addMapping("+configId+", "+partnerId+")'></i>");
            }
        });
    }
</script>

<style>
    .dataTables_paginate
    {
        float: right;
    }
    .dataTables_filter{
        float: right !important;
        margin-top: -30px !important;
    }
    div.dt-buttons {
        position: relative;
        float: right;
        margin-top: -30px;
        margin-left: 10px;
    }
</style>