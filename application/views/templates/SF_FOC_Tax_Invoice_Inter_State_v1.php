<html>
    <head>
        <title>SF FOC Tax Invoice</title>
        <style type="text/css">
            .table{
                border:solid 1px;
                border-collapse: collapse;
                width: 960px;
                height: 75%;
                font-family: sans-serif;
                font-size: 100%;
                margin: auto;
            }

            td{
                border:solid 1px;
                padding: 1%;
            }



        </style>
    </head>
    <body>
        <table style="border:solid 1px; border-collapse: collapse; width: 960px; height: 75%; font-family: sans-serif; font-size: 15px; margin: auto;">
            <tr>
                <td style="text-align: center;border-bottom: hidden; " colspan="8" ><h1 style="margin: 0px;"><?php echo $meta['company_name']; ?></h1></td>
            </tr>
            <tr style="">
                <td colspan="6" align="center" style="border-right:hidden; padding-left: 20%"><?php echo $meta['company_address']; ?><br>Phone: <?php echo $meta['owner_phone_1']; ?><br><b>GSTIN :  <?php echo $meta['gst_number']; ?></td>
                <td colspan="2"  style="text-align: center; width: 20%; border-left: hidden;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>


            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="8" align="center"><b>Tax Invoice</b></tr>


            <tr><td colspan="3">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="5">Period: <?php echo $meta['sd']; ?> - <?php echo $meta['ed']; ?>
            <tr><td colspan="3">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="3">Reverse Charge (Y/N):</td>
                <td colspan="2">N</td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="8" align="center"><b>Bill to Party</b></tr>


            <tr style="height: 20px"><td colspan="8">Name: <?php echo $meta['main_company_name']; ?></td></tr>
            <tr style="height: 5%"><td colspan="8">Address: <?php echo $meta['main_company_address']; ?></td></tr>
            <tr><td colspan="2">GSTIN: <?php echo $meta['main_company_gst_number']; ?></td>
                <td colspan="4">Place of Supply: <?php echo $meta['main_company_state']; ?></td>	
                <td colspan="2">Code: <?php echo $meta['main_company_state_code']; ?></td></tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td rowspan="2" width="30%"><b>Product Description</td>
                <td rowspan="2" width="10%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td rowspan="2" width="10%"><b>Qty</td>
                <td rowspan="2" width="10%"><b>Rate</td>
                <td rowspan="2" width="10%"><b>Taxable value</td>
                <td colspan="2" width="20%"><b>IGST</td>
                <td rowspan="2" ><b>Total</td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)"><td style="padding: 1px"><b>Rate</td>
                <td><b>Amount</td>
            </tr>


            <?php
            foreach ($booking as $data) {
            ?>
                <tr style='text-align: center;'>
                        <td width="30%"><?php echo $data['description']; ?></td>
                        <td width="10%"><?php echo $data['hsn_code']; ?></td>
                        <td width="10%"><?php echo $data['qty']; ?></td>
                        <td width="10%"><?php echo $data['rate']; ?></td>
                        <td width="10%"><?php echo $data['taxable_value']; ?></td>
                        <td><?php echo $data['igst_rate']; ?></td>
                        <td><?php echo $data['igst_tax_amount']; ?></td>
                        <td ><?php echo $data['total_amount']; ?></td>
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
                <td width="60%" colspan="4" align="center"><b>Total Invoice amount in words</b></td>
                <td width="30%" colspan="3"><b>Total Amt before Tax</td>
                <td width="10%" colspan="1"><?php echo $meta['total_taxable_value']; ; ?></td>
            </tr>
            <tr align="left">
                <td colspan="4" rowspan="2" style="padding: 2%; text-align: center;"><?php echo $meta['price_inword']; ?></td>
                <td colspan="3"><b>Add: IGST</td>
                <td colspan="1"><?php echo $meta['igst_total_tax_amount']; ?></td>
            <tr style="text-align: left;"><td colspan="3"><b>Total Amt after Tax</td>
                <td colspan="1"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td rowspan="4" width="30%" align="left">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="4" colspan="3"></td>
                <td colspan="3" align="left"><b>GST on Reverse Charge</td>
                <td colspan="1" align="left">0</td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="padding: 2%;background-color: rgb(211,211,211);"><b>For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="4" height="30%" style="padding: 3%"></td>
            </tr>

            <tr>
                <td colspan="4" align="center" height="40%"><b>Authorised signatory</td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

