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
        <table  style="border:solid 1px; border-collapse: collapse; width: 960px;height: 75%;font-family: sans-serif;margin: auto;">
            <tr>
                <td style="text-align: center; border-bottom: hidden;" colspan="8" >
                    <h1 style="margin: 0px;"><?php echo $meta['main_company_name']; ?></h1>
                </td>
            </tr>
            <tr style="">
                <td align="left" style="border-right: hidden;">
                    <?php if($meta['main_company_logo']){ ?>
                    <img style="padding: 5px; height: 110px; width: 101px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo']; ?>">
                    <?php } ?>
                </td>
                <td colspan="5" align="center" style="border:hidden;"><?php echo $meta['main_company_address'].", ".$meta['main_company_state'].", ".$meta['main_company_pincode']; ?><br>Email: <?php echo $meta['main_company_email']; ?><br><br><b>GSTIN: <?php echo $meta['main_company_gst_number']; ?></b></td>
                <td colspan="2" style="text-align: right;  border-left: hidden;"><b>(<?php echo $meta['recipient_type'];?>)</td>
            </tr>
<!--            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">
                <td colspan="8"><b><?php// echo $meta['invoice_type']; ?></b>
            </tr>-->
             <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">	
                 <td colspan="8"  align="center"><b><?php echo $meta['invoice_type']; ?></b>
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
                <td colspan="8" align="center"><b>Bill to Party</b>
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
                foreach ($booking as $data) { ?>
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
                	
              <?php  } ?>
           
            <tr align="center">
                <td style="background-color: rgb(211,211,211);"><b>Total</td>
                <td ></td>
                <td><?php echo $meta['total_qty'];?></td>
                <td></td>
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
                <td align="center" style="background-color: rgb(211,211,211); width:30px;"><b>Bank Details</td>
                <td rowspan="6" colspan="3" align="center">
                    <?php if($meta['main_company_seal']){ ?>
                    <img  src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_seal']; ?>" style="width: 160px;">
                    <?php } ?>
                </td>
                <td align="left" colspan="3"><b>GST on Reverse Charge</td>
                <td align="left" colspan="1">0</td>
            </tr>
            <tr>
                <td width="30%">Bank Name: <?php echo $meta['main_company_bank_name']; ?></td>
                <td rowspan="2" colspan="4" style=" background-color: rgb(211,211,211);" align="center"><b>For <?php echo $meta['main_company_name']; ?></td>
            </tr>
            <tr>
                <td width="30%">Acc No: <?php echo $meta['main_company_bank_account']; ?></td>
            </tr>
            <tr>
                <td width="30%">IFSC: <?php echo $meta['main_company_ifsc_code']; ?></td>
                <td rowspan="2" colspan="4" align="center">
                    <?php if($meta['main_company_signature']){ ?>
                    <img src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_signature']; ?>" style="width: 170px;">
                    <?php } ?>
                </td>
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