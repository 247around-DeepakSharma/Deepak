<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Capacity List
                    <a class="btn btn-primary btn-md pull-right" href='javascript:void(0)' id="add_capacity" title="Add Capacity"><i class="glyphicon glyphicon-plus"></i></a>
                </h1>                        
            </div>
        </div>
        <div class="x_panel" style="height: auto;">
            <table id="capacity_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Capacity</th>
                        <th>Name</th>
                        <th>Active</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $rec) {
                        $count = $key + 1;
                        ?>      
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $rec->private_key; ?></td>
                            <td><?php echo $rec->name; ?></td>
                            <td id="row<?= $rec->id ?>">
                                <?php
                                if (!empty($rec->active)) {
                                    echo '<i class="fa fa-check-circle fa-2x text-success" id="btn' . $rec->id . '" onClick="changeStatus(this.id,0)"></i>';
                                } else {
                                    echo '<i class="fa fa-times-circle  fa-2x text-danger" id="btn' . $rec->id . '" onClick="changeStatus(this.id,1)"></i>';
                                }
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-xs" href='javascript:void(0)' title="Update Capacity" id="update_capacity" capacity_id="<?= $rec->id ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--Modal start-->
    <div id="capacity_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action" style="display: inline-block;">Add Capacity</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="capacity_details" method="post" action="<?php echo base_url() . 'capacity/save' ?>">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <!--<label class="col-md-4">Key</label>-->
                                    <div class="col-md-8">
                                        <input type="hidden" name="capacity_id" id="capacity_id" value=""/>
                                        <!--<input type="text" name="private_key" id="private_key" class="form-control" value="" required>-->
                                        <p class="text-danger" id="errorMessage"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Name*</label>
                                    <div class="col-md-8">
                                        <input type="text" name="name" id="name" class="form-control" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Active</label>
                                    <div class="col-md-8">
                                        <input type="checkbox" name="active" id="active" value="1"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="Save" id="Save" class="btn btn-primary" onclick="return validate_form()">
                            <p class="pull-left text-danger">* These fields are required</p>
                            <!--<br/> <p class="pull-left text-danger">* No Special Characters are allowed in Name except dot(.) and Hyphen(-)</p>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
</div>

<script>
    $('#capacity_list').dataTable();

    $(document).on("click", "#add_capacity", function () {
        // Display the Bootstrap modal
        $('#modal_title_action').html("Create Capacity");
        $('#capacity_data').modal('show');
    });

    $(document).on("click", "#update_capacity", function () {
        var id = $(this).attr('capacity_id');
        $.post('<?php echo base_url(); ?>capacity/get_capacity_data', {id: id}, function (response) {
            var data = JSON.parse(response);
            $('#private_key').val(data.private_key);
            $('#name').val(data.name);
            $('#capacity_id').val(data.id);
            if (data.active == 1)
            {
                $('#active').prop("checked", true);
            }
            // Display the Bootstrap modal
            $('#modal_title_action').html("Update Capacity");
            $('#capacity_data').modal('show');
        });
    });

    function changeStatus(btnId, status)
    {
        var statusFlag = "Deactivate";
        if (status == '1')
        {
            statusFlag = "Activate";
        }

        if (!confirm("Are you sure, You want to " + statusFlag + " Capacity ?"))
        {
            return false;
        }

        var id = btnId.substr(3);
        $.post('<?php echo base_url(); ?>capacity/update_status', {id: id, status: status}, function (data) {
            data = $.trim(data);
            if (data == '1')
            {
                if (status == '1')
                {
                    $("#row" + id).html("<i class='fa fa-check-circle fa-2x text-success' id='btn" + id + "' onClick='changeStatus(this.id,0)'></i>");

                }
                else
                {
                    $("#row" + id).html("<i class='fa fa-times-circle fa-2x text-danger' id='btn" + id + "' onClick='changeStatus(this.id,1)'></i>");
                }
            }
        });
    }

    function validate_form()
    {
        var flag = 1;
        var name = $('#name').val();
        var capacity_id = $('#capacity_id').val();
        $("#errorMessage").html('');

        $.ajax({
            url: '<?php echo base_url(); ?>capacity/validate_form',
            async: false,
            method: 'post',
            data: {
                name: name,
                capacity_id: capacity_id
            },
            success: function (data) {
                data = $.trim(data);
                if (data == 'fail')
                {
                    $("#errorMessage").html('Name ' + name + ' has already been taken');
                    flag = 0;
                }            
            },
        });

        if (flag == 0)
        {
            return false;
        }
        return true;
    }

</script>

<style>
    .dataTables_filter, .dataTables_paginate
    {
        float: right;
    }
</style>