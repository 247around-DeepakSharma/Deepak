<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <?php if($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>' . $this->session->userdata('success') . '</strong>
                </div>';
                }
                ?>
              <form action="<?php echo base_url(); ?>service_center/process_update_defective_parts/<?php echo $spare_parts[0]['id']; ?>" class ="form-horizontal" 
                id="update_form"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>Booking Details </h2>
                </div>
                <div class="panel-body">
                   
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="Booking ID" class="col-md-4">Booking ID</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_id" name="booking_id" value = "<?php echo $spare_parts[0]['booking_id']; ?>" placeholder="Enter Booking ID" readonly="readonly" required>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="User" class="col-md-3">User</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="serial_number" name="user_name" value = "<?php echo $spare_parts[0]['name']; ?>"  readonly="readonly" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="Booking ID" class="col-md-3">Mobile</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="mobile" name="mobile" value = "<?php echo $spare_parts[0]['booking_primary_contact_no']; ?>" placeholder="Enter Mobile" readonly="readonly" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="defective_return_to_entity_type" name="defective_return_to_entity_type" value="<?php echo $spare_parts[0]['defective_return_to_entity_type']; ?>">
                            <input type="hidden" class="form-control" id="defective_return_to_entity_id" name="defective_return_to_entity_id" value="<?php echo $spare_parts[0]['defective_return_to_entity_id']; ?>">
                            <input type="hidden" class="form-control" id="shipped_inventory_id" name="shipped_inventory_id" value="<?php echo $spare_parts[0]['shipped_inventory_id']; ?>">
                        </div>
                 
                    <!-- Close Panel Body -->
                </div>
            </div>
            <?php $sp_id = array();?>
          
                <?php  foreach ($spare_parts as $value) { ?>
                <input type="hidden" class="form-control" id="defective_part_shipped" name="defective_part_shipped[<?php echo $value['id'];?>]" value="<?php echo $value['parts_shipped']; ?>">
                <input type="hidden" class="form-control" id="defective_part_shipped" name="partner_challan_number[<?php echo $value['id'];?>]" value="<?php echo $value['partner_challan_number']; ?>">
                <input type="hidden" class="form-control" id="defective_part_shipped" name="challan_approx_value[<?php echo $value['id'];?>]" value="<?php echo $value['challan_approx_value']; ?>">
                <input type="hidden" class="form-control" id="defective_part_shipped" name="parts_requested[<?php echo $value['id'];?>]" value="<?php echo $value['parts_requested']; ?>">
                <?php 
                 } ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Courier Details</h2>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">                                                             
                                <div class="form-group <?php if (form_error('awb_by_sf')) { echo 'has-error';} ?>">
                                    <label for="awb" class="col-md-4">AWB</label>
                                    <div class="col-md-6">
                                        <input onblur="check_awb_exist()" type="text" class="form-control" id="awb_by_sf" name="awb_by_sf" value = "<?php if((set_value("awb_by_sf"))){ echo set_value("awb_by_sf");} else{ echo $spare_parts[0]['awb_by_sf'];}?>" placeholder="Please Enter AWB"  required>
                                    </div>
                                    <?php echo form_error('awb_by_sf'); ?>

                                </div>
                                <div class="form-group <?php if (form_error('courier_charges_by_sf')) { echo 'has-error';} ?>">
                                    <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value = "<?php if((set_value("courier_charges_by_sf"))){ echo set_value("courier_charges_by_sf");} else{ echo $spare_parts[0]['courier_charges_by_sf'];}?>" placeholder="Please Enter Courier Charges"  required>
                                    </div>
                                    <?php echo form_error('courier_charges_by_sf'); ?>
                                </div>
                                 <div class="form-group <?php if (form_error('awb_by_sf')) { echo 'has-error';} ?>">
                                    <label for="awb" class="col-md-4">No Of Boxes</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="defective_parts_shipped_boxes_count" name="defective_parts_shipped_boxes_count" required="">
                                            <option selected="" disabled="" value="">Select Boxes</option> 
                                            <?php for($i = 1 ; $i < 11; $i++){ ?>
                                            <option value="<?php echo $i;?>"><?php echo $i; ?></option> 
                                            <?php } ?>
                                        </select>     
                                    </div>
                                    <?php echo form_error('awb_by_sf'); ?>
                                </div>  
                                <div class="form-group <?php if (form_error('defective_courier_receipt')) { echo 'has-error';} ?>">
                                    <label for="AWS Receipt" class="col-md-4">Courier Invoice</label>
                                    <div class="col-md-6">
                                        <input id="aws_receipt" class="form-control"  name="defective_courier_receipt" type="file" required  style="background-color:#fff;pointer-events:cursor">
                                        <?php if(!empty($value['defective_courier_receipt'])) {?><a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $value['defective_courier_receipt']; ?> " target="_blank">Click Here to download previous invoice</a><?php } ?>
                                        <input type="hidden" class="form-control"  id="exist_courier_image" name="exist_courier_image" >
                                    </div>
                                     <?php echo form_error('defective_courier_receipt'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">                                                                
                                <div class="form-group <?php if (form_error('courier_name_by_sf')) { echo 'has-error';} ?>">
                                    <label for="courier" class="col-md-4">Courier Name</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="courier_name_by_sf" name="courier_name_by_sf" required>
                                            <option selected="" disabled="" value="">Select Courier Name</option>
                                            <?php foreach ($courier_details as $value1) { ?> 
                                            <option <?php if((set_value("courier_name_by_sf") == $value1['courier_name'])){ echo "selected";} else if($spare_parts[0]['courier_name_by_sf'] == $value1['courier_code']){ echo "selected";}?> value="<?php echo $value1['courier_code'];?>"><?php echo $value1['courier_name'];?></option>
                                            <?php } ?>
                                        </select>
                                        
                                    </div>
                                     <?php echo form_error('courier_name_by_sf'); ?>
                                </div>                              
                                <div class="form-group <?php if (form_error('defective_part_shipped_date')) { echo 'has-error';} ?>">
                                    <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="defective_part_shipped_date" class="form-control"  name="defective_part_shipped_date" type="text" value = "<?php if(!empty($spare_parts[0]['defective_part_shipped_date'])){ echo $spare_parts[0]['defective_part_shipped_date']; } else { echo  date("Y-m-d", strtotime("+0 day"));} ?>" required readonly='true' style="background-color:#fff;pointer-events:cursor">
                                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                     <?php echo form_error('defective_part_shipped_date'); ?>
                                </div>
                                <div class="form-group <?php if (form_error('courier_name_by_sf')) {
                                    echo 'has-error';
                                } ?>">
                                    <label for="courier" class="col-md-4">Weight</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" style="width: 31%; display: inline-block;" id="defective_parts_shipped_weight_in_kg" name="defective_parts_shipped_kg" value="" placeholder="Weight in KG" required=""> <strong> in KG</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="number" class="form-control" style="width: 31%; display: inline-block;" id="defective_parts_shipped_weight_in_gram" name="defective_parts_shipped_gram" value="" placeholder="in gram" required="">&nbsp;<strong>in Gram </strong>                                       
                                    </div>
                                <?php echo form_error('courier_name_by_sf'); ?>
                                </div>
                                <div class="form-group <?php if (form_error('remarks_defective_part')) { echo 'has-error';} ?>">
                               <label for="remarks_defective_part" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_defective_part" placeholder="Please Enter Remarks"  required><?php if((set_value("remarks_defective_part"))){ echo set_value("remarks_defective_part");} else{ echo $spare_parts[0]['remarks_defective_part_by_sf'];}?></textarea>
                                </div>  
                                <?php echo form_error('remarks_defective_part'); ?>
                            </div>
                                 
                            </div>
                        </div>
                        <div class="text-center">
                            <span id="same_awb" style="display:none">This AWB already used same price will be added</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center" style="margin-bottom:30px;">
                    
                    <input type="hidden" name="sf_id" value="<?php echo $spare_parts[0]['service_center_id']?>">
                    <input type="hidden" name="booking_partner_id" value="<?php echo $spare_parts[0]['booking_partner_id']?>">
                    <input type="submit" value="Update Booking" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" class="btn btn-md btn-default" />
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
//    $("#defective_part_shipped_date").datepicker({dateFormat: 'yy-mm-dd'});
     $('#defective_part_shipped_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate:false,
        locale:{
            format: 'YYYY-MM-DD'
        }
    });
            
    $('#defective_part_shipped_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $('#defective_part_shipped_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    
    $("#defective_parts_shipped_weight_in_kg").on({
        "click": function() {
            var weight_kg = $(this).val();            
            if(weight_kg.length>2){
                $(this).val('');
                return false;                
            }
        },
        "keypress": function() { 
            var weight_kg = $(this).val();            
            if(weight_kg.length>1){
                $(this).val('');
                return false;                
                    }
        },
        "mouseleave":function(){
            var weight_kg = $(this).val();            
            if(weight_kg.length>2){
                $(this).val('');
                return false;                
                    }
        }
    });
    
    
    $("#defective_parts_shipped_weight_in_gram").on({
        "click": function() {
            var weight_kg = $(this).val();            
            if(weight_kg.length>3){
                $(this).val('');
                return false;                
            }
        },
        "keypress": function() { 
            var weight_kg = $(this).val();            
            if(weight_kg.length>2){
                $(this).val('');
                return false;                
                    }
        },
        "mouseleave":function(){
            var weight_kg = $(this).val();            
            if(weight_kg.length>3){
                $(this).val('');
                return false;                
                    }
        }
    });
    
     $('#defective_parts_shipped_weight_in_gram,#defective_parts_shipped_weight_in_kg').bind('keydown', function (event) {
        switch (event.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
            break;
            default:
            var regex = new RegExp("^[a-zA-Z0-9,]+$");
            var key = event.key;
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
            break;
        }
    });
</script>
<style type="text/css">
    #update_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 0px 0 0px 0px;
    padding: 0;
    text-align: left;
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
          $("#update_form").validate({
                rules: {
                remarks_defective_part: "required",
                courier_name_by_sf:"required",
                awb_by_sf: "required",
                defective_part_shipped_date: "required",
                courier_charges_by_sf: "customNumber",
                defective_courier_receipt:"required"
                },
                messages: {
                remarks_defective_part: "Please Enter Remarks",
                courier_name_by_sf: "Please Enter Courier Name",
                awb_by_sf: "Please Enter Valid AWB",
                defective_part_shipped_date:"Please Select Shipped Date",
                courier_charges_by_sf: "Please Enter Valid Courier Charges",
                defective_courier_receipt:"Please Select Courier Receipt"
              
                },
                submitHandler: function (form) {
                form.submit();
                }
            });
            }
        };
        $.validator.addMethod('customNumber', function (value, element) {
        return this.optional(element) || /^[\d.]+$/.test(value);
    }, "Please Enter Valid Courier Charges");
    
    
    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
        JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    
    function check_awb_exist(){
            var awb = $("#awb_by_sf").val();
            if(awb){
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
                    url: '<?php echo base_url() ?>employee/service_centers/check_sf_shipped_defective_awb_exist',
                    data:{awb:awb},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.code === 247){
                            alert("This AWB already used same price will be added");
                            $("#same_awb").css("display","block");
                            $('body').loadingModal('destroy');
                           
                            $("#defective_part_shipped_date").val(data.message[0].defective_part_shipped_date);
                            $("#courier_name_by_sf").val(data.message[0].courier_name_by_sf);
                            $("#courier_charges_by_sf").val("0");
                            $("#courier_charges_by_sf").css("display","none");
                            if(data.message[0].defective_courier_receipt){
                               
                                $("#exist_courier_image").val(data.message[0].defective_courier_receipt);
                                $("#aws_receipt").css("display","none");
                            }

                        } else {

                            $('body').loadingModal('destroy');
                            $("#aws_receipt").css("display","block");
                            $("#courier_charges_by_sf").css("display","block");
                            $("#same_awb").css("display","none");
                            $("#exist_courier_image").val("");
                        }

                    }
                });
            }
            
        }
    
</script>
<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success');  }?>