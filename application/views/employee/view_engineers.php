<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>


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
       
        <h1>Egineers Details</h1>
        <div class="pull-right" style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>employee/vendor/add_engineer"><input class="btn btn-primary" type="Button" value="Add Engineer"></a>
        </div>
        
        <table  class="table table-striped table-bordered">
          
          <tr>
          	<th>ID</th>
            <th>Service Center</th>
            <th>Appliances</th>
          	<th>Name</th>
          	<th>Mobile</th>
          	<th>Alternate Mobile Number</th>
            <th>Phone Type</th>
          	<th width="250px;">Address</th>
          	<th>Id Proof</th>
          	<th>Id Card No.</th>
          	<th>Bank Name</th>
          	<th>Bank Account Number</th>
          	<th>Bank IFSC Code</th>
          	<th>Ac Holder Name</th>
            <th colspan="2">Acttion</th>
          	
          </tr>

          
          <?php foreach($engineers as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><?php  echo $row['service_center_name']; ?></td>
            <td><?php echo $row['appliance_name']; ?></td>
            <td><?php echo $row['name'];?></td>
            <td>
                <?php echo $row['phone'];?>
                <button type="button" onclick="outbound_call(<?php echo $row['phone']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
            </td>
            <td><?php echo $row['alternate_phone']; ?>
             <button type="button" onclick="outbound_call(<?php echo $row['alternate_phone']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>

            </td>
            <td><?php echo $row['phone_type'];?></td>
          	<td><?php echo $row['address'];?></td>
          	<td><?=$row['identity_proof'];?></td>
          	<td><?=$row['identity_proof_number'];?></td>
          	<td>
          	    <?=$row['bank_name'];?>
               
          	</td>
          	
          	<td><?=$row['banck_ac_no'];?></td>
          	<td><?php echo $row['bank_ifsc_code'];  ?></td>
            <td><?php echo $row['bank_holder_name']; ?></td>
          	<td><?php if($row['active']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/vendor/change_engineer_activation/$row[id]/0>Deactivate</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-success' "
                                    . "href=" . base_url() . "employee/vendor/change_engineer_activation/$row[id]/1>Activate</a>";                
                }
              ?>
            </td>
            <td><?php  echo "<a onClick=\"javascript: return confirm('Please confirm, want to delete engineer');\" id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/delete_engineer/$row[id]>Delete</a>";                ?></td>
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
