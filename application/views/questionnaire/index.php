<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Questionnaire List
                    <a class="btn btn-primary btn-md pull-right" href='<?php echo base_url(); ?>employee/questionnaire/add_question' id="add_question" title="Add Question"><i class="glyphicon glyphicon-plus"></i></a>
                </h1>                        
            </div>
        </div>
        <?php if ($this->session->userdata('failed')) { ?>
            <div class="alert alert-error alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php echo $this->session->userdata('failed') ?></strong>
            </div>
        <?php } ?>

        <?php if ($this->session->userdata('success')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php echo $this->session->userdata('success') ?></strong>
            </div>
        <?php } ?>

        <?php       
            $this->session->unset_userdata('success');
            $this->session->unset_userdata('failed');          
        ?>
        <div class="x_panel" style="height: auto;">
            <table id="question_list" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Panel</th>
                        <th>Form</th>
                        <th>Product</th>
                        <th>Request Type</th>
                        <th>Question</th>  
                        <th style="display: none;">Question</th>
                        <th style="display: none;">Options</th>
                        <th style="display: none;">Active</th>
                        <th class="no-sort">Active</th> 
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $rec) {
                        $count = $key + 1;
                        ?>      
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td>
                                <?php 
                                    if($rec->panel == 1) {
                                        echo "Admin";                                         
                                    } else { 
                                        echo "Partner";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if($rec->form == 1) {
                                        echo "Booking Cancellation";                                         
                                    } else { 
                                        echo "Booking Completion";
                                    }
                                ?>
                            </td>
                            <td><?php echo $rec->services; ?></td>
                            <td><?php echo $rec->service_category; ?></td>                            
                            <td>
                                <?php 
                                    echo $rec->question; 
                                    if(!empty($rec->answers)){
                                        echo "<ul><li>".str_replace(",", "</li><li>", $rec->answers)."</li></ul>";
                                    }
                                ?>
                            </td>
                            <td style="display: none;"><?php echo $rec->question; ?></td>
                            <td style="display: none;"><?php echo $rec->answers; ?></td>
                            <td style="display: none;">
                                <?php echo(empty($rec->active) ? "No" : "Yes") ;  ?>
                            </td>
                            <td>
                                <img id="loader_gif_<?= $rec->q_id ?>" src="" style="display: none;">
                                <div id="status<?= $rec->q_id ?>">
                                    <?php
                                    if (!empty($rec->active)) {
                                        echo '<i class="fa fa-check-circle fa-2x text-success" id="btn' . $rec->q_id . '" onClick="changeStatus(this.id,0)"></i>';
                                    } else {
                                        echo '<i class="fa fa-times-circle  fa-2x text-danger" id="btn' . $rec->q_id . '" onClick="changeStatus(this.id,1)"></i>';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-xs" href='<?php echo base_url(); ?>employee/questionnaire/add_question/<?= $rec->q_id ?>' title="Update Question" id="update_question"><i class="glyphicon glyphicon-pencil"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>    
</div>

<script>
    $('#question_list').dataTable({
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
                title: 'Question-list',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6, 7, 8]
                }
            }
        ]                        
    });    

    function changeStatus(btnId, status)
    {
        var statusFlag = "Drop";
        if (status == '1')
        {
            statusFlag = "Add";
        }

        if (!confirm("Are you sure, You want to " + statusFlag + " Question ?"))
        {
            return false;
        }

        var q_id = btnId.substr(3);
        
        $('#status'+q_id).html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:30px'>");
        var postdata = {q_id: q_id, status: status};
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/questionnaire/update_status',
            data: postdata,
            success: function (data) {
                data = $.trim(data);
                if (data == '1')
                {
                    if (status == '1')
                    {
                        $("#status" + q_id).html("<i class='fa fa-check-circle fa-2x text-success' id='btn" + q_id + "' onClick='changeStatus(this.id,0)'></i>");

                    }
                    else
                    {
                        $("#status" + q_id).html("<i class='fa fa-times-circle fa-2x text-danger' id='btn" + q_id + "' onClick='changeStatus(this.id,1)'></i>");
                    }
                }
                else{
                    alert("Data can not be saved! Please Try Again.")
                }
            }
        });        
    }


</script>

<style>
    .dataTables_filter, .dataTables_paginate
    {
        float: right;
    }
</style>