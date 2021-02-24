<script>
 var DUPLICATE_SERIAL_NO_CODE = '<?php echo DUPLICATE_SERIAL_NO_CODE; ?>';
 var DUPLICATE_SERIAL_NUMBER_USED = '<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>';
</script>
<script src="<?php echo base_url();?>js/validation_js.js"></script>
<style>
    .disable_link {
        display: none;
    }
    .select2-container--default{
        width: 100% !important;
    }
</style>
<body>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" style='padding-bottom: 10px'>
                <h2 class="page-header">
                    Update Booking
                </h2>
                <?php if(validation_errors()) { ?>
                <div class=" alert alert-danger">
                    <?php echo validation_errors(); ?>
                </div>
                <?php } ?>
                <?php
                    if ($this->session->userdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                    ?>
                <form class="form-horizontal" id="requested_parts" name="myForm" action="<?php echo base_url() ?>employee/service_centers/process_update_booking" method="POST" onSubmit="document.getElementById('submitform').disabled=true;" enctype="multipart/form-data">
                    <input type="hidden" name="service_center_closed_date" value="<?php if(!empty($bookinghistory[0]['service_center_closed_date'])) { echo $bookinghistory[0]['service_center_closed_date'];} else {echo "";} ?>">
                    <div class="col-md-12" style="margin-left:-31px;">
                        <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <tr>
                                <th>Booking Id</th>
                                <th>Customer Name</th>
                                <th>Phone Number</th>
                                <?php if(isset($saas_module) && (!$saas_module)) { ?>
                                <th style="text-align: center;">Edit Request Type</th>
                                <?php } ?>
                                <th style="text-align: center;">Warranty Checker</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_id'])) {echo $bookinghistory[0]['booking_id']; }?>"  readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['name'])) {echo $bookinghistory[0]['name']; }?>"  disabled>
                                </td>
                                <td>
                                    <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_primary_contact_no'])) {echo $bookinghistory[0]['booking_primary_contact_no']; }?>"  disabled>
                                </td>
                                <?php if(isset($saas_module) && (!$saas_module)) { ?>
                                       <td><center><a target="_blank" id="change_request_type" href="<?php echo base_url(); ?>service_center/get_sf_edit_booking_form/<?php echo urlencode(base64_encode($bookinghistory[0]['booking_id']))?>" style="height: 29px;width: 36px;" class="btn btn-sm btn-success"  title="Edit Request Type"><i class="fa fa-edit" aria-hidden="true"></i></a></center></td>
                                <?php } ?>                                    
                                <td>
                                        <?php 
                                            $partner_id = "";
                                            $service_id = "";
                                            if (isset($bookinghistory[0]['partner_id'])) {$partner_id = '/'.$bookinghistory[0]['partner_id']; };
                                            if (!empty($partner_id) && isset($bookinghistory[0]['service_id'])) {$service_id = '/'.$bookinghistory[0]['service_id']; }
                                        ?>
                                        <center><a href="<?php echo base_url(); ?>service_center/warranty<?=$partner_id?><?=$service_id?>" target="_blank" class='btn btn-sm btn-success' title='Warranty Checker' style="height: 29px;width: 36px;"><i class='fa fa-certificate' aria-hidden='true'></i></a></center>
                                </td>
                            </tr>                            
                        </table>
                    </div>
                    <input type="hidden" class="form-control" id="booking_id" name="booking_id" value = "<?php echo $booking_id; ?>">
                    <input type="hidden" class="form-control"  name="amount_due" value = "<?php if (isset($bookinghistory[0]['amount_due'])) {echo $bookinghistory[0]['amount_due']; }?>">
                    <input type="hidden" class="form-control"  name="partner_id" value = "<?php if (isset($bookinghistory[0]['partner_id'])) {echo $bookinghistory[0]['partner_id']; }?>">
                    <input type="hidden" class="form-control"  name="price_tags" value = "<?php if (isset($price_tags)) {echo $price_tags; }?>">                   
                    <input type="hidden" class="form-control"  name="is_serial_number_required" value = "<?php echo $is_serial_number_required; ?>">
                    <input type="hidden" class="form-control"  name="is_invoice_required" value = "<?php echo $is_invoice_required; ?>">
                    <input type="hidden" class="form-control" id="partner_flag" name="partner_flag" value="0" />
                    <input type="hidden" name="spare_shipped" value="<?php echo $spare_shipped; ?>" />
                    <div class="form-group ">
                        <label for="reason" class="col-md-2" style="margin-top:39px;">Reason</label>
                        <div class="col-md-6" style="margin-top:39px;">
                            <!-- If status is 'InProcess' in service_center_booking_action_action table, booking can not be rescheduled -->
                            <?php if(!empty($bookinghistory['allow_reshedule']) || !empty($bookinghistory['allow_estimate_approved'])) { ?>
                            <?php foreach ($internal_status as $key => $data1) { ?>
                            <div class="radio ">
                                <label>
                                <input type="radio"  name="reason" id= "<?php echo "reason_id" . $key; ?>" onclick="internal_status_check(this.id)" class="internal_status" value="<?php echo $data1['status']; ?>" >
                                <?php echo $data1['status']; ?>
                                </label>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <?php if($spare_flag != SPARE_PART_RADIO_BUTTON_NOT_REQUIRED ){ ?>
                            <div class="radio ">
                                <label>
                                <input type="radio" id="spare_parts" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo $spare_flag;?>" >
                                <?php echo $spare_flag;?>
                                </label>
                            </div>
                            <?php }?>
                            <hr id="seperator">
                            <!-- If status is 'InProcess' in service_center_booking_action_action table, booking can not be rescheduled -->
                            <?php if(!empty($bookinghistory['allow_reshedule'])) { ?>
                            <?php if($bookinghistory[0]['is_upcountry'] == 1 ){ ?>
                            <div class="radio ">
                                <label class="<?php if(!empty($nrn_flag) && $nrn_flag==1){ echo "hide"; } ?>">
                                <input type="radio" id="reschedule_for_upcountry" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo RESCHEDULE_FOR_UPCOUNTRY; ?>" >
                                <?php echo RESCHEDULE_FOR_UPCOUNTRY. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                            <?php }?>
                            <div class="radio ">
                                <label class="<?php if(!empty($nrn_flag) && $nrn_flag==1){ echo "hide"; } ?>" >
                                <input type="radio" id="rescheduled" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo CUSTOMER_ASK_TO_RESCHEDULE; ?>" >
                                <?php echo CUSTOMER_ASK_TO_RESCHEDULE. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                            <div class="radio ">
                                <label class="<?php if(!empty($nrn_flag) && $nrn_flag==1){ echo "hide"; } ?>">
                                <input type="radio" id="product_not_delivered" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER; ?>" >
                                <?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER . " - Reschedule"; ?>
                                </label>
                            </div>
                            <?php if(!empty($spare_shipped_flag)){ ?>
                            <div class="radio ">
                                <label class="<?php if(!empty($nrn_flag) && $nrn_flag==1){ echo "hide"; } ?>">
                                <input type="radio" id="spare_not_delivered" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF; ?>" >
                                <?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                                <div class="text-warning" style="font-size:16px;"><i class="fa fa-info-circle"></i>&nbsp;Booking In-Process, can not be Rescheduled</div>
                            <?php } ?>
                        </div>
                    </div>
                    <input type="hidden" name="days" value="<?php echo $days; ?>" />    
                    <div class="row">
                        <div class='col-md-2'></div>
                        <div class="col-md-10 errorMsg" style="font-weight:bold;padding:15px;color:red;"></div>                            
                    </div>
                    <div class="panel panel-default col-md-offset-2" id="hide_spare" >                        
                        <div class="panel-body">
                            <div class="row">
                                <div class = 'col-md-6'>
                                    <div class="form-group">
                                        <label for="model_number" class="col-md-4">Model Number *</label>
                                        <?php $is_modal_number = false;  if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-6">
                                            <select class="form-control spare_parts" id="model_number_id" tabindex="-1" name="model_number_id">
                                                <option value="" disabled="" selected="">Select Model Number <?php  //echo $unit_model_number; ?></option>
                                                <?php foreach ($inventory_details as $key => $value) { ?> 
                                                <option value="<?php echo $value['id']; ?>"   <?php if(trim(strtoupper($unit_model_number))==trim(strtoupper($value['model_number']))){ $is_modal_number = true; echo 'selected';} ?>   ><?php echo $value['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                            
                                            <input type="hidden" id="model_number" name="model_number" value="<?php echo trim($unit_model_number); ?>">
                                            
                                        </div>
                                        <?php } else { ?> 
                                        <div class="col-md-6" id="appliance_model_div">
                                            <input type="hidden" id="model_number_id" name="model_number_id">
                                            <input type="text" class="form-control spare_parts" id="model_number" tabindex="-1" name="model_number" value = "<?php if(isset($unit_model_number) && !empty($unit_model_number)){ $is_modal_number = TRUE; echo $unit_model_number;} ?>" placeholder="Model Number" required="" onkeypress="return checkQuote(event);" oninput="return checkInputQuote(this);">
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" >
                                        <label for="dop" class="col-md-4" id="dat_of_puchase">Date of Purchase *</label>
                                        <div class="col-md-6">
                                            <div class="input-group input-append date">
                                                <input id="dop" class="form-control" tabindex="-1" value="<?php if(isset($purchase_date) && (!empty($purchase_date) && $purchase_date != "0000-00-00" && $ask_purchase_date==0)){ echo date('d-m-Y', strtotime($purchase_date)); } ?>"  placeholder="Select Date" name="dop" type="text" autocomplete='off' onkeypress="return false;">
                                                <span class="input-group-addon add-on" id="dop_calendar" onclick="dop_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number" class="col-md-4">Serial Number *</label>
                                        <div class="col-md-6"> 
                                            <input type="text" class="form-control spare_parts" id="serial_number" name="serial_number"  value="<?php if(isset($unit_serial_number) && !empty($unit_serial_number)){echo $unit_serial_number;}  ?>" placeholder="Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" required="">
                                            <input type="hidden" id="is_sn_correct" name="is_sn_correct" value="">
                                            <span id="error_serial_no" class="error_serial_no text-danger"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number_pic" class="col-md-4" id="serial_text">Serial Number Picture <?php if((!isset($unit_serial_number_pic) || empty($unit_serial_number_pic)) && empty($on_saas)){echo '*';}  ?></label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" tabindex="-1" id="serial_number_pic" name="serial_number_pic" required>
                                            <input type="hidden" value="<?php if(isset($unit_serial_number_pic) && !empty($unit_serial_number_pic)){echo $unit_serial_number_pic;}  ?>"  name="serial_number_pic_exist" >
                                        </div>
                                        <?php if(isset($unit_serial_number_pic)  && !empty($unit_serial_number_pic)){ ?>
                                            <a target="_blank" class="<?php if(!isset($unit_serial_number_pic) ||  empty($unit_serial_number_pic)){echo 'hide';}  ?>" href="<?php if(isset($unit_serial_number_pic) && !empty($unit_serial_number_pic)){echo S3_WEBSITE_URL.SERIAL_NUMBER_PIC_DIR."/".$unit_serial_number_pic;}  ?>">View</a>
                                     <?php    } ?>

                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice_pic" class="col-md-4">Invoice Picture</label>
                                        <div class="col-md-6">
                                            <?php 
                                                $invoice_file = '';
                                                if(!empty($bookinghistory['spare_parts'])) {
                                                    foreach($bookinghistory['spare_parts'] as $spares) {
                                                        if(!empty($spares['invoice_pic']) && $spares['status'] != _247AROUND_CANCELLED) {
                                                            $invoice_file = $spares['invoice_pic'];
                                                            break;
                                                        }
                                                    }
                                                } 
                                            ?>
                                            <input type="file" class="form-control spare_parts" tabindex="-1" id="invoice_pic" name="invoice_image" <?php if(!empty($invoice_file)) { echo 'style="cursor: not-allowed; pointer-events: none;background-color:#eee;"'; } ?>>
                                            <?php if(!empty($invoice_file)) { ?>
                                                <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/purchase-invoices/<?php echo $invoice_file; ?> " target="_blank">Click Here to view Invoice Picture</a>
                                            <?php } ?>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button type="button" style="margin-left: 95%;" class="btn btn-primary addButton">Request More Parts</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" style="margin-left:10px; margin-right:10px;">
                            <div class="panel-body" >
                                <div class="row">
                                    <div class = 'col-md-6 hide'>
                                        <div class="form-group ">
                                            <label for="part_warranty" class="col-md-4">Part Warranty Status  <?php echo $bookinghistory[0]['request_type'];  ?></label>                                             
                                            <div class="col-md-6">
        <input type="text" id="part_warranty_status_0"  value="<?php if(strpos($bookinghistory[0]['request_type'],'Out Of Warranty') == true || strpos($bookinghistory[0]['request_type'],'Gas Recharge - Out')==true ){echo '2';}else{echo '1';}  ?>" name="part[0][part_warranty_status]" class="<?php if(strpos($bookinghistory[0]['request_type'],'Out Of Warranty') == false || strpos($bookinghistory[0]['request_type'],'Gas Recharge - Out')==false ){echo 'part_in_warranty_status';} ?>">  
                                            </div>
                                        </div>
                                    </div>
                                    <!--<div class = 'col-md-6'>
                                        <div class="form-group">
                                            <label for="Technical Issue" class="col-md-4">Technical Problem *</label>                                             
                                            <div class="col-md-6">
                                                <select class="form-control spare_request_symptom" id="spare_request_symptom_0" name="part[0][spare_request_symptom]">
                                                    <option selected disabled>Select Technical Problem</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div class = 'col-md-6'>
                                        <div class="form-group">
                                            <label for="parts_type" class="col-md-4">Part Type *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control parts_type_check parts_type spare_parts" onchange="part_type_changes('0')" id="parts_type_0" name="part[0][parts_type]" >
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>                                            
                                            <?php } else { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts_type" id="parts_type_0" name="part[0][parts_type]" value = "<?php echo set_value('parts_type'); ?>">
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="parts_name" class="col-md-4">Part Name *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                                <div class="col-md-6">
                                                    <select class="form-control spare_parts parts_name" id="parts_name_0" name="part[0][parts_name]" onchange="get_inventory_id(this.id)">
                                                        <option selected disabled>Select Part Name</option>
                                                    </select>
                                                    <span id="spinner" style="display:none"></span>
                                                    <span id="inventory_stock_0"></span>
                                                </div>
                                                <input type="hidden" id="requested_inventory_id_0" name="part[0][requested_inventory_id]" value="" /> 
                                            <?php } else { ?> 
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control spare_parts parts_name" id="parts_name_0" name="part[0][parts_name]" value = "" placeholder="Part Name" required="">
                                                </div>
                                            <?php } ?> 
                                                <a target="_blank"  href="#" id="parts_image_0" class="disable_link"><i style="font-size: 25px;" class="glyphicon glyphicon-picture"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="parts_number_0" class="col-md-4">Part Number</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control spare_parts parts_number" id="parts_number_0" name="part[0][parts_number]" disabled>
                                                            <option>Please select part type first</option>
                                                        </select>
                                                        <span id="spinner_part_number" style="display:none"></span>
                                                    </div>
                                                    <a target="_blank"  href="#" id="parts_image_1" class="disable_link"><i style="font-size: 25px;" class="glyphicon glyphicon-picture"></i></a>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="defective_parts_pic" class="col-md-4">Defective Front Part Picture <?php if(empty($on_saas)){ ?> * <?php } ?></label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control defective_parts_pic spare_parts" id="defective_parts_pic_0" name="defective_parts_pic[0]" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">


                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="defective_parts_pic" class="col-md-4">Defective Back Part Picture <?php if(empty($on_saas)){ ?> *<?php } ?></label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control defective_back_parts_pic spare_parts" id="defective_back_parts_pic_0" name="defective_back_parts_pic[0]" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity" class="col-md-4">Quantity *</label>
                                            <div class="col-md-6">
                                                
                                                 <input type="text"  required=""   min="1" readonly=""  value="1" class="form-control quantity  spare_parts" id="parts_quantity_0" name="part[0][quantity]" >
                                                <span id="error_span_0" style="color:red;" class="hide"></span>

                                            </div>

                                        </div>
                                    </div>
                                </div>



                                    <div class="row">



                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="symptom" class="col-md-4">Symptom  </label>
                                            <div class="col-md-6">
                                                <select class="form-control symptom " id="symptom_0" name="part[0][symptom]" >
                                                        <option selected disabled>Select Symptom</option>
                                                        <?php foreach($technical_problem as $problem){ ?>

                                                            <option value="<?php echo $problem['id'] ?>" ><?php echo $problem['symptom'] ?></option>

                                                        <?php }  ?>

                                                    </select>  
                                            </div>

                                        </div>
                                    </div>


                                      <div class="col-md-6">
                                        <div class="form-group">

                                            <label for="defect_pic" class="col-md-4">Defect Picture *</label>
                                            <div class="col-md-6">
                                                <input type="file" required="required" class="form-control defect_pic spare_parts" id="defect_pic_0" name="defect_pic[0]" >
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>



                            </div>
                        </div>
                        <div id="template" class="hide">
                            <div class="panel panel-default spare_clone " style="margin-left:10px; margin-right:10px;" >
                                <div class="panel-body" >
                                    <div class = "row">
                                        <div class = 'col-md-6 hide'>
                                            <div class="form-group ">
                                                <label for="part_warranty" class="col-md-4">Part Warranty Status </label>                               
                                                <div class="col-md-6">
<input type="text" id="part_warranty_status"  value="<?php if(strpos($bookinghistory[0]['request_type'],'Out Of Warranty') == true || strpos($bookinghistory[0]['request_type'],'Gas Recharge - Out')==true ){echo '2';}else{echo '1';}  ?>"> 
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class = 'col-md-6'>
                                            <div class="form-group">
                                                <label for="Technical'Issue" class="col-md-4">Technical Problem *</label>                                             
                                                <div class="col-md-6">
                                                    <select class="form-control" id="spare_request_symptom">
                                                        <option selected disabled>Select Technical Problem</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class = 'col-md-6'>
                                            <div class="form-group">
                                                <label for="parts_type" class="col-md-4">Part Type *</label>
                                                <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                                <div class="col-md-6">
                                                    <select class="form-control parts_type spare_parts" id="parts_type">
                                                        <option selected disabled>Select Part Type</option>
                                                    </select>
                                                    <span id="spinner" style="display:none"></span>
                                                </div>
                                                <?php } else { ?> 
                                                <div class="col-md-6">
                                                    <select class="form-control spare_parts_type" id="parts_type" value = "<?php echo set_value('parts_type'); ?>">
                                                        <option selected disabled>Select Part Type</option>
                                                    </select>
                                                </div>
                                                <?php } ?>

                                            </div>
                                        </div>


                                           <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="parts_name" class="col-md-4">Part Name *</label>
                                                <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                                <div class="col-md-6">
                                                    <select class="form-control parts_name spare_parts" id="parts_name" onchange="get_inventory_id(this.id)">
                                                        <option selected disabled>Select Part Name</option>
                                                    </select>
                                                    <span id="spinner" style="display:none"></span>
                                                    <span id="inventory_stock"></span>
                                                    <input type="hidden" id="requested_inventory_id" value="" /> 
                                                </div>
                                                <?php } else { ?> 
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control spare_parts parts_name" id="parts_name" value = "" placeholder="Part Name" >
                                                </div>
                                                <?php } ?>
                                                <a target="_blank"  href="#" id="parts_image" style="padding-right: 10px;" class="disable_link"><i style="font-size: 25px;" class="glyphicon glyphicon-picture"></i></a>
                                                <button type="button" id="remove_section" style="display: inline-block;margin-bottom: 14px; padding: 3px 8px;" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="row">
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="parts_number" class="col-md-4">Part Number</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control spare_parts parts_number" id="parts_number" disabled="true">
                                                            <option>Please select part type first</option>
                                                        </select>
                                                        <span id="spinner_part_number" style="display:none"></span>
                                                    </div>
                                                    <a target="_blank"  href="#" id="parts_image" class="disable_link"><i style="font-size: 25px;" class="glyphicon glyphicon-picture"></i></a>
                                            </div>
                                        </div>
                                        <?php } ?>

                                         <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="defective_parts_pic" class="col-md-4">Defective Front Part Picture <?php if(empty($on_saas)){ ?>*<?php } ?></label>
                                                <div class="col-md-6">
                                                    <input type="file" class="form-control defective_parts_pic spare_parts" id="defective_parts_pic" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="defective_parts_pic" class="col-md-4">Defective Back Part Picture <?php if(empty($on_saas)){ ?> *<?php } ?></label>
                                                <div class="col-md-6">
                                                    <input type="file" class="form-control defective_back_parts_pic spare_parts " id="defective_back_parts_pic" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quantity" class="col-md-4">Quantity *</label>
                                                <div class="col-md-6">
                                                    <input type="text"  required=""  min="1" readonly="" value="1" class="form-control  spare_parts" id="quantity" >
                                                    <span id="error_span" style="color:red;" class="hide"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="symptom" class="col-md-4">Symptom </label>
                                            <div class="col-md-6">
                                                <select class="form-control symptom " id="symptom" name="part[0][symptom]" >
                                                        <option selected disabled>Select Symptom</option>
                                                        <?php foreach($technical_problem as $problem){ ?>

                                                            <option value="<?php echo $problem['id'] ?>" ><?php echo $problem['symptom'] ?></option>

                                                        <?php }  ?>

                                                    </select>  
                                            </div>

                                        </div>
                                    </div>


                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="defect_pic" class="col-md-4">Defect Picture *</label>
                                            <div class="col-md-6">
                                                <input type="file"   class="form-control defect_pic spare_parts " id="defect_pic">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>
                         <?php if(!empty($spare_parts_details)) { ?>
                        <div class="row">
                        <div class="col-md-11" style="margin-left:10px;">
                            <table class="table table-bordered spare-consumption">
                                <caption style="font-weight:bold;">Please let us know the status of the previously requested parts.</caption>
                                <thead>
                                    <tr style="background-color:#f5f5f5;">
                                        <th width="2%">S.No.</th>
                                        <th width="20%">Part Number</th>
                                        <th width="15%">Part Name</th>
                                        <th width="10%">Part Type</th>
                                        <th width="15%">Status</th>
                                        <th width="23%">
                                            <a href="javascript:void(0);" data-trigger="hover" data-html="true" data-toggle="popover" data-placement="left" title="Consumption Status Description" data-content="">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                            </a> Consumption Reason<span style="color:red;">*</span> 
                                        </th>
                                        <th width="15%">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($spare_parts_details as $SerialNo => $spare_part_detail) { $consumption_status_description = '';
                                        if(empty($spare_part_detail['parts_shipped'])) { continue; }
                                    ?>
                                    <tr>
                                        <td><?php echo ++$SerialNo; ?></td>
                                        <td><?php echo $spare_part_detail['part_number']; ?></td>
                                        <td><?php echo $spare_part_detail['parts_requested']; ?></td>
                                        <td><?php echo $spare_part_detail['parts_requested_type']; ?></td>
                                        <td><?php echo $spare_part_detail['status']; ?></td>
                                        <td>
                                            <select style="width:100%;" name="spare_consumption_status[<?php echo $spare_part_detail['id']; ?>]" class="spare_consumption_status" id="spare_consumption_status_<?php echo $spare_part_detail['id']; ?>">
                                                <option value="" selected disabled>Select Reason</option>
                                                <?php $description_no = 1; foreach($spare_consumed_status as $k => $status) {
                                                    if (!empty($status['status_description'])) { $consumption_status_description .= $description_no.". <span style='font-size:12px;font-weight:bold;'>{$status['consumed_status']}</span>: <span style='font-size:12px;'>{$status['status_description']}.</span><br />"; } ?>
                                                    <option value="<?php echo $status['id']; ?>" data-shipped_inventory_id="<?php echo $spare_part_detail['shipped_inventory_id']; ?>" data-tag="<?php echo $status['tag']; ?>" data-part_number="<?php echo $spare_part_detail['part_number']; ?>" data-spare_id="<?php echo $spare_part_detail['id']; ?>"
                                                    <?php if(isset($en_consumpton_details)){
                                                        if($en_consumpton_details[$spare_part_detail['id']]['consumption_status_id'] == $status['id']){
                                                           echo "selected"; 
                                                        }
                                                    } ?>
                                                    ><?php echo $status['consumed_status']; ?></option>
                                                <?php $description_no++; } ?>
                                            </select>

                                        </td>
                                        <td>
                                            <textarea name="consumption_remarks[<?php echo $spare_part_detail['id']; ?>]" rows="2" class="form-control"></textarea>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <span hidden id="status_consumption_status"><?php echo $consumption_status_description; ?></span>
                        </div>
                    </div>       
                        <?php } ?>
                        <div class="row" >
                            <div class="col-md-12" style="margin-left:10px; margin-right:10px;">
                                <div class="form-group">
                                    <label for="prob_desc" class="col-md-4">Problem Description* </label>
                                    <div class="col-md-11" style="width: 89.666667%;">
                                        <textarea class="form-control spare_parts"  id="prob_desc" name="reason_text" rows="5" placeholder="Problem Description" ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-warning"> <span class="badge badge-info"><i class="fa fa-info"></i></span> * These fields are required</div>
                    </div>
                    <div  id="hide_rescheduled" >
                        <div class="form-group">
                            <label for="reschdeduled" class="col-md-2"> New Booking Date</label>
                            <div class="col-md-4" style="width:24%">
                                <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control rescheduled_form" placeholder="Select Date" name="booking_date" type="text" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on" onclick="booking_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="hide_remarks">
                        <label for="remarks" class="col-md-2">Remarks *</label>
                        <div class="col-md-4" style="width:24%">
                            <textarea class="form-control remarks"  id="sc_remarks" name="sc_remarks" value = "" placeholder="Enter Remarks" rows="5" ></textarea>
                        </div>
                    </div>
                    
                    <div class='row' style='padding-bottom: 10px;display:none' id='part_brought_at'>
                        <label for="prob_desc" class="col-md-2">Did you bring appliance at workshop ?</label>
                        <div class='col-md-6'>
                            <input type='radio' name='part_brought_at' value=2 <?php if (!empty($booking_set_location[0]['part_brought_at']) && $booking_set_location[0]['part_brought_at'] == 2) { ?> checked onclick='return false'<?php } ?>>&nbsp;&nbsp;Yes &nbsp;&nbsp;
                            <input type='radio' name='part_brought_at' value=1 <?php if (!empty($booking_set_location[0]['part_brought_at']) && $booking_set_location[0]['part_brought_at'] == 1) { ?>checked <?php } ?> <?php if (!empty($booking_set_location[0]['part_brought_at']) && $booking_set_location[0]['part_brought_at'] == 2) { ?> onclick='return false'<?php } ?>>&nbsp;&nbsp;No
                                                                                                 
                        </div>
                    </div>
                    <?php 
                   //if current status is completed or cancelled by admin. SF cannot update the booking.
                   if(($bookinghistory[0]['current_status'] == _247AROUND_COMPLETED)||($bookinghistory[0]['current_status'] == _247AROUND_CANCELLED)){
                    echo "<center><b>This booking is ".$bookinghistory[0]['current_status']." by Admin. You cannot update this booking.</b></center>";}
                    else {
                    ?>
                    <div class="col-md-6 col-md-offset-2">
                        <input type="button"  value="Update Booking" id="submitform" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return submitForm();"   class="btn btn-danger btn-large">
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
<div id="CancelBookingOtpModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="cancel_booking_otp_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style='display: none;'>&times;</button>
                <h4 class="modal-title">Reschedule Booking</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-md-12">
                        <label>Enter customer OTP here</label>
                        <input class="form-control" type="text" name="reschedule_booking_otp" id="reschedule_booking_otp" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <input type="submit" id="reschedule_booking_otp_btn" name="cancel-otp" value="Reschedule" class="btn btn-primary form-control" style="margin-top:2px;">
                    </div>
                </div>
                
                <div class="row" style="margin-top:3%;">
                    <div class="col-md-12 form-group">
                        <span class="text-info"><b>Note :</b> OTP has been sent to customer. OTP is valid for 5 mins.</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".spare_consumption_status").select2();
    $('#symptom_0').select2();
    $('[data-toggle="popover"]').attr('data-content', $('#status_consumption_status').html());
    function alpha(e) {
       var k;
       document.all ? k = e.keyCode : k = e.which;
       return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57) || k==189);
    }

    function validate_serial_number_and_warranty()
    {
        // Validate Serial Number in case of Spare Request page
        var reason = $("input[name='reason']:checked"). val();
        if(reason !== "<?php echo SPARE_PARTS_REQUIRED;?>" && reason !== "<?php echo SPARE_OOW_EST_REQUESTED;?>"){
            return true;
        }
        var postData = {};
        var valid_serial_number = false;
        var UrlValidateSerialNumber = '<?php echo base_url(); ?>employee/service_centers/validate_booking_serial_number';
        postData['serial_number'] = $.trim($("#serial_number").val());
        postData['price_tags'] = "<?= $bookinghistory[0]['request_type']?>";
        postData['user_id'] = "<?= $bookinghistory[0]['user_id']?>";
        postData['booking_id'] = "<?= $bookinghistory[0]['booking_id']?>";
        postData['partner_id'] = "<?= $bookinghistory[0]['partner_id']?>";
        postData['appliance_id'] = "<?= $bookinghistory[0]['service_id']?>";
        if(postData['serial_number'] !== ''){
            $.ajax({
                type: 'POST',
                async: false,
                url: UrlValidateSerialNumber,
                data:postData,
                success: function (response) {                    
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if(data.code === 247){
                        console.log("Correct Serial Number");
                        $("#is_sn_correct").val("1");
                        $(".error_serial_no").html("");
                        valid_serial_number = check_booking_request();
                    } else if(data.code === Number(DUPLICATE_SERIAL_NO_CODE)){
                        console.log("Duplicate Serial Number");
                        $("#is_sn_correct").val("0");
                        $(".error_serial_no").html(data.message);
                    } else {
                        console.log("Incorrect Serial Number");
                        $("#is_sn_correct").val("0");
                        $(".error_serial_no").html(data.message);
                    }
                }
            });
        }        
        else{
            valid_serial_number = check_booking_warranty();
        }
        return valid_serial_number;        
    }
    
    // function to cross check request type of booking with warranty status of booking 
    function check_booking_request()
    {
        var model_number = $('#model_number').val();
        var dop = $("#dop").val();
        var serial_number = $("#serial_number").val();
        var partner_id = "<?= $bookinghistory[0]['partner_id']?>";
        var brand = "<?= $unit_details[0]['appliance_brand']; ?>";
        var service_id = "<?= $bookinghistory[0]['service_id']?>";
        var booking_id = "<?= $bookinghistory[0]['booking_id']?>";
        var booking_request_type = "<?= $bookinghistory[0]['request_type']; ?>";  
        var booking_create_date = "<?= $bookinghistory[0]['booking_create_date']; ?>"; 
        var valid_request = false;
        // Model Number & DOP/Serial number should be there for checking warranty
        // Booking Request Type should not be AMC/repeat
        if((model_number !== "" && model_number !== null) && (dop !== "" || serial_number != "")){                               
            $.ajax({
                method:'POST',
                async: false,
                url:"<?php echo base_url(); ?>employee/service_centers/get_warranty_data/2/1",
                data:{
                    'bookings_data[0]' : {
                        'partner_id' : partner_id,
                        'brand' : brand,
                        'booking_id' : booking_id,
                        'booking_create_date' : booking_create_date,
                        'service_id' : service_id,
                        'model_number' : model_number,
                        'purchase_date' : dop, 
                        'serial_number' : serial_number,
                        'booking_request_types' : [booking_request_type]
                    }
                },
                success:function(response){
                    var returnData = JSON.parse(response);
                    $(".errorMsg").html(returnData['message']);
                    if(returnData['status'] == 1)
                    {
                        console.log("Invalid Booking Request Type");
                    }
                    else
                    {                        
                        if($("#is_sn_correct").val() == "1"){
                            console.log("Valid Booking Request Type");
                            valid_request = true;
                        }
                    }
                }                            
            });
            return valid_request;
        }
    }
    
    <?php if(isset($inventory_details) && !empty($inventory_details)) { ?> 
        <?php if(!$is_disable) { ?>
            $('#model_number_id').select2();
        <?php } else { ?>
            $("#model_number_id").css({"cursor" : "not-allowed", "pointer-events" : "none", "background" : "#eee" });
        <?php } ?>
        $('#parts_name_0').select2({
            placeholder: "Select Part Name",
            allowClear:true
        });
        $('#parts_type_0').select2({
            placeholder: "Select Part Type",
            allowClear:true
        });
        
//      function getPartTypes(){
//            $('#model_number_id').on('change', function() {
//            var model_number_id = $('#model_number_id').val();
//            
//            var model_number = $("#model_number_id option:selected").text();
//            $('#spinner').addClass('fa fa-spinner').show();
//            if(model_number){
//                $('#model_number').val(model_number);
//                $.ajax({
//                    method:'POST',
//                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
//                    data: { model_number_id:model_number_id},
//                    success:function(data){
//                        $('.parts_type').val('val', "");
//                        $('.parts_type').val('Select Part Type').change();
//                        $('.parts_type').html(data);
//                        $('.parts_name').val('val', "");
//                        $('.parts_name').val('Select Part Type').change();
//                        $('#spinner').removeClass('fa fa-spinner').hide();
//                    }
//                });
//            }else{
//                alert("Please Select Model Number");
//            }
//        });
//      }  

   
 
 function getPartType(){
            var model_number_id = $('#model_number_id option:selected').val();
            var model_number = $("#model_number_id option:selected").text();
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number){
                $('#model_number').val(model_number);
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_type/1',
                    data: { model_number_id:model_number_id},
                    success:function(data){
                        $('.parts_type').val('val', "");
                        $('.parts_type').val('Select Part Type').change();
                        $('.parts_type').html(data);
                        $(".select2-container").css('width','100% !important%');
                        $('.parts_name').val('val', "");
                        $('.parts_name').val('Select Part Type').change();
                        $('#spinner').removeClass('fa fa-spinner').hide();
                        
                    }
                });
            }else{
                alert("Please Select Model Number");
            }
     
 }
 
 <?php if(!empty($is_modal_number)) { ?>
 
 getPartType();
 <?php } ?>
 
        
        $('#model_number_id').on('change', function() {
        
            getPartType();
        });
        
        
        $('.parts_type').on('change', function() {
            
        });
        
        function part_type_changes(count){
            var model_number_id = $('#model_number_id').val();
           $("#parts_quantity_"+count).removeAttr("readonly");
            var part_type = $('#parts_type_' + count).val();
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number_id && part_type){
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_name/1',
                    data: {model_number_id:model_number_id,entity_id: '<?php echo $bookinghistory[0]['partner_id']?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $bookinghistory[0]['service_id']; ?>', part_type:part_type},
                    success:function(data){
                        console.log(data);
                        $('#parts_name_' + count).val('val', "");
                        $('#parts_name_' +count).html(data).change();;
                        $('#spinner').removeClass('fa fa-spinner').hide();
                    }
                });
                //change part number too with above ajax
            }else{
                console.log("Please Select Model Number");
            }
        }
        $(document).ready(function(){
            $(document).on("change",".parts_name",function(){
                var id = $(this)[0].id;
                if(!!id){
                    var count = id.split("_")[2];
                    if(!!count){
                        var model_number_id = $('#model_number_id').val();
           
                        var part_type = $('#parts_type_' + count).val();
                        var part_name = $(this).val();
                        if(!!part_name){
                            if(model_number_id && part_type){
                                $.ajax({
                                    method:'POST',
                                    url:'<?php echo base_url(); ?>employee/inventory/get_part_number/1',
                                    data: {
                                        model_number_id:model_number_id,
                                        entity_id: '<?php echo $bookinghistory[0]['partner_id']?>' ,
                                        entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' ,
                                        service_id: '<?php echo $bookinghistory[0]['service_id']; ?>',
                                        part_type:part_type,
                                        part_name: part_name
                                    },
                                    success:function(data){
                                        $('#parts_number_' + count).val('val', "");
                                        $('#parts_number_' +count).html(data).change();
                                        $('#spinner_part_number').removeClass('fa fa-spinner').hide();
                                        $('#parts_number_' + count).select2();
                                    }
                                });
                            }
                        }
                        else{
                            $('#parts_number_' + count).empty().select2();
                        }
                    }
                }
            });
        });
        
        
    <?php } else { ?>
        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/inventory/get_appliance_model_number',
            data: {partner_id: '<?php echo $bookinghistory[0]['partner_id']?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $bookinghistory[0]['service_id']; ?>'},
            success:function(data){
                if(data){
                    $("#appliance_model_div").empty();
                    var html = "<select class='form-control spare_parts' id='model_number_id' name='model_number_id' onchange='model_number_text();'>";
                        html += data;
                        html += "</select>";
                        html += "<input type='hidden' id='model_number' name='model_number'>";
                        $("#appliance_model_div").html(html);
                        <?php if(!$is_disable) { ?>
                            $('#model_number_id').select2();
                        <?php } else { ?>
                            $("#model_number_id").css({"cursor" : "not-allowed", "pointer-events" : "none", "background" : "#eee" });
                        <?php } ?>
                        var model_number = "<?php echo $unit_model_number; ?>";
                        $('#model_number_id option').map(function() {
                        if ($.trim(($(this).text()).toUpperCase()) == $.trim(model_number.toUpperCase())){
                            var model_no = $(this).val();
                            $("#model_number_id").val(model_no).change();
                        }                            
                        });
                         
                        var model_id = $( "#model_number_id option:selected" ).val();
                        $(".select2-container--default").css('width','100%');
                }
            }
        });
        
        function model_number_text() {
            var model_number = $("#model_number_id option:selected").text();
            $('#model_number').val(model_number);
        }
        
   <?php } ?>
    
    $(document).ready(function (){
       $(".spare_parts").attr("disabled", "true");
       <?php if(isset($consume_spare_status) && $consume_spare_status == true && $spare_flag == SPARE_PARTS_REQUIRED){ ?>
            internal_status_check('spare_parts');
            $("#spare_parts").prop('checked', true);
       <?php } ?>
    });
        
    function submitForm(){
                                     
     var checkbox_value = 0;
     $("input[type=radio]:checked").each(function(i) {
         checkbox_value = 1;
    
     });

     if(checkbox_value ===0){
          alert('Please select atleast one checkbox.');
          checkbox_value = 0;
     }

     // check for consumption reason if spare part required is checked.
     $('.spare-consumption').children('tbody').children('tr').each(function(index) {
        var checkReason = $(this).find("td:eq(5)").children('.spare_consumption_status').val();
        var reason = $("input[name='reason']:checked"). val();
        if(reason == "<?php echo SPARE_PARTS_REQUIRED; ?>" || reason == "<?php echo SPARE_OOW_EST_REQUESTED; ?>") {
            if(checkReason == null || checkReason == '') {
                alert('Please let us know the status of the previously requested parts.');
                checkbox_value = 0;
                return false;
            }
        }
     });

      var reason = $("input[name='reason']:checked"). val();
      if(reason === "<?php echo CUSTOMER_ASK_TO_RESCHEDULE; ?>" 
              || reason === "<?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER; ?>" 
              || reason === "<?php echo RESCHEDULE_FOR_UPCOUNTRY; ?>"
              || reason === "<?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF;?>"){
          
          var booking_date = $('#booking_date').val();
          if(booking_date === ""){
              alert("Please select new date");
              checkbox_value = 0;
          }
          
          var sc_remarks = $("#sc_remarks").val();
          if(sc_remarks === ""){
              alert("Please Enter remarks");
              checkbox_value = 0;
          }
         
            // ask otp to enter.
//            var booking_id = $('#booking_id').val();
//            var sms_template = '';
//
//            // send one time password to customer and open popup.
//            $.ajax({
//                method : 'POST',
//                url: 'employee/service_centers/send_otp_customer',
//                data: {booking_id, sms_template},
//            }).done(function(data) {
//                response = $.trim(data);
//                $('#verified_otp').val(data);
//                $('#CancelBookingOtpModal').modal({backdrop: 'static', keyboard: false});
//                // hide modal after 5 mins.
//                setTimeout(function() {$('#CancelBookingOtpModal').modal('hide');}, 300000);
//                $('#submitform').val('Proceed');
//            });       
//
//            return false;
      } else if(reason === "<?php echo SPARE_PARTS_REQUIRED;?>" || reason === "<?php echo SPARE_OOW_EST_REQUESTED; ?>"){
          var around_flag = $('#partner_flag').val();
          
          if(around_flag === '0'){ 
              var model_number = $('#model_number').val();
              var serial_number = $("#serial_number").val();
              var prob_des = $("#prob_desc").val();
              var dop = $("#dop").val();
              var serial_number_pic = $('#serial_number_pic').val();               

              if(model_number ==="" || model_number === null){
                  alert("Please enter model number");
                  checkbox_value =0;
                  return false;
              }
              
              if(dop === ""){
                alert("Please Select Date of Purchase");
                checkbox_value = 0; 
                return false;
                
              }
              
              if(serial_number === "" || serial_number === null){
                alert("Please Enter serial number");
                checkbox_value = 0;
                return false;
              }            
  <?php if((!isset($unit_serial_number_pic) ||  empty($unit_serial_number_pic)) && empty($on_saas)){ ?>
      
                if(serial_number_pic.length === 0){
                alert("Please Upload Serial Number Image");
                checkbox_value = 0; 
                return false;
               } 
              
  <?php } ?>            
            $('.spare_parts_type').each(function() {
                var id = $(this).attr('id');
                if(id !== "parts_type"){
                    if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                        alert('Please Enter Part Type');
                        checkbox_value = 0;
                       return false;
                    }
                }
            });
         
            $('.parts_type').each(function() {
                  var id = $(this).attr('id');
                  if(id !== "parts_type"){
                      if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                          alert('Please Enter Part Type');
                          checkbox_value = 0;
                         return false;
                      }
                  }
              });
              
              $('.parts_name').each(function() {
                var id = $(this).attr('id');
                if(id !== "parts_name"){
                    if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                        alert('Please Enter Part Name');
                        checkbox_value = 0;
                        return false;
                        
                    }
                  }
                
                });
            
           
    var ptypes =[];
    $(".parts_type_check").each(function(i) {


         var current = $(this).val();
         if (ptypes.length>0) {
 
            var n = ptypes.includes(current);
             if (n) {
               alert("Same part type can not be requested.For multiple part please fill quantity.");
               checkbox_value = 0;
               return false;
             }else{
              ptypes.push(current);
             } 
         }else{
          
           ptypes.push(current); 

         }
          
     });
              
    <?php if(empty($on_saas)){ ?>
            $('.defective_parts_pic').each(function() {
                var id = $(this).attr('id');
                if(id !== "defective_parts_pic"){
                    if($(this).val().length === 0){
                        alert('Please Upload Defective Front Part Image');
                        checkbox_value = 0;
                       return false;
                    }
                }
            });
            
            $('.defective_back_parts_pic').each(function() {
                var id = $(this).attr('id');
                if(id !== "defective_back_parts_pic"){
                    if($(this).val().length === 0){
                        alert('Please Upload Defective Back Part Image');
                        checkbox_value = 0;
                       return false;
                    }
                }
            });
            
            $('.defect_pic').each(function() {
                var id = $(this).attr('id');
                if(id !== "defect_pic"){
                    if($(this).val().length === 0){
                        alert('Please Upload Defective Image');
                        checkbox_value = 0;
                       return false;
                    }
                }
            });
        <?php if(empty($invoice_file)) { ?>
            var invoice_pic = $("#invoice_pic").val();    
            $('.part_in_warranty_status').each(function() {
                var id = $(this).attr('id');
                if(id !== "part_in_warranty_status"){
                    if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                        alert('Please Select Part Warranty Status');    
                        checkbox_value = 0;
                       return false;
                    }else if((($(this).val() == 1) || ($('#is_invoice_required').val() == 1)) && (invoice_pic =='' || invoice_pic == null)){
                        alert('Please Upload Invoice Picture.');    
                        checkbox_value = 0;
                        return false;
                    }
                }
            });
           
        <?php } } ?>
            /*$('.spare_request_symptom').each(function() {
                var id = $(this).attr('id');
                if(id !== "spare_request_symptom"){
                    if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                        alert('Please Select Technical Problem');    
                        checkbox_value = 0;
                       return false;
                    }
                }
            });*/
              
            if(prob_des === "" || prob_des === null){
                alert("Please Enter problem description");
                checkbox_value = 0;
                return false;
            }
            
            radioName = 'part_brought_at';
            if ($('input[name='+radioName+']:checked').length == '0'){
                alert("Please select option, Did you bring appliance at workshop ?");
                checkbox_value = 0;
                return false;
            }
                          
          } else if(around_flag === '1'){
              var parts_name1 = $('#247parts_name').val();
              var reschduled_booking_date = $("#reschduled_booking_date").val();
              var reason_text = $("#247reason_text").val();
    
              if(parts_name1 === ""){
                   alert("Please Enter parts name");
                  checkbox_value = 0;
                  return false;
              }
              
               if(reschduled_booking_date === ""){
                  alert("Please select reschedule date");
                  checkbox_value = 0; 
                  return false;
              }
              
          }
      } else {
          var sc_remarks = $("#sc_remarks").val();
          if(sc_remarks === ""){
              alert("Please Enter remarks");
              checkbox_value = 0;
          }

      }
      
      
         
        if(checkbox_value === 0){
            $('#submitform').val("Update Booking");
            return false;
          
        } else if(checkbox_value === 1){          
            if(confirm("Validating Serial Number & Booking Warranty Status, Click OK to continue.")){
                $("#submitform").prop("disabled", true);
                $("#submitform").attr('value', 'Validating Data...');
                setTimeout(function(){ 
                    var is_correct = validate_serial_number_and_warranty();
                    if(is_correct){
                        $("#requested_parts").submit();
                        return true;
                    }
                    else{                        
                        $("#submitform").attr('value', "Update Booking");
                        $("#submitform").prop("disabled", false);
                        return false;
                    }
                }, 3000);                
            }          
        }    
    }
    
    $('#reschedule_booking_otp_btn').on('click', function() {
        var reschedule_booking_otp = $('#reschedule_booking_otp').val();
//        if(reschedule_booking_otp == '' || reschedule_booking_otp == null) {
//            alert('Please enter OTP.');
//            return false;
//        }
//        if(reschedule_booking_otp == response) {
//            $('#requested_parts').submit();
//            return true;
//        }
//        
//        alert('Entered OTP is incorrect.');
//        return false;
        
        if(reschedule_booking_otp == '' || reschedule_booking_otp == null) {
            $('#requested_parts').submit();
            return true;
        }
        if(reschedule_booking_otp == response) {
            $('#requested_parts').submit();
            return true;
        }
        
        alert('Entered OTP is incorrect.');
        return false;
    });
    
    function internal_status_check(id){
        if(id ==="spare_parts"){
            $('#hide_spare').show();
            $(".spare_parts").removeAttr("disabled");
            $(".parts_number").prop("disabled","true");
            $(".rescheduled_form").attr("disabled", "true");
            $('#hide_rescheduled').hide();
            $(".remarks").attr("disabled", "true");
            $('#hide_remarks').hide();
            $('#submitform').val('Update Booking');
            $("#part_brought_at").show();
        } else  if(id ==="rescheduled" || id === "product_not_delivered" 
                || id=== "reschedule_for_upcountry"
                || id=== "spare_not_delivered"){
            $(".spare_parts").attr("disabled", "true");
            $('#hide_spare').hide();
            $('#hide_rescheduled').show();
            $(".rescheduled_form").removeAttr("disabled");
            $('#hide_remarks').show();
            $(".remarks").removeAttr("disabled");
            $("#part_brought_at").show();
            //$('#submitform').val('Proceed');
        }  else {
         $(".spare_parts").attr("disabled", "true");
         $(".rescheduled_form").attr("disabled", "true");
         $('#hide_spare').hide();
         $('#hide_rescheduled').hide();
         $('#hide_remarks').show();
         $(".remarks").removeAttr("disabled");
         $('#submitform').val('Update Booking');
         $("#part_brought_at").hide();
        }
    }
    
    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: +1, maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>', changeMonth: true,changeYear: true});
    $("#dop").datepicker({dateFormat: 'dd-mm-yy', changeMonth: true,changeYear: true, 
                maxDate:0});
    $("#reschduled_booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: 0, 
                maxDate:0
    });
     function booking_calendar(){
      
        $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: false,changeYear: false}).datepicker('show');
    }
    
    function dop_calendar(){
         $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true}).datepicker('show');
    }
    
    function reschduled_booking_date_calendar(){
        $("#reschduled_booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: 0, 
                maxDate:+7
        }).datepicker('show');
    }

    var partIndex = 0;
    $('#requested_parts').on('click', '.addButton', function () {
        
          if(partIndex >0){
              $("#remove_section_"+partIndex).hide();
          }
            partIndex++;
            var $template = $('#template'),
                $clone = $template
                        .clone()
                        .removeClass('hide')
                        .removeAttr('id')
                        .attr('data-book-index', partIndex)
                        .insertBefore($template);
        


            // Update the name attributes
            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                    $clone
                        .find('[id="parts_name"]').attr('name', 'part[' + partIndex + '][parts_name]').addClass('parts_name').attr('id','parts_name_'+partIndex).select2({placeholder:'Select Part Type'}).attr("required", true).end()
                        .find('[id="parts_number"]').attr('name', 'part[' + partIndex + '][parts_number]').addClass('parts_number').attr('id','parts_number_'+partIndex).select2().end()
                        .find('[id="parts_type"]').attr('name', 'part[' + partIndex + '][parts_type]').addClass('parts_type parts_type_check').attr('id','parts_type_'+partIndex).attr("onchange", "part_type_changes('"+partIndex+"')").attr("required", true).select2({placeholder:'Select Part Type'}).end()
                        .find('[id="requested_inventory_id"]').attr('name', 'part[' + partIndex + '][requested_inventory_id]').attr('id','requested_inventory_id_'+partIndex).end()
                        .find('[id="defective_parts_pic"]').attr('name', 'defective_parts_pic[' + partIndex + ']').addClass('defective_parts_pic').attr('id','defective_parts_pic_'+partIndex).end()
                        .find('[id="defective_back_parts_pic"]').attr('name', 'defective_back_parts_pic[' + partIndex + ']').addClass('defective_back_parts_pic').attr('id','defective_back_parts_pic_'+partIndex).end()
                        .find('[id="part_warranty_status"]').attr('name', 'part[' + partIndex + '][part_warranty_status]').addClass('part_in_warranty_status').attr('id','part_warranty_status_'+partIndex).attr("required", true).end()//.attr("onchange", "get_symptom('"+partIndex+"')") error_span
                        .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').addClass('quantity').attr('id','parts_quantity_'+partIndex).attr("required", true).end()

                        .find('[id="symptom"]').attr('name', 'part[' + partIndex + '][symptom]').addClass('symptom').attr('id','parts_symptom_'+partIndex).attr("required", true).select2({placeholder:'Select Symptom'}).end()

                        .find('[id="defect_pic"]').attr('name', 'defect_pic[' + partIndex + ']').addClass('defect_pic').attr('id','parts_defect_pic_'+partIndex).attr("required", true).end()
                        .find('[id="error_span"]').addClass('hide').attr('id','error_span_'+partIndex).attr("required", true).end()
                        .find('[id="inventory_stock"]').attr('id', 'inventory_stock_'+partIndex).end()
                        .find('[id="parts_image"]').attr('id', 'parts_image_'+partIndex).end()                
                        .find('[id="remove_section"]').attr('id', 'remove_section_'+partIndex).end()
                
            <?php } else { ?>
                $clone

                   .find('[id="parts_type"]').attr('name', 'part[' + partIndex + '][parts_type]').addClass('parts_type parts_type_check').attr('id','parts_type_'+partIndex).attr("required", true).select2({placeholder:'Select Part Type'}).end()
                   .find('[id="parts_name"]').attr('name', 'part[' + partIndex + '][parts_name]').addClass('parts_name').attr('id','parts_name_'+partIndex).attr("required", true).end()
                   .find('[id="requested_inventory_id"]').attr('name', 'part[' + partIndex + '][requested_inventory_id]').attr('id','requested_inventory_id_'+partIndex).end()
                   .find('[id="defective_parts_pic"]').attr('name', 'defective_parts_pic[' + partIndex + ']').addClass('defective_parts_pic').attr('id','defective_parts_pic_'+partIndex).end()
                   .find('[id="part_warranty_status"]').attr('name', 'part[' + partIndex + '][part_warranty_status]').addClass('part_in_warranty_status').attr('id','part_warranty_status_'+partIndex).attr("required", true).end()//.attr("onchange", "get_symptom('"+partIndex+"')")
                   .find('[id="quantity"]').attr('name', 'part[' + partIndex + '][quantity]').addClass('quantity').attr('id','parts_quantity_'+partIndex).attr("required", true).end()

                   .find('[id="symptom"]').attr('name', 'part[' + partIndex + '][symptom]').addClass('symptom').attr('id','parts_symptom_'+partIndex).attr("required", true).select2({placeholder:'Select Symptom'}).end()

                   .find('[id="defect_pic"]').attr('name', 'defect_pic[' + partIndex + ']').addClass('defect_pic').attr('id','parts_defect_pic_'+partIndex).attr("required", true).end()

                   .find('[id="error_span"]').addClass('hide').attr('id','error_span_'+partIndex).attr("required", true).end()
                   .find('[id="defective_back_parts_pic"]').attr('name', 'defective_back_parts_pic[' + partIndex + ']').addClass('defective_back_parts_pic').attr('id','defective_back_parts_pic_'+partIndex).end()
                   .find('[id="inventory_stock"]').attr('id', 'inventory_stock_'+partIndex).end()
                   .find('[id="parts_image"]').attr('id', 'parts_image_'+partIndex).end()  
                   .find('[id="remove_section"]').attr('id', 'remove_section_'+partIndex).end()
            <?php } ?>
    
        }) 
    
        // Remove button click handler
        .on('click', '.removeButton', function () {
            var $row = $(this).parents('.spare_clone'),
                index = $row.attr('data-part-index');
                partIndex = partIndex -1;
                $("#remove_section_"+partIndex).show();
            $row.remove();
        });
    function get_inventory_id(id){       
        var inventory_id =$("#"+id).find('option:selected').attr("data-inventory"); 
        var part_image =$("#"+id).find('option:selected').attr("data-partimage"); 
        var str_arr =id.split("_");
        indexId = str_arr[2]; 
        $("#requested_inventory_id_"+indexId).val(inventory_id);
        if((part_image !=undefined || part_image !=null) && part_image !=''){
           $("#parts_image_"+indexId).attr("href", "<?php echo S3_WEBSITE_URL."misc-images/"; ?>"+part_image); 
           $("#parts_image_"+indexId).css('display','inline');
        }else{
            $("#parts_image_"+indexId).css('display','none');
        }
        
        if(inventory_id!=undefined){           
           $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_inventory_stock_count',
                    data: {service_centres_id:'<?php echo $this->session->userdata('service_center_id'); ?>',inventory_id:inventory_id,entity_type: '<?php echo _247AROUND_SF_STRING; ?>'},
                    success:function(data){
                        obj=JSON.parse(data);                        
                        $('#inventory_stock_'+indexId).html("Available Stock- "+obj['total_stock']).css({'padding': '5px','font-weight': 'bold'});
                    }
                });
        }
        
    }


     $(document).on('keyup', ".parts_name", function(e){
        $(".quantity").removeAttr("readonly");
     });
    
     $(document).on('keyup', ".quantity", function(e)
       {

        var charCode = (e.which) ? e.which : e.keyCode;
        if ((charCode > 47 && charCode < 58) || (charCode > 95 && charCode < 105) || charCode == 8) {
     
        var id = $(this).attr("id");
        var str_arr =id.split("_");
        var indexId = str_arr[2]; 
        var val =$(this).val(); 
        val = Math.floor(parseInt(val));
        $(this).val(val);
        if (val>0) {
         var max = parseInt($("#parts_name_"+indexId+" option").filter(":selected").attr("data-maxquantity"));
        if(val>max){
         $(this).val("1");
        // swal("Error !", "Maximum quantity'allowed to ship is : "+max);
           $("#error_span_"+indexId).text('Maximum quantity allowed to ship is : '+max);
           $("#error_span_"+indexId).removeClass('hide');
        }else{
             $("#error_span_"+indexId).addClass('hide');
        }
        }else{
          $(this).val("");
        //  swal("Error !", "0 quantity or negative value not allowed");  
          //$(this).css("border-color","red");
          $("#error_span_"+indexId).text('0 quantity,special charcter or negative value not allowed ');
          $("#error_span_"+indexId).removeClass('hide');
        }
        }else{
          $(this).val("");
          //swal("Error !", "Special chars not allowed");
           $("#error_span_"+indexId).text('');
           $("#error_span_"+indexId).text('Special chars not allowed');
           $("#error_span_"+indexId).removeClass('hide');

        }
       });
    
    
    $(document).ready(function(){
        var service_id = "<?php echo $bookinghistory[0]['service_id']; ?>";
        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/inventory/get_inventory_parts_type',
            data: { service_id:service_id},
            success:function(data){                       
                $('.spare_parts_type').html(data); 
                $('#parts_type_0').select2();
            }
        });
    });
      
    $(document).ready(function(){
        var model_number = $("#model_number_id option:selected").val();    
        if (!$('input[type=radio][name=reason]').length) {
            $("#seperator").hide();
            $("#submitform").hide();
        }
    });
        
    <?php if(isset($purchase_date) && (!empty($purchase_date) && $purchase_date != "0000-00-00")){ if($is_disable){  ?>
    <?php if(!$ask_purchase_date) { ?>
        $("#dop").attr('readonly', 'readonly');
        $("#dop").css("cursor", "not-allowed");
        $("#dop").css("pointer-events","none");    
    <?php } ?>
        $("#dat_of_puchase").css("cursor", "not-allowed");
        $("#dat_of_puchase").css("pointer-events","none");
        <?php if(!$ask_purchase_date) { ?>
        $("#dop_calendar").attr("onclick", "").unbind("click");
        $("#dop").attr("tabindex",-1);
        <?php } ?>
     <?php } } ?>
         
    <?php if(isset($unit_serial_number_pic)  && !empty($unit_serial_number_pic)){ if($is_disable){ ?>
        $("#serial_number_pic").attr('readonly', 'readonly');
        $("#serial_number_pic").css("cursor", "not-allowed");
        $("#serial_number_pic").css("pointer-events","none");
        $("#serial_text").css("cursor", "not-allowed");
        $("#serial_text").css("pointer-events","none");
    <?php } } ?>
    <?php if(isset($unit_serial_number) && !empty($unit_serial_number)){ if($is_disable){ ?> 
        $("#serial_number").attr('readonly', 'readonly');
        $("#serial_number").css("cursor", "not-allowed");
        $("#serial_number").css("pointer-events","none");
    <?php } } ?>    
</script>
<style type="text/css">
    #hide_spare, #hide_rescheduled { display: none;}
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
