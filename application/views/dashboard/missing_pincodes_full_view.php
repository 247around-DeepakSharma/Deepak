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
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Missing Pincodes</h2>
                    <a id="download_pin_code" class="btn btn-info" href="<?php echo base_url() ?>employee/dashboard/download_missing_sf_pincode_excel/<?php echo $agent ?>" style="float:right">Download Excel</a>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab1" data-toggle="tab">
        <div class="hidden-xs"> Pincode Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab2" data-toggle="tab">
        <div class="hidden-xs"> District Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab3" data-toggle="tab">
        <div class="hidden-xs"> Partner Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-info" href="#tab4" data-toggle="tab">
        <div class="hidden-xs"> Appliance Level</div>
    </button>
</div>
</div>
                    <div class="tab-content" style="margin-top: 10px;">
                            <div class="tab-pane fade in active" id="tab1">
                                <table class="table table-bordered">
                                    <tr style="background: #405467;color: #fff;">
                                        <th>S.N</th>
                                        <th>Pincode</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Pending Query Count</th>
                                        <th>action</th>
                                    </tr>
                                <?php
                                $sn = 1;
                                foreach($pincodeResult as $pincode=>$pincodeData){
                                    ?>
                                    <tr>
                                        <td><?php echo $sn;?></td>
                                        <td><button  onclick='missingPincodeDetailedView(<?php echo json_encode($pincodeData)?>)' style="font-size: 15px;margin: 0px;padding: 3px 11px;background: #5bc0de;" type="button" class="btn btn-info btn-lg" data-toggle="modal" 
                                                    data-target="#missingPincodeDetails"><?php echo $pincode;?></button></td>
                                        <td><?php echo $pincodeData['state'];?></td>
                                        <td><?php echo $pincodeData['city'];?></td>
                                        <td><?php echo $pincodeData['count'];?></td>
                                        <td><button style="margin: 0px;padding: 6px;" class="btn btn-info " onclick='submitPincodeForm(<?php echo $pincode; ?>,<?php echo json_encode($pincodeData) ?>)'>
                                                Add Service Center</button>
                                            <a style="margin: 0px;padding: 6px;float:right;" class="btn btn-info " href="<?php echo base_url(); ?>employee/dashboard/wrong_pincode_handler/<?php echo $pincode ?>">Wrong Pincode</a>
                                        </td>
                                    </tr>   
                                    <?php
                                    $sn++;
                                }
                                ?>
                                </table>
                                </div>
                            <div class="tab-pane fade in" id="tab2"></div>
                            <div class="tab-pane fade in" id="tab3"></div>
                            <div class="tab-pane fade in" id="tab4"></div>
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
<div id="missingPincodeDetails" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Missing Pincodes Detailed View</h4>
      </div>
      <div class="modal-body">
          <table class="table table-bordered" id="mssingPincodeTable">
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
 <form method='post' action="<?php echo base_url() ?>employee/vendor/insert_pincode_form" id='pincodeForm' target='_blank'>";
        <input type='hidden' value='' name='pincode' id='pincode'>
        <input type='hidden' value='' name='city' id='city'>
        <input type='hidden' value='' name='state' id='state'>
        <input type='hidden' value='' name='service' id='service'>
</form>
<script>
     function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    $(document).ready(function(){
        get_data_group_by_district();
        get_data_group_by_partner();
        get_data_group_by_appliance();
    });
        function get_data_group_by_district(){
        var data = {};
         url = '<?php echo base_url(); ?>employee/dashboard/get_missing_pincode_data_group_by_district/<?php echo $agent?>';
        data = '';
        post_request = 'post';
        sendAjaxRequest(data,url,post_request).done(function(response){
            document.getElementById("tab2").innerHTML = response;
        });
    }
    function get_data_group_by_partner(){
         var data = {};
         url = '<?php echo base_url(); ?>employee/dashboard/get_missing_pincode_data_group_by_partner/<?php echo $agent?>';
        data = '';
        post_request = 'post';
        sendAjaxRequest(data,url,post_request).done(function(response){
            document.getElementById("tab3").innerHTML = response;
        });
    }
    function get_data_group_by_appliance(){
         var data = {};
         url = '<?php echo base_url(); ?>employee/dashboard/get_missing_pincode_data_group_by_appliance/<?php echo $agent?>';
        data = '';
        post_request = 'post';
        sendAjaxRequest(data,url,post_request).done(function(response){
            document.getElementById("tab4").innerHTML = response;
        });
    }
    function group_by_district_for_appliance(data){
        var tableString = '<table class="table table-bordered" id="mssingPincodeTable">';
        tableString = tableString += '<tr>';
        tableString = tableString += '<th>Pincode</th>';
        tableString = tableString += '<th>Pending Query Count</th>';
        tableString = tableString += '</tr>';
        var keys = Object.keys(data);
        var count = keys.length;
        for(var i=0;i<count;i++){
            tableString = tableString += '<tr>';
            tableString = tableString += '<td>'+keys[i]+'</td>';
            tableString = tableString += '<td>'+ data[keys[i]]+'</td>';
            tableString = tableString += '</tr>';
        }
        tableString = tableString += '</table>';
        document.getElementById("mssingPincodeTable").innerHTML = tableString;
    }
    function submitPincodeForm(pincode,data){
        var count = data.services.length;
        var finalObj = new Object();
        for(var i=0;i<count;i++){
            var obj = new Object();
            obj.service_id = data.service_id[i];
            obj.pincodeCount = data.services_count[i];
            obj.service_name = data.services[i];
            finalObj[i] = obj;
        }
        document.getElementById("pincode").value=pincode;
        document.getElementById("service").value=JSON.stringify(finalObj);
        document.getElementById("pincodeForm").submit();
    }
    </script>

