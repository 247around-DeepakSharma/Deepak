<html>
    <head>
        <title>Tax Invoice Intrastate</title>
        <style type="text/css">
            td{
                border:solid 1px;
                padding: 1%;
            }
        </style>
    </head>
    <body>
        <table class="table" style="border:solid 2px; border-collapse: collapse; width: 960px; height: 75%; font-family: sans-serif;font-size: 100%; margin: auto;">
            <tr>
                <td style="text-align: center;border-bottom: hidden; " colspan="10" ><h1 style="margin-bottom: 0px;"><?php echo $meta['company_name']; ?></h1></td>
            </tr>
            <tr>
                <?php if($meta['main_company_logo']){ ?>
                <td align="left" style="border-right: hidden;"><img style="padding: 5px;  height: 110px; width: 110px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>"></td>
                <?php } ?>
                <td colspan="7"  align="center" style="border:hidden;"><?php echo $meta['company_address']; ?> <br>Phone: <?php echo $meta['owner_phone_1']; ?><br><br><b>GSTIN :  <?php echo $meta['gst_number']; ?></td>
                <td colspan="2" style="text-align: right; border-left: hidden;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
<!--            <tr style="border: hidden;"><td colspan="6" align="center" style=" border-right: hidden; ">Phone: <?php echo $meta['owner_phone_1']; ?></td></tr>-->
    <!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
            -->	
<!--            <tr style="text-align: center;" ><td colspan="10">GSTIN :  <?php// echo "$gst_number"; ?></td></tr>-->


            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="10" style="text-align: center; height: 50px; font-size: 40px;"><b>Tax Invoice</b></tr>


            <tr><td colspan="4">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="3">Booking ID:
                <td colspan="3"><?php echo $meta['booking_id']; ?></td></tr>
            <tr><td colspan="4">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="3">Reverse Charge (Y/N):</td>
                <td colspan="3">N</td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="10" style="text-align: center;"><b>Bill to Party</b></tr>


            <tr style="height: 20px"><td colspan="10">Name:  <?php echo $meta['customer_name']; ?></td></tr>
            <tr style="height: 5%"><td colspan="10">Address: <?php echo $meta['customer_address']; ?></td></tr>
            <tr><td colspan="3">Phone: 9555000247</td>
                <td colspan="4" style="">Place of Supply: <?php echo $meta['state']; ?></td>	
                <td colspan="3">Code:<?php echo $meta['state_code']; ?></td></tr>

            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td rowspan="2"  style="font-size: 14px;"><b>Product Description</td>
                <td rowspan="2"  style="font-size: 14px;"style="padding: 1px"><b>HSN/SAC<br>Code</b></td>
                <td rowspan="2"  style="font-size: 14px;"><b>Qty</b></td>
                <td rowspan="2"  style="font-size: 14px;"><b>Rate</b></td>
                <td rowspan="2"  style="font-size: 14px;"><b>Taxable <br> value</b></td>
                <td colspan="2" width="15%" style="font-size: 14px;"><b>CGST</b></td>
                <td colspan="2" width="15%" style="font-size: 14px;"><b>SGST</b></td>
                <td rowspan="2"  style="font-size: 14px; border-right: 2px solid;"><b>Total</b></td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)">
                <td style="font-size: 14px;">Rate</td>
                <td style="font-size: 14px;">Amount</td>
                <td style="font-size: 14px;">Rate</td>
                <td style="font-size: 14px;">Amount</td>
            </tr>


            <?php
            foreach ($booking as $data) {
            ?>
            <tr style='text-align: center;'>
                    <td width="32%"><?php echo $data['description']; ?></td>
                    <td width="10%"><?php echo $data['hsn_code']; ?></td>
                    <td width="5%"><?php echo $data['qty']; ?></td>
                    <td width="5%"><?php echo $data['rate']; ?></td>
                    <td width="15%"><?php echo $data['taxable_value']; ?></td>
                    <td width="7%"><?php echo $data['cgst_rate']; ?></td>
                    <td width="8%"><?php echo $data['cgst_tax_amount']; ?></td>
                    <td width="7%"><?php echo $data['sgst_rate']; ?></td>
                    <td width="8%"><?php echo $data['sgst_tax_amount']; ?></td>
                    <td width="10%" style="border-right: 2px solid;"><?php echo $data['total_amount']; ?></td>
                </tr>
            <?php
            }
            ?>		


            <tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style="border-right: hidden;"></td>	
                <td><?php echo $meta['total_qty']; ?></td>	
                <td style="border-left: hidden;"></td>	
                <td><?php echo $meta['total_taxable_value']; ?></td>
                <td></td>
                <td><?php echo $meta['cgst_total_tax_amount']; ?></td>
                <td></td>
                <td><?php echo $meta['sgst_total_tax_amount']; ?></td>
                <td><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr align="center" >
                <td width="60%" colspan="4"><b>Total Invoice amount in words</b></td>
                <td width="30%" colspan="5" align="left"><b>Total Amount before Tax</td>
                <td width="10%" colspan="1" align="left"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr align="center">
                <td colspan="4" rowspan="3" style="padding: 2%"><?php echo $meta['price_inword']; ?></td>
                <td colspan="5" align="left"><b>Add: CGST</td>
                <td colspan="1" align="left"><?php echo $meta['cgst_total_tax_amount']; ?></td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="5"><b>Add: SGST</td>
                <td colspan="1"><?php echo $meta['sgst_total_tax_amount']; ?></td>
            </tr>
            <tr style="text-align: left;">
                <td colspan="5"><b>Total Amt after Tax</td>
                <td colspan="1"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td rowspan="4" align="left" width="30%">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="4" colspan="3"></td>
                <td colspan="5" align="left"><b>GST on Reverse Charge</td>
                <td colspan="1" align="left">0</td>
            </tr>
            <tr>
                <td colspan="6" style="background-color: rgb(211,211,211);">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="6" style="padding:3%"><h1></td>
            </tr>

            <tr>
                <td colspan="6"></td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin: auto;width: 960px" ><?php echo $meta['main_company_description']; ?>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

