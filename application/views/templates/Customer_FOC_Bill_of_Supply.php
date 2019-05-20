<html>
    <head>
        <title>Bill of Supply</title>
        <style type="text/css">
            .table{
                border:solid 2px;
                border-collapse: collapse;
                width: 960px;
                height: 75%;
                font-family: sans-serif;
                font-size: 100%;
                margin: auto;
            }
            td{
                border:solid 2px;
                padding: 1%;
            }
        </style>
    </head>
    <body>
        <table class="table">
            <tr>
                <td style="text-align: center;border-bottom: hidden; padding-bottom: 0px;" colspan="6" ><h1 style="margin:0px"><?php echo $meta['company_name']; ?></h1></td>
            </tr>
            <tr>
                <?php if($meta['main_company_logo']){ ?>
                <td  width="20%" align="left" style="border-right: hidden; padding-top: 0px; padding-bottom: 0px; padding-left: 0px;"><img style="padding: 5px;  height: 110px; width: 110px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>"></td>
                <?php } ?>
                <td colspan="4" width="75%" align="center" style="border:hidden; padding-left: 5%; padding-bottom: 0px;"><?php echo $meta['company_address']; ?><br></br><b>GSTIN :  <?php echo $meta["gst_number"]; ?></b></td>
                <td  colspan="1" style="text-align: center; width: 20%; border-left: hidden;padding-bottom: 0px;"><b>(<?php echo $meta["recipient_type"]; ?>)</td>
            </tr>
            
            <tr style="height: 50px; background-color: rgb(211,211,211);">	
                <td colspan="6" style="text-align: center; font-size: 40px"><b>Bill of Supply</b></tr>


            <tr><td colspan="3">Invoice No:<?php echo $meta["invoice_id"]; ?></td>		
                <td colspan="3">Booking ID:
                    <?php echo $meta["booking_id"]; ?></td></tr>
            <tr>	
                <td colspan="2">Reverse Charge (Y/N):</td>
                <td colspan="1">N</td>
                <td colspan="3">Invoice Date: <?php echo $meta["invoice_date"]; ?></td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="6"><b>Bill to Party</b></tr>


            <tr style="height: 20px" class="bold"><td colspan="6">Name:  - <?php echo $meta["customer_name"] ?> </td></tr>
            <tr style="height: 5%" class="bold"><td colspan="6">Address - <?php echo $meta["customer_address"]; ?></td></tr>
            <tr><td colspan="2">Phone: 9555000247</td>
                <td colspan="3" width="30%">Place of Supply: <?php echo $meta["state"]; ?></td>	
                <td>Code:<?php echo $meta["state_code"]; ?></td></tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td colspan="2"><b>Product Description</td>
                <td style="padding: 1px"><b>HSN/SAC<br>Code</td>
                        <td><b>Qty</td>
                        <td><b>Rate</td>
                        <td><b>Value of Supply</td>

            </tr>


            <?php
            foreach ($booking as $data) {
            ?>
                <tr style='text-align: center;'>
                        <td colspan='2'><?php echo $data['description']; ?></td>
                        <td><?php echo $data['hsn_code']; ?></td>
                        <td><?php echo $data['qty']; ?></td>
                        <td><?php echo $data['rate']; ?></td>
                        <td><?php echo $meta['sub_total_amount']; ?></td>
                </tr>
            <?php
            }
            ?>		

            <tr align="center"><td style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style="border-right: hidden;width: 20%"></td>	
                <td style="border-left: hidden;"></td>
                <td><b><?php echo $meta["total_qty"]; ?></td>	
                <td></td>
                <td><b><?php echo $meta["sub_total_amount"]; ?></td>
            </tr>

            <tr>
                <td colspan="6" align="center" style="background-color: rgb(211,211,211); font-weight: bold;"><b>Total Invoice amount in words</b></td>
            <tr>
                <td colspan="6" rowspan="1" style="padding: 2%; text-align: center; font-weight: bold;"><?php echo $meta["price_inword"]; ?></td>

            <tr style="text-align: center;">
                <td rowspan="3" align="center">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="3" colspan="2"></td>
                <td colspan="4" style="background-color: rgb(211,211,211); font-weight: bold;">For <?php echo $meta["company_name"]; ?></td>
            </tr>

            <tr>
                <td colspan="3" height="30%"></td>
            </tr>

            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
    </td>
</tr>
<p style="text-align: center; margin: auto;width: 960px" ><?php echo $meta['main_company_description']; ?>
<p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>

</body>
</html>

