<html>
    <head>
        <style>
            #parent{
                background-image: url('<?php echo base_url('images/247_letter_head.jpg'); ?>');
                width: 595px;
                margin: 0 auto;
                background-size: contain;
                background-position: top;
                background-repeat: no-repeat;
                height: 892px;
            }
            #top{
                height: 200px;
            }
            #parent tr{
                vertical-align: top;
            }
            #content-top{
                height: fit-content;
                vertical-align: bottom;
            }
            #content-body{
                vertical-align: top;
                height: fit-content;
            }
            #content-body td{
                padding: 25px;
                line-height: 24.5px;
            }
            #content tr{
                height: fit-content;
            }
        </style>

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
                        <p>This is certify that <?php echo $sf_deatils['name'] ?> located at <?php echo $sf_deatils['address'] . ' ' . $sf_deatils['district'] . ' ' . $sf_deatils['state'] . ' ' . $sf_deatils['pincode']; ?> is associated with us for after sale services for the product of <?php echo $sf_deatils['appliances'] . ' in ' . $sf_deatils['district']; ?>.</p>
                        <br/>
                       
                    </td>
                </tr>
                <tr style="height: fit-content;">
                    <td>
            <center>Validity: <?php echo $financial_year; ?> </center>
                        <br/><br/></td>
    </tr>
    <tr style="vertical-align: top;">
        <td style="text-align:right">
            <img style="margin-right: -257px;" src="<?php echo base_url('images/anujsign.png'); ?>" />
            <img src="<?php echo base_url('images/stamp.png'); ?>" />
        </td>
    </tr>
    <tr style="vertical-align: top;">
        <td style="text-align:right;padding-right: 30px;">Anuj Agrawal</td>
    </tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
</body>
</html>