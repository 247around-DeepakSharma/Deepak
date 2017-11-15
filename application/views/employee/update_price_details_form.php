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
                    if (isset($data) & !empty($data)) {
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
                                    <label for="parts">Total Part Charges </label>
                                    <input type="number" class="form-control" id="part_charge" name="part_charges" value = "<?php echo set_value("part_charges");?>" placeholder="Enter Parts Charge" required>
                                    <input type="hidden" class="form-control" name="booking_id" value="<?php
                                    if (isset($data)) {
                                        echo $data[0]['booking_id'];
                                    }
                                    ?>" id="booking_id" />
                                    <input type="hidden" name="assigned_vendor_id" value="<?php echo $data[0]['assigned_vendor_id']; ?>" />
                                    <input type="hidden" name="spare_parts" value="<?php if (isset($data['spare_parts'])){ echo "1"; }else { echo "0";} ?>" />
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Around Part Commission </label>
                                    <input type="number" class="form-control" id="part_charge" name="around_part_commission" value = "<?php echo set_value("around_part_commission");?>" placeholder="Enter Around Commission" required>
                                   
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Service Charges </label>
                                    <input type="number" class="form-control" id="service_charge" name="service_charge" value = "<?php echo set_value("service_charge");?>" placeholder="Enter Parts Charge" required>
                                </div>
                            </div>
                            
                            </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Transport Charges </label>
                                    <input type="number" class="form-control" id="trans_charge" name="transport_charge" value = "<?php echo set_value("transport_charge");?>" placeholder="Enter Parts Charge" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Courier Charges </label>
                                    <input type="number" class="form-control" id="courier_charge" name="courier_charge" value = "<?php echo set_value("courier_charge");?>" placeholder="Enter Parts Charge" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Part Will Arrange By </label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" name="same_diff_vendor" value="2">Same Vendor
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="same_diff_vendor" value="1">Different Vendor
                                    </label>
                                    
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="parts">Select Vendor or Partner </label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" name="entity" onclick="partner_vendor('vendor')" value="vendor">Vendor
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="entity" onclick="partner_vendor('partner')" value="partner">Partner
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
                            <div class="col-md-4">
                                <div class="form-group col-md-12 ">
                                    <label for="remarks">Remarks </label>
                                    <textarea class="form-control" placeholder="Enter Remarks" name="remarks" required><?php echo set_value("remarks"); ?></textarea>
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
    </div>
<?php } ?>
</div>

<script>
$("#entity").select2();

function check_validation() {
    var part_charges = Number($("#part_charge").val());

    if (part_charges > 0) {
        var part_arrange_by = $('input[name="same_diff_vendor"]:checked').val();
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
            vendor_partner_id: "",
            invoice_flag: 0
        },
        success: function(data) {
            //console.log(data);
            $("#entity_id").select2().html(data).change();
            $('body').loadingModal('destroy');

        }
    });
}
</script>
<?php $this->session->unset_userdata('success'); ?>