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
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Spare Parts </h2>
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
 
                            <!-- end col-md-6 -->
                        </div>
                        <div class="col-md-6">
                             <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Requested Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="parts_name" name="parts_name" readonly="readonly" required><?php echo $spare_parts[0]['parts_requested']; ?></textarea>
                                </div>   
                            </div>
                             <div class="form-group ">
                               <label for="Booking ID" class="col-md-4">Received Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="parts_name" name="parts_name" readonly="readonly" required><?php echo $spare_parts[0]['parts_shipped']; ?></textarea>
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
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Defective Spare Parts </h2>
            </div>
            <div class="panel-body">
                <form action="<?php echo base_url(); ?>service_center/process_update_defective_parts/<?php echo $spare_parts[0]['booking_id']; ?>/<?php echo $spare_parts[0]['id']; ?>" class ="form-horizontal" 
                      id="update_form"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group ">
                               <label for="defective_part_shipped" class="col-md-4">Defective Parts</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="defective_part_shipped" name="defective_part_shipped" required  placeholder="Enter Defective Shipped parts"></textarea>
                                </div>   
                            </div>
                            
                            
                             <div class="form-group ">
                               <label for="remarks_defective_part" class="col-md-4">Remarks</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_defective_part" placeholder="Please Enter Remarks"  required></textarea>
                                </div>  
                            </div>
                            
                            <div class="form-group ">
                               <label for="awb" class="col-md-4">AWB</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="awb_by_sf" name="awb_by_sf" value = "" placeholder="Please Enter AWB"  required>
                                </div>  
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group ">
                               <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value = "" placeholder="Please Enter Courier Charges"  required>
                                </div>  
                            </div>
                            
                             <div class="form-group ">
                               <label for="courier" class="col-md-4">Courier Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="courier_name_by_sf" name="courier_name_by_sf" value = "" placeholder="Please Enter Courier Name"  required>
                                </div>  
                            </div>
                             <div class="form-group ">
                                <label for="shipment_date" class="col-md-4">Shipment Date</label>
                                <div class="col-md-6">
                                <div class="input-group input-append date">
                                    <input id="defective_part_shipped_date" class="form-control"  name="defective_part_shipped_date" type="date" value = "<?php echo  date("Y-m-d", strtotime("+0 day")); ?>" required readonly='true' style="background-color:#fff;pointer-events:cursor">
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                </div>
                            </div>
                            
                             <div class="form-group ">
                                <label for="AWS Receipt" class="col-md-4">Courier Receipt</label>
                                <div class="col-md-6">
                                    <input id="aws_receipt" class="form-control"  name="defective_courier_receipt" type="file" required  style="background-color:#fff;pointer-events:cursor">
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
                defective_part_shipped: "required",
                remarks_defective_part: "required",
                courier_name_by_sf:"required",
                awb_by_sf: "required",
                defective_part_shipped_date: "required",
                courier_charges_by_sf: "customNumber",
                defective_courier_receipt:"required"
                },
                messages: {
                defective_part_shipped: "Please Enter Shipped Parts",
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

