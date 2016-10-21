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
       
        <h1>Partners</h1>
        <div class="pull-right" style="margin:0px 30px 20px 0px;">
            <a href="<?php echo base_url();?>employee/partner/get_add_partner_form"><input class="btn btn-primary" type="Button" value="Add Partner"></a>
        </div>
        
        <table style="width:98%;" class="table table-striped table-bordered">
          
          <tr>
          	<th>ID</th>
          	<th width="200px;">Company Name</th>
          	<th width="200px;">Public Name</th>
          	<th width="250px;">Address</th>
          	<th>PoC Name</th>
          	<th>PoC Number</th>
          	<th>PoC Email</th>
          	<th>Owner Name</th>
          	<th>Owner Phone No.</th>
          	<th>Owner Email</th>
          	<th>Login</th>
          	<th colspan="2">Action</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><a href="<?php echo base_url();?>employee/partner/editpartner/<?=$row['id'];?>"><?=$row['company_name'];?></a></td>
            <td><a href="<?php echo base_url();?>employee/partner/editpartner/<?=$row['id'];?>"><?=$row['public_name'];?></a></td>
            
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
                <td><?=$row['user_name'];?> / <?=$row['user_name'];?></td>
          	<td><?php if($row['is_active']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/partner/deactivate/$row[id]>Deactivate</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-success' "
                                    . "href=" . base_url() . "employee/partner/activate/$row[id]>Activate</a>";                
                }
              ?>
            </td>
            
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
