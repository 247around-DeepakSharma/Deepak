<!--Cancel Modal-->
<div id="cancelmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/booking/cancel_missed_calls_lead" method="POST">
          
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-align: center"><b>Cancel Reason</b></h4>
          </div>
          <div class="modal-body">
              <span id="error_message" style="display:none;color: red;margin-bottom:10px;"><b>Please Select Reason</b></span>
              <ul style="list-style-type: none;">
              <?php foreach($cancellation_reason as $value){?>
                  <li>
                      <input type="radio" name="cancellation_reason" value="<?php echo $value['reason']?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value['reason']?>
                  </li> 
              <?php }?>
              </ul>
              <input type="hidden" name="id" id="leads_id" value="" >
          </div>
          <div class="modal-footer">
             <input type="button" onclick="form_submit()" value="Submit" class="btn btn-info " form="modal-form">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
          
      </form>
  </div>
</div>

<!--Update Modal-->

<div id="updatemodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <form name="update_form" id="update_form" class="form-horizontal" action="<?php echo base_url() ?>employee/booking/update_missed_calls_lead" method="POST">
          
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-align: center"><b>Update Reason</b></h4>
          </div>
          <div class="modal-body">
              <span id="error_message_update" style="display:none;color: red;margin-bottom:10px;"><b>Please Select Reason</b></span>
              <ul style="list-style-type: none;">
              <?php foreach($updation_reason as $value){?>
                  <li>
                      <input type="radio" name="updation_reason" value="<?php echo $value['reason']?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value['reason']?>
                  </li> 
              <?php }?>
              </ul>
              <input type="hidden" name="id" id="leads_id_update" value="" >
          </div>
          <div class="modal-footer">
             <input type="button" onclick="form_submit_update()" value="Submit" class="btn btn-info " form="modal-form">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
          
      </form>
  </div>
</div>

<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;font-weight: bold">Missed Calls Lead</center></div>
        
        <div class="alert alert-danger alert-dismissible" id="add_error" role="alert" style="margin-top:10px;width:25%;margin-left:1%;margin-bottom:-10px;display:none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Error in Adding Bookings</strong>
        </div>
         <?php
                    if ($this->session->flashdata('cancel_leads')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;width:25%;margin-left:1%;margin-bottom:-10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('cancel_leads').'</strong>
                    </div>';
                    }
                    ?>
         <?php
                    if ($this->session->flashdata('update_leads')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;width:25%;margin-left:1%;margin-bottom:-10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('update_leads').'</strong>
                    </div>';
                    }
                    ?>
        
        <div class="panel-body">
            <table class="table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="jumbotron" style="text-align: center">S.N.</th>
                        <th class="jumbotron" style="text-align: center">PHONE</th>
                        <th class="jumbotron" style="text-align: center">ACTION DATE</th>
                        <th class="jumbotron" style="text-align: center">CALL COUNTER</th>
                        <th class="jumbotron" style="text-align: center">LAST CALL DATE</th>
                        <th class="jumbotron" style="text-align: center">UPDATE REASON</th>
                        <th class="jumbotron" style="width:5%;text-align: center">BOOK</th>
                        <th class="jumbotron" style="width:5%;text-align: center">CANCEL</th>
                        <th class="jumbotron" style="width:5%;text-align: center">UPDATE</th>
                        <th class="jumbotron" style="width:5%;text-align: center">CALL</th>
                        
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($data as $key=>$value){ ?>		
                    <tr>
                            <td><?php echo ($key+1).'.'?></td>
                            <td style="text-align: center"><?php echo $value['phone'];?></td>
                            <td style="text-align: center"><?php 
                                    $old_date_timestamp = strtotime($value['action_date']);
                                    echo date('j M, Y g:i A', $old_date_timestamp);  
                                ?>
                            </td>
                            <td style="text-align: center"><?php echo $value['counter'];?></td>
                            <td style="text-align: center"><?php 
                                    $old_date_timestamp = strtotime($value['update_date']);
                                    if($value['counter'] != 0){
                                        echo date('j M, Y g:i A', $old_date_timestamp);  
                                    }
                                ?>
                            </td>
                            <td style="text-align: center"><?php echo $value['updation_reason'];?></td>
                            <td style="text-align: center">
                                <a href="javascript:void(0)" onclick="return update_partner_missed_calls(<?php echo $value['id']?>,<?php echo $value['phone']?>)" class="btn btn-sm btn-success" title="Create Booking" > <i class="fa fa-book" aria-hidden="true"></i></a>
                            </td>
                            <td style="text-align: center">
                                <a href="javascript:void(0)" class="btn btn-sm btn-warning" title="Cancel Lead" onclick="return assign_id(<?php echo $value['id'] ?>)" data-toggle="modal" data-target="#cancelmodal"> <i class="fa fa-times" aria-hidden="true"></i></a>
                            </td>
                            <td style="text-align: center">
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary" title="Update Lead" onclick="return assign_id_update(<?php echo $value['id'] ?>)" data-toggle="modal" data-target="#updatemodal"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            </td>
                            <td style="text-align: center">
                                <button type="button" onclick="outbound_call(<?php echo $value['phone'] ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>
<script type="text/javascript">
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call === true) {

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
    
    function update_partner_missed_calls(id,phone){
        
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/update_partner_missed_calls/' + id+'/Completed',
                success: function(response) {
                    if(response){
                        //On success page reload and redirect to Find User
                        location.reload();
                        window.open('<?php echo base_url()?>employee/user/finduser/0/0/'+phone, '_blank');
                        
                    }else{
                        //On Error in Updation
                        $('#add_error').css('display','block');
                    }

                }
            });
    }
    
    function assign_id(id){
        $('#leads_id').val(id);
    }
    function assign_id_update(id){
        $('#leads_id_update').val(id);
    }
    
    function form_submit() {
        
        check = $('input:radio[name ="cancellation_reason"]:checked').val();
        if(check === undefined){
            $('#error_message').css('display','block');
            return false;
        }else{
            $("#cancellation_form").submit();
        }
    }  
    
    function form_submit_update() {
        
        check = $('input:radio[name ="updation_reason"]:checked').val();
        if(check === undefined){
            $('#error_message_update').css('display','block');
            return false;
        }else{
            $("#update_form").submit();
        }
    }    
    </script>
    <?php $this->session->unset_userdata('cancel_leads'); ?>
<?php $this->session->unset_userdata('update_leads'); ?>