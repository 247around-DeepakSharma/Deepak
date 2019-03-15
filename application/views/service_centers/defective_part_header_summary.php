
<table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th >Defective Part to be Shipped</th>
            <td class="text-center"><?php echo (!empty($defective_part))? $defective_part[0]['count']:"0"; ?></td>
            <th >Max Age of Defective Part</th>
            <td class="text-center"> <?php echo (!empty($defective_part))? $defective_part[0]['max_sp_age']:"0"; ?></td>
            <th >Challan Approx Value</th>
            <td class="text-center"> <i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($defective_part))? $defective_part[0]['challan_value']:"0"; ?></td>
            
        </tr>
        <tr>
            <th >OOT Shipped Part</th>
            <td class="text-center"><?php echo (!empty($oot_shipped))? $oot_shipped[0]['count']:"0"; ?></td>
            <th >Max Age of OOT Shipped Part</th>
            <td class="text-center"> <?php echo (!empty($oot_shipped))? $oot_shipped[0]['max_sp_age']:"0"; ?></td>
            <th >OOT Challan Approx Value</th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($oot_shipped))? $oot_shipped[0]['challan_value']:"0"; ?></td>
            
        </tr>
        <tr>
            <th >Defective Part Shipped</th>
            <td class="text-center"><?php echo (!empty($shipped_parts))? $shipped_parts[0]['count']:"0";; ?></td>
            <th >Max Age of Shipped Defective Part</th>
            <td class="text-center"> <?php echo (!empty($shipped_parts))? $shipped_parts[0]['max_sp_age']:"0"; ?></td>
            <th >Challan Approx Value</th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($shipped_parts))? $shipped_parts[0]['challan_value']:"0"; ?></td>
            
        </tr>
    </thead>
</table>