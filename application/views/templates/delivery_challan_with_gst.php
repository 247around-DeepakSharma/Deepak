<!DOCTYPE html>
<html>
    <head>
        <title>Delivery Challan</title>
        <style type="text/css">
            #table1{
                border:solid 2px;
                border-collapse: collapse;
                width: 960px;
                font-family: sans-serif;
                font-size: 100%;
                margin: auto;

                /*total cols=14*/
            }
            #table1 td{
                border: solid 1px;

                padding: 1%
            }
            #table1 .blank_row td{
                border-right: hidden;
            }

        </style>
    </head>
    <body>
        <table id="table1">
            <tr>
                <td colspan="2" style="border-right: hidden;"><img style="width: 25%;" src="https://aroundhomzapp.com/images/logo.jpg"></td>
                <td colspan="11" align="left"><h1>Delivery Challan</h1></td>
            </tr>
            <tr>
                <td colspan="13" style="border-bottom: hidden; text-align: center;"><b><?php echo $meta['sf_name']; ?></td>
            </tr>
            <tr>
                <td colspan="7" align="left" style="border-right: hidden;"><?php echo $meta['sf_address']; ?></td>
                <td style="border-right: hidden;"></td>
                <td colspan="5" align="right">GST: <?php echo $meta['sf_gst']; ?></td>
            </tr>
            <tr>
                <td colspan="7" align="center" style="border-bottom: hidden;"><b><?php echo $meta['partner_name']; ?></td>
                <td style="border-bottom: hidden;border-right: hidden;"></td>
                <td  colspan="5" align="left" style="border-bottom: hidden;"><b>Challan No: </b><?php echo $meta['sf_challan_no']; ?></td>
            </tr>
            <tr>
                <td  colspan="7" rowspan="2" align="left" style="border-bottom: hidden;">Address: <?php echo $meta['partner_address']; ?></td>
                <td style="border-bottom: hidden;border-right: hidden;"></td>
                <td colspan="5" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo $meta['partner_challan_no']; ?></td>
            </tr>
            <tr>
                <td style="border-bottom: hidden;border-right: hidden;"></td>
                <td colspan="5" align="left" style="border-bottom: hidden;"><b>Date: </b><?php echo $meta['date']; ?></td>
            </tr>
            <tr>
                <td  colspan="7" align="left"><b>GST: </b><?php echo $meta['partner_gst']; ?></td>
                <td style="border-right: hidden;"></td>
                <td colspan="5"></td>
            </tr>
            <tr class="blank_row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td style="border-right: solid 1px;"></td>
            </tr>
            <tr  style="text-align: center;">
                <td colspan="1" width="10%"><b>S No</b></td>
                <td colspan="6" width="40%"><b>Description</b></td>
                <td colspan="1" width="10%"><b>Qty</b></td>
                <td colspan="3" width="25%"><b>Booking ID</b></td>
                <td colspan="2" width="15%"><b>Value (Rs.)</b></td>
            </tr>
            <?php
            $i = 0;
            foreach ($booking as $data) {
            $i++;
            ?>
                <tr>	
                    <td colspan="1" align="center"><?php echo $i; ?></td>
		    <td colspan="6" align="center"><?php echo $data[spare_desc]; ?></td>
                    <td colspan="1" align="center"><?php echo $data[qty]; ?></td>
                    <td colspan="3" align="center"><?php echo $data[booking_id]; ?></td>
                    <td colspan="2" align="center"><?php echo $data[value]; ?></td>
		</tr>
            <?php
                $total_qty += $data["qty"];
                $total_value += $data["value"];
            }
            ?>
            <tr  style="font-weight: bold;">
                <td colspan="1" align="center" style="border-right: hidden;"></td>
                <td colspan="6" align="right">Total Qty</td>
                <td colspan="3" align="left"><?php echo $meta['total_qty']; ?></td>
                <td colspan="1" align="right">Total Amt</td>
                <td colspan="2" align="left"><?php echo $meta['total_value']; ?></td>
            </tr>
            <tr>
                <td style="text-align: right; padding: 2%;" colspan="13">For <?php echo $meta['sf_name']; ?></td>
            </tr>
            <tr><td colspan="13" style="border-bottom: hidden;border-right: hidden;border-left: hidden; text-align: center;"><small>This is a computer generated challan and does not need signature.</tr>
        </table>
    </body>
</html>