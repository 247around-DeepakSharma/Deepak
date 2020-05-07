<?php if(!empty($stock_details)) { ?> 
<table class="table table-bordered table-striped table-responsive">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Part Number</th>
                <th>Part Description</th>
                <th>Part Name</th>
                <th>Current Stocks</th>
                <th>View Ledger</th>
                
            </tr>
        </thead>
        <tbody>
            <?php $sn = 1;foreach($stock_details as $value){?> 
            <tr>
                <td><?php echo $sn;?></td>
                <td><?php echo $value->part_number;?></td>
                <td><?php echo $value->description;?></td>
                <td><?php echo $value->part_name;?></td>
                <td><?php echo $value->stock;?></td>
                <td>
                    <a href="<?php echo base_url();?>employee/inventory/show_inventory_ledger_list/0/<?php echo ($value->entity_type."/".$value->entity_id."/".$value->inventory_id) ?>" target="_blank"><i class="fa fa-eye"></i></a>
                </td>
                
            </tr>
            <?php $sn++;} ?>
        </tbody>
    </table>
<?php } else{ ?> 
<div class="alert alert-danger"> No Details Found</div>
<?php }?>