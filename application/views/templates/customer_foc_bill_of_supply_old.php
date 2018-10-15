<?php
include 'customer_foc_bill_of_supply_variables.php';
?>
<html>
    <head>
        <title>Bill of Supply</title>
        <style type="text/css">
            td{
                border:solid 1px;
                padding: 1%;
            }
        </style>
    </head>
    <body>
        <table style="border:solid 2px; border-collapse: collapse; width: 960px; height: 75%; font-family: sans-serif; font-size: 100%; margin: auto;">
            <tr>
                <td style="text-align: center;border-bottom: hidden; border-right: 2px solid;" colspan="6" ><h1 style="margin-bottom: 0px;"><?php echo $meta['company_name']; ?></h1></td>
            </tr>
            <tr>
                <td colspan="1" align="left" style="padding-top: 0px !important;"><img style="width:30%" src="<?php echo base_url(); ?>images/logo.jpg"></td>
                <td colspan="4" align="center" style="border:hidden; padding-top: 0px !important;"><?php echo $meta['company_address']; ?><br><br><b>GSTIN :  <?php echo $meta['gst_number']; ?></td>
                <td  colspan="1" style="text-align: right; width: 20%; border-left: hidden; padding-top: 0px !important; border-right: 2px solid;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
            <tr style="height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="6" align="center" style="border-right: 2px solid;"><b>Bill of Supply</b>
            </tr>


            <tr><td colspan="3">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="3" style="border-right: 2px solid;">Booking ID:
                    <?php echo $meta['booking_id']; ?></td></tr>
            <tr>	
                <td colspan="2">Reverse Charge (Y/N):</td>
                <td colspan="1">N</td>
                <td colspan="3" style="border-right: 2px solid;">Invoice Date: <?php echo $meta['invoice_date']; ?></td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="6" style="border-right: 2px solid;"><b>Bill to Party</b></tr>


            <tr style="height: 20px" class="bold"><td colspan="6" style="border-right: 2px solid;">Name: <?php echo $meta['customer_name'] ?></td></tr>
            <tr style="height: 5%" class="bold"><td colspan="6" style="border-right: 2px solid;">Address: <?php echo $meta['customer_address']; ?></td></tr>
            <tr><td colspan="2">Phone:9555000247</td>
                <td colspan="3" width="30%">Place of Supply: <?php echo $meta['state']; ?></td>	
                <td style="border-right: 2px solid;">Code:<?php echo $meta['state_code']; ?></td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td colspan="2"><b>Product Description</td>
                <td style="padding: 1px"><b>HSN/SAC<br>Code</td>
                        <td><b>Qty</td>
                        <td><b>Rate</td>
                        <td style="border-right: 2px solid;"><b>Value of Supply</td>
            </tr>


            <?php
            foreach ($booking as $data) {
                ?>
                <tr style='text-align: center;'>
                    <td colspan='2'><?php echo $data['description']; ?></td>
                    <td><?php echo $data['hsn_code']; ?></td>
                    <td><?php echo $data['qty']; ?></td>
                    <td><?php echo $data['rate']; ?></td>
                    <td style='border-right: 2px solid;'><?php echo $data['total_amount']; ?></td>
                </tr>
                <?php
            }
            ?>		

            <tr><td colspan="6" style="border-right: 2px solid;"></td></tr>
            <tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style="border-right: hidden;width: 20%"></td>	
                <td style="border-left: hidden;"></td>
                <td><b><?php echo $meta['total_qty']; ?></td>	
                <td></td>
                <td style="border-right: 2px solid;"><b><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td colspan="6" align="center" style="background-color: rgb(211,211,211); font-weight: bold;border-right: 2px solid;"><b>Total Invoice amount in words</b></td>
            <tr>
                <td colspan="6" rowspan="1" style="padding: 2%; text-align: center; font-weight: bold; border-right: 2px solid;"><?php echo $meta['price_inword']; ?></td>
            </tr>
            <tr style="text-align: center;">
                <td rowspan="3" align="center">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="3" colspan="2"></td>
                <td colspan="4" style="background-color: rgb(211,211,211); font-weight: bold;">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="3" height="30%" style="border-right: 2px solid;"></td>
            </tr>

            <tr>
                <td colspan="3" style="border-right: 2px solid;"></td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin: auto;width: 960px">Book Appliance Service from Qualified Engineers on "247AROUND" App / On Phone - <b>9555000247</b> / On Website -<b> www.247around.com</b>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

