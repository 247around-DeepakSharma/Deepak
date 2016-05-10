<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h1><b>Service Centres</b><a class="pull-right" href="<?php echo base_url();?>employee/vendor/add_vendor"><input class="btn btn-primary" type="Button" value="Add Service Centre"></a></h1>
        
        
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
          	<th colspan="2">Action</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=$row['id'];?></td>
            <td><a href="<?php echo base_url();?>employee/vendor/editvendor/<?=$row['id'];?>"><?=$row['name'];?></a></td>
            <td><?=$row['phone_1'];?></td>
          	<td><?=$row['email'];?></td>
          	<td><?=$row['address'];?></td>
          	<td><?=$row['primary_contact_name'];?></td>
          	<td><?=$row['primary_contact_phone_1'];?></td>
          	<td><?=$row['primary_contact_email'];?></td>
          	<td><?=$row['owner_name'];?></td>
          	<td><?=$row['owner_phone_1'];?></td>
          	<td><?=$row['owner_email'];?></td>
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
            <td><?php  echo "<a id='edit' class='btn btn-small btn-danger' "
                                    . "href=" . base_url() . "employee/vendor/delete/$row[id]>Delete</a>";                ?></td>
          </tr>
          <?php } ?>
        </table>

        
      </div>
    </div>
</div>      
