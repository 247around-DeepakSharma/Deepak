
<p>Agent Name:- <?php echo $this->session->userdata('employee_id'); ?></p>
<p><?php echo $file_name; ?>  :- This file was request to upload </p>
<p>Total Booking came today: <?php echo $total_booking_came_today; ?></p>
<p>Total Booking inserted today: <?php echo $total_booking_inserted; ?> </p>

<?php if (isset($count_booking_updated)) { ?>
    <p>Total Booking Updated: <?php echo $count_booking_updated; ?>
    <?php } if (isset($count_booking_not_updated)) { ?>
    <p> Booking Not Updated: <?php echo $count_booking_not_updated; ?></p>
<?php } ?>
<br/>
<div style="margin-top: 30px;">
    <table style="width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #ddd;">
        <thead>
            <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                <th style="border: 1px solid #ddd;">Type</th>
                <th style="border: 1px solid #ddd;">Order ID</th>
                <th style="border: 1px solid #ddd;">Reference date</th>
                <th style="border: 1px solid #ddd;">Delivery date</th>
                <th style="border: 1px solid #ddd;">Brand</th>
                <th style="border: 1px solid #ddd;">Model</th>
                <th style="border: 1px solid #ddd;">Product</th>
                <th style="border: 1px solid #ddd;">Product Type</th>
                <th style="border: 1px solid #ddd;">Customer Name</th>
                <th style="border: 1px solid #ddd;">Phone</th>
                <th style="border: 1px solid #ddd;">Email ID</th>
                <th style="border: 1px solid #ddd;">Address</th>
                <th style="border: 1px solid #ddd;">Pincode</th>
                <th style="border: 1px solid #ddd;">Call Type</th>
                <th style="border: 1px solid #ddd;">CRM Remarks SR_No</th>
                <th style="border: 1px solid #ddd;">Status by Around 247</th>
                <th style="border: 1px solid #ddd;">Scheduled Appointment</th>
                <th style="border: 1px solid #ddd;">Remarks by Around 247</th>
                <th style="border: 1px solid #ddd;">Status by Snapdeal</th>
                <th style="border: 1px solid #ddd;">Remarks by Snapdeal</th>
                <th style="border: 1px solid #ddd;">Final Status</th>
            </tr>
        </thead>
        <tbody >

            <?php if (isset($invalid_phone)) { ?>

                <?php foreach ($invalid_phone as $data) { ?>
                    <tr>
                        <th>Phone Number is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

                <?php }
            } ?>


            <?php if (isset($invalid_product)) { ?>

    <?php foreach ($invalid_product as $data) { ?>
                    <tr>
                        <th>Product is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

    <?php }
} ?>


            <?php if (isset($invalid_product_type)) { ?>

    <?php foreach ($invalid_product_type as $data) { ?>
                    <tr>
                        <th>Product Type is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_pincode)) { ?>

    <?php foreach ($invalid_pincode as $data) { ?>
                    <tr>
                        <th>Pincode is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_date)) { ?>

    <?php foreach ($invalid_date as $data) { ?>
                    <tr>
                        <th>Shipped/delivered is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_order_id)) { ?>

    <?php foreach ($invalid_order_id as $data) { ?>
                    <tr>
                        <th>Order ID is not valid Excel data:</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

    <?php }
} ?>

<?php if (isset($invalid_same_order_id_phone)) { ?>

    <?php foreach ($invalid_same_order_id_phone as $data) { ?>
                    <tr>
                        <th>Phone Number is same as Order Id</th>
                        <td><?php echo $data['Sub_Order_ID']; ?></td>
                        <td><?php echo $data['Referred_Date_and_Time']; ?></td>
                        <td><?php echo $data['Delivery_Date']; ?></td>
                        <td><?php echo $data['Brand']; ?></td>
                        <td><?php echo $data['Model']; ?></td>
                        <td><?php echo $data['Product']; ?></td>
                        <td><?php echo $data['Product_Type']; ?></td>
                        <td><?php echo $data['Customer_Name']; ?></td>
                        <td><?php echo $data['Phone']; ?></td>
                        <td><?php echo $data['Email_ID']; ?></td>
                        <td><?php echo $data['Customer_Address']; ?></td>
                        <td><?php echo $data['Pincode']; ?></td>
                        <td><?php echo $data['Call_Type_Installation_Table_Top_InstallationDemo_Service']; ?></td>
                        <td><?php echo $data['CRM_Remarks_SR_No']; ?></td>
                        <td><?php echo $data['Status_by_Around_247']; ?></td>
                        <td><?php echo $data['Scheduled_Appointment_DateMMDDYYYY']; ?></td>
                        <td><?php echo $data['Remarks_by_Around_247']; ?></td>
                        <td><?php echo $data['Status_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Remarks_by_Snapdeal']; ?></td>
                        <td><?php echo $data['Final_Status']; ?></td>
                    </tr>  

    <?php }
} ?>










