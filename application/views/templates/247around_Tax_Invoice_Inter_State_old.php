<html>
    <head>
        
        <style type="text/css">
        td{
            border:solid 1px;
            padding: 1%;
        }
        </style>
    </head>
    <body>
        <table  style="border:solid 2px; border-collapse: collapse; width: 960px;height: 75%;font-family: sans-serif;margin: auto;">
            <tr>
                <td style="text-align: center; border-bottom: hidden;" colspan="8" >
                    <h1 style="margin: 0px;">Blackmelon Advance Technology Co. Pvt. Ltd.</h1>
                </td>
            </tr>
            <tr style="">
                <td align="left" style="border-right: hidden;"><img style="padding: 5px;" src="<?php echo base_url();?>images/logo.jpg"></td>
                <td colspan="5" align="center" style="border:hidden;">A-1/7, F/F A BLOCK, KRISHNA NAGAR, DELHI, 110051<br>Email: seller@247around.com<br><br><b>GSTIN: 07AAFCB1281J1ZQ</b></td>
                <td colspan="2" style="text-align: right;  border-left: hidden;"><b>(<?php echo $meta['recipient_type'];?>)</td>
            </tr>
            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">
                <td colspan="8"><b><?php echo $meta['invoice_type']; ?></b>
            </tr>
            <tr>
                <td colspan="3">Invoice No:<?php echo $meta['invoice_id'];?></td>
                <td colspan="5">Reference No: 	<?php echo $meta['reference_invoice_id'];?></td>
            </tr>
    
            <tr>
                <td colspan="3">Invoice Date: <?php echo $meta['invoice_date'];?></td>
                <td colspan="5">Period: <?php echo $meta['sd'];?> - <?php echo $meta['ed'];?></td>
            <tr>
                <td colspan="2">Reverse Charge (Y/N):</td>
                <td colspan="1">N</td>
                <td colspan="5"></td>
            </tr>        
            <tr style="text-align: center;background-color: rgb(211,211,211);">
                <td colspan="8"><b>Bill to Party</b>
            </tr>
            <tr style="height: 20px">
                <td colspan="8">Name : <?php echo $meta['company_name'];?></td>
            </tr>
            <tr style="height: 5%">
                <td colspan="8" style="padding-bottom: 2%;padding-top: 2%">Address : <?php echo $meta['company_address'];?></td>
            </tr>
            <tr>
                <td colspan="2">GSTIN: <?php echo $meta['gst_number'];?></td>
                <td colspan="4" style="">Place of Supply: <?php echo $meta['state'];?></td>
                <td colspan="2">Code:<?php echo $meta['state_code'];?></td>
            </tr>
            <tr style="text-align: center; background-color: rgb(211,211,211);">
                <td rowspan="2" width="30%"><b>Product Description</td>
                <td rowspan="2" width="8%" style="padding: 1px"><b>HSN/SAC<br>Code</td>
                <td rowspan="2" width="8%"><b>Qty</td>
                <td rowspan="2" width="12%"><b>Rate</td>
                <td rowspan="2" width="12%"><b>Taxable value</td>
                <td colspan="2" width="20%" style="text-align: center;"><b>IGST</td>
                <td rowspan="2" width="20%"><b>Total</td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)">
                <td style="padding: 1px">Rate</td>
                <td>Amount</td>
            </tr>
            <?php 
                foreach ($booking as $data) { ?>
            <tr style='text-align: center;'>
                <td width="30%"><?php echo $data['description']; ?></td>
                <td width="8%"><?php echo $data['hsn_code']; ?></td>
                <td width="8%"><?php echo $data['qty']; ?></td>
                <td width="12%"><?php echo $data['rate']; ?></td>
                <td width="12%"><?php echo $data['taxable_value']; ?></td>
                <td width="8%"><?php echo $data['igst_rate']; ?></td>
                <td width="12%"><?php echo $data['igst_tax_amount']; ?></td>
                <td width="20%"><?php echo $data['toal_amount']; ?></td>
            </tr>
                	
              <?php  } ?>
           
            <tr align="center">
                <td style="background-color: rgb(211,211,211);"><b>Total</td>
                <td style="border-right: hidden;"></td>
                <td><?php echo $meta['total_qty'];?></td>
                <td style="border-left: hidden;"></td>
                <td><?php echo $meta['total_taxable_value'];?></td>
                <td></td>
                <td><?php echo $meta['igst_total_tax_amount'];?></td>
                <td><?php echo $meta['sub_total_amount'];?></td>
            </tr>
            
            <tr align="left" >
                <td width="60%" colspan="4" align="center"><b>Total Invoice amount in words</b></td>
                <td width="30%" colspan="3"><b>Total Amt before Tax</td>
                <td width="10%" colspan="1"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr align="left">
                <td colspan="4" rowspan="2" style="padding: 2%" align="center"><?php echo $meta['price_inword'];?></td>
                <td colspan="3"><b>Add: IGST</td>
                <td colspan="1"><?php echo $meta['igst_total_tax_amount'];?></td>
            <tr style="text-align: left;">
                <td colspan="3" style="font-weight: bold;">Total Amt after Tax</td>
                <td colspan="1"><?php echo $meta['sub_total_amount'];?></td>
            </tr>
            <tr>
                <td align="center" style="background-color: rgb(211,211,211); width:30%"><b>Bank Details</td>
                <td rowspan="6" colspan="3" align="center" style="width: 60%;"><img  src="<?php echo base_url()."images/247aroundstamp.png"; ?>" style="width: 68%;"></td>
                <td align="left" colspan="3"><b>GST on Reverse Charge</td>
                <td align="left" colspan="1">0</td>
            </tr>
            <tr>
                <td width="30%">Bank Name: ICICI Bank</td>
                <td rowspan="2" colspan="4" style=" background-color: rgb(211,211,211);" align="center"><b>For Blackmelon Advance <br>Technology Co. Pvt. Ltd.</td>
            </tr>
            <tr>
                <td width="30%">Acc No: 102405500277</td>
            </tr>
            <tr>
                <td width="30%">IFSC: ICIC0001024</td>
                <td rowspan="2" colspan="4" align="center"><img src="<?php echo base_url()."images/anujsign.png"; ?>" style="width: 40%;"></td>
            </tr>
            <tr>
                <td style="border-bottom: hidden;">	</td>
            </tr>
            <tr>
                <td>	</td>
                <td colspan="4" align="center"><b>Authorised Signature</b></td>
            </tr>
        </table>
        </td>
        </tr>
        <p style="text-align: center; margin-top: 0px"><small>This is a computer generated invoice, no signature is required.</small>
    </body>
</html>