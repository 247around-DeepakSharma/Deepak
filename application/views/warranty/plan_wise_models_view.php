<div id="page-wrapper">
    <div class="container-fluid">
        <?php if (validation_errors()) { ?>
            <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
                <div class="panel-heading" style="padding:7px 0px 0px 13px">
                    <?php echo validation_errors(); ?>

                </div>
            </div>
        <?php } ?>
        <?php if ($this->session->userdata('failed')) { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><strong><?php echo $this->session->userdata('failed') ?></strong></center>

            </div>
        <?php } ?>


        <?php if ($this->session->userdata('success')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center> <strong><?php echo $this->session->userdata('success') ?></strong></center>

            </div>
        <?php } ?>

        <?php
        $this->session->unset_userdata('success');
        $this->session->unset_userdata('failed');
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">                    
                    <h1>Warranty Plans</h1> <hr/>
                    <div class="row">
                        <div class="col-md-10">
                            <form name="myForm" id="myForm" action="<?php echo base_url()?>employee/warranty/plan_model_mapping"  method="POST"   >
                                <div class="row">
                                    <div class="col-md-4"  style="width:200px;">
                                        <select type="text" class="form-control"  id="partner_id" name="partner_id" required>
                                            <option selected="selected" disabled="disabled">Select Partner</option>
                                            <?php foreach ($partners as $key => $values) { ?>
                                            <option value=<?php echo $values['id']; ?> <?php if($values['id'] == $selected_partner_id){ echo " selected "; } ?>>
                                                <?php echo $values['public_name']; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4"  style="width:200px;">
                                        <select type="text" class="form-control"  id="service_id" name="service_id" required >
                                            <option selected="selected" disabled="disabled">Select Product</option>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option value=<?php echo $values['id']; ?> <?php if($values['id'] == $selected_service_id){ echo " selected "; } ?>>
                                                <?php echo $values['services']; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button name="show" value="Show" class="btn btn-primary" onclick='return showplanlist()'>Show</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" id="submit_btn" target="_blank" onClick="window.location.href = '<?php echo base_url(); ?>employee/warranty/add_model_to_plan';
                            return false;" style="float:right" value="Add Models">Add Models</button>
                        </div>
                    </div>                                     
                </div>                        
            </div>
        </div>
        
            <div class="table-responsive">
                <div class="x_panel" style="height: auto;">
                    <table class="table table-bordered table-condensed" id="vendor_details">
                        <thead>
                            <tr>
                                <th>Plan Name</th>
                                <th>Plan Description</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Warranty type</th>
                                <th>Period</th>
                                <th>Partner</th>
                                <th>Product</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
</div>




<script>
    function showplanlist(){
         vendor_details.ajax.reload(null, false);
        return false;
    }
    vendor_details = $('#vendor_details').DataTable({
        "processing": true,
        "serverSide": true,
        "dom": 'lBfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: 'Export',
                 exportOptions: {
                    columns: [0, 1,2, 3, 4, 5, 6, 7,8,9]
                },
                title: 'plan_model_mapping'
            },
        ],
        "language": {
            "processing": "<div class='spinner'>\n\
                                       <div class='rect1' style='background-color:#db3236'></div>\n\
                                       <div class='rect2' style='background-color:#4885ed'></div>\n\
                                       <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                       <div class='rect4' style='background-color:#3cba54'></div>\n\
                                   </div>",
            "emptyTable": "No Data Found"
        },

        "order": [],
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "ordering": false,
        "ajax": {
            url: "<?php echo base_url(); ?>employee/warranty/plan_model_mapping_ajax",
            type: "POST",
            data: function (d) {
                var entity_details = get_entity_details();
                d.partner_id = entity_details.partner_id,
                        d.service_id = entity_details.service_id
            }
        }
    });



    function get_entity_details() {
        var data = {
            'partner_id': $('#partner_id').val(),
            'service_id': $('#service_id').val(),
            'city': '',
            'sf_cp': 1
        };

        return data;
    }
</script>
<script>
    $("#partner_id").select2();
    $("#service_id").select2();
    
    $('#datatablemappingview').DataTable({
        "processing": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "serverSide": false,
        "dom": 'lBfrtip',
        title: 'warranty_plan_models',
        "buttons": [
            {
                extend: 'excel',
                text: '<span class="fa fa-file-excel-o"></span>  Export',
                pageSize: 'LEGAL',
                title: 'warranty_plan_models',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    modifier: {
                        // DataTables core
                        order: 'index', // 'current', 'applied', 'index',  'original'
                        page: 'current', // 'all',     'current'
                        search: 'none'     // 'none',    'applied', 'removed'
                    }
                }

            }
        ],
        "language": {
            "processing": "<div class='spinner'>\n\
                                <div class='rect1' style='background-color:#db3236'></div>\n\
                                <div class='rect2' style='background-color:#4885ed'></div>\n\
                                <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                <div class='rect4' style='background-color:#3cba54'></div>\n\
                            </div>"
        },
        select: {
            style: 'multi'
        },
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "ordering": false,
        "order": [],
        "pageLength": 25,
        "ordering": false,
        "deferRender": true
    });
    
    function delete_mapping(key) {
        var mapping_id = $("#removebtn" + key).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/warranty/remove_model_from_plan',
            data: {mapping_id: mapping_id},
            success: function (data) {
                console.log(data);
                if($.trim(data) == "success")
                {
                    alert("Model Removed Successfully");
                    $("#column" + key).html("");
                    vendor_details.ajax.reload(null, false);
                }
            }
        });
    }
    
    function add_mapping(key) {
        var mapping_id = $("#addbtn" + key).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/warranty/activate_model_to_plan',
            data: {mapping_id: mapping_id},
            success: function (data) {
                console.log(data);
                if($.trim(data) == "success")
                {
                    alert("Model Added Successfully");
                    $("#column" + key).html("");
                    vendor_details.ajax.reload(null, false);
                }
            }
        });
    }
    
    function deactivate_plan(key) {
        var plan_id = $("#deactivate_model_btn" + key).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/warranty/deactivate_plan',
            data: {plan_id: plan_id},
            success: function (data) {
                console.log(data);
                if($.trim(data) == "success")
                {
                    alert("Plan Deactivated Successfully");
                    vendor_details.ajax.reload(null, false);
                }
            }
        });
    }
    
    function activate_plan(key) {
        var plan_id = $("#activate_model_btn" + key).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/warranty/activate_plan',
            data: {plan_id: plan_id},
            success: function (data) {
                console.log(data);
                if($.trim(data) == "success")
                {
                    alert("Plan Activated Successfully");
                    vendor_details.ajax.reload(null, false);
                }
            }
        });
    }
</script>

<style>
    @media (min-width: 1200px){
        .container {
            width: 100% !important;
        }

        .dataTables_filter{
            float: right !important;
            margin-top: -30px !important;
        }
    }
    
    div.dt-buttons {
        position: relative;
        float: right;
        margin-top: -30px;
        margin-left: 10px;
    }
</style>