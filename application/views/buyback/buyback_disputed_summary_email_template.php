
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
            <div style="padding: 5px;"><p style="padding: 20px 0px 3px;font: normal 14px/19px Arial;"><b>Note</b><br>
                1) Without Invoiced Without Reimbursement  - 247Around Amount deduct for the orders<br>
                2) Invoiced to CP on Claimed Prices And Without Reimbursement  -  Amount Which we will get after deduction of basic cost<br>
                3) Without Invoiced Without Reimbursement  - 247Around Amount deduct for the orders<br>
                4) 1st 2 tables Contains Data From last 12 month till last to last month </p>
            </div>
            <div style="padding: 5px;">
                <?php
                foreach($data as $keysArray){
                ?>
                <h2 style="padding: 7px 0px;margin: 0px;font: bold 20px/25px Arial;"><?php echo $keysArray['Title']; ?></h2>
                <table border="1" cellspacing="0" cellpadding="1px" style="width:100%; table-layout: fixed; ">
                    <tr>
                        <th style="font: bold 14px/19px Arial;text-align: left;">status</th>
                        <th style="font: bold 14px/19px Arial;text-align: left;">Amount</th>
                        <th style="font: bold 14px/19px Arial;text-align: left;">Count</th>
                    </tr>
                    <?php foreach($keysArray['data'] as $keys => $values) { ?>
                        <tr>
                            <td style="font: normal 14px/19px Arial;text-align: left;"><?php echo $values ->status?></td>
                            <td style="font: normal 14px/19px Arial;text-align: left;"><?php echo $values ->amount?></td>
                            <td style="font: normal 14px/19px Arial;text-align: left;"><?php echo $values ->count?></td>
                        </tr>
                    <?php } ?>
                </table>
                <?php
                }
                ?>
            </div>
        </div>
    </body>
</html>


