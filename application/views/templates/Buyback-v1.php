<html>
    <head>
        <title>SF FOC Bill of Supply</title>
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

            .bold{
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <table class="table">
            <tr>
                <td style="text-align: center;border-bottom: hidden; border-right: 1px solid;" colspan="6" ><h1 style="margin: 0px;">Blackmelon Advance Technology Co. Pvt. Ltd.</h1></td></tr>
            <tr style="">
                <td colspan="5" align="center" style="padding-left: 20%">A-1/7, F/F A BLOCK, KRISHNA NAGAR, DELHI, 110051<br>Phone:  9555000247</td>
                <td colspan="1" style="text-align: center; width: 20%; border-left: hidden; border-right: 2px solid;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
<!--            <tr style="font-weight: bold;"><td colspan="6" style="text-align: center;">Phone:  <?php //echo "$owner_phone_1"; ?></td></tr>-->
    <!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
            -->	


            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="6" style="border-right: 2px solid;"  align="center"><b>Bill of Supply</b></tr>


            <tr><td colspan="2">Invoice No: <?php echo $meta['invoice_id']; ?></td>		
                <td colspan="4" style="border-right: 2px solid;">Period: <?php echo $meta['sd']; ?> - <?php echo $meta['ed']; ?>
            <tr><td colspan="2">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="4" style="border-right: 2px solid;">Reverse Charge (Y/N): N</td>
            </tr>
            <tr><td colspan="2">Reference No: <?php echo $meta['reference_invoice_id']; ?></td></tr>
            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td colspan="6" style="border-right: 2px solid;" align="center"><b>Bill to Party</b></tr>


            <tr style="height: 20px" class="bold"><td colspan="6" style="border-right: 2px solid;">Name: <?php echo $meta['company_name']; ?></td></tr>
            <tr style="height: 5%"><td colspan="6" style="padding-top: 2%; padding-bottom: 2%; border-right: 2px solid;">Address: <?php echo $meta['company_address']; ?></td></tr>
            <tr><td colspan="2">GSTIN: </td>
                <td colspan="3" width="30%">Place of Supply: <?php echo $meta["state"]; ?></td>	
                <td style="border-right: 2px solid;">Code: <?php echo $meta["state_code"]; ?></td></tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td style="width: 40%;" colspan="2"><b>Product Description</td>
                <td style="width: 15%; "style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td style="width: 10%;"><b>Qty</td>
                <td style="width: 15%;"><b>Rate</td>
                <td style="width: 20%; border-right: 2px solid;"><b>Value of Supply</td>

            </tr>


            <?php
           
            foreach ($booking as $data) {
                
            ?>
                <tr style='text-align: center;'>
                        <td colspan='2'><?php echo $data['description']; ?></td>
                        <td><?php echo $data['hsn_code']; ?></td>
                        <td><?php echo $data['qty']; ?></td>
                        <td><?php echo $data['rate']; ?></td>
                        <td style="border-right: 2px solid;"><?php echo $data['taxable_value']; ?></td>
                </tr>
            <?php
               
            }
            ?>		

                <tr align="center"><td colspan="2" style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style=""></td>
                <td><b><?php echo $meta['total_qty']; ?></td>	
                <td></td>
                <td style="border-right: 2px solid;"><b><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr>
                <td colspan="6" align="center" style="background-color: rgb(211,211,211); font-weight: bold;border-right: 2px solid;"><b>Total Invoice amount in words</b></td>
            <tr>
                <td colspan="6" rowspan="1" style="padding: 2%; text-align: center; border-right: 2px solid;"><?php echo $meta['price_inword']; ?></td>

            <tr style="text-align: center;">
                <td style="width: 40%;" rowspan="3" align="center">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
<!--                <td rowspan="3" colspan="2"></td>-->
                <td rowspan="3" colspan="2" align="center"><img  src="<?php echo base_url()."images/247aroundstamp.jpg"; ?>" style="width: 120px;"></td>
                <td colspan="4" class="bold" style="background-color: rgb(211,211,211);">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
<!--                <td colspan="3" height="30%" style="padding: 2%; border-right: 2px solid;"></td>-->
                <td colspan="3" style="border-right: 2px solid;" align="center"><img src="<?php echo base_url()."images/anujsign.jpg"; ?>" style="width: 120px;"></td>
            </tr>

            <tr>
                <td colspan="3" align="center" style="border-right: 2px solid;"><b>Authorised signatory</td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

