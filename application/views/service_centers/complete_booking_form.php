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
                <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url() ?>employee/service_centers/process_complete_booking/<?php echo $booking_id; ?>"  method="POST" enctype="multipart/form-data">
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
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="booking_city" name="city" >
                                            <option value="<?php
                                                if (isset($booking_history[0]['city'])) {
                                                echo $booking_history[0]['city'];
                                                }
                                                ?>" selected="selected" disabled="disabled"><?php
                                                if (isset($booking_history[0]['city'])) {
                                                    echo $booking_history[0]['city'];
                                                }
                                                ?></option>
                                        </select>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Order ID </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="order_id" name="order_id" value = "<?php
                                            if (isset($booking_history[0]['order_id'])) {
                                                echo $booking_history[0]['order_id'];
                                            }
                                            ?>" readonly="readonly">
                                    </div>
                                </div>
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
                                        <select type="text" class="form-control"  id="service_id" name="service_id" >
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
                    <input type="hidden" id="spare_parts_required" name="spare_parts_required" value="<?php  $flag = 0;if($is_spare_required ==1){ if(isset($booking_history['spare_parts'])){ 
                                                                                                                                                                                 
                        foreach ($booking_history['spare_parts'] as  $value) {
                            if($value['status'] == "Completed" || $value['status'] == "Cancelled"){} else {
                               $flag = 1; 
                            }
         
                        }
                        echo $flag;
                        
                    } else { echo $flag;
                        
                    } } else { echo $flag; }?>" />
                    <?php $count = 0; foreach ($bookng_unit_details as $key => $unit_details) { ?>
                    <div class="clonedInput panel panel-info " id="clonedInput1">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <div class="col-md-12 ">
                                            <select type="text" class="form-control appliance_brand"    name="appliance_brand[]" id="appliance_brand_1" >
                                                <option selected disabled><?php echo $unit_details['brand']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 ">
                                            <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category[]"  >
                                                <option selected disabled><?php echo $unit_details['category']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (!empty($unit_details['capacity'])) { ?>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity[]" >
                                                <option selected disabled><?php echo $unit_details['capacity']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-9">
                                    <table class="table priceList table-striped table-bordered" name="priceList" >
                                        <tr>
                                            <th style="width:375px;">Enter Serial Number</th>
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
                                                foreach ($unit_details['quantity'] as $key => $price) {
                                                    ?>
                                            <tr>
                                                <td>
                                                    <?php if ($price['pod'] == "1") { ?>
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <input type="text" id="<?php echo "serial_number" . $count ?>" class="form-control" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['serial_number']; ?>" placeholder="Enter Serial No" required  />
                                                            <input type="hidden" id="<?php echo "pod" . $count ?>" class="form-control" name="<?php echo "pod[" . $price['unit_id'] . "]" ?>" value="<?php echo $price['pod']; ?>"   />
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $price['price_tags'] ?></td>
                                                <td id="<?php echo "amount_due".$count; ?>"><?php echo $price['customer_net_payable']; ?></td>
                                                <td>  
                                                    <?php  if ($price['product_or_services'] == "Service"){ ?>
                                                    
                                                    <input  id="<?php echo "basic_charge".$count; ?>" type="text" class="form-control cost"  name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "<?php
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
                                                     <?php  if ($price['product_or_services'] == "Product"){ ?>
                                                    
                                                    <input  id="<?php echo "basic_charge".$count; ?>" type="text" class="form-control cost"  name="<?php echo "customer_basic_charge[" . $price['unit_id'] . "]" ?>"  value = "<?php
                                                    $paid_basic_charges += $price['customer_paid_basic_charges'];
                                                    if (!empty($price['customer_paid_basic_charges'])) {
                                                    echo $price['customer_paid_basic_charges'];
                                                    } else {
                                                    echo "0";
                                                    }
                                                    ?>">
                                                    <?php } ?>
                                                    <input id="<?php echo "parts_cost".$count; ?>"  type="<?php  if ($price['product_or_services'] == "Product") { echo "hidden";} else { echo "text";}?>" class="form-control cost"  name="<?php echo "parts_cost[" . $price['unit_id'] . "]" ?>"  value = "<?php
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
                                                                        <label><input type="radio"  name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
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
                                                                        <input type="radio" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
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
                                    <textarea class="form-control"  rows="5" name="booking_remarks" readonly><?php
                                        if (isset($booking_history[0]['booking_remarks'])) {
                                            echo str_replace("<br/>", "&#13;&#10;", $booking_history[0]['booking_remarks']);
                                        }
                                        ?></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="remark" class="col-md-12">Closing Remarks</label>
                                <div class="col-md-12" >
                                    <textarea class="form-control"  rows="5" name="closing_remarks" id="closing_remarks" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  col-md-12" >
                        <center style="margin-top:60px;">
                            <input type="submit" id="submitform"  onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>')" class="btn btn-lg" style="background-color: #2C9D9A;
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
    
    
    function onsubmit_form(upcountry_flag) {
    var flag = 0;
    var is_completed_checkbox = [];
    $(':radio:checked').each(function (i) {
     //console.log($(this).val());
     var div_no = this.id.split('_');
     is_completed_checkbox[i] = div_no[0];
     if (div_no[0] === "completed") {
    //if POD is also 1, only then check for serial number.
    if (div_no[1] === "1") {
      var serial_number = $("#serial_number" + div_no[2]).val();
      if (serial_number === "") {
    
    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
    flag = 1;
      }
    
      if (serial_number === "0" ) {
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
                
    var amount_due = $("#amount_due"+div_no[2]).text();
    var basic_charge = $("#basic_charge"+div_no[2]).val();
    var additional_charge = $("#extra_charge"+div_no[2]).val();
    var parts_cost =$("#parts_cost"+div_no[2]).val();
    if(Number(amount_due) > 0){
        var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
        if(Number(total_sf) === 0){
            alert("Please fill amount collected from customer, Amount Due: Rs."+ amount_due );
            flag = 1;
        }
    }
     }
    });
    
    if ($.inArray('completed', is_completed_checkbox) !== -1)
    {
    
    } else {
     alert('Please Select atleast one Completed or Delivered checkbox.');
     $flag = 0;
     return false;
    
    }
    var is_sp_required = $("#spare_parts_required").val();   
   
    if(Number(is_sp_required) === 1){
         alert("Ship Defective Spare Parts");
    }
         
    if(Number(upcountry_flag) === 1) {
        var upcountry_charges = $("#upcountry_charges").val();
        if(Number(upcountry_charges) === 0 ){
            flag =1;
            document.getElementById('upcountry_charges').style.borderColor = "red";
            alert("Please Enter Upcountry Charges which Paid by Customer");
            return false;
        } else if(Number(upcountry_charges)> 0 ){
            flag =0;
            document.getElementById('upcountry_charges').style.borderColor = "green";
        }
    }   
    var closing_remarks =$("#closing_remarks").val();        
    if(closing_remarks === ""){
        alert("Please Enter Remarks");
        document.getElementById('closing_remarks').style.borderColor = "red";
        flag= 0;
        return false;
    }
    if (flag === 0) {
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
    
    
    
</script>