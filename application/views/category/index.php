<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Category List
                    <a class="btn btn-primary btn-md pull-right" href='javascript:void(0)' id="add_category" title="Add Category"><i class="glyphicon glyphicon-plus"></i></a>
                </h1>                        
            </div>
        </div>
        <div class="x_panel" style="height: auto;">
            <table id="category_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Category</th>
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
                                <a class="btn btn-primary btn-xs" href='javascript:void(0)' title="Update Category" id="update_category" category_id="<?= $rec->id ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--Modal start-->
    <div id="category_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action" style="display: inline-block;">Add Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="category_details" method="post" action="<?php echo base_url() . 'category/save' ?>">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <!--<label class="col-md-4">Key</label>-->
                                    <div class="col-md-8">
                                        <input type="hidden" name="category_id" id="category_id" value=""/>
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
    $('#category_list').dataTable();

    $(document).on("click", "#add_category", function () {
        // Display the Bootstrap modal
        $('#modal_title_action').html("Create Category");
        $('#category_data').modal('show');
    });

    $(document).on("click", "#update_category", function () {
        var id = $(this).attr('category_id');
        $.post('<?php echo base_url(); ?>category/get_category_data', {id: id}, function (response) {
            var data = JSON.parse(response);
            $('#private_key').val(data.private_key);
            $('#name').val(data.name);
            $('#category_id').val(data.id);
            if (data.active == 1)
            {
                $('#active').prop("checked", true);
            }
            // Display the Bootstrap modal
            $('#modal_title_action').html("Update Category");
            $('#category_data').modal('show');
        });
    });

    function changeStatus(btnId, status)
    {
        var statusFlag = "Deactivate";
        if (status == '1')
        {
            statusFlag = "Activate";
        }

        if (!confirm("Are you sure, You want to " + statusFlag + " Category ?"))
        {
            return false;
        }

        var id = btnId.substr(3);
        $.post('<?php echo base_url(); ?>category/update_status', {id: id, status: status}, function (data) {
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
        var category_id = $('#category_id').val();
        $("#errorMessage").html('');
        
        // Validate name only alphanumeric characters along with . and - are allowed
//        var reg_exp = '/^[0-9a-zA-Z]+$/';
//        if (!(name.match(reg_exp))) {
//            alert("Only alpha-numeric characters along with dot(.) and hyphen(-) are allowed for Category Name");
//            return false;
//        }

        $.ajax({
            url: '<?php echo base_url(); ?>category/validate_form',
            async: false,
            method: 'post',
            data: {
                name: name,
                category_id: category_id
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