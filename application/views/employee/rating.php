<div id="page-wrapper">
  <div class="">
    <!--<div class="row">-->
      <!--<div style="width:600px;margin:50px;">-->
         
      <h1 class="page-header" style="padding-left: 42px;">Rating Given by Customer <?php if($data[0]['rating_unreachable_count']>0){ ?><span style="color: red;font-size: 20px;" class="pull-right">
      Unreachable Count : <?php echo $data[0]['rating_unreachable_count']; ?></span> <?php } ?></h1>
      <div class="clear"></div>
        <div class="col-md-12"> 
                <div class="col-md-6">
                  <div class="form-group-cancel">
                      <label for="name" class="col-md-4">Name</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="name" name="user_name" value = "<?php if (isset($data[0]['name'])) {echo $data[0]['name']; } ?>" readonly="readonly">
                        </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group-cancel">
                           <label for="booking_id" class="col-md-4">Booking ID</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_id" name="booking_id" value = "<?php if (isset($data[0]['booking_id'])) {echo $data[0]['booking_id']; } ?>" readonly="readonly">
                           </div>
                        </div>
                </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-6">
                        <div class="form-group-cancel <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                           <label for="service_name" class="col-md-4">Appliance</label>
                           <div class="col-md-6">
                              <input type="hidden" name="service" id="services"/>
                              <select type="text" disabled="disabled"  class="form-control"  id="service_id" name="service_id" required>
                                 <option value="<?php if (isset($data[0]['service_id'])) {echo $data[0]['service_id']; } ?>" selected="selected" disabled="disabled"><?php if (isset($data[0]['services'])) {echo $data[0]['services']; } ?></option>
                              </select>
                           </div>
                        </div>
            </div>

                <div class="col-md-6">

                  <div class="form-group-cancel">
                           <label for="booking_primary_contact_no" class="col-md-4">Mobile</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if (isset($data[0]['booking_primary_contact_no'])) {echo $data[0]['booking_primary_contact_no']; } ?>" readonly="readonly">
                           </div>
                           <?php if($c2c){ ?>
                           <div class="col-md-2">
                              
                                <button type="button" onclick="outbound_call(<?php echo $data[0]['booking_primary_contact_no']; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                            </div>
                           <?php } ?>
                        </div>
                </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-6">
                        <div class="form-group-cancel">
                           <label for="booking_date" class="col-md-4">Booking Timeslot</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_timeslot" name="booking_timeslot" value = "<?php if (isset($data[0]['booking_timeslot'])) {echo $data[0]['booking_timeslot']; } ?>" readonly="readonly">
                           </div>
                        </div>
            </div>
            <div class="col-md-6">
                        <div class="form-group-cancel">
                           <label for="booking_date" class="col-md-4">Booking Date</label>
                           <div class="col-md-6">
                              <input type="text" class="form-control"  id="booking_date" name="booking_date" value = "<?php if (isset($data[0]['booking_date'])) {echo $data[0]['booking_date']; } ?>" readonly="readonly">
                           </div>
                        </div>
                  </div>
        </div>
        &nbsp;<hr>
        <form class="form-horizontal" id ="rating_form" action="<?php echo base_url()?>employee/booking/process_rating_form/<?php echo $data[0]['booking_id'];?>/<?php echo $status; ?>" method="POST">

        <div>
            <input type="hidden" name="user_id" value="<?php echo $data[0]['user_id'];?>">
            <input type="hidden" name="mobile_no" value="<?php echo $data[0]['booking_primary_contact_no']; ?>">
        </div>
            <div class="col-md-12">
        <div class="col-md-6">
            
                <div class="form-group <?php if( form_error('rating_star') ) { echo 'has-error';} ?>">
                    <label for="rating_star" class="col-md-2" style="margin-left: 13px;">Rating</label>
                                <div class="col-md-6">
                                    <Select type="text" class="form-control"  name="rating_star" id="rating_star" value="<?php echo set_value('rating_star'); ?>" style="margin-left: 117px;width: 96%;">
                                    <option value ="">Select</option>
                                    <option>-1</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    </Select>
                                </div>
            </div>
        </div>
            <div class="col-md-4">
                            <div class="form-group" style="margin-left:14px;">
                    <div class="checkbox"> <label><input type="checkbox" id="not_reachable" name="not_reachable"><b>Customer Not Reachable</b></label></div>
            </div>
                </div>
              <div class="col-md-2">
                            <div class="form-group" style="margin-left:14px;">
                                    <div class="checkbox"> <label><input type="checkbox" id="not_send_sms" name="not_send_sms"><b>Do Not Send Sms</b></label></div>
                            </div>
                </div>
            </div>
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="form-group <?php if( form_error('rating_comments') ) { echo 'has-error';} ?>">
                    <label for="rating_comments" class="col-md-2" style="margin-left: 9px; width: 23%;">Customer Feedback</label>
                    <div class="col-md-6">
                        <textarea style="height:80px;width:333px;margin-left: 73px;" class="form-control"  name="rating_comments"></textarea>
                        <?php echo form_error('rating_comments'); ?>
                    </div>
            </div>
                </div>
            <div class="col-md-6" style=" padding-left: 29px;">
                <div style="margin: 26px 0px 0px 0px;"><input type="submit" value="Save Rating" class="btn btn-primary" onclick="return validation(this)"></div>
            </div>
        </div>
    <br>
     
      <div>
        
        </form>
      </div>
      
    </div>
  <!--</div>-->
<!--</div>-->
</div>
<script type="text/javascript">
    
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call == true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

    }
    function validation(){
        var ratingStar = $("#rating_star").val();
        var not_reachable = $("#not_reachable").prop('checked');
        if(ratingStar && not_reachable){
            alert("Either Choose not reachable or add rating, Don't select both option together");
            return false;
        }
        else if(ratingStar || not_reachable){
            return true;
        }
        else{
            alert("Please Select atleast 1 option");
            return false;
        }
    }
    
</script>
