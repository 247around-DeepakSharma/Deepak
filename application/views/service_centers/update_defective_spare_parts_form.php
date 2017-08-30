<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
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
              <form action="<?php echo base_url(); ?>service_center/process_update_defective_parts/<?php echo $spare_parts[0]['booking_id']; ?>" class ="form-horizontal" 
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
                            
                        </div>
                 
                    <!-- Close Panel Body -->
                </div>
            </div>
            <?php $sp_id = array();?>
          
                <?php  foreach ($spare_parts as $value) { ?>
                <input type="hidden" class="form-control" id="defective_part_shipped" name="defective_part_shipped[<?php echo $value['id'];?>]" value="<?php echo $value['parts_requested']; ?>">
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
                                        <input type="text" class="form-control" id="awb_by_sf" name="awb_by_sf" value = "<?php echo set_value("awb_by_sf");?>" placeholder="Please Enter AWB"  required>
                                    </div>
                                    <?php echo form_error('awb_by_sf'); ?>

                                </div>
                                <div class="form-group <?php if (form_error('courier_charges_by_sf')) { echo 'has-error';} ?>">
                                    <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value = "<?php echo set_value("courier_charges_by_sf");?>" placeholder="Please Enter Courier Charges"  required>
                                    </div>
                                    <?php echo form_error('courier_charges_by_sf'); ?>
                                </div>
                                <div class="form-group <?php if (form_error('defective_courier_receipt')) { echo 'has-error';} ?>">
                                    <label for="AWS Receipt" class="col-md-4">Courier Invoice</label>
                                    <div class="col-md-6">
                                        <input id="aws_receipt" class="form-control"  name="defective_courier_receipt" type="file" required  style="background-color:#fff;pointer-events:cursor">
                                    </div>
                                     <?php echo form_error('defective_courier_receipt'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group <?php if (form_error('courier_name_by_sf')) { echo 'has-error';} ?>">
                                    <label for="courier" class="col-md-4">Courier Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="courier_name_by_sf" name="courier_name_by_sf" value = "<?php echo set_value("courier_name_by_sf");?>" placeholder="Please Enter Courier Name"  required>
                                    </div>
                                     <?php echo form_error('courier_name_by_sf'); ?>
                                </div>
                                <div class="form-group <?php if (form_error('defective_part_shipped_date')) { echo 'has-error';} ?>">
                                    <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="defective_part_shipped_date" class="form-control"  name="defective_part_shipped_date" type="date" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" required readonly='true' style="background-color:#fff;pointer-events:cursor">
                                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                     <?php echo form_error('defective_part_shipped_date'); ?>
                                </div>
                                <div class="form-group <?php if (form_error('remarks_defective_part')) { echo 'has-error';} ?>">
                               <label for="remarks_defective_part" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_defective_part" placeholder="Please Enter Remarks"  required><?php echo set_value("remarks_defective_part");?></textarea>
                                </div>  
                                <?php echo form_error('remarks_defective_part'); ?>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center" style="margin-bottom:30px;">
                    <input type="submit" value="Update Booking" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" class="btn btn-md btn-default" />
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#defective_part_shipped_date").datepicker({dateFormat: 'yy-mm-dd'});
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
    
</script>
<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success');  }?>