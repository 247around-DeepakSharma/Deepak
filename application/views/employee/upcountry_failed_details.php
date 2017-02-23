<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Upcountry Details </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive table-editable" id="table" >
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                           <th>S No.</th>
                           <th>Booking ID</th>
                           <th>Booking City</th>
                           <th>Booking Pincode</th>
                           <th>District Pincode</th>
                           <th>Distance</th>
                           <th>Action</th>
  
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($details as $value) { ?>
                          <tr id="<?php echo "table_tr_". $sn_no;?>">
                              <td><?php echo $sn_no; ?></td>
                              <td><?php echo $value['booking_id']; ?></td>
                              <td><?php echo $value['city']; ?></td>
                               <td><?php echo $value['booking_pincode']; ?></td>
                               <td><?php echo $value['upcountry_pincode']; ?></td>
                             
                               <td contenteditable="true" id="<?php echo "distance_".$sn_no; ?>">0</td>
                               <td><button class="btn btn-primary" 
                                           onclick="submit_button('<?php echo $value["booking_id"];?>',
                                           '<?php echo $sn_no; ?>')">Submit</button></td>
                               </tr>
                             
                        <?php $sn_no++; }?>
                        
                     </tbody>
                  </table>
               </div>
            </div>
           </div>
           
       </div>
   </div>

</div>

</div>

<style>

table {
  word-wrap:break-word;
    table-layout:fixed;
}

</style>
<script type="text/javascript">
    $(".sc_pincode").select2();
    function submit_button(booking_id, div_no){

        var distance = $("#distance_"+ div_no).text();
      
        var event_taget = event.target;
        var event_element = event.srcElement;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/upcountry/update_failed_upcountry_booking',
            data: {distance:distance, 
              booking_id:booking_id},
            success: function (data) {
               // console.log(data);
             }
          });
          $(event_taget || event_element).parents('tr').hide();

              
      
    }
    </script>
