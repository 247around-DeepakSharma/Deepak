<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Requested Spare Parts </h2>
            </div>
            
            <div class="panel-body">
                <form action="#" class ="form-horizontal" >
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Booking ID</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="booking_ids" name="booking_ids" value = "<?php echo $spare_parts[0]->booking_id; ?>" placeholder="Enter Booking ID" readonly="readonly" required>
                                </div>   
                            </div>
                            <div class="form-group ">
                               <label for="User" class="col-md-4">User</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="serial_number" name="user_name" value = "<?php echo $spare_parts[0]->name; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Mobile</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="mobile" name="mobile" value = "<?php echo $spare_parts[0]->booking_primary_contact_no; ?>" placeholder="Enter Mobile" readonly="readonly" required>
                                </div>   
                            </div>
                            
                            
                            <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Requested Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="parts_name" name="parts_name" readonly="readonly" required><?php echo $spare_parts[0]->parts_requested; ?></textarea>
                                </div>   
                            </div>
                            
                             
                            <!-- end col-md-6 -->
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Model Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="model_number" name="model_number" value = "<?php echo $spare_parts[0]->model_number; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                          
                            
                            <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Serial Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="serial_number" name="serial_number" value = "<?php echo $spare_parts[0]->serial_number; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            
                             <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Date of Purchase</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="dop" name="dop" value = "<?php echo $spare_parts[0]->date_of_purchase; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            
                            <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Invoice Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]->invoice_pic)){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]->invoice_pic;?>" target="_blank">View Image</a>
                                <?php } ?>
                                </div>
                                    
                            </div>
                            
                            
                           <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Serial Number Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]->serial_number_pic)){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]->serial_number_pic;?>" target="_blank">View Image</a>
                                 <?php } ?>
                                </div>
                                    
                            </div>
                             <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Defective Part Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]->defective_parts_pic)){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]->defective_parts_pic;?>" target="_blank">View Image</a>
                                 <?php } ?>
                                </div>
                                    
                            </div>
                           
                            
                        </div>
                    </div>
            </form>
                    
                
                
                
                <!-- Close Panel Body -->
            </div>
         </div>
        <form enctype="multipart/form-data" action="<?php echo base_url(); ?>partner/process_update_spare_parts/<?php echo $spare_parts[0]->booking_id; ?>/<?php echo $spare_parts[0]->id; ?>" class ="form-horizontal" name="update_form" id="update_form"  method="POST">  
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Spare Parts </h2>
            </div>
                <div class="panel-body">    
                    <div class="row">
                        <div class="col-md-6">
                            <?php if(!is_null($spare_parts[0]->estimate_cost_given_date) || $spare_parts[0]->request_type == REPAIR_OOW_TAG){ ?>
                            <div class="form-group <?php
                                    if (form_error('incoming_invoice')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="Parts Invoice" class="col-md-4">Spare Invoice (PDF)</label>
                                <div class="col-md-6">
                                    <input type="file" name="incoming_invoice" class="form-control" required />
                                    <?php echo form_error('incoming_invoice'); ?>
                                </div>   
                                
                            </div>
                            <?php } ?>
                             <div class="form-group <?php
                                    if (form_error('shipped_parts_name')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="delivered_parts_name" class="col-md-4">Shipped Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="shipped_parts_name" name="shipped_parts_name" required  placeholder="Enter Shipped parts"></textarea>
                                 <?php echo form_error('shipped_parts_name'); ?>
                                </div> 
                                
                            </div>
                            
                            <input type="hidden" name="request_type" value="<?php echo $spare_parts[0]->request_type?>"/>
                            <input type="hidden" class="form-control" name="booking_id" value = "<?php echo $spare_parts[0]->booking_id; ?>"  required>
                             <div class="form-group <?php
                                    if (form_error('remarks_by_partner')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="remarks_by_partner" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_by_partner" placeholder="Please Enter Remarks"  required></textarea>
                                <?php echo form_error('remarks_by_partner'); ?>
                                </div>  
                               
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <?php if(!is_null($spare_parts[0]->estimate_cost_given_date) || $spare_parts[0]->request_type == REPAIR_OOW_TAG){ ?>
                            <div class="form-group <?php
                                    if (form_error('invoice_amount')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="Invoice Charge" class="col-md-4">Invoice Amount (including tax)</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="invoice_amount" name="invoice_amount" value = "" placeholder="Please Enter Invoice Amount"  required>
                                    <?php echo form_error('invoice_amount'); ?>
                                </div> 
                              
                            </div>
                            <?php } ?>
                             <div class="form-group <?php
                                    if (form_error('awb')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="awb" class="col-md-4">AWB</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="awb" name="awb" value = "" placeholder="Please Enter AWB"  required>
                                     <?php echo form_error('awb'); ?>
                                </div>  
                              
                            </div>
                             <div class="form-group <?php
                                    if (form_error('courier_name')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="courier" class="col-md-4">Courier Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="courier_name" name="courier_name" value = "" placeholder="Please Enter courier Name"  required>
                                    <?php echo form_error('courier_name'); ?>
                                </div> 
                              
                            </div>
                              
                             <div class="form-group <?php
                                    if (form_error('shipment_date')) {
                                        echo 'has-error';
                                    } ?>">
                                <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="shipment_date" class="form-control"  name="shipment_date" type="date" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                    <?php echo form_error('shipment_date'); ?>
                                </div>
                                 
                            </div>
<!--                            <div class="form-group ">
                                <label for="EDD" class="col-md-4">Estimated Delivery Date</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="edd" class="form-control"  name="edd" type="date" value = "<?php// echo  date("Y-m-d", strtotime("+2 day")); ?>" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>-->
                        </div>
                        
                        
                    </div>
                </div>
           </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                   <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>Upload Challan Details</h2>
                </div>
                <div class="panel-body">    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?php
                                    if (form_error('partner_challan_number')) {
                                        echo 'has-error';
                                    } ?>">
                                <label for="partner_challan_number" class="col-md-4">Challan Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="partner_challan_number" name="partner_challan_number" value = "" placeholder="Please Enter challan Number"  required>
                                     <?php echo form_error('partner_challan_number'); ?>
                                </div>  
                              
                            </div>   
                            <div class="form-group <?php
                                    if (form_error('approx_value')) {
                                        echo 'has-error';
                                    } ?>">
                                <label for="approx_value" class="col-md-4">Approx Value</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="approx_value" name="approx_value" value = "" placeholder="Please Enter approx value"  required>
                                     <?php echo form_error('approx_value'); ?>
                                </div>  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?php
                                    if (form_error('challan_file')) {
                                        echo 'has-error';
                                    } ?>">
                               <label for="challan_file" class="col-md-4">Challan File</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control" id="challan_file" name="challan_file">
                                     <?php echo form_error('challan_file'); ?>
                                </div>  
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 text-center" style="margin:20px 0px;">
                <input type="submit"  <?php if (!is_null($spare_parts[0]->estimate_cost_given_date) || $spare_parts[0]->request_type == REPAIR_OOW_TAG) { ?> 
                           onclick="return check_invoice_amount('<?php echo $spare_parts[0]->purchase_price; ?>')" <?php } ?> value="Update Booking" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" class="btn btn-md btn-default" />
            </div>
        </form>     
      </div>
   </div>
</div>
<script type="text/javascript">
    $("#edd").datepicker({dateFormat: 'yy-mm-dd'});
    $("#shipment_date").datepicker({dateFormat: 'yy-mm-dd'});
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
                shipped_parts_name: "required",
                remarks_by_partner: "required",
                courier_name:"required",
                awb: "required",
                shipment_date:"reuired"
                },
                messages: {
                shipped_parts_name: "Please Enter Shipped Parts",
                remarks_by_partner: "Please Enter Remarks",
                courier_name: "Please Courier Name",
                awb: "Please Enter Valid AWB",
                shipment_date:"Please Enter Shipped date"
              
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
    
    function check_invoice_amount(estimate_given){
       
        var invoice_amount = Number($("#invoice_amount").val()); 
        if(invoice_amount > Number(estimate_given)){
            swal("OOPS!", "Invoice amount exceeding the quote provided earlier.", "error");
            return false;
        } else if(Number(invoice_amount) === 0){
            swal("OOPS!", "Please Enter Invoice amount.", "error");
            return false;
        }
       
    }

</script>

