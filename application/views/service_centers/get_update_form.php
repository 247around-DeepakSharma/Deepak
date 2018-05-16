<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-header">
                    Update Booking
                </h2>
                <?php if(validation_errors()) { ?>
                    <div class=" alert alert-danger">
                        <?php echo validation_errors(); ?>
                    </div>
                <?php } ?>
                
                <form class="form-horizontal" name="myForm" action="<?php echo base_url() ?>employee/service_centers/process_update_booking" method="POST" onSubmit="document.getElementById('submitform').disabled=true;" enctype="multipart/form-data">
                    <div class="col-md-12" style="margin-left:-31px;">
                          <div class="col-md-3">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_id'])) {echo $bookinghistory[0]['booking_id']; }?>"  disabled>
                       
                      </div>
                         <div class="col-md-3">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['name'])) {echo $bookinghistory[0]['name']; }?>"  disabled>
                       
                      </div>
                         <div class="col-md-3">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_primary_contact_no'])) {echo $bookinghistory[0]['booking_primary_contact_no']; }?>"  disabled>
                       
                      </div>
                    </div>
                   
                    
                    
                    <input type="hidden" class="form-control"  name="booking_id" value = "<?php echo $booking_id;
                        ?>">
                    <input type="hidden" class="form-control"  name="partner_id" value = "<?php if (isset($bookinghistory[0]['partner_id'])) {echo $bookinghistory[0]['partner_id']; }?>">
                    <input type="hidden" class="form-control"  name="price_tags" value = "<?php if (isset($price_tags)) {echo $price_tags; }?>">
                    <input type="hidden" class="form-control" id="partner_flag" name="partner_flag" value="0" />
                    <input type="hidden" name="spare_shipped" value="<?php echo $spare_shipped; ?>" />
                    <div class="form-group ">
                        <label for="reason" class="col-md-2" style="margin-top:39px;">Reason</label>
                        <div class="col-md-6" style="margin-top:39px;">
                            <?php  ?>
                            <?php foreach ($internal_status as $key => $data1) { ?>
                            <div class="radio ">
                                <label>
                                <input type="radio"  name="reason" id= "<?php echo "reason_id" . $key; ?>" onclick="internal_status_check(this.id)" class="internal_status" value="<?php echo $data1['status']; ?>" >
                                <?php echo $data1['status']; ?>
                                </label>
                            </div>
                             <?php } ?>
                            
                            <?php if($spare_flag != SPARE_PART_RADIO_BUTTON_NOT_REQUIRED ){ ?>
                                <div class="radio ">
                                <label>
                                <input type="radio" id="spare_parts" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo $spare_flag;?>" >
                                <?php echo $spare_flag;?>
                                </label>
                            </div>
                           <?php }?>
                            <hr/>
                            <?php if($bookinghistory[0]['is_upcountry'] == 1 ){ ?>
                               <div class="radio ">
                                <label>
                                <input type="radio" id="reschedule_for_upcountry" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo RESCHEDULE_FOR_UPCOUNTRY; ?>" >
                                <?php echo RESCHEDULE_FOR_UPCOUNTRY. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                           <?php }?>
                            <div class="radio ">
                                <label>
                                <input type="radio" id="rescheduled" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo CUSTOMER_ASK_TO_RESCHEDULE; ?>" >
                                <?php echo CUSTOMER_ASK_TO_RESCHEDULE. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                            <div class="radio ">
                                <label>
                                <input type="radio" id="product_not_delivered" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER; ?>" >
                                <?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER . " - Reschedule"; ?>
                                </label>
                            </div>
                            <?php if(!empty($spare_shipped_flag)){ ?>
                                <div class="radio ">
                                <label>
                                <input type="radio" id="spare_not_delivered" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="<?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF; ?>" >
                                <?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF. "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Reschedule"; ?>
                                </label>
                            </div>
                            <?php } ?>
                           
                           
                        </div>
                    </div>
                   
                    <input type="hidden" name="days" value="<?php echo $days; ?>" /> 
                    <div class="panel panel-default col-md-offset-2" id="hide_spare" >
                        <div class="panel-body" >
                            <div class="row">
                                <div class = 'col-md-6'>
                                    <div class="form-group">
                                        <label for="model_number" class="col-md-4">Model Number *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts" id="model_number" name="model_number">
                                                    <option value="" disabled="" selected="">Select Model Number</option>
                                                    <?php foreach (array_column($inventory_details, 'model_number') as $key => $value) { ?> 
                                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } else { ?> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="model_number" name="model_number" value = "" placeholder="Model Number">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class = 'col-md-6'>
                                    <div class="form-group">
                                        <label for="parts_type" class="col-md-4">Parts Type *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts" id="parts_type" name="parts_type">
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>
                                        <?php } else { ?> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="parts_type" name="parts_type" value = "" placeholder="Parts Type" >
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="parts_name" class="col-md-2">Parts Name *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-9">
                                                <select class="form-control spare_parts" id="parts_name" name="parts_name">
                                                    <option selected disabled>Select Part Name</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>
                                        <?php } else { ?> 
                                            <div class="col-md-9">
                                                <input type="text" class="form-control spare_parts" id="parts_name" name="parts_name" value = "" placeholder="Parts Name" >
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number" class="col-md-4">Serial Number *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control spare_parts" id="serial_number" name="serial_number" value="<?php echo set_value('serial_number'); ?>" placeholder="Serial Number">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" >
                                        <label for="dop" class="col-md-4">Date of Purchase *</label>
                                        <div class="col-md-6">
                                            <div class="input-group input-append date">
                                                <input id="dop" class="form-control" placeholder="Select Date" name="dop" type="text" >
                                                <span class="input-group-addon add-on" onclick="dop_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="defective_parts_pic" class="col-md-4">Defective Front Part Pic *</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="defective_parts_pic" name="defective_parts_pic" >
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="defective_parts_pic" class="col-md-4">Defective Back Part Pic *</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="defective_back_parts_pic" name="defective_back_parts_pic" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number_pic" class="col-md-4">Serial Number Picture *</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="serial_number_pic" name="serial_number_pic">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice_pic" class="col-md-4">Invoice Picture</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="invoice_pic" name="invoice_image">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="prob_desc" class="col-md-3">Problem Description* </label>
                                        <div class="col-md-9" style="margin-left: 8px;">
                                            <textarea class="form-control spare_parts"  id="prob_desc" name="reason_text" rows="5" placeholder="Problem Description" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-warning"> <span class="badge badge-info"><i class="fa fa-info"></i></span> * These fields are required</div>
                                <?php  //} if($around_flag) { ?>
                                <!--                                 <div class="col-md-12">
                                    <div class="col-md-6">
                                         <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Parts Name *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="247parts_name" name="parts_name" value = "" placeholder="Parts Name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Model Number </label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="247model_number" name="model_number" value = "" placeholder="Model Number">
                                            </div>
                                        </div>
                                       
                                    </div>
                                     <div class="col-md-6">
                                          <div class="form-group" >
                                            <label for="reschdeduled" class="col-md-4">Rescheduled Date*</label>
                                            <div class="col-md-6">
                                                <div class="input-group input-append date">
                                                    <input id="reschduled_booking_date" class="form-control" placeholder="Select Date" name="booking_date" type="text" required readonly='true' style="background-color:#fff;">
                                                    <span class="input-group-addon add-on" onclick="reschduled_booking_date_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                <span style="color:red; font-size: 12px;">Date By which SF will receive the part</span>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label for="Serial Number" class="col-md-4">Serial Number </label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="247serial_number" name="serial_number" value = "" placeholder="Serial Number">
                                            </div>
                                        </div>
                                         
                                     </div>
                                      <div class="form-group col-md-12 ">
                                                <label for="reason" class="col-md-2">Problem Description*</label>
                                                <div class="col-md-9" style="width:78%;">
                                                    <textarea class="form-control spare_parts"  id="247reason_text" name="reason_text" value = "" rows="3" placeholder="Problem Description" ></textarea>
                                                </div>
                                           </div>
                                    </div>-->
                                <?php //}?>
                            </div>
                        </div>
                    <div  id="hide_rescheduled" >
                        <div class="form-group">
                            <label for="reschdeduled" class="col-md-2"> New Booking Date</label>
                            <div class="col-md-4" style="width:24%">
                                <div class="input-group input-append date">
                                    <input id="booking_date" class="form-control rescheduled_form" placeholder="Select Date" name="booking_date" type="text" required readonly='true' style="background-color:#fff;">
                                    <span class="input-group-addon add-on" onclick="booking_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="hide_remarks">
                        <label for="remarks" class="col-md-2">Remarks </label>
                        <div class="col-md-4" style="width:24%">
                            <textarea class="form-control remarks"  id="sc_remarks" name="sc_remarks" value = "" required placeholder="Enter Remarks" rows="5" ></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 col-md-offset-2">
                        <input type="submit"  value="Update Booking" id="submitform" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return submitForm();"   class="btn btn-danger btn-large">
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    
    <?php if(isset($inventory_details) && !empty($inventory_details)) { ?> 
        
        $('#model_number').select2();
        $('#parts_name').select2({
            placeholder: "Select Part Name",
            allowClear:true
        });
        $('#parts_type').select2({
            placeholder: "Select Part Type",
            allowClear:true
        });
        
        $('#model_number').on('change', function() {
        
            var model_number = $('#model_number').val();
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number){
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
                    data: { model_number:model_number, entity_id: '<?php echo $bookinghistory[0]['partner_id']?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $bookinghistory[0]['service_id']; ?>' },
                    success:function(data){
                        $('#parts_type').val('val', "");
                        $('#parts_type').val('Select Part Type').change();
                        $('#parts_type').select2().html(data);
                        $('#spinner').removeClass('fa fa-spinner').hide();
                    }
                });
            }else{
                alert("Please Select Model Number");
            }
        });
        
        $('#parts_type').on('change', function() {
        
            var model_number = $('#model_number').val();
            var part_type = $('#parts_type').val();
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number){
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_name',
                    data: { model_number:model_number, entity_id: '<?php echo $bookinghistory[0]['partner_id']?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $bookinghistory[0]['service_id']; ?>', part_type:part_type },
                    success:function(data){
                        $('#parts_name').val('val', "");
                        $('#parts_name').val('Select Part Name').change();
                        $('#parts_name').select2().html(data);
                        $('#spinner').removeClass('fa fa-spinner').hide();
                    }
                });
            }else{
                alert("Please Select Model Number");
            }
        });
        
    <?php } ?>

    $(document).ready(function (){
       $(".spare_parts").attr("disabled", "true");
    });
    
    function submitForm(){
       
     var checkbox_value = 0;
     $("input[type=radio]:checked").each(function(i) {
         checkbox_value = 1;
    
     });
     
     if(checkbox_value ===0){
     	  alert('Please select atleast one checkbox.');
     	  checkbox_value = 0;
     }
     
      var reason = $("input[name='reason']:checked"). val();
      if(reason === "<?php echo CUSTOMER_ASK_TO_RESCHEDULE; ?>" 
              || reason === "<?php echo PRODUCT_NOT_DELIVERED_TO_CUSTOMER; ?>" 
              || reason === "<?php echo RESCHEDULE_FOR_UPCOUNTRY; ?>"
              || reason === "<?php echo SPARE_PARTS_NOT_DELIVERED_TO_SF;?>"){
          
          var booking_date = $('#booking_date').val();
          if(booking_date === ""){
              alert("Please select new date");
              checkbox_value = 0;
          }
         
      } else if(reason === "Spare Parts Required"){
          var around_flag = $('#partner_flag').val();
          
          if(around_flag === '0'){
              var model_number = $('#model_number').val();
              var serial_number = $("#serial_number").val();
              var prob_des = $("#prob_desc").val();
              var parts_name = $("#parts_name").val();
              var dop = $("#dop").val();
              var parts_type = $('#parts_type').val();
              var serial_number_pic = $('#serial_number_pic').val();
              var defective_parts_pic = $('#defective_parts_pic').val();
              var defective_back_parts_pic = $('#defective_back_parts_pic').val();
              
              if(model_number ==="" || model_number === null){
                  alert("Please enter model number");
                  checkbox_value =0;
                  return false;
              }
              
              if(parts_type === "" || parts_type === null){
                   alert("Please Enter parts Type");
                  checkbox_value = 0;
                  return false;
              }
              
              if(parts_name === "" || parts_name === null){
                   alert("Please Enter parts name");
                  checkbox_value = 0;
                  return false;
              }
              
              
              
              if(serial_number === "" || serial_number === null){
                   alert("Please Enter serial number");
                  checkbox_value = 0;
                  return false;
              }
              
              if(dop === ""){
                  alert("Please Select Date of Purchase");
                  checkbox_value = 0; 
                  return false;
              }
              
              
              
              if(defective_parts_pic.length === 0){
                  alert("Please Upload Back Front Defective Front Parts Image");
                  checkbox_value = 0; 
                  return false;
              }
              
               if(defective_back_parts_pic.length === 0){
                  alert("Please Upload Back Defective Back Parts Image");
                  checkbox_value = 0; 
                  return false;
              }
              
              if(serial_number_pic.length === 0){
                  alert("Please Upload Serial Number Image");
                  checkbox_value = 0; 
                  return false;
              }
              
              if(prob_des === "" || prob_des === null){
                   alert("Please Enter problem description");
                  checkbox_value = 0;
                  return false;
              }
              
          } else if(around_flag === '1'){
              var parts_name1 = $('#247parts_name').val();
              var reschduled_booking_date = $("#reschduled_booking_date").val();
              var reason_text = $("#247reason_text").val();

              if(parts_name1 === ""){
                   alert("Please Enter parts name");
                  checkbox_value = 0;
                  return false;
              }
              
               if(reschduled_booking_date === ""){
                  alert("Please select reschedule date");
                  checkbox_value = 0; 
                  return false;
              }
              
          }
      }

      if(checkbox_value === 0){
          $('#submitform').val("Update Booking");
          return false;
          
      } else if(checkbox_value === 1){
          
          $('#submitform').val("Please wait.....");
          return true;
          
      }
 
    
    }
    
    function internal_status_check(id){
        if(id ==="spare_parts"){
            $('#hide_spare').show();
            $(".spare_parts").removeAttr("disabled");
            $(".rescheduled_form").attr("disabled", "true");
            $('#hide_rescheduled').hide();
            $(".remarks").attr("disabled", "true");
            $('#hide_remarks').hide();
          
        } else  if(id ==="rescheduled" || id === "product_not_delivered" 
                || id=== "reschedule_for_upcountry"
                || id=== "spare_not_delivered"){
            $(".spare_parts").attr("disabled", "true");
            $('#hide_spare').hide();
            $('#hide_rescheduled').show();
            $(".rescheduled_form").removeAttr("disabled");
            $('#hide_remarks').show();
            $(".remarks").removeAttr("disabled");
    
       }  else {
         $(".spare_parts").attr("disabled", "true");
         $(".rescheduled_form").attr("disabled", "true");
         $('#hide_spare').hide();
         $('#hide_rescheduled').hide();
         $('#hide_remarks').show();
         $(".remarks").removeAttr("disabled");
       }
    }
    
    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: true,changeYear: true});
    $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
    $("#reschduled_booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: 0, 
                maxDate:+7
    });
    
    function booking_calendar(){
      
        $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: true,changeYear: true}).datepicker('show');
    }
    
    function dop_calendar(){
         $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true}).datepicker('show');
    }
    
    function reschduled_booking_date_calendar(){
        $("#reschduled_booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: 0, 
                maxDate:+7
        }).datepicker('show');
    }
    
</script>
<style type="text/css">
    #hide_spare, #hide_rescheduled { display: none;}
</style>