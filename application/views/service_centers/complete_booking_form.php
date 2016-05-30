<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>


<?php 
   if (isset($query2)) 
   {
     $brand  ="";
     $category="";
     $capacity="";
     
     for($i=0; $i< $booking[0]['quantity']; $i++)
     {
       $brand .=$query2[$i]['appliance_brand'].",";
       $category .=$query2[$i]['appliance_category'].",";
       $capacity .=$query2[$i]['appliance_capacity'].",";
   
     }
   } 
   ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>  Complete Booking Form</h2>
            </div>
            <div class="panel-body">
               <form class="form-horizontal" id="complete_booking_form" action="<?php echo base_url()?>employee/service_centers/process_complete_booking/<?php echo $booking_id ?>" method="POST" >
                  <div class="col-md-6">
                    
                     <div class="form-group ">
                        <label for="name" class="col-md-3">User Name</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="name" value = "<?php if (isset($booking[0]['name'])) {echo $booking[0]['name']; }?>"  disabled>
                          
                        </div>
                     </div>
                     <div class="form-group ">
                        <label for="phone_number" class="col-md-3">User Phone No:</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="phone_number" value = "<?php if (isset($booking[0]['phone_number'])) {echo $booking[0]['phone_number']; }?>"  disabled>
                           
                        </div>
                     </div>
                     <div class="form-group ">
                        <label for="appliance_brand" class="col-md-3">Brand</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="appliance_brand" value = "<?php echo $brand;?>"  disabled>
                           <?php echo form_error('appliance_brand'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                        <label for="appliance_category" class="col-md-3">Category</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="appliance_category" value = "<?php echo $category;?>"  disabled>
                           <?php echo form_error('appliance_category'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                        <label for="capacity" class="col-md-3">Capacity</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="appliance_capacity" value = "<?php echo $capacity;?>"  disabled>
                           <?php echo form_error('appliance_capacity'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('quantity') ) { echo 'has-error';} ?>">
                        <label for="quantity" class="col-md-3">Quantity</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="quantity" value = "<?php if (isset($booking[0]['quantity'])) {echo $booking[0]['quantity']; }?>" disabled>
                           <?php echo form_error('quantity'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                        <label for="booking_date" class="col-md-3">Booking Date</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="booking_date" value = "<?php if (isset($booking[0]['booking_date'])) {echo $booking[0]['booking_date']; }?>"  disabled>
                           <?php echo form_error('booking_date'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('booking_timeslot') ) { echo 'has-error';} ?>">
                        <label for="booking_timeslot" class="col-md-3">Booking Time</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="booking_timeslot" value = "<?php if (isset($booking[0]['booking_timeslot'])) {echo $booking[0]['booking_timeslot']; }?>"  disabled>    
                           <?php echo form_error('booking_timeslot'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('amount_due') ) { echo 'has-error';} ?>">
                        <label for="amount_due" class="col-md-3">Amount Due</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  name="amount_due" value = "<?php if (isset($booking[0]['amount_due'])) {echo $booking[0]['amount_due']; }?>"  disabled>    
                           <?php echo form_error('amount_due'); ?>
                        </div>
                     </div>
                     <div class="form-group <?php if( form_error('booking_remarks') ) { echo 'has-error';} ?>">
                        <label  for="booking_remarks" class="col-md-3">Booking Remark:</label>
                        <div class="col-md-6">
                           <textarea  rows="5" type="text" class="form-control"  name="booking_remarks" value = "<?php if (isset($booking[0]['booking_remarks'])){echo $booking[0]['booking_remarks'];}?>" disabled><?php if (isset($booking[0]['booking_remarks'])){echo $booking[0]['booking_remarks'];}?></textarea>
                           <?php echo form_error('booking_remarks'); ?>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group <?php if( form_error('service_charge') ) { echo 'has-error';} ?>">
                        <label for="service_charge" class="col-md-3">Service Charge</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control"  id="service_charge" name="service_charge" value ="<?php if(isset($charges[0]['service_charge'])){ echo $charges[0]['service_charge']; } else { echo "0"; } ?>"  placeholder="Enter Service Charge" required>
                           <?php echo form_error('service_charge'); ?>
                        </div>
                         <span id="errmsg2" style="color: red"></span>
                     </div>

                     <div class="form-group <?php if( form_error('additional_service_charge') ) { echo 'has-error';} ?>">
                        <label for="additional_service_charge" class="col-md-3">Additional Service Charge</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control" id="additional_service_charge" name="additional_service_charge" value ="<?php if(isset($charges[0]['additional_service_charge'])){ echo $charges[0]['additional_service_charge']; } else { echo "0"; } ?>"  placeholder="Enter Additional Service Charge" required>
                           <?php echo form_error('additional_service_charge'); ?>
                        </div>
                        <span id="errmsg1"></span>
                     </div>
                     <div class="form-group <?php if( form_error('parts_cost') ) { echo 'has-error';} ?>">
                        <label for="parts_cost" class="col-md-2">Parts Cost</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control" id="parts_cost" name="parts_cost" value ="<?php if(isset($charges[0]['parts_cost'])){ echo $charges[0]['parts_cost']; } else { echo "0"; } ?>"  placeholder="Enter Parts Cost">
                           <?php echo form_error('parts_cost'); ?>
                        </div>
                        <span id="errmsg"></span>
                     </div>
                     <div class="form-group">
                        <label for="parts_cost" class="col-md-2">Total Charge</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control" id="total_charge" name="total_charge"  placeholder="Total Charges" readonly>
                        </div>
                     </div>

                      <?php if (strstr($booking_id, "SS") == TRUE) { ?>
               <div class="form-group <?php if( form_error('internal_status') ) { echo 'has-error';} ?>">
                  <label for="internal_status" class="col-md-2">Internal Status</label>
                  <div class="col-md-10">
                     <?php foreach($internal_status as $status){?>
                     <div style="float:left;">
                        <input type="radio" class="form-control" <?php  if(isset($charges[0]['internal_status'])) { if($status->status == $charges[0]['internal_status']){ echo "checked"; } } ?> name="internal_status" id="internal_status"
                           value="<?php  echo $status->status;?>" style="height:20px;width:20px;margin-left:20px;" required>
                        <?php  echo $status->status;?>&nbsp;&nbsp;&nbsp;&nbsp;
                     </div>
                     <?php } ?> 
                     <?php echo form_error('internal_status'); ?>
                  </div>
               </div>
               <?php } else { ?>
               <input type ="hidden" name ="internal_status" value = "Completed" >
               <?php } ?>
                     
                     <div class="form-group <?php if( form_error('service_center_remarks') ) { echo 'has-error';} ?>">
                        <label for="description" class="col-md-2">Closing Remarks</label>
                        <div class="col-md-6">
                           <textarea rows="10"class="form-control"  name="closing_remarks" ><?php if(isset($charges[0]['service_center_remarks'])){ echo str_replace("<br/>","&#13;&#10;", $charges[0]['service_center_remarks']); }  ?></textarea>
                           <?php echo form_error('service_center_remarks'); ?>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12"><center>
                  <input type="submit" class="btn btn-primary btn-lg" value ="Save" ></input></center>
                  </div>
               </form>
            </div>
         </div>
         <!-- end md-12-->
      </div>
   </div>
</div>
<script type="text/javascript">
   
 $(document).ready(function () {
   sum_service_charges();
  //called when key is pressed in textbox
  $("#parts_cost").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
    sum_service_charges();
    

   });

  $("#additional_service_charge").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }

   });

  $("#service_charge").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }

   });


});

 function sum_service_charges(){
   var service_charge = $('#service_charge').val();
   var additional_service_charge  = $('#additional_service_charge').val();
   var parts_cost  = $('#parts_cost').val();

   var sum = Number(service_charge) + Number(additional_service_charge) + Number(parts_cost) ;
   $("#total_charge").val(sum);
 }

   $(document).on('keyup', '#service_charge', function(e) {

      sum_service_charges();
   });
   $(document).on('keyup', '#additional_service_charge', function(e) {

      sum_service_charges();
   });
   $(document).on('keyup', '#parts_cost', function(e) {

      sum_service_charges();
   });


 $(document).ready(function () {
  //called when key is pressed in textbox
  $("#grand_total_price").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg1").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

   (function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#complete_booking_form").validate({
                rules: {
                    closing_remarks: "required"
                    
                },
                messages: {
                    closing_remarks: "Please enter Closing Remarks"
                    
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);
</script>

<style type="text/css">
  /* example styles for validation form demo */
#complete_booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    padding: 0;
    text-align: left;
    width: 100%;
}
</style>
