<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo GOOGLE_MAPS_API_KEY;?>"></script>
<script src="<?php echo base_url();?>js/googleScript.js"></script> 
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">

                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab1" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_content1" role="tab" data-toggle="tab" aria-expanded="true">Booking Details</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content2" role="tab" data-toggle="tab" aria-expanded="false">Appliance Details</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content3" role="tab" data-toggle="tab" aria-expanded="false">Spare Parts Details</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content4" role="tab" data-toggle="tab" aria-expanded="false">Booking History / SMS</a>
                            </li>
                            <li role="presentation" onclick="sf_tab_active()" class=""><a href="#tab_content5" role="tab" data-toggle="tab" aria-expanded="false">SF Details</a>
                            </li>
                        </ul>
                        <div id="myTabContent2" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1">
                                <table class="table  table-striped table-bordered" >
                                    <tr>
                                        <th >Name: </th>
                                        <td><?php echo $booking_history[0]['name']; ?></td>
                                        <th>Mobile: </th>
                                        <td><?php echo $booking_history[0]['booking_primary_contact_no'];
                                            if (!empty($booking_history[0]['booking_alternate_contact_no'])) {
                                                echo "/" . $booking_history[0]['booking_alternate_contact_no'];
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th >Booking ID: </th>
                                        <td><?php echo $booking_history[0]['booking_id']; ?></td>
                                        <th>Platform / Order ID: </th>
                                        <td><?php
                                            echo $booking_history[0]['partner_source'] . " / ";
                                            if (!empty($booking_history[0]['order_id'])) {  echo $booking_history[0]['order_id']; } ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Serial Number: </th>
                                        <td><?php if (isset($unit_details[0]['partner_serial_number'])) {
                                            echo $unit_details[0]['partner_serial_number'];
                                        } ?></td>
                                        <th>Call Type: </th>
                                        <td><?php echo $booking_history[0]['request_type']; ?></td>
                                    </tr>

                                    <tr>
                                       
                                        <th>Registration Date </th>
                                        <td><?php 
                                        $createDatArray = explode(' ',$booking_history[0]['create_date']);
                                        $time2 = strtotime($createDatArray[0]);
                                        $reg_date = date('d-m-Y',$time2);
                                        echo $reg_date; ?></td>
                                        <th>Booking Date </th>
                                        <td><?php 
                                        $time = strtotime($booking_history[0]['booking_date']);
                                        $booking_date = date('d-m-Y',$time);
                                        echo $booking_date ?></td>
                                        
                                    </tr>
                                    <tr>
                                        <th>City: </th>
                                        <td><?php echo $booking_history[0]['city']; ?></td>
                                        <th>State: </th>
                                        <td><?php echo $booking_history[0]['state']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Pincode: </th>
                                        <td><?php echo $booking_history[0]['booking_pincode']; ?></td>
                                        <th>Address: </th>
                                        <td><?php echo $booking_history[0]['booking_address']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Status: </th>
                                        <td><?php echo $booking_history[0]['current_status'] . " / " . $booking_history[0]['partner_internal_status']; ?></td>
                                        <th>Cancellation Reason: </th>
                                        <td><?php echo $booking_history[0]['cancellation_reason']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Booking closed date: </th>
                                        <td><?php echo $booking_history[0]['service_center_closed_date']; ?></td>
                                        <th>Rating Star </th>
                                        <td><?php if (!empty($booking_history[0]['rating_stars'])) {
                                                echo $booking_history[0]['rating_stars'] . '/5';
                                            } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Symptom (Booking Creation Time)</th>
                                        <td><?php if(!empty($symptom)){ echo $symptom[0]['symptom'];};?>
                                        </td>
                                        <th >Symptom (Booking Completion Time)</th>
                                        <td><?php if(!empty($completion_symptom)) { echo $completion_symptom[0]['symptom']; } ;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Defect</th>
                                        <td ><?php if(!empty($technical_defect)) { echo $technical_defect[0]['defect']; }?></td>
                                        <th >Solution</th>
                                        <td style="max-width:200px;"><?php if(!empty($technical_solution)) { echo $technical_solution[0]['technical_solution']; }?></td>
                                    </tr>
                                    <tr>
                                        <th>Dealer Name: </th>
                                        <td><?php if($booking_history[0]['dealer_id'] || $booking_history[0]['dealer_id']>0){ echo $booking_history[0]['dealer_name'];  } ?></td>
                                        <th>Dealer Phone Number</th>
                                        <td><?php if($booking_history[0]['dealer_id'] || $booking_history[0]['dealer_id']>0){ echo $booking_history[0]['dealer_phone_number_1'];  } ?></td>
                                    </tr>   
                                     <tr>
                                        <th>Repeat Reason: </th>
                                        <td><?php if($booking_history[0]['repeat_reason']){ echo $booking_history[0]['repeat_reason'];  } ?></td>
                                        <th>Job Card</th>
                                        <td><?php if(!empty($booking_history['0']['booking_jobcard_filename'])){ ?> 
                                                    <a target="_blank" href='<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/jobcards-pdf/".$booking_history['0']['booking_jobcard_filename']; ?>' class="btn btn-sm btn-primary btn-xs"><i class="fa fa-download" aria-hidden="true"></i></a>
                                            <?php } ?></td>
                                    </tr>
                                    <tr>
                                        <th>Remarks: </th>
                                        <td><?php echo $booking_history[0]['booking_remarks']; ?></td>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                </table>
                                <?php if(isset($booking_files) && !empty($booking_files)) { ?>
                                <table class="table  table-striped table-bordered" >
                                    <tr>
                                        <th colspan="2" style="font-size: 16px; color: #2c9d9c;">Support Files</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%;">File Type </th>
                                        <th style="width: 50%;">File</th>
                                    </tr>
                                    <?php foreach($booking_files as $key => $files) { ?>
                                    <tr>
                                        <td style="width: 50%;"><?php if(isset($files['file_description'])) echo $files['file_description']; ?></td>
                                        <td style="width: 50%;">
                                            <?php $src = base_url() . 'images/no_image.png';
                                            $image_src = $src;
                                            if (isset($files['file_name']) && !empty($files['file_name'])) {
                                                //Path to be changed
                                                $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$files['file_name'];
                                                $image_src = base_url().'images/view_image.png';
                                            }
                                            ?>
                                            <a id="a_order_support_file_<?=$key?>" href="<?php  echo $src?>" target="_blank"><img id="m_order_support_file_<?=$key?>" src="<?php  echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:10px;" /></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php } ?>
                                <table class="table  table-striped table-bordered" id="relative_holder">
                        <tr>
                            <th colspan="3" style="font-size: 16px; color: #2c9d9c;">Booking Relatives</th>
                        </tr> 
                        <tr>
                            <th style="width: 25%;">Parent </th>
                            <th style="width: 21%;">Child</th>
                            <th style="width: 21%;">Siblings</th>
                        </tr>
                        <tr>
                            <td style="width: 23%;" id="parent_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                            <td style="width: 23%;" id="child_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                            <td style="width: 23%;" id="sibling_holder"><center><img  src="<?php echo base_url(); ?>images/loadring.gif" ></center></td>
                        </tr>
                    </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content2">
                                <?php if (!empty($unit_details)) { ?>
                                    <table class="table  table-striped table-bordered">
                                        <tr>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Capacity</th>
                                            <th>Model Number</th>
                                            <th>SF Model Number</th>
                                            <th>SF Serial Number / <?php echo $booking_history[0]['public_name'] ?> Serial Number</th>
                                            <th>Description</th>
                                            <th>Purchase Date</th>
                                            <th>Call Type</th>
                                            
                                            <?php if ($booking_history[0]['current_status'] != "Completed") { ?>
                                         
                                                    <th>Upcountry Charges</th>
                                        
                                                <th>Total Charges</th>
                                            <?php } else { ?>
                                                <th>Paid Service Charges</th>
                                                <th>Paid Additional Charges</th>
                                                <th>Paid Parts Cost</th>
                                                <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                                                    <th>Paid Upcountry Charges</th>
                                                <?php } ?>
                                                    <th>Total Amount Paid</th>
                                                <?php } ?>
                                            <th>Booking Status</th>
                                                <?php if ($booking_history[0]['current_status'] === 'Completed') { ?>
                                                <th>Invoice ID</th>
                                                <?php } ?>
                                        </tr>
                                        <tbody>
                                                    <?php foreach ($unit_details as $key => $unit_detail) { ?>
                                                <tr>
                                                    <td><?php echo $unit_detail['appliance_brand'] ?></td>
                                                    <td><?php echo $unit_detail['appliance_category'] ?></td>
                                                    <td><?php echo $unit_detail['appliance_capacity'] ?></td>
                                                    <td><?php echo $unit_detail['sf_model_number'] ?></td>
                                                    <td><?php echo $unit_detail['model_number'] ?></td>
                                                    <td><?php if(!empty($unit_detail['serial_number_pic'])){?>
                                        <a target="_blank" href="<?php echo S3_WEBSITE_URL;?><?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $unit_detail['serial_number_pic'];?>"><?php echo $unit_detail['serial_number'];?></a>
                                             <?php } else { echo $unit_detail['serial_number'];} ?> / <?php echo $unit_detail['partner_serial_number']?></td>
                                                    <td><?php echo $unit_detail['appliance_description'] ?></td>
                                                    <td><?php if(!empty($unit_detail['purchase_date'])) {echo $unit_detail['purchase_date'];}?></td>
                                                        <?php if ($booking_history[0]['current_status'] != "Completed") { ?>
                                                        <td><?php print_r($unit_detail['price_tags']); ?></td>
                                                            <?php $cp = 0; if($key == 0){ $cp = 0; if ($booking_history[0]['is_upcountry'] == 1) { ?>
                                                            <td><?php
                                                                if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                                                                    echo "0";
                                                                } else if($booking_history[0]['flat_upcountry'] == 1){
                                                                    $cp =  $booking_history[0]['upcountry_to_be_paid_by_customer'];;
                                                                    echo $cp;
                                                                } else{
                                                                    $cp = round($booking_history[0]['upcountry_distance'] * $booking_history[0]['partner_upcountry_rate'], 0);
                                                                    echo $cp;
                                                                }
                                                                ?>
                                                            </td>
                                                        <?php }else{ echo "<td></td>";}}else { echo "<td></td>";} ?>
                                                        <td><?php
                                                        if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                                                            echo $unit_detail['customer_net_payable'];
                                                        }else if($key == 0) {{
                                                            echo $cp + $unit_detail['customer_net_payable'];
                                                        }}else{ echo $unit_detail['customer_net_payable'];}
                                                        ?></td>
                                                        <?php } else { ?>
                                                        <td><?php print_r($unit_detail['price_tags']); ?></td>
                                                        <td><?php print_r($unit_detail['customer_paid_basic_charges']); ?></td>
                                                        <td><?php print_r($unit_detail['customer_paid_extra_charges']); ?></td>
                                                        <td><?php print_r($unit_detail['customer_paid_parts']); ?></td>
                                                        <?php if ($booking_history[0]['is_upcountry'] == 1) { ?>
                                                            <td><?php echo $booking_history[0]['customer_paid_upcountry_charges']; ?></td>
                                                        <?php } ?>
                                                        <td><?php
                                                        if ($booking_history[0]['upcountry_paid_by_customer'] == 0) {
                                                            echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts']);
                                                        } else if($key == 0){
                                                            echo ($unit_detail['customer_paid_basic_charges'] + $unit_detail['customer_paid_extra_charges'] + $unit_detail['customer_paid_parts'] + $booking_history[0]['customer_paid_upcountry_charges']);
                                                        }else {
                                                                echo ($unit_detail['customer_paid_basic_charges'] 
                                                         + $unit_detail['customer_paid_extra_charges'] 
                                                         + $unit_detail['customer_paid_parts']);
                                                        }
                                                        ?>
                                                        </td>
                                                    <?php } ?>
                                                    <td><?php print_r($unit_detail['booking_status']); ?></td>
                                                    <?php if ($booking_history[0]['current_status'] === 'Completed') { ?>
                                                        <td><?php print_r($unit_detail['partner_invoice_id']); ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <div class="row">
                                    <center><img id="misc_charge_loader" style="width: 50px;" src="<?php echo base_url(); ?>images/loader.gif"/></center>
                                     <div class="col-md-12" id="misc_charge_div" >
                                        <h1 style='font-size:24px;margin-top: 40px;'>Miscellaneous Charge</h1>

                                        <div id="misc_charge">

                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?> 
                                
                                    <div class="text-danger">No Data Found</div>
                            <?php } ?>
                                 
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content3">
                            <?php if (isset($booking_history['spare_parts'])) {
                                $estimate_given = false;
                                $parts_shipped = false;
                                $defective_parts_shipped = FALSE; ?>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="x_panel">
                                            <div class="x_title">
                                                <h2>Spare Parts Requested By SF</h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <table class="table  table-striped table-bordered" >
                                                    <thead>
                                                        <tr>
                                                            <th >Request to Partner/Warehouse </th>
                                                            <th >Model Number </th>
                                                            <th >Original Requested Parts </th>
                                                            <th >Final Requested Parts </th>
                                                            <th >Requested Parts type </th>
                                                            <th>Requested Quantity</th>
                                                            <th>Shipped Quantity</th>
                                                            <th >Requested Date</th>
                                                            <th >Invoice Image </th>
                                                            <th >Serial Number Image </th>
                                                            <th >Defective Front Part Image </th>
                                                            <th >Defective Back Part Image </th>
                                                            <th >Serial Number </th>
                                                            <th >Acknowledge Date BY SF </th>
                                                            <th >Remarks By SC </th>
                                                            <th >Current Status</th>
                                                            <th >Spare Cancellation Reason</th>
                                                            <th>Consumption</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                                <?php foreach ($booking_history['spare_parts'] as $sp) { ?>
                                                            <tr>
                                                                <td><span id="entity_type_id"><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING){ echo "Partner";} else if(in_array($sp['partner_id'],array(15,804))){
                                                                       echo "Warehouse (Central)";     
                                                                  }else { echo $booking_history[0]['city']. " Warehouse (Micro)";} ?></span></td>
                                                                <td><?php echo $sp['model_number']; ?></td>
                                                                <td style=" word-break: break-all;"><?php if(isset($sp['original_parts'])){ echo $sp['original_parts']."<br><br><b>".$sp['original_parts_number']."</b>"; } else { echo $sp['parts_requested'].(isset($sp['part_number']) ? ("<br><br><b>".$sp['part_number']."</b>") : ''); } ?></td>
                                                                <td style=" word-break: break-all;"><?php if(isset($sp['final_spare_parts'])){ echo $sp['final_spare_parts']."<br><br><b>".$sp['part_number']."</b>"; }  ?></td>
                                                                <td><?php echo $sp['parts_requested_type']; ?></td>       
                                                                <td><?php echo $sp['quantity']; ?></td>  
                                                                <td><?php echo $sp['shipped_quantity']; ?></td> 
                                                                <td><?php echo $sp['create_date']; ?></td>
                                                                <td><?php
                                                                    if (!is_null($sp['invoice_pic'])) {
                                                                        if ($sp['invoice_pic'] != '0') {
                                                                            ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['invoice_pic']; ?> " target="_blank">Click Here</a><?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php
                                                            if (!is_null($sp['serial_number_pic'])) {
                                                                if ($sp['serial_number_pic'] !== '0') {
                                                                    ?> <a href="https://s3.amazonaws.com/bookings-collateral/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $sp['serial_number_pic']; ?> " target="_blank">Click Here</a><?php
                                                                }
                                                            }
                                                            ?>
                                                                </td>
                                                                <td><?php
                                                            if (!is_null($sp['defective_parts_pic'])) {
                                                                if ($sp['defective_parts_pic'] !== '0') {
                                                                    ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_parts_pic']; ?> " target="_blank">Click Here</a><?php
                                                                }
                                                            }
                                                            ?>
                                                                </td>
                                                             </td>
                                                                <td><?php
                                                            if (!is_null($sp['defective_back_parts_pic'])) {
                                                                if ($sp['defective_back_parts_pic'] !== '0') {
                                                                    ?> <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_back_parts_pic']; ?> " target="_blank">Click Here</a><?php
                                                                }
                                                            }
                                                            ?>
                                                                </td>
                                                                <td><?php echo $sp['serial_number']; ?></td>
                                                                <td><?php echo $sp['acknowledge_date']; ?></td>
                                                                <td><?php echo $sp['remarks_by_sc']; ?></td>
                                                                <td><?php echo $sp['status']; ?></td>
                                                                <td><?php echo $sp['part_cancel_reason'];?></td>
                                                                <td><?php if($sp['is_consumed'] == 1) { echo 'Yes';} else { echo 'No';} ?></td>
                                                            </tr>
                                                            <?php
                                                            if (!is_null($sp['parts_shipped'])) {
                                                                $parts_shipped = true;
                                                            } if (!empty($sp['defective_part_shipped'])) {
                                                                $defective_parts_shipped = TRUE;
                                                            } if ($sp['purchase_price'] > 0) {
                                                                $estimate_given = TRUE;
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                       </div>
                                    </div>
                                        <?php if ($estimate_given) { ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h2>Estimate Given</h2>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <table class="table table-striped table-bordered" >
                                                        <thead>
                                                            <tr>
                                                                <th >Estimate Given</th>
                                                                <th >Estimate Given Date </th>
                                                                <th >Estimate Invoice</th>
                                                                <th >Status </th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php foreach ($booking_history['spare_parts'] as $sp) {
                                                                if ($sp['purchase_price'] > 0) { ?>
                                                                    <tr>

                                                                        <td><?php 
                                                                        if($sp['status'] == SPARE_OOW_EST_GIVEN){
                                                                           ?>
                                                                            <input type="text" value="<?php echo $sp['purchase_price']?>" id="edit_purchase_price"
                                                                                   onchange="update_purchase_price('<?php echo $sp['id']?>','<?php echo $sp['booking_unit_details_id']?>')">
                                                                            <?php
                                                                        }
                                                                        else{
                                                                            echo $sp['purchase_price']; 
                                                                        }?></td>
                                                                        <td><?php if (!empty($sp['estimate_cost_given_date'])) {
                                                                                echo date("d-m-Y", strtotime($sp['estimate_cost_given_date']));
                                                                            } ?>
                                                                        </td>
                                                                        <td><?php if (!is_null($sp['incoming_invoice_pdf'])) {
                                                                                if ($sp['incoming_invoice_pdf'] !== '0') { ?> <a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $sp['incoming_invoice_pdf']; ?> " target="_blank">Click Here</a><?php }
                                                                            } ?>
                                                                        </td>
                                                                        <td><?php echo $sp['status']; ?></td>
                                                                    </tr>
                                                                <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div> 
                                    <?php }
                                    ?>
                                    <?php if ($parts_shipped) { ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h2>Spare Parts Shipped</h2>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="">
                                                    <table class="table  table-striped table-bordered" >
                                                        <thead>
                                                            <tr>
                                                                <th>Part Shipped By Partner/Warehouse</th>
                                                                <th >Shipped Parts </th>
                                                                <th >Courier Name</th>
                                                                <th >AWB </th>
                                                                <th >Shipped date </th>
                                                                <th >EDD </th>
                                                                <th >Remarks By Partner</th>
                                                                <th>Challan File</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['parts_shipped'])){?>
                                                                <tr>
                                                                    <td><?php if($sp['entity_type'] == _247AROUND_PARTNER_STRING) { echo "Partner";} else { echo "Warehouse";} ?></td>
                                                                    <td><?php echo $sp['parts_shipped']; ?></td>
                                                                    <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_partner'])); ?></td>
                                                                    <td><a href="javascript:void(0)" onclick="get_awb_details('<?php echo $sp['courier_name_by_partner']; ?>','<?php echo $sp['awb_by_partner']; ?>','<?php echo $sp['status']; ?>','<?php echo "awb_loader_".$sp['awb_by_partner']; ?>')"><?php echo $sp['awb_by_partner']; ?></a> 
                                            <span id=<?php echo "awb_loader_".$sp['awb_by_partner'];?> style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                                                                    <td><?php echo $sp['shipped_date']; ?></td>
                                                                    <td><?php echo $sp['edd']; ?></td>
                                                                    <td><?php echo $sp['remarks_by_partner']; ?></td>
                                                                    <td> 
                                                                        <?php  if(!empty($sp['partner_challan_file'])) { ?> 
                                                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $sp['partner_challan_file']; ?>" target="_blank">Click Here to view</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php }} ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } if ($defective_parts_shipped) { ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h2>Defective Spare Parts Shipped By SF</h2>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <table class="table  table-striped table-bordered" >
                                                        <thead>
                                                            <tr>
                                                                <th >Shipped Parts </th>
                                                                <th >Courier Name </th>
                                                                <th >AWB </th>
                                                                <th> No. Of Boxes </th>
                                                                <th> Weight</th>
                                                                <th >Courier Charge </th>
                                                                <th> Courier Invoice</th>
                                                                <th >Shipped date </th>
                                                                <th >Remarks By SF </th>
                                                                <th >Remarks By Partner </th>
                                                                <th>Challan File</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($booking_history['spare_parts'] as $sp) { if(!empty($sp['defective_part_shipped'])){ ?>
                                                                <tr>
                                                                    <td><?php echo $sp['defective_part_shipped']; ?></td>
                                                                    <td><?php echo ucwords(str_replace(array('-','_'), ' ', $sp['courier_name_by_sf'])); ?></td>
                                                                            <?php
                                                                            $spareStatus = DELIVERED_SPARE_STATUS;
                                                                            if (!$sp['defactive_part_received_date_by_courier_api']) {
                                                                                $spareStatus = $sp['status'];
                                                                            }
                                                                            ?>
                                                                    <td><a href="javascript:void(0)" onclick="get_awb_details('<?php echo $sp['courier_name_by_sf']; ?>','<?php echo $sp['awb_by_sf']; ?>','<?php echo $spareStatus; ?>','<?php echo "awb_loader_".$sp['awb_by_sf']; ?>')"><?php echo $sp['awb_by_sf']; ?></a> 
                                            <span id=<?php echo "awb_loader_".$sp['awb_by_sf'];?> style="display:none;"><i class="fa fa-spinner fa-spin"></i></span></td>
                                                                    
                                                                    <td><?php
                                                                                if (!empty($sp['awb_by_sf']) && !empty($courier_boxes_weight_details['box_count'])) {
                                                                                    echo $courier_boxes_weight_details['box_count'];
                                                                                }
                                                                          ?></td>
                                                                    <td><?php
                                                                            if (!empty($sp['awb_by_sf'])) {
                                                                                if (!empty($courier_boxes_weight_details['billable_weight'])) {
                                                                                    $expl_data = explode('.', $courier_boxes_weight_details['billable_weight']);
                                                                                    if (!empty($expl_data[0])) {
                                                                                        echo $expl_data[0] . ' KG ';
                                                                                    }
                                                                                    if (!empty($expl_data[1])) {
                                                                                        echo $expl_data[1] . ' Gram';
                                                                                    }
                                                                                }
                                                                            }
                                                                                    ?></td>
                                                                    <td><?php echo $sp['courier_charges_by_sf']; ?></td>
                                                                    <td><a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $sp['defective_courier_receipt']; ?> " target="_blank">Click Here to view</a></td>
                                                                    <td><?php echo date('Y-m-d', strtotime($sp['defective_part_shipped_date'])); ?></td>
                                                                    <td><?php echo $sp['remarks_defective_part_by_sf']; ?></td>
                                                                    <td><?php echo $sp['remarks_defective_part_by_partner']; ?></td>
                                                                    <td> 
                                                                        <?php  if(!empty($sp['sf_challan_file'])) { ?> 
                                                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $sp['sf_challan_file']; ?>" target="_blank">Click Here to view</a>
                                                                        <?php } ?>
                                                                    </td>

                                                                </tr>
                                                            <?php } } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?> 
                                    <div class="text-danger">Spare Part Not Requested</div>
                                <?php } ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content4">
                                <div id="historyDetailsPartner"></div>
                                <div id="commentboxPartner"> </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content5">

                                   <?php if(isset($booking_history[0]['primary_contact_name'])){ ?>
                                    <table class="table table-striped table-bordered" >
                                        <tr>
                                            <th>Back Office Person</th>
                                            <?php if($this->session->userdata('user_group') != PARTNER_CALL_CENTER_USER_GROUP) { ?>
                                            <th>Mobile</th>
                                            <?php } ?>
                                            <th>Email</th>
                                            <th>Municipal Limit </th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td><?php echo $booking_history[0]['primary_contact_name'];?></td>
                                                <?php if($this->session->userdata('user_group') != PARTNER_CALL_CENTER_USER_GROUP) { ?>
                                                <td><?php echo $booking_history[0]['primary_contact_phone_1'];?></td>
                                                <?php } ?>
                                                <td><?php echo $booking_history[0]['primary_contact_email'];?></td>
                                                <td><?php if($booking_history[0]['is_upcountry'] == 1){ echo $booking_history[0]["municipal_limit"]." KM";}  ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php  }?>
                   
                    <table class="table  table-striped table-bordered">
                        <thead>
                            <th>One Way Distance </th>
                            <th>Upcountry Distance </th>
                            <th>SF District </th>
                            <th>SF Pincode </th>
                            <th>Upcountry District </th>
                            <th>Upcountry Pincode</th>
                            <th>Customer District </th>
                            <th>Customer Pincode </th>
                            <th>Get Route</th>
                        <thead>
                        <tbody>
                            <tr>
                                <td> <?php if($booking_history[0]['is_upcountry'] == 1){  ?>
                                    <?php echo round(($booking_history[0]["upcountry_distance"] + ($booking_history[0]["municipal_limit"] * 2))/2,2) . " KM"; ?>
                                <?php } ?></td>
                                <td><?php if($booking_history[0]['is_upcountry'] == 1){ echo $booking_history[0]["upcountry_distance"]." KM";} ?></td>
                                <td><?php if(isset($booking_history[0]['sc_district'])){ echo $booking_history[0]['sc_district'];}?></td>
                                <td><?php if(isset($booking_history[0]['pincode'])){ echo $booking_history[0]['pincode'];}?></td>
                                <td> <?php if(isset($dhq[0]['original_district'])){echo $dhq[0]['original_district'];}?></td>
                                <td><?php if(isset($dhq[0]['pincode'])){ echo $dhq[0]['pincode'];} ?></td>
                                <td><?php if(isset($booking_history[0]['district'])){ echo $booking_history[0]['district'];}?></td>
                                <td><?php if(isset($booking_history[0]['booking_pincode'])){ echo $booking_history[0]['booking_pincode'];}?></td>
                                <td>
                                <div class="col-md-4"> <button class="btn btn-success" onclick="GetRoute()">Get Route</button></div>
                                </td>
                            </tr>
                            <tr>
                                <?php if($booking_history[0]['is_upcountry'] == 1){  ?>  
                            <tr>
                                <td colspan="9">
                                    <div class="col-md-12">
                                        <div class="col-md-4"> <input type="hidden" class="form-control" id="txtSource" value="<?php echo $booking_history[0]['booking_pincode'].", india"; ?>"></div>
                                        <div class="col-md-4">   <input type="hidden" class="form-control" id="txtDestination" value="<?php if(isset($dhq[0]['district'])){
                                            echo $dhq[0]['pincode'].", India";}?>"></div>
                                        
                                    </div>
                                    <div class="col-md-12">
                                        <div id="dvDistance" style="display:none;"></div>
                                        <br/>
                                        <div id="dvMap" style=" height: 200px">
                                        </div>
                                        </div
                                </td>
                            </tr>
                            <?php } ?>
                            </tr>
                        </tbody>
                    </table>   
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!-- model -->
    <div id="gen_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="gen_model_title"></h4>
                </div>
                <div class="modal-body" id="gen_model_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<script>
    function update_purchase_price(spare_id,booking_unit_id){
        var price = $('#edit_purchase_price').val();
        if(price && price > 1){
            swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: true
                },
                function(){
                    $.ajax({
                    method: 'POST',
                    data: {booking_id: '<?php echo $booking_history[0]['booking_id'] ?>', vendor_id: '<?php echo $booking_history[0]['assigned_vendor_id'] ?>', amount_due: '<?php echo $booking_history[0]['amount_due'] ?>', 
                        spare_id: spare_id, updated_price: price, partner_id: '<?php echo $this->session->userdata('partner_id');?>', agent_id: '<?php echo $this->session->userdata('agent_id');?>', booking_unit_id:booking_unit_id},
                    url: '<?php echo base_url(); ?>employee/partner/update_spare_estimate_quote',
                        success: function (response) {
                            if(response){
                                alert("Price has been updated successfully");
                            }
                            else{
                                alert("Something Went Wrong please contact to admin");
                            }
                        }
                    });
                }
            );
        }
        else{
            alert("Estimate price Can not be blank or 0");
        }
    }
    function sf_tab_active(){
        <?php if($booking_history[0]['is_upcountry'] == 1){  ?>  
             setTimeout(function(){ GetRoute(); }, 1000);
        <?php } ?>
    }
    $('document').ready(function () {
        var booking_id = '<?php echo base_url() ?>partner/get_booking_life_cycle/<?php echo $booking_history[0]['booking_id'] ?>';
        $.ajax({
            type: 'POST',
            url: booking_id,
            success: function (response) {
                $('#historyDetailsPartner').html(response);
            }
        });
        
    $.ajax({
        method: 'POST',
        data: {},
        url: '<?php echo base_url(); ?>partner/get_comment_section/<?php echo $booking_history[0]['booking_id']?>/1',
        success: function (response) {
            document.getElementById("commentboxPartner").innerHTML = response;
            $("#commnet_btn").css("display", "none");
            $("#commentboxPartner").find("table tr td button").css("display", "none");
        }
    });
        get_booking_relatives();
    });
    $.ajax({
        method:'GET',
        url:'<?php echo base_url(); ?>employee/vendor/get_miscellaneous_charges/<?php echo $booking_history[0]['booking_id']?>/0/1',
        success:function(response){
           
            if(response === "Failed"){
               $("#misc_charge_loader").css('display','none');
            } else{
                $("#misc_charge_loader").css('display','none');
               $("#misc_charge_div").css('display', 'block');
               $("#misc_charge").html(response);
              
            }

        }
    });
    
    function get_awb_details(courier_code,awb_number,status,id){
        if(courier_code && awb_number && status){
            $('#'+id).show();
            $.ajax({
                method:"POST",
                data : {courier_code: courier_code, awb_number: awb_number, status: status},
                url:'<?php echo base_url(); ?>courier_tracking/get_awb_real_time_tracking_details',
                success: function(res){
                    $('#'+id).hide();
                    $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                    $('#gen_model_body').html(res);
                    $('#gen_model').modal('toggle');
                }
            });
        }else{
            alert('Something Wrong. Please Refresh Page...');
        }
    }
     function get_booking_relatives(){
            $.ajax({
                method:"POST",
                data : {},
                url:'<?php echo base_url(); ?>employee/partner/get_booking_relatives/<?php echo $booking_history[0]['booking_id']; ?>',
                success: function(res){
                    if(res){
                    $("#relative_holder").show();
                    var obj = JSON.parse(res);
                    parent_string = child_string = sibling_string = "NULL";
                    if(obj.parent){
                        parent_string = "<a href = '<?php echo base_url(); ?>partner/booking_details/"+obj.parent+"' target = '_blank'>"+obj.parent+"</a>";
                    }
                    if(obj.siblings){
                        sibling_string ="";
                        sibling_array = obj.siblings.split(",");
                        for(var i = 0;i<sibling_array.length;i++){
                            sibling_string = sibling_string+(i+1)+") <a href = '<?php echo base_url(); ?>partner/booking_details/"+sibling_array[i]+"' target = '_blank'>"+sibling_array[i]+"</a><br>";
                        }
                    }
                    if(obj.child){
                        child_string ="";
                        child_array = obj.child.split(",");
                        for(var i = 0;i<child_array.length;i++){
                            child_string = child_string+(i+1)+") <a href = '<?php echo base_url(); ?>partner/booking_details/"+child_array[i]+"' target = '_blank'>"+child_array[i]+"</a><br>";
                        }
                    }
                    $('#parent_holder').html(parent_string);
                    $('#sibling_holder').html(sibling_string);
                    $('#child_holder').html(child_string);
                }
             }
            });
        }
</script>