<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
</style>

<div class="right_col" role="main">
    <div class="row">

        <div class="clearfix"></div>
                <?php
                if($is_am == 1){
                     $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID."/0/1"; 
                }
                else{
                    $url =  base_url()."employee/dashboard/tat_calculation_full_view/".$rmID; 
                }
                ?>
        <form action="<?php echo $url?>" method="post">
       <div class="table_filter" style="background: #5bc0de;padding: 10px;margin-bottom: 10px;border-radius: 5px;">
           <div class="row">
               <?php  if(!$this->session->userdata('partner_id')){ ?>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="partner_id" name="partner_id">
                                <option value="" selected="selected" disabled="">Select Partner</option>
                                <?php foreach($partners as $val){ ?>
                                <option value="<?php echo $val['id']?>" <?php if(isset($filters['partner_id'])){if($filters['partner_id'] == $val['id']){echo 'selected="selected"';}} ?>><?php echo $val['public_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
               <?php } ?>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="service_id" name="services">
                                <option value="" selected="selected" disabled="">Select Service</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>" <?php if(isset($filters['services'])){if($filters['services'] == $val['id']){echo 'selected="selected"';}} ?>><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="request_type" name="request_type[]" multiple="">
                                <option value="Installation" <?php if(isset($filters['request_type'])){if(in_array("Installation", $filters['request_type'])){echo 'selected="selected"';}} ?>>Installations</option>
                                <option value="Repair_with_part" <?php if(isset($filters['request_type'])){if(in_array("Repair_with_part", $filters['request_type'])){echo 'selected="selected"';}} ?>>Repair With Spare</option>  
                                <option value="Repair_without_part" <?php if(isset($filters['request_type'])){if(in_array("Repair_without_part", $filters['request_type'])){echo 'selected="selected"';}} ?>>Repair Without Spare</option>  
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">  
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="" selected="selected" disabled="">Is Free</option>
                                <option value="Yes" <?php if(isset($filters['free_paid'])){if($filters['free_paid']=='Yes'){echo 'selected="selected"';}} ?>>Yes (In Warranty)</option>
                                <option value="No" <?php if(isset($filters['free_paid'])){if($filters['free_paid']=='No'){echo 'selected="selected"';}} ?>>No (Out Of Warranty)</option>  
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="upcountry" name="upcountry">
                                <option value="" selected="selected" disabled="">Is Upcountry</option>
                                <option value="Yes" <?php if(isset($filters['upcountry'])){if($filters['upcountry']=='Yes'){echo 'selected="selected"';}} ?>>Yes</option>
                                 <option value="No" <?php if(isset($filters['upcountry'])){if($filters['upcountry']=='No'){echo 'selected="selected"';}}?>>No</option>
                            </select>
                        </div>
                    </div>
                </div>
               <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 200px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <?php
                            $datrangeInitialValue =  date('Y-m-d', strtotime('-31 days'))." - ".date("Y-m-d");
                            ?>
                            <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id" style="width: 190px; height: 38px;" 
                                   value="<?php if(isset($filters['daterange_completed_bookings'])){echo $filters['daterange_completed_bookings'];} else{ echo $datrangeInitialValue;}?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="status" name="status">
                                <option value="" selected="selected" disabled="">Select Status</option>
                                <option value="Completed" <?php if(isset($filters['status'])){if($filters['status']=='Completed'){echo 'selected="selected"';}} ?>>Completed</option>
                                 <option value="Cancelled" <?php if(isset($filters['status'])){if($filters['status']=='Cancelled'){echo 'selected="selected"';}}?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 133px;">
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
                                    <input type="hidden" value='<?php echo json_encode($state);?>' name="data">
                                    <input type="submit" value="Download CSV" class="btn btn-primary" style="background: #405467;border: none;">
                                    </form>
                                <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_state_table">
    <thead>
        <tr style="background: #405467;color: #fff;margin-top: 5px;">
                            <th>S.no</th>
                            <th>States</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
                        </tr>
    </thead>
    <tbody>
        <?php
        foreach($state as $key => $values){
            ?>
        <tr>
            <td><?php echo $key+1   ;?></td>
            <td><button style="margin: 0px;padding: 3px 9px;font-size: 15px;" type="button" class="btn btn-info" id="district_level"><?php echo $values['entity']?></button></td>
            <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
            <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
            <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
            <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
            <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
            <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
            <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
            <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
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
                                    <input type="submit" value="Download CSV" class="btn btn-primary" style="background: #405467;border: none;">
                                    </form>
                               <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_sf_table">
    <thead>
        <tr style="background: #405467;color: #fff;margin-top: 5px;">
                            <th>S.no</th>
                            <th>Service Centers</th>
                            <th>D0</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5 - D7</th>
                             <th>D8 - D15</th>
                             <th>> D15</th>
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
            <td><button style="margin: 0px;padding: 3px 9px;font-size: 15px;text-align:left;" type="button" class="btn btn-info">
                <?php
                if($this->session->userdata('partner_id')){
                    if($values['id'] !="00"){
                        echo "247Around_Service_Center_".$values['id'];
                    }
                    else{
                        echo wordwrap($values['entity'], 30, "<br />\n");
                    }
                }
                else{
                    echo wordwrap($values['entity'], 30, "<br />\n");
                }
                ?>
           </button> </td>
            <td><?php echo $values['TAT_0'] ."<br>(". $values['TAT_0_per']."%)";?></td>
            <td><?php echo $values['TAT_1'] ."<br>(". $values['TAT_1_per']."%)";?></td>
            <td><?php echo $values['TAT_2'] ."<br>(". $values['TAT_2_per']."%)";?></td>
            <td><?php echo $values['TAT_3'] ."<br>(". $values['TAT_3_per']."%)";?></td>
            <td><?php echo $values['TAT_4'] ."<br>(". $values['TAT_4_per']."%)";?></td>
            <td><?php echo $values['TAT_5'] ."<br>(". $values['TAT_5_per']."%)";?></td>
            <td><?php echo $values['TAT_8'] ."<br>(". $values['TAT_8_per']."%)";?></td>
            <td><?php echo $values['TAT_16'] ."<br>(".$values['TAT_16_per']."%)";?></td>
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
    $('#request_type').select2();
     $('#partner_id').select2({
        placeholder: "Select Partner",
        allowClear: true
    });
    $('#free_paid').select2({
        placeholder: "Is Free",
        allowClear: true
    });
    $('#upcountry').select2({
        placeholder: "Is Upcountry",
        allowClear: true
    });
    $('#service_id').select2({
        placeholder: "Select Appliance",
        allowClear: true
    });
    $('#status').select2({
        placeholder: "Select Status",
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