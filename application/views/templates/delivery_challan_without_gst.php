
<style type="text/css">
    #table1{
      border: solid 1px;
        font-family: sans-serif;
        font-size: 13px;
        margin: auto;
        border-collapse: collapse;

        /*total cols=14*/
    }
    #table1 td {
        word-wrap: break-word;         /* All browsers since IE 5.5+ */
        overflow-wrap: break-word;     /* Renamed property in CSS3 draft spec */
    }
    #table1 td{
        border: solid .5px;

        padding: .5%
    }
    #table1 .blank_row td{
        border-right: hidden;
    }

</style>
<body>
    <table id="table1">
        <tr>
            <td colspan="2" style="border-right: hidden;">
                <?php if($excel_data['main_company_logo']){ ?>
                <img style="padding: 2px;height: 75px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$excel_data['main_company_logo']; ?>">
                <?php } ?>
            </td>
            <td colspan="13"><h1>Delivery Challan</h1></td>
        </tr>
        <tr>
            <td colspan="9" align="left" style="border-bottom: hidden;"><p>To,</p><?php echo $excel_data['sf_name']; ?></td>
            <td  colspan="6" align="left" style="border-bottom: hidden;"><b>Challan No: </b><?php echo $excel_data['sf_challan_no']; ?></td>
        </tr>
        <tr>
            <td  colspan="9" rowspan="2" align="left" style="border-bottom: hidden;"><b>Address:</b> <?php if(!empty($excel_data['sf_contact_person_name'])){ echo 'C/o '.$excel_data['sf_contact_person_name'].", ";} echo $excel_data['sf_address']; ?> 
          
            <?php
                if (!empty($excel_data['sf_contact_number'])) {
                    echo '<br><br><b>Contact Number : </b>' . $excel_data['sf_contact_number'];
                }
            ?>
                            
            </td>
            <td colspan="6" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo $excel_data['partner_challan_no']; ?></td>
        </tr>
        <tr>
            <td colspan="6" align="left" style="border-bottom: hidden;"><b>Date: </b>
                <?php echo date("j M Y"); ?>
            </td>
        </tr>
        <tr>
            <td  colspan="9" align="left"  style="<?php if(!empty($excel_data['courier_servicable_area'])){ ?>border-bottom: hidden;<?php } ?>"><b>GST: </b><?php echo $excel_data['sf_gst']; ?></td>
            <td colspan="6"  style="<?php if(!empty($excel_data['courier_servicable_area'])){ ?>border-bottom: hidden;<?php } ?>"></td>
        </tr>
        <?php
        if(!empty($excel_data['courier_servicable_area'])){
        ?>
        <tr>
            <td  colspan="15" align="left"><b>Courier Servicable Area: </b><?php echo $excel_data['courier_servicable_area']; ?></td>
        </tr>
        <?php
        }
        ?>
        
        <tr style="text-align: center;">
            <td style="text-align: center;"><b>S No</b></td>
            <td style="text-align: center;"><b>Part Name</b></td>
            <td style="text-align: center;"><b>Part Number</b></td>
            <td style="text-align: center;"><b>HSN Code</b></td>
            <td  style="text-align: center;"><b>Booking ID</b></td>
            <?php if(!empty($excel_data['show_consumption_reason'])){ ?>
            <td  style="text-align: center;"><b>Consumption</b></td>
            <?php } ?>
            <?php if(!empty($excel_data['show_serial_number'])){?>
            <td  style="text-align: center"><b>Model Number</b></td>
	    <td  style="text-align: center"><b>Serial Number</b></td>
            <?php } ?>
            <td style="text-align: center;"><b>Qty</b></td>
            <td style="text-align: center; ;"><b>Rate</b></td>
            
           <td style="text-align: center"><b>Total Amount</b></td>
        </tr>
        <?php
       
        $total_qty = 0;
        $total_value = $cgst_amount = $sgst_amount = $igst_amount = 0;
        foreach ($excel_data_line_item as $key => $info) {  ?>
            <tr >
                <td ><?php echo $key +1; ?></td>
                <td ><?php echo $info['spare_desc'] ; ?></td>
                <td ><?php echo $info['part_number'] ; ?></td>
                 <td ><?php echo $info['hsn_code'] ; ?></td>
                <td ><?php echo $info['booking_id'] ; ?></td>
                <?php if(!empty($excel_data['show_consumption_reason'])){?>
                <td>
                    <?php if ($info['consumption'] == 'Part consumed') {
                            $info['consumption'] = 'Defective Part';
                      } echo $info['consumption'];  ?>
                </td>
                <?php  } ?>
                <?php if(!empty($excel_data['show_serial_number'])){
                    ?>
                <td><?php echo $info['model_number_shipped'];?></td>
                <td><?php echo $info['serial_number'];?></td>
                <?php } ?>
                <td><?php echo $info['qty'];?></td>
                <td><?php echo sprintf("%.2f", $info['rate']);?></td>
                
                
                <td><?php echo sprintf("%.2f", $info['value']); $total_qty += $info['qty']; $total_value += sprintf("%.2f", $info['value']); ?></td>
            </tr>
       <?php } 
        ?>
            
        <tr >
            <td><b>Total</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <?php if(!empty($excel_data['show_consumption_reason'])){?>
            <td>
                
            </td>
            <?php  } ?>
            <?php if(!empty($excel_data['show_serial_number'])){
                ?>
            <td></td>
            <td></td>
            <?php } ?>
            <td><?php echo $total_qty?></td>
            <td></td>

            <td><b><?php echo sprintf("%.2f", $total_value);?></b></td>
        </tr>
        <tr>
            <td colspan="14" style="border-bottom: hidden; font-size: 15px">
                <p>If undelivered return to:</p>
                <b><?php echo $excel_data['partner_name']; ?>
            </td>
        </tr>
         <tr>
            <td colspan="7" align="left" style="border-right: hidden;"><?php echo $excel_data['partner_address']; ?>
            <?php
                if (!empty($excel_data['partner_contact_number'])) {
                    echo '<br><br><b>Contact Number: </b>' . $excel_data['partner_contact_number'];
                }
            ?>
            </td>
            <td style="border-right: hidden;"></td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td  colspan="7" align="left" style="border-top: hidden;"><b>GST: </b><?php echo $excel_data['partner_gst']; ?></td>
            <td style="border-top: hidden; border-right:hidden; border-left: hidden;"></td>
            <td colspan="7" style="border-top: hidden; border-left: hidden;"></td>
        </tr>
    <?php if($excel_data['generated_by_wh'] == 1){ ?>
        <tr><td colspan="14" style="border-left: hidden; border-right: hidden; text-align: center;">Any discrepancy found in parts should be reported to 247around within 4 working days. After which no responsibility of part mismatch will be of 247around as we will clear it for the SF.</td></tr>
    <?php } ?>
        <tr><td colspan="14" style="border-bottom: hidden;border-right: hidden;border-left: hidden;text-align: center;"><small>This is a computer generated challan and does not need signature.</td></tr>
    </table>

<hr style="border-top: dashed 1px;">

<table id="table2">
    <tr>
        <td align="center"><h1>Declaration of GST Non-Enrollment</td>
    </tr>
    <tr>
        <td>To Whom It May Concern:</td>
    </tr>
    <tr>
        <td>
            Dear Sir / Madam,<br><br>
            We, <?php if(!empty($excel_data['sf_owner_name'])){echo $excel_data['sf_owner_name'];} ?>, <?php echo $excel_data['sf_name']; ?>, <?php echo $excel_data['sf_address']; ?> do hereby state that we are not required to get ourselves registered under the Goods and Services Tax Act, 2017 as we have the turnover below the taxable limit as specified under the Goods and Services Tax Act, 2017.
        </td>
    <tr>
        <td>
            We hereby also confirm that if during any financial year, we decide or require to register under the GST in that case, we undertake to provide all the requisite information and documents.
        </td>
    </tr>
    <tr>
        <td>We request you to treat this communication as a declaration regarding non-requirement to be registered under the Goods and Service Tax Act, 2017. 
            <br><br>
            Signature of Authorized Signatory:
            <br><br>
            <div style="border:solid  1px; width: 500px; height: 100px">
                <?php if(!empty($excel_data['signature_file'])){ ?>
                    <img style="padding: 2px;" src="<?php echo base_url();?>/247around_tmp/<?php echo $excel_data['signature_file']; ?>">
                <?php } ?>
                
            </div>
            <br>
            Name of the Authorized Signatory: <?php if(!empty($excel_data['sf_owner_name'])){echo $excel_data['sf_owner_name'];} ?><br>
            Name of Business: <?php echo $excel_data['sf_name']; ?><br>
            Date:  <?php echo date('Y-m-d'); ?>
        </td>
    </tr>
</table>