
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
            <td colspan="11"><h1>Delivery Challan</h1></td>
        </tr>
        <tr>
            <td colspan="8" align="left" style="border-bottom: hidden;"><p>To,</p><?php echo $excel_data['sf_name']; ?></td>
            <td  colspan="5" align="left" style="border-bottom: hidden;"><b>Challan No: </b><?php echo $excel_data['sf_challan_no']; ?></td>
        </tr>
        <tr>
            <td  colspan="8" rowspan="2" align="left" style="border-bottom: hidden;"><b>Address:</b> <?php if(!empty($excel_data['sf_contact_person_name'])){ echo 'C/o '.$excel_data['sf_contact_person_name'].", ";} echo $excel_data['sf_address']; ?> 
          
            <?php
                if (!empty($excel_data['sf_contact_number'])) {
                    echo '<br><br><b>Contact Number : </b>' . $excel_data['sf_contact_number'];
                }
            ?>
                            
            </td>
            <td colspan="5" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo $excel_data['partner_challan_no']; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="left" style="border-bottom: hidden;"><b>Date: </b>
                <?php 
                    if (isset($excel_data['generated_by_wh']) && $excel_data['generated_by_wh'] == 1) {
                        echo date("j M Y");
                    } else {
                        echo $excel_data['date'];
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td  colspan="8" align="left"  style="<?php if(!empty($excel_data['courier_servicable_area'])){ ?>border-bottom: hidden;<?php } ?>"><b>GST: </b><?php echo $excel_data['sf_gst']; ?></td>
            <td colspan="13"  style="<?php if(!empty($excel_data['courier_servicable_area'])){ ?>border-bottom: hidden;<?php } ?>"></td>
        </tr>
        <?php
        if(!empty($excel_data['courier_servicable_area'])){
        ?>
        <tr>
            <td  colspan="13" align="left"><b>Courier Servicable Area: </b><?php echo $excel_data['courier_servicable_area']; ?></td>
        </tr>
        <?php
        }
        ?>
        
        <tr style="text-align: center;">
            <td style="text-align: center;max-width:3px;"><b>S No</b></td>
            <td style="text-align: center;max-width:40px;"><b>Part Name</b></td>
            <td style="text-align: center; max-width:30px;"><b>Part Number</b></td>
            <td  style="text-align: center; max-width:30px;"><b>Booking ID</b></td>
            <?php if(!empty($excel_data['show_consumption_reason'])){ ?>
            <td  style="text-align: center;"><b>Consumption</b></td>
            <?php } ?>
            <?php if(!empty($excel_data['show_serial_number'])){?>
            <td  style="text-align: center"><b>Model Number</b></td>
	    <td  style="text-align: center"><b>Serial Number</b></td>
            <?php } ?>
            <td style="text-align: center;max-width:10px;"><b>Qty</b></td>
            <td style="text-align: center; ;"><b>Rate</b></td>
            <td style="text-align: center; ;"><b>Taxable Amount</b></td>
            <?php  if($c_s_gst){ ?>
                <td  style="text-align: center"><b>CGST Rate</b></td>
                <td  style="text-align: center"><b>CGST Amount</b></td>
                <td style="text-align: center"><b>SGST Rate</b></td>
                <td  style="text-align: center"><b>SGST Amount</b></td>
           <?php  } else { ?>
                <td  style="text-align: center"><b>IGST Rate</b></td>
                <td  style="text-align: center"><b>IGST Amount</b></td>
           <?php } ?>
           <td style="text-align: center"><b>Total Amount</b></td>
        </tr>
        <?php
       
        $total_qty = 0;
        $total_value = $cgst_amount = $sgst_amount = $igst_amount = 0;
        foreach ($excel_data_line_item as $key => $info) {  ?>
            <tr >
                <td style="max-width:3px;"><?php echo $key +1; ?></td>
                <td style="max-width:40px;"><?php echo $info['spare_desc'] ; ?></td>
                <td style="max-width:30px;"><?php echo $info['part_number'] ; ?></td>
                <td style="max-width:30px;"><?php echo $info['booking_id'] ; ?></td>
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
                <td style="max-width:10px;"><?php echo $info['qty'];?></td>
                <td><?php echo sprintf("%.2f", $info['rate']);?></td>
                <td><?php echo sprintf("%.2f", $info['taxable_value']);?></td>
                <?php  if($c_s_gst){ ?>
                <td><?php echo sprintf("%.2f", $info['c_gst_rate']);?></td>
                <td><?php echo sprintf("%.2f", $info['c_gst_amount']);   $cgst_amount += $info['c_gst_amount']; ?></td>
                <td><?php echo sprintf("%.2f", $info['s_gst_rate']);?></td>
                <td><?php echo sprintf("%.2f", $info['s_gst_amount']); $sgst_amount += $info['s_gst_amount'];  ?></td>
                <?php } else { ?>
                    <td><?php echo sprintf("%.2f", $info['i_gst_rate']); $igst_amount += $info['i_gst_amount']; ?></td>
                <td><?php echo sprintf("%.2f", $info['i_gst_amount']);?></td>
                <?php }?>
                
                <td><?php echo sprintf("%.2f", $info['value']); $total_qty += $info['qty']; $total_value += sprintf("%.2f", $info['value']); ?></td>
            </tr>
       <?php } 
        ?>
            
        <tr >
            <td><b>Total</b></td>
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
            <td></td>
            <?php  if($c_s_gst){ ?>
            <td></td>
            <td><b><?php echo sprintf("%.2f", $cgst_amount);?></b></td>
            <td></td>
            <td><b><?php echo sprintf("%.2f", $sgst_amount);?></b></td>
            <?php } else { ?>
                <td></td>
                <td><b><?php echo sprintf("%.2f", $igst_amount);?></b></td>
            <?php }?>

            <td><b><?php echo sprintf("%.2f", $total_value);?></b></td>
        </tr>
        <tr>
            <td colspan="13" style="border-bottom: hidden; font-size: 15px">
                <p>If undelivered return to:</p>
                <b><?php echo $excel_data['partner_name']; ?>
            </td>
        </tr>
         <tr>
            <td colspan="13" align="left"><?php echo $excel_data['partner_address']; ?>
            <?php
                if (!empty($excel_data['partner_contact_number'])) {
                    echo '<br><br><b>Contact Number: </b>' . $excel_data['partner_contact_number'];
                }
            ?>
            </td>
        </tr>
        <tr>
            <td  colspan="8" align="left" style="border-top: hidden;"><b>GST: </b><?php echo $excel_data['partner_gst']; ?></td>
            <td colspan="5" style="border-top: hidden; border-left: hidden;"></td>
        </tr>
        <?php if(isset($excel_data['generated_by_wh']) && $excel_data['generated_by_wh'] == 1){ ?>
        <tr><td colspan="13" style="text-align: center;">Any discrepancy found in parts should be reported to 247around within 4 working days. After which no responsibility of part mismatch will be of 247around as we will clear it for the SF.</tr>
        <?php } ?>
        <tr><td colspan="13" style="border-bottom: hidden;border-right: hidden;border-left: hidden; text-align: center;"><small>This is a computer generated challan and does not need signature.</tr>
    </table>
</body>