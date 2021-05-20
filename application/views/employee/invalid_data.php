
<p>Agent Name:- <?php if($this->session->userdata('employee_id') === NULL){ echo _247AROUND_DEFAULT_AGENT_NAME;}else{echo $this->session->userdata('employee_id');} ?></p>
<p><?php echo $file_name; ?>  :- This file was request to upload </p>
<p>Total Booking came today: <?php if(!empty($total_booking_came_today)) { echo $total_booking_came_today ; } ?></p>
<p>Total Booking inserted today: <?php if(!empty($total_booking_inserted)) { echo $total_booking_inserted; } ?> </p>

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
                <th style="border: 1px solid #ddd;">brand</th>
                <th style="border: 1px solid #ddd;">model</th>
                <th style="border: 1px solid #ddd;">product</th>
                <th style="border: 1px solid #ddd;">product Type</th>
                <th style="border: 1px solid #ddd;">Customer Name</th>
                <th style="border: 1px solid #ddd;">phone</th>
                <th style="border: 1px solid #ddd;">Email ID</th>
                <th style="border: 1px solid #ddd;">Address</th>
                <th style="border: 1px solid #ddd;">pincode</th>
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
                        <th>phone Number is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php echo $data['call_type_installation_table_top_installationdemo_service']; ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

                <?php }
            } ?>


            <?php if (isset($invalid_product)) { ?>

    <?php foreach ($invalid_product as $data) { ?>
                    <tr>
                        <th>product is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php echo $data['call_type_installation_table_top_installationdemo_service']; ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

    <?php }
} ?>


            <?php if (isset($invalid_product_type)) { ?>

    <?php foreach ($invalid_product_type as $data) { ?>
                    <tr>
                        <th>product Type is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php echo $data['call_type_installation_table_top_installationdemo_service']; ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_pincode)) { ?>

    <?php foreach ($invalid_pincode as $data) { ?>
                    <tr>
                        <th>pincode is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php echo $data['call_type_installation_table_top_installationdemo_service']; ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_date)) { ?>

    <?php foreach ($invalid_date as $data) { ?>
                    <tr>
                        <th>Shipped/delivered is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php echo $data['call_type_installation_table_top_installationdemo_service']; ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

                <?php }
            } ?>


<?php if (isset($invalid_order_id)) { ?>

    <?php foreach ($invalid_order_id as $data) { ?>
                    <tr>
                        <th>Order ID is not valid Excel data:</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php if(isset($data['call_type_installation_table_top_installationDemo_service'])){echo $data['call_type_installation_table_top_installationDemo_service']; }?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

    <?php }
} ?>

<?php if (isset($invalid_same_order_id_phone)) { ?>

    <?php foreach ($invalid_same_order_id_phone as $data) { ?>
                    <tr>
                        <th>phone Number is same as Order Id</th>
                        <td><?php echo $data['sub_order_id']; ?></td>
                        <td><?php echo $data['referred_date_and_time']; ?></td>
                        <td><?php if(isset($data['delivery_date'])){echo $data['delivery_date'];} ?></td>
                        <td><?php echo $data['brand']; ?></td>
                        <td><?php echo $data['model']; ?></td>
                        <td><?php echo $data['product']; ?></td>
                        <td><?php echo $data['product_type']; ?></td>
                        <td><?php echo $data['customer_name']; ?></td>
                        <td><?php echo $data['phone']; ?></td>
                        <td><?php echo $data['email_id']; ?></td>
                        <td><?php echo $data['customer_address']; ?></td>
                        <td><?php echo $data['pincode']; ?></td>
                        <td><?php if(isset($data['call_type_installation_table_top_installationDemo_service'])){echo $data['call_type_installation_table_top_installationDemo_service'];} ?></td>
                        <td><?php if(isset($data['crm_remarks_sr_no'])){echo $data['crm_remarks_sr_no'];} ?></td>
                        <td><?php if(isset($data['status_by_around_247'])){echo $data['status_by_around_247'];} ?></td>
                        <td><?php if(isset($data['scheduled_appointment_datemmddyyyy'])){ echo $data['scheduled_appointment_datemmddyyyy']; } ?></td>
                        <td><?php if(isset($data['remarks_by_around_247'])){echo $data['remarks_by_around_247'];} ?></td>
                        <td><?php if(isset($data['status_by_snapdeal'])){echo $data['status_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['remarks_by_snapdeal'])){echo $data['remarks_by_snapdeal'];} ?></td>
                        <td><?php if(isset($data['final_status'])){echo $data['final_status'];} ?></td>
                    </tr>  

    <?php }
} ?>










