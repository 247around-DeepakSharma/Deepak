<?php 
    if(!empty($data)){ ?>
    <p> Paytm File Uploaded SuccessFully. </p>
    <p> Upload File Name <b><?php echo $upload_file_name; ?></b></p>
    <table border = "1px solid black" cellspacing="0">
            <thead>
                <tr>
                    <th>Sheet Name</th>
                    <th>Total Bookings Came</th>
                    <th>Total Booking Inserted</th>
                    <th>Empty Contact Number</th>
                    <th>Wrong Pincode</th>
                </tr>
            </thead>
            <tbody>
       <?php foreach ($data as $value) { ?>
                <tr>
                    <td><?php echo $value['sheet_name']; ?></td>
                    <td><?php echo $value['total_bookings_came']; ?></td>
                    <td><?php echo $value['total_bookings_inserted']; ?></td>
                    <td><?php echo count($value['contact_number_empty']); ?></td>
                    <td><?php echo count($value['incorrct_pincode']); ?></td>
                    
                </tr>
        <?php } ?>
            </tbody>   
    </table>
    
    <?php foreach ($data as $value){ ?>
        <?php if(!empty($value['contact_number_empty'])) { ?>
            <p>Empty Contact Number Details Of <?php echo $value['sheet_name']; ?> Sheet</p>
            <tr><td>Order ID</td></tr>
            <?php foreach ($value['contact_number_empty'] as $val) { ?>
                <tr><td><?php echo $val['order_id'] ; ?></td></tr>
            <?php } ?>
        <?php }?>
            
        <?php if(!empty($value['incorrct_pincode'])) { ?>
        <p>Incorrect Pincode Details Of <?php echo $value['sheet_name']; ?> Sheet</p>
        <tr><td>Order ID</td></tr>
        <?php foreach ($value['incorrct_pincode'] as $val) { ?>
            <tr><td><?php echo $val['order_id'] ; ?></td></tr>
        <?php } ?>
        <?php }?>    
    <?php } ?>
            
<?php } ?>