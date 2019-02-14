<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
    .select2-selection--multiple{
            min-height: 38px !important;
            border: 1px solid #aaa !important;
    }
</style>

<div class="right_col" role="main">
    <div class="row">
       
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>RM State Pincode Report</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="tab-content" style="margin-top: 10px;">
                            <div class="tab-pane fade in active" id="tab1">
                            <table class="table table-striped table-bordered" id="rm_state_missing_table">
        <thead>
                <tr style="background: #405467;color: #fff;margin-top: 5px;">
                    <th>State</th> 
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
        foreach($rm_arr as $value){
             if(isset($state_arr[$value]))
                        {
                           ?>   
                        <tr>
                                <td><?php echo $state_arr[$value];?></td>
                               <?php
                                foreach($service_arr as $servicekey=>$servicevalue){

                                   if(isset($vendorStructuredArray['state_'.$value]['appliance_'.$servicekey])){
                                        $result=$vendorStructuredArray['state_'.$value]['appliance_'.$servicekey];
                                        $percent=round(($result['missing_pincode_per']*100),0);
                                ?>
                               <td> <?php echo wordwrap($result['Total_pincode']).'<br>'; ?>
                                <?php echo wordwrap($result['missing_pincode']).'<br>';  ?>
                                <?php echo wordwrap($percent.' %'); ?></td>
                                <?php    
                                }
                                    else{
                                       ?>
                                   <td>     <?php echo wordwrap($result['Total_pincode']).'<br>'; ?>
                                <?php echo wordwrap('0').'<br>';?>
                                <?php echo wordwrap('0 %'); ?></td>
                                        <?php
                                    }
                                }
                        }
               
                ?>
                        </tr>
           <?php }
           ?>
    </tbody>
                            </table>
    
                        </div>
                            </div>
                        </div>
                   
            </div>
        </div>
        
    

    </div>

    <!-- END -->
</div>
