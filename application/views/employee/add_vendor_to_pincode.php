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
#appliance_container{
    display: inline;
}
#appliance_brand_container{
    clear: both;
    margin: 10px;
}
#brand_container{
    display: inline;
}
.appliance_checked_container{
        background: #fff828;
    border-radius: 8px;
}
.vendor_exists_services{
    background:#acf7b2;
    border-radius: 8px;
}
</style>
<div id="page-wrapper" >
   <div class="container" >
      	<div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">Assign Vendor to Pincode
                <div class="pull-right">
                    <p><span style="padding: 8px 12px;margin: 10px;" class="appliance_checked_container">Requested Appliances</span><span style="padding: 8px 12px;"  class="vendor_exists_services">Appliances SF Already Servicing</span></p>
                </div>
            </div>
         	<div class="panel-body">
        		
         		<div class="row">
               <form name="myForm" class="form-horizontal" id ="vendor_form" action="<?php echo base_url()?>employee/vendor/process_add_vendor_to_pincode_form"  method="POST" enctype="multipart/form-data">
         			<div class="container">
         				<div class="col-md-12">
                                    <div class="form-group <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Vendor*</label>
                           <div class="col-md-6" style="padding:0px;">
                               <select style="width:300px" class="brands" id="vendor" name="vendor_id" required="">
                                                           <option value="">Select Vendor</option>
                                            <?php if (isset($vendors)){?>
                                            <?php foreach ($vendors as $key => $values) { ?>
                                            <option  value="<?php echo $values['Vendor_ID']."__".$values['Vendor_Name']; ?>" > <?php echo $values['Vendor_Name']; } ?></option>
                                            <?php } ?>
                               </select>
                           </div>
                           <div style="margin-left:40%;margin-top:40px;"><?php echo form_error('vendor_id'); ?></div>
                        </div> 
                                    
                          <div class="form-group <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Pincode*</label>
                           <div class="col-md-6" style="padding:0px;width:47%;">
                               <input type="text" class="form-control" id="pincode" name="pincode" value = "<?php if (isset($pincode)) {echo $pincode; } ?>" readonly="" required>
                           </div>
                           <div style="margin-left:25%;margin-top:40px;"><?php echo form_error('pincode'); ?></div>
                        </div>  
               
                              <?php if(isset($all_appliance)){ ?>
                                    <p style="display:none;" id="appliance_count_helper"><?php echo count($all_appliance); ?></p>
                                                <div class="form-group">
                                                    <label for="appliance" class="col-md-4" style="margin:0px 0px 20px 0px;">Appliance*</label>
                                                    <div id="appliance_brand_container">
                            <?php 
                            $allCheckedValue = array();
                            foreach($all_appliance as $key=>$values){
                                                  $checked="";
                                                  $disabled="disabled";
                                                  if(isset($selected_appliance)){
                                                            foreach($selected_appliance as $applianceData){
                                                                      if($applianceData['service_id'] == $values->id){
                                                                                $checked="checked";
                                                                                $allCheckedValue[] = $values->id."__".$values->services;
                                                                      } 
                                                             }
                                                  }?>
                               
                                                        <div id="appliance_container" style="width: 30%;margin: 4px 4px;" class="col-md-4 <?php if($checked=='checked'){echo "appliance_checked_container";} ?>">
                                                            <input  id="<?php echo $key?>" style="display:inline" type="checkbox"  name="appliance[]" value="<?php echo $values->id."__".$values->services ?>" <?php echo $checked?>><?php echo $values->services;?>
                               </div>
                                                 
                                       <?php } ?>   
                                                        <p id="already_selected_boxes" style="display:none;"><?php echo json_encode($allCheckedValue); ?></p>
                               </div>   
                           </div>
                              <?php } ?>

         		</div>
         		</div>
          </form>
                            <div class="form-group">
                          <center>
                              <input type="button" id="submitform" class="btn btn-info " onclick="formValidation()" value="Save"/>
                         </center>
                       </div>
         		</div>

        	</div>
    	</div>
    </div>
</div>
    </div>
    <p id="last_vendor_value_holder" style="display:none;"></p>
<script type="text/javascript">
$(".brands").select2();
$(".vendor").select2();
        function formValidation(){
            var applianceCount = document.getElementById("appliance_count_helper").innerHTML;
            var vendor = document.getElementById("vendor").value;
            if(!vendor){
                alert("Please select any vendor");
                exit();
            }
            var selectedAppliance =0;
            for(var x=0;x<applianceCount;x++){
                     isChecked = document.getElementById(x).checked;
                      if(isChecked === true){
                          var selectedAppliance = 1;
                      }
                  }
                  if(selectedAppliance===0){
                      alert("Please Select Atleast 1 Appliance");
                      exit();
                  }
                  else{
                        document.getElementById("vendor_form").submit();
                  }
        }
        function checkUncheckVendorServices(checkboxValue,className,response){
             var obj = JSON.parse(response);
             var alreadyExistServicesObj = JSON.parse($("#already_selected_boxes").html());
            for(var i=0;i<obj.length;i++){
            if($.inArray(obj[i].service, alreadyExistServicesObj) > -1){
            }
            else{
              $(":checkbox[value='"+obj[i].service+"']").prop("checked",checkboxValue);
              $(":checkbox[value='"+obj[i].service+"']").parent().attr('class', className);
          }
          }
        }
$("#vendor").change(function(){
   var vendor_id = $("#vendor").val().split("_")[0];
    $.ajax({
        type: 'GET',
        url: '<?php echo base_url()?>employee/vendor/getServicesForVendor/'+vendor_id,
        success: function (response) {
            var lastVendorData = $("#last_vendor_value_holder").html();
            if(lastVendorData !== ''){
                checkUncheckVendorServices("","col-md-4",lastVendorData);
            }
            checkUncheckVendorServices("true","col-md-4 vendor_exists_services",response);
            $("#last_vendor_value_holder").html(response);
       }
    });
});
</script>