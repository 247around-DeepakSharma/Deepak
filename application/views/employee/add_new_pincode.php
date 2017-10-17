<style type="text/css">
    .repeat_input_container{
   border: 1px solid #5fa2c3;
    margin: 0px auto;
    padding: 17px 20px 0px 20px;
    width: 61%;
    }
    .button_container{
        width: 36%;
    margin: 0px auto;
    padding: 20px 0px;
    }
    .view_container{
        width: 185.7px;
        text-align: center; 
    }
    .ul_holder{
         background: #d9edf7;
         border: 1px solid #70adac;
    margin: 10px;
    }
    #view_display_holder{
        border: 1px solid #2c9d9c;
    margin: 10px;
    padding: 0px;
    }
</style>
<div id="page-wrapper" >
<div class="container" >
<div class="panel panel-info" style="margin-top:20px;">
    <div class="panel-heading"style="text-align:center;font: bold 20px/28px Arial"><b>Before Assigning the SF to Pincode, Please enter Pincode details</b></div>
    <div id="view_display_holder">
    <ul class="list-inline ul_holder" style="margin: 0px auto;background: #2c9d9c;border: 0px;">
        <li class="list-inline-item view_container" ><b>District</b></li>
        <li class="list-inline-item view_container" style="margin-left: -4px;"><b>Taluk</b></li>
        <li class="list-inline-item view_container" style="margin-left: -4px;"><b>Region</b></li>
        <li class="list-inline-item view_container" style="margin-left: -4px;"><b>Division</b></li>
        <li class="list-inline-item view_container" style="margin-left: -4px;"><b>Area</b></li>
        <li class="list-inline-item view_container" style="margin-left: -3.6px;"><b>Cancel</b></li>
</ul>
        <div style="clear:both"></div>
    </div>
 <div class="panel-body">
     <div style="clear:both"></div>
        	<div class="row">
               <form name="myForm" class="form-horizontal" id ="pincode_form" action="<?php echo base_url()?>employee/vendor/add_new_pincode"  method="POST" enctype="multipart/form-data">
                   <div class="container">
                    <div class="form-group">
                           <label for="pincode" class="col-md-4">Pincode*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="pincode" name="pincode" value = "<?php if (isset($pincode)) {echo $pincode; } ?>" readonly="" required>
                           </div>
                        </div>  
                     <div class="form-group <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="States" class="col-md-4">States*</label>
                           <div class="col-md-6">
                               <select type="text" class="form-control"  id="states" name="states" required>
                                            <option value="">Select States</option>
                                            <?php if (isset($states)){?>
                                                    <?php foreach ($states as $key => $values) { ?>
                                                    <option  value="<?php echo $values['state'] ?>" > <?php echo $values['state'];?></option>
                                                    <?php } 
                                            }?>
                               </select>
                           </div>
                        </div> 
                       <div class="repeat_input_container">
                           <div class="form-group">
                           <label for="district" class="col-md-4">District*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="district" name="district" value = "">
                           </div>
                        </div>  
                           <div class="form-group">
                           <label for="taluk" class="col-md-4">Taluk*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="taluk" name="taluk" value = "" >
                           </div>
                        </div>  
                           <div class="form-group">
                           <label for="region" class="col-md-4">Region*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="region" name="region" value = "" >
                           </div>
                        </div>  
                           <div class="form-group">
                           <label for="division" class="col-md-4">Division*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="division" name="division" value = "">
                           </div>
                        </div> 
                           <div class="form-group">
                           <label for="area" class="col-md-4">Area*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="area" name="area" value = "" >
                           </div>
                        </div>  
                           <input type="hidden" id="value_holder" value="" name="value_holder">
                           <p id="last_value_holder" style="display:none;"></p>
                       </div>
                   </div>
               </form>
                    <div class="button_container">
                        <input style='float:left;' type="button" id="submitform" class="btn btn-info " onclick="addNewInputFields()" value="Add More Area"/>
                        <input style='float:right' type="button" id="submitform" class="btn btn-info " onclick="savePincode()" value="Save"/>
                    </div>
                        
  </div>
</div>
    	</div>
</div>
</div>
<script type="text/javascript">
    function checkNotEmpty(fieldNameArray){
        var state = document.getElementById("states").value;
        if(!state){
            alert("Please Select the state");
                    return false;
        }
        else{
            document.getElementById("states").readOnly = true;
        }
        for(var i=0;i<fieldNameArray.length;i++){
               var value = document.getElementById(fieldNameArray[i]).value;
               if(!value){
                  alert(fieldNameArray[i]+" Should not be blank");
                    return false;
               }
        }
        return true;
    }
    function deleteDetails(rowNumber){
         var all_old_values = document.getElementById("value_holder").value;
         var temp = [];
        temp = all_old_values.split(",,,");
        headingHolder = '<ul class="list-inline ul_holder" style="margin: 0px auto;background: #2c9d9c;border: 0px;">';
        headingHolder = headingHolder+'<li class="list-inline-item view_container" ><b>District</b></li>';
        headingHolder = headingHolder+'<li class="list-inline-item view_container" ><b>Taluk</b></li>';
        headingHolder = headingHolder+'<li class="list-inline-item view_container" ><b>Region</b></li>';
        headingHolder = headingHolder+'<li class="list-inline-item view_container"><b>Division</b></li>';
        headingHolder = headingHolder+'<li class="list-inline-item view_container" ><b>Area</b></li>';
        headingHolder = headingHolder+' <li class="list-inline-item view_container" style="margin-left:-4px;"><b>Cancel</b></li>';
        headingHolder = headingHolder+'</ul>';
        headingHolder = headingHolder+'<div style="clear:both"></div>';
        document.getElementById("view_display_holder").innerHTML=headingHolder;
        document.getElementById("value_holder").value='';
        document.getElementById("last_value_holder").innerHTML='';
        for(var m=0;m<(temp.length-1);m++){
            if(m != (rowNumber-1)){
           document.getElementById("value_holder").value = document.getElementById("value_holder").value+temp[m]+",,,";
           document.getElementById("last_value_holder").innerHTML = temp[m];
           createView();
       }
         }
     }
    function createView(){
      var old_values = document.getElementById("last_value_holder").innerHTML;
      var ul_count = document.getElementsByClassName("ul_holder").length;
      viewArray = old_values.split(",");
      var viewTemp='';
      viewTemp = '<ul class="list-inline ul_holder" style="">';
      viewTemp = viewTemp+ '<li class="list-inline-item view_container" style="width: 181px;">'+viewArray[0]+'</li>';
      viewTemp = viewTemp+ '<li class="list-inline-item view_container" style="width: 181px;">'+viewArray[1]+'</li>';
      viewTemp = viewTemp+ '<li class="list-inline-item view_container" style="width: 181px;">'+viewArray[2]+'</li>';
      viewTemp = viewTemp+ ' <li class="list-inline-item view_container" style="margin-left: -4px;width: 181px;">'+viewArray[3]+'</li>';
      viewTemp = viewTemp+ '<li class="list-inline-item view_container" style="width: 181px;">'+viewArray[4]+'</li>';
      viewTemp = viewTemp+ '<li class="list-inline-item view_container" onclick=deleteDetails('+ul_count+')><span class="glyphicon glyphicon-remove-circle"></span></li>';
viewTemp = viewTemp+'</ul>';
       document.getElementById("view_display_holder").innerHTML = document.getElementById("view_display_holder").innerHTML+viewTemp;
    }
    function setElementValuesArray(fieldNameArray){
        document.getElementById("last_value_holder").innerHTML = "";
        for(var x=0;x<fieldNameArray.length;x++){
               var fieldValue = document.getElementById(fieldNameArray[x]).value;
               document.getElementById("value_holder").value = document.getElementById("value_holder").value+fieldValue+",";
               document.getElementById("last_value_holder").innerHTML = document.getElementById("last_value_holder").innerHTML+fieldValue+",";
        }
        document.getElementById("value_holder").value = document.getElementById("value_holder").value+",,";
    }
    function resetValues(fieldNameArray){
        fieldNameArray = ['area'];
         for(var x=0;x<fieldNameArray.length;x++){
               document.getElementById(fieldNameArray[x]).value='';
        }
    }
    function addNewInputFields(){
        var fieldNameArray = ['district','taluk','region','division','area'];
        var is_empty = checkNotEmpty(fieldNameArray);
        if(is_empty){
           setElementValuesArray(fieldNameArray); 
           resetValues(fieldNameArray);
           createView();
        }
    }
 
    function savePincode(){
        var state = document.getElementById("states").value;
        if(!state){
            alert("Please Select the state");
             return false;
        }
        var fieldNameArray = ['district','taluk','region','division','area'];
            var is_empty = checkNotEmpty(fieldNameArray);
            if(is_empty){
                 for(var x=0;x<fieldNameArray.length;x++){
                        var fieldValue = document.getElementById(fieldNameArray[x]).value;
                        document.getElementById("value_holder").value = document.getElementById("value_holder").value+fieldValue+",";
                        document.getElementById("last_value_holder").innerHTML = document.getElementById("last_value_holder").innerHTML+fieldValue+",";
                     }
                }
                else{
                    return false;
                }
      document.getElementById("pincode_form").submit();
    }
    </script>