<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" name="myForm" action="<?php echo base_url() ?>employee/inventory/process_update_booking/<?php echo $bookinghistory[0]['booking_id']; ?>/<?php echo $bookinghistory[0]['id']; ?>" method="POST" onsubmit="return submitForm();" enctype="multipart/form-data">
                    <div class="panel panel-default " style="margin-top:20px" >
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Requested Spare Parts </h2>
                        </div>
                        <div class="panel-body" >
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="booking_id" class="col-md-4">Booking Id *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="booking_id" name="booking_id" value = "<?php if (isset($bookinghistory[0]['booking_id'])) {
                                                    echo $bookinghistory[0]['booking_id'];
                                                    } ?>" placeholder="Booking Id">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Model Number *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="model_number" name="model_number" value = "<?php if (isset($bookinghistory[0]['model_number'])) {
                                                    echo $bookinghistory[0]['model_number'];
                                                    } ?>" placeholder="Model Number">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Requested Parts Name *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="parts_name" name="parts_name" value = "<?php if (isset($bookinghistory[0]['parts_requested'])) {
                                                    echo $bookinghistory[0]['parts_requested'];
                                                    } ?>" placeholder="Parts Name" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Invoice picture" class="col-md-4">Invoice Picture</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control spare_parts" id="invoice_pic" name="invoice_image">
                                            </div>
                                            <div class="col-md-2">
                                                <?php
                                                    $src = base_url() . 'images/no_image.png';
                                                    if (isset($bookinghistory[0]['invoice_pic'])) {
                                                        if (!is_null($bookinghistory[0]['invoice_pic'])) {
                                                    
                                                            if (isset($bookinghistory[0]['invoice_pic']) && !empty($bookinghistory[0]['invoice_pic'])) {
                                                                //Path to be changed
                                                                $src = "https://s3.amazonaws.com/bookings-collateral/misc-images/" . $bookinghistory[0]['invoice_pic'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Invoice picture" class="col-md-4">Defective Part Picture</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control spare_parts" id="defective_parts_pic" name="defective_parts_pic">
                                            </div>
                                            <div class="col-md-2">
                                                <?php
                                                    $src = base_url() . 'images/no_image.png';
                                                    if (isset($bookinghistory[0]['defective_parts_pic'])) {
                                                        if (!is_null($bookinghistory[0]['defective_parts_pic'])) {
                                                    
                                                            if (isset($bookinghistory[0]['defective_parts_pic']) && !empty($bookinghistory[0]['defective_parts_pic'])) {
                                                                //Path to be changed
                                                                $src = "https://s3.amazonaws.com/bookings-collateral/misc-images/" . $bookinghistory[0]['defective_parts_pic'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Serial Number" class="col-md-4">Serial Number *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="serial_number" name="serial_number" value = "<?php if (isset($bookinghistory[0]['serial_number'])) {
                                                    echo $bookinghistory[0]['serial_number'];
                                                    } ?>" placeholder="Serial Number">
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label for="reschdeduled" class="col-md-4">Date of Purchase</label>
                                            <div class="col-md-6">
                                                <div class="input-group input-append date">
                                                    <input id="dop" class="form-control" placeholder="Select Date" name="dop" type="text" required readonly='true' style="background-color:#fff;" value="<?php if (isset($bookinghistory[0]['date_of_purchase'])) {
                                                        echo $bookinghistory[0]['date_of_purchase'];
                                                        } ?>">
                                                    <span class="input-group-addon add-on" onclick="dop_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Invoice picture" class="col-md-4">Serial Number Picture</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control spare_parts" id="serial_number_pic" name="serial_number_pic" >
                                            </div>
                                            <div class="col-md-2">
                                                <?php
                                                    $src = base_url() . 'images/no_image.png';
                                                    if (isset($bookinghistory[0]['serial_number_pic'])) {
                                                        if (!is_null($bookinghistory[0]['serial_number_pic'])) {
                                                    
                                                            if (isset($bookinghistory[0]['serial_number_pic']) && !empty($bookinghistory[0]['serial_number_pic'])) {
                                                                //Path to be changed
                                                                $src = "https://s3.amazonaws.com/bookings-collateral/".SERIAL_NUMBER_PIC_DIR."/" . $bookinghistory[0]['serial_number_pic'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="reason" class="col-md-4">Problem Description* </label>
                                            <div class="col-md-6">
                                                <textarea class="form-control spare_parts"  id="prob_desc" name="reason_text" value = "" rows="5" placeholder="Problem Description" ><?php if (isset($bookinghistory[0]['remarks_by_sc'])) {
                                                    echo $bookinghistory[0]['remarks_by_sc'];
                                                    } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Shipped Spare Parts </h2>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="delivered_parts_name" class="col-md-4">Shipped Parts</label>
                                        <div class="col-md-6">
                                            <textarea type="text" class="form-control" id="shipped_parts_name" name="shipped_parts_name" required  placeholder="Enter Shipped parts"><?php if (isset($bookinghistory[0]['parts_shipped'])) {
                                                echo $bookinghistory[0]['parts_shipped'];
                                                } ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="remarks_by_partner" class="col-md-4">Remarks</label>
                                        <div class="col-md-6">
                                            <textarea type="text" class="form-control" id="remarks" name="remarks_by_partner" placeholder="Please Enter Remarks"  ><?php if (isset($bookinghistory[0]['remarks_by_partner'])) {
                                                echo $bookinghistory[0]['remarks_by_partner'];
                                                } ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group ">
                                        <label for="awb" class="col-md-4">AWB</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="awb" name="awb" value = "<?php if (isset($bookinghistory[0]['awb_by_partner'])) {
                                                echo $bookinghistory[0]['awb_by_partner'];
                                                } ?>" placeholder="Please Enter AWB"  >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    
<!--                                    <div class="form-group ">
                                        <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                         <div class="col-md-6">
                                             <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value = "<?php if (isset($bookinghistory[0]['courier_charges_by_sf'])) {
                                                echo $bookinghistory[0]['courier_charges_by_sf'];
                                                } ?>" placeholder="Please Enter Courier Charges"  required>
                                         </div>  
                                     </div>-->
                                    
                                    <div class="form-group ">
                                        <label for="courier" class="col-md-4">Courier Name</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="courier_name" name="courier_name" placeholder="Please Enter courier Name"  value="<?php if (isset($bookinghistory[0]['courier_name_by_partner'])) {
                                                echo $bookinghistory[0]['courier_name_by_partner'];
                                                } ?>">
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                        <div class="col-md-6">
                                            <div class="input-group input-append date">
                                                <input id="shipment_date" class="form-control"  name="shipment_date" type="date"  value="<?php if (isset($bookinghistory[0]['shipped_date'])) {
                                                    echo $bookinghistory[0]['shipped_date'];
                                                    } ?>" required readonly='true' style="background-color:#fff;">
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Change Spare Booking Status </h2>
                        </div>
                        <div class="panel-body">
                            <label class="radio-inline"><input type="radio"  name="status" <?php if (isset($bookinghistory[0]['status'])) {
                                if ($bookinghistory[0]['status'] == SPARE_PARTS_REQUESTED) {
                                    echo "checked";
                                }
                                } ?> value="<?php echo SPARE_PARTS_REQUESTED;?>">Spare Parts Requested</label>
                            <label class="radio-inline"><input type="radio" name="status" <?php if (isset($bookinghistory[0]['status'])) {
                                if ($bookinghistory[0]['status'] == "Shipped") {
                                    echo "checked";
                                }
                                } ?> value="<?php echo SPARE_SHIPPED_BY_PARTNER;?>">Spare Shipped</label>
                            <label class="radio-inline"><input type="radio" name="status" <?php if (isset($bookinghistory[0]['Delivered'])) {
                                if ($bookinghistory[0]['status'] == "Delivered") {
                                    echo "checked";
                                }
                                } ?> value="Delivered">Spare Delivered</label>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <input type="submit" value="Update Booking" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" class="btn btn-md btn-default" />
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: true, changeYear: true});
    $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $("#reschduled_booking_date").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        maxDate: +7
    });
    
    function booking_calendar() {
    
        $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: true, changeYear: true}).datepicker('show');
    }
    
    function dop_calendar() {
        $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true}).datepicker('show');
    }
    
    function reschduled_booking_date_calendar() {
        $("#reschduled_booking_date").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            maxDate: +7
        }).datepicker('show');
    }
    
    
    
</script>
<style type="text/css">
</style>
