<html>
    <head>
    </head>
    <body>
        <table id="parent">
            <tbody>
                <tr id="top" ><td height="200"></td></tr>
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

                <img  src="<?php echo $sign; ?>" />

        </td>
    </tr>
    <tr style="vertical-align: top;">
        <td style="text-align:right;padding-right: 60px;">Anuj Agrawal</td>
    </tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
</body>
</html>