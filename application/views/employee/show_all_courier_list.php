<style>
    #courier_list_table_filter{
        text-align: right;
    }
    .dataTables_paginate{
        text-align: right;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="row">
            <h2>Courier Supported by Tracking More</h2>
        </div>
        <hr>
        <div class="row">
            <div class="courier_list">
                <table id="courier_list_table" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Courier Code</th>
                            <th>Courier Help Line</th>
                            <th>Courier Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($courier_data['data'])) {  $sn =1; foreach($courier_data['data'] as $value) { ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><a href="<?php echo $value['homepage']; ?>" target="_blank"><?php echo $value['name']; ?></a></td>
                            <td><?php echo $value['code']; ?></td>
                            <td><?php echo $value['phone']; ?></td>
                            <td><?php echo $value['type']; ?></td>
                        </tr>
                        <?php $sn++; }} ?> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('#courier_list_table').dataTable({
        pageLength:50
    });
</script>