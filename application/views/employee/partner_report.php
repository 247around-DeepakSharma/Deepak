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
        <th colspan="7"><?php echo $month;?></th>
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
        if(!array_key_exists('0', $monthData)){$monthData['0']['count'] = 0;}
        echo $zeroDay =  $monthData['0']['count'];
        ?></td>
        <td><?php
        if(!array_key_exists('1', $monthData)){$monthData['1']['count'] = 0;}
        echo $firstDay = $zeroDay+$monthData['1']['count'];
        ?></td>
        <td><?php
        if(!array_key_exists('2', $monthData)){$monthData['2']['count'] = 0;}
        echo $secondDay = $firstDay+$monthData['2']['count'];
        ?></td>
        <td><?php
        if(!array_key_exists('3', $monthData)){$monthData['3']['count'] = 0;}
        echo $thirdDay = $secondDay+$monthData['3']['count'];
        ?></td>
        <td><?php
        if(!array_key_exists('4', $monthData)){$monthData['4']['count'] = 0;}
        echo $fourthDay  = $thirdDay+$monthData['4']['count'];
        ?></td>
        <td><?php
        if(!array_key_exists('5', $monthData)){$monthData['5']['count'] = 0;}
        echo $fifthDay  = $fourthDay+$monthData['5']['count'];
        ?></td>
    </tr>
     <td><?php echo number_format((float)(($zeroDay*100)/$monthData['completedCount']), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($firstDay*100)/$monthData['completedCount']), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($secondDay*100)/$monthData['completedCount']), 2, '.', '')." % ";?></td>
     <td><?php echo number_format((float)(($thirdDay*100)/$monthData['completedCount']), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($fourthDay*100)/$monthData['completedCount']), 2, '.', '')." % "; ?></td>
     <td><?php echo number_format((float)(($fifthDay*100)/$monthData['completedCount']), 2, '.', '')." % "; ?></td>
    <tr>
        
    </tr>
  
</table>
 
</body>
</html>