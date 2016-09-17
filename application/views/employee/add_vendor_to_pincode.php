<style type="text/css">
ul{
     list-style:none;
}
#service_id,#pincode,#city,#area,#state,#vendor{
  margin-bottom:1px;
}
select[multiple], select[size]{
  min-height:140px;
}
.alert{
  padding:0 0 0 20px;
  width:38%;
  font-size:75%;
}
</style>
<div id="page-wrapper" >
   <div class="container" >
      	<div class="panel panel-info" style="margin-top:20px;">
         	<div class="panel-heading">Assign Vendor to Pincode</div>
         	<div class="panel-body">
        		
         		<div class="row">
               <form name="myForm" class="form-horizontal" id ="vendor_form" action="<?php echo base_url()?>employee/vendor/process_add_vendor_to_pincode_form"  method="POST" enctype="multipart/form-data">
         			<div class="container">
         				<div class="col-md-12">
         			<div class="col-md-7">
               <input type="hidden" value="<?php echo isset($Appliance)?$Appliance:''?>" name="Appliance"/>   
                   <input type="hidden" value="<?php echo isset($Appliance_ID)?$Appliance_ID:''?>" name="Appliance_ID"/>    
               
                   
         				<div class="form-group">
                           <label for="name" class="col-md-4">Appliance</label>
                           <div class="col-md-6">
                              <select type="text" class="form-control"  id="service_id" name="service_id[]" value = "<?php echo set_value('service_id'); ?>" required readonly>
                                  <option value=<?= $Appliance_ID; ?>>
                                                <?php echo $Appliance; 
                                                ?>
                                  </option>
                               </select>

                           </div>
                      </div>

                       <div class="form-group <?php if( form_error('brand') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Brand</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control" id="brand" name="brand" value = "<?php if (isset($brand)) {echo $brand; }  ?>" >
                           </div>
                            <?php echo form_error('pincode'); ?>
                        </div>

                        <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Pincode</label>
                           <div class="col-md-6">
                            <input type="text" class="form-control" id="pincode" name="pincode" value = "<?php if (isset($pincode)) {echo $pincode; } ?>" >
                           </div>
                            <?php echo form_error('pincode'); ?>
                        </div>
                        <div class="form-group <?php if( form_error('city') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">City</label>
                           <div class="col-md-6">
                           <input type="text" class="form-control" id="city" name="city" value = "<?php if (isset($city)) {echo $city; } ?>" >
                           </div>
                           <?php echo form_error('city'); ?>
                        </div>
                        <div class="form-group <?php if( form_error('area') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Area</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="area" name="area" value = "<?php if (isset($area)) {echo $area; } ?>" >
                           </div>
                           <?php echo form_error('area'); ?>
                        </div>
                        <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">State</label>
                           <div class="col-md-6">
                            <select type="text" class="form-control"  class="form-control" id="state" name="state" >
                                            <option value="">Select State</option>
                                            <?php foreach ($state as $key => $value) { ?>
                                             <option  value=<?= $value['state']; ?> <?php echo (isset($state))?'selected="selected"':''?> > <?php echo $value['state']; } ?></option>
                               </select>
                           </div>
                           <?php echo form_error('state'); ?>
                        </div>
                        <div class="form-group <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Vendor</label>
                           <div class="col-md-6">
                               <select type="text" class="form-control"  id="vendor" name="vendor_id">
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendor_details as $key => $values) { ?>
                                             <option  value=<?= $values['Vendor_ID']; ?> <?php if(isset($vendor_id)){ if(!empty($vendor_id)){ echo "selected"; } }?> > <?php echo $values['Vendor_Name']; } ?></option>
                               </select>
                           </div>
                           <?php echo form_error('vendor_id'); ?>
                        </div>
                        <div class="form-group">
                          <center>
                         <input type="submit" id="submitform" class="btn btn-info " value="Save"/>
                         </center>
                       </div>
         			</div>

         			<div class="col-md-5">
         				<div class="form-group <?php if( form_error('choice') ) { echo 'has-error';} ?>">
         					<label for="name">Vendor Availiable Services</label><hr>

                   <?php if(!empty(form_error('choice'))){?>
                      <div class="alert alert-danger">
                        <?php echo form_error('choice'); ?>
                      </div>
                  <?php }?>

         					<div id="vendor_services">
         						 <span>No Vendor Selected</span>
         					</div>
                    
         				</div>
                    <!-- <div style="color:red;border:1px solid maroon;">
                    <span><?php echo form_error('choice'); ?></span>
                  </div> -->
         			</div>

         		</div>
         		</div>
          </form>
         		</div>

        	</div>
    	</div>
    </div>
</div>

<script type="text/javascript">

//Making request Onchange 
$('#vendor').on('change',function(){

//Making an AJAX request to get data
call_get_vendor_services();

});

$(document).ready(function () {
  
 call_get_vendor_services();
});

function call_get_vendor_services(){
  var vendor_id = $('#vendor').val();
  if(vendor_id !=""){
    $.ajax({
        type: "GET",
        url: '<?php echo base_url()?>employee/vendor/get_vendor_services/' + vendor_id,
        success: function(data){
          var appliance = JSON.parse(data).Appliance;
          var appliance_id = JSON.parse(data).Appliance_ID;

            //Assigning value to div
            $("#vendor_services" ).empty();
            $('#vendor_services').append("<ul id='newList'></ul>");
              for (cnt = 0; cnt < appliance.length; cnt++) {
                $("#newList").append("<li><input type='checkbox'  name='choice[]' value='"+appliance_id[cnt]+"' />&nbsp;&nbsp;"+appliance[cnt] +"</li>");
                // $("#newList").append("<li>"+appliance[cnt] +"</li>");
              }

          }

      });

  }
  

}


</script>