<div class="right_col" role="main" style="padding: 30px;">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#state">State Wise serviceability Missing Report </a></li>
    <li><a data-toggle="tab" href="#district">District Wise serviceability Missing Report</a></li>
  </ul>

    <div class="tab-content" style="    margin-top: 15px;">
    <div id="state" class="tab-pane fade in active container">
         <table class="table table-striped table-bordered" id="state_table" > 
        <thead>
                <tr style="background: #2C9D9C;color: #fff;margin-top: 5px;">
                    <th>District</th> 
                    <th>Total Pincode</th> 
 <?php
                        foreach($services as $servicekey => $servicevalue){
                            ?>
                    <th><?php echo $servicevalue;?></th>
                  <?php
                  }
                    ?>
                 </tr>
        </thead>
    <tbody>
        <?php
        foreach($state_data as $state => $values){
            ?>
        <tr>
            <td><?php echo $values['state']?></td>
            <td><?php echo $values['total_india_pincode']?></td>
            <?php
            foreach($services as $serviceID => $serviceName){
                if(array_key_exists('appliance_'.$serviceID, $values)){
                ?>
            <td><?php
            $missing = $values['total_india_pincode'] - $values['appliance_'.$serviceID]['total_pincode'];
            if($missing < 0){
                $missing = 0; 
            }
            echo $missing?></td>
            <?php
                }
                else{
          ?>
            <td>-</td>
            <?php
                }
            }
            ?>
        </tr>
        <?php
            
        }
?>
    </tbody>
                            </table>
    </div>
    <div id="district" class="tab-pane fade">
<div id="district_table_holder">  <center><img id="loader_gif_completed_rm" src="<?php echo base_url(); ?>images/loadring.gif" ></center></div>     
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
   get_district_missing_servicablity_data();
    $('#state_table').DataTable( {
        dom: 'Blfrtip',
        buttons: ['excel', 'print'],
        order: [[ 16, "desc" ]],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );
function get_district_missing_servicablity_data(appliance_id){
        var data = {};
        url = '<?php echo base_url(); ?>employee/dashboard/get_district_missing_servicablity_data';
        data['rm_id'] = '<?php echo $rm_id ?>';
        //data['appliance_id'] = appliance_id;
        sendAjaxRequest(data,url,'POST').done(function(response){
           $("#district_table_holder").html(response);
        });
    }
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    </script>