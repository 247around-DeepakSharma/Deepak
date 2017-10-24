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
        width: 557px;
        text-align: center; 
    }
    .ul_holder{
         background: #d9edf7;
         border: 1px solid #70adac;
    margin: 10px;
    }
    #view_display_holder{
    margin: 10px;
    padding: 0px;
    }
</style>
<div id="page-wrapper" >
<div class="container" >
<div class="panel panel-info" style="margin-top:20px;">
    <div class="panel-heading"style="text-align:center;font: bold 20px/28px Arial"><b>Before Assigning the SF to Pincode, Please enter Pincode details</b></div>
        <div id="view_display_holder">
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
                               <input type="text" class="form-control" id="pincode" name="pincode" <?php if (isset($pincode)) { echo "value =".$pincode." readonly='' required";   } ?>>
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
                           <label for="district" class="col-md-4">District/City*</label>
                           <div class="col-md-6">
                               <input type="text" class="form-control" id="district" name="district" value = "">
                                <input style='float:right;margin-top: 16px;' type="button" id="submitform" class="btn btn-info " onclick="addNewInputFields()" value="Add More District/City"/>
                           </div>
                        </div>
                       </div>
                   </div>
               </form>
                    <div class="button_container">
                        <input type="button" id="submitform" class="btn btn-info " onclick="savePincode()" value="Save"/>
                    </div>
                        
  </div>
</div>
    	</div>
</div>
</div>
<script type="text/javascript">
    var district = [];
    function isEmpty(id){
        var value = document.getElementById(id).value;
        if(!value){
            alert(id+" Should Not be blank");
            return false;
        }
        return value;
    }
     function cancel(index){
            district.splice(index, 1);
            createView();
    }
    function createView(){
        document.getElementById("view_display_holder").style.border = "1px solid #2c9d9c";
        var view = '<ul class="list-inline ul_holder" style="margin: 0px auto;background: #2c9d9c;border: 0px;">';
        var view = view+'<li class="list-inline-item view_container" ><b>City/District</b></li>';
        var view = view+'<li class="list-inline-item view_container" style="margin-left: -3.6px;"><b>Cancel</b></li>';
        var view = view+'</ul>';
        var length = district.length;
        for(var i = 0;i<length;i++){
            var view = view+'<ul class="list-inline ul_holder" style="margin: 0px auto;border: 0px;">';
            view = view+'<li class="list-inline-item view_container" >'+district[i]+'</li>';
            view = view+'<li class="list-inline-item view_container" onclick="cancel('+i+')">Cancel</li>';
            view = view+'</ul>';
            view = view+'<hr style="margin: 0px;">';
        }
        document.getElementById("view_display_holder").innerHTML = view;
    }
    function addNewInputFields(){
             districtValue = isEmpty("district");
             if(districtValue){
                    var length = district.length;
                    district[length] = document.getElementById("district").value;
                    document.getElementById("district").value = '';
                    document.getElementById("states").disabled = true;
                    createView();
        } 
    }
    function savePincode(){  
        var  stateValue  = isEmpty("states");
        var  pincode  = isEmpty("pincode");
        if(stateValue && pincode){
            var length = district.length;
             if(length>0){
                document.getElementById("district").value = district.toString();
                document.getElementById("states").disabled = false;
                document.getElementById("pincode_form").submit();
             }
             else{
                 alert("Please Add atleast 1 District/City");
             }
    }
    }
    </script>