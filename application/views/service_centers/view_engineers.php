<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>

<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h2>Egineers Details</h2>
        <div class="pull-right" style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>service_center/add_engineer"><input class="btn btn-primary" type="Button" value="Add Engineer"></a>
        </div>
        
        <table  class="table table-striped table-bordered">
          
          <tr>
          	<th>SNo.</th>
            
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

          
          <?php $sno = 1; foreach($engineers as $key =>$row){?>
          <tr>
            <td><?php echo $sno;?></td>
            <td><?php echo $row['appliance_name']; ?></td>
            <td><?php echo $row['name'];?></td>
            <td>
                <?php echo $row['phone'];?>
               
            </td>
            <td><?php echo $row['alternate_phone']; ?></td>
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
                                    . "href=" . base_url() . "employee/vendor/change_engineer_activation/$row[id]/0>Disable</a>";                
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
          
          </tr>
          <?php $sno++;} ?>
        </table>


        
      </div>
    </div>
</div>      
