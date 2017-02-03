<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Vendor Inventory Details</center></div>
        <div class="panel-body">

            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Vendor Name</th>
                        <th>19 to 24 inch</th>
                        <th>26 to 32 inch</th>
                        <th>36 to 42 inch</th>
                        <th>&gt;43 inch</th>
                        <th>Remarks</th>
                        <th>Increment/Decrement</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($final_array as $key=>$value){ ?>		
                        <tr>
                            <td><?php echo ($key+1).'.'?></td>
                            <td><?php echo $value['sc_name']?></td>
                            <td><?php echo $value['19_24_current_count']?></td>
                            <td><?php echo $value['26_32_current_count']?></td>
                            <td><?php echo $value['36_42_current_count']?></td>
                            <td><?php echo $value['43_current_count']?></td>
                            <td><?php echo $value['remarks']?></td>
                            <td><?php echo $value['increment/decrement']?></td>

                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>