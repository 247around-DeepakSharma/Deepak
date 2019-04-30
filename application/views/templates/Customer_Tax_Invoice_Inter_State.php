<html>
    <head>
        <title>Tax Invoice Interstate</title>
        <style type="text/css">
            td{
                border:solid 1px;
                padding: 1%;
            }

        </style>
    </head>
    <body>
        <table style=" border:solid 2px; border-collapse: collapse; width: 960px; height: 75%; font-family: sans-serif; font-size: 100%; margin: auto;">
            <tr>
                <td style="text-align: center;border-bottom: hidden; " colspan="8" ><h1 style="margin-bottom: 0px;"><?php echo $meta['company_name']; ?></h1></td>
            </tr>
            <tr style="">
                <?php if($meta['main_company_logo']){ ?>
                <td  colspan="1" align="left" style="border-right: hidden;"><img style="padding: 2px;  height: 110px; width: 110px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>"></td>
                <?php } ?>
                <td colspan="4" align="center" style="border:hidden; padding-right: 5%"><?php echo $meta['company_address']; ?> <br> Phone: <?php echo $meta['owner_phone_1']; ?><br><br><b>GSTIN :  <?php echo $meta['gst_number']; ?></td>
                <td colspan="3" style="text-align: center; border-left: hidden;"><b>(<?php echo $meta['recipient_type'];?>)</td>
            </tr>
<!--            <tr style="border: hidden;"><td colspan="6" align="center" style="padding-right: 15%; border-right: hidden;">Phone: <?php //echo "$owner_phone_1"; ?></td></tr>-->
    <!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
            -->	
<!--            <tr style="text-align: center;" ><td colspan="8">GSTIN :  <?php echo $meta['gst_number']; ?></td></tr>-->


            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="8" style="text-align: center; height: 50px; font-size: 40px;"><b>Tax Invoice</b></tr>


            <tr><td colspan="3">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="3">Booking ID:</td>
                <td colspan="2"><?php echo $meta['booking_id']; ?></td></tr>
            <tr><td colspan="3">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="3">Reverse Charge (Y/N):</td>
                <td colspan="2">N</td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="8" style="text-align: center;"><b>Bill to Party</b></tr>


            <tr style="height: 20px"><td colspan="8">Name: <?php echo $meta['customer_name']; ?></td></tr>
            <tr style="height: 5%"><td colspan="8">Address: <?php echo $meta['customer_address']; ?></td></tr>
            <tr><td colspan="3">Phone: 9555000247</td>
                <td colspan="3" style="">Place of Supply: <?php echo $meta['state']; ?></td>
                <td colspan="2">Code: <?php echo $meta['state_code']; ?></td>
            </tr>


            <tr style="text-align: center; background-color: rgb(211,211,211);">
                <td rowspan="2" style="font-size: 13px;"><b>Product Description</td>
                <td rowspan="2" style="font-size: 13px;" style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td rowspan="2" style="font-size: 13px;"><b>Qty</td>
                <td rowspan="2" style="font-size: 13px;"><b>Rate</td>
                <td rowspan="2" style="font-size: 13px;"><b>Taxable value</td>
                <td colspan="2" style="font-size: 13px;" style="text-align: center;"><b>IGST</td>
                <td rowspan="2" style="font-size: 13px;"><b>Total</td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)">
                <td style="padding: 1px">Rate</td>
                <td>Amount</td>
            </tr>


            <?php
            foreach ($booking as $data) {
            ?>
                <tr style='text-align: center;'>
                <td width="30%"><?php echo $data['description']; ?></td>
                <td width="8%"><?php echo $data['hsn_code']; ?></td>
                <td width="8%"><?php echo $data['qty']; ?></td>
                <td width="12%"><?php echo $data['rate']; ?></td>
                <td width="12%"><?php echo $data['taxable_value']; ?></td>
                <td width="8%"><?php echo $data['igst_rate']; ?></td>
                <td width="12%"><?php echo $data['igst_tax_amount']; ?></td>
                <td width="20%"><?php echo $data['total_amount']; ?></td>
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
                <td><?php echo $meta['igst_total_tax_amount']; ?></td>

                <td><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr align="left" >
                <td width="60%" colspan="4"><b>Total Invoice amount in words</b></td>
                <td width="30%" colspan="3"><b>Total Amt before Tax</td>
                <td width="10%" colspan="1" align="left"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr align="left">
                <td colspan="4" rowspan="2" align="center" style="padding: 2%"><?php echo $meta['price_inword']; ?></td>
                <td colspan="3"><b>Add: IGST</td>
                <td colspan="1" align="left"><?php echo $meta['igst_total_tax_amount']; ?></td>
            <tr style="text-align: left;"><td colspan="3" style="font-weight: bold;">Total Amt after Tax</td>
                <td colspan="1" align="left"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td rowspan="4" align="left" width="40%">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="4" colspan="3"></td>
                <td colspan="3"><b>GST on Reverse Charge</td>
                <td colspan="1" align="left">0</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: rgb(211,211,211);">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="4" height="20%" style="padding:3%"></td>
            </tr>

            <tr>
                <td colspan="4"></td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin: auto;width: 960px" ><?php echo $meta['main_company_description']; ?></b>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

