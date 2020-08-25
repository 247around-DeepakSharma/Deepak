<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Vendor Inventory Details</center></div>
        <div class="panel-body">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Vendor Name</th>
<!--                        <th>19 to 24 inch</th>-->
                        <th>Less Than 32 Inch</th>
                        <th>32 Inch & Above</th>
<!--                        <th>&gt;43 inch</th>-->
                        <th>Remarks</th>
                        <th>Increment/Decrement</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($final_array as $key => $value) { ?>		
                        <tr>
                            <td><?php echo ($key + 1) . '.' ?></td>
                            <td><?php echo $value['sc_name'] ?></td>
    <!--                            <td><?php //echo $value['19_24_current_count'] ?></td>-->
                            <td>
                                <?php
                                if (!empty($value['19_24_current_count']) && is_numeric($value['19_24_current_count'])) {
                                    $one_nine_two_four = $value['19_24_current_count'];
                                } else {
                                    $one_nine_two_four = 0;
                                }

                                if (!empty($value['26_32_current_count']) && is_numeric($value['26_32_current_count'])) {
                                    $two_six_three_two = $value['26_32_current_count'];
                                } else {
                                    $two_six_three_two = 0;
                                }
                                $total = ($one_nine_two_four + $two_six_three_two);
                                echo $total;
                                ?>
                            </td>
                            <td>
                                <?php 
                                  if (is_numeric($value['36_42_current_count'])) {
                                        $three_six_four_two = $value['36_42_current_count'];
                                    } else {
                                        $three_six_four_two = 0;
                                    }

                                    if (is_numeric($value['43_current_count'])) {
                                        $four_three = $value['43_current_count'];
                                    } else {
                                        $four_three = 0;
                                    }
                                    $total_value = ($three_six_four_two + $four_three);
                                    echo $total_value; 
                                ?>
                            </td>
    <!--                            <td><?php //echo $value['43_current_count'] ?></td>-->
                            <td><?php echo $value['remarks'] ?></td>
                            <td><?php echo $value['increment/decrement'] ?></td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>