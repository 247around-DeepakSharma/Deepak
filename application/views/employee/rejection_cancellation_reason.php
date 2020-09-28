<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                   Cancellation Rejection Reason
                    <a class="btn btn-primary btn-md pull-right" href='javascript:void(0)' id="add_inventory" title="Add Reason"><i class="glyphicon glyphicon-plus"></i></a>
                </h1>                        
            </div>
        </div>
        <?php if ($this->session->userdata('error')) { ?>
             <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <center><strong><?php echo $this->session->userdata('error') ?></strong></center>
                        
                    </div>
             <?php } ?>
        
           
                 <?php if ($this->session->userdata('success')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>


                        <center>  <strong><?php echo $this->session->userdata('success') ?></strong></center>
                      
                    </div>
             <?php } ?>
        
             <?php
//             
               $this->session->unset_userdata('success');
               $this->session->unset_userdata('error');
//            
             ?>
        <div class="x_panel" style="height: auto;">
            <table id="inventory_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Booking Cancellation Reason</th>
                        <th>Admin Rejection Reason</th>>
                        <th>Penalty</th>
                        <th>Agent Name</th>>
                        <th class="no-sort">Active</th> 
                        <th class="no-sort">Action</th>
                        <th style="display: none;">Active</th>


                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($cancel_reject as $key => $rec) {
                        $count = $key + 1;
                        ?>      
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $rec['reason']; ?></td>
                            <td><?php echo $rec['criteria']; ?></td>
                            <td><?php echo $rec['penalty_point']; ?></td>
                            <td><?php echo $rec['full_name']; ?></td>>
                            <td id="row<?= $rec['id'] ?>"> 
                               <?php
                                if (!empty($rec['active'])) {
                                    echo '<i class="fa fa-check-circle fa-2x text-success" id="btn' . $rec['id'] . '" onClick="changeStatus(this.id,0)"></i>';
                                } else {
                                    echo '<i class="fa fa-times-circle  fa-2x text-danger" id="btn' . $rec['id'] . '" onClick="changeStatus(this.id,1)"></i>';
                                }
                                ?> 
                             </td> 
                            <!-- <td><?php echo "active"; ?></td>> -->
                            <td>
                                <a class="btn btn-primary btn-xs" href='javascript:void(0)' title="Update Reason" id="update_cancel_reject" review_id="<?= $rec['id']; ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                            </td>
                            <td style="display: none;">

                                <?php 
                                    echo $rec['active'];
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--Modal start-->
    <div id="inventory_data" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action" style="display: inline-block;">Add Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="category_details" method="post" action="<?php echo base_url() . 'employee/inventory/save_reject_cancel'?>">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <!--<label class="col-md-4">Key</label>-->
                                    <div class="col-md-8">
                                        <input type="hidden" name="review_id" id="review_id" value=""/>
                                        <!--<input type="text" name="private_key" id="private_key" class="form-control" value="" required>-->
                                        <p class="text-danger" id="errorMessage"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <div class="col-md-8">
                                    <label for="service_id" class="col-md-4">Cancellation Reason *</label>
                                    <select name="cancellation" class="form-control" id="cancellation" required>
                                            <option selected disabled="">Please Select cancellation reason</option>
                                            <?php foreach ($reason as $value) { ?>
                                                <option value="<?php echo $value->id; ?>" > <?php echo $value->reason;
                                               
                                                ?></option>
                                                    <?php } ?>
                                        </select>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <label class="col-md-4">Review Reject*</label>
                                    <div class="col-md-8">
                                        <select name="review_reject" class="form-control" id="review_reject" required>
                                            <option selected disabled="">Please Select review reject reason</option>
                                            <?php foreach ($penalty as $value) { ?>
                                                <option value="<?php echo $value['id']; ?>" > <?php echo $value['criteria'];
                                               
                                                ?></option>
                                                    <?php } ?>
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
                            <input type="submit" name="Save" id="Save" class="btn btn-primary" onclick="return validate_review_form()">
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
    $('#inventory_list').dataTable({
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
                    title: 'Capacity-list',

                    exportOptions: {
                    columns: [0, 1, 2, 5]
                }
                }
                ]
                        
     });

    $(document).on("click", "#add_inventory", function () {
        // Display the Bootstrap modal
        $('#modal_title_action').html("Create Reason");
        $('#inventory_data').modal('show');
    });

    $(document).on("click", "#update_cancel_reject", function () {
        var id = $(this).attr('review_id');
        $.post('<?php echo base_url(); ?>employee/inventory/get_cancel_reject_data', {id: id}, function (response) {
            var data = JSON.parse(response);
            $('#cancellation').val(data.cancellation_reason);
             $('#review_reject').val(data.rejection_reason);
            $('#review_id').val(data.id);
            // $('#reason_of').val(data.reason_of);
            if (data.active == 1)
            {
                $('#active').prop("checked", true);
            }
            else {
                $('#active').prop("checked", false);
            }
            // Display the Bootstrap modal
            $('#modal_title_action').html("Update Reason");
            $('#inventory_data').modal('show');
        });
    });

    function changeStatus(btnId, status)
    {
        var statusFlag = "Deactivate";
        if (status == '1')
        {
            statusFlag = "Activate";
        }

        if (!confirm("Are you sure, You want to " + statusFlag + " Reason ?"))
        {
            return false;
        }

        var id = btnId.substr(3);
        // alert(id);
        $.post('<?php echo base_url(); ?>employee/inventory/update_inventory_status', {id: id, status: status}, function (data) {
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

    function validate_review_form()
    {
        var flag = 1;
        var criteria = $('#criteria').val();
        var review_id = $('#review_id').val();
        $("#errorMessage").html('');
        
        // Validate name only alphanumeric characters along with . and - are allowed
//        var reg_exp = '/^[0-9a-zA-Z]+$/';
//        if (!(name.match(reg_exp))) {
//            alert("Only alpha-numeric characters along with dot(.) and hyphen(-) are allowed for Category Name");
//            return false;
//        }

        $.ajax({
            url: '<?php echo base_url(); ?>employee/inventory/validate_review_form',
            async: false,
            method: 'post',
            data: {
                name: criteria,
                review_id: review_id
            },
            success: function (data) {
                data = $.trim(data);
                if (data == 'fail')
                {
                    $("#errorMessage").html('Name ' + criteria + ' has already been taken');
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
<script type="text/javascript">
    if ($("#cancellation").val() === "") {
    // ...
}

</script>
<style>
    .dataTables_filter, .dataTables_paginate
    {
        float: right;
    }
</style>