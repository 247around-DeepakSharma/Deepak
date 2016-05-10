
<script>
    function getCapacityForCategory(service_id, category)
    {
        $("#priceList").html("<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>");
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/getCapacityForCategory/' + service_id + "/" + category,
            success: function (data) {
                $("#appliance_capacity").html(data);
                if (data != "<option></option>")
                {
                    var capacity = $("#appliance_capacity").val();
                    getPricesForCategoryCapacity();
                }
                else
                {
                    $("#appliance_capacity").html(data);
                    var capacity = "NULL";
                    getPricesForCategoryCapacity();
                }
            }
        });
    }

    function getPricesForCategoryCapacity()
    {
        var service_id = $("#service_id").val();
        var category = $("#appliance_category").val();
        if ($("#appliance_capacity").val() != "")
        {
            var capacity = $("#appliance_capacity").val();
        }
        else
        {
            var capacity = "NULL";
        }
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/getPricesForCategoryCapacity/' + service_id + "/" + category + "/" + capacity,
            success: function (data) {
                $("#priceList").html(data);
            }
        });

    }

    function service()
    {
        var service_name = '';
        $('#priceList .Checkbox1:checked').each(function () {
            service_name += ($(this).attr('name')).toString() + ',';
        });

        $('#items_selected').val(service_name);
        //alert(service_name);
    }
</script>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                New Booking
                </h1>
                <form name="myForm" class="form-horizontal" id ="booking_form" onsubmit="service()"
                      action="<?php echo base_url() ?>employee/bookings_excel/get_confirm_sd_lead_form" method="POST">

                    <div class="form-group <?php if (form_error('name')) { echo 'has-error'; } ?>">
                        <label for="name" class="col-md-2">User Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="name" value = "<?php echo $user['name']; ?>"
                                   readonly="readonly">
                            <?php echo form_error('name'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if (form_error('booking_primary_contact_no')) { echo 'has-error'; } ?>">
                        <label for="booking_primary_contact_no" class="col-md-2">Primary Contact Number</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="booking_primary_contact_no" value = "<?php echo $user['phone_number']; ?>">
                            <?php echo form_error('booking_primary_contact_no'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php if (form_error('booking_alternate_contact_no')) { echo 'has-error';} ?>">
                        <label for="booking_alternate_contact_no" class="col-md-2">Alternate Contact No</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="booking_alternate_contact_no" value = "<?php echo set_value('booking_alternate_contact_no'); ?>">
                        <?php echo form_error('booking_alternate_contact_no'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('product')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="services" class="col-md-2">Product Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="product" value = "<?php echo $lead['Product_Type']; ?>" readonly="readonly">
                            <?php echo form_error('product'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('services')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="services" class="col-md-2">Appliance</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="services" value = "<?php echo $lead['Product']; ?>" readonly="readonly">
                            <?php echo form_error('services'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('brand')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="brand" class="col-md-2">Brand</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="appliance_brand" value = "<?php echo $lead['Brand']; ?>" readonly="readonly">
                                   <?php echo form_error('brand'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('model_number')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="brand" class="col-md-2">Model</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="model_number" value = "<?php echo $lead['Model']; ?>" readonly="readonly">
                                   <?php echo form_error('model_number'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_category')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="appliance_category" class="col-md-2">Category</label>
                        <div class="col-md-6">
                            <select style="width:300px;" type="text" class="form-control" id="appliance_category" name="appliance_category" value = "" onChange="getCapacityForCategory(<?php echo $service_id; ?>, this.value);" required>
                                        <?php foreach ($category as $key => $value) { ?>
                                    <option>
                                        <?php echo $value['category']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_capacity')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="appliance_capacity" class="col-md-2">Capacity</label>
                        <div class="col-md-6">
                            <select style="width:300px;" type="text" class="form-control" id="appliance_capacity"  name="appliance_capacity" value = "" onChange="getPricesForCategoryCapacity();">
                                <option>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="select_service" class="col-md-2">Select Service</label>
                        <div style="width:300px;" class="col-md-1">

                            <table class="table table-striped table-bordered" name="priceList" id="priceList">
                                <tr><th>Service Category</th>
                                    <th>Total Charges</th>
                                    <th>Selected Services</th>
                                </tr>

                            </table>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('total_price')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label for="total_price" class="col-md-2">Total Price</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="total_price" value = ""
                                   placeholder="Enter Total Price" required>
                                   <?php echo form_error('total_price'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('items_selected')) {
                        echo 'has-error';
                    }
                    ?>">
                        <div style="width:150px;" class="col-md-6">
                            <input style="width:150px;" type="hidden" class="form-control"  name="items_selected" id="items_selected" value = "<?php echo set_value('items_selected'); ?>">
                            <?php echo form_error('items_selected'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_date')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="booking_date" for="booking_date" class="col-md-2">Booking Date</label>

                        <div class="col-md-6">
                            <input type="date" class="form-control"  id="booking_date" name="booking_date" value = "<?php echo set_value('booking_date'); ?>" required>
                            <?php echo form_error('booking_date'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_timeslot')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="booking_timeslot1" for="booking_timeslot" class="col-md-2">Booking Time</label>
                        <div class="col-md-6">
                            <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>">

                                <option>10AM-1PM</option>
                                <option>1PM-4PM</option>
                                <option>4PM-7PM</option>
                            </select>
                            <?php echo form_error('booking_timeslot'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_address')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="booking_address" for="booking_address" class="col-md-2">Booking Address</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_address" value = "<?php echo $user['home_address']; ?>">
                            <?php echo form_error('booking_address'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_pincode')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="booking_pincode" for="booking_pincode" class="col-md-2">Booking Pincode</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_pincode" value = "<?php echo $lead['Pincode']; ?>" required>
                            <?php echo form_error('booking_pincode'); ?>
                        </div>
                    </div>

                    <div>
                        <div class="form-group <?php
                        if (form_error('booking_remarks')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label style="width:200px;" for="booking_remarks" class="col-md-2">Booking Remark:</label>
                            <div class="col-md-2">
                                <textarea style="height:120px;width:600px;" type="text" class="form-control"  name="booking_remarks" value = ""></textarea>
                                <?php echo form_error('booking_remarks'); ?>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" class="form-control"  name="lead_id" value = "<?php echo $lead['id']; ?>" >
                    <input type="hidden" class="form-control"  name="user_id" value = "<?php echo $user['user_id']; ?>" >
                    <input type="hidden" class="form-control"  id="service_id" name="service_id" value = "<?php echo $service_id; ?>">
                   
                    <div>
                        <center>
                            <input type="submit" value="Save Booking" class="btn btn-danger">
<?php echo "<a id='edit' class='btn btn-small btn-primary' href=" . base_url() . "employee/bookings_excel/get_unassigned_bookings>Cancel</a>"; ?>
                        </center>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>