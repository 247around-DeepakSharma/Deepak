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
</script>

<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h1>Service Centres</h1>
        <div class="pull-right" style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>employee/vendor/add_vendor"><input class="btn btn-primary" type="Button" value="Add Service Centre"></a>
        </div>
        
        <table style="width:98%;" class="table table-striped table-bordered">
          
          <tr>
          	<th>ID</th>
          	<th width="200px;">Name</th>
          	<th>Phone No.</th>
          	<th>Email</th>
          	<th width="250px;">Address</th>
          	<th>PoC Name</th>
          	<th>PoC Number</th>
          	<th>PoC Email</th>
          	<th>Owner Name</th>
          	<th>Owner Phone No.</th>
          	<th>Owner Email</th>
          	<th>CRM Login / Password</th>
                <th colspan="2" style="text-align: center;">Action</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?php echo ($key+1).'.';?></td>
            <td><a href="<?php echo base_url();?>employee/vendor/editvendor/<?=$row['id'];?>"><?=$row['name'];?></a></td>
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
          	<td><?php echo strtolower($row['sc_code']) . " / " . strtolower($row['sc_code']);  ?></td>
          	<td><?php if($row['active']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/vendor/deactivate/$row[id]>Deactivate</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-success' "
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
