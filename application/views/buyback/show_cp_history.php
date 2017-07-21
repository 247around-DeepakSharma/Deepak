<?php if(!empty($cp_history)) { ?>
<table class="table table-striped table-bordered table-responsive">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Action</th>
            <th>Action Performed By</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php $sn_no = 1; foreach ($cp_history as $value) { ?>
        <tr>
            <td><?php echo $sn_no?></td>
            <td><?php echo $value['action']?></td>
            <td><?php echo $value['agent_name']?></td>
            <td><?php echo date('d-m-Y' , strtotime($value['create_date']))?></td>
        </tr>
        <?php $sn_no++;} ?>
    </tbody>
</table>
<?php } else { ?>
<div class="text-center text-danger"><strong>No Data Found</strong></div>
<?php } ?>
