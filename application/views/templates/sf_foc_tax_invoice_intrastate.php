<html>
    <head>
        <title>SF FOC Invoice Intrastate</title>
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
                <td style="text-align: center;border-bottom: hidden; padding-bottom: 0px; border-right: 2px solid" colspan="10" ><h1 style="margin:0px"><?php echo $meta['company_name']; ?></h1></td></tr>
            <tr style="">
                <td colspan="8" align="center" style="border-right:hidden; padding-left: 20%;"><?php echo $meta['company_address']; ?><br>Phone: <?php echo $meta['owner_phone_1']; ?><br><b>GSTIN :  <?php echo $meta['gst_number']; ?><b></td>
                <td  colspan="2" style="text-align: center; width: 20%; border-left: hidden; border-right: 2px solid"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
<!--            <tr style="border-bottom: hidden;"><td colspan="10" align="center">Phone: <?php //echo "$owner_phone_1"; ?></td></tr>-->
    <!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
            -->	
<!--            <tr style="text-align: center;" ><td colspan="10"><b>GSTIN :  <?php //echo "$gst_number"; ?></td></tr>-->


            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="10" align="center" style="border-right: 2px solid; height: 50px; font-size: 40px;"><b>Tax Invoice</b></tr>


            <tr><td colspan="4">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="6" style="border-right: 2px solid">Period: <?php echo $meta['sd']; ?> - <?php echo $meta['ed']; ?>
            <tr><td colspan="4">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="5" >Reverse Charge (Y/N):</td>
                <td colspan="1" style="border-right: 2px solid">N</td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="10" align="center" style="border-right: 2px solid"><b>Bill to Party</b></tr>


            <tr style="height: 20px"><td colspan="10" style="border-right: 2px solid">Name: Blackmelon Advance Technology Co. Pvt. Ltd.</td></tr>
            <tr style="height: 5%; "><td colspan="10" style="padding-top: 2%; padding-bottom: 2%; border-right: 2px solid">Address: A-1/7, F/F A BLOCK, KRISHNA NAGAR, DELHI, 110051</td></tr>
            <tr><td colspan="2">GSTIN: 07AAFCB1281J1ZQ</td>
                <td colspan="6">Place of Supply: Delhi</td>	
                <td colspan="2" style="border-right: 2px solid">Code: 07</td></tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td rowspan="2" width="30%"><b>Product Description</td>
                <td rowspan="2" width="5%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td rowspan="2" width="5%"><b>Qty</td>
                <td rowspan="2" width="5%"><b>Rate</td>
                <td rowspan="2" width="10%"><b>Taxable value</td>
                <td colspan="2" width="15%"><b>CGST</td>
                <td colspan="2" width="15%"><b>SGST</td>
                <td rowspan="2" width="10%" style="border-right: 2px solid"><b>Total</td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)"><td style="padding: 1px"><b>Rate</td>
                <td><b>Amount</td>
                <td style="padding: 1px"><b>Rate</td>
                <td><b>Amount</td>
            </tr>


            <?php
            foreach ($booking as $data) {
            ?>
                <tr style='text-align: center;'>
                    <td width="30%"><?php echo $data['description']; ?></td>
                    <td width="5%"><?php echo $data['hsn_code']; ?></td>
                    <td width="5%"><?php echo $data['qty']; ?></td>
                    <td width="5%"><?php echo $data['rate']; ?></td>
                    <td width="10%"><?php echo $data['taxable_value']; ?></td>
                    <td><?php echo $data['cgst_rate']; ?></td>
                    <td><?php echo $data['cgst_tax_amount']; ?></td>
                    <td><?php echo $data['sgst_rate']; ?></td>
                    <td><?php echo $data['sgst_tax_amount']; ?></td>
                    <td width="10%" style="border-right: 2px solid"><?php echo $data['total_amount']; ?></td>
                </tr>
            <?php
            }
            ?>		


            <tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style=""></td>	
                <td><?php echo $meta['total_qty']; ?></td>	
                <td style=""></td>	
                <td><?php echo $meta['total_taxable_value']; ?></td>
                <td></td>
                <td><?php echo $meta['cgst_total_tax_amount']; ?></td>
                <td></td>
                <td><?php echo $meta['sgst_total_tax_amount']; ?></td>
                <td style="border-right: 2px solid"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr >
                <td width="60%" colspan="4" align="center"><b>Total Invoice amount in words</b></td>
                <td width="30%" colspan="5" align="left"><b>Total Amt before Tax</td>
                <td width="10%" colspan="3" align="left"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr>
                <td colspan="4" rowspan="3" style="padding: 2%; text-align: center;"><?php echo $meta['price_inword']; ?></td>
                <td colspan="5" align="left"><b>Add: CGST</td>
                <td colspan="3" align="left"><?php echo $meta['cgst_total_tax_amount']; ?></td>
            <tr >
                <td colspan="5" align="left" style="font-weight: bold;"><b>Add: SGST</td>
                <td colspan="3" align="left"><?php echo $meta['sgst_total_tax_amount']; ?></td>
            </tr>
            <tr align="left"><td colspan="5" style="font-weight: bold;">Total Amt after Tax</td>
                <td colspan="3"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td width="30%" rowspan="4" align="left">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="4" colspan="3"></td>
                <td colspan="5" align="left"><b>GST on Reverse Charge</td>
                <td colspan="3" align="left">0</td>
            </tr>
            <tr>
                <td colspan="6" style="border-right: 2px solid; font-weight: bold;text-align: center;padding: 2%;background-color: rgb(211,211,211);">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="6" style="padding: 3%; border-right: 2px solid">&nbsp</td>
            </tr>

            <tr>
                <td colspan="6" align="center" style="border-right: 2px solid"><b>Authorised signatory</td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

