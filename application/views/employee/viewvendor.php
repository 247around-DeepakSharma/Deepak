<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>

<script>
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Vendor ?");
       
        if (confirm_call == true) {
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                }
            });
        } else {
            return false;
        }

    }

    function validate_form(){
        var state = $('#state_select').val();
        if(state == ""){
            $('#state_error').css('display','block');
            $('#state_form').css('margin-bottom','20px');
            $('#inner_state_div').css('height','75px');
            return false;
        }else{
            return true;
        }
    }
    
    function get_data()
    {
        var data = $("#active_state option:selected").val();
        $('#get_vender').submit();
    }
</script>
<div  id="page-wrapper">
    <div class="row">
        <?php
    if ($this->session->userdata('success')) {
    echo '<div class="alert alert-success alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('success') . '</strong>
                 </div>';
    }
    ?>
        <?php
    if ($this->session->userdata('error')) {
    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <strong>' . $this->session->userdata('error') . '</strong>
                 </div>';
    }
    ?>
      <div >
       
        <h1>Service Center</h1>
       
        <div class="pull-right" style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>employee/vendor/add_vendor"><input class="btn btn-primary" type="Button" value="Add Service Centre"></a>
        </div>
        
        <div class="pull-right" style="margin-bottom: 20px; margin-right: 50px;">
            <form action="<?php echo base_url();?>employee/vendor/viewvendor" method="get" id="get_vender" class="form-inline">
                <label for="active_state">Show Vender &nbsp; &nbsp;</label>
                <select name="active_state" id="active_state" onchange="get_data();" class="form-control">
                    <option value="all" <?php echo isset($selected) && $selected['active_state'] == 'all'? 'selected="selected"':''?>>ALL</option>
                    <option value="1" <?php echo isset($selected) && $selected['active_state'] == '1'? 'selected="selected"':''?>>Active</option>
                </select> 
            </form>
        </div>
        
        <div class="col-md-6" id="state_form">
            <div style="background-color: #EEEEEE;width:400px;height:50px;padding-bottom:20px;border-radius: 5px;" id="inner_state_div">
                <form method="POST" action ="<?php echo base_url(); ?>employee/vendor/get_sc_charges_list" style="padding-top:8px;">
                    <span id="state_error" style="display:none;color:red;margin-left:20px;">Please enter State</span>
                    <div class="col-md-6">
                        <select name="state" id="state_select" class="form-control">
                            <option value="" disabled="" selected>Select State</option>
                            <?php foreach ($state as $value) { ?>
                                <option value="<?php echo $value['state'] ?>"><?php echo $value['state'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="submit" value="Download Charges List" onclick="return validate_form()" class="btn btn-primary" />
                    </div>
                </div>
            </form>
        </div>
        
        <table class="table table-bordered table-condensed" id="vender_details">
          
          <tr>
          	<th class="jumbotron">ID</th>
          	<th class="jumbotron">Name</th>
                <th class="jumbotron">CRM Login / Password</th>
          	<th class="jumbotron">Phone No.</th>
          	<th class="jumbotron">Email</th>
          	<th width="250px;" class="jumbotron">Address</th>
          	<th class="jumbotron">PoC Name</th>
          	<th class="jumbotron">PoC Number</th>
          	<th class="jumbotron">PoC Email</th>
          	<th class="jumbotron">Owner Name</th>
          	<th class="jumbotron">Owner Phone No.</th>
          	<th class="jumbotron">Owner Email</th>
          	<th class="jumbotron">Sub District Office</th>
          	<th class="jumbotron">Temporary</th>
          	<th colspan="2" class="jumbotron">Permanent</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><a href="<?php echo base_url();?>employee/vendor/editvendor/<?=$row['id'];?>"><?=$row['name'];?></a></td>
            <td class="text-center">
                    <a href="javascript:void(0)" class="btn btn-md btn-success" onclick='return login_to_vendor(<?php echo $row['id']?>)'  <?php echo ($row['active'] == 0)?'disabled=""':'' ?> title="<?php echo strtolower($row['sc_code']) . " / " . strtolower($row['sc_code']);  ?>">Login</a>
            </td>
            <td>
                <?=$row['phone_1'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['phone_1']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
            </td>
          	<td><?=$row['email'];?></td>
          	<td><?=$row['address'];?></td>
          	<td><?=$row['primary_contact_name'];?></td>
          	<td>
          	    <?=$row['primary_contact_phone_1'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['primary_contact_phone_1']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
          	</td>
          	<td><?=$row['primary_contact_email'];?></td>
          	<td><?=$row['owner_name'];?></td>
          	<td>
          	    <?=$row['owner_phone_1'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['owner_phone_1']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
          	</td>
          	
          	<td><?=$row['owner_email'];?></td>
                <td>
                    <?php if ($row['is_upcountry'] == 1) { ?>
                        <a class='btn btn-sm btn-primary' href="<?php echo base_url(); ?>employee/vendor/get_sc_upcountry_details/<?php echo $row['id'];  ?>"><i class='fa fa-eye' aria-hidden='true'></i></a>
                    <?php } ?>    
                </td>
                
                <td>
                        <?php
                        if ($row['on_off'] == 1) { ?>
                            <a id='edit' class='btn btn-small btn-danger' href="<?php base_url() ?>temporary_on_off_vendor/<?php echo $row['id']?>/0" <?php if($row['active'] == 0){echo 'disabled';}?>>Off</a>
                        <?php } else { ?>
                            <a id='edit' class='btn btn-small btn-success' href="<?php base_url() ?>temporary_on_off_vendor/<?php echo $row['id']?>/1" <?php if($row['active'] == 0){echo 'disabled';}?>>On</a>
                        <?php }
                        ?>
                    </td>
                    
          	<td><?php if($row['active']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/deactivate/$row[id]>Deactivate</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/vendor/activate/$row[id]>Activate</a>";                
                }
              ?>
            </td>
<!--            <td><?php  echo "<a onClick=\"javascript: return confirm('Please confirm, want to delete vendor');\" id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/delete/$row[id]>Delete</a>";                ?></td>-->
            <td><?php if($row['is_update']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-warning' "
                                    . "href=" . base_url() . "employee/vendor/control_update_process/$row[id]/0>Disable New CRM</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-info' "
                                    . "href=" . base_url() . "employee/vendor/control_update_process/$row[id]/1>Enable New CRM</a>";                
                }
              ?>
            </td>
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
<script type='text/javascript'>
    function login_to_vendor(vendor_id){
        var c = confirm('Do you want to login ?');
        if(c){
            $.ajax({
                url:'<?php echo base_url()."employee/vendor/allow_log_in_to_vendor/" ?>'+vendor_id,
                success: function (data) {
                    window.open("<?php echo base_url().'service_center/pending_booking'?>",'_blank');
                }
            });
            
        }else{
            return false;
        }
    }
    
    </script>
    
    <?php if ($this->session->userdata('success')) {
        $this->session->unset_userdata('success'); 
    } if ($this->session->userdata('error')) {
         $this->session->unset_userdata('error'); 
    }?>