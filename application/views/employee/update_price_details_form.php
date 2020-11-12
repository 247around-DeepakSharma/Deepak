<style>
.alert-dismissable .close, .alert-dismissible .close {
    position: relative;
    top: -10px;
    right: -21px;
}
</style>
<div id="page-wrapper" >
     <div class="container col-md-12" >
 <?php if(validation_errors()){?>
        <div class="panel panel-danger" >
            <div class="panel-heading" >
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
         <?php if($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('success') . '</strong>
            </div>';
            }
            ?>
     </div>
    <div class="container col-md-4" >
        <div class="panel panel-info" >
            <div class="panel-heading" >
                Search Booking: 
                <form name="myForm" id="myForm" class="form-horizontal" action="<?php echo base_url(); ?>employee/inventory/update_part_price_details"  method="POST" >
                    <input type="text" class="form-control" name="booking_id" value="<?php
                    if (!empty($data[0])) {
                        echo $data[0]['booking_id'];
                    }
                    ?>" id="booking_id" />
                </form>
            </div>
        </div>
    </div>
<?php if (isset($data) & !empty($data)) { ?>
        <div class="container col-md-12" >
            <div class="panel panel-info" >
                <div class="panel-heading" >Update Price</div>
                <form action="<?php echo base_url(); ?>employee/inventory/process_update_parts_details" class="form-horizontal" method="POST">

                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Part Name </label>
                                    <input type="text"  class="form-control" id="part_name" name="part_name" value = "<?php if(!empty($zopper)){ echo $zopper[0]['part_name']; }?>" placeholder="Enter Part Name" required="">
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Part Estimate Given (Without GST) <span class="text-danger">*</span> </label>
                                    <input type="number" step="0.01" class="form-control total_charges part_estimate" id="part_charge" name="part_estimate_given" value = "<?php if(!empty($zopper)){ echo $zopper[0]['part_estimate_given']; }?>" placeholder="Enter Parts Charge" required>
                                    <input type="hidden" class="form-control" name="booking_id" value="<?php
                                    if (!empty($data[0])) {
                                        echo $data[0]['booking_id'];
                                    }
                                    ?>" id="booking_id" />
                                    <input type="hidden" name="assigned_vendor_id" value="<?php if(!empty($data[0])){ echo $data[0]['assigned_vendor_id']; }?>" />
                                    <input type="hidden" name="partner_id" value="<?php if(!empty($data[0])){ echo $data[0]['partner_id']; }?>" />
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Around Part Commission (%)</label>
                                    <input type="number" step="0.01" class="form-control total_charges around_part_commission" id="around_part_commission" name="around_part_commission" value = "<?php if(!empty($zopper)){ echo $zopper[0]['around_part_commission']; } else { echo '30';} ?>" placeholder="Enter Around Commission" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Total Parts Charges  </label>
                                    <input type="number" step="0.01" class="form-control charges" id="total_parts_charges" name="total_parts_charges" value = "" placeholder="Total Parts Charges" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                             <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Service Charges (Without GST)<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control total_charges service_charge" id="service_charge" name="service_charge" value = "<?php if(!empty($zopper)){ echo $zopper[0]['service_charge']; }?>" placeholder="Enter Service Charge" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Around Service Commission (%)</label>
                                    <input type="number" step="0.01" class="form-control total_charges around_service_commission" id="around_service_commission" name="around_service_commission" value = "<?php if(!empty($zopper)){ echo $zopper[0]['around_service_commission']; } else { echo '0'; } ?>" placeholder="Enter Service Commission" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Total Service Charges  </label>
                                    <input type="number" step="0.01" class="form-control charges" id="total_service_charges" name="total_service_charges" value = "" placeholder="Total Service Charges" readonly="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Transport Charges (Without GST)<span class="text-danger">*</span> </label>
                                    <input type="number" step="0.01" class="form-control total_charges transport_charge" id="transport_charge" name="transport_charge" value = "<?php if(!empty($zopper)){ echo $zopper[0]['transport_charge']; }?>" placeholder="Enter Transport Charge" required>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Around Transport Commission (%)</label>
                                    <input type="number" step="0.01" class="form-control total_charges around_transport_commission" id="around_transport_commission" name="around_transport_commission" value = "<?php if(!empty($zopper)){ echo $zopper[0]['around_transport_commission']; } else { echo '0'; } ?>" placeholder="Enter Transport Commission" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Total Transport Charges </label>
                                    <input type="number" step="0.01" class="form-control total_charges charges" id="total_transport_charges" name="total_transport_charges" value = "" placeholder="Total Transport Charges" readonly="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Courier Charges (Without GST)<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control total_charges courier_charge" id="courier_charge" name="courier_charge" value = "<?php if(!empty($zopper)){ echo $zopper[0]['courier_charge']; }?>" placeholder="Enter Courier Charge" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Around Courier Commission (%)</label>
                                    <input type="number" step="0.01" class="form-control total_charges around_courier_commission" id="around_courier_commission" name="around_courier_commission" value = "<?php if(!empty($zopper)){ echo $zopper[0]['around_courier_commission']; } else { echo '0'; } ?>" placeholder="Enter Courier Commission" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Total Courier Charges  </label>
                                    <input type="number" step="0.01" class="form-control charges" id="total_courier_charges" name="total_courier_charges" value = "" placeholder="Total Courier Charges" readonly="">
                                </div>
                            </div>
                        </div>                 
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="remarks">Remarks </label>
                                    <textarea class="form-control" placeholder="Enter Remarks" name="remarks" required><?php if(!empty($zopper)){ echo $zopper[0]['remarks']; }?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="remarks">Print Remarks On Invoice </label>
                                    <textarea class="form-control" placeholder="Enter Invoice Remarks" name="estimate_remarks" required><?php if(!empty($zopper)){ echo $zopper[0]['estimate_remarks']; }?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Total Charges </label>
                                    <input type="number" step="0.01" class="form-control" id="total_charges" name="total_charges" value = "<?php if(!empty($zopper)){ echo round(($zopper[0]['part_estimate_given'] +  
                                            $zopper[0]['courier_charge'] + $zopper[0]['transport_charge'] +$zopper[0]['service_charge'] + $zopper[0]['around_part_commission'])*1.18,0) ; }?>" placeholder="Enter Parts Charge" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="remarks">Send Estimate to Partner </label>
                                    <input type="checkbox" class="form-control" id="send_estimate" name="estimate_sent" value = "1" >
                                </div>
                            </div>
                        </div>
                        <br/>  <hr/>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Part Will Arrange By </label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" name="arrange_part_by" value="<?php echo PART_ARRANGE_BY_SAME_VENDOR; ?>" <?php if(!empty($zopper)){ if($zopper[0]['arrange_part_by'] ==PART_ARRANGE_BY_SAME_VENDOR){ echo "checked"; } }?> required>Same Vendor
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="arrange_part_by" value="<?php echo PART_ARRANGE_BY_DIFF_VENDOR; ?>" <?php if(!empty($zopper)){ if($zopper[0]['arrange_part_by'] ==PART_ARRANGE_BY_DIFF_VENDOR){ echo "checked"; } }?> required>Different Vendor
                                    </label>
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Select Vendor or Partner </label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" name="entity" onclick="partner_vendor('vendor')" <?php if(!empty($zopper)){ if($zopper[0]['entity'] == "vendor"){ echo "checked"; } }?> value="vendor">Vendor
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="entity" onclick="partner_vendor('partner')" <?php if(!empty($zopper)){ if($zopper[0]['entity'] == "partner"){ echo "checked"; } }?>  value="partner">Partner
                                    </label>
                                    
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group col-md-12 ">
                                    <label for="city ">Vendor or Partner Name</label>
                                    
                                    <select  class="form-control"  id="entity_id" name="entity_id" required>
                                        <option selected="selected" disabled="disabled">Select Entity</option>
                                       
                                    </select>
                                   
                                </div>
                            </div>    
                    </div>
                        <div class="col-md-12">
                        <div class="col-md-offset-4">
                            <input type="submit" onclick="return check_validation()" class="btn btn-md btn-info"/>
                        </div>
                    </div>
                        
                    </div>
                    
                </form>
            </div>
        </div>
    
<?php } else { 
        if($error){
    ?>
    <div class="container col-md-12">
        <span style="font-size: 18px; color: #f01b1b;margin-left: 15PX;"> <?php echo $error_message; ?></span>
    </div>
<?php } } ?>
</div>
<script>
$("#entity").select2();
<?php if(!empty($zopper)){ ?>partner_vendor('<?php if(!empty($zopper)){echo $zopper[0]['entity'];}?>'); <?php }?>
function check_validation() {
    var part_charges = Number($("#part_charge").val());

    if (part_charges > 0) {
        var part_arrange_by = $('input[name="arrange_part_by"]:checked').val();
        if (part_arrange_by === null || part_arrange_by === undefined) {
            alert("Please Select Part Arrange By");
            return false;
        } else {
            if(Number(part_arrange_by) === 1){
                <?php //if(!isset($data['spare_parts'])){ ?> 
                  // alert("Spare Part has not requested By SF. Please Request Part");
                  // return false;
                <?php //}?>
                var entity = $('input[name="entity"]:checked').val();
                if (entity === null || entity === undefined) {
                    alert("Please Select Vendor or Partner");
                    return false;
                } else {
                    var entity_id = $("#entity_id").val();
                    if (entity_id === null) {
                        alert("Please Select Entity Name");
                        return false;
                    }
                }
            }
        }
    }
}

function partner_vendor(vendor_partner) {

    $.ajax({
        type: 'POST',
        beforeSend: function() {

            $('body').loadingModal({
                position: 'auto',
                text: 'Loading Please Wait...',
                color: '#fff',
                opacity: '0.7',
                backgroundColor: 'rgb(0,0,0)',
                animation: 'wave'
            });

        },
        url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + vendor_partner,
        data: {
            vendor_partner_id: "<?php if(!empty($zopper)){echo $zopper[0]['entity_id'];}?>",
            invoice_flag: 0
        },
        success: function(data) {
            //console.log(data);
            $("#entity_id").select2().html(data).change();
            $('body').loadingModal('destroy');

        }
    });
}
$(document).on('keyup', '.part_estimate', function (e) {
    charges = 0;
    $(".part_estimate").each(function (i) {
        commission_in_persentage = ($("#around_part_commission").val()/100);
        price = $(this).val();
        charges += Number(price * commission_in_persentage);
        $("#total_parts_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.around_part_commission', function (e) {
    charges = 0;
    $(".around_part_commission").each(function (i) {
        commission_in_persentage = ($(this).val()/100);
        price = $("#part_charge").val();
        charges += Number(price * commission_in_persentage);
        $("#total_parts_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.service_charge', function (e) {
    charges = 0;
    $(".service_charge").each(function (i) {
        commission_in_persentage = ($("#around_service_commission").val()/100);
        price = $(this).val();
        charges += Number(price * commission_in_persentage);
        $("#total_service_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.around_service_commission', function (e) {
    charges = 0;
    $(".around_service_commission").each(function (i) {
        commission_in_persentage = ($(this).val()/100);
        price = $("#service_charge").val();
        charges += Number(price * commission_in_persentage);
        $("#total_service_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.transport_charge', function (e) {
    charges = 0;
    $(".transport_charge").each(function (i) {
        commission_in_persentage = ($("#around_transport_commission").val()/100);
        price = $(this).val();
        charges += Number(price * commission_in_persentage);
        $("#total_transport_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.around_transport_commission', function (e) {
    charges = 0;
    $(".around_transport_commission").each(function (i) {
        commission_in_persentage = ($(this).val()/100);
        price = $("#transport_charge").val();
        charges += Number(price * commission_in_persentage);
        $("#total_transport_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.courier_charge', function (e) {
    charges = 0;
    $(".courier_charge").each(function (i) {
        commission_in_persentage = ($("#around_courier_commission").val()/100);
        price = $(this).val();
        charges += Number(price * commission_in_persentage);
        $("#total_courier_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.around_courier_commission', function (e) {
    charges = 0;
    $(".around_courier_commission").each(function (i) {
        commission_in_persentage = ($(this).val()/100);
        price = $("#courier_charge").val();
        charges += Number(price * commission_in_persentage);
        $("#total_courier_charges").val((Number(price)+Number(charges)).toFixed(2));
    });
});

$(document).on('keyup', '.total_charges', function (e) {
    charges = 0;
    $(".charges").each(function (i) {
        price = $(this).val();
       
        charges += Number(price);
    });
    
    $("#total_charges").val((charges * 1.18).toFixed(2));
});


</script>
<?php if($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>