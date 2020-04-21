<?php if (isset($charges_data)) {
    if (!empty($charges_data)) { ?>

        <table class="table table-striped table-bordered" id="bb_price_list">
            <th>No.</th>
            <th>Category</th>
            <th>Brand</th>
            <th>City</th>
            <?php if($hide_field) { ?> <th>Partner Total</th> <?php } ?>
            <th>CP Total</th>
            <?php if($hide_field) { ?> <th>Around Total</th> <?php } ?>
            <?php if($hide_field) { ?> <th>Visible To Partner</th> <?php } ?>
            <?php if($hide_field) { ?> <th>Partner CP</th> <?php } ?>
            <tbody>
                <?php $i = 1;
                foreach ($charges_data as $key => $value) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $value['category'] ?></td>
                        <td><?php echo $value['brand'] ?></td>
                        <td><?php echo $value['city'] ?></td>
                        <?php if($hide_field) { ?> <td><?php echo $value['partner_total'] ?></td> <?php } ?>
                        <td><?php echo $value['cp_total'] ?></td>
                        <?php if($hide_field) { ?> <td><?php echo $value['around_total'] ?></td><?php } ?>
                        <?php if($hide_field) { ?> <td><?php if($value['visible_to_partner']){ ?>
                            <span class="label label-success">Yes</span>
                            <?php }else{ ?> 
                            <span class="label label-danger">No</span>
                            <?php } ?>
                        </td> <?php } ?>
                        <?php if($hide_field) { ?> <td><?php if($value['visible_to_cp']){ ?>
                            <span class="label label-success">Yes</span>
                            <?php }else{ ?> 
                            <span class="label label-danger">No</span>
                            <?php } ?>
                        </td><?php } ?>
                    </tr>
            <?php $i++;
        } ?>

            </tbody>
        </table>


    <?php } else { ?> 
        <div class="alert alert-danger text-center" id="data-not-found" style="margin: 10px;"> No data found</div>
    <?php }
} ?> 