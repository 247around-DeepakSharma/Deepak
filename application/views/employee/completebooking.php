<script src="<?php echo base_url();?>js/validation_js.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<?php $str_disabled = $is_spare_requested ? "pointer-events:none;background:#eee;" : "";?>
<div id="page-wrapper" >
    <div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php
        if ($this->session->userdata('error') || $this->session->userdata('error_msg')) {
            $error_msg = ($this->session->userdata('error_msg')) ? $this->session->userdata('error_msg') : $this->session->userdata('error');
            echo '<div class="alert alert-warning alert-dismissible" role="alert" style="margin-top:10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $error_msg . '</strong>
                </div>';
        }
        ?>
        <?php $enable_button = TRUE; 
                if($booking_history[0]['current_status'] == _247AROUND_COMPLETED){
                    if($booking_history[0]['current_status'] == _247AROUND_COMPLETED && empty($is_invoice_generated)
                        && ($this->session->userdata('user_group') == _247AROUND_ADMIN) 
                        || ($this->session->userdata('user_group') == _247AROUND_CLOSURE)
                        || ($this->session->userdata('user_group') == INVENTORY_USER_GROUP ) 
                        || ($this->session->userdata('user_group') == INVENTORY_USER_GROUP_HOD )
                        || ($this->session->userdata('user_group') == _247AROUND_DEVELOPER )
                        || ($this->session->userdata('user_group') == _247AROUND_RM ))  {
                             $enable_button = TRUE; 

                        } else {
                            $enable_button = FALSE; 
                        }
                } else {
                    $enable_button = TRUE;
                } 
                ?>
        <?php $isModelMandatory =0 ; $dop_mendatory=0; /* $required_sp_id = array(); */ $can_sp_id = array(); ?>
        <?php $is_spare_pending_for_acknowledge = false; $flag = 0; $requestedParts = false; if(isset($booking_history['spare_parts'])){ 
            foreach ($booking_history['spare_parts'] as  $value) {
                if($value['status'] == _247AROUND_COMPLETED || $value['status'] == _247AROUND_CANCELLED){} else {
                    if($value['defective_part_required'] == 1 && $value['status'] != SPARE_PARTS_REQUESTED){
                        if(!empty($value['parts_shipped'])){
                            switch ($value['status']){
                                case SPARE_SHIPPED_BY_PARTNER:
                                case SPARE_DELIVERED_TO_SF:
                                case DEFECTIVE_PARTS_REJECTED:
                                case DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE:
                                case DEFECTIVE_PARTS_PENDING:
                                    $flag = 1; 
                                  //  array_push($required_sp_id, $value['id']); 
                                    break;
                                case _247AROUND_COMPLETED:
                                if(empty($value['defective_part_shipped_date'])) {
                                    $flag = 1; 
                                }
                                break;
                                case DEFECTIVE_PARTS_SHIPPED:
                                    $is_spare_pending_for_acknowledge = false;
                                break;    
                            }
                              
                        }
                    }
                }
            
                if(empty($value['parts_shipped']) && empty($value['shipped_date']) && $value['status'] != _247AROUND_CANCELLED && $value['status'] != _247AROUND_COMPLETED){
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
        
        
        
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Complete Booking <span class="pull-right"><input id="enable_change_unit" type="checkbox" onchange="update_brand_details()" name="enable_change_unit"> <span>Change Brand Details</span></span></div>
            <div class="panel-body">
                <?php if(!in_array($this->session->userdata['user_group'], [_247AROUND_ADMIN, _247AROUND_CLOSURE, _247AROUND_RM])) { ?>
                <div class="alert alert-warning">
                    <span style="font-weight:bold;">You don't have permission to complete booking.</span>
                </div>
                <?php  } ?>
                <?php if($requestedParts || !$enable_button || $is_invoice_generated || $is_spare_pending_for_acknowledge) { ?>
            <div class="alert alert-warning">
                <span><?php if($requestedParts) { ?><span style="color:red; font-weight: bold;" ><?php echo UNABLE_COMPLETE_BOOKING_SPARE_MSG;?></span><?php } ?></span>
                <span><?php if(!$enable_button) { ?><span style="color:red; font-weight: bold;" ><?php echo CAN_NOT_ALLOW_RE_COMPLETE_BOOKING_TEXT;?></span><?php } ?></span>
                <span><?php if($is_spare_pending_for_acknowledge) { ?> <span style="color:red; font-weight: bold;" ><?php echo SPARE_PENDING_FOR_ACKNOWLEDGE_MSG;?></span><?php }?></span>
                <div style="font-weight: bold;">
                <?php if($requestedParts) { ?>
                <?php } elseif(!empty ($is_invoice_generated)) { ?>
                     <span style="font-weight: bold;">
                    <?php echo "<p style='color:red; '>".UNABLE_TO_COMPLETE_BOOKING_INVOICE_GENERATED_MSG."</p>";?>
                </span>
                <?php } else { ?>
                <?php } ?>
                </div>
            </div>
                <?php } ?>
                
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
                                        <input type="hidden" name ="booking_primary_id" id="booking_primary_id" value="<?php echo $booking_history[0]['booking_primary_id']; ?>" />
                                        <input type="hidden" id="change_appliance_details" name="change_appliance_details" value="0" />
                                        <input type="hidden" id= "user_id" name="user_id" value='<?php echo $booking_history[0]['user_id']; ?>' />
                                        <input type="text" class="form-control"  id="booking_id" name="booking_id" value = "<?php
                                            if (isset($booking_history[0]['booking_id'])) {
                                            echo $booking_history[0]['booking_id'];
                                            }
                                            ?>" readonly="readonly">
                                        <input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php echo $flag;?>" />
<!--                                        <input type="hidden" id="sp_required_id" name="sp_required_id" value='<?php // echo json_encode($required_sp_id,TRUE); ?>' />-->
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
                                    <?php if($c2c){ ?>
                                    <div class="col-md-2">
                                        <?php $booking_primary_id = (!empty($booking_history[0]['booking_primary_id']) ? $booking_history[0]['booking_primary_id'] : ""); ?>
                                        <button type="button" onclick="outbound_call(<?php echo $booking_history[0]['booking_primary_contact_no']; ?>, <?php echo $booking_primary_id; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                                    </div>
                                    <?php }?>
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
                                            if (!empty($booking_history[0]['booking_date']) && $booking_history[0]['booking_date'] != '0000-00-00') {echo date("d-m-Y", strtotime($booking_history[0]['booking_date']));
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <?php
                            $paid_basic_charges = 0;
                            $paid_additional_charges = 0;
                            $paid_parts_cost = 0;
                    ?>
                    <!-- row End  -->
                    <?php $k_count = 0;$count = 1; foreach ($booking_unit_details as $keys => $unit_details) { ?>
                    <?php $selected_dop = $this->miscelleneous->get_formatted_date_dmy($unit_details['sf_purchase_date']);  ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                            <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3" style="margin-left:15px;">
                                    <div class="form-group">
                                        <label >Category</label>
                                        <select type="text" class="form-control appliance_category appliance_change" onChange="getCapacityForCategory(this.value, this.id);" disabled="disabled" id="<?php echo "appliance_category_".($keys + 1);?>"  name="appliance_category[<?php echo $keys;?>]"  >
                                            <option selected><?php echo $unit_details['category']; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <?php if (!empty($unit_details['capacity'])) { ?>
                                <div class="col-md-3" style="margin-left:15px;">
                                    <div class="form-group">
                                        <label style="margin-left:8%;">Capacity</label>
                                        <select type="text" class="form-control appliance_capacity appliance_change" disabled="disabled"  id="<?php echo "appliance_capacity_".($keys + 1);?>"  name="appliance_capacity[<?php echo $keys;?>]" >
                                            <option selected><?php echo $unit_details['capacity']; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <?php } 
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                         <label style="margin-left:8%;">Purchase Date</label>
                                         <div class="input-group input-append date" style="width: 150px;margin-left: 14px;">
                                                <input autocomplete="off" onkeydown="return false" onchange="update_dop_for_unit('<?php echo $keys?>')"  id="<?php echo "dop_".$keys?>" class="form-control dop" placeholder="Purchase Date" name="dop[]" type="text" value="<?php echo $selected_dop ?>">
                                                        <span class="input-group-addon add-on date-picker" onclick="dop_calendar('<?php echo "dop_".$keys?>')"><span class="glyphicon glyphicon-calendar"></span></span>
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="margin-left:8%;">Purchase Invoice</label>
                                        <div class="input-group input-append" style="margin-left: 14px;">
                                            <input type="file" name="sf_purchase_invoice" class="form-control purchase-invoice"
                                                   onchange="update_purchase_invoice_for_unit('<?php echo $keys?>')"
                                                   id="<?php echo "purchase_invoice_".$keys?>" value="<?= (!empty($unit_details['quantity'][0]['sf_purchase_invoice']) ? $unit_details['quantity'][0]['sf_purchase_invoice'] : ""); ?>">
                                             <?php $src = base_url() . 'images/no_image.png';
                                            $image_src = $src;
                                            if (!empty($unit_details['quantity'][0]['sf_purchase_invoice'])) {
                                                //Path to be changed
                                                $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/purchase-invoices/".$unit_details['quantity'][0]['sf_purchase_invoice'];
                                                //$image_src = base_url().'images/view_image.png';
                                            }
                                            ?>
                                            <a id="a_order_support_file_0" href="<?php  echo $src?>" target="_blank"><small style="white-space:nowrap;"><?= (!empty($unit_details['quantity'][0]['sf_purchase_invoice']) ? "View Purchase Invoice Pic" : ""); ?></small></a>
                                            
                                       </div>
                                    </div>
                                </div>
                                <div class="col-md-1" style="float:right;margin-top:25px;margin-right:20px;">
                                    <div class="form-group">
                                        <?php
                                            $redirect_url = base_url()."employee/booking/get_complete_booking_form/".$booking_history[0]['booking_id'];
                                        ?>
                                        <a class="btn btn-primary" style="padding: 5px 20px;float:right;" href="<?php echo base_url(); ?>employee/booking/get_edit_request_type_form/<?php echo urlencode(base64_encode($booking_history[0]['booking_id'])); ?>/<?php echo urlencode(base64_encode($redirect_url))?>">Go To Step 1</a>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table priceList table-striped table-bordered" name="priceList" width="100%">
                                        <tr>
                                            <th width="20%">Serial Number</th>
                                            <th width="14%">Service Category</th>
                                            <th width="12%">Amount Due</th>
                                            <th width="12%">Customer Basic Charge</th>
                                            <th width="12%">Additional Charge</th>
                                            <th width="13%">Parts Cost</th>
                                            <th width="17%">Status</th>
                                        </tr>
                                        <tbody>
                                            <?php
                                                
                                                foreach ($unit_details['quantity'] as $key => $price) { ?>
                                                <input type="hidden" name="is_sf_purchase_invoice_required_<?php echo $keys; ?>" id="is_sf_purchase_invoice_required_<?php echo $keys; ?>" value="<?= (!empty(array_filter($unit_details['quantity'], function ($quantity) {return ($quantity['invoice_pod'] == 1);})) ? 1 : 0); ?>" data-priceTags='<?php echo json_encode(array_column(array_filter($unit_details['quantity'], function ($quantity) {return ($quantity['invoice_pod'] == 1); }), 'price_tags')); ?>'>

                                                    <input type="hidden" value="<?php echo count($unit_details['quantity']) ?>" id="count_line_item_<?php echo $keys; ?>">
                                            <input type="hidden" name="b_unit_id[<?php echo $keys; ?>][]" value="<?php echo $price['unit_id'];?>" />
                                            <tr style="background-color: white; ">
                                                <td>
                                                    <input type="hidden" name="<?php echo "appliance_dop[" . $price['unit_id'] . "]" ?>" 
                                                            class="<?php echo "unit_dop_".$keys."_".$key;?>" value="<?php if(isset($unit_details['quantity'][0]['sf_purchase_date'])){  echo $unit_details['quantity'][0]['sf_purchase_date']; } ?>" />
                                                    <input type="hidden" name="<?php echo "appliance_purchase_invoice[" . $price['unit_id'] . "]" ?>" 
                                                            class="<?php echo "unit_purchase_invoice_".$keys."_".$key;?>" value="<?php if(isset($unit_details['quantity'][0]['sf_purchase_invoice'])){  echo $unit_details['quantity'][0]['sf_purchase_invoice']; } ?>" />
                                                    
                                                    <?php  if ((strpos($price['price_tags'],REPAIR_STRING) !== false) && (strpos($price['price_tags'],IN_WARRANTY_STRING) !== false)) {
                                                                   $dop_mendatory = 1; 
                                                            }
                                                            ?>
                                                    <div class="form-group">
                                                        <div class="col-md-12 ">
                                                            <input type="text" style="text-transform: uppercase;pointer-events: none;background: #eee;margin-bottom: 5px;" onblur="validateSerialNo()" class="form-control" id="serial_number" name="serial_number"  value="<?php if(!empty($booking_history['spare_parts'])){ echo $booking_history['spare_parts'][0]['serial_number'];} else {echo $price["serial_number"];}  ?>"  placeholder = "Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" />
                                                            <input type="hidden" class="form-control" id="serial_number_pic" name="serial_number_pic"  value="<?php if(!empty($booking_history['spare_parts'])){ echo $booking_history['spare_parts'][0]['serial_number_pic'];} else {echo $price["serial_number_pic"];} ?>"  />
                                                            <input type="hidden" id="pod" class="form-control" name="pod" value="<?php echo $price['pod']; ?>"   />
                                                            <input type="hidden" id="sno_required" class="form-control" name="is_sn_file" <?php if(isset($price['is_sn_correct']) && ($price['is_sn_correct'] == IS_SN_CORRECT)){ echo 'value="1"';} else { echo 'value="0"'; }?>   />

                                                            <input type="hidden" id="duplicate_sno_required" class="form-control" name="is_dupliacte" value="0"   />
                                                            <input type="file" style="margin: 10px 0px;display:none;"  id="upload_serial_number_pic"   class="form-control" name="upload_serial_number_pic" value="<?php if(!empty($booking_history['spare_parts'])){ echo $booking_history['spare_parts'][0]['serial_number_pic'];} else {echo $price["serial_number_pic"];}  ?>"   />
                                                            <?php
                                                            if(!empty($price['serial_number_pic'])) {
                                                                $price_unit=$price['unit_id'];
                                                                $url="https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/'.SERIAL_NUMBER_PIC_DIR.'/'.$price['serial_number_pic']; ?>
                                                                <p style="margin-top: 5px;"><a href="<?php echo $url; ?>" class="<?php if(isset($price['is_sn_correct']) && ($price['is_sn_correct'] == IS_SN_CORRECT)){ $containsWrongSerialNumber = 1; echo "text-danger ";}?> target="_blank">SF Serial Number Pic</a></p>
                                                                 <?php
                                                            }
                                                            elseif(!empty($booking_history['spare_parts']) && !empty($booking_history['spare_parts'][0]['serial_number_pic'])) {
                                                                $price_unit=$price['unit_id'];
                                                                $url="https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/'.SERIAL_NUMBER_PIC_DIR.'/'.$booking_history['spare_parts'][0]['serial_number_pic']; ?>
                                                                <p style="margin-top: 5px;"><a href="<?php echo $url; ?>" target="_blank">SF Serial Number Pic</a></p>
                                                                 <?php
                                                            }
                                                            ?>
                                                            <span style="color:red;" id="<?php echo 'error_serial_no'.$count;?>"></span>
                                                                    <?php
                                                                    $selected_model = (!empty($booking_history['spare_parts'][0]['model_number']) && $booking_history['spare_parts'][0]['status'] != _247AROUND_CANCELLED) ? $booking_history['spare_parts'][0]['model_number'] : $unit_details['sf_model_number'];                                                                        
                                                                    if(isset($unit_details['model_dropdown']) && !empty($unit_details['model_dropdown'])){ 
                                                                        $isModelMandatory =1 ;
                                                                        $arrModels = array_column($unit_details['model_dropdown'], 'model_number');
                                                                        $arrModels = array_map('strtoupper', $arrModels);                                                                        
                                                                        ?>
                                                                        <select class="form-control model_number" id="<?php echo "model_number_" . $count ?>" name="<?php echo "model_number[" . $price['unit_id'] . "]" ?>" style="width:266px;">
                                                                            <option value="" selected="" disabled="">Model Number</option>
                                                                            <?php foreach ($unit_details['model_dropdown'] as $m) { ?>
                                                                            <option value="<?php echo $m['model_number'];?>" <?php if(trim(strtoupper($m['model_number'])) == trim(strtoupper($selected_model))){ echo 'selected="selected"';} ?> ><?php echo $m['model_number'];?></option>  
                                                                            <?php }?>
                                                                        </select>
                                                                        <?php
                                                                        if(!empty($selected_model) && !in_array(strtoupper($selected_model), $arrModels)){ ?>
                                                                            <div class="col-md-12" style="padding-bottom:10px;padding-top:0px;padding-left:0px;">
                                                                                <span class="text-danger" ><i class="fa fa-warning"></i>&nbsp;Model Number '<?= $selected_model ?>' filled during Spare Request is not mapped with the partner! Please Contact Admin.</span>
                                                                            </div>
                                                                        <?php }
                                                                        ?>
                                                                        <?php }  else { 
                                                                            $isModelMandatory =1 ;
                                                                        ?>
                                                                            <input type="text" name="<?php echo "model_number[" . $price['unit_id'] . "]" ?>" value="<?= $selected_model ?>" class="form-control model_number" id="<?php echo "model_number_text_" . $count ?>" placeholder = "ENTER MODEL NUMBER" onkeypress="return checkQuote(event);" oninput="return checkInputQuote(this);">
                                                                        <?php } ?>                                                                        
                                                        </div>
                                                    </div>
                                                    <?php //} ?>
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
                                                                        <input type="radio" class="<?php echo "completed_".$count."_".$keys;?>" onclick="return change_status('<?php echo $count;?>');" id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
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
                                                                        <input type="radio" class="<?php echo "cancelled_".$count."_".$keys;?>" onclick="return change_status('<?php echo $count;?>');" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
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
                                        </tbody>
                                    </table>
                                    <span class="error_msg" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(!empty($spare_parts_details) && !empty($spare_consumed_status)) { ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">Spare Parts Detail</div>
                            <div class="panel-body">
<div class="col-md-12" style="padding-left:0px;">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <th width="3%">S.No.</th>
                                    <th width="25%">Part Number</th>
                                    <th width="15%">Part Name</th>
                                    <th width="10%">Part Type</th>
                                    <th width="20">Spare Status</th>
                                    <th width="27%">
                                         <a href="javascript:void(0);" data-trigger="hover" data-html="true" data-toggle="popover" data-placement="left" title="Consumption Status Description" data-content="">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                        </a> Consumption Reason<span style="color:red;">*</span> 
                                       
                                    </th>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php foreach($spare_parts_details as $sno => $spare_part_detail) { $consumption_status_description = ''; ?>
                                    <tr>
                                        <td><?php echo ++$sno; ?></td>
                                        <td><?php echo $spare_part_detail['part_number']; ?></td>
                                        <td><?php echo $spare_part_detail['parts_requested']; ?></td>
                                        <td><?php echo $spare_part_detail['parts_requested_type']; ?></td>
                                        <td><?php echo $spare_part_detail['status']?></td>
                                        <td>
                                            <input type="hidden" name="spare_qty[<?php echo $spare_part_detail['id']; ?>]" value="<?php echo $spare_part_detail['quantity']; ?>">
                                            <input type="hidden" name="wrong_part[<?php echo $spare_part_detail['id']; ?>]" value="" id="wrong_part_<?php echo $spare_part_detail['id']; ?>">
                                            <select style="width:100%;" name="spare_consumption_status[<?php echo $spare_part_detail['id']; ?>]" class="spare_consumption_status" id="spare_consumption_status_<?php echo $spare_part_detail['id']; ?>" <?php if(!empty($spare_part_detail['awb_by_sf'])) { echo 'readonly';} ?>>
                                                <option value="" selected disabled>Select Reason</option>
                                                <?php $description_no = 1; foreach($spare_consumed_status as $k => $status) {
                                                    if (!empty($status['status_description'])) { $consumption_status_description .= $description_no.". <span style='font-size:12px;font-weight:bold;'>{$status['consumed_status']}</span>: <span style='font-size:12px;'>{$status['status_description']}.</span><br />"; } ?>
                                                    <option value="<?php echo $status['id']; ?>" <?php if(!empty($spare_part_detail['consumed_part_status_id']) && $spare_part_detail['consumed_part_status_id'] == $status['id']) { echo 'selected';} ?> data-tag="<?php echo $status['tag']; ?>" data-shipped_inventory_id="<?php echo $spare_part_detail['shipped_inventory_id']; ?>" data-part_number="<?php echo $spare_part_detail['part_number']; ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"><?php echo $status['consumed_status']; ?></option>
                                                <?php $description_no++; } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <span hidden id="status_consumption_status"><?php echo $consumption_status_description; ?></span>
                        </div>
                            </div>
                        </div>
                        
                    <?php } ?>
                    
                    <!--Review Questionnaire section-->
                    <?php echo $questionnaire_html; ?>
                    
                    <div class="row">
                        <div class ="col-md-12">
                            <div class="form-group col-md-6">
                                <label for="type" class="col-md-4" style="padding:0;">Total Customer Paid</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo floatval($paid_basic_charges) + floatval($paid_additional_charges) + floatval($paid_parts_cost) +floatval($upcountry_charges); ?>" placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-4" style="padding-left:10%;">Symptom *</label>
                                <div class="col-md-8" >
                                    <select  class="form-control" name="closing_symptom" id="technical_problem" onchange="update_defect()" <?php if(!empty($technical_problem)){ echo "required";} ?>>
                                        <option value="" selected="" disabled="">Please Select Symptom</option>
                                        <?php if(isset($technical_problem)) {
                                            foreach ($technical_problem as $value) { 
                                                $selected=((($value['id'] == 0) || (!empty($booking_symptom) && ($value['id'] == $booking_symptom[0]['symptom_id_booking_completion_time']))) ? 'selected' :''); //$booking_symptom[0]['symptom_id_booking_creation_time'] ?>
                                            <option value="<?php echo $value['id']?>" <?=$selected?> ><?php echo $value['symptom']; ?></option>
                                         
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-4" style="padding:0;">Defect *</label>
                                <div class="col-md-8" >
                                    <select  class="form-control" name="closing_defect" id="technical_defect" onchange="update_solution()" required >
                                        <option value="" selected="" disabled="">Please Select Defect</option>
                                        <?php foreach ($technical_defect as $value) { 
                                            $selected=((($value['defect_id'] == 0) || (!empty($booking_symptom) && ($value['defect_id'] == $booking_symptom[0]['defect_id_completion']))) ? 'selected' :''); ?>
                                        <option value="<?php echo $value['defect_id']?>" <?=$selected?> ><?php echo $value['defect']; ?></option> 
                                    <?php }?>
                                    </select>
                                </div>
                            </div>                            
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-4" style="padding-left:10%;">Solution *</label>
                                <div class="col-md-8" >
                                    <select class="form-control" name="technical_solution" id="technical_solution" disabled required >
                                        <option value="" selected="" disabled="">Please Select Solution</option>
                                        <?php if($technical_problem[count($technical_problem)-1]['id'] == 0) { ?>
                                        <option value="0" selected>Default</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-6">
                                <label for="rating_star" class="col-md-4" style="padding:0;">Star Rating*</label>
                                <div class="col-md-8">
                                    <Select type="text" class="form-control" name="rating_stars" id="rating_star" value="">
                                        <option value="">Select</option>
                                       <!--  <option <?php
                                            if ($booking_history[0]['rating_stars'] == '-1') {
                                                echo "selected";
                                            }
                                            ?>>-1</option> -->
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
                            <div class="col-md-4">
                                <div class="form-group" style="margin-left:14px;">
                                    <div class="checkbox"> <label><input type="checkbox" id="not_reachable" name="not_reachable"><b>Customer Not Reachable</b></label></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="checkbox"> <label><input type="checkbox" id="not_send_sms" name="not_send_sms"><b>Do Not Send SMS</b></label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    if($booking_history[0]['is_upcountry'] == '1' && $booking_history[0]['upcountry_paid_by_customer']== '1' ){ ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-md-6" style="padding:0;">
                                    <label for="type" class="col-md-4">Paid Upcountry Charges</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="input-group-addon">Rs.</div>
                                            <input  type="text" class="form-control cost"  name="upcountry_charges" id="upcountry_charges" value="<?php echo $upcountry_charges; ?>" placeholder="Total Price">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <input  type="hidden" class="form-control cost"  name="upcountry_charges" id="upcountry_charges" value="0" placeholder="Total Price">
                    <?php } ?>                        
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <textarea class="form-control" rows="5" name="rating_comments" placeholder ="Rating Comment"><?php echo $booking_history[0]['rating_comments']; ?></textarea>
                                </div>
                                <div class="col-md-4" >
                                    <textarea class="form-control" id="admin_remarks" rows="5" name="admin_remarks" placeholder ="Admin Remarks (Enter min. <?php echo ADMIN_REMARKS_MIN_LENGTH ?> chars.)"  minlength="<?php echo ADMIN_REMARKS_MIN_LENGTH ?>" required></textarea>
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
                        
                        <?php } else { ?>
                        <center>
                            <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $booking_history[0]['user_id']; ?>">
                            <?php if($enable_button && empty($is_invoice_generated) && empty($is_spare_pending_for_acknowledge) && in_array($this->session->userdata['user_group'], [_247AROUND_ADMIN, _247AROUND_CLOSURE, _247AROUND_RM])){
                            $is_upcountry = 0;
                            if(($booking_history[0]['upcountry_paid_by_customer'] == 1) && ($booking_history[0]['is_upcountry'] == 1)){
                                $is_upcountry = 1;
                            }
                            ?>

                            <?php
                            //if booking is cancelled on Admin panel, complete button gets disabled. 
                            if($booking_history[0]['current_status']!=_247AROUND_CANCELLED){ ?>
                            <input type="submit" id="submitform" onclick="return onsubmit_form('<?php echo $is_upcountry; ?>', '<?php echo $k_count; ?>')" class="btn btn-info" value="Complete Booking">
                            <?php } else {?>
                                <input type="submit" id="submitform" onclick="return onsubmit_form('<?php echo $is_upcountry; ?>', '<?php echo $k_count; ?>')" class="btn btn-info" value="Complete Booking" disabled tabindex=-1>
                                <?php }?> 
                            <?php } else {
                                
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
<!-- Wrong spare parts modal -->
<div id="WrongSparePartsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="wrong_spare_part_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Wrong Part</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<script>
    $("#technical_problem").select2();
    $('#technical_defect').select2();
    $('#technical_solution').select2();
    $(".booking_source").select2();  
    $(".spare_consumption_status").select2();
    $('[data-toggle="popover"]').attr('data-content', $('#status_consumption_status').html());
</script>
<script>
    <?php if(empty($str_disabled) && isset($unit_details['model_dropdown']) && !empty($unit_details['model_dropdown'])){ ?> 
        $(".model_number").select2();
    <?php } ?>  
    $("#service_id").select2();
    $("#booking_city").select2();
    var brandServiceUrl =  '<?php echo base_url();?>/employee/booking/getBrandForService/';
    var categoryForServiceUrl = '<?php echo base_url();?>/employee/booking/getCategoryForService/';
    var CapacityForCategoryUrl = '<?php echo base_url();?>/employee/booking/getCapacityForCategory/';
    
    var solution_id = "";
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover(); 
    //called when key is pressed in textbox
    $(".cost").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
    //display error message
    $(".error_msg").html("Digits Only").show().fadeOut("slow");
    return false;
     }
    });
    
    solution_id = "<?php echo ((!empty($booking_symptom) && ($technical_problem[count($technical_problem)-1]['id'] != 0))?$booking_symptom[0]['solution_id']:"");?>";
    technical_defect = $('#technical_defect').val();
    if(((solution_id !== "") && (solution_id != 0) && (solution_id != null)) || (technical_defect != "")) {
        update_solution();
    }
    
    if(($('#technical_solution').val() == 0) || ($('#technical_solution').val() == '') || ($('#technical_solution').val() == null)) {
        $('#technical_solution').removeAttr('disabled');
    }
    
        $(":radio").each(function() {
            $("#"+this.id).prop("checked",false);
        });
        
        $('.spare_consumption_status').on('change', function() {
            if($(this).children("option:selected").data('tag') == '<?php echo WRONG_PART_RECEIVED_TAG; ?>') {
                open_wrong_spare_part_model($(this).children("option:selected").data('spare_id'), '<?php echo $booking_history[0]['booking_id']; ?>', $(this).children("option:selected").data('part_number'), '<?php echo $booking_history[0]['service_id']; ?>', $(this).children("option:selected").data('shipped_inventory_id'));
            }
        })
        
    });
    
    
    $(document).on('keyup', '.cost', function (e) {
    
    var price = 0;
    $("input.cost").each(function () {
     price += Number($(this).val());
    
    });
    
    
    $("#grand_total_price").val(price);
    });
    
    function update_defect(){
        var technical_problem = $("#technical_problem").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_defect_on_symptom',
            data:{technical_problem:technical_problem},
            success: function (response) {
                $('#technical_solution').attr('disabled',true);
                $('#select2-technical_defect-container').empty();
                $('#select2-technical_solution-container').empty();
                $('#technical_defect').empty();
                $('#technical_solution').empty();
                response=JSON.parse(response);
                var str="<option value='' selected='' disabled=''>Please Select Defect</option>";
                if(response.length>0)
                {
                    for(var i=0;i<response.length;i++)
                    {
                        str+="<option value="+response[i]['defect_id']+" >"+response[i]['defect']+"</option>";
                    }
                }
                $('#technical_defect').append(str);
            }
        });
    }
    
    function update_solution(){
        var technical_symptom = $("#technical_problem").val();
        var technical_defect = $("#technical_defect").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_solution_on_symptom_defect',
            data:{technical_symptom:technical_symptom,technical_defect:technical_defect},
            async: false,
            success: function (response) {
                $('#technical_solution').removeAttr('disabled');
                $('#select2-technical_solution-container').empty();
                $('#technical_solution').empty();
                response=JSON.parse(response);
                var str="<option value='' disabled=''>Please Select Solution</option>";
                if(solution_id == null || solution_id == '' || solution_id == 0){
                    str="<option value='' selected='' disabled=''>Please Select Solution</option>";
                }
                var selected;
                if(response.length>0)
                {
                    for(var i=0;i<response.length;i++)
                    {
                        selected="";
                        if(response[i]['solution_id'] === solution_id)
                        {
                            selected = "selected";
                            $('#select2-technical_solution-container').text(response[i]['technical_solution']);
                        }
                        str+="<option value="+response[i]['solution_id']+" "+selected+" >"+response[i]['technical_solution']+"</option>";
                    }
                }
                $('#technical_solution').append(str);
            }
        });
    }
    
    function onsubmit_form(upcountry_flag, number_of_div) { 
    $('#submitform').css("pointer-events", "none");
    $('#submitform').css("opacity", "0.5");
    var flag = 0;
    var div_count = 0;
    var is_completed_checkbox = [];
    var serial_number_tmp = [];
    var prediv = -1;
    $(':radio:checked').each(function(i) {
        div_count = div_count + 1;
    
        //console.log($(this).val());
        var div_no = this.id.split('_');
        var div_class = $(this).attr('class').split('_')[2];
        is_completed_checkbox[i] = div_no[0];
        if (div_no[0] === "completed") {
            var price_tag_row_number = parseInt(i)+1;
            var service_category_pod_required = $('#is_sf_purchase_invoice_required_'+div_class).attr('data-pricetags');
            if (typeof service_category_pod_required !== "undefined") { 
                if(service_category_pod_required.includes($.trim($('#price_tags'+price_tag_row_number).text()))) {
                    var is_sf_purchase_invoice_required = $('#is_sf_purchase_invoice_required_'+div_class).val();
                    if(is_sf_purchase_invoice_required == '1') {
                        var sf_purchase_invoice = $('#purchase_invoice_'+div_class).val();
                        if(sf_purchase_invoice == '') {
                            var sf_purchase_invoice = $('#purchase_invoice_'+div_class).attr('value');
                        }
                        if(sf_purchase_invoice == '') {
                            alert("Please upload sf purchase invoice document.");
                            flag = 1;
                            $('#submitform').css("pointer-events", "auto");
                            $('#submitform').css("opacity", "1");
                            return false;
                        }
                    }
                }
            }
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
                    document.getElementById('error_serial_no' + div_no[2]).innerHTML = "Please Enter Serial Number";                        
                    flag = 1;
                }
    
                if (serial_number === "0") {
                    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                    document.getElementById('error_serial_no' + div_no[2]).innerHTML = "Enter Valid Serial Number"; 
                    flag = 1;
                }
                if($('#sno_required'+ div_no[2]).val() === '1' && !$('#sn_remarks').val()){
                     alert('Please Correct Serial Number or Entered Remarks, Why We Should go With Wrong Serial Number');
                     flag = 1;
                     $('#submitform').css("pointer-events", "auto");
                     $('#submitform').css("opacity", "1");
                     return false;
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
                                $('#submitform').css("pointer-events", "auto");
                                $('#submitform').css("opacity", "1");
                                return false;
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
                            document.getElementById('model_number_' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            $('#submitform').css("pointer-events", "auto");
                            $('#submitform').css("opacity", "1");
                            return false;
                        }
                    }
                    else{
                        var model_text_value = $("#model_number_text_" + div_no[2]).val();
                        if(model_text_value ===""){
                            alert("Model Number is blank");
                            document.getElementById('model_number_text_' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            $('#submitform').css("pointer-events", "auto");
                            $('#submitform').css("opacity", "1");
                            return false;
                        }
                    }
                    <?php
                }
                ?>
       <?php if($dop_mendatory ==1){ ?>
        var dop = $("#dop").val();
        if(dop === ""){
                alert("Please Select Date of Purchase");
                return false; 
              }  
        <?php } ?>
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
                                    case DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE:
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
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }
    if ($.inArray('completed', is_completed_checkbox) !== -1) {
    
    } else {
        alert('Please Select atleast one Completed or Delivered checkbox.');
        flag = 1;
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    
    }
    temp = [];
    $.each(serial_number_tmp, function(key, value) {
        if ($.inArray(value, temp) === -1) {
            temp.push(value);
        } else {
            alert(value + " is a Duplicate Serial Number");
            flag = 1;
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
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
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
            return false;
        } else if (Number(upcountry_charges) > 0) {
//            flag = 0;
            document.getElementById('upcountry_charges').style.borderColor = "green";
        }
    }
    <?php if(!empty($technical_problem)){ ?>
        var technical_problem = $("#technical_problem").val();

        if(technical_problem === null){
            alert('Please Select Symptom');
            document.getElementById('technical_problem').style.borderColor = "red";
            flag = 1;
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
            return false;
        }
    <?php } ?>

    <?php if(!empty($technical_defect)){ ?>
        var technical_defect = $("#technical_defect").val();

        if(technical_defect === null){
            alert('Please Select Defect');
            document.getElementById('technical_defect').style.borderColor = "red";
            flag = 1;
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
            return false;
        }
    <?php } ?>
    if($("#technical_solution").val() != '') {
        var technical_solution = $("#technical_solution").val();

        if(technical_solution === null){
            alert('Please Select Solution');
            document.getElementById('technical_solution').style.borderColor = "red";
            flag = 1;
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
            return false;
        }
    }
    var closing_remarks = $("#closing_remarks").val();
    if (closing_remarks === "") {
        alert("Please Enter Remarks");
        document.getElementById('closing_remarks').style.borderColor = "red";
        flag = 1;
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }

    $('.spare_consumption_status').each(function(index, value) {
        if($(this).val() == '' || $(this).val() == null) {
            alert('Please select spare consumption status for all parts.');
            flag = 1;
            $('#submitform').css("pointer-events", "auto");
            $('#submitform').css("opacity", "1");
            return false;                
        }
    });

    // check Admin remarks filled or not with min chars
    var admin_remarks = $('#admin_remarks');
    var minlength = "<?php echo ADMIN_REMARKS_MIN_LENGTH ?>";
    if(admin_remarks.val().length < minlength) {
        alert('You need to enter at least '+minlength+' characters, for Admin Remarks');
        flag = 1;
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }
    
    // Check rating is filled or not
    var ratingStar = $("#rating_star").val();
    var not_reachable = $("#not_reachable").prop('checked');
    if(ratingStar && not_reachable){
        flag = 1;
        alert("Either Choose Customer not reachable or add rating, Don't select both option together");
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }
    else if(!ratingStar && !not_reachable){
        alert("Please Add Rating or Select Customer not reachable");
        flag = 1;
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }
    
    if (flag === 0) {
        return true;
    
    } else if (flag === 1) {
        $('#submitform').css("pointer-events", "auto");
        $('#submitform').css("opacity", "1");
        return false;
    }
    }
    
    function outbound_call(phone_number, booking_primary_id = ''){
        var confirm_call = confirm("Call Customer ?");
    
        if (confirm_call == true) {
    
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number + '/' + booking_primary_id,
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
    width: 300px;
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
    
    function change_status(div) {
        $("#basic_charge"+div).prop("readonly",false);
        $("#extra_charge"+div).prop("readonly",false);
        $("#parts_cost"+div).prop("readonly",false);
        
        var total = parseInt($("#basic_charge"+div).val())+parseInt($("#extra_charge"+div).val())+parseInt($("#parts_cost"+div).val());
        if($(".cancelled_"+div+"_0").is(":checked")) {
            if(total > 0) {
                var cnfrm = confirm("You have entered cost as Rs. "+total+" . Do you want to change its status as Not Completed/Delivered ? ");
                if(!cnfrm){
                    return false;
                }
                $("#basic_charge"+div).val('0');
                $("#extra_charge"+div).val('0');
                $("#parts_cost"+div).val('0');
                $("#grand_total_price").val(parseInt($("#grand_total_price").val())-parseInt(total));
            }
            $("#basic_charge"+div).prop("readonly",true);
            $("#extra_charge"+div).prop("readonly",true);
            $("#parts_cost"+div).prop("readonly",true);
        }
    }
    
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
    
    function validateSerialNo(count = ""){
    var postData = {};
    postData['serial_number'] = $.trim($("#serial_number"+count).val());
    postData['price_tags'] = $("#price_tags"+count).text();
    postData['user_id'] = $("#user_id").val();
    postData['booking_id'] = $("#booking_id").val();
    postData['partner_id'] = $("#partner_id").val();
    postData['appliance_id'] = '<?php echo $booking_history[0]['service_id'];?>';
    $("#submitform").attr("disabled",false);
    $('#serial_number' + count).css("border-color", "#ccc");
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
                        $("#submitform").attr("disabled",true);
                    } else {
                        $("#sno_required"+count).val('1');
                        $("#error_serial_no" +count).html(data.message);
                        //$("#upload_serial_number_pic"+count).css('display', "block");
                        $("#duplicate_sno_required"+count).val('0');
                        $('body').loadingModal('destroy');
                        if(data.code == "247" || (data.message.indexOf('Serial Number not valid') !== -1)) {
                            $("#submitform").attr("disabled",true);
                        }
                    }
                }
            });
    }
    
    
    
    }
    function update_dop_for_unit(div){
          var div_item_count = $("#count_line_item_"+div).val();
          var dopValue = $("#dop_"+div).val();
            for(i = 0; i < Number(div_item_count); i++ ){
                $(".unit_dop_"+div+"_"+i).val(dopValue);
         }
    }
    function update_purchase_invoice_for_unit(div){
          var div_item_count = $("#count_line_item_"+div).val();
          var purchase_invoice_value = $("#purchase_invoice_"+div).val();
        
            for(i = 0; i < Number(div_item_count); i++ ){
                $(".unit_purchase_invoice_"+div+"_"+i).val(purchase_invoice_value);
         }
    }
      function dop_calendar(id){
         $("#"+id).datepicker({
             dateFormat: 'dd-mm-yy', 
             changeMonth: true,
             changeYear: true,
             maxDate:0
         }).datepicker('show');
    }
    
    function open_wrong_spare_part_model(spare_part_detail_id, booking_id, part_name, service_id, shipped_inventory_id = '') {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/wrong_spare_part/' + booking_id,
            data: {spare_part_detail_id:spare_part_detail_id, booking_id:booking_id, part_name:part_name, service_id:service_id, shipped_inventory_id:shipped_inventory_id},
            success: function (data) {
                $("#wrong_spare_part_model").children('.modal-content').children('.modal-body').html(data);   
                $('#WrongSparePartsModal').modal({backdrop: 'static', keyboard: false});
            }
        });
    }

    $(document).on('change',"#wrong_part", function() {
        $('#part_number').val($('#wrong_part').children("option:selected").data('part_number'));
    });

</script>
<style>
    <?php if(!empty($str_disabled)) { ?> 
        .dop , .date-picker, .model_number{
            pointer-events : none !important;
            background : #eee !important;
        }    
    <?php } ?>
</style>
<style>
select[readonly].select2-hidden-accessible + .select2-container {
  pointer-events: none;
  touch-action: none;
}
</style>
<?php 
if($this->session->userdata('error')){$this->session->unset_userdata('error');} 
if($this->session->userdata('error_msg')){$this->session->unset_userdata('error_msg');} 
if($this->session->userdata('success')){$this->session->unset_userdata('success');} 
?>