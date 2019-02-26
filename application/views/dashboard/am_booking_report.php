
<table class="table table-striped table-bordered" style="margin-top:30px;">
                    <thead>
                        <tr>
                            <th>AM Name</th> 
                            <th>Total Repair Call</th>
                            <th>Complete Repair Call</th>
                            <th>Pending Repair Call</th>
                             <th>Cancalled Repair Call</th>
                            <th>Total Installation Call</th>
                            <th>Complete Installation Call</th>
                            <th>Pending Installation Call</th>
                            <th>Cancalled Installation Call</th>
                      </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                        if(!empty($am_booking_data)){
                         foreach($am_booking_data['am_data'] as $value){
                        ?>   
                        <tr>
                              <td><?php echo $value['full_name']?></td>
                               <?php
                               if(array_key_exists('am_'.$value['id'],$am_booking_data['am_booking_data']))
                               {
                                $value=$am_booking_data['am_booking_data']['am_'.$value['id']]['booking_data'];
                               ?>
                              <td><?php echo $value['repair_total']; ?></td>
                              <td><?php echo $value['repair_completed']; ?></td>
                              <td><?php echo $value['repair_pending']; ?></td>
                              <td><?php echo $value['repair_cancalled']; ?></td>
                              <td><?php echo $value['install_total']; ?></td>
                              <td><?php echo $value['install_completed']; ?></td>
                              <td><?php echo $value['install_pending']; ?></td>
                              <td><?php echo $value['install_cancalled']; ?></td>
                                <?php    
                               
                                }
                                else
                                {
                                    ?>
                               
                                   <td><?php echo '0'; ?></td> 
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <td><?php echo '0'; ?></td>
                                   <?php }
                             
                                ?>
                        </tr>
           <?php }
           }
           ?>
                        
                    </tbody>
   </table>

            

