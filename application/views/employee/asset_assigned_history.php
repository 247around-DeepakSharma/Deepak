<table class = "table table-condensed table-bordered table-striped table-responsive">
                    
                    <tr>
                        <th>Sno</th>
                        <th>Employee name</th>
                        <th>Agent name</th>
                        <th>create_date</th>
                    </tr>
                    <?php
                if (!empty($data)) {
                    foreach ($data as $key => $row) {
                        ?>
                        <tr> 
                            <td><?php echo ($key +1) ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['agent_name']; ?></td>
                            <td><?php echo date('d-M-Y', strtotime($row['create_date'])); ?></td>
                        </tr>

    <?php }
} else { ?>
                    <tr>
                        <td>"no data found"</td>
                    </tr>
                    <?php
                }
                ?>
                    
</table

