<?php
foreach ($calls as $partner => $months) {
    $sum = 0;
    ?>
    <tr data-partner="<?php echo $partner; ?>">
        <td style="vertical-align: middle;font-size: 15px;width: max-content;display: inline-block;border: 0;vertical-align: text-top;"><b><?php echo (isset($calls[$partner]['public_name'])) ? $calls[$partner]['public_name'] : ''; ?></b></td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
            <td style="vertical-align: middle;"><?php
                $mtd = 0;

                if (isset($months[$i]['call_count'])) {
                    if ($i == 1) {
                        $mtd = '';
                    } else {
                        if($year == date('Y') && $i == date('m')){
                            $mtd = 0;
                        }else if ($months[$i]['call_count'] == 0 && $months[$i - 1]['call_count'] == 0) {
                            $mtd = 0;
                        }else if ($months[$i]['call_count'] > 0 && $months[$i - 1]['call_count'] == 0) {
                            $mtd = 100;
                        } else {
                            $mtd = (($months[$i]['call_count'] - $months[$i - 1]['call_count']) / $months[$i - 1]['call_count']) * 100;
                        }
                    }
                    $mtd = number_format((float)$mtd,2);
                    if($mtd < 0){
                        echo '<div style="color:white;width: max-content;background-color:red;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }else if($mtd > 0){
                        echo '<div style="color:white;width: max-content;background-color:green;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }else{
                        echo '<div style="color:white;width: max-content;background-color:#aaa;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }
                    
                    $sum = $sum + $months[$i]['call_count'];
                } else {
                    $months[$i]['call_count'] = 0;
                    if ($i == 1) {
                        $mtd = 0;
                    } else {
                        if($year == date('Y') && $i == date('m')){
                            $mtd = 0;
                        }else if ($months[$i]['call_count'] == 0 && $months[$i - 1]['call_count'] == 0) {
                            $mtd = 0;
                        }else if ($months[$i]['call_count'] > 0 && $months[$i - 1]['call_count'] == 0) {
                            $mtd = 100;
                        } else {
                            $mtd = (($months[$i]['call_count'] - $months[$i - 1]['call_count']) / $months[$i - 1]['call_count']) * 100;
                        }
                    }
                    $mtd = number_format((float)$mtd,2);
                    if($mtd < 0){
                        echo '<div style="color:white;width: max-content;background-color:red;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }else if($mtd > 0){
                        echo '<div style="color:white;width: max-content;background-color:green;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }else{
                        echo '<div style="color:white;width: max-content;background-color:#aaa;padding: 8px;text-align: center;">'.$months[$i]['call_count'] . ' ('.$mtd."%)</div>";
                    }
                }
                ?></td>

        <?php } ?>  
            <td style="vertical-align: middle;"><strong><?php echo $sum; ?></strong></td>

    </tr>
<?php } ?>