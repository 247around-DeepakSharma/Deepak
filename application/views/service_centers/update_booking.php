<script>
 var DUPLICATE_SERIAL_NO_CODE = '<?php echo DUPLICATE_SERIAL_NO_CODE; ?>';
 var DUPLICATE_SERIAL_NUMBER_USED = '<?php echo DUPLICATE_SERIAL_NUMBER_USED;?>';
</script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<script src="<?php echo base_url();?>js/base_url.js"></script>
<script src="<?php echo base_url();?>js/validation_js.js"></script>
<script src="<?php echo base_url();?>js/custom_js.js?v=<?=mt_rand()?>"></script>
<?php
// this is used to avoid all booking related extra validations on change request type panel
if(!empty($this->session->userdata('service_center_id')) || !empty($allow_skip_validations)){
    $allow_skip_validations = 1;
}
else
{
    $allow_skip_validations = "";
}

// get selected price tags
$selected_price_tags = !empty($unit_details[0]['quantity']) ? array_column($unit_details[0]['quantity'], 'price_tags') : [];
$selected_price_tags = implode(",", $selected_price_tags);
// Get saved Partner and 247 discounts against Booking
$arr_247around_discount = array();
$arr_partner_discount = array();
?>
<div id="page-wrapper" >
    <div class="container" >
        <div class="sf_edit_form_container" style="border: 1px solid #2c9d9c;    border-radius: 4px;">
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
                }
        ?>
        <?php
            $is_repeat_value = "";
            $parentBkng = "";
            $bkng_id = $booking_history[0]['booking_id'];
            $header = "Update Request type for ".$booking_history[0]['booking_id'];
            // if spare is requested on any booking or the booking is invoiced to partner, then booking details can not be edited
            $is_partner_invoiced = !empty($booking_history['is_partner_invoiced']) ? $booking_history['is_partner_invoiced'] : false;
            $str_disabled = ($booking_history['is_spare_requested'] || $is_partner_invoiced) ? "pointer-events:none;background:#eee;" : "";
            if($booking_history[0]['parent_booking'] && !$is_repeat){
                 $is_repeat_value = 1;
                 $parentBkng = $booking_history[0]['parent_booking'];
            }

            $button_caption = "Submit Booking";
            $redirect_url = "";
            // if Request Type Change is done before Booking completion from SF panel and Admin Panel
            // Do not insert data in booking_state_change Table
            $do_not_insert_state_change = 0;
            if(!empty($booking_history['redirect_url']))
            {
                $do_not_insert_state_change = 1;
                $button_caption = "Next";
                $redirect_url = $booking_history['redirect_url'];
                $header = "Step 1 : Update Request type for ".$booking_history[0]['booking_id'];
            }
            ?>
            <h1 style=" text-align: center;font-size: 26px;color: #2c9d9c;margin-bottom: -39px;"><?= $header ?></h1>
            <br/><br/>
            <h3 style="color:red;text-align: center;font-size: 16px;margin-bottom: -39px;font-weight:bold;" class="errorMsg"></h3>
            <div class="panel-body">                
                <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php if(isset($booking_history[0]['booking_id'])){ echo base_url()?>service_center/update_booking_by_sf/<?php echo $booking_history[0]['user_id'];?>/<?php echo $bkng_id;?>/<?php echo $do_not_insert_state_change;}  ?> "  method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $booking_history[0]['booking_id'] ?>" name="booking_id" id="booking_id">
                    <input type="hidden" value="<?php echo $is_repeat_value ?>" name="is_repeat" id="is_repeat">
                    <input type="hidden" name="upcountry_data" value="<?php echo json_decode(""); ?>" id="upcountry_data" /> 
                    <input type="hidden" id="name" name="user_name" value = "<?php echo $booking_history[0]['name'] ?>"/>
                    <input type="hidden" name="partner_type" value="<?php echo $partner_type; ?>" id="partner_type" />
                    <input type="hidden" name="is_active" value="<?php echo $active;?>" id="is_active" />
                    <input type="hidden" id="partner_channel" value="<?php echo $booking_history[0]['partner_source']; ?>"/>
                    <input type="hidden" name="booking_type" id="booking_type" value="<?php echo $booking_history[0]["type"];?>" />
                    <input type="hidden" name="partner_id" value="<?php echo $booking_history[0]['partner_id'];?>" id="partner_id" />
                    <input type="hidden" name="assigned_vendor_id" value="<?php if(!empty($booking_history[0]['assigned_vendor_id'])){ echo $booking_history[0]['assigned_vendor_id']; } else { echo '';} ?>" id="assigned_vendor_id" />
                    <input type="hidden" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $booking_history[0]['booking_primary_contact_no']?>" required  <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>/>
                    <input type="hidden" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($booking_history[0]['booking_pincode'])){echo $booking_history[0]['booking_pincode'];} ?>" placeholder="Enter Area Pin" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                    <input type="hidden" name="city" value="<?php echo strtolower($booking_history[0]['city']);?>" id="booking_city" />
                    <input type="hidden" name="service_id" value="<?php echo $booking_history[0]['service_id'];?>" id="service_id" />
                    <input type="hidden" name="service" id="services"/>
                    <input type="hidden" name="order_id" value="<?php if(isset($booking_history[0]['order_id'])){ echo $booking_history[0]['order_id']; } ?>" id="order_id" />
                    <input type="hidden" name="dealer_phone_number" value="<?php if(isset($booking_history[0]['dealer_phone_number'])){ echo $booking_history[0]['dealer_phone_number']; } ?>" id="dealer_phone_number" />
                    <input type="hidden" name="dealer_id" id="dealer_id" value="<?php if(isset($booking_history[0]['dealer_id'])){ echo $booking_history[0]['dealer_id']; } ?>">
                    <input type="hidden"  id="booking_user_email" name="user_email" value = "<?php echo $booking_history[0]['user_email']; ?>">
                    <input type="hidden"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php echo $booking_history[0]['alternate_phone_number']?>">
                    <input type="hidden"  id="source_code" name="source_code" value = "<?php echo $booking_history[0]['source']?>">
                    <input type="hidden"  id="partner_source" name="partner_source" value = "<?php echo $booking_history[0]['partner_source']?>">
                    <input type="hidden" value="<?php echo $parentBkng; ?>" name="parent_id" id="parent_id">
                    <input type="hidden" name= "dealer_name" value="<?php if(isset($booking_history[0]['dealer_name'])){ echo $booking_history[0]['dealer_name']; } ?>" id="dealer_name"/>
                    <input type="hidden" name="appliance_id[]" value="<?php if(isset($unit_details[0]['appliance_id'])){echo $unit_details[0]['appliance_id'];} ?>"/>
                    <input type="hidden" value="<?php echo $redirect_url ?>" name="redirect_url" id="redirect_url">
                    <input type="hidden" value="<?php echo $str_disabled ?>" name="is_spare_requested" id="is_spare_requested">                    
                    <input type="hidden" value="<?php echo $allow_skip_validations ?>" name="is_sf_panel" id="is_sf_panel">                                       
                    <input checked="checked" style ='visibility: hidden;' id="booking" type="radio" class="form-control booking_type" onclick="check_prepaid_balance('Booking')" name="type" value="Booking" <?php if($is_repeat){ echo "checked"; } ?> required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?>>
                    <input type="hidden" value="<?php echo $selected_price_tags ?>" name="selected_price_tags" id="selected_price_tags">                                       
                    <input type="hidden" value="<?php echo $booking_history[0]['user_id']?>" name="user_id" id="user_id">
                    <span style="display:none;" id="price_tags"><?php echo $booking_history[0]['request_type']?></span>
                    
                    <p id="parent_id_temp" style="display:none;"><?php echo $parentBkng; ?></p>
                    <p id="booking_old_type_holder" style="display:none;"><?php echo $booking_history[0]['type'] ?></p>
                    <span id="error_pincode" style="color:red"></span>
                    <?php if($is_partner_invoiced) { echo "<b><center><span class='text-danger' >".MSG_PARTNER_INVOICED."</span></center></span></b>"; } ?>                                                        
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12" style="padding:0;">
                                    <div class="col-md-6" style="width: 40%;">                                        
                                        <?php 
                                        $booking_model_number = !empty($unit_details[0]['sf_model_number']) ?  $unit_details[0]['sf_model_number'] : "";
                                        $booking_model_purchase_date = (!empty($unit_details[0]['sf_purchase_date']) && $unit_details[0]['sf_purchase_date'] != '0000-00-00') ?  date("d-m-Y", strtotime($unit_details[0]['sf_purchase_date'])) : "";
                                        if(!empty($booking_history['spare_parts'][0]['model_number']) && $booking_history['spare_parts'][0]['status'] != _247AROUND_CANCELLED){
                                            $booking_model_number = $booking_history['spare_parts'][0]['model_number'];
                                        }
                                        if(!empty($booking_history['spare_parts'][0]['date_of_purchase']) && $booking_history['spare_parts'][0]['status'] != _247AROUND_CANCELLED){
                                            $booking_model_purchase_date = date("d-m-Y",strtotime($booking_history['spare_parts'][0]['date_of_purchase']));
                                        }
                                        if(!empty($booking_model_number) && !empty($model[0]) && $booking_history['is_spare_requested']){
                                            $arrModels = array_column($model[0], 'model');
                                            $arrModels = array_map('strtoupper', $arrModels);
                                            if(!in_array(strtoupper($booking_model_number), $arrModels)){ ?>
                                                <input type="hidden" name="model_not_mapped" id="model_not_mapped" value="1"/>
                                                <div class="col-md-12" style="padding-bottom:10px;padding-top:0px;padding-left:0px;">
                                                    <span class="text-primary" ><i class="fa fa-warning"></i>&nbsp;Model Number '<?= $booking_model_number ?>' filled during Spare Request is not mapped with the partner! Please Contact Admin.</span>
                                                </div>
                                            <?php }
                                        }
                                        ?>
                                        <div class="form-group ">
                                            <label for="type" class="col-md-4">Appliance Model* </label>
                                            <div class="col-md-6">
                                                <?php if(empty($model[0])) { ?>
                                                    <input  type="text" class="form-control input-model"  name="model_number[]" id="model_number_1" style="<?= $str_disabled?>" value = "<?php echo $booking_model_number; ?>" placeholder="Enter Model"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> required onkeypress="return checkQuote(event);" oninput="return checkInputQuote(this);">
                                                <?php } else { ?>
                                                    <select class="form-control select-model"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="model_number_1" name="model_number[]" required onchange="getCapacityCategoryForModel(this.value, this.id);" style="<?= $str_disabled?>">
                                                        <option selected disabled value="">Select Appliance Model</option>
                                                        <?php foreach ($model[0] as $value) { ?>
                                                        <option <?php if(!empty($booking_model_number)) {if(trim(strtoupper($value['model'])) == trim(strtoupper($booking_model_number))) { echo "selected"; }} elseif(isset($unit_details[0]['model_number'])) {if(trim(strtoupper($value['model'])) == trim(strtoupper($unit_details[0]['model_number']))) { echo "selected"; }}?>
                                                            ><?php echo $value['model']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>                                                                                                
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="dop" class="col-md-4">Purchase Date* </label>
                                            <div class="col-md-6">
                                                <input <?php if($is_repeat && (!empty($booking_model_purchase_date))){ echo 'readonly="readonly"'; } ?> id="purchase_date_1" class="form-control purchase_date"  name="purchase_date[]" type="text" value = "<?php if(!empty($booking_model_purchase_date)){ echo $booking_model_purchase_date; }elseif(isset($unit_details[0]['purchase_date']) && $unit_details[0]['purchase_date'] != '0000-00-00'){ echo date("d-m-Y", strtotime($unit_details[0]['purchase_date'])); }?>" max="<?=date('Y-m-d');?>" autocomplete='off' onkeydown="return false" required style="<?= $str_disabled?>">
                                            </div>
                                        </div> 
                                        <div class="form-group ">
                                            <label for="service_name" class="col-md-4">Brand *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_brand"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>
                                                    name="appliance_brand[]" id="appliance_brand_1" onChange="getCategoryForService(this.id)"  required style="background: #eee;pointer-events: none;" tabindex=-1>
                                                    <option selected disabled>Select Brand</option>
                                                    <?php 
                                                        if(!empty($brand[0])) {
                                                            foreach ($brand[0] as  $appliance_brand) { ?>
                                                    <option <?php if(isset($unit_details[0]['brand'])) {  if (strcasecmp($appliance_brand['brand_name'], $unit_details[0]['brand']) == 0){ echo "selected";}}   ?>
                                                        ><?php echo $appliance_brand['brand_name']; ?></option >
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="service_name" class="col-md-4">Category *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_category"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_category_1" name="appliance_category[]"  onChange="getCapacityForCategory(this.value, this.id);" required <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> style="background: #eee;pointer-events: none;" tabindex=-1>
                                                    <option selected disabled>Select Appliance Category</option>
                                                    <?php foreach ($category[0] as $key => $appliance_category) { ?>
                                                    <option <?php if(isset($unit_details[0]['category'])) { if(strtoupper(str_replace(" ","",$appliance_category['category'])) == strtoupper(str_replace(" ","",$unit_details[0]['category']))) { echo "selected"; } } ?>><?php echo $appliance_category['category']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                            <label for="service_name" class="col-md-4">Capacity *</label>
                                            <div class="col-md-6">
                                                <select type="text" class="form-control appliance_capacity"  <?php if(!empty($appliance_id)) { echo "disabled"; } ?>  id="appliance_capacity_1" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);getModelForServiceCategoryCapacity(this.id);" <?php if($is_repeat && (isset($unit_details[0]['capacity']) && (trim($unit_details[0]['capacity']) !== ''))){ echo 'readonly="readonly"'; } ?> style="background: #eee;pointer-events: none;" tabindex=-1>
                                                    <option  selected disabled>Select Appliance Capacity</option>
                                                    <?php if(!empty($capacity[0])) { foreach ($capacity[0] as $appliance_capacity) { ?>
                                                    <option  <?php if(isset($unit_details[0]['capacity'])) {if(strtoupper(str_replace(" ","",$appliance_capacity['capacity'])) == strtoupper(str_replace(" ","",$unit_details[0]['capacity']))) { echo "selected"; }  } ?>  ><?php echo $appliance_capacity['capacity']; ?></option>
                                                    <?php } } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!--Serial Number & Pic Section -->        
                                        <div class="form-group">
                                            <label for="serial_number" class="col-md-4">Serial Number *</label>
                                            <div class="col-md-6">
                                                <?php
                                                    $serial_number = !empty($unit_details[0]['quantity'][0]['serial_number']) ? $unit_details[0]['quantity'][0]['serial_number'] : "";
                                                    $serial_number_pic = !empty($unit_details[0]['quantity'][0]['serial_number_pic']) ? $unit_details[0]['quantity'][0]['serial_number_pic'] : ""; 
                                                    $pod = !empty($unit_details[0]['quantity'][0]['pod']) ? $unit_details[0]['quantity'][0]['pod'] : ""; 
                                                ?>
                                                <input type="text" style="<?= $str_disabled?>" class="form-control" id="serial_number" name="serial_number"  value="<?php echo $serial_number; ?>"  placeholder = "Enter Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8" tabindex=-1/>
                                                <input type="hidden" class="form-control" id="serial_number_pic" name="serial_number_pic"  value="<?php echo $serial_number_pic; ?>"  />
                                                <input type="hidden" id="pod" class="form-control" name="pod" value="<?php echo $pod; ?>"   />
                                                <input type="hidden" id="sno_required" class="form-control" name="is_sn_file" value="0"   />
                                                <input type="hidden" id="duplicate_sno_required" class="form-control" name="is_dupliacte" value="0"   />
                                                <input type="hidden" id="is_sn_correct" class="form-control" name="is_sn_correct"/> 
                                                <input type="hidden" id="booking_create_date" class="form-control" name="booking_create_date" value="<?php echo $booking_history[0]['create_date']; ?>"/> 
                                                <input type="hidden" id="btn_text" class="form-control" name="btn_text" value="<?php echo $button_caption; ?>"/> 
                                                <input type="file" id="upload_serial_number_pic" class="form-control serialNumberPic" name="upload_serial_number_pic" value="<?php echo $serial_number_pic; ?>" style="margin-top:5px;<?= $str_disabled?>"  tabindex=-1 accept="image/*"/>
                                                <?php
                                                if(!empty($serial_number_pic)) {
                                                    $url="https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/'.SERIAL_NUMBER_PIC_DIR.'/'.$serial_number_pic; ?>
                                                    <p style="margin-top: 5px;"><a href="<?php echo $url; ?>" target="_blank">SF Serial Number Pic</a></p>
                                                     <?php
                                                }
                                                ?>                                                                  
                                            </div>
                                            <div class="col-md-10">
                                                <span style="color:red;" id="error_serial_no"></span>  
                                            </div>
                                            <label for="serial_number" class="col-md-4">AMC File *</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control support_file" id="support_file_1"  name="support_file[]" tabindex=-1 style="margin-top:5px;">
                                                <input type="hidden" class="form-control file_description" id="file_description_1" name="file_description[]" value="<?php  echo ANNUAL_MAINTENANCE_CONTRACT;  ?>">
                                                <?php
                                                if(!empty($amc_file_lists)) {
                                                    foreach ($amc_file_lists as $file){
                                                    $url="https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/purchase-invoices/'.$file; ?>
                                                    <p style="margin-top: 5px;"><a href="<?php echo $url; ?>" target="_blank">Annual Maintenance Contract File</a></p>
                                                   <?php
                                                        }
                                                    }
                                                    ?>
                                                <span style="color:red;" id="error_serial_no"></span>                    
                                            </div>
                                             
                                        </div>
                                        </div>
                                                <input type="hidden"  name="appliance_description[]" id="description_1" placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?>  value="<?php if(isset($unit_details[0]['description'])) { echo $unit_details[0]['description']; } ?>">
                                                 <input type="hidden" <?php if($is_repeat){ echo 'readonly="readonly"'; } ?> class="form-control" name= "order_item_id[]" value="<?php if(isset($unit_details[0]['sub_order_id'])){ echo $unit_details[0]['sub_order_id']; } ?>" placeholder="Enter Order Item Id" id="order_item_id_1"/>                                                
                                                <div class="col-md-6" style="    width: 60%;">
                                        <div class="form-group">
                                            <div  class="col-md-12">
                                                <table class="table priceList table-striped table-bordered" name="priceList" id="priceList_1">
                                                    <tr>
                                                        <th>Service Category</th>
                                                        <th style="display:none;">Customer Charges</th>
                                                        <th>Customer Net Payable</th>
                                                        <th>Selected Services</th>
                                                    </tr>
                                                    <tbody>
                                                        <?php if(!empty($prices)) {?>
                                                        <?php $clone_number = 1; $i=0; $div = 1; $k=0; foreach ( $prices[0] as  $price) {
                                                        // Do not show request type Installation if Spare is requested on Booking
                                                        if(!empty($booking_history['is_spare_requested']) && strpos(str_replace(" ", "", strtoupper($price['service_category'])), "INSTALLATION") !== false)    
                                                        {
                                                            continue;
                                                        }    
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $price['service_category']; ?></td>
                                                                <?php 
                                                            $ct = $price['customer_total'];
                                                              if(isset($unit_details[0]['quantity'])){
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $ct = $tags['customer_total'];
                                                                           }
                                                                        }
                                                                    }
                                                                    ?>
                                                                <input type="hidden" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$unit_details[0]['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_1"; ?>" value = "<?php  if(isset($unit_details[0]['quantity'])){
                                                                    $partner_net_payable = NULL;                                                                    
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $partner_net_payable = $tags['partner_net_payable'];
                                                                              $arr_partner_discount[$price['service_category']] = $partner_net_payable;
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($partner_net_payable)){
                                                                        echo $price['partner_net_payable'];
                                                                    } else {
                                                                        echo $partner_net_payable;
                                                                    }?>" readonly  />
                                                            <td style="display:none;"><?php echo $ct; ?></td>
                                                            <td>
                                                                <?php  if(isset($unit_details[0]['quantity'])){
                                                                        $customer_net_payable = NULL;
                                                                        foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $customer_net_payable = $tags['customer_net_payable'];
                                                                           }
                                                                        }
                                                                        if(is_null($customer_net_payable)){
                                                                            echo $price['customer_net_payable'];
                                                                        } else {
                                                                            echo $customer_net_payable;
                                                                        }
                                                                    }
                                                                    ?>
                                                            </td>
                                                            <input type="hidden" class="form-control discount" name="<?php echo "discount[".$unit_details[0]['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "discount_".$div . "_1"; ?>"
                                                                value = "<?php  if(isset($unit_details[0]['quantity'])){
                                                                    $around_net_payable = NUll;
                                                                       foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $around_net_payable = $tags['around_net_payable'];
                                                                              $arr_247around_discount[$price['service_category']] = $around_net_payable;                                                                                    
                                                                           }
                                                                        }
                                                                    }
                                                                    
                                                                    if(is_null($around_net_payable)){
                                                                        echo intval($price['around_net_payable']);
                                                                    } else {
                                                                        echo $around_net_payable;
                                                                    }?>"
                                                                placeholder='Enter discount' readonly />
                                                            <td>
                                                                <?php
                                                                 $onclick = "onclick='final_price(), get_symptom(), enable_discount(this.id), set_upcountry()'";
                                                                ?>
                                                                <input type='hidden'name ='is_up_val'   data-customer_price = '<?php echo $price['upcountry_customer_price'];?>' data-flat_upcountry = '<?php echo $price['flat_upcountry'];?>' id="<?php echo "is_up_val_".$div."_1" ?>" value="<?php echo $price['is_upcountry']; ?>" />
                                                                <input <?php if ($price['service_category'] == REPEAT_BOOKING_TAG){ echo "class='price_checkbox repeat_".$price['product_or_services']."'"; } else { ?>
                                                                class='price_checkbox <?php echo $price['product_or_services']; } ?>' <?php if(isset($unit_details[0]['quantity'])){
                                                                    foreach ($unit_details[0]['quantity'] as  $tags) {
                                                                        if($is_repeat){
                                                                            if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                echo " checked ";
                                                                            }
                                                                            echo "style= 'pointer-events: none;'";
                                                                        }
                                                                        else{
                                                                            if($price['service_category'] ==  REPAIR_OOW_PARTS_PRICE_TAGS){
                                                                                echo "style= 'pointer-events: none;'";
                                                                            }
                                                                            if(($tags['price_tags'] == $price['service_category'])){
                                                                                echo " checked ";
                                                                                if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                    $tempString = "'".$booking_history[0]['booking_primary_contact_no']."','".$booking_history[0]['service_id']."','".$booking_history[0]['partner_id']."',this.checked,true,'".$booking_history[0]['initial_booking_date']."'";
                                                                                    $onclick = 'onclick="get_parent_booking('.$tempString.'), final_price(), get_symptom(), enable_discount(this.id), set_upcountry()"';
                                                                                }
                                                                                // If partner is billed against a line item do not allow to uncheck this item
                                                                                if(!empty($tags['partner_invoice_id']))
                                                                                {
                                                                                    $onclick = "onclick='return false;' ";
                                                                                }
                                                                            }
                                                                            else{ 
                                                                                if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                   $tempString = "'".$booking_history[0]['booking_primary_contact_no']."','".$booking_history[0]['service_id']."','".$booking_history[0]['partner_id']."',this.checked,false,'".$booking_history[0]['initial_booking_date']."'";
                                                                                   $onclick = 'onclick="get_parent_booking('.$tempString.'), final_price(), get_symptom(), enable_discount(this.id), set_upcountry()"';
                                                                                    // If partner is billed against a line item , Repeat booking category can not be selected
                                                                                    if($is_partner_invoiced){
                                                                                        $onclick = "onclick='return false;' ";
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                     }
                                                                    }
                                                                    
                                                                    ?>
                                                                    type='checkbox' id="<?php echo "checkbox_" . $div . "_1" ; ?>" name='prices[<?php echo $unit_details[0]['brand_id']; ?>][<?php echo $clone_number; ?>][]' <?php if( $price['service_category'] ==REPAIR_OOW_PARTS_PRICE_TAGS){ if($customer_net_payable == 0){ echo "onclick='return false;' ";}}?>  <?php echo $onclick; ?> value = "<?php echo $price['id']. "_" .intval($ct)."_".$div."_1" ?>"  data-price_tag="<?php echo $price['service_category']?>" data-pod="<?php echo $price['pod']; ?>">
                                                            </td>
                                                        </tr>
                                                        <?php  $i++; $div++; if(count($unit_details[0]['quantity']) > $k){  $k++;} }} ?>
                                                    </tbody>
                                                </table>
                                                <input type="hidden" name="arr_partner_discount" id="arr_partner_discount" value='<?php if(isset($arr_partner_discount)) { echo json_encode($arr_partner_discount); } ?>'>
                                                <input type="hidden" name="arr_247around_discount" id="arr_247around_discount" value='<?php if(isset($arr_247around_discount)) { echo json_encode($arr_247around_discount); } ?>'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="cloned">
                        <?php if(!$is_repeat) { ?>
                        <?php if(count($unit_details) > 1) { ?>
                        <?php $number = 1; foreach ($unit_details as $key => $booking_unit_details) { ?>
                        <?php if($number > 1) { $clone_number++; ?>
                        <div class="clonedInput panel panel-info " id="<?php echo "cat_".$number;?>">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6" style="width: 40%;">
                                            <div class="form-group ">
                                                <label for="service_name" class="col-md-4">Brand *</label>
                                                <div class="col-md-6">
                                                    <select type="text" onchange="getCategoryForService(this.id)" class="form-control appliance_brand"    name="appliance_brand[]" id="<?php echo "appliance_brand_".$number;?>" required readonly>
                                                        <option disabled>Select Brand</option>
                                                        <?php foreach ($brand[$key] as  $appliance_brand) { ?>
                                                        <option <?php  if (strcasecmp($appliance_brand['brand_name'], $booking_unit_details['brand']) == 0){ echo "selected";} else { echo "disabled"; } ?> ><?php echo $appliance_brand['brand_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="appliance_id[]" value="<?php echo $booking_unit_details['appliance_id']; ?>"/>                                            
                                            <div class="form-group ">
                                                <label for="type" class="col-md-4">Appliance Model </label>
                                                <div class="col-md-6">
                                                    <input  type="text" class="form-control"  name="model_number[]" id="<?php echo "model_number_".$number;?>" value = "<?php echo $booking_model_number; ?>" placeholder="Enter Model" style="<?= $str_disabled?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="service_name" class="col-md-4">Category *</label>
                                                <div class="col-md-6">
                                                    <select type="text" class="form-control appliance_category"   id="<?php echo "appliance_category_".$number;?>" name="appliance_category[]"  onChange="getCapacityForCategory(this.value,this.id);" required style="background: #eee;pointer-events: none;">
                                                        <option disabled>Select Appliance Category</option>
                                                        <?php if(!empty($category)){ 
                                                        foreach ($category[$key] as  $appliance_category) { ?>
                                                        <option <?php if(strtoupper(str_replace(" ","",$appliance_category['category'])) == strtoupper(str_replace(" ","",$booking_unit_details['category']))) { echo "selected"; } else{ echo "disabled";} ?>><?php echo $appliance_category['category']; ?></option>
                                                        <?php } }?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group <?php if (form_error('appliance_capacity')) { echo 'has-error';} ?>">
                                                <label for="service_name" class="col-md-4">Capacity *</label>
                                                <div class="col-md-6">
                                                    <select type="text" class="form-control appliance_capacity"   id="<?php echo "appliance_capacity_".$number;?>" name="appliance_capacity[]"  onChange="getPricesForCategoryCapacity(this.id);" style="background: #eee;pointer-events: none;">
                                                        <option  disabled>Select Appliance Capacity</option>
                                                        <?php foreach ($capacity[$key] as  $value) {  ?>
                                                        <option <?php if(strtoupper(str_replace(" ","",$value['capacity'])) == strtoupper(str_replace(" ","",$booking_unit_details['capacity']))){ echo "selected";} else{  echo "disabled"; }?> ><?php echo $value['capacity'];?></option>
                                                        <?php   } ?>
                                                    </select>
                                                </div>
                                            </div>
                                    <input  type="hidden" class="form-control"  name="appliance_description[]" id="<?php echo "description".$number;?>"  value = "<?php if(isset($booking_unit_details['description'])) { echo $booking_unit_details['description']; } ?>"  placeholder="Enter Description"  <?php if(!empty($appliance_id)) { echo "readonly"; } ?> >
                                    <input type="hidden" class="form-control" name= "order_item_id[]" placeholder="Enter Order Item Id" value = "<?php if(isset($booking_unit_details['sub_order_id'])) { echo $booking_unit_details['sub_order_id']; } ?>" id="<?php echo "order_item_id_".$number ;?>"/>
                                    <input class="form-control purchase_date" name= "purchase_date[]" type="hidden" value = "<?php if(!empty($booking_model_purchase_date)) { echo $booking_model_purchase_date; } elseif(isset($booking_unit_details['purchase_date'])  && $booking_unit_details['purchase_date'] != '0000-00-00') { echo $booking_unit_details['purchase_date']; } ?>" id="<?php echo "purchase_date_".$number ;?>" max="<?=date('Y-m-d');?>" autocomplete='off' onkeydown="return false" style="<?= $str_disabled?>"/>
                                    </div>
                                    <div class="col-md-6" style="width: 60%;">
                                            <div class="form-group">
                                                <div  class="col-md-12">
                                                    <table class="table priceList table-striped table-bordered" name="priceList" id='<?php echo "priceList_".$number ?>'>
                                                        <tr>
                                                            <th>Service Category</th>
                                                            <th style="display:none;">Customer Charges</th>
                                                            <th>Customer Net Payable</th>
                                                            <th>Selected Services</th>
                                                        </tr>
                                                        <tbody>
                                                            <?php if(!empty($prices)) { ?>
                                                            <?php $i=0; $k=0; foreach ( $prices[0] as  $price) { ?>
                                                            <tr>
                                                                <td><?php echo $price['service_category']; ?></td>
                                                                    <?php 
                                                                $ct = $price['customer_total'];
                                                              if(isset($unit_details[0]['quantity'])){
                                                                   
                                                                       foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                           if($tags['price_tags'] == $price['service_category'] ){
                                                                              $ct = $tags['customer_total'];
                                                                           }
                                                                        }
                                                                    }
                                                                ?>
                                                                    <input type="hidden" class="form-control partner_discount" name="<?php echo "partner_paid_basic_charges[".$booking_unit_details['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "partner_paid_basic_charges_". $div . "_".$number; ?>" value = "<?php  if(isset($booking_unit_details['quantity'])){
                                                                        $partner_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $partner_net_payable = $tags['partner_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($partner_net_payable)){
                                                                            echo $price['partner_net_payable'];
                                                                        } else {
                                                                            echo $partner_net_payable;
                                                                        }?>" readonly />
                                                                <td style="display:none;"><?php echo $ct; ?></td>
                                                                <td>
                                                                    <?php  if(isset($booking_unit_details['quantity'])){
                                                                        $customer_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $customer_net_payable = $tags['customer_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($customer_net_payable)){
                                                                            echo intval($price['customer_net_payable']);
                                                                        } else {
                                                                            echo $customer_net_payable;
                                                                        }?>
                                                                </td><input type="hidden" class="form-control discount" name="<?php echo "discount[".$booking_unit_details['brand_id']."][".$clone_number."][". $price['id']."][]"; ?>" id="<?php echo "discount_". $div . "_".$number; ?>"
                                                                    value = "<?php  if(isset($booking_unit_details['quantity'])){
                                                                        $around_net_payable = NUll;
                                                                           foreach ($booking_unit_details['quantity'] as  $tags) {
                                                                               if($tags['price_tags'] == $price['service_category'] ){
                                                                                  $around_net_payable = $tags['around_net_payable'];
                                                                               }
                                                                            }
                                                                        }
                                                                        
                                                                        if(is_null($around_net_payable)){
                                                                             echo intval($price['around_net_payable']);
                                                                        } else {
                                                                            echo $around_net_payable;
                                                                        }?>"
                                                                    placeholder='Enter discount' readonly />
                                                                <td>
                                                                   
                                                                    <input class='price_checkbox' <?php if(isset($booking_unit_details['quantity'])){
                                                                        foreach ($unit_details[$key]['quantity'] as  $tags) {
                                                                            if($tags['price_tags'] == $price['service_category'] ){
                                                                                echo " checked ";
                                                                                // If partner is billed against a line item do not allow to uncheck this item
                                                                                if(!empty($tags['partner_invoice_id']))
                                                                                {
                                                                                    echo " style='pointer-events:none;' ";
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                if($price['service_category'] ==  REPEAT_BOOKING_TAG){
                                                                                    // If partner is billed against a line item , Repeat booking category can not be selected
                                                                                    if($is_partner_invoiced){
                                                                                        echo " style='pointer-events:none;' ";
                                                                                    }
                                                                                }
                                                                            }
                                                                         }
                                                                        }
                                                                        
                                                                        ?>
                                                                        type='checkbox' id="<?php echo "checkbox_" . $div . "_".$number ; ?>" name='prices[<?php echo $booking_unit_details['brand_id']; ?>][<?php echo $clone_number;?>][]'  onclick='final_price(), enable_discount(this.id), set_upcountry()' value = "<?php echo $price['id']. "_" .intval($ct)."_".$div."_".$number ?>">
                                                                </td>
                                                            </tr>
                                                            <?php  $i++; $div++; if(count($booking_unit_details['quantity']) > $k){ $k++;} }} ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php  } $number++; } } }
                            ?>
                    </div>
                    <div class="row">
                          <input id="booking_date" class="form-control"  name="booking_date" type="hidden" value = "<?php if(!empty($booking_history[0]['booking_date']) && !$is_repeat){ echo  date("Y-m-d", strtotime($booking_history[0]['booking_date'])); } 
                                           else { if(date('H') < '13'){echo  date("Y-m-d");}else{ echo date("Y-m-d", strtotime("+1 day"));} } ?>" required>
                              <input type='hidden' id="booking_address" name="home_address" value = "<?php echo $booking_history[0]['booking_address']; ?>">
                              <input  type="hidden"  name="upcountry_charges" id="upcountry_charges" value="0" placeholder="upcountry_charges" >
                              <input  type="hidden" name="grand_total_price" id="grand_total_price" value="0" placeholder="Total Price">
                              <input  type="hidden" name="booking_timeslot" id="booking_timeslot" value="<?php echo $booking_history[0]['booking_timeslot']; ?>">
                              <input  type="hidden" name="booking_request_symptom" id="booking_request_symptom" value="<?php if(isset($booking_symptom[0]['symptom_id_booking_creation_time'])){echo $booking_symptom[0]['symptom_id_booking_creation_time']; } ?>">
                              <input  type="hidden" name="query_remarks" id="query_remarks" value="<?php if (isset($booking_history[0]['type'])) {
                                        if ($booking_history[0]['type'] == "Booking") {
                                        echo$booking_history[0]['booking_remarks'];
                                        } else {
                                        echo $booking_history[0]['query_remarks'];
                                        }
                                        } ?>">
                              <input  type="hidden" name="repeat_reason" id="repeat_reason" value="<?php echo $booking_history[0]['repeat_reason']; ?>">
                              <input  type="hidden" name="internal_status" id="internal_status" value="<?php echo $booking_history[0]['internal_status']; ?>">
                        <div class="form-group  col-md-12" >
                            <center>
                                
                                <input type="button" id="submitform" onclick="return addBookingDialog('sf_update', 1)" class="btn btn-primary" value="<?= $button_caption?>">
                        </div>
                        </center>
                    </div>
                </form>
                <!-- end Panel Body  -->
            </div>
    </div>
        </div>
</div>
<!-- Repeat Booking Model  -->
<div class="modal fade" id="repeat_booking_model" tabindex="-1" role="dialog" aria-labelledby="repeat_booking_model" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Parent Booking</h5>
        <button type="button" class="close repeat-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" id="repeat_booking_body" style="padding: 3px;   font-size: 13px;">
      </div>
<!--      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>-->
    </div>
  </div>
</div>
<script>

    $("body").on("change", ".support_file", function () {
        var allowedFiles = [".gif", ".jpg",".png",".jpeg",".pdf"];
        var fileUpload = $("#support_file_1");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:()])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.val().toLowerCase())) {
            $("#support_file_1").val('');
            alert("Please upload files having extensions:(" + allowedFiles.join(', ') + ") only.");
            return false;
        }
    });     
   
</script>
<script>
    check_pincode();
    if(($("#model_not_mapped").val() != 1) && ($("#is_spare_requested").val() == ""))
    {
        $(".select-model").select2();
    }
    $(".booking_source").select2();
    //$("#service_id").select2();
    $('#service_id').css('pointer-events','none'); 

     $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, maxDate: '<?php echo date("Y-m-d", strtotime("+15 day")); ?>'});
       getPartnerChannel();
       
    $('.serialNumberPic').on('change', function(){
        $('#'+$(this).attr('id').replace("upload_", "")).val($(this).val());
    });   
</script>

<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex= $(".clonedInput").length +1;
           
    function clone(){
       $(this).parents(".clonedInput").clone()
    .appendTo(".cloned")
           .attr("id","cat" +  cloneIndex)
        .find("*")
           .each(function() {
               var id= this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1]+ (cloneIndex);
            }
    })
           .on('click', 'button.clone', clone)
           .on('click', 'button.remove', remove);
    
           $('#priceList_'+cloneIndex).html("");
           $('#order_item_id_'+cloneIndex).val("");
       cloneIndex++;
       return false;
    }
    function remove(){
       $(this).parents(".clonedInput").remove();
       final_price();
       return false;
    }
    $("button.clone").on("click", clone);
    
    $("button.remove").on("click", remove);
    
    var cloneIndexSample = $(".clonedInputSample").length +1;
    
    function clone1(){
       $(this).parents(".clonedInputSample").clone()
            .appendTo(".cloned1")
            .attr("id", "cat" +  cloneIndexSample)
           .find("*")
           .each(function() {
               var id = this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1] + (cloneIndexSample);
               }
           })
            .on('click', 'button.clone1', clone1)
            .on('click', 'button.remove1', remove1);
          $("#support_file_"+cloneIndexSample).val('');
          $("#a_support_file_"+cloneIndexSample).attr("href","<?php echo base_url() . 'images/no_image.png'; ?>");
          $("#img_support_file_"+cloneIndexSample).attr("src","<?php echo base_url() . 'images/no_image.png'; ?>");
       cloneIndexSample++;
       return false;
    }  
    function remove1(){
        if($('div.clonedInputSample').length > 1) {
            $(this).parents(".clonedInputSample").remove();
        }
       
        return false;
    }
    $("button.clone1").on("click", clone1);
    
    $("button.remove1").on("click", remove1);
    
    $("#btn_addSupportFile").click(function() {
        $('div.clonedInputSample').toggle();
    });
    
     $(document).ready(function () {
        if($('.select-model').css("display") == "none") {
            $('.select-model').next(".select2-container").hide();
        }
        else
        {
            $('.select-model').trigger("change");
        }
        final_price();
        if($('div.uploaded_support_file').length == 1) {
            $("#btn_addSupportFile").click();
        }
    });
    
    // function to cross check request type of booking with warranty status of booking 
    function check_booking_request()
    {
        // Do nothing
        // Booking Request type verification is done along with serial number verification
    }
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

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
    
     $(document).ready(function () {
     <?php
     if($is_repeat){ ?>
        $('#repeat_reason_holder').show();
   <?php  }
     ?>
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
   var postData = {};
    postData['service_id'] = $("#service_id").val();
    postData['brand'] = $('#appliance_brand_1').val();
    postData['category'] = $("#appliance_category_1").val();
     
    postData['partner_type'] =  $("#partner_type").val();
    postData['booking_city'] =  $("#booking_city").val();
    postData['booking_pincode'] =  $("#booking_pincode").val();
    postData['clone_number'] = 1;
    postData['assigned_vendor_id'] = $("#assigned_vendor_id").val();
    postData['capacity'] = $("#appliance_capacity_1").val();
    postData['partner_id'] = $("#partner_id").val();
//    $('#submitform').attr('disabled',true);

    sendAjaxRequest(postData, pricesForCategoryCapacityUrl).done(function(data) {
        var data1 = jQuery.parseJSON(data);
        
        $("#upcountry_data").val(data1.upcountry_data);
        final_price();
        if($("#is_sn_correct").val() != '1')
        {
            $('#submitform').attr('disabled',false);
        }
        set_upcountry();
       
    });
   
});
  $("#purchase_date").datepicker({dateFormat: 'dd-mm-yy', changeYear: true, changeMonth: true});
  $('.purchase_date').each(function () {
    if ($(this).hasClass('hasDatepicker')) {
        $(this).removeClass('hasDatepicker');
    } 
    $(this).datepicker({dateFormat: 'dd-mm-yy', maxDate: 0, changeYear: true, changeMonth: true});
 });
  
  function readonly_select(objs, action) {
    if (action===true)
        objs.prepend('<div class="disabled-select"></div>');
    else
        $(".disabled-select", objs).remove();
}

function upload_supporting_file(supportfileLoader){
    $("#"+supportfileLoader).click();
}

function uploadsupportingfile(key, id){
     var file = $("#supportfileLoader_"+key).val();
     if (file === '') {
        alert('Please select file');
        return;
    } else {
        var formData = new FormData();
        formData.append('support_file', $("#supportfileLoader_"+key)[0].files[0]);
        formData.append('id', id);
        formData.append('booking_id', '<?php echo $booking_history[0]['booking_id'];?>');
        
        $.ajax({
                url: '<?php echo base_url();?>employee/booking/upload_order_supporting_file',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            console.log(percentComplete);
                            $('#myprogress_supproting_file_'+key).text(percentComplete + '%');
                            $('#myprogress_supproting_file_'+key).css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    $('#myprogress_supproting_file_'+key).css('width', '0%');
                    obj = JSON.parse(response);
                    
                    if(obj.code === "success"){
                        $("#a_order_support_file_"+key).attr("href", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                        $("#m_order_support_file_"+key).attr("src", "<?php echo S3_WEBSITE_URL;?>misc-images/" + obj.name);
                    } else {
                        alert(obj.message);
                    }
                }
            });
    }
}
function delete_supporting_file(id){
    var cnfrm = confirm("Are you sure, you want to delete this file ?");
    if(!cnfrm){
        return false;
    }
    
    $.ajax({
        url: '<?php echo base_url();?>employee/booking/delete_order_supporting_file',
        data: {id: id},
        type: 'POST',
        success: function (response) {
            obj = JSON.parse(response);
            alert(obj.message);
            if(obj.status === "success") {
                location.reload();
            }
        }
    });
}
function get_parent_booking(contactNumber,serviceID,partnerID,isChecked,is_already_repeat,initial_booking_date){
        if(isChecked){
            if(!$(".input-model").length)
            {
                var model_number = $(".select-model").val();
            }
            else
            {
                var model_number = $(".input-model").val();
            } 
            var parent_booking_id = $('#parent_id_temp').val();
            if(!is_already_repeat){
              $.ajax({
                      type: 'POST',
                      url: '<?php echo base_url(); ?>employee/booking/get_posible_parent_id',
                      data: {contact: contactNumber, service_id: serviceID,partnerID:partnerID,day_diff:<?php echo _247AROUND_REPEAT_BOOKING_ALLOWED_DAYS; ?>,initial_booking_date:initial_booking_date,model_number:model_number},
                      success: function(response) {
                          obj = JSON.parse(response);
                          if(obj.status  == <?Php echo _NO_REPEAT_BOOKING_FLAG; ?>){
                              $("#repeat_booking_body").html("<p style='padding:10px;'>There is not any Possible Parent booking for this booking, It can not be a repeat booking</p>");
                              $('.repeat_Service:checked').prop('checked', false);
                              $('.repeat_Service').prop('disabled', true);
                              $("#repeat_reason_holder").hide();
                          }
                         else if(obj.status  == <?Php echo _ONE_REPEAT_BOOKING_FLAG; ?>){
                             $('.Service:checked').prop('checked', false);
                             $('.Service').each(function() {
                                $(this).prop('disabled', true);
                             });
                             $("#parent_id").val(obj.html);
                             $("#is_repeat").val("1");
                             $("#repeat_reason_holder").show();
                             $(".cloned :input").attr("disabled", true);
                             $('#repeat_booking_model').hide();
                             alert("Parent Booking : "+obj.html);                             
                          }
                          else if(obj.status  == <?Php echo _MULTIPLE_REPEAT_BOOKING_FLAG; ?>){
                              $('.Service:checked').prop('checked', false);
                                $('.Service').each(function() {
                                    $(this).prop('disabled', true);
                                });
                              $('#repeat_booking_model').modal('show');
                              $("#repeat_booking_body").html(obj.html);
                              $("#repeat_reason_holder").show();
                              $(".cloned :input").attr("disabled", true);
                          }
                      }
                  });
              }
              else{
                $('.Service:checked').prop('checked', false);
                $('.Service').each(function() {
                    $(this).prop('disabled', true);
                });
                $("#parent_id").val($("#parent_id_temp").text());
                $("#is_repeat").val("1");
                $("#repeat_reason_holder").show();
              }
           }
           else{
           $('.Service').each(function() {
               $(this).prop('disabled', false);
           });
            $("#parent_id").val("");
            $("#is_repeat").val("");
            $("#repeat_reason_holder").hide();
            $(".cloned :input").attr("disabled", false);
           }
    }
    function parentBooking(id){
        $("#parent_id").val(id);
        $("#is_repeat").val("1");
        $('#repeat_booking_model').modal('hide');
        if($("#is_sn_correct").val() != '1')
        {
            $("#submitform").attr("disabled", false);
        }
    }
    
    $("#purchase_date_1").datepicker({dateFormat: 'YYYY-MM-DD', maxDate: 0});
    
    <?php if(!empty($str_disabled)) { ?> 
        $(".purchase_date").attr("tabindex",-1);   
        $(".select-model").attr("tabindex",-1);  
    <?php } ?>

    
</script>
<style type="text/css">
    #errmsg1
    {
    color: red;
    }
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>