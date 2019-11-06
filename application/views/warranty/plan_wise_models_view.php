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
                <h1 class="page-header">
                    Warranty Plans 
                    <button type="button" class="btn btn-primary" id="submit_btn" target="_blank" onClick="window.location.href = '<?php echo base_url(); ?>employee/warranty/add_model_to_plan';
                            return false;" style="float:right" value="Add Models">Add Models</button>
                </h1>                        
            </div>
        </div>
        <div class="x_panel" style="height: auto;">
            <table id="datatablemappingview" class="table table-striped table-bordered">
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($plan_data as $key => $row) {
                        ?>
                        <tr>
                            <td><?php echo $row->plan_name; ?></td>
                            <td><?php echo $row->plan_description; ?></td>
                            <td><?php echo date('jS M, Y', strtotime($row->period_start)); ?></td>                            
                            <td><?php echo date('jS M, Y', strtotime($row->period_end)); ?></td>   
                            <td><?php echo ($row->warranty_type == 1 ? "IW" : "EW"); ?></td>
                            <td><?php echo $row->warranty_period." Months"; ?></td>
                            <td><?php echo $row->public_name; ?></td>
                            <td><?php echo $row->services; ?></td>
                            <td><?php echo $row->model_number; ?></td>
                            <td id='<?php echo "column" . $key; ?>'>
                                <?php if(!empty($row->is_active)){?>
                                <button id='<?php echo "removebtn" . $key; ?>' class="btn btn-primary remove" 
                                        value="remove" data-id="<?php echo $row->mapping_id; ?>" onclick="delete_mapping(<?php echo $key; ?>)" title="Remove Model">               
                                        <i class="fa fa-trash"></i>
                                </button>
                                <?php } else { ?>
                                    <button id='<?php echo "addbtn" . $key; ?>' class="btn btn-primary add" 
                                        value="add" data-id="<?php echo $row->mapping_id; ?>" onclick="add_mapping(<?php echo $key; ?>)" title="Add Model">               
                                        <i class="fa fa-link"></i>
                                    </button>
                                <?php } ?>                                
                            </td>
                        </tr>
                    <?php }
                    ?>  
                </tbody>
            </table>
        </div>
    </div>    
</div>
<script>
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
                title: 'Plans Table',
                exportOptions: {
                    columns: [0, 1, 2, 3],
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