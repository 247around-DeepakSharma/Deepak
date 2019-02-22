
<style>
    h2{
        text-decoration: underline; 
        text-align:center;
        padding:1px;
        margin-top:10px; 
    } 
    p{
        font-size:18px;
    }
    table, th, td{
        border: 1px solid black;
    }
    th {
        text-align: left;
    }
</style>

<!DOCTYPE html>
<html>
    <body> 
        <?php foreach ($coueriers_declaration as $value) { ?>
            <br><br> 
            <p>
            <h2>TO WHOMSOEVER IT MAY CONCERN</h2>
        </p>
        <br><br><br> 
        <p>
            I/ We hereby confirm that the <?php echo $value['public_name']; ?> <?php echo $value['appliance_name']; ?> Spare Parts is being

            sent to <strong><?php echo $value['name']; ?> </strong>  ( Address :- <?php echo $value['address']; ?>, <?php echo $value['district']; ?> ,<?php echo $value['state']; ?>,

            Pin Code  <?php echo $value['pincode']; ?>) is for the repair purpose and not for sale. It doesn’t

            carry any commercial value.
        </p>
        <br>
        <table>
            <tr>
                <th>S.No</th>
                <th>Shipped Part</th>
                <th style="width:20%">Spare Amount</th> 
            </tr>
            <tr>
                <td>1.</td>
                <td><?php echo $value['parts_requested']; ?></td>
                <td><?php echo $value['challan_approx_value']; ?></td>
            </tr>

        </table>
        <br><br><br>
        <p>Thanking you.</p>      
        <p>Authorized Signatory.</p>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php } ?>
</body>
</html>

