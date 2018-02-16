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
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Complete Booking</div>
            <div class="panel-body">
                <form name="myForm" onSubmit="document.getElementById('submitform').disabled=true;" class="form-horizontal" id ="booking_form" action="<?php echo base_url() ?>employee/service_centers/process_complete_booking/<?php echo $booking_id; ?>"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_id" name="booking_id" value = "<?php
                                            if (isset($booking_history[0]['booking_id'])) {
                                                echo $booking_history[0]['booking_id'];
                                            }
                                            ?>" readonly="readonly">
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
                                </div>
                                
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php
                                            if (isset($booking_history[0]['booking_primary_contact_no'])) {
                                            echo $booking_history[0]['booking_primary_contact_no'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="service" id="services"/>
                                        <select type="text" disabled class="form-control"  id="service_id" name="service_id" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['service_id'])) {
                                                echo $booking_history[0]['service_id'];
                                                }
                                                ?>" selected="selected" disabled="disabled"><?php
                                                if (isset($booking_history[0]['services'])) {
                                                    echo $booking_history[0]['services'];
                                                }
                                                ?></option>
                                        </select>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                    <!-- row End  -->
                    <?php $required_sp_id = array(); ?>
                    <?php  $flag = 0; if(isset($booking_history['spare_parts'])){ 
                        foreach ($booking_history['spare_parts'] as  $value) {
                            if($value['status'] == "Completed" || $value['status'] == "Cancelled"){} else {
                                if($value['defective_part_required'] == 1){
                                    if(!empty($value['parts_shipped'])){
                                        $flag = 1; 
                                        array_push($required_sp_id, $value['id']);   
                                    }
                                }
                            }
         
                        }
                        
                    }?>
                    <input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php echo $flag;?>" />
                    <input type="hidden" name="sp_required_id" value='<?php echo json_encode($required_sp_id,TRUE); ?>' />
                    <input type="hidden" name="partner_id" value='<?php echo $booking_history[0]['partner_id']; ?>' />
                    <input type="hidden" name="approval" value='0' />
                    <input type="hidden" name="count_unit"id ="count_unit" value="<?php echo count($bookng_unit_details);?>" />
                    <input type="hidden" name="mismatch_pincode" id="mismatch_pincode" value="<?php if(isset($mismatch_pincode)) { echo $mismatch_pincode; }?>" />
                    <?php $count = 0; foreach ($bookng_unit_details as $key1 => $unit_details) { ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div <?php if($this->session->userdata('is_engineer_app') == 1){?> class="col-md-8" <?php } else { ?> class="col-md-12" <?php } ?> >
                                        <div class="form-group col-md-4" style="<?php if($this->session->userdata('is_engineer_app') == 1){?>width:26.32%;
                                            <?php } else {?> width:26.32%;<?php }?>">
                                            <div class="col-md-12" style="padding-left:0px;">
                                                <label> Product Found Broken</label>
                                                <select type="text" class="form-control appliance_broken" id="<?php echo "broken_".$key1?>" name="broken[]" onchange="check_broken('<?php echo $key1;?>')" >
                                                    <option selected disabled>Product Found Broken</option>
                                                    <option  value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="<?php echo "count_line_item_".$key1;?>" value="<?php echo count($unit_details['quantity']);?>"/>
                                        <div class="form-group col-md-4" style="<?php if($this->session->userdata('is_engineer_app') == 1){?>width:29.32%;
                                            <?php } else {?> width:26.32%;<?php }?>">
                                            <div class="col-md-12 ">
                                                 <label> Brand</label>
                                                <select type="text" disabled="" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" >
                                                    <option selected disabled><?php echo $unit_details['brand']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4" style="width:29.3%">
                                            <div class="col-md-12 ">
                                                <label> Category</label>
                                                <select type="text" disabled="" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  >
                                                    <option selected disabled><?php echo $unit_details['category']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4"style="width:29.2%" style=" padding-right: 0px;">
                                            <div class="col-md-12">
                                                <label> Capacity</label>
                                                <select type="text" disabled="" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]" >
                                                    <?php if (!empty($unit_details['capacity'])) { ?>
                                                    <option selected disabled><?php echo $unit_details['capacity']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding-left:0px;">
                                            <table class="table priceList table-striped table-bordered" name="priceList" >
                                                <tr>
                                                    <th style="width:320px;">Serial Number</th>
                                                    <th>Service Category</th>
                                                    <th>Amount Due</th>
                                                    <th>Customer Basic Charge</th>
                                                    <th>Additional Charge</th>
                                                    <th style="width: 112px;">Parts Cost</th>
                                                    <th style="width:265px;">Status</th>
                                                </tr>
                                                <tbody>
                                                    <?php
                                                        $paid_basic_charges = 0;
                                                        $paid_additional_charges = 0;
                                                        $paid_parts_cost = 0;
                                                        $serial_number = "";
                                                        $serial_number_pic = "";
                                                        foreach ($unit_details['quantity'] as $key => $price) {
                                                            ?>
                                                    <tr>
                                                        <td>
                                                            <?php $sr =FALSE; if(isset($price['en_serial_number'])){ if(!empty($price['en_serial_number'])){ $sr = TRUE; }} ?>
                                                            <?php if ($price['pod'] == "1" || !empty($sr)) { ?>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <input type="hidden" id="<?php echo "serial_number_pic" . $count ?>" class="form-control" name="<?php echo "serial_number_pic[" . $price['unit_id'] . "]" ?>" 
                                                                        value="<?php if(isset($price['en_serial_number_pic'])){ echo $price['en_serial_number_pic'];} else {$price["serial_number_pic"];}  ?>" placeholder=""   />
                                                                    <input type="text" id="<?php echo "serial_number" . $count ?>" class="form-control" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>" 
                                                                        value="<?php if(isset($price['en_serial_number'])){ echo $price['en_serial_number'];} else {$price["serial_number"];}  ?>" placeholder="Enter Serial No" required  />
                                                                    <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td id="<?php echo "price_tags".$count; ?>"><?php echo $price['price_tags'] ?></td>
                                                        <td id="<?php echo "amount_due".$count; ?>"><?php echo $price['customer_net_payable']; ?></td>
                                                        <td>  
                                                            <?php if($price['product_or_services'] != "Product"){  ?>
                                                            <input  id="<?php echo "basic_charge".$count; ?>" type="<?php  if (($price['product_or_services'] == "Service" 
                                                                && $price['customer_net_payable'] == 0) ){ echo "hidden";} ?>" 
                                                                class="form-control cost"  name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "0">
                                                            <?php } ?>
                                                        </td>
                                                        <td>  <input id="<?php echo "extra_charge".$count; ?>"  type="<?php  if ($price['product_or_services'] == "Product") { 
                                                            echo "hidden";} else { echo "text";} ?>" class="form-control cost"  
                                                            name="<?php echo "additional_charge[" . $price['unit_id'] . "]" ?>"  
                                                            value = "0">
                                                        </td>
                                                        <td>  
                                                            <?php if($price['product_or_services'] != "Service"){  ?>
                                                            <input  id="<?php echo "basic_charge".$count; ?>" type="<?php if ($price['product_or_services'] == "Product"
                                                                && $price['customer_net_payable'] > 0){ echo "text"; } 
                                                                else { echo "hidden";}?>" class="form-control cost" 
                                                                name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "0">
                                                            <?php } ?>
                                                            <input id="<?php echo "parts_cost".$count; ?>"  type="<?php if($price['product_or_services'] != "Service"){ 
                                                                if ($price['product_or_services'] == "Product" && $price['customer_net_payable'] == 0) { 
                                                                    echo "text";} else { echo "hidden";} } else { echo "text";}?>" 
                                                                class="form-control cost" 
                                                                name="<?php echo "parts_cost[" . $price['unit_id'] . "]" ?>"  value = "0" >
                                                        </td>
                                                        <input type="hidden" name="<?php echo "appliance_broken[" . $price['unit_id'] . "]" ?>" 
                                                            class="<?php echo "is_broken_".$count;?>" value="" />
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group ">
                                                                        <div class="col-md-12">
                                                                            <div class="radio">
                                                                                <label><input onclick="check_broken('<?php echo $key1;?>')" class="<?php echo "completed_".$key."_".$key1;?>" type="radio"  name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
                                                                                    if ($price['booking_status'] == "Completed") {
                                                                                    echo "checked";
                                                                                    }
                                                                                    ?> id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" required ><?php
                                                                                    if ($price['product_or_services'] == "Product") {
                                                                                    echo " Delivered";
                                                                                    } else {
                                                                                    echo " Completed";
                                                                                    }
                                                                                    ?><br/>
                                                                                <input onclick="check_broken('<?php echo $key1;?>')" class="<?php echo "cancelled_".$key."_".$key1;?>" type="radio" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
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
                                                        if(isset($price['en_serial_number'])){ if(!empty($price['en_serial_number'])){ 
                                                            $serial_number = $price['en_serial_number'];
                                                            $serial_number_pic = $price['en_serial_number_pic'];
                                                        }}
                                                         $count++;
                                                          
                                                         }
                                                         ?>
                                                </tbody>
                                            </table>
                                            <span class="error_msg" style="color: red"></span>
                                        </div>
                                    </div>
                                    <?php  if($this->session->userdata('is_engineer_app') == 1){?>
                                    <div class="col-md-4">
                                        <?php if(!empty($serial_number)){ ?>
                                        <div class="col-md-12">
                                            <div class="col-md-12 page-header" style="margin: 0px 0 0px;">
                                                Serial Number:  <b><?php echo $serial_number;?></b>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6" style="padding-left:0px; margin-top: 10px; padding-left: 15px;">
                                            <?php if(!empty($serial_number_pic)){ ?>
                                            <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $serial_number_pic;?>">   
                                            <img style="height:150px; width:150px; " src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $serial_number_pic;?>" />
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6" style="padding-left:0px; margin-top: 10px; padding-left: 15px;">
                                            <?php if(!empty($signature)){ ?><a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>">   
                                            <img style="height:150px;width:150px;  <?php if(!empty($signature)){ ?>border: 1px solid;<?php } ?>" src="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $signature;?>" /></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row" style=" margin-left:-29px;">
                        <div class ="col-md-12">
                            <?php if($booking_history[0]['is_upcountry'] == '1' 
                                && $booking_history[0]['upcountry_paid_by_customer']== '1' ){ ?>
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="type" class="col-md-12">Upcountry Charges Paid By Customer</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <input  type="hidden" class="form-control cost" name="upcountry_charges" id="upcountry_charges" value="<?php echo "0";?>" placeholder="Enter Upcountry Charges Paid By Customer">
                            <?php } ?>
                            <div class="form-group col-md-6">
                                <label for="type" class="col-md-12">Total Customer Paid</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-addon">Rs.</div>
                                        <input  type="text" class="form-control"  name="grand_total_price" id="grand_total_price" value="<?php echo $paid_basic_charges + $paid_additional_charges + $paid_parts_cost; ?>" placeholder="Total Price" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:25px;">
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-6" style=" margin-left:-29px;">
                                <label for="remark" class="col-md-12">Booking Remarks</label>
                                <div class="col-md-12" >
                                    <textarea class="form-control"  rows="2" name="booking_remarks" readonly><?php
                                        if (isset($booking_history[0]['booking_remarks'])) {
                                            echo str_replace("<br/>", "&#13;&#10;", $booking_history[0]['booking_remarks']);
                                        }
                                        ?></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-12">Closing Remarks</label>
                                <div class="col-md-12" >
                                    <textarea class="form-control"  rows="2" name="closing_remarks" id="closing_remarks" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  col-md-12" >
                        <center style="margin-top:60px;">
                            <input type="submit" id="submitform"  onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>', '<?php echo $count; ?>', '<?php echo count($bookng_unit_details)?>')" class="btn btn-lg" style="background-color: #2C9D9A;
                                border-color: #2C9D9A; color:#fff;" value="Complete Booking">
                    </div>
                    </center>
            </div>
            </form>
            <!-- end Panel Body  -->
        </div>
    </div>
</div>
</div>
<script>
    $("#service_id").select2();
    $("#booking_city").select2();
    
    
    $(document).ready(function() {
        //called when key is pressed in textbox
        $(".cost").keypress(function(e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                $(".error_msg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });
    
    });
    
    
    $(document).on('keyup', '.cost', function(e) {
    
        var price = 0;
        $("input.cost").each(function() {
            price += Number($(this).val());
    
        });
    
        $("#grand_total_price").val(price);
    });
    
    
    function onsubmit_form(upcountry_flag, number_of_div, appliance_count) {
    
        var flag = 0;
        var div_count = 0;
        var is_completed_checkbox = [];
        var serial_number_tmp = [];
       
        for(m= 0; m< Number(appliance_count); m++){
            var isbroken = $("#broken_"+ m).val();
          
            if(isbroken === null){
                alert("Please Select Is Broken DropDown");
                document.getElementById("broken_"+ m).style.borderColor = "red";
                flag = 1;
                return false;
            }
        }
        $(':radio:checked').each(function(i) {
            div_count = div_count + 1;
    
            //console.log($(this).val());
            var div_no = this.id.split('_');
            is_completed_checkbox[i] = div_no[0];
            if (div_no[0] === "completed") {
                //if POD is also 1, only then check for serial number.
                if (div_no[1] === "1") {
                    var serial_number = $("#serial_number" + div_no[2]).val();
                    serial_number_tmp.push(serial_number);
                    if (serial_number === "") {
    
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        flag = 1;
                        
                    }
    
                    if (serial_number === "0") {
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        flag = 1;
                        
                    }
    
                    var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
                    if (numberRegex.test(serial_number)) {
                        if (serial_number > 0) {
                            flag = 0;
                        } else {
                            
                            document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                            flag = 1;
                            
                        }
                    }
                }
               
                var amount_due = $("#amount_due" + div_no[2]).text();
                var price_tags = $("#price_tags" + div_no[2]).text();
                var basic_charge = $("#basic_charge" + div_no[2]).val();
                var additional_charge = $("#extra_charge" + div_no[2]).val();
                var parts_cost = $("#parts_cost" + div_no[2]).val();
                if (Number(amount_due) > 0) {
                   
                    var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
                    if (Number(total_sf) === 0) {
                       
                        alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                        flag = 1;
                        
                    }
                    
                    if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                       
                        if(Number(basic_charge) < Number(amount_due)){
                           alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                           flag = 1;
                           }
                             
                    }
                     
                }
                  
            } else if(div_no[0] === "cancelled"){
                var price_tags = $("#price_tags" + div_no[2]).text();
                var amount_due = $("#amount_due" + div_no[2]).text();
                if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                    alert("You can not mark as a not delivered of Spare Parts. fill amount collected from customer, Amount Due: Rs." + amount_due);
                    flag = 1;
                    return false;
                }
            }
        });
      
        if (Number(number_of_div) !== Number(div_count)) {
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
        if (flag === 0) {
            $('#submitform').val("Please wait.....");
            return true;
    
        } else if (flag === 1) {
    
            return false;
        }
    }
</script>
<style type="text/css">
    .panel-info>.panel-heading {
    color: #fff;
    background-color: #2C9D9A;
    border-color: #2C9D9A;
    }
    .panel-info {
    border-color: #bce8f1;
    }
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 42px 0 0px 0px;
    padding: 0;
    text-align: left;
    width: 240px;
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
    serial_number: "required",
    closing_remarks: "required"
       },
       messages: {
    booking_status: "Please select on of these button ",
    serial_number: " Please Enter Serial Number",
    closing_remarks: "Please Enter Remarks"
    
       },
       submitHandler: function (form) {
    form.submit();
       }
    });
      }
    };
    
    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
     JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    function check_broken(div){
        
        var broken = Number($("#broken_"+ div).val());
      
        var div_item_count = $("#count_line_item_"+div).val();
        var count_unit = $("#count_unit").val();
        var no  = 0;
        
           for(k =0; k< Number(count_unit); k++){
            for(i = 0; i < Number(div_item_count); i++ ){

                if(broken === 1){
                    $(".is_broken_"+no).val("1");
                } else {

                     $(".is_broken_"+no).val("0");
                }

               var amount_due = Number($("#amount_due" + no).text());
               var price_tags = $("#price_tags" +no ).text();
               if(amount_due === 0 && price_tags.indexOf("Wall Mount Stand") > -1 && broken === 1){

                    $(".cancelled_"+i+"_"+div).prop('checked', true);
               }
                   
               no++;
            }
        }
    }
    
</script>