<html>
    <head>
        <title></title>
    </head>
    <body>

        <div bgcolor="#ffffff; " style="border: 1px solid #CCCCCC; width:980px; ">
            <div style="background-color: #2C9D9C;">
                <center>
                    <img src="https://aroundhomzapp.com/images/logo.jpg" alt="" style="border:0" width="" height="" class="CToWUd">
                </center>
            </div>
            <?php
            if(isset($jeevesDate)){
            ?>
            <div style="float:left; padding: 15px; padding"><p><b>Dear Partner</b></p>
                <p style="color:#515151;">Please find Service Status Sheet Download link for leads shared in last One Month and Pending Bookings for all time thanks.</p>
              <p><b>TAT Calculation Using Jeeves Reference Date:</b></p>
            </div>
            <div style="padding: 15px;">
                <table border="1" cellspacing="0" cellpadding="1px" style="width:100%; table-layout: fixed; ">
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
        if(!array_key_exists('day_0', $jeevesDate)){$jeevesDate['day_0'] = 0;}
        echo $zeroDay =  $jeevesDate['day_0'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_1', $jeevesDate)){$jeevesDate['day_1'] = 0;}
        echo $firstDay = $zeroDay+$jeevesDate['day_1'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_2', $jeevesDate)){$jeevesDate['day_2'] = 0;}
        echo $secondDay = $firstDay+$jeevesDate['day_2'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_3', $jeevesDate)){$jeevesDate['day_3'] = 0;}
        echo $thirdDay = $secondDay+$jeevesDate['day_3'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_4', $jeevesDate)){$jeevesDate['day_4'] = 0;}
        echo $fourthDay  = $thirdDay+$jeevesDate['day_4'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_5', $jeevesDate)){$jeevesDate['day_5'] = 0;}
        echo $fifthDay  = $fourthDay+$jeevesDate['day_5'];
        ?></td>
    </tr>
    <?php
    $allCompleted = array_sum(array_values($jeevesDate));
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
            </div>
            <?php
            }
            ?>
<div style="float:left; padding: 15px; padding">
              <p><b>TAT Calculation Using 247Around Booking Date:</b></p>
            </div>
      <div style="padding: 15px;">
             <table border="1" cellspacing="0" cellpadding="1px" style="width:100%; table-layout: fixed; ">
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
        if(!array_key_exists('day_0', $aroundDate)){$aroundDate['day_0'] = 0;}
        echo $zeroDay =  $aroundDate['day_0'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_1', $aroundDate)){$aroundDate['day_1'] = 0;}
        echo $firstDay = $zeroDay+$aroundDate['day_1'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_2', $aroundDate)){$aroundDate['day_2'] = 0;}
        echo $secondDay = $firstDay+$aroundDate['day_2'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_3', $aroundDate)){$aroundDate['day_3'] = 0;}
        echo $thirdDay = $secondDay+$aroundDate['day_3'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_4', $aroundDate)){$aroundDate['day_4'] = 0;}
        echo $fourthDay  = $thirdDay+$aroundDate['day_4'];
        ?></td>
        <td><?php
        if(!array_key_exists('day_5', $aroundDate)){$aroundDate['day_5'] = 0;}
        echo $fifthDay  = $fourthDay+$aroundDate['day_5'];
        ?></td>
    </tr>
    <?php
    $allCompleted = array_sum(array_values($aroundDate));
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
            </div>
             <div style="float:left; padding: 15px;"><p><b>Best regards, <br>247around Team</b></p>

            </div>
        </div>

    </body>
</html>


