<?php if (isset($charges_data)) {
    if (!empty($charges_data)) { ?>

        <table class="table table-striped table-bordered" >
            <th>No.</th>
            <th>Category</th>
            <th>Brand</th>
            <th>City</th>
            <th>Partner Total</th>
            <th>CP Total</th>
            <th>Around Total</th>
            <th>Visible To Partner</th>
            <th>Visible To CP</th>
            <tbody>
                <?php $i = 1;
                foreach ($charges_data as $key => $value) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $value['category'] ?></td>
                        <td><?php echo $value['brand'] ?></td>
                        <td><?php echo $value['city'] ?></td>
                        <td><?php echo $value['partner_total'] ?></td>
                        <td><?php echo $value['cp_total'] ?></td>
                        <td><?php echo $value['around_total'] ?></td>
                        <td><?php if($value['visible_to_partner']){ ?>
                            <span class="label label-success">Yes</span>
                            <?php }else{ ?> 
                            <span class="label label-danger">No</span>
                            <?php } ?>
                        </td>
                        <td><?php if($value['visible_to_cp']){ ?>
                            <span class="label label-success">Yes</span>
                            <?php }else{ ?> 
                            <span class="label label-danger">No</span>
                            <?php } ?>
                        </td>
                    </tr>
            <?php $i++;
        } ?>

            </tbody>
        </table>


    <?php } else { ?> 
        <div class="alert alert-danger text-center" id="data-not-found" style="margin: 10px;"> No data found</div>
    <?php }
} ?> 