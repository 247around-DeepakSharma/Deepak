<table class="table  table-striped table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th> S.No. </th>
            <th> Status</th>
            <th>Remarks</th>
            <th> Agent </th>
            <th> Partner </th>
            <th> Date </th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($spare_history)) {
            $k = 1;
            foreach ($spare_history as $sh) {
                ?>
                <tr>
                    <td><?php echo $k; ?></td>
                    <td><?php echo $sh['action']; ?></td>
                    <td><?php echo $sh['remarks']; ?></td> 
                    <td><?php echo $sh['full_name']; ?></td>
                    <td><?php echo $sh['source']; ?></td>  
                    <td><?php echo date_format(date_create($sh['create_date']),'d-M-Y h:i:A'); ?></td>  
                </tr>
                <?php
                $k++;
            }
        } else { ?>
        <tr>
            <td colspan="6" style="text-align:center;">Data Not Found!</td>
        </tr>
       <?php }  ?>
    </tbody>
</table>