<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container" >
	<div class="panel panel-info" style="margin-top:20px;">
	    <div class="panel-heading">Complete Booking</div>
	    <div class="panel-body">

		<?php
		if (isset($booking_history[0]['current_status'])) {
		    if ($booking_history[0]['current_status'] == "Completed") {
			$status = "1";
		    } else {
			$status = "0";
		    }
		} else {
		    echo $status = "1";
		}
		?>
		<form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url() ?>employee/booking/process_complete_booking/<?php echo $booking_id; ?>/<?php echo $status; ?>"  method="POST" enctype="multipart/form-data">
		    <div class="row">
			<div class="col-md-12">
			    <div class="col-md-6">
				<div class="form-group ">
				    <label for="booking_id" class="col-md-4">Booking ID</label>
				    <div class="col-md-6">
                                       
					<input type="text" class="form-control"  id="booking_id" name="booking_id" value = "<?php
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
					    ?>" selected="selected" disabled="disabled"><?php
						if (isset($booking_history[0]['services'])) {
							    echo $booking_history[0]['services'];
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
    					    <select type="text" class="form-control appliance_brand"  disabled="disabled"   name="appliance_brand[]" id="appliance_brand_1" >
    						<option selected disabled><?php echo $unit_details['brand']; ?></option>
    					    </select>
    					</div>
    				    </div>
    				    <div class="form-group">
    					<div class="col-md-12 ">
    					    <select type="text" class="form-control appliance_category" disabled="disabled"   id="appliance_category_1" name="appliance_category[]"  >
    						<option selected disabled><?php echo $unit_details['category']; ?></option>
    					    </select>
    					</div>
    				    </div>
					<?php if (!empty($unit_details['capacity'])) { ?>
					    <div class="form-group">
						<div class="col-md-12">
						    <select type="text" class="form-control appliance_capacity" disabled="disabled"   id="appliance_capacity_1" name="appliance_capacity[]" >
							<option selected disabled><?php echo $unit_details['capacity']; ?></option>
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
						    ?>
						    <tr style="background-color: white; ">
							<td>
							    <?php if ($price['pod'] == "1") { ?>
	    						    <div class="form-group">
	    							<div class="col-md-12 ">
	    							    <input type="text" class="form-control" id="<?php echo "serial_number" . $count; ?>" name="<?php echo "serial_number[" . $price['unit_id'] . "]" ?>"  value="<?php echo $price['serial_number']; ?>" placeholder = "Enter Serial Number" />
	    							</div>
	    						    </div>
							    <?php } ?>
							</td>
							<td><?php echo $price['price_tags'] ?></td>
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
											    <input type="radio" class="my_checkbox" id="<?php echo "completed_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Completed" <?php
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

													   <input type="radio" class="my_checkbox" id="<?php echo "cancelled_" . $price['pod'] . "_" . $count; ?>" name="<?php echo "booking_status[" . $price['unit_id'] . "]" ?>"  value="Cancelled" <?php
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
	    							    <input type="text" class="form-control"  id="<?php echo "serial_number" . $count; ?>" name="<?php echo "serial_number[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="" placeholder= "Enter Serial Number" />
								    <?php } ?>

									</td>

									<td> <?php echo $value['service_category']; ?> </td>
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
											    <input type="radio" name="<?php echo "booking_status[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="Completed" id="<?php echo "completed_" . $value['pod'] . "_" . $count; ?>" > Completed<br/>

												    <input type="radio" name="<?php echo "booking_status[" . $price['unit_id'] . "new" . $value['id'] . "]" ?>"  value="Cancelled" id="<?php echo "cancelled_" . $value['pod'] . "_" . $count; ?>" > Not Completed
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
				<label for="remark" class="col-md-2">Rating Comment</label>
				<div class="col-md-4">
				    <textarea class="form-control" rows="5" name="rating_comments"><?php echo $booking_history[0]['rating_comments']; ?></textarea>
				</div>
				<label for="remark" class="col-md-2">Admin Remarks</label>
				<div class="col-md-4" >
				    <textarea class="form-control"  rows="5" name="admin_remarks"></textarea>
				</div>
			    </div>
                           <input type="hidden" class="form-control" id="partner_id" name="partner_id" value = "<?php if (isset($booking_history[0]['partner_id'])) {echo $booking_history[0]['partner_id']; } ?>" >
			</div>
		    </div>
		    <br>
		    <div class="form-group  col-md-12" >
			<center>
			    <input type="submit" id="submitform" onclick="return onsubmit_form('<?php echo $booking_history[0]['upcountry_paid_by_customer']; ?>', '<?php echo $k_count; ?>')" class="btn btn-info" value="Complete Booking">
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
    $(".booking_source").select2();
</script>
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

    function onsubmit_form(upcountry_flag, number_of_div) {

    var flag = 0;
    var div_count = 0;
    var is_completed_checkbox = [];
    var serial_number_tmp = [];
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
                    alert("Please Enter Serial Number");
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
            var basic_charge = $("#basic_charge" + div_no[2]).val();
            var additional_charge = $("#extra_charge" + div_no[2]).val();
            var parts_cost = $("#parts_cost" + div_no[2]).val();
            if (Number(amount_due) > 0) {
                var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
                if (Number(total_sf) === 0) {
                    alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                    flag = 1;
                }
            }
        }
    });
    

    if (Number(number_of_div) > Number(div_count)) {
        alert();
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



</script>