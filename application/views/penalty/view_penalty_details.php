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
                <th>S.No.</th>
                <th>Escalation Reason</th>
                <th>Penalty Amount</th>
                <th>CAP Amount</th>
                <th>Update</th>
                <th>Active/Deactive</th>
                <th hidden>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($penalty_details)) {
                foreach ($penalty_details as $key => $penalty_detail) {
                    ?>
                    <tr>

                        <td width="5%"><?php echo ++$key; ?></td>
                        <td width="45%"><?php echo $penalty_detail['escalation_reason']; ?></td>
                        <td width="15%"><?php if(!empty($penalty_detail['penalty_amount'])) { echo $penalty_detail['penalty_amount'];} else { echo '-'; } ?></td>
                        <td width="15%"><?php if(!empty($penalty_detail['cap_amount'])) { echo $penalty_detail['cap_amount'];} else { echo '-'; } ?></td>
                        <td width="10%">
                            <a class="btn btn-sm btn-primary " style="background-color:#2C9D9C; border-color: #2C9D9C;" href="<?php echo base_url() ?>penalty/get_penalty_detail_form/<?php echo $penalty_detail['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        </td>
                         <td width="10%"><?php if ($penalty_detail['escalation_policy_active'] == 1) { ?>
                                <a class="btn btn-sm btn-danger" href="<?php echo base_url() ?>penalty/edit_penalty_detail/<?php echo $penalty_detail['id']; ?>?action=deactivate&escalation_id=<?php echo $penalty_detail['escalation_id']; ?>" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this penalty detail?')"><i class="fa fa-times" aria-hidden="true"></i></a>       
                            <?php } else { ?>
                                <a class="btn btn-sm btn-success" href="<?php echo base_url() ?>penalty/edit_penalty_detail/<?php echo $penalty_detail['id']; ?>?action=activate&escalation_id=<?php echo $penalty_detail['escalation_id']; ?>" title="Activate" onclick="return confirm('Are you sure you want to activate this penalty detail?')"><i class="fa fa-check" aria-hidden="true"></i></a>                
                            <?php } ?>
                        </td>
                        <td hidden><?php if ($penalty_detail['escalation_policy_active'] == 1) { echo 'Yes';} else { echo 'No';} ?></td>
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
        "pageLength": 20,
        'columnDefs': [ {
            'targets': [4,5], /* column index */
            'orderable': false, /* true or false */
         }],
        buttons: [
            { 
                extend: 'csv',
                title: 'Penalty_details',
                text: 'Download List',
                exportOptions: {
                    columns: [1,2,3,6]
                }
            }
        ]
    } );
    });
</script>