
<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h2>Engineer Details</h2>
        <br>
        <div style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>service_center/add_engineer"><input class="btn btn-primary" type="Button" value="Add Engineer"></a>
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
          	<th>No.</th>
          	<th>Name</th>
            <th>Appliances</th>
          	<th>Mobile</th>
          	<th>Alternate Mobile</th>
          	<th>ID Proof</th>
          	
            <th colspan="3">Actions</th>
          </tr>

          
          <?php $sno = 1; foreach($engineers as $key =>$row){?>
          <tr>
            <td><?php echo $sno;?></td>
            <td><a href="<?php echo base_url()?>employee/vendor/get_edit_engineer_form/<?php echo $row['id']?>"><?php echo $row['name'];?></a></td>
            <td><?php echo $row['appliance_name']; ?></td>
            <td><?php echo $row['phone'];?></td>
            <td><?php echo $row['alternate_phone']; ?></td>
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
            <td><?php  echo "<a onClick=\"javascript: return confirm('Delete engineer?');\" id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/delete_engineer/$row[id]>Delete</a>";                ?></td>
          </tr>
          
          </tr>
          <?php $sno++;} ?>
        </table>


        
      </div>
    </div>
</div>      
