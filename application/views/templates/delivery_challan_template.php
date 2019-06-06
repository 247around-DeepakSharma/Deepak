
<style type="text/css">
    #table1{
        border:solid 2px;
        border-collapse: collapse;
        width: 960px;
        font-family: sans-serif;
        font-size: 100%;
        margin: auto;

        /*total cols=14*/
    }
    #table1 td{
        border: solid 1px;

        padding: 1%
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
            <td colspan="7" align="left" style="border-bottom: hidden;"><p>To,</p><?php echo $excel_data['sf_name']; ?></td>
            <td style="border-bottom: hidden;border-right: hidden;"></td>
            <td  colspan="5" align="left" style="border-bottom: hidden;"><b>Challan No: </b><?php echo $excel_data['sf_challan_no']; ?></td>
        </tr>
        <tr>
            <td  colspan="7" rowspan="2" align="left" style="border-bottom: hidden;"><b>Address:</b> <?php if(!empty($excel_data['sf_contact_person_name'])){ echo 'C/o '.$excel_data['sf_contact_person_name'].", ";} echo $excel_data['sf_address']; ?> 
          
            <?php
                if (!empty($excel_data['sf_contact_number'])) {
                    echo '<br><br><b>Contact Number : </b>' . $excel_data['sf_contact_number'];
                }
            ?>
                            
            </td>
            <td style="border-bottom: hidden;border-right: hidden;"></td>
            <td colspan="5" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo $excel_data['partner_challan_no']; ?></td>
        </tr>
        <tr>
            <td style="border-bottom: hidden;border-right: hidden;"></td>
            <td colspan="5" align="left" style="border-bottom: hidden;"><b>Date: </b><?php echo $excel_data['date']; ?></td>
        </tr>
        <tr>
            <td  colspan="7" align="left"><b>GST: </b><?php echo $excel_data['sf_gst']; ?></td>
            <td style="border-right: hidden;"></td>
            <td colspan="5"></td>
        </tr>
        <tr class="blank_row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td style="border-right: solid 1px;"></td>
        </tr>
        <tr  style="text-align: center;width:100%;">
            <td style="text-align: center"><b>S No</b></td>
            <td colspan="2" style="text-align: center;"><b>Part Name</b></td>
            <td colspan="2" style="text-align: center"><b>Part Number</b></td>
            <td colspan="2" style="text-align: center; width: 50px;"><b>Qty</b></td>
            <td colspan="4" style="text-align: center;"><b>Booking ID</b></td>
            <td colspan="3" style="text-align: center"><b>Value (Rs.)</b></td>
        </tr>
        <?php
        $i = 1;
        $total_qty = 0;
        $total_value = 0;
        foreach ($excel_data_line_item as $info) {
            echo "<tr style='width:100%;text-align:center;'>	<td style='width:16.67%;' align=" . "\"center\"" . ">" . $i++ . "
							<td style='word-break: break-all;' colspan=" . "2" . " align=" . "\"center\"" . ">" . $info['spare_desc'] . "
                                                        <td colspan=" . "2" . " align=" . "\"center\"" . ">" . $info['part_number'] . "
							<td colspan=" . "2" . " align=" . "\"center\"" . ">" . $info['qty'] . "
							<td colspan=" . "4" . " align=" . "\"center\"" . ">" . $info['booking_id'] . "
							<td colspan=" . "3" . " align=" . "\"center\"" . ">" . $info['value'] . "
					</tr>";
            $total_qty +=$info['qty'];
            $total_value +=$info['value'];
        }
        ?>
        <tr  style="font-weight: bold;">
            <td ></td>
            <td colspan="4" style="border-left: hidden; text-align: center"><b>Total Qty</b></td>
            <td colspan="2" style="text-align: center;width: 50px;"><b><?php echo $total_qty; ?></b></td>
            <td colspan="4" style="text-align: center"><b>Total Amount </b></td>
            <td colspan="3" style="text-align: center"><b><?php echo $total_value; ?></b></td>
        </tr>
        <tr>
            <td style="text-align: right;padding-top: 3%; padding-bottom: 3%;padding-right: 2%" colspan="13">For <?php echo $excel_data['sf_name']; ?></td>
        </tr>
        <tr>
            <td colspan="13" style="border-bottom: hidden; font-size: 15px">
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
            <td colspan="5"></td>
        </tr>
        <tr>
            <td  colspan="7" align="left" style="border-top: hidden;"><b>GST: </b><?php echo $excel_data['partner_gst']; ?></td>
            <td style="border-top: hidden; border-right:hidden; border-left: hidden;"></td>
            <td colspan="5" style="border-top: hidden; border-left: hidden;"></td>
        </tr>
        <tr><td colspan="13" style="border-bottom: hidden;border-right: hidden;border-left: hidden; text-align: center;"><small>This is a computer generated challan and does not need signature.</tr>
    </table>
</body>
