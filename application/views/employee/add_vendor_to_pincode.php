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
                                    <div class="form-group <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Vendor*</label>
                           <div class="col-md-6">
                               <select type="text" class="form-control"  id="vendor" name="vendor_id" required>
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
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="pincode" name="pincode" value = "<?php if (isset($pincode)) {echo $pincode; } ?>" readonly="" required>
                           </div>
                           <div style="margin-left:25%;margin-top:40px;"><?php echo form_error('pincode'); ?></div>
                        </div>  
               
                              <?php if(isset($all_appliance)){ ?>
                                    <p style="display:none;" id="appliance_count_helper"><?php echo count($all_appliance); ?></p>
                                                <div class="form-group">
                           <label for="appliance" class="col-md-4">Appliance</label>
                           <div class="col-md-6">
                            <?php foreach($all_appliance as $key=>$values){
                                                  $checked="";
                                                  $disabled="disabled";
                                                  if(isset($selected_appliance)){
                                                            foreach($selected_appliance as $applianceData){
                                                                      if($applianceData['service_id'] == $values->id){
                                                                                $checked="checked";
                                                                      } 
                                                             }
                                                  }?>
                               <input  id="<?php echo $key?>" onclick="handleBrandDisplay(<?php echo "'".$key."'"?>)" type="checkbox" name="appliance[]" value="<?php echo $values->id."__".$values->services ?>" <?php echo $checked?>><?php echo $values->services;?>
                                                  <select <?php echo $disabled ?> multiple style="width:300px" class="brands" id="brands_<?php echo $key ?>" name="brands_<?php echo $values->id.'[]"'?>>
                                                          <option>First,Please select Any vendor</option>
                                                          </select>
                                                  <?php echo "</br>";
                                       } ?>
                           
                      </div>
                           </div>
                              <?php } ?>
                       
                        <div class="form-group">
                          <center>
                         <input type="submit" id="submitform" class="btn btn-info " value="Save"/>
                         </center>
                       </div>
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
$(".brands").select2();
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
  if(vendor_id !==""){
    $.ajax({
        type: "GET",
        url: '<?php echo base_url()?>employee/vendor/get_vendor_brands/' + vendor_id,
        success: function(data){
                      var brands = JSON.parse(data).brands;
                      var dropDown = "";
           for(var i=0;i<brands.length;i++){
                  var dropDown = dropDown+"<option value='"+brands[i]+"'>"+brands[i]+"</optinon>";
            } 
            var applianceCount = document.getElementById("appliance_count_helper").innerHTML;
            for(var x=0;x<applianceCount;x++){
                     isChecked = document.getElementById(x).checked;
                      if(isChecked == true){
                          document.getElementById("brands_"+x).disabled=false;
                      }
                     document.getElementById("brands_"+x).innerHTML = dropDown;
             }
          }

      });

  }
  

}

function handleBrandDisplay(serviceNumber){
            isChecked = document.getElementById(serviceNumber).checked;
            if(isChecked == true){
                document.getElementById("brands_"+serviceNumber).disabled=false;
            }
            else{
                document.getElementById("brands_"+serviceNumber).disabled=true;
            }
            
        }
</script>