
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>Upcountry Details </h2>
            </div>
            <div class="panel-body">
                <div class="success" style="display:none">
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>Details have been Updated Successfully</div>
                </div>
                <div class="error" style="display:none">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>Error in updating Details</div>
                </div>
               <div class="table-responsive table-editable" id="table" >
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                           <th>S No.</th>
                           <th>State</th>
                           <th>City</th>
                           <th>Pincode</th>
                           <th>Upcountry Rate (Per KM)</th>
                           <th>Action</th>
  
                         </tr>
                     </thead>
                     <tbody>
                         <?php $sn_no =1; foreach ($data as $value) { ?>
                          <tr id="<?php echo "table_tr_". $sn_no;?>">
                              <td><?php echo $sn_no; ?></td>
                              <td><?php echo $value['state']; ?></td>
                              <td contenteditable="true" id="<?php echo "district".$sn_no; ?>" ><?php echo $value['district']; ?></td>
                              <td contenteditable="true" id="<?php echo "pincode".$sn_no; ?>" class='allownumericwithdecimal'><?php echo $value['pincode']; ?></td>
                              <td contenteditable="true" id="<?php echo "upcountry_rate".$sn_no; ?>" class='allownumericwithdecimal'><?php echo $value['upcountry_rate']; ?></td>
                              <td><button class="btn btn-primary" 
                                           onclick="submit_button('<?php echo $value["id"];?>',
                                           '<?php echo $sn_no; ?>','<?php echo $value["service_center_id"];; ?>')">Submit</button></td>
                               
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
    function submit_button(id, div_no,service_center_id){

        var district = $("#district"+ div_no).text();
        var pincode = $("#pincode"+ div_no).text();
        var upcountry_rate = $("#upcountry_rate"+ div_no).text();
      
        var event_taget = event.target;
        var event_element = event.srcElement;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/update_sub_service_center_details',
            data: {district:district, pincode:pincode,upcountry_rate:upcountry_rate,id:id,service_center_id:service_center_id},
            success: function (data) {
                if(data === 'success'){
                    $('.success').show().delay(5000).fadeOut();;
                }else{
                    $('.error').show().delay(5000).fadeOut();;
                }
             }
          });
          $(event_taget || event_element).parents('tr').hide();

              
      
    }
    
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
    </script>
