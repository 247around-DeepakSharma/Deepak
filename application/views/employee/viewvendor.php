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
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover();   
    });
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
            <div class="col-md-12" id="state_form">
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
         </form>
        </div>
        
        <table class="table table-bordered table-condensed" id="vender_details">
          
          <tr>
          	<th class="jumbotron">ID</th>
          	<th class="jumbotron">Name</th>
                <th class="jumbotron">CRM Login / Password</th>
          	<th class="jumbotron">PoC Name</th>
          	<th class="jumbotron">PoC Number</th>
          	<th class="jumbotron">Owner Name</th>
          	<th class="jumbotron">Owner Phone No.</th>
          	<th class="jumbotron">Sub District Office</th>
          	<th class="jumbotron">Temporary</th>
          	<th class="jumbotron">Permanent</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><a href="<?php echo base_url();?>employee/vendor/editvendor/<?=$row['id'];?>"><?=$row['name'];?></a></td>
            <td class="text-center">
                    <a href="javascript:void(0)" class="btn btn-md btn-success" onclick='return login_to_vendor(<?php echo $row['id']?>)'  <?php echo ($row['active'] == 0)?'disabled=""':'' ?> title="<?php echo strtolower($row['sc_code']) . " / " . strtolower($row['sc_code']);  ?>">Login</a>
            </td>
            <td><a href="mailto:<?php echo $row['primary_contact_email'];?>" data-toggle="popover" data-trigger="hover" data-content="Send Mail To POC"><?=$row['primary_contact_name'];?></a></td>
          	<td>
          	    <?=$row['primary_contact_phone_1'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['primary_contact_phone_1']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
          	</td>
                <td><a href="mailto:<?php echo $row['owner_email'];?>" data-toggle="popover" data-trigger="hover" data-content="Send Mail to Owner"><?=$row['owner_name'];?></a></td>
          	<td>
          	    <?=$row['owner_phone_1'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['owner_phone_1']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
          	</td>
                <td>
                    <?php if ($row['is_upcountry'] == 1) { ?>
                        <a style= "background-color: #fff;" target="_blank" class='btn btn-sm btn-primary' href="<?php echo base_url(); ?>employee/vendor/get_sc_upcountry_details/<?php echo $row['id'];  ?>"><i style="color:red; font-size:20px;" class="fa fa-road" aria-hidden="true"></i></a>
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
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
<script type='text/javascript'>
    function login_to_vendor(vendor_id){
        var c = confirm('Login to Service Center CRM?');
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