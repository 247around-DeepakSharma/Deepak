      <div style="width:100%;margin-left:10px;margin-right:5px;">
	  <h2 align="left">Rescheduled Bookings</h2>
          <div class="col-md-12" style="padding-left: 0px;">
	     <form action="<?php echo base_url(); ?>employee/booking/process_review_reschedule_bookings" method="post">
		 <table class="table table-bordered table-hover table-striped">
                  <thead>
                     <tr>
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>Service Center </th>
                        <th>User Name</th>
                        <th>Original Booking Date</th>
                        <th>Booking Date</th>
                        <th>Reschedule Booking Date</th>
                        <th>Reschedule Reason</th>
                        <th  ><input type="checkbox" id="selecctall_reschedule" />  Reschedule</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $sno = 1; foreach ($data as $key => $value) { ?>
                     <tr>
                        <td><?php echo $sno; if($value['is_upcountry'] == 1) { ?>.<i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $value['assigned_vendor_id'];?>','<?php echo $value['booking_id'];?>', '<?php echo $value['amount_due'];?>', '<?php echo $value['flat_upcountry'];?>')" class="fa fa-road" aria-hidden="true"></i><?php } ?></td>
                        <td>
                            <a href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $value['booking_id'];  ?>" target="_blank"><?php echo $value['booking_id'];  ?></a>
                        </td>
                        <td><?php echo $value['service_center_name'];  ?></td>
                        <td>
                            <?php echo $value['customername'];  ?>
                            <br/>
                            <?php if($c2c) { ?>
                            <a href="javascipt:void(0);" onclick="outbound_call(<?php echo $value['booking_primary_contact_no'] ?>)"><?php echo $value['booking_primary_contact_no'];  ?></a>
                            <?php } else { ?>
                                <?php echo $value['booking_primary_contact_no'] ?>
                          <?php  }?>
                        </td>
                       
                        <td><?php echo $value['initial_booking_date'];  ?></td>
                        <td><?php echo $value['booking_date']." / ".$value['booking_timeslot'] ;  ?></td>
                        <td><?php echo  date('d-m-Y',strtotime($value['reschedule_date_request'])) ; ?>
                        <div class="<?php echo (($value['count_reschedule'] != 0)?'blink':''); ?>">
                           <div class="<?php echo (($value['count_reschedule'] != 0)?'esclate':''); ?>"><?php echo '<b>' . $value['count_reschedule'] . " times</b><br>";?></div>
                                
                        </div>
                           
                            
                        
                           <input type="hidden" name="reschedule_booking_date[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_date_request'] ?>" ></input>
<!--                           <input type="hidden" name="reschedule_booking_timeslot[<?php //echo $value['booking_id']; ?>]" value="<?php //echo $value['reschedule_timeslot_request'] ?>" ></input>-->
                           <input type="hidden" name="reschedule_reason[<?php echo $value['booking_id']; ?>]" value="<?php echo $value['reschedule_reason'] ?>" ></input>
                        </td>
                        <td><?php echo $value['reschedule_reason'];  ?></td>
                        <td><input id="reschedule_checkbox" type="checkbox"  class="checkbox_reschedule" name="reschedule[]" value="<?php echo $value['booking_id']; ?>"></input>
                            <a href="#"><span style="float: right;" class="glyphicon glyphicon-remove" data-toggle="modal" data-target="#review_reject_form" onclick="create_reject_form(<?php echo "'".$value['booking_primary_contact_no']."'";  ?>,<?php echo "'".$value['booking_id']."'";  ?>)"></span> </a>
                        </td>
                        
                        <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo $value['partner_id']; ?>" >
                     </tr>
                     <?php $sno++;  } ?>
                  </tbody>
               </table>
               <?php if(!empty($data)){?>
               <div class"col-md-12">
                  <center><input onclick="check_limit_reschedule()" type="submit" value="Approve Rescheduled"  style=" background-color: #2C9D9C;
                     border-color: #2C9D9C;" class="btn btn-md btn-success"></input></center>
               </div>
               <?php } ?>
            </form>
         </div>
      </div>


    <!-- Modals-->
    <div id="review_reject_form" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center">Fake Reschedule</h4>
      </div>
      <div class="modal-body">
<form>
 <div class="form-group">
     <input type="hidden" value="" name="p_number" id="p_number">
     <input type="hidden" value="" name="b_id" id="b_id">
     <input type="hidden" value="<?php echo $this->session->userdata('employee_id'); ?>" name="employee_id" id="employee_id">
     <input type="hidden" value="<?php echo $this->session->userdata('id'); ?>"  name="id" id="id">
  <label for="comment">Remarks:</label>
  <textarea class="form-control" rows="5" id="remarks"></textarea>
</div>
 <button type="submit" align="center" class="btn btn-success" onclick="return cancel_reschedule_request()">Cancel Reschedule</button>
  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
    $(document).ready(function(){
        $("#selecctall_reschedule").change(function(){
           $(".checkbox_reschedule").prop('checked', $(this).prop("checked"));
        });
    });
    
    function create_reject_form(p_number,booking_id){
        $("#p_number").val(p_number);
        $("#b_id").val(booking_id);
    }
        function cancel_reschedule_request(){
        var p_number  = $("#p_number").val();
        var booking_id  = $("#b_id").val();
        var remarks  = $("#remarks").val();
        var employeeID = $("#employee_id").val();
        var id = $("#id").val();
        var url =  '<?php echo base_url();?>employee/booking/cancel_rescheduled_booking';
         $.ajax({
            type: 'POST',
            url: url,
            data: {p_number: p_number, remarks: remarks, employeeID: employeeID, id: id,booking_id:booking_id},
            success: function (response) {
                console.log(response);
                if(response == true){
                    alert("Reschedule Cancelled Successfully");
                }
                else{
                    alert("Something Went Wrong Please Try Again");
                }
                location.reload();
            }
        });
        return false;
    }
       function open_upcountry_model(sc_id, booking_id, amount_due, flat_upcountry){
      
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due + "/"+flat_upcountry,
      success: function (data) {
       $("#open_model1").html(data); 
      
       $('#myModal1').modal('toggle');
    
      }
    });
    }
        function check_limit_reschedule(){
      var count = 0;
      $("#reschedule_checkbox:checked").each(function(i) {
         count = count+1;
      });
     
      if(count >40){
         alert("Maximum 40 bookings can be rescheduled in one time.");
         return false;
      } else if(count ===0){
         alert("Please select at least one booking to reschedule.");
         return false;
      } else {
          return true;
      }
   }
    </script>