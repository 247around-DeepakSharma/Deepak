<style type="text/css">
    th,td{
    border: 1px #f2f2f2 solid;
    vertical-align: center;
    padding: 1px;
    }
    tr:nth-child(even) {background-color: #f2f2f2}
</style>
   <a href="https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $booking_history[0]['booking_jobcard_filename']; ?> " class='btn btn-md btn-warning  pull-right' download style="margin-right: 40px;margin-top:15px;"><i class="fa fa-download" aria-hidden="true"></i></a>
<div id="page-wrapper">
    <div class="">
        <div class="row">
            <div>
                <div class="col-md-12"><b >Booking Details:-</b></div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered" >
                        <tr>
                            <th >Name: </th>
                            <td><?php echo $booking_history[0]['name']; ?></td>
                        </tr>
                        <tr>
                             <th>Mobile: </th>
                            <td><?php echo $booking_history[0]['booking_primary_contact_no']; if(!empty($booking_history[0]['booking_alternate_contact_no'])) { echo " / ".$booking_history[0]['booking_alternate_contact_no']; } ?></td>
                        </tr>
                        <tr>
                            <th >Booking ID: </th>
                            <td><?php echo $booking_history[0]['booking_id']; ?></td>
                        </tr>
                       
                        <tr>
                            <th>Booking date: </th>
                            <td><?php echo $booking_history[0]['booking_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Timeslot: </th>
                            <td><?php echo $booking_history[0]['booking_timeslot']; ?></td>
                        </tr>
                         <tr>
                            <th>Amount Due: </th>
                            <td><?php echo $booking_history[0]['amount_due']; ?></td>
                        </tr>
                        <tr>
                            <th>Amount Paid: </th>
                            <td><?php echo $booking_history[0]['amount_paid']; ?></td>
                        </tr>
                        <tr>
                            <th>Address: </th>
                            <td><?php echo $booking_history[0]['booking_address'];?></td>
                        </tr>
                        <tr>
                            <th>City: </th>
                            <td><?php echo $booking_history[0]['city']; ?></td>
                        </tr>
                        

                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table  table-striped table-bordered">
                         
                        
                     
                         <tr>
                            <th>State: </th>
                            <td><?php echo $booking_history[0]['state']; ?></td>
                        </tr>
                        
                        <tr>
                            <th>Pincode: </th>
                            <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                        </tr>
                         <tr>
                            <th>Remarks: </th>
                            <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                        </tr>
  
                        <tr>
                            <th>Status: </th>
                            <td><?php echo $booking_history[0]['current_status']; ?></td>
                        </tr>
                        
                        <tr>
                            <th>Rescheduled Reason: </th>
                            <td><?php echo $booking_history[0]['reschedule_reason']; ?></td>
                        </tr>
                        <tr>
                            <th>Cancellation Reason: </th>
                            <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                        </tr>
                        <tr>
                            <th>Closed date: </th>
                            <td><?php echo $booking_history[0]['closed_date']; ?></td>
                        </tr>
                        <tr>
                            <th>Closing Remarks: </th>
                            <td><?php echo $booking_history[0]['closing_remarks']; ?></td>
                        </tr>
                        
                        <tr>
                            <th>Rating: </th>
                            <td><?php if(!empty($booking_history[0]['vendor_rating_stars'])){ echo $booking_history[0]['vendor_rating_stars']."/5"; } ?></td>
                        </tr>
                         <tr>
                            <th>Rating Comment: </th>
                            <td><?php if(!empty($booking_history[0]['vendor_rating_comments'])){ echo $booking_history[0]['vendor_rating_comments']."/5"; } ?></td>
                        </tr>
                       
                    </table>
                </div>

                <?php if(!empty($unit_details)) { ?>
                <div class="col-md-12" style="margin-top:20px;" >
                    <h1 style='font-size:24px;'>Appliance Details:-</h1>
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Capacity</th>
                            <th>Model Number</th>
                            <th>Serial Number</th>
                            <th>Description</th>
                            <th>Call Type</th>
                            <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                            <?php if($booking_history[0]['is_upcountry'] ==1){ ?>
                            <th>Upcountry Charges</th>
                            <?php } ?>
                            <th>Total Charges</th>
                            <?php } else { ?>
                           
                            <th>Paid Service Charges</th>
                            <th>Paid Additional Charges</th>
                            <th>Paid Parts Cost</th>
                            <?php if($booking_history[0]['is_upcountry'] ==1){ ?>
                            <th>Paid Upcountry Charges</th>
                            <?php } ?>
                            <th>Total Amount Paid</th>
                            
                            <?php } ?>
                            <th>Booking Status</th>
                        </tr>
                        <tbody>
                           <?php  foreach ( $unit_details as  $unit_detail) { ?>
                            
                            <tr>
                                <td><?php echo $unit_detail['appliance_brand']?></td>
                                <td><?php echo $unit_detail['appliance_category']?></td>
                                <td><?php echo $unit_detail['appliance_capacity']?></td>
                                <td><?php echo $unit_detail['model_number']?></td>
                                <td><?php echo $unit_detail['serial_number']?></td>
                               
                                <td><?php echo $unit_detail['appliance_description']?></td>
                                <?php if($booking_history[0]['current_status'] != "Completed"){ ?>
                                <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                <?php if($booking_history[0]['is_upcountry'] ==1){ ?>
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo "0";} 
                                else {echo $booking_history[0]['upcountry_distance'] *$booking_history[0]['partner_upcountry_rate']; } ?>
                                </td>
                                <?php } ?>
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo $unit_detail['customer_net_payable'];} 
                                else {echo ($booking_history[0]['upcountry_distance'] *DEFAULT_UPCOUNTRY_RATE) + $unit_detail['customer_net_payable']; } ?></td>
                                <?php } else {   ?>
                                <td><?php  print_r($unit_detail['price_tags']); ?></td>
                                
                                <td><?php  print_r($unit_detail['customer_paid_basic_charges']); ?></td>
                                <td><?php print_r($unit_detail['customer_paid_extra_charges']);  ?></td>
                                <td><?php print_r($unit_detail['customer_paid_parts']);  ?></td>
                                <?php if($booking_history[0]['is_upcountry'] ==1){ ?>
                                <td><?php echo $booking_history[0]['customer_paid_upcountry_charges'];?></td>
                                <?php } ?>
                                <td><?php if($booking_history[0]['upcountry_paid_by_customer'] == 0){ echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts']); } else{
                                    echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts'] + $booking_history[0]['customer_paid_upcountry_charges']);
                                }   ?></td>
                                
                                <?php }?>
                                <td><?php print_r($unit_detail['booking_status']);  ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
                
               <?php if(isset($booking_history['spare_parts'])){ ?>
                
                <div class="col-md-12">
                   
                    <h1 style='font-size:24px;'>Spare Parts Requested By SF</h1>
                    <div class="col-md-12" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <thead>
                                <tr>
                                <th >Model Number </th>
                                <th >Requested Parts </th>
                                <th >Requested Date</th>
                                <th >Invoice Image </th>
                                <th >Serial Number Image </th>
                                <th >Defective Part Image </th>
                                <th >Serial Number </th>
                                <th >Acknowledge Date BY SF </th>
                                <th >Remarks By SC </th>
                            </tr>
                            </thead>
                            
                            <tbody>
                                 <?php foreach ($booking_history['spare_parts'] as $sp){  ?>
                            <tr>
                                <td><?php echo $sp['model_number']; ?></td>
                                <td><?php echo $sp['parts_requested']; ?></td>
                                <td><?php echo $sp['create_date']; ?></td>
                                <td><?php if(!is_null($sp['invoice_pic']) ) { if($sp['invoice_pic'] != '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here to view Invoice Image</a><?php } } ?></td>
                                <td><?php if(!is_null($sp['serial_number_pic'])) { if( $sp['serial_number_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['serial_number_pic'];  ?> " target="_blank">Click Here to view Serial Number Image</a><?php } } ?></td>
                                <td><?php if(!is_null($sp['defective_parts_pic']) ) { if($sp['defective_parts_pic'] !== '0'){ ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_parts_pic'];  ?> " target="_blank">Click Here to view Defective Part Image</a><?php } } ?></td>
                                <td><?php echo $sp['serial_number']; ?></td>
                                <td><?php echo $sp['acknowledge_date']; ?></td>
                                <td><?php echo $sp['remarks_by_sc']; ?></td>
                            </tr>
                                 <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                 
                 <?php if(!is_null($sp['parts_shipped'])){ ?>
                <div class="col-md-12">
                    <h1 style='font-size:24px;'>Spare Parts Shipped</h1>
                    <div class="col-md-12" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <thead>
                                <tr>
                                    <th >Shipped Parts </th>
                                    <th >Courier Name</th>
                                    <th >AWB </th>
                                    <th >Shipped date </th>
                                    <th >EDD </th>
                                    <th >Remarks By Partner</th>
                                </tr>
                                
                            </thead>
                            <tbody>
                                <?php foreach ($booking_history['spare_parts'] as $sp){  ?>
                                <tr>
                                    <td><?php echo $sp['parts_shipped']; ?></td>
                                    <td><?php echo $sp['courier_name_by_partner']; ?></td>
                                    <td><?php echo $sp['awb_by_partner']; ?></td>
                                    <td><?php echo $sp['shipped_date']; ?></td>
                                    <td><?php echo $sp['edd']; ?></td>
                                     <td><?php echo $sp['remarks_by_partner']; ?></td>
                                   
                                </tr>
                                <?php } ?>
                            </tbody>
                          
                        </table>
                    </div>
                   
                </div>
                <?php }  if($sp['approved_defective_parts_by_partner'] == "1"){ ?>
                <div class="col-md-12">
                    <h1 style='font-size:24px;'>Defective Spare Parts Shipped By SF</h1>
                    <div class="col-md-12" style="padding-left:1px;">
                        <table class="table  table-striped table-bordered" >
                            <thead>
                                <tr>
                                 <th >Shipped Parts </th>
                                 <th >Courier Name </th>
                                 <th >AWB </th>
                                 <th >Shipped date </th>
                                 <th >Remarks By SF </th>
                                 <th >Rejected Remarks By Partner </th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php foreach ($booking_history['spare_parts'] as $sp){  ?>
                                <tr>
                                    <td><?php echo $sp['defective_part_shipped']; ?></td>
                                    <td><?php echo $sp['courier_name_by_sf']; ?></td>
                                    <td><?php echo $sp['awb_by_sf']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                    <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                    <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                </tr>
                                 <?php } ?>
                            </tbody>
                           
                        </table>
                    </div>
                    
                </div>
                <?php } }  ?>
            </div>
        </div>
    </div>
    <div style="margin-top: 40px;"></div>
</div>
   <style type="text/css">
   .spare_image {
  width: 350px;;
  height: 300px;
  background: url('<?php echo base_url()?>images/loader.gif') 50% no-repeat;
  border: 1px solid black;
  border-radius: 5px;
}
   </style>
