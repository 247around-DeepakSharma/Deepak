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
                                    <input type="text" class="form-control" id="booking_id" name="booking_id" value = "<?php echo $spare_parts[0]['booking_id']; ?>" placeholder="Enter Booking ID" readonly="readonly" required>
                                </div>   
                            </div>
                            <div class="form-group ">
                               <label for="User" class="col-md-4">User</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="serial_number" name="user_name" value = "<?php echo $spare_parts[0]['name']; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Mobile</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="mobile" name="mobile" value = "<?php echo $spare_parts[0]['booking_primary_contact_no']; ?>" placeholder="Enter Mobile" readonly="readonly" required>
                                </div>   
                            </div>
                            
                            
                            <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Requested Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="parts_name" name="parts_name" readonly="readonly" required><?php echo $spare_parts[0]['parts_requested']; ?></textarea>
                                </div>   
                            </div>
                            
                             
                            <!-- end col-md-6 -->
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Model Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="model_number" name="model_number" value = "<?php echo $spare_parts[0]['model_number']; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                          
                            
                            <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Serial Number</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="serial_number" name="serial_number" value = "<?php echo $spare_parts[0]['serial_number']; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            
                             <div class="form-group ">
                               <label for="Model Number" class="col-md-4">Date of Purchase</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="dop" name="dop" value = "<?php echo $spare_parts[0]['date_of_purchase']; ?>"  readonly="readonly" required>
                                </div>
                                    
                            </div>
                            
                            <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Invoice Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]['invoice_pic'])){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]['invoice_pic'];?>" target="_blank">View Image</a>
                                <?php } ?>
                                </div>
                                    
                            </div>
                            
                            
                           <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Serial Number Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]['serial_number_pic'])){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]['serial_number_pic'];?>" target="_blank">View Image</a>
                                 <?php } ?>
                                </div>
                                    
                            </div>
                             <div class="form-group ">
                               <label for="Invoice pic" class="col-md-4">Defective Part Image</label>
                                <div class="col-md-6">
                                    <?php if(!is_null($spare_parts[0]['defective_parts_pic'])){ ?>
                                    <a href="https://s3.amazonaws.com/bookings-collateral/misc-images/<?php echo $spare_parts[0]['defective_parts_pic'];?>" target="_blank">View Image</a>
                                 <?php } ?>
                                </div>
                                    
                            </div>
                           
                            
                        </div>
                    </div>
            </form>
                    
                
                
                
                <!-- Close Panel Body -->
            </div>
         </div>
          
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Spare Parts </h2>
            </div>
            <div class="panel-body">
                <form action="<?php echo base_url(); ?>partner/process_update_spare_parts/<?php echo $spare_parts[0]['booking_id']; ?>" class ="form-horizontal" id="update_form"  method="POST">
                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group ">
                               <label for="delivered_parts_name" class="col-md-4">Shipped Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="shipped_parts_name" name="shipped_parts_name" required  placeholder="Enter Shipped parts"></textarea>
                                </div>   
                            </div>
                            
                             <div class="form-group ">
                               <label for="awb" class="col-md-4">AWB</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="awb" name="awb" value = "" placeholder="Please Enter AWB"  required>
                                </div>  
                            </div>
                             <div class="form-group ">
                               <label for="remarks_by_partner" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_by_partner" placeholder="Please Enter Remarks"  required></textarea>
                                </div>  
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                             <div class="form-group ">
                               <label for="courier" class="col-md-4">Courier Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="courier_name" name="courier_name" value = "" placeholder="Please Enter courier Name"  required>
                                </div>  
                            </div>
                             <div class="form-group ">
                                <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="shipment_date" class="form-control"  name="shipment_date" type="date" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="EDD" class="col-md-4">EDD</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="edd" class="form-control"  name="edd" type="date" value = "" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="col-md-12 text-center">
                        <input type="submit" value="Update Booking" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" class="btn btn-md btn-default" />
                    </div>
                </form>
            </div>
           </div>
          
          
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
                awb: {
                    digits: true,
                    required:true
                    }
                },
                messages: {
                shipped_parts_name: "Please Enter Shipped Parts",
                remarks_by_partner: "Please Enter Remarks",
                courier_name: "Please Courier Name",
                awb: "Please Enter Valid AWB"
              
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

