<table class="table table-striped table-bordered jambo_table bulk_action">
                    <thead>
                        <tr>
                            <th>RM/Services</th> 
                        <?php
                            foreach($service_arr as $servicekey=>$servicevalue){
                            ?>
                        <th><?php echo $servicevalue;?></th>
                  <?php
                  }
                    ?>
                      </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                        if(!empty($rm_arr)){
                         foreach($rm_arr as $value){
                        ?>   
                        <tr>
<!--                                <td><?php //echo $value['full_name'];?></td>-->
                                <td><a type="button"  class="btn btn-info" target="_blank" 
                                      href='<?php echo base_url(); ?>employee/dashboard/pincode_rm_wise/<?php echo $value['rm_id'] ?>'><?php echo $value['full_name'];?></a></td>
                               <?php
                                foreach($service_arr as $servicekey=>$servicevalue){
                                     $missing_pincode=0;
                                     $missing_pincode_per=0;$total_pincode=0;
                                    foreach($value['state_code'] as $value1)
                                      {
                                        if(array_key_exists('state_'.$value1,$india_pincode))
                                        {
                                            $total_pincode=$total_pincode+$india_pincode['state_'.$value1];
                                        }
                                      if(isset($state_arr[$value1]))
                                       {
                                   if(isset($vendorStructuredArray['state_'.$value1]['appliance_'.$servicekey])){
                                        $missing_pincode=$missing_pincode+$vendorStructuredArray['state_'.$value1]['appliance_'.$servicekey]['missing_pincode'];
                                       }
                                     
                                       }
                                      }
                                          
                                      $missing_pincode_decimal=$missing_pincode/$total_pincode;
                                      $missing_pincode_per=round(($missing_pincode_decimal*100),0);
                                    ?>
                                <?php //echo $total_pincode.'<br>'; ?>
                                <?php// echo $missing_pincode.'(';  ?>
                               <td> <?php echo wordwrap($missing_pincode_per.'%'); ?></td>
                                <?php    
                               
                                }
                                ?>
                        </tr>
           <?php }
           }
           ?>
                        
                    </tbody>
                </table>

