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
                <td colspan="7" style="border-left: hidden;">A-34, FIRST FLOOR, SECTOR-63, NOIDA, UTTAR PRADESH, 201301<br>Email: billing@247around.com<br><br><b>GSTIN: 09AAFCB1281J1ZM</b></td>
            
            </tr>
            <tr style="text-align: center; height: 50px; font-size: 40px;background-color: rgb(211,211,211);">
                <td colspan="8"><b>Estimate Sheet</b>
            </tr>

            <tr style="height: 20px">
                <td colspan="5">Name : <?php echo $meta['company_name'];?></td>
                <td colspan="3">GSTIN: <?php echo $meta['company_gst_number'];?></td>
            </tr>
            <tr style="height: 5%">
                <td colspan="8" style="padding-bottom: 2%;padding-top: 2%">Address : <?php echo $meta['company_address'];?></td>
            </tr>
            <tr>
                <td colspan="6">247Around Booking ID: <?php echo $meta['booking_id'];?></td>
                <td colspan="2" style="">Date: <?php echo $meta['date'];?></td>
                
            </tr>
            <tr>
                <td colspan="6">Code:<?php echo $meta['order_id'];?></td>
                <td colspan="2"></td>
            </tr>
            <tr style="text-align: center; background-color: rgb(211,211,211);">
                <td colspan="2" ><b>Customer Name</b></td>
                <td colspan="1"><b>Appliance</b></td>
                <td colspan="2" ><b>Category</b></td>
                <td colspan="3"><b>Capacity</b></td>
            </tr>
            
            <tr style="text-align: center;" >
                <td colspan="2" ><?php echo $meta['name'];?></td>
                <td colspan="1"><?php echo $meta['services'];?></td>
                <td colspan="2" ><?php echo $meta['category'];?></td>
                <td colspan="3"><?php echo $meta['capacity'];?></td>
            </tr>            
            <tr style="text-align: center; background-color: rgb(211,211,211);">
                <td rowspan="2" colspan="2" ><b>Brand/Model</b></td>
                <td rowspan="2" colspan="1" ><b>Service</b></td>
                <td rowspan="2"colspan="1"  ><b>Taxable value</b></td>
                <td  colspan="3"  style="text-align: center;"><b>GST</b></td>
                <td rowspan="2" colspan="1"  ><b>Total</b></td>
            </tr>
            <tr align="center" style="background-color: rgb(211,211,211)">
                <td style="padding: 1px">Rate</td>
                <td colspan="2">Amount</td>
            </tr>
            <?php 
                foreach ($estimate as $data) { ?>
            <tr style='text-align: center;'>
                <td colspan="2"><?php echo $data['brand']." / ".$data['model_number']; ?></td>
                <td colspan="1" ><?php echo $data['service_type']; ?></td>
                <td colspan="1"><?php echo $data['taxable_value']; ?></td>
                <td colspan="1"><?php echo $data['gst_rate']; ?></td>
                <td colspan="2"><?php echo $data['gst_tax_amount']; ?></td>
                <td colspan="1"><?php echo $data['total_amount']; ?></td>
            </tr>
                	
              <?php  } ?>
           
            <tr align="center">
                <td colspan="2" style="background-color: rgb(211,211,211);"><b>Total</td>
                <td colspan="1"></td>
                <td colspan="1"><?php echo $meta['total_taxable_value'];?></td>
                <td colspan="1"></td>
                <td colspan="2"><?php echo $meta['total_gst_tax_amount'];?></td>
                <td colspan="1"><?php echo $meta['total_amount_with_gst'];?></td>
            </tr>
            
            <tr align="left" >
                <td colspan="4" align="center"><b>Total Invoice amount in words</b></td>
                <td  colspan="3"><b>Total Amt before Tax</td>
                <td colspan="1"><?php echo $meta['total_taxable_value']; ?></td>
            </tr>
            <tr align="left">
                <td colspan="4" rowspan="2" style="padding: 2%" align="center"><?php echo $meta['price_inword'];?></td>
                <td colspan="3"><b>Add: GST</td>
                <td colspan="1"><?php echo $meta['total_gst_tax_amount'];?></td>
             </tr>
            <tr style="text-align: left;">
                <td colspan="3" style="font-weight: bold;">Total Amt after Tax</td>
                <td colspan="1"><?php echo $meta['total_amount_with_gst'];?></td>
            </tr>
            <tr>
                <td align="center" style="background-color: rgb(211,211,211); width:30%"><b>Bank Details</td>
                <td rowspan="6" colspan="3" align="center" style="width: 25%;"><img  src="<?php echo base_url()."images/UP.jpg"; ?>" style="width: 50%;"></td>
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