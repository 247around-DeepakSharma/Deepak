<html>
    <head>
        <title></title>
    </head>
    <body>

        <div bgcolor="#ffffff; " style="border: 1px solid #CCCCCC; width:980px; ">
            <div style="background-color: #2C9D9C;">
                <center>
                    <img src="https://aroundhomzapp.com/images/logo.jpg" alt="" style="border:0" width="" height="" class="CToWUd">
                </center>
            </div>
            <div style="float:left; padding: 15px; padding"><p><b>Dear Partner</b></p>
                <p style="color:#515151;"><?php echo $text; ?> </p>
            </div>
            <div style="padding: 15px;">
                <table border="1" cellspacing="0" cellpadding="1px" style="width:100%; table-layout: fixed; ">
                    <tr>
                        <td><b>Booking ID</b></td>
                    </tr>
                    <?php
                    foreach($bookings as $booking){
                    ?>
                    <tr>
                        <td><?php echo $booking; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
            
             <div style="float:left; padding: 15px;"><p><b>Best regards, <br>247around Team</b></p>

            </div>
        </div>

    </body>
</html>


