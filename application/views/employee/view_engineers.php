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
       
        <h1>Engineer Details</h1>
        <br>
        <div style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>employee/vendor/add_engineer"><input class="btn btn-primary" type="Button" value="Add Engineer"></a>
        </div>
        <?php if($this->session->userdata('update_success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('update_success') . '</strong>
                    </div>';
                    }
        ?>
        <table  class="table table-striped table-bordered">
          
          <tr>
          	<th>ID</th>
            <th>Service Center</th>
          	<th>Name</th>
            <th>Appliances</th>
          	<th>Mobile</th>
          	<th>Alternate Mobile Number</th>
          	<th>ID Proof</th>
          	
            <th colspan="3">Action</th>
          	
          </tr>

          
          <?php foreach($engineers as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><?php  echo $row['service_center_name']; ?></td>
            <td><a href="<?php echo base_url()?>employee/vendor/get_edit_engineer_form/<?php echo $row['id']?>"><?php echo $row['name'];?></a></td>
            <td><?php echo $row['appliance_name']; ?></td>
            <td>
                <?php echo $row['phone']; if(!empty($row['phone']) && !empty($c2c)) { ?>
                <button type="button" onclick="outbound_call(<?php echo $row['phone']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
                <?php } ?>
            </td>
            <td><?php echo $row['alternate_phone']; ?>
              <?php if(!empty($row['alternate_phone']) && !empty($c2c)) {?>
             <button type="button" onclick="outbound_call(<?php echo $row['alternate_phone']; ?>)" 
                    class="btn btn-sm btn-info">
                        <i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i>
                </button>
              <?php } ?>

            </td>
          	<td><?php echo $row['identity_proof'];?></td>
          	<td><?php if($row['active']==1)
                {
                  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/vendor/change_engineer_activation/$row[id]/0>Disable</a>";                
                }
                else
                {
                  echo "<a id='edit' class='btn btn-small btn-success' "
                                    . "href=" . base_url() . "employee/vendor/change_engineer_activation/$row[id]/1>Enable</a>";                
                }
              ?>
            </td>
            <td><?php  echo "<a id='edit' class='btn btn-small btn-primary' "
                                    . "href=" . base_url() . "employee/vendor/get_edit_engineer_form/$row[id]>Edit</a>";?></td>
            <td><?php  echo "<a onClick=\"javascript: return confirm('Delete Engineer?');\" id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/delete_engineer/$row[id]>Delete</a>";                ?></td>
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
