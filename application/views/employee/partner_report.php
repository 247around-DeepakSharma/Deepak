<html>
<head>
<style>
table {
}
tr{
    border: 1px solid;
}
td{
    border-right: 1px solid;
    text-align: center;
    padding: 6px 0px;
}
p{
    font-family: sans-serif;
    font-size: 13px;
}
#top_row{
    border-right: none;
    text-align: left;
}
</style>
</head>
<body>
<table style="width:100%;border-collapse: collapse;">
    <tr>
        <th colspan="7"><?php echo date("F");?></th>
    </tr> 
    <tr>
        <th>D0</th>
        <th>D1</th>
        <th>D2</th>
        <th>D3</th>
        <th>D4</th>
        <th>D5+</th>
    </tr> 
    <tr>
        <td><?php
        if(!array_key_exists('day_0', $dynamicParams)){$dynamicParams['day_0'] = 0;}
        echo $zeroDay =  $dynamicParams['day_0'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_1', $dynamicParams)){$dynamicParams['day_1'] = 0;}
        echo $firstDay = $zeroDay+$dynamicParams['day_1'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_2', $dynamicParams)){$dynamicParams['day_2'] = 0;}
        echo $secondDay = $firstDay+$dynamicParams['day_2'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_3', $dynamicParams)){$dynamicParams['day_3'] = 0;}
        echo $thirdDay = $secondDay+$dynamicParams['day_3'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_4', $dynamicParams)){$dynamicParams['day_4'] = 0;}
        echo $fourthDay  = $thirdDay+$dynamicParams['day_4'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_5', $dynamicParams)){$dynamicParams['day_5'] = 0;}
        echo $fifthDay  = $fourthDay+$dynamicParams['day_5'];
        ?></td>
    </tr>
    <?php
    $allCompleted = array_sum(array_values($dynamicParams));
    if($allCompleted){
    ?>
     <td><?php echo number_format((float)(($zeroDay*100)/$allCompleted), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($firstDay*100)/$allCompleted), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($secondDay*100)/$allCompleted), 2, '.', '')." % ";?></td>
     <td><?php echo number_format((float)(($thirdDay*100)/$allCompleted), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($fourthDay*100)/$allCompleted), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($fifthDay*100)/$allCompleted), 2, '.', '')." % "; ?></td>
    <?php } 
    else{
        ?>
     <td> 0% </td>
     <td> 0% </td>
     <td> 0% </td>
     <td> 0% </td>
     <td> 0% </td>
     <td> 0% </td>
        <?php
    }
?>
    <tr>
        
    </tr>
  
</table>
 
</body>
</html>