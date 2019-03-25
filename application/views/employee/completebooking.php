<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php $enable_button = TRUE; 
                if($booking_history[0]['current_status'] == _247AROUND_COMPLETED){
                    if($booking_history[0]['current_status'] == _247AROUND_COMPLETED && ($this->session->userdata('user_group') == "admin") 
                        || ($this->session->userdata('user_group') == "closure")
                        || ($this->session->userdata('user_group') == "inventory_manager" ) 
                        || ($this->session->userdata('user_group') == "developer" )  )  {
                             $enable_button = TRUE; 

                        } else {
                            $enable_button = FALSE; 
                        }
                } else {
                    $enable_button = TRUE;
                } 
                ?>
        <?php $required_sp_id = array(); $can_sp_id = array(); ?>
        <?php $isModelMandatory =0 ; $required_sp_id = array(); $can_sp_id = array(); ?>
        <?php  $flag = 0; $requestedParts = false; if(isset($booking_history['spare_parts'])){ 
            foreach ($booking_history['spare_parts'] as  $value) {
                if($value['status'] == _247AROUND_COMPLETED || $value['status'] == _247AROUND_CANCELLED){} else {
                    if($value['defective_part_required'] == 1 && $value['status'] != SPARE_PARTS_REQUESTED){
                        if(!empty($value['parts_shipped'])){
                            switch ($value['status']){
                                case SPARE_SHIPPED_BY_PARTNER:
                                case SPARE_DELIVERED_TO_SF:
                                case DEFECTIVE_PARTS_REJECTED:
                                case DEFECTIVE_PARTS_PENDING:
                                    $flag = 1; 
                                    array_push($required_sp_id, $value['id']); 
                            }
                              
                        }
                    }
                }
            
                if($value['status'] == SPARE_PARTS_REQUESTED){
                    $date1=date_create($value['date_of_request']);
                    $date2=date_create(date('Y-m-d'));
                    $diff=date_diff($date1,$date2);
                    $d = $diff->format("%R%a days");
            
                    if($diff->format("%R%a days") < 15){
                       $requestedParts = true;
                    } else{
                        array_push($can_sp_id, array('part_name' => $value['parts_requested'], "part_id" => $value['id']));
                    }
                } 
            }
            
            }?>
        <center><?php if($requestedParts) { ?><span style="color:red; font-weight: bold;" ><?php echo UNABLE_COMPLETE_BOOKING_SPARE_MSG;?></span><?php } ?></center>
        <center><?php if(!$enable_button) { ?><span style="color:red; font-weight: bold;" ><?php echo CAN_NOT_ALLOW_RE_COMPLETE_BOOKING_TEXT;?></span><?php } ?></center>
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Complete Booking <span class="pull-right"><input id="enable_change_unit" type="checkbox" onchange="update_brand_details()" name="enable_change_unit"> <span>Change Brand Details</span></span></div>
            <div class="panel-body">
                <?php
                    if (isset($booking_history[0]['current_status'])) {
                        if ($booking_history[0]['current_status'] == "Completed") {
                    	$status = "1";
                        } else {
                    	$status = "0";
                        }
                    } else {
                        $status = "1";
                    }
                    ?>
                <form name="myForm" class="form-horizontal" id ="booking_form"  action="<?php echo base_url() ?>employee/booking/process_complete_booking/<?php echo $booking_id; ?>/<?php echo $status; ?>"  method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $booking_history[0]['service_center_closed_date']; ?>" name="service_center_closed_date">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="booking_id" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name ="partner_type" id="partner_type" />
                                        <input type="hidden" name ="booking_id" id="booking_id" value="<?php echo $booking_history[0]['booking_id']; ?>" />
                                        <input type="hidden" id="change_appliance_details" name="change_appliance_details" value="0" />
                                        <input type="hidden" id= "user_id" name="user_id" value='<?php echo $booking_history[0]['user_id']; ?>' />
                                        <input type="text" class="form-control"  id="booking_id" name="booking_id" value = "<?php
                                            if (isset($booking_history[0]['booking_id'])) {
                                            echo $booking_history[0]['booking_id'];
                                            }
                                            ?>" readonly="readonly">
                                        <input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php echo $flag;?>" />
                                        <input type="hidden" id="sp_required_id" name="sp_required_id" value='<?php echo json_encode($required_sp_id,TRUE); ?>' />
                                        <input type="hidden" name="can_sp_required_id" value='<?php echo json_encode($can_sp_id,TRUE); ?>' />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php
                                            if (isset($booking_history[0]['name'])) {
                                                echo $booking_history[0]['name'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                     <div class="col-md-6">
                                        <input type="hidden" class="form-control" id="appliance_id" name="appliance_id" value = "<?php
                                            if (isset($booking_history[0]['service_id'])) {
                                                echo $booking_history[0]['service_id'];
                                            }
                                            ?>" readonly="readonly">
                                       
                                </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City</label>
                                    <div class="col-md-6">
                                        <select type="text" disabled="disabled" class="form-control"  id="booking_city" name="city" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['city'])) {
                                                echo $booking_history[0]['city'];
                                                }
                                                ?>" selected="selected" ><?php
                                                if (isset($booking_history[0]['city'])) {
                                                	    echo $booking_history[0]['city'];
                                                	}
                                                	?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group <?php
                                    if (form_error('service_id')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label for="service_name" class="col-md-4">Appliance</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" disabled="disabled"  class="form-control"  id="service_id" name="service_id" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['service_id'])) {
                                                echo $booking_history[0]['service_id'];
                                                }
                                                ?>" selected="selected" ><?php
                                                if (isset($booking_history[0]['services'])) {
                                                	    echo $booking_history[0]['services'];
                                                	}
                                                	?></option>
                                        </select>
                                    </div>
                                </div>
                                <?php if(isset($booking_history[0]['onlinePaymentAmount'])) { ?>
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Customer Paid Through Paytm</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="customer_paid_through_paytm" name="customer_paid_through_paytm" value = "<?php
                                            if (isset($booking_history[0]['onlinePaymentAmount'])) {
                                                echo $booking_history[0]['onlinePaymentAmount'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <?php } else { ?>
                                <input type="hidden" class="form-control" id="customer_paid_through_paytm" name="customer_paid_through_paytm" value = "0" readonly="readonly">
                                <?php } ?>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php
                                            if (isset($booking_history[0]['booking_primary_contact_no'])) {
                                            echo $booking_history[0]['booking_primary_contact_no'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no']; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Pincode</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_pincode" name="booking_pincode" value = "<?php
                                            if (isset($booking_history[0]['booking_pincode'])) {
                                                echo $booking_history[0]['booking_pincode'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source</label>
                                    <div class="col-md-6">
                                        <select type="text" disabled="disabled"  class="booking_source form-control"  id="source_code" name="source_code" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['source'])) {
                                                echo $booking_history[0]['source'];
                                                }
                                                ?>" selected="selected" disabled="disabled"><?php
                                                if (isset($booking_history[0]['source'])) {
                                                	    echo $booking_history[0]['source_name'];
                                                	}
                                                	?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_date" class="col-md-4">Booking Date</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_date" name="booking_date" value = "<?php
                                            if (isset($booking_history[0]['booking_date'])) {
                                            echo $booking_history[0]['booking_date'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <!-- row End  -->
                    <?php $k_count = 0;$count = 1; foreach ($booking_unit_details as $keys => $unit_details) { ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                            <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <div class="col-md-12 ">
                                            <select type="text" class="form-control appliance_brand appliance_change" onChange="getCategoryForService(this.id)"   disabled="disabled"   name="appliance_brand[<?php echo $keys;?>]" id="<?php echo "appliance_brand_".($keys + 1);?>" >
                                                <option selected><?php echo $unit_details['brand']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 ">
                                            <select type="text" class="form-control appliance_category appliance_change" onChange="getCapacityForCategory(this.value, this.id);" disabled="disabled" id="<?php echo "appliance_category_".($keys + 1);?>"  name="appliance_category[<?php echo $keys;?>]"  >
                                                <option selected><?php echo $unit_details['category']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (!empty($unit_details['capacity'])) { ?>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <select type="text" class="form-control appliance_capacity appliance_change" disabled="disabled"  id="<?php echo "appliance_capacity_".($keys + 1);?>"  name="appliance_capacity[<?php echo $keys;?>]" >
                                                <option selected><?php echo $unit_details['capacity']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-9">
                                    <table class="table priceList table-striped table-bordered" name="priceList" >
                                        <tr>
                                            <th style="width: 292px;">Serial Number</th>
                                            <th>Service Category</th>
                                            <th>Amount Due</th>
                                            <th>Customer Basic Charge</th>
                                            <th>Additional Charge</th>
                                            <th style="width: 121px;">Parts Cost</th>
                                            <th style="width:265px;">Status</th>
                                        </tr>
                                        <tbody>
                                            <?php
                                                $paid_basic_charges = 0;
                                                $paid_additional_charges = 0;
                                                $paid_parts_cost = 0;
                                                
                                                foreach ($unit_details['quantity'] as $key => $price) {
                                                    //print_r($price);
                                                    ?>
                                            <input type="hidden" name="b_unit_id[<?php echo $keys; ?>][]" value="<?php echo $price['unit_id'];?>" />
                                            <tr style="background-color: white; ">
                                                <td>
                                                    <?php if ($price['pod'] == "1") { ?>
                                                    <div class="form-group">
                                                        <div class="col-md-12 ">
                                                            <input type="text" style="text-transform: uppercase;" onblur="validateSerialNo('<?php echo $count;?>')" class="form-control" id="<?php echo "serial_number" . $count; ?>" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>"  value="<?php echo $price['serial_number']; ?>" placeholder = "Enter Serial Number" />
                                                            <input type="hidden" class="form-control" id="<?php echo "serial_number_pic" . $count; ?>" name="<?php echo "serial_number_pic[" . $price['unit_id'] . "]" ?>"  value="<?php echo $price['serial_number_pic']; ?>"  />
                                                            <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                            <input type="hidden" id="<?php echo "sno_required" . $count ?>" class="form-control" name="<?php echo "is_sn_file[" . $price['unit_id'] . "]" ?>" <?php if($price['is_sn_correct'] == IS_SN_CORRECT){ echo 'value="1"';} else { echo 'value="1"'; }?>   />
                                                            <input type="hidden" id="<?php echo "duplicate_sno_required" . $count ?>" class="form-control" name="<?php echo "is_dupliacte[" . $price['unit_id'] . "]" ?>" value="0"   />
                                                            <input type="file" style="margin: 10px 0px;"  id="<?php echo "upload_serial_number_pic" . $count ?>"   class="form-control" name="<?php echo "upload_serial_number_pic[" . $price['unit_id'] . "]" ?>"   />
                                                            <span style="color:red;" id="<?php echo 'error_serial_no'.$count;?>"></span>
                                                                    <?php
                                                                    if(isset($unit_details['model_dropdown']) && !empty($unit_details['model_dropdown'])){ 
                                                                        $isModelMandatory =1 ;
                                                                        ?>
                                                                        <select class="form-control model_number" id="<?php echo "model_number_" . $count ?>" name="<?php echo "model_number[" . $price['unit_id'] . "]" ?>">
                                                                            <option value="" selected="" disabled="">Model Number</option>
                                                                            <?php foreach ($unit_details['model_dropdown'] as $m) { ?>
                                                                            <option value="<?php echo $m['model_number'];?>" <?php if($m['model_number'] == $unit_details['model_number'] ){ echo 'selected="selected"';} ?> ><?php echo $m['model_number'];?></option>  
                                                                            <?php }?>
                                                                        </select>
                                                                    <?php } 
                                                                        if(!empty($price['serial_number_pic'])) {
                                                                            $price_unit=$price['unit_id'];
                                                                            $url="https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/engineer-uploads/'.$price['serial_number_pic']; ?>
                                                                            <p style="margin-top: 5px;"><a href="<?php echo $url; ?>" class="<?php if($price['is_sn_correct'] == IS_SN_CORRECT){ $containsWrongSerialNumber = 1; echo "text-danger ";}?> target="_blank">SF Serial Number Pic</a></p>
                                                                             <?php
                                                                        }
                                                                     ?>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td ><span id="<?php echo "price_tags".$count;?>"><?php echo $price['price_tags'] ?></span>
                                                    <input type="hidden"  id="<?php echo "booking_unit_details".$count;?>" value="<?php echo $price['unit_id'] ?>" />
                                                 <input type="hidden" name="<?php echo "price_tags[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['price_tags'];?>">
                                                </td>
                                                <td id="<?php echo "amount_due".$count; ?>"><?php echo $price['customer_net_payable']; ?></td>
                                                <td>  
                                                    <?php  if($price['product_or_services'] != "Product"){  ?>
                                                    <input  id="<?php echo "basic_charge".$count; ?>" type="<?php  if (($price['product_or_services'] == "Service" 
                                                        && $price['customer_net_payable'] == 0) ){ echo "hidden";} ?>" class="form-control cost"  name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "<?php
                                                        $paid_basic_charges += $price['customer_paid_basic_charges'];
                                                        if (!empty($price['customer_paid_basic_charges'])) {
                                                        echo $price['customer_paid_basic_charges'];
                                                        } else {
                                                        echo "0";
                                                        }
                                                        ?>">
                                                    <?php } ?>
                                                </td>
                                                <td>  <input id="<?php echo "extra_charge".$count; ?>"  type="<?php  if ($price['product_or_services'] == "Product") { echo "hidden";} else { echo "text";} ?>" class="form-control cost"  name="<?php echo "additional_charge[" . $price['unit_id'] . "]" ?>"  value = "<?php
                                                    $paid_additional_charges += $price['customer_paid_extra_charges'];
                                                    if (!empty($price['customer_paid_extra_charges'])) {
                                                    echo $price['customer_paid_extra_charges'];
                                                    } else {
                                                    echo "0";
                                                    }
                                                    ?>">
                                                </td>
                                                <td>  
                                                    <?php if($price['product_or_services'] != "Service"){  ?>
                                                    <input  id="<?php echo "basic_charge".$count; ?>" type="<?php if ($price['product_or_services'] == "Product"
                                                        && $price['customer_net_payable'] > 0){ echo "text"; } 
                                                        else { echo "hidden";}?>" class="form-control cost" 
                                                        name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "<?php
                                                            $paid_basic_charges += $price['customer_paid_basic_charges'];
                                                            if (!empty($price['customer_paid_basic_charges'])) {
                                                            echo $price['customer_paid_basic_charges'];
                                                            } else {
                                                            echo "0";
                                                            }
                                                            ?>">
                                                    <?php } ?>
                                                    <input id="<?php echo "parts_cost".$count; ?>"  type="<?php if($price['product_or_services'] != "Service"){ 
                                                        if ($price['product_or_services'] == "Product" && $price['customer_net_payable'] == 0) { 
                                                            echo "text";} else { echo "hidden";} } else { echo "text";}?>" 
                                                        class="form-control cost" 
                                                        name="<?php echo "parts_cost[" . $price['unit_id'] . "]" ?>"  value = "<?php
                                                            $paid_parts_cost += $price['customer_paid_parts'];
                                                            if (!empty($price['customer_paid_parts'])) {
                                                            echo $price['customer_paid_parts'];
                                                            } else {
                                                            echo "0";
                                                            }
                                                            ?>" >
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group ">
                                                                <div class="col-md-10">
                                                                    <div class="radio">
                                                                        <label>
                                                                        <input type="radio" class="<?php echo "completed_".$count."_".$keys;?>" id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
                                                                            if ($price['booking_status'] == "Completed") {
                                                                                echo "checked";
                                                                            }
                                                                            ?> required><?php
                                                                            if ($price['product_or_services'] == "Product") {
                                                                               echo " Delivered";
                                                                            } else {
                                                                               echo " Completed";
                                                                            }
                                                                            ?><br/>
                                                                        <input type="radio" class="<?php echo "cancelled_".$count."_".$keys;?>" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
                                                                            if ($price['booking_status'] == "Cancelled") {
                                                                                echo "checked";
                                                                            }
                                                                            ?>  required><?php
                                                                            if ($price['product_or_services'] == "Product") {
                                                                               echo " Not Delivered";
                                                                            } else {
                                                                               echo " Not Completed";
                                                                            }
                                                                            ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                                $count++;
                                                $k_count++;
                                                }
                                                ?>
                                            <?php foreach ($prices[$keys] as $index => $value) { ?>
                                            <tr style="background-color:   #bce8f1; color: #222222;">
                                                <td> <?php if ($value['pod'] == "1") { ?>
                                                    <input type="text" class="form-control" onblur="validateSerialNo('<?php echo $count;?>')"  id="<?php echo "serial_number" . $count; ?>" name="<?php echo "serial_number[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="" placeholder= "Enter Serial Number" />
                                                    <input type="hidden"  id="<?php echo "model_number" . $count; ?>" class="form-control" value="<?php echo $unit_details['model_number']; ?>"   />
                                                    <input type="hidden" class="form-control" id="<?php echo "serial_number_pic" . $count; ?>" name="<?php echo "serial_number_pic[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="" />
                                                    <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                    <?php } ?>
                                                </td>
                                                <td> <?php echo $value['service_category']; ?> </td>
                                                <input type="hidden" name="<?php echo "price_tags[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['price_tags'];?>">
                                                <td><input  type="hidden" class="form-control"   name="<?php echo "customer_net_payable[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value = "<?php echo $value['customer_net_payable']; ?>"><?php echo $value['customer_net_payable']; ?>  </td>
                                                <td>  <input  type="text" class="form-control cost"   name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value = "0.00">
                                                <td>  <input  type="text" class="form-control cost"  name="<?php echo "additional_charge[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"   value = " <?php echo "0.00"; ?>">
                                                </td>
                                                <td>  <input  type="text" class="form-control cost"   name="<?php echo "parts_cost[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value = "<?php echo "0.00"; ?>"></td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group ">
                                                                <div class="col-md-10">
                                                                    <div class="radio">
                                                                        <label>
                                                                        <input <?php echo "completed_".$count."_".$keys;?> type="radio" name="<?php echo "booking_status[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="Completed" id="<?php echo "completed_" . $value['pod'] . "_" . $count; ?>" > Completed<br/>
                                                                        <input <?php echo "Cancelled_".$count."_".$keys;?> type="radio" name="<?php echo "booking_status[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="Cancelled" id="<?php echo "cancelled_" . $value['pod'] . "_" . $count; ?>" > Not Completed
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                                $count++;
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                                    <span class="error_msg" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class ="col-md-12">
                            <?php if($booking_history[0]['is_upcountry'] == '1' 
                                && $booking_history[0]['upcountry_paid_by_customer']== '1' ){ ?>
                            <div class="form-group col-md-offset-1">
                                <label for="type" class="col-sm-2">Paid Upcountry Charges</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control cost"  name="upcountry_charges" id="upcountry_charges" value="<?php echo $upcountry_charges; ?>" placeholder="Total Price">
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <input  type="hidden" class="form-control cost"  name="upcountry_charges" id="upcountry_charges" value="0" placeholder="Total Price">
                            <?php } ?>
                            <div class="form-group col-md-offset-1">
                                <label for="type" class="col-sm-2">Total Customer Paid</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost +$upcountry_charges;; ?>" placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="rating_star" class="col-md-2">Star Rating</label>
                                <div class="col-md-4">
                                    <Select type="text" class="form-control"  name="rating_stars" value="">
                                        <option value="">Select</option>
                                        <option <?php
                                            if ($booking_history[0]['rating_stars'] == '1') {
                                                echo "selected";
                                            }
                                            ?>>1</option>
                                        <option <?php
                                            if ($booking_history[0]['rating_stars'] == '2') {
                                                echo "selected";
                                            }
                                            ?>>2</option>
                                        <option <?php
                                            if ($booking_history[0]['rating_stars'] == '3') {
                                                echo "selected";
                                            }
                                            ?>>3</option>
                                        <option <?php
                                            if ($booking_history[0]['rating_stars'] == '4') {
                                                echo "selected";
                                            }
                                            ?>>4</option>
                                        <option <?php
                                            if ($booking_history[0]['rating_stars'] == '5') {
                                                echo "selected";
                                            }
                                            ?>>5</option>
                                    </Select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <textarea class="form-control" rows="5" name="rating_comments" placeholder ="Rating Comment"><?php echo $booking_history[0]['rating_comments']; ?></textarea>
                                </div>
                                <div class="col-md-4" >
                                    <textarea class="form-control" id="admin_remarks" rows="5" name="admin_remarks" placeholder ="Admin Remarks"></textarea>
                                </div>
                                <div class="col-md-4" >
                                    <textarea class="form-control" id="sn_remarks" rows="5" name="sn_remarks" placeholder ="Serial Number Remarks"></textarea>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($booking_history[0]['partner_id'])) {echo $booking_history[0]['partner_id']; } ?>" >
                        </div>
                    </div>
                    <br>
                    <div class="form-group  col-md-12" >
                        <?php if($requestedParts) { ?>
                        <center style="margin-top:60px; font-weight: bold;">
                            <?php echo UNABLE_COMPLETE_BOOKING_SPARE_MSG;?>
                        </center>
                        <?php } else { ?>
                        <center>
                            <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $booking_history[0]['user_id']; ?>">
                            <?php if($enable_button){
                            ?>
                            <input type="submit" id="submitform" onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>', '<?php echo $k_count; ?>')" class="btn btn-info" value="Complete Booking">
                            <?php } else {
                                echo "<p style='color:red; '>".CAN_NOT_ALLOW_RE_COMPLETE_BOOKING_TEXT."</p>";
                            }?>
                        </center>
                        <?php } ?>
                    </div>
            </div>
            </form>
            <!-- end Panel Body  -->
        </div>
    </div>
</div>
</div>
<script>
    $(".booking_source").select2();
    $(".model_number").select2();
</script>
<script>
    $("#service_id").select2();
    $("#booking_city").select2();
    var brandServiceUrl =  '<?php echo base_url();?>/employee/booking/getBrandForService/';
    var categoryForServiceUrl = '<?php echo base_url();?>/employee/booking/getCategoryForService/';
    var CapacityForCategoryUrl = '<?php echo base_url();?>/employee/booking/getCapacityForCategory/';
    
    
    $(document).ready(function () {
    //called when key is pressed in textbox
    $(".cost").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
    //display error message
    $(".error_msg").html("Digits Only").show().fadeOut("slow");
    return false;
     }
    });
    });
    
    
    $(document).on('keyup', '.cost', function (e) {
    
    var price = 0;
    $("input.cost").each(function () {
     price += Number($(this).val());
    
    });
    
    
    $("#grand_total_price").val(price);
    });
    
    function onsubmit_form(upcountry_flag, number_of_div) { 
    
    var flag = 0;
    var div_count = 0;
    var is_completed_checkbox = [];
    var serial_number_tmp = [];
    var prediv = -1;
    $(':radio:checked').each(function(i) {
        div_count = div_count + 1;
    
        //console.log($(this).val());
        var div_no = this.id.split('_');
        is_completed_checkbox[i] = div_no[0];
        if (div_no[0] === "completed") {
            //if POD is also 1, only then check for serial number.
            if (div_no[1] === "1") {
                
                var completedRadioButton = document.getElementById(this.id);
                    
                var className = completedRadioButton.className;
                var appdiv = Number(className.split('_')[2]);
                var serial_number = $("#serial_number" + div_no[2]).val();
                if(prediv !== appdiv){
                      
                    prediv = appdiv;
                    serial_number_tmp.push(serial_number);
                }
               
    
                if (serial_number === "") {
                    alert("Please Enter Serial Number");
                    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                    flag = 1;
                }
    
                if (serial_number === "0") {
                    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                    flag = 1;
                }
                if($('#sno_required'+ div_no[2]).val() === '1' && !$('#sn_remarks').val()){
                     alert('Please Correct Serial Number or Entered Remarks, Why We Should go With Wrong Serial Number');
                     flag = 1;
                }
    
                //If Serial Number Invalid then serial number image should be mendatory
//                var requiredPic = $('#sno_required'+ div_no[2]).val();
//                    if(requiredPic === '1'){
                        if( document.getElementById("upload_serial_number_pic"+div_no[2]).files.length === 0 ){
                            var serialnumberpic_prev=$('#serial_number_pic'+div_no[2]).val();
                            if(serialnumberpic_prev == ''){
                                alert('Please Attach Serial Number image');
                                document.getElementById('upload_serial_number_pic' + div_no[2]).style.borderColor = "red";
                                flag = 1;
                            }
                         }
                    //}
                   
                    var duplicateSerialNo = $('#duplicate_sno_required'+ div_no[2]).val();
                    if(duplicateSerialNo === '1'){
                        alert('<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>');
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        $("#error_serial_no" +div_no[2]).html('<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>');
                        flag = 1;
                    }
            }
    
                <?php
                if($isModelMandatory){
                    ?>
                    if($("#model_number_"+div_no[2]).length == 1){
                        var modelNumber = $('#model_number_'+div_no[2]).val();
                        if(modelNumber == null){
                            alert("Please Select Model number");
                            flag = 1;
                        }
                    }
                    <?php
                }
                ?>
            var amount_due = $("#amount_due" + div_no[2]).text();
            var basic_charge = $("#basic_charge" + div_no[2]).val();
            var additional_charge = $("#extra_charge" + div_no[2]).val();
            var parts_cost = $("#parts_cost" + div_no[2]).val();
            if (Number(amount_due) > 0) {
                var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
                if (Number(total_sf) === 0) {
                    alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                    flag = 1;
                }
                else if(Number(total_sf) < 10){
                    admin_remarks = $("#admin_remarks").val();
                    if(!admin_remarks){
                        alert("Please Add remarks");
                        flag = 1;
                    }
                }
            }
        } else {
            var cancelled_price_tags = $("#price_tags"+ div_no[2]).text();
            var unit_id = $("#booking_unit_details"+ div_no[2]).val();
            if(cancelled_price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS; ?>'){
                <?php $required_sp_id1 = array(); if(isset($booking_history['spare_parts'])){ foreach ($booking_history['spare_parts'] as  $value) {
                    if($value['status'] == _247AROUND_COMPLETED || $value['status'] == _247AROUND_CANCELLED){} else {
                        if($value['status'] != SPARE_PARTS_REQUESTED){
                            
                            if(!empty($value['parts_shipped'])){
                                switch ($value['status']){
                                    case SPARE_SHIPPED_BY_PARTNER:
                                    case SPARE_DELIVERED_TO_SF:
                                    case DEFECTIVE_PARTS_REJECTED:
                                    case SPARE_OOW_SHIPPED:
                                    case DEFECTIVE_PARTS_PENDING: ?>
                                        if(unit_id === '<?php echo $value['booking_unit_details_id'];?>'){
                                            <?php 
                                            $flag = 1; 
                                            array_push($required_sp_id1, $value['id']); 
                                            ?>
                                        }
                                    <?php    
                                }

                            }
                        }
                    }
                } }?>
                $("#spare_parts_required").val('<?php echo $flag; ?>');
                $("#sp_required_id").val('<?php echo json_encode($required_sp_id1,TRUE); ?>');
                
            }
        }
    });
    
    if (Number(number_of_div) > Number(div_count)) {
        
        alert('Please Select All Services Delivered Or Not Delivered.');
        flag = 1;
        return false;
    }
    if ($.inArray('completed', is_completed_checkbox) !== -1) {
    
    } else {
        alert('Please Select atleast one Completed or Delivered checkbox.');
        flag = 1;
        return false;
    
    }
    temp = [];
    $.each(serial_number_tmp, function(key, value) {
        if ($.inArray(value, temp) === -1) {
            temp.push(value);
        } else {
            alert(value + " is a Duplicate Serial Number");
            flag = 1;
            return false;
        }
    });
    
    var is_sp_required = $("#spare_parts_required").val();
    
    if (Number(is_sp_required) === 1) {
        alert("Ship Defective Spare Parts");
    }
    
    if (Number(upcountry_flag) === 1) {
        var upcountry_charges = $("#upcountry_charges").val();
        if (Number(upcountry_charges) === 0) {
            flag = 1;
            document.getElementById('upcountry_charges').style.borderColor = "red";
            alert("Please Enter Upcountry Charges which Paid by Customer");
            return false;
        } else if (Number(upcountry_charges) > 0) {
            flag = 0;
            document.getElementById('upcountry_charges').style.borderColor = "green";
        }
    }
    var closing_remarks = $("#closing_remarks").val();
    if (closing_remarks === "") {
        alert("Please Enter Remarks");
        document.getElementById('closing_remarks').style.borderColor = "red";
        flag = 1;
        return false;
    }
//    var customer_paid_through_paytm = Number($("#customer_paid_through_paytm").val());
//        if(customer_paid_through_paytm > 0){
//        var grand_total_price = $("#grand_total_price").val();
//            
//       if(grand_total_price < customer_paid_through_paytm){
//            alert("Please fill correct amount collected from customer");
//            flag = 1;
//            return false;
//        }
//    }
    if (flag === 0) {
        return true;
    
    } else if (flag === 1) {
    
        return false;
    }
    }
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
    
        if (confirm_call == true) {
    
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);
    
                }
            });
        } else {
            return false;
        }
    
    }
</script>
<style type="text/css">
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 0px 0 0px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
    position: absolute;
    }
</style>
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
      setupFormValidation: function ()
      {
    //form validation rules
    $("#booking_form").validate({
       rules: {
    booking_status: "required",
    serial_number: "required"
       },
       messages: {
    booking_status: "Please select on of these button ",
    serial_number: " Please Enter Serial Number"
    
       },
       submitHandler: function (form) {
    form.submit();
       }
    });
      }
    }
    
    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
     JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    function update_brand_details() {
    if (document.getElementById('enable_change_unit').checked) {
        $("#change_appliance_details").val('1');
        $('.appliance_change').prop("disabled", false);
        
        var postData = {};
        postData['service_id'] = '<?php echo $booking_history[0]['service_id']; ?>';
        postData['source_code'] = '<?php echo $booking_history[0]['source'];?>';
       
        sendAjaxRequest(postData, brandServiceUrl).done(function(data) {
           
            var data1 = jQuery.parseJSON(data);
            $("#partner_type").val(data1.partner_type);
    
            $(".appliance_brand").html(data1.brand);
    
    
        });
        
    } else {
        $("#change_appliance_details").val('0');
        $('.appliance_change').prop("disabled", true);
    }
    }
    
    function getCategoryForService(div_id) {
    var postData = {};
    var div_no = div_id.split('_');
    
    postData['service_id'] = '<?php echo $booking_history[0]['service_id']; ?>';
    postData['partner_id'] = '<?php echo $booking_history[0]['partner_id']; ?>';
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    
    sendAjaxRequest(postData, categoryForServiceUrl).done(function (data) {
    console.log(data);
        if (div_id === undefined) {
            $(".appliance_category").html(data).change();
            // $(".appliance_capacity").html(data2); 
    
        } else {
    
            $("#appliance_category_" + div_no[2]).html(data).change();
            var data2 = "<option disabled></option>";
            $("#appliance_capacity_" + div_no[2]).html(data2).change();
        }
    
    });
    
    }
    
    function getCapacityForCategory(category, div_id) {
    var postData = {};
    var div_no = div_id.split('_');
    
    postData['service_id'] = '<?php echo $booking_history[0]['service_id']; ?>';
    postData['partner_id'] = '<?php echo $booking_history[0]['partner_id']; ?>';
    postData['category'] = category;
    postData['partner_type'] = $("#partner_type").val();
    postData['brand'] = $("#appliance_brand_" + div_no[2]).val();
    
    
    sendAjaxRequest(postData, CapacityForCategoryUrl).done(function (data) {
    
    
        $("#appliance_capacity_" + div_no[2]).html(data).change();
    
    });
    }
    
    function sendAjaxRequest(postData, url) {
    return $.ajax({
        data: postData,
        url: url,
        type: 'post'
    });
    }
    
    function validateSerialNo(count){
    var postData = {};
    postData['serial_number'] = $("#serial_number"+count).val();
    postData['price_tags'] = $("#price_tags"+count).text();
    postData['user_id'] = $("#user_id").val();
    postData['booking_id'] = $("#booking_id").val();
    postData['partner_id'] = $("#partner_id").val();
    postData['appliance_id'] = '<?php echo $booking_history[0]['service_id'];?>';
    
    if(postData['serial_number'] !== ''){
        $.ajax({
                type: 'POST',
                beforeSend: function(){
    
                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                });
    
                    },
                url: '<?php echo base_url() ?>employee/service_centers/validate_booking_serial_number',
                data:postData,
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if(data.code === 247){
                        $('body').loadingModal('destroy');
                       // $("#upload_serial_number_pic"+count).css('display', "none");
                        $("#error_serial_no" +count).text("");
                        $("#sno_required"+count).val('0');
                        $("#duplicate_sno_required"+count).val('0');
                    } else if(data.code === Number(<?php echo DUPLICATE_SERIAL_NO_CODE; ?>)){
                        $("#duplicate_sno_required"+count).val('1');
                        $("#error_serial_no" +count).html(data.message);
                        $('body').loadingModal('destroy');

                    } else {
                        $("#sno_required"+count).val('1');
                        $("#error_serial_no" +count).html(data.message);
                        //$("#upload_serial_number_pic"+count).css('display', "block");
                        $("#duplicate_sno_required"+count).val('0');
                        $('body').loadingModal('destroy');
                    }
                }
            });
    }
    
    
    
    }
    
</script>
