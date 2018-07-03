<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
     Update Tagged Invoice
   </h1>
        <form name="myForm" class="form-horizontal" id ="myForm" action="<?php echo base_url();?>employee/inventory/"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Update Tagged Invoice</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                
                                <div class="form-group <?php if( form_error('previous_booking_id') ) { echo 'has-error';} ?>">
                                    <label for="previous_booking_id" class="col-md-4">Previous Booking ID *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="previous_booking_id" name="previous_booking_id" value="<?php echo set_value('previous_booking_id'); ?>" placeholder="Enter Booking ID" required>
                                    <?php echo form_error('previous_booking_id'); ?>
                                    </div>
                                   
                                </div>
                                
                                <div class="form-group <?php if( form_error('original_booking_id') ) { echo 'has-error';} ?>">
                                    <label for="original_booking_id" class="col-md-4">Original Booking ID *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="original_booking_id" name="original_booking_id" value="<?php echo set_value('original_booking_id'); ?>" placeholder="Enter Booking ID" required>
                                    <?php echo form_error('original_booking_id'); ?>
                                    </div>
                                   
                                </div>
                                
                                <div class="form-group <?php if( form_error('invoice_id') ) { echo 'has-error';} ?>">
                                    <label for="invoice_id" class="col-md-4">Invoice ID *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="invoice_id" name="invoice_id" value="<?php echo set_value('invoice_id'); ?>" placeholder="Enter Invoice ID" required>
                                    <?php echo form_error('invoice_id'); ?>
                                    </div>
                                   
                                </div>
                                
                                 <div class="form-group <?php if( form_error('inventory_item') ) { echo 'has-error';} ?>">
                                    <label for="inventory_item" class="col-md-4">Inventory *</label>
                                    <div class="col-md-6">
                                        <select name="inventory_item" class="form-control" id="inventory_item"   required>
                                            <option selected disabled="">Please Select Inventory</option>
                                           <?php foreach ($inventory as $value) { ?>
                                            <option value="<?php echo $value['inventory_id'];?>" ><?php echo $value['part_name'];?></option>
        
                                          <?php }?>
                                        </select>
                                        <?php echo form_error('inventory_item'); ?>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
   

            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
            <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
 
            </div>

        </form>
    </div>
</div>


<script type="text/javascript">
 (function ($, W, D)
    {
        var JQUERY4U = {};
    
        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        $("#myForm").validate({
                            rules: {
                                previous_booking_id: "required",
                                original_booking_id: "required",
                                invoice_id: "required",
                                inventory_item: "required"
    
                            },
                            messages: {
                                previous_booking_id: "Please enter previous booking ID",
                                original_booking_id: "Please enter original booking ID",
                                invoice_id: "Please enter invoice ID",
                                inventory_item: "Please Select Inventory",
                                
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
    
    