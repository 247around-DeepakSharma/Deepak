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
                    <a id="download_pin_code" class="btn btn-info" href="download_missing_sf_pincode_excel/<?php echo $agent ?>" style="float:right">Download Excel</a>
                    <a id="download_pin_code" class="btn btn-success" href="<?php echo base_url(); ?>employee/vendor/insert_pincode_form" style="float:right">Add New Pincode</a>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
<div class="btn-group" role="group">
    <button type="button" class="btn btn-success" href="#tab1" data-toggle="tab">
        <div class="hidden-xs"> Pincode Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-success" href="#tab2" data-toggle="tab">
        <div class="hidden-xs"> District Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-success" href="#tab3" data-toggle="tab">
        <div class="hidden-xs"> Partner Level</div>
    </button>
</div>
<div class="btn-group" role="group">
    <button type="button" class="btn btn-success" href="#tab4" data-toggle="tab">
        <div class="hidden-xs"> Appliance Level</div>
    </button>
</div>
</div>
                    <div class="well">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1">
                                <table class="table table-bordered">
                                    <tr style="background: #2a3f54;color: #fff;">
                                        <th>S.N</th>
                                        <th>Pincode</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Pending Query Count</th>
                                    </tr>
                                <?php
                                $sn = 1;
                                foreach($pincodeResult as $pincode=>$pincodeData){
                                    ?>
                                    <tr>
                                        <td><?php echo $sn;?></td>
                                        <td><button  onclick='missingPincodeDetailedView(<?php echo json_encode($pincodeData)?>)' style="margin: 0px;padding: 0px 6px;background: #26b99a;" type="button" class="btn btn-info btn-lg" data-toggle="modal" 
                                                    data-target="#missingPincodeDetails"><?php echo $pincode;?></button></td>
                                        <td><?php echo $pincodeData['state'];?></td>
                                        <td><?php echo $pincodeData['city'];?></td>
                                        <td><?php echo $pincodeData['count'];?></td>
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
    </script>

