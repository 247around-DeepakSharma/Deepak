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

    #table2{
        border:hidden;
        width: 960px;
        font-family: sans-serif;
        font-size: 100%;
        margin: auto;
    }
    #table2 td{
        padding:1%;
    }
</style>
<table id="table1">
    <tr>
        <td colspan="2" style="border-right: hidden;">
            <?php if($excel_data['main_company_logo']){ ?>
            <img style="padding: 2px;height: 75px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$excel_data['main_company_logo']; ?>">
            <?php } ?>
        </td>
        <td colspan="11" align="left"><h1>Delivery Challan</h1></td>
    </tr>
    <tr>
        <td colspan="13" style="border-bottom: hidden; text-align: center; font-size: 15px"><b><?php echo $excel_data['sf_name']; ?></td>
    </tr>
    <tr>
        <td colspan="7" align="left" style="border-right: hidden;"><b><?php echo $excel_data['sf_address']; ?></td>
        <td style="border-right: hidden;"></td>
        <?php if(!empty($excel_data['sf_gst'])) { ?> 
            <td colspan="5" align="right"><b>GST:</b> <?php echo $excel_data['sf_gst']; ?></td>
        <?php } ?>
    </tr>
    <tr>
        <td colspan="7" align="left" style="border-bottom: hidden;"><b><?php echo $excel_data['partner_name']; ?></td>
        <td style="border-bottom: hidden;border-right: hidden;"></td>
        <td  colspan="5" align="left" style="border-bottom: hidden;"><b>Challan No: </b><?php echo $excel_data['sf_challan_no']; ?></td>
    </tr>
    <tr>
        <td  colspan="7" rowspan="2" align="left" style="border-bottom: hidden;"><b>Address:</b> <?php echo $excel_data['partner_address']; ?></td>
        <td style="border-bottom: hidden;border-right: hidden;"></td>
        <td colspan="5" align="left" style="border-bottom: hidden;"><b>Ref No: </b><?php echo $excel_data['partner_challan_no']; ?></td>
    </tr>
    <tr>
        <td style="border-bottom: hidden;border-right: hidden;"></td>
        <td colspan="5" align="left" style="border-bottom: hidden;"><b>Date: </b><?php echo $excel_data['date']; ?></td>
    </tr>
    <tr>
        <td  colspan="7" align="left"><b>GST: </b><?php echo $excel_data['partner_gst']; ?></td>
        <td style="border-right: hidden;"></td>
        <td colspan="5"></td>
    </tr>
    <tr class="blank_row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        <td></td><td></td><td></td><td></td><td style="border-right: solid 1px;"></td>
    </tr>
    <tr  style="text-align: center;">
        <td   style="text-align: center"><b>S No</b></td>
        <td colspan="3" style="text-align: center"><b>Description</b></td>
        <td colspan="2" style="text-align: center"><b>Part Number</b></td>
        <td colspan="2" style="text-align: center"><b>Qty</b></td>
        <td  colspan="3" style="text-align: center"><b>Booking ID</b></td>
        <td colspan="3" style="text-align: center"><b>Value (Rs.)</b></td>
    </tr>
    <?php
    $i = 1;
    $total_qty = 0;
    $total_value = 0;
    foreach ($excel_data_line_item as $info) {
        echo "<tr style='width:100%;text-align:center;'>	<td style='width:16.67%'  align=" . "\"center\"" . ">" . $i++ . "
							<td colspan=" . "3" . " align=" . "\"center\"" . ">" . $info['spare_desc'] . "
                                                        <td colspan=" . "2" . " align=" . "\"center\"" . ">" . $info['part_number'] . "
							<td colspan=" . "2" . " align=" . "\"center\"" . ">" . $info['qty'] . "
							<td colspan=" . "3" . " align=" . "\"center\"" . ">" . $info['booking_id'] . "
							<td colspan=" . "3" . " align=" . "\"center\"" . ">" . $info['value'] . "
					</tr>";
        $total_qty +=$info['qty'];
        $total_value +=$info['value'];
    }
    ?>
    <tr  style="font-weight: bold;">
        <td></td>
        <td style="border-left: hidden; text-align: center" colspan="5"><b>Total Qty</b></td>
        <td style="text-align: center" colspan="2"><b><?php echo $total_qty; ?></b></td>
        <td   style="text-align: center" colspan="2"><b>Total Amt</b></td>
        <td colspan="4" style="text-align: center"><b><?php echo $total_value; ?></b></td>
    </tr>
    <tr>
        <td style="text-align: right;padding-top: 3%; padding-bottom: 10%;padding-right: 2%" colspan="13">For <?php echo $excel_data['sf_name']; ?></td>
    </tr>
    <tr><td colspan="13" style="border-bottom: hidden;border-right: hidden;border-left: hidden; text-align: center;"><small>This is a computer generated challan and does not need signature.</tr>
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
            We, <?php echo $excel_data['sf_owner_name']; ?>, <?php echo $excel_data['sf_name']; ?>, <?php echo $excel_data['sf_address']; ?> do hereby state that we are not required to get ourselves registered under the Goods and Services Tax Act, 2017 as we have the turnover below the taxable limit as specified under the Goods and Services Tax Act, 2017.
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
            Name of the Authorized Signatory: <?php echo $excel_data['sf_owner_name']; ?><br>
            Name of Business: <?php echo $excel_data['sf_name']; ?><br>
            Date:  <?php echo date('Y-m-d'); ?>
        </td>
    </tr>
</table>