<?php
$vendor_name = "vendor_name";
$owner_name = "owner_name";
$address = "address";
$district = "district";
$state = "state";
$pincode = "pincode";
$primary_contact_phone_1 = "primary_contact_phone_1";
$primary_contact_phone_2 = "primary_contact_phone_2";
$booking_id = "booking_id";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Address Printout</title>
        <style type="text/css">
            table {
                font-family: sans-serif;
                width: 960px;
                margin: auto;
                border-collapse: collapse;
                text-align: center;
                font-weight: bold;
                border: solid 2px;
            }
            td{
                border: solid 1px;
                padding: 2%;
                font-size: 100%;
            }

        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="2" id="top_row" style="width: 12%;text-align: left; border-right: hidden; padding: 5px;"><img style="width: 70%" src="https://aroundhomzapp.com/images/logo.jpg"></td>
                <td colspan="4" align="center" style="padding: 0%"><h1>247AROUND SERVICE CENTER</h1></td>
            </tr>
            <tr>
                <td colspan="6"><?php echo $meta['vendor_name']; ?>
                    <br>
                    C/O - <?php echo $meta['owner_name']; ?>
                </td>
            </tr>
            <tr><td colspan="6">Address - <?php echo $meta['address']; ?>, <?php echo $meta['district']; ?>,<br>
                    <?php echo $meta['state']; ?>, <?php echo $meta['pincode']; ?>	
                </td>
            </tr>
            <tr><td colspan="6">Phone -  <?php echo $meta['primary_contact_phone_1']; ?>, <?php echo $meta['primary_contact_phone_2']; ?><br>
                    Job No -  <?php echo $meta['booking_id']; ?>
               </td>
            </tr>
        </table>

    </body>
</html>