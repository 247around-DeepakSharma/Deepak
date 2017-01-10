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
                           <th>Pincode</th>
                           <th>Distance</th>
                           <th>Action</th>
  
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($details as $value) { ?>
                          <tr id="<?php echo "table_tr_". $sn_no;?>">
                              <td><?php echo $sn_no; ?></td>
                              <td><?php echo $value['booking_id']; ?></td>
                             
                               <td>

                                   <select name="sc_id_pincode" class="form-control sc_pincode" id="<?php echo "sub_service_center_id_".$sn_no; ?>" >
                                       <option disabled selected> Please Select District & Pincode</option>
                                       <?php foreach ($value['pincode_details'] as $sub_vendor) { ?>
                                       <option value="<?php echo $sub_vendor['id']."-".$sub_vendor['pincode']."-".$sub_vendor['upcountry_rate'];?>"> <?php echo $sub_vendor['district']." - ".$sub_vendor['pincode'];?></option>;
                                       <?php } ?>
                                  
                                  </select>
                               </td>
                               <td contenteditable="true" id="<?php echo "distance_".$sn_no; ?>">0</td>
                               <td><button class="btn btn-primary" onclick="submit_button('<?php echo $value["booking_id"];?>','<?php echo $sn_no; ?>')">Submit</button></td>
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
      
        var sub_sc_id_pincode = $("#sub_service_center_id_"+ div_no).val();
        if(sub_sc_id_pincode !== null){
            var sub_sc_id_pincode_array = sub_sc_id_pincode.split('-');
            var sc_id = sub_sc_id_pincode_array[0];
            var pincode = sub_sc_id_pincode_array[1];
            var upcountry_rate = sub_sc_id_pincode_array[2];
            var distance = $("#distance_"+ div_no).text();
            var event_taget = event.target;
            var event_element = event.srcElement;
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/upcountry/update_failed_upcountry_booking',
                data: {sc_id: sc_id, pincode: pincode, distance:distance, 
                  booking_id:booking_id,upcountry_rate:upcountry_rate},
                success: function (data) {
                  $(event_taget || event_element).parents('tr').hide();
                   
                 }
              });
              
              
        } else {
           alert("Please Select District-Pincode");
        }
    }
    </script>
