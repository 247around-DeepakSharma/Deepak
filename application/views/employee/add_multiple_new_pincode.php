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
        <?php
    if($this->session->userdata('final_msg')){
        ?>
        <div class="panel-heading"style="text-align:center;font: bold 20px/28px Arial;background: #f1d6d6;"><b><?php   echo $this->session->userdata('final_msg');?></b></div>
        <?php
    }
    ?>
<div class="panel panel-info" style="margin-top:20px;">
    <div class="panel-heading"style="text-align:center;font: bold 20px/28px Arial"><b>Following Pincodes Does'nt Have City States, Please Add City States For every Pincode </b></div>
        <div id="view_display_holder">
        <div style="clear:both"></div>
    </div>

 <div class="panel-body">
     <div style="clear:both"></div>
        	<div class="row">
               <form name="myForm" class="form-inline" id ="pincode_form" action="<?php echo base_url()?>employee/vendor/add_multiple_pincode"  method="POST">
                   <?php
                   foreach($pincodeArray as $index=>$pincodes){
                       ?>
                   <div class="form-group" style="padding-left: 215px;padding-top: 15px;">
                       <input type="email" class="form-control" id="pincode_<?php  echo $index?>" name="pincode_<?php  echo $index?>" placeholder="Pincode" value="<?php echo $pincodes ?>" readonly="">
                       <select type="text" class="form-control multiple_city"  id="city_<?php echo $index ?>" name="city_<?php echo $index ?>[]" required multiple>
                                                 <?php if (isset($city)){?>
                                                         <?php foreach ($city as $key => $values) { ?>
                                                         <option  value="<?php echo $values['district'] ?>" > <?php echo $values['district'];?></option>
                                                         <?php } 
                                                 }?>
                                    </select>
   <select type="text" class="form-control"  id="states_<?php echo $index?>" name="states_<?php echo $index ?>" required>
       <option value="">Select State</option>
                                            <?php if (isset($states)){?>
                                                    <?php foreach ($states as $key => $values) { ?>
                                                    <option  value="<?php echo $values['state'] ?>" > <?php echo $values['state'];?></option>
                                                    <?php } 
                                            }?>
                               </select>
  </div>
                   <?php
                   }
                   ?>
                   <input type="hidden" name="pincode_count" value="<?php echo count($pincodeArray) ?>">
                </form>        
                    <div class="button_container" style="width: 5%;">
                        <input type="button" id="submitform" class="btn btn-info " onclick="savePincode(<?php echo count($pincodeArray) ?>)" value="Save"/>
                    </div>
  </div>
</div>
    	</div>
</div>
</div>
<script type="text/javascript">
$(".multiple_city").select2({
        placeholder: "Select City",
        allowClear: true
    });
    function savePincode(pincodeCount){  
        for(var i=0;i<pincodeCount;i++){
             var  stateValue  = $("#states_"+i).val();
             var  pincode  = $("#pincode_"+i).val();
             var  city  = $("#city_"+i).val();
             if(!(stateValue && pincode && city)){
                 alert("Please Select City and state for every pincode");
                 return false;
          }
        }
        document.getElementById("pincode_form").submit();
    }

    </script>