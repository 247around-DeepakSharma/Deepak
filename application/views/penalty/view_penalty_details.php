<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">

            <h1>Penalty Details
                <div class="pull-right" style="margin:0px 10px 20px 0px;">
                    <a href="<?php echo base_url(); ?>penalty/get_penalty_detail_form">
                        <input class="btn btn-sm btn-primary" type="Button" value="Add Detail"></a>
                    <!--<a href="<?php echo base_url(); ?>employee/partner/download_partner_summary_details" class="btn btn-sm btn-success">Download Detail List</a>-->
        <!--            <a href="<?php echo base_url(); ?>employee/partner/upload_partner_brand_logo"><input class="btn btn-primary" type="Button" value="Upload Partner Brand Logo" style="margin-left:10px;"></a>-->
                </div>
            </h1>
        </div> 
        <div class="col-md-12">
    <table id="book-table" class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Criteria</th>
                <th>Escalation</th>
                <th>Penalty Amount</th>
                <th>CAP Amount</th>
                <th>Update</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($penalty_details)) {
                foreach ($penalty_details as $penalty_detail) {
                    ?>
                    <tr>
                        <td><?= $penalty_detail['criteria']; ?></td>
                        <td>
                            <?php 
                            if(!empty($penalty_detail['escalation_id'])) {
                                echo $this->reusable_model->get_search_result_data('vendor_escalation_policy', '*', ['id' => $penalty_detail['escalation_id']], NULL, NULL, NULL, NULL, NULL)[0]['escalation_reason'];
                            }
                            
                             ?></td>
                        <td><?= $penalty_detail['penalty_amount']; ?></td>
                        <td><?= $penalty_detail['cap_amount']; ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary " style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url() ?>penalty/get_penalty_detail_form/<?php echo $penalty_detail['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        </td>
                         <td><?php if ($penalty_detail['active'] == 1) { ?>
                                <a class="btn btn-sm btn-danger" href="<?php echo base_url() ?>penalty/edit_penalty_detail/<?php echo $penalty_detail['id']; ?>?action=deactivate" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this penalty detail?')"><i class="fa fa-times" aria-hidden="true"></i></a>       
                            <?php } else { ?>
                                <a class="btn btn-sm btn-success" href="<?php echo base_url() ?>penalty/edit_penalty_detail/<?php echo $penalty_detail['id']; ?>?action=activate" title="Activate" onclick="return confirm('Are you sure you want to activate this penalty detail?')"><i class="fa fa-check" aria-hidden="true"></i></a>                
                            <?php } ?>
                        </td>
                        
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
            </div>
        </div>
</div>
<style>
    #book-table_filter, .pagination{
        float:right;
    }
    #book-table_filter {
        display: none;
    }
    .dt-buttons {
        float: right !important;
        margin-bottom: 5px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $('#book-table').DataTable({
        dom: 'Bfrtip',
        "pageLength": 50,
        buttons: [
            { 
                extend: 'csv',
                text: 'Download List'
             }
        ]
    } );
    });
</script>