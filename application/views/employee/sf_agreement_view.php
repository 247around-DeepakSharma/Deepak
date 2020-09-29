<html>
    <head>
     </head>
    <body>
        <table id="parent" style=" background-image: url('<?php echo base_url('images/247_letter_head.jpg'); ?>');
                width: 595px;
                margin: 0 auto;
                background-size: contain;
                background-position: top;
                background-repeat: no-repeat;
                height: 892px;">
            <tbody>
                <tr id="top" style="height: 200px;"><td></td></tr>
                <tr style="vertical-align: top;">
                    <td>
                        <table id="content">
                            <tbody>
                                <tr id="content-top" style=" height: fit-content;vertical-align: bottom;">
                                    <td>
                            <center><b><h3>To whom it may concern</h3></b></center>
                    </td>
                </tr>
                <tr id="content-body" style="vertical-align: top;height: fit-content;">
                    <td style="padding: 25px;line-height: 24.5px;">
                        <p>This is certify that <?php echo $sf_details['name'] ?> located at <?php echo $sf_details['address'] . ' ' . $sf_details['district'] . ' ' . $sf_details['state'] . ' ' . $sf_details['pincode']; ?> is associated with us for after sale services for the product of <?php echo $sf_details['appliances'] . ' in ' . $sf_details['district']; ?>.</p>
                        <br/>
                       
                    </td>
                </tr>
                <tr style="height: fit-content;">
                    <td>
            <center>Validity: <?php //echo $financial_year; ?> </center>
                        <br/><br/></td>
    </tr>
    
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
</body>
</html>