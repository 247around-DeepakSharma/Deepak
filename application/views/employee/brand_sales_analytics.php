<?php
foreach ($calls as $partner => $months) {
    $sum = 0;
    ?>
    <tr data-partner="<?php echo $partner; ?>">
        <td><?php echo (isset($calls[$partner]['public_name'])) ? $calls[$partner]['public_name'] : ''; ?></td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
            <td><?php
                $mtd = 0;

                if (isset($months[$i]['call_count'])) {
                    if ($i == 1) {
                        $mtd = '';
                    } else {
                        if ($months[$i - 1]['call_count'] == 0) {
                            $mtd = 0;
                        } else {
                            $mtd = (($months[$i]['call_count'] - $months[$i - 1]['call_count']) / $months[$i - 1]['call_count']) * 100;
                        }
                    }
                    $mtd = number_format((float)$mtd,2);
                    if($mtd <= 0){
                        echo '<span style="color:red">'.$months[$i]['call_count'] . " (".$mtd."%) </span>";
                    }else if($mtd > 0){
                        echo '<span style="color:green">'.$months[$i]['call_count'] . " (".$mtd."%)</span>";
                    }
                    
                    $sum = $sum + $months[$i]['call_count'];
                } else {
                    $months[$i]['call_count'] = 0;
                    if ($i == 1) {
                        $mtd = 0;
                    } else {
                        if ($months[$i - 1]['call_count'] == 0) {
                            $mtd = 0;
                        } else {
                            $mtd = (($months[$i]['call_count'] - $months[$i - 1]['call_count']) / $months[$i - 1]['call_count']) * 100;
                        }
                    }
                    $mtd = number_format((float)$mtd,2);
                    if($mtd <= 0){
                        echo '<span style="color:red">'.$months[$i]['call_count'] . " (".$mtd."%) </span>";
                    }else if($mtd > 0){
                        echo '<span style="color:green">'.$months[$i]['call_count'] . " (".$mtd."%)</span>";
                    }
                }
                ?></td>

        <?php } ?>  
        <td><?php echo $sum; ?></td>

    </tr>
<?php } ?>