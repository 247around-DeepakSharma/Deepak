<html>
    <head>
        <!-- Bootstrap -->
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="background: #f2f2f2;overflow-x: hidden;"> 
        <div class="page-heading">Booking Successfully Created</div>
        <div class="row" style="margin-top:30px;">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <table class="table borderless"  style="padding:10px;">
                    <tr>
                        <td colspan="6" class="table-heading"> <span style="padding:20px;"> Booking Summary </span> </td>
                    </tr>
                    <tr>
                        <th>Booking ID</th>
                        <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        <th>Date</th>
                        <td><?php echo date("d-M-Y", strtotime($booking_history[0]['booking_date'])); ?></td>
                        <th>Booking Type</th>
                        <td><?php echo $booking_history[0]['type']; ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo $booking_history[0]['name']; ?></td>
                        <th>Contact Number</th>
                        <td colspan="2"><?php echo $booking_history[0]['phone_number']; ?></td>
                    </tr>
                    <tr>
                        <th>Appliance</th>
                        <td><?php echo $booking_history[0]['services']; ?></td>
                        <th>Model Number</th>
                        <td colspan="2"><?php echo $booking_unit_details[0]['model_number']; ?></td>
                    </tr>
                    <tr>
                        <th>Assigned Service Center</th>
                        <td><?php echo $booking_history[0]['vendor_name']; ?></td>
                        <th>Service Center Address</th>
                        <td colspan="2"><?php echo $booking_history[0]['address']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-1"></div>
        </div>
    </body>  
</html>
<style>
    body{
        text-shadow: 0.5px 1px 1px;
        font-family: Verdana;
    }
    
    .borderless td , .borderless th{
        border: none !important;
        padding: 20px !important;
    }    
    
    .borderless th {
        color : grey;
    }
    
    .borderless td {
        padding : 5px;
        color : lightgray;
    }
    
    .borderless {
        background: #fff;
        padding:20px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }
    
    .table-heading{
        text-align:center;
        font-size:25px;
        color:#32b1b0 !important;
    }
    
    .page-heading{
        text-align: center;
        font-size:30px;
        margin-top:100px;
        color:#8c8c8c;
    }
</style>