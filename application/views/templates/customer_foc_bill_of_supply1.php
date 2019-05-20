<?php 
include 'customer_foc_bill_of_supply_variables.php';
?>
<html>
    <head>
        <title>SF FOC Bill of Supply</title>
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
                <td style="text-align: center;border-bottom: hidden; border-right: 2px solid;" colspan="6" ><h1 style="margin: 0px;"><?php echo $meta['company_name']; ?></h1></td></tr>
            <tr style="">
                <td colspan="1" align="left" style="padding-top: 0px !important; border-right: hidden;"><img style="width:30%" src="<?php echo base_url(); ?>images/logo.jpg"></td>
                <td colspan="4" align="center" style="padding-left: 20%"><?php echo $meta['company_address']; ?><br></br><b>GSTIN : <?php echo $gst_number; ?></td>
                <td colspan="1" style="text-align: center; width: 20%; border-left: hidden; border-right: 2px solid;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
           <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="6" style="border-right: 2px solid;"><b>Bill of Supply</b></tr>


            <tr><td colspan="2">Invoice No: <?php echo $meta['invoice_id']; ?></td>		
                <td colspan="4" style="border-right: 2px solid;">Period: <?php echo $meta['sd']; ?> - <?php echo $meta['ed']; ?>
            <tr><td colspan="2">Invoice Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="4" style="border-right: 2px solid;">Reverse Charge (Y/N): N</td>
            </tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);"><td colspan="6" style="border-right: 2px solid;"><b>Bill to Party</b></tr>


            <tr style="height: 20px" class="bold"><td colspan="6" style="border-right: 2px solid;">Name: Blackmelon Advance Technology Co. Pvt. Ltd.</td></tr>
            <tr style="height: 5%"><td colspan="6" style="padding-top: 2%; padding-bottom: 2%; border-right: 2px solid;">Address: A-1/7, F/F A BLOCK, KRISHNA NAGAR, DELHI, 110051</td></tr>
            <tr><td colspan="2">GSTIN: 07AAFCB1281J1ZQ</td>
                <td colspan="3" width="30%">Place of Supply: Delhi</td>	
                <td style="border-right: 2px solid;">Code: 07</td></tr>


            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td style="width: 40%;" colspan="2"><b>Product Description</td>
                <td style="width: 15%; "style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td style="width: 10%;"><b>Qty</td>
                <td style="width: 15%;"><b>Rate</td>
                <td style="width: 20%; border-right: 2px solid;"><b>Value of Supply</td>

            </tr>


            <?php
            foreach ($booking as $data) {
                $total_amount = $data['qty'] * $data['rate'];
            ?>
                <tr style='text-align: center;'>
                        <td colspan='2'><?php echo $data['description']; ?></td>
                        <td><?php echo $data['hsn_code']; ?></td>
                        <td><?php echo $data['qty']; ?></td>
                        <td><?php echo $data['rate']; ?></td>
                        <td style="border-right: 2px solid;"><?php echo $total_amount; ?></td>
                </tr>
            <?php
                $total_qty += $data['qty'];
                $sub_total_amount += $total_amount;
            }
            ?>		

                <tr align="center"><td colspan="2" style="background-color: rgb(211,211,211);"><b>Total</td>	
                <td style=""></td>
                <td><b><?php echo "$total_qty"; ?></td>	
                <td></td>
                <td style="border-right: 2px solid;"><b><?php echo "$sub_total_amount"; ?></td>
            </tr>

            <tr>
                <td colspan="6" align="center" style="background-color: rgb(211,211,211); font-weight: bold;border-right: 2px solid;"><b>Total Invoice amount in words</b></td>
            <tr>
                <td colspan="6" rowspan="1" style="padding: 2%; text-align: center; border-right: 2px solid;"><?php echo $meta['price_inword']; ?></td>

            <tr style="text-align: center;">
                <td style="width: 40%;" rowspan="3" align="center">Declaration: We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</td>
                <td rowspan="3" colspan="2"></td>
                <td colspan="4" class="bold" style="background-color: rgb(211,211,211);">For <?php echo $meta['company_name']; ?></td>
            </tr>

            <tr>
                <td colspan="3" height="30%" style="padding: 2%; border-right: 2px solid;">&nbsp</td>
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

