<html>
    <head>
        <title>247 Around Tax Invoice</title>
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
        <table class="table"  style="border:solid 2px; border-collapse: collapse; width: 960px; height: 75%; font-family: sans-serif; font-size: 15px; margin: auto;">
            <tr>
                <td style="text-align: center;border-bottom: hidden; padding-bottom: 0px; border-right: 1px solid;" colspan="10" ><h1 style="margin-bottom: 0px;">Blackmelon Advance Technology Co. Pvt. Ltd.</h1></td>
            </tr>
            <tr style=""><td align="left" style="border-right: hidden;"><img style="padding: 5px;" src="<?php echo base_url();?>images/logo.jpg"></td>
                <td colspan="7"  align="center" style="border:hidden;padding-right: 15%; ">A-1/7a, A BLOCK, KRISHNA NAGAR,
                    <br> DELHI 110051
                    <br>Email: billing@247around.com
                    <br><br>
                    <b>GSTIN: 07AAFCB1281J1ZQ</b>
                </td>
                <td colspan="2" style="text-align: right; border-left: hidden; border-right: 2px solid;"><b>(<?php echo $meta['recipient_type']; ?>)</td>
            </tr>
<!--            <tr style="border: hidden;"><td colspan="6" align="center" style=" border-right: hidden;padding-right: 5%">Email: seller@247around.com</td></tr>-->
    <!-- 	<tr><td style="border-bottom: hidden;border-top: hidden;border-right: hidden;padding: 1%">shmskjdhlskdjls</td>	
            -->	
<!--            <tr style="text-align: center;" ><td colspan="10" style="padding-top: 0px;"><b>GSTIN: 07AAFCB1281J1ZQ</td></tr>-->
            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                <td colspan="10" style="border-right: 0px solid;" align="center"><b><?php echo $meta['invoice_type']; ?></b></td>
            </tr>
            <tr><td colspan="3">Invoice No:<?php echo $meta['invoice_id']; ?></td>		
                <td colspan="7" style="border-right: 2px solid;">Reference No: </td>
            </tr>
            <tr><td colspan="3">Date: <?php echo $meta['invoice_date']; ?></td>	
                <td colspan="7" style="border-right: 2px solid;">Period: <?php echo $meta['sd']; ?> - <?php echo $meta['ed']; ?></td>
            </tr>
            <tr><td colspan="3">Reverse Charge (Y/N): N</td>	
                <td colspan="7" style="border-right: 2px solid;"></td>
            </tr>
            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td colspan="10" style="border-right: 0px solid;" align="center"><b>Bill to Party</b></tr>
            <tr style="height: 20px">
                <td colspan="10" style="border-right: 2px solid;">Name: <?php echo $meta['company_name']; ?></td></tr>
            <tr><td colspan="10" style="border-right: 2px solid;">Address:  <?php echo $meta['company_address']; ?></td></tr>
            <tr><td colspan="2">GSTIN: <?php echo $meta['gst_number']; ?></td>
                <td colspan="6">Place of Supply: <?php echo $meta['state']; ?></td>	
                <td colspan="2" style="border-right: 2px solid;">Code:<?php echo $meta['state_code']; ?></td></tr>

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
                    <td width="8%"><?php echo $data['taxable_value']; ?></td>
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
                <td style=""></td>	
                <td><?php echo $meta['total_qty']; ?></td>	
                <td style=""></td>	
                <td><?php echo $meta['total_taxable_value']; ?></td>
                <td></td>
                <td><?php echo $meta['cgst_total_tax_amount']; ?></td>
                <td></td>
                <td><?php echo $meta['sgst_total_tax_amount']; ; ?></td>
                <td style="border-right: 2px solid;"><?php echo $meta['sub_total_amount']; ?></td>
            </tr>

            <tr >
                <td colspan="4" align="center"><b>Total Invoice amount in words</b></td>
                <td colspan="5"><b>Total Amt before Tax</td>
                <td colspan="1" align="center" style="border-right: 2px solid;"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr>
                <td colspan="4" rowspan="3" style="padding: 2%; text-align: center;"><?php echo $meta['price_inword']; ?></td>
                <td colspan="5"><b>Add: CGST</td>
                <td colspan="1" align="center" style="border-right: 2px solid;"><?php echo $meta['cgst_total_tax_amount'];; ?></td>
            <tr >
                <td colspan="5" style="font-weight: bold;"><b>Add: SGST</td>
                <td colspan="1" align="center" style="border-right: 2px solid;"><?php echo $meta['sgst_total_tax_amount'];; ?></td>
            </tr>
            <tr ><td colspan="5" style="font-weight: bold;">Total Amount after Tax</td>
                <td colspan="1" align="center" style="border-right: 2px solid;"><?php echo $meta['sub_total_amount'];; ?></td>
            </tr>

            <tr>
                <td width="32%" align="center" style="background-color: rgb(211,211,211);"><b>Bank Details</td>
                <td rowspan="6" colspan="3"><img style="width: 160px;" src="<?php echo base_url()."images/247aroundstamp.jpg"; ?>"></td>
                <td colspan="5"><b>GST on Reverse Charge</td>
                <td colspan="1" align="center" style="border-right: 2px solid;">0</td>
            </tr>
            <tr><td width="32%">Bank Name: ICICI Bank</td>
                <td rowspan="2" colspan="7" style="padding: 2%;background-color: rgb(211,211,211);"><b>For Blackmelon Advance Technology Co. Pvt. Ltd.</td>
            </tr>
            <tr><td width="32%">Acc No: 102405500277</td></tr>
            <tr><td width="32%">IFSC: ICIC0001024</td>
                <td rowspan="2" colspan="7" align="center"><img src="<?php echo base_url()."images/anujsign.jpg"; ?>" style="width: 170px;"></td>
            </tr>
            <tr>
                <td style="border-bottom: hidden;">	</td>
            </tr>
            <tr><td>	</td>
                <td colspan="7" align="center"><b>Authorised Signature</b></td>
            </tr>

        </table>
        <p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>
    </body>
</html>

