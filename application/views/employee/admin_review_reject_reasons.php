<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('success') . '</strong>
                 </div>';
            }
            ?>
            <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('error') . '</strong>
                 </div>';
            }
            ?>
        <div>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Admin Review Rejection Reasons
                    <a class="btn btn-primary btn-md pull-right" href='javascript:void(0)' id="add_reason" title="Add Reason"><i class="glyphicon glyphicon-plus"></i></a>
                </h1>                        
            </div>
        </div>
        <div class="x_panel" style="height: auto;">
            <table id="reason_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Rejection Reason</th>
                        <th>Penalty Point</th>
                        <th>Reason Of</th>
                        <th>Agent Name</th>
                        <th class="no-sort">Active</th> 
                        <th class="no-sort">Action</th>
                        <th style="display: none;">Active</th>


                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $rec) {
                        $count = $key + 1;
                        ?>      
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $rec['criteria']; ?></td>
                            <td><?php echo $rec['penalty_point']; ?></td>
                            <td><?php if($rec['reason_of'] == 1) {
                                echo "Completion";
                            } else {
                                echo "Cancellation";
                            } ?></td>
                            <td><?php echo $rec['agent_name']; ?></td>
                            <td id="row<?= $rec['id'] ?>">
                                <?php
                                if (!empty($rec['active'])) {
                                    echo '<i class="fa fa-check-circle fa-2x text-success" id="btn' . $rec['id'] . '" onClick="changeStatus(this.id,0)"></i>';
                                } else {
                                    echo '<i class="fa fa-times-circle  fa-2x text-danger" id="btn' . $rec['id'] . '" onClick="changeStatus(this.id,1)"></i>';
                                }
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-xs" href='javascript:void(0)' title="Update Reason" id="update_reason" reason_id="<?= $rec['id']; ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            </td>
                            <td style="display: none;">
                                <?php if(!empty($rec['active'])) {
                                    echo "Yes";
                                } else  {
                                    echo "No";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--Modal start-->
    <div id="reason_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action" style="display: inline-block;">Add Review Rejection Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="reject_reasons" method="post" action="<?php echo base_url() . 'penalty/save_review_reject_reason' ?>">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <input type="hidden" name="reason_id" id="reason_id" value=""/>
                                <p class="text-danger" id="errorMessage"></p>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Rejection Reason*</label>
                                    <div class="col-md-8">
                                        <input type="text" name="criteria" id="criteria" class="form-control" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Penalty Point*</label>
                                    <div class="col-md-8">
                                        <input type="number" name="penalty_point" id="penalty_point" class="form-control" value="" min="1" max="10" required>
                                    </div>
                                </div>
                            </div>
                                <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Reason Of*</label>
                                    <div class="col-md-8">
                                        <select id="reason_of" name="reason_of" class="form-control" required>
                                            <option value="2">Cancellation</option> 
                                            <option value="1">Completion</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Active</label>
                                    <div class="col-md-8">
                                        <input type="checkbox" checked name="active" id="active" value="1"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="save_data" id="save_data" class="btn btn-primary" onclick="return validate_review_form()">Save</button>
                            <p class="pull-left text-danger">* These fields are required</p>                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
</div>

<script>
    $('#reason_list').dataTable({
        "order": [],
         "columnDefs": [ {
           "targets"  : 'no-sort',
           "orderable": false,
        }],
        "dom": 'lBfrtip',
        "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    title: 'admin-review-rejection-reasons-list',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 7]
                    }
                }
        ]                        
     });

    $(document).on("click", "#add_reason", function () {
        // Display the Bootstrap modal
        $('#modal_title_action').html("Add Review Rejection Reason");
        $('#reason_data').modal('show');
    });

    $(document).on("click", "#update_reason", function () {
        var id = $(this).attr('reason_id');
        $.post('<?php echo base_url(); ?>penalty/get_penalty_data', {id: id}, function (response) {
            var data = JSON.parse(response);
            $('#criteria').val(data.criteria);
            $('#penalty_point').val(data.penalty_point);
            $('#reason_id').val(data.id);
            $('#reason_of').val(data.reason_of);
            if (data.active == 1)
            {
                $('#active').prop("checked", true);
            }
            else {
                $('#active').prop("checked", false);
            }
            // Display the Bootstrap modal
            $('#modal_title_action').html("Update Review Rejection Reason");
            $('#reason_data').modal('show');
        });
    });

    function changeStatus(btnId, status)
    {
        var statusFlag = "Deactivate";
        if (status == '1')
        {
            statusFlag = "Activate";
        }

        if (!confirm("Are you sure, You want to " + statusFlag + " this reason ?"))
        {
            return false;
        }

        var id = btnId.substr(3);
        $.post('<?php echo base_url(); ?>penalty/update_review_rejection_reason_status', {id: id, status: status}, function (data) {
            data = $.trim(data);
            if (data == '1')
            {
                alert("Data updated successfully");            
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

    function validate_review_form()
    {
        var flag = 1;
        var criteria = $('#criteria').val();
        var reason_id = $('#reason_id').val();
        var reason_of = $('#reason_of').val();
        $("#errorMessage").html('');
        $("#save_data").prop('disabled', true);

        $.ajax({
            url: '<?php echo base_url(); ?>penalty/validate_review_rejection_reason',
            async: false,
            method: 'post',
            data: {
                criteria : criteria,
                reason_id : reason_id,
                reason_of : reason_of
            },
            success: function (data) {
                $("#save_data").prop('disabled', false);
                data = $.trim(data);
                if (data == 'fail')
                {
                    $("#errorMessage").html('Name "' + criteria + '" has already been taken');
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

    $('#reason_data').on('hidden.bs.modal', function () {
        $('#reason_id').val('');
        $('#reason_of').val('');
        $('#criteria').val('');
        $('#penalty_point').val('');
    });
</script>

<style>
    .dataTables_filter, .dataTables_paginate
    {
        float: right;
    }
</style>
<?php
    if ($this->session->userdata('success')) {
        $this->session->unset_userdata('success');
    } if ($this->session->userdata('error')) {
        $this->session->unset_userdata('error');
    }
?>