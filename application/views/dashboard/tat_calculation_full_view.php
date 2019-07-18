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

        <div class="clearfix"></div>
                <?php
                if($is_am == 1){
                    if($is_pending){
                        $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID."/0/1/pending"; 
                    }
                    else{
                        $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID."/0/1"; 
                    }
                }
                else{
                     if($is_pending){
                         $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID."/0/0/pending"; 
                     }
                     else{
                         $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID; 
                     }
                }
                ?>
        <form action="<?php echo $url?>" method="post">
       <div class="table_filter" style="background: #5bc0de;padding: 10px;margin-bottom: 10px;border-radius: 5px;">
           <div class="row">
               <?php  if(!$this->session->userdata('partner_id')){ ?>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 145px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="" style="color:#fff">Partners</label>
                            <select class="form-control filter_table" id="partner_id" name="partner_id">
                                <option value="not_set" <?php if(isset($filters['partner_id'])){if($filters['partner_id'] == 'not_set'){echo 'selected="selected"';}} ?>>All</option>
                                <?php foreach($partners as $val){ ?>
                                <option value="<?php echo $val['id']?>" <?php if(isset($filters['partner_id'])){if($filters['partner_id'] == $val['id']){echo 'selected="selected"';}} ?>><?php echo $val['public_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
               <?php } ?>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 145px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="" style="color:#fff">Appliance</label>
                            <select class="form-control filter_table" id="service_id" name="services">
                                    <option value="not_set" <?php if(isset($filters['services'])){if($filters['services'] == 'not_set'){echo 'selected="selected"';}} ?>>All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>" <?php if(isset($filters['services'])){if($filters['services'] == $val['id']){echo 'selected="selected"';}} ?>><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 188px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="" style="color:#fff">Request Type</label>
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
                                 <option value="not_set" <?php if(isset($filters['request_type'])){if(in_array("not_set", $filters['request_type'])){echo 'selected="selected"';}} ?>>All</option>
                                <option value="Installation" <?php if(isset($filters['request_type'])){if(in_array("Installation", $filters['request_type'])){echo 'selected="selected"';}} ?>>Installations</option>
                                <option value="Repair_with_part" <?php if(isset($filters['request_type'])){if(in_array("Repair_with_part", $filters['request_type'])){echo 'selected="selected"';}} ?>>Repair With Spare</option>  
                                <option value="Repair_without_part" <?php if(isset($filters['request_type'])){if(in_array("Repair_without_part", $filters['request_type'])){echo 'selected="selected"';}} ?>>Repair Without Spare</option>  
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 156px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">  
                             <label for="" style="color:#fff">In Warranty</label>
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="not_set" <?php if(isset($filters['free_paid'])){if($filters['free_paid']=='not_set'){echo 'selected="selected"';}} ?>>All</option>
                                <option value="Yes" <?php if(isset($filters['free_paid'])){if($filters['free_paid']=='Yes'){echo 'selected="selected"';}} ?>>Yes (In Warranty)</option>
                                <option value="No" <?php if(isset($filters['free_paid'])){if($filters['free_paid']=='No'){echo 'selected="selected"';}} ?>>No (Out Of Warranty)</option>  
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 156px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="" style="color:#fff">Is Upcountry</label>
                            <select class="form-control filter_table" id="upcountry" name="upcountry">
                                <option value="not_set" <?php if(isset($filters['upcountry'])){if($filters['upcountry']=='not_set'){echo 'selected="selected"';}} ?>>All</option>
                                <option value="Yes" <?php if(isset($filters['upcountry'])){if($filters['upcountry']=='Yes'){echo 'selected="selected"';}} ?>>Yes</option>
                                 <option value="No" <?php if(isset($filters['upcountry'])){if($filters['upcountry']=='No'){echo 'selected="selected"';}}?>>No</option>
                            </select>
                        </div>
                    </div>
                </div>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 206px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <?php
                            $datrangeInitialValue =  date('Y-m-d', strtotime('-31 days'))." - ".date("Y-m-d");
                            ?>
                            <label for="" style="color:#fff">Booking Initial date range</label>
                            <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" style="width: 190px; height: 38px;border-radius: 4px;" 
                                   value="<?php if(isset($filters['daterange_completed_bookings'])){echo $filters['daterange_completed_bookings'];} else{ echo $datrangeInitialValue;}?>">
                        </div>
                    </div>
                </div>
               <?php
               if($is_pending){?>
                   <div class="form-group col-md-3" style="margin: 0px;padding: 0px 1px;width: 159px;">
                       <label for="" style="color:#fff">Dependency</label>
                       <select class="form-control"  ng-model="status" id="status" name="status[]" multiple="">
                                <option value="not_set" <?php if(!empty($filters['status'])){if(in_array("not_set", $filters['status'])){echo 'selected="selected"';}} ?>>All</option>
                                <option value="247Around" <?php if(!empty($filters['status'])){if(in_array("247Around", $filters['status'])){echo 'selected="selected"';}} ?>>247Around</option>
                                <option value="Partner" <?php if(!empty($filters['status'])){if(in_array("Partner", $filters['status'])){echo 'selected="selected"';}} ?>>Partner</option>  
                                <option value="Vendor:not_define" <?php if(!empty($filters['status'])){if(in_array("Vendor:not_define", $filters['status'])){echo 'selected="selected"';}} ?>>Vendor</option>  
                                        </select>
                    </div>
               <?php } else {
               ?>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 159px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="" style="color:#fff">Status</label>
                            <select class="form-control filter_table" id="status" name="status">
                                <option value="not_set" <?php if(isset($filters['status'])){if($filters['status']=='not_set'){echo 'selected="selected"';}} ?>>All</option>
                                <option value="Completed" <?php if(isset($filters['status'])){if($filters['status']=='Completed'){echo 'selected="selected"';}} ?>>Completed</option>
                                 <option value="Cancelled" <?php if(isset($filters['status'])){if($filters['status']=='Cancelled'){echo 'selected="selected"';}}?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
               <?php } ?>
                <div class="col-md-3" style="margin-top: 21px;padding: 0px 1px;width: 100px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input class="btn btn-success" type="submit" value="Apply Filters" style="background: #405467;padding: 8px;">
                        </div>
                    </div>
                </div>
                </div> 
        </div>
            </form>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>TAT Report</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab1" data-toggle="tab">
        <div class="hidden-xs">States</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab2" data-toggle="tab">
        <div class="hidden-xs">Service Centers</div>
    </button>
</div>
</div>
                    <div class="tab-content" style="margin-top: 10px;">
                            <div class="tab-pane fade in active" id="tab1">
                                <form action="<?php echo base_url()?>employee/dashboard/download_tat_report" method="post">
                                    <input type="hidden" value='<?php if(isset($state['TAT'])) { echo json_encode($state['TAT']); } else { echo json_encode($state); } ?>' name="data">
                                    <input type="submit" value="Download CSV" class="btn btn-primary" style="background: #405467;border: none;">
                                    </form>
                                <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_state_table">
    <thead>
        <tr style="background: #405467;color: #fff;margin-top: 5px;">
                            <th>S.no</th>
                            <th>States</th>
                            <?php if ($is_pending) {?>
                             <th> >D3</th>
                             <?php  } ?>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                              <?php if ($is_pending) {?>
                            <th>Total</th>
                             <?php  } ?>
                        </tr>
    </thead>
    <tbody>
        <?php
         if(!$is_pending){
             $stateTemp = $state;
             $state = $state['TAT'];
             $sfTemp = $sf;
             $sf = $sf['TAT'];
         }
        $index = 0;
        foreach($state as $key => $values){
            $index++;
            ?>
        <tr>
            <td><?php echo $index;?></td>
            <td><?php echo $values['entity']?></td>
            <?php
            if(!$is_pending || $this->session->userdata('partner_id')){ 
                 if($is_pending){
                    ?>
                    <td><?php echo $values['TAT_GREATER_THAN_3'];?></td>
                    <?php
                }
                ?>
                <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
                <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
                <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
                <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
                <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
                <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
                <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
                <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
            <?php
                if($is_pending){
                    ?>
                    <td><?php echo $values['Total_Pending'] ."<br>(".$values['TAT_total_per']."%)";?></td>
                    <?php
                }
            }
            else{
                ?>  <td><?php echo $values['TAT_GREATER_THAN_3'];?></td> <?php
                if($values['entity'] != 'Total') {?>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_0_bookings']; ?>">
                            <input type="submit" value="<?php echo $values['TAT_0'];?>" class="btn btn-success">
                        </form>
                    <?php echo "(". $values['TAT_0_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_1_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_1'];?>" class="btn btn-success">
                        </form>
                    <?php echo "(". $values['TAT_1_per']."%)";?></td>
              <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_2_bookings'];?>">
                <input type="submit" value="<?php echo $values['TAT_2'];?>" <?php if($values['TAT_2'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_2_per']."%)";?></td>
              <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_3_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_3'];?>" <?php if($values['TAT_3'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_3_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_4_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_4'];?>" <?php if($values['TAT_4'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_4_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_5_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_5'];?>" <?php if($values['TAT_5'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_5_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_8_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_8'];?>" <?php if($values['TAT_8'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_8_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_16_bookings']; ?>">
                            <input type="submit" value="<?php echo $values['TAT_16'];?>" <?php if($values['TAT_16'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_16_per']."%)";?></td>
                 <?php
            }
            else{ ?>
                <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
                <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
                <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
                <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
                <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
                <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
                <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
                <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
                <?php
            }
            ?>
                  <td><?php echo $values['Total_Pending'] ."<br>(".$values['TAT_total_per']."%)";?></td>
                <?php
            }
                ?>
                 
                                  </tr>
                 <?php
        }
        ?>
    </tbody>
    </table>
                                </div>
                        <div class="tab-pane fade in" id="tab2">
                             <form action="<?php echo base_url()?>employee/dashboard/download_tat_report" method="post">
                                    <input type="hidden" value='<?php echo json_encode($sf);?>' name="data">
                                    <input type="hidden" value='<?php echo json_encode($sf_state);?>' name="data_state">
                                    <input type="submit" value="Download CSV" class="btn btn-primary" style="background: #405467;border: none;">
                                    </form>
                               <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_sf_table">
    <thead>
        <tr style="background: #405467;color: #fff;margin-top: 5px;">
                            <th>S.no</th>
                            <th>Service Centers</th>
                            <th>State</th>
                            <?php if ($is_pending) {?>
                             <th> >D3</th>
                             <?php  } ?>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                             <?php if ($is_pending) {?>
                             <th>> Total</th>
                             <?php  } ?>
                        </tr>
    </thead>
    <tbody>
        <?php
        $index = 0;
        foreach($sf as $key => $values){
            $index++;
            ?>
        <tr>
            <td><?php echo $index;   ;?></td>
            <td>
                <?php
//                if($this->session->userdata('partner_id')){
//                    if($values['id'] !="00"){
//                        echo "247Around_Service_Center_".$values['id'];
//                    }
//                    else{
//                        echo wordwrap($values['entity'], 30, "<br />\n");
//                    }
//                }
//                else{
                    echo wordwrap($values['entity'], 30, "<br />\n");
                //}
                ?> </td>
            <td><?php 
            $onlyID = "00";
            $onlyIDArray = explode("_",$values['id']);
            if(isset($onlyIDArray[1])){
                $onlyID = $onlyIDArray[1];
            }
            if(array_key_exists("sf_".$onlyID, $sf_state)){
                echo $sf_state["sf_".$onlyID];
            }
            ?></td>
            <?php
               if($is_pending){
                    ?>
                    <td><?php echo $values['TAT_GREATER_THAN_3'];?></td>
                    <?php
                }
            if(!$is_pending || $this->session->userdata('partner_id')){
                ?>
                <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
                <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
                <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
                <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
                <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
                <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
                <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
                <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
            <?php
            }
            else {
                if($values['entity'] != 'Total'){
                ?>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_0_bookings']; ?>">
                            <input type="submit" value="<?php echo $values['TAT_0'];?>" <?php if($values['TAT_0'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_0_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_1_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_1'];?>" <?php if($values['TAT_1'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_1_per']."%)";?></td>
              <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_2_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_2'];?>" <?php if($values['TAT_2'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_2_per']."%)";?></td>
              <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo $values['TAT_3_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_3'];?>" <?php if($values['TAT_3'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_3_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_4_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_4'];?>" <?php if($values['TAT_4'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_4_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_5_bookings'];?>">
                            <input type="submit" value="<?php echo $values['TAT_5'];?>" <?php if($values['TAT_5'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_5_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_8_bookings'];?>">
                             <input type="submit" value="<?php echo $values['TAT_8'];?>" <?php if($values['TAT_8'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_8_per']."%)";?></td>
                <td>
                    <form action="<?php echo base_url()."employee/booking/open_pending_bookings"?>" method="post" target="_blank">
                            <input type="hidden" name="booking_id_status" value="<?php echo  $values['TAT_16_bookings']; ?>">
                            <input type="submit" value="<?php echo $values['TAT_16'];?>" <?php if($values['TAT_16'] >0){ ?> class="btn btn-danger" <?php } else{ ?>  class="btn btn-success"<?php } ?>>
                        </form>
                    <?php echo "(". $values['TAT_16_per']."%)";?></td>
            <?php
                }
       else{ ?>
           <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
            <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
            <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
            <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
            <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
            <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
            <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
            <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
            <?php
                }
                ?>
                             <td><?php echo $values['Total_Pending'] ."<br>(".$values['TAT_total_per']."%)";?></td>
            <?php
            }
?>
                
        </tr>
        <?php
        }
        ?>
    </tbody>
    </table>
                        </div>
                            </div>
                        </div>
                    <?php
                    
                    ?>
            </div>
        </div>
        
        <div class="clearfix"></div>

    </div>

    <!-- END -->
</div>
<script>
    $(document).ready(function(){
      var state_table = $('#tat_state_table').DataTable({
          "pageLength": 1000,
           dom: 'Bfrtip',
        buttons: ['csv'],
          "ordering": false
      });
     // state_table.Columns[""].DataType = typeof(int);
      var sf_table = $('#tat_sf_table').DataTable({
          "pageLength": 1000,
           dom: 'Bfrtip',
        buttons: ['csv'],
        "ordering": false
      });
    });
    $('#request_type').select2({
        allowClear: true
    });
    $('#service_id').select2({
        allowClear: true
    });
     $('#partner_id').select2({
        allowClear: true
    });
    $('#free_paid').select2({
        allowClear: true
    });
    $('#upcountry').select2({
        allowClear: true
    });
    $('#status').select2({
        allowClear: true
    });
$(function() {
        $('input[name="daterange_completed_bookings"]').daterangepicker({
             timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
    });
});
    </script>
    <style>
        .dataTables_length{
            display: none;
        }
        .dt-buttons{
            display: none;
        }
        .dataTables_filter{
                margin-top: -38px;
        }
        .select2-selection--multiple{
           width: 170px !important; 
        }
 </style>