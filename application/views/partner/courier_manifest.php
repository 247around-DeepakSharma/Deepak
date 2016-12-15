<?php foreach ($courier_manifest as $value) { ?>

<div style="display:inline; height: 480px; float:left;border: 1px solid #ccc; margin-left: 10px;margin-top:20px;width: 320px;padding: 8px;">
    <table style="border: 1px solid #ddd;">
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Brand</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;"><?php echo $value['brand'];?></td>
        </tr>
         <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Job No</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;"><?php echo $value['booking_id'];?></td>
        </tr>
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Customer Name</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px; "><?php echo $value['name'];?></td>
        </tr>
         <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Address</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;height:170px;"><?php echo $value['booking_address'];?></td>
        </tr>
        <?php 
       
            $date1=date_create(date('Y-m-d', strtotime($value['initial_booking_date'])));
            $date2=date_create(date('Y-m-d H:i:s'));
            $diff=date_diff($date1,$date2);

        ?>
         <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Booking Age</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;"><?php echo $diff->days." days";?></td>
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Parts Requested</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;height:60px;"><?php echo $value['parts_requested'];?></td>
        </tr>
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Parts Shipped</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;height:60px;"><?php echo $value['parts_shipped'];?></td>
        </tr>
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Model Number</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px;"><?php echo $value['model_number'];?></td>
        </tr>
        <tr style=" display: table-row; ">
        <th style="border: 1px #f2f2f2 solid; padding: 2px;width: 160px;">Serial Number</th>
        <td style=" border: 1px #f2f2f2 solid; padding: 2px;width: 160px"><?php echo $value['serial_number'];?></td>
        </tr>
    </table>
    
</div>
<?php } ?>



<script>

    window.print();

    setTimeout(function(){
        window.close();
    }, 1);
</script>
