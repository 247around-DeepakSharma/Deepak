 <table class="table table-striped table-bordered" id="district_table" style="margin-top: 25px;"> 
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
        foreach($district_data as $state => $values){
            ?>
        <tr>
            <td><?php echo $values['City']?></td>
            <td><?php echo $values['total_india_pincode']?></td>
            <?php
            foreach($services as $serviceID => $serviceName){
                if(array_key_exists('appliance_'.$serviceID, $values)){
                ?>
            <td><?php
            echo ($values['appliance_'.$serviceID]['missing_pincode'])?></td>
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
<script>
    $(document).ready(function() {
   var no_ajax_refresh ="<?php if(empty($no_ajax_refresh)){echo 1;}else{echo 0;} ?>";
   //Added above line to stop recursively call below function as same page load during ajax call and initiate another request
   if(no_ajax_refresh==1){
       get_district_missing_servicablity_data();
   }
    $('#district_table').DataTable( {
        dom: 'Blfrtip',
        buttons: ['excel', 'print'],
        order: [[ 16, "desc" ]],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );
    </script>