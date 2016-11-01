<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-header">
                    Update Booking
                </h2>
                <form class="form-horizontal" name="myForm" action="<?php echo base_url() ?>employee/service_centers/process_update_booking" method="POST" onsubmit="return submitForm();" enctype="multipart/form-data">
                    

                    <div class="form-group ">
                      <label for="booking_id" class="col-md-2">Booking ID</label>
                      <div class="col-md-4">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_id'])) {echo $bookinghistory[0]['booking_id']; }?>"  disabled>
                       
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="name" class="col-md-2">Name</label>
                      <div class="col-md-4">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['name'])) {echo $bookinghistory[0]['name']; }?>"  disabled>
                       
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="mobile" class="col-md-2">Mobile</label>
                      <div class="col-md-4">
                        <input type="text" class="form-control"   value = "<?php if (isset($bookinghistory[0]['booking_primary_contact_no'])) {echo $bookinghistory[0]['booking_primary_contact_no']; }?>"  disabled>
                       
                      </div>
                    </div>
                    <input type="hidden" class="form-control"  name="booking_id" value = "<?php echo $booking_id;
                        ?>">
                    <input type="hidden" class="form-control"  name="partner_id" value = "<?php if (isset($bookinghistory[0]['partner_id'])) {echo $bookinghistory[0]['partner_id']; }?>">
                    <input type="hidden" class="form-control" id="partner_flag" name="partner_flag" value="<?php echo $around_flag ?>" />
                    <div class="form-group ">
                        <label for="reason" class="col-md-2">Reason</label>
                        <div class="col-md-6">
                            <?php  ?>
                            
                            <?php foreach ($internal_status as $key => $data1) { ?>
                            <div class="radio ">
                                <label>
                                <input type="radio"  name="reason" id= "<?php echo "reason_id" . $key; ?>" onclick="internal_status_check(this.id)" class="internal_status" value="<?php echo $data1->status; ?>" >
                                <?php echo $data1->status; ?>
                                </label>
                            </div>
                             <?php } ?>
                            <div class="radio ">
                                <label>
                                <input type="radio" id="rescheduled" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="Rescheduled" >
                                Rescheduled
                                </label>
                            </div>
                            <?php if ($spare_flag == 1) { ?>
                            <div class="radio ">
                                <label>
                                <input type="radio" id="spare_parts" onclick="internal_status_check(this.id)" name="reason" class="internal_status" value="Spare Parts Required" >
                                Spare Parts required
                                </label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel panel-default col-md-offset-2" id="hide_spare" >
                        <div class="panel-body" >
                            <div class="row">
                                <?php if($around_flag == 0){ ?>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Model Number *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="model_number" name="model_number" value = "" placeholder="Model Number">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Model Number" class="col-md-4">Parts Name *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="parts_name" name="parts_name" value = "" placeholder="Parts Name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Invoice picture" class="col-md-4">Invoice Image</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control spare_parts" id="invoice_pic" name="invoice_image">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Serial Number" class="col-md-4">Serial Number *</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts" id="serial_number" name="serial_number" value = "" placeholder="Serial Number">
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label for="reschdeduled" class="col-md-4">Date of Purchase</label>
                                            <div class="col-md-6">
                                                <div class="input-group input-append date">
                                                    <input id="dop" class="form-control" placeholder="Select Date" name="dop" type="text" required readonly='true' style="background-color:#fff;">
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Invoice picture" class="col-md-4">Panel Picture</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control spare_parts" id="panel_pic" name="panel_pic" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                                <label for="reason" class="col-md-2">Problem Description* </label>
                                               <div class="col-md-9" style="width:78%; ">
                                                    <textarea class="form-control spare_parts"  id="prob_desc" name="reason_text" value = "" rows="3" placeholder="Problem Description" ></textarea>
                                                </div>
                                           </div>
                                </div>
                                <?php } else if($around_flag == 1){ ?>
                                 <div class="col-md-12">
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
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
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
                                 </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div  id="hide_rescheduled" >
                    <div class="form-group">
                        <label for="reschdeduled" class="col-md-2"> New Booking Date</label>
                        <div class="col-md-4" style="width:24%">
                            <div class="input-group input-append date">
                                <input id="booking_date" class="form-control rescheduled_form" placeholder="Select Date" name="booking_date" type="text" required readonly='true' style="background-color:#fff;">
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        
                    </div>
                     <div class="form-group ">
                            <label for="reason" class="col-md-2"> Rescheduled Reason</label>
                            <div class="col-md-4" >
                                    <textarea class="form-control rescheduled_form"  id="rescheduled_text" name="reason_text" value = "" rows="3" placeholder="Rescheduled Reason" ></textarea>
                            </div>
                        </div>
            </div>
                   
                    <div class="col-md-6 col-md-offset-4">
                        <input type="submit"  value="Update Booking" style="background-color: #2C9D9C; border-color: #2C9D9C; "  class="btn btn-danger btn-large">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function (){
       $(".spare_parts").attr("disabled", "true");
    });
    
    function submitForm(){
     var checkbox_value = 0;
     $("input[type=radio]:checked").each(function(i) {
         checkbox_value = 1;
    
     });
     
     if(reason_text ==="" && checkbox_value ===0){
     	  alert('Please select atleast one checkbox or type reason');
     	  checkbox_value = 0;
     }
     
      var reason = $("input[name='reason']:checked"). val();
      if(reason === "Rescheduled"){
          var res_text = $("#rescheduled_text").val();
          var booking_date = $('#booking_date').val();
          if(booking_date === ""){
              alert("Please Select Date");
              checkbox_value = 0;
          }
          if(res_text ===""){
              alert("Please Enter Rescheduled Reason in the Comment");
              checkbox_value = 0;
          }
      } else if(reason === "Spare Parts required"){
          var around_flag = $('#partner_flag').val();
         
          if(around_flag === '0'){
              var model_number = $('#model_number').val();
              var serial_number = $("#serial_number").val();
              var prob_des = $("#prob_desc").val();
              var parts_name = $("#parts_name").val();
              var dop = $("#dop").val();
              if(parts_name === ""){
                   alert("Please Enter Parts Number");
                  checkbox_value = 0;
                  return false;
              }
              if(model_number ===""){
                  alert("Please Enter Model Number");
                  checkbox_value =0;
                  return false;
              }
              if(serial_number === ""){
                   alert("Please Enter Serial Number");
                  checkbox_value = 0;
                  return false;
              }
              if(prob_des === ""){
                   alert("Please Enter Problem Description");
                  checkbox_value = 0;
                  return false;
              }
              
              if(dop === ""){
                  alert("Please Select Date of Purchase");
                  checkbox_value = 0; 
                  return false;
              }
          } else if(around_flag === '1'){
              var parts_name1 = $('#247parts_name').val();
              var reschduled_booking_date = $("#reschduled_booking_date").val();
              var reason_text = $("#247reason_text").val();

              if(parts_name1 === ""){
                   alert("Please Enter Parts Name");
                  checkbox_value = 0;
                  return false;
              }
              
               if(reschduled_booking_date === ""){
                  alert("Please Select Reschedule Date");
                  checkbox_value = 0; 
                  return false;
              }
              if(reason_text === ""){
                  alert("Please Enter Problem Description");
                  checkbox_value = 0; 
                  return false;
              }
              
          }
      }
           
     
      if(checkbox_value === 0){
          return false;
          
      } else if(checkbox_value === 1){
          return true;
          
      }
 
    
    }
    
    function internal_status_check(id){
        if(id ==="spare_parts"){
            $('#hide_spare').show();
            $(".spare_parts").removeAttr("disabled");
            $(".rescheduled_form").attr("disabled", "true");
            $('#hide_rescheduled').hide();
          
        } else  if(id ==="rescheduled"){
            $(".spare_parts").attr("disabled", "true");
            $('#hide_spare').hide();
            $('#hide_rescheduled').show();
            $(".rescheduled_form").removeAttr("disabled");
    
       } else {
         $(".spare_parts").attr("disabled", "true");
         $(".rescheduled_form").attr("disabled", "true");
         $('#hide_spare').hide();
         $('#hide_rescheduled').hide();
       }
    }
    
    $("#booking_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0, changeMonth: true,changeYear: true});
    $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
    $("#reschduled_booking_date").datepicker({
                dateFormat: 'yy-mm-dd', 
                minDate: 0, 
                maxDate:+7
    });
    
    
     
</script>
<style type="text/css">
    #hide_spare, #hide_rescheduled { display: none;}
</style>