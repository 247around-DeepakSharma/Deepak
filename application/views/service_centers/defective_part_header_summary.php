<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th >Defective Part to be Shipped</th>
            <td class="text-center"><a href="javascript:void(0)" onclick="get_defective_spare_count('<?php echo $this->session->userdata('service_center_id');?>', 'get_pending_defective_parts_list')"><?php echo (!empty($defective_part))? $defective_part[0]['count']:"0"; ?></a></td>
            <th >Max Age of Defective Part</th>
            <td class="text-center"> <?php echo (!empty($defective_part))? $defective_part[0]['max_sp_age']:"0"; ?></td>
            <th >Challan Approx Value</th>
            <td class="text-center"> <i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($defective_part))? $defective_part[0]['challan_value']:"0"; ?></td>
            
        </tr>
        <tr>
            <th >OOT Shipped Part</th>
            <td class="text-center"><a href="javascript:void(0)" onclick="get_defective_spare_count('<?php echo $this->session->userdata('service_center_id');?>', 'get_oot_shipped_defective_parts')"><?php echo (!empty($oot_shipped))? $oot_shipped[0]['count']:"0"; ?></a></td>
            <th >Max Age of OOT Shipped Part</th>
            <td class="text-center"> <?php echo (!empty($oot_shipped))? $oot_shipped[0]['max_sp_age']:"0"; ?></td>
            <th >OOT Challan Approx Value</th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($oot_shipped))? $oot_shipped[0]['challan_value']:"0"; ?></td>
            
        </tr>
        <tr>
            <th >Defective Part Shipped</th>
            <td class="text-center"><a href="javascript:void(0)" onclick="get_defective_spare_count('<?php echo $this->session->userdata('service_center_id');?>', 'get_intransit_defective_parts')"><?php echo (!empty($shipped_parts))? $shipped_parts[0]['count']:"0";; ?></a></td>
            <th >Max Age of Shipped Defective Part</th>
            <td class="text-center"> <?php echo (!empty($shipped_parts))? $shipped_parts[0]['max_sp_age']:"0"; ?></td>
            <th >Challan Approx Value</th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo (!empty($shipped_parts))? $shipped_parts[0]['challan_value']:"0"; ?></td>
            
        </tr>
    </thead>
</table>
<!--Invoice Defective Part Pending Booking with Age Modal-->
    <div id="defective_part_pending_booking_age" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <table class="table table-bordered  table-hover table-striped data">
                      <thead>
                        <th>SN</th>
                        <th>Booking ID</th>
                        <th>Shipped Part Type</th>
                        <th>Pending Age</th>
                        <th>Challan Approx Value</th>
                      </thead>
                      <tbody id="defective-model">
                          
                      </tbody>
                  </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<script>
function get_defective_spare_count(vendor_id, function_name){
    
    $.ajax({
        type:"POST",
        beforeSend: function(){

                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                });

        },
        url: "<?php echo base_url(); ?>employee/invoice/"+function_name+"/" + vendor_id,
        success:function(response){
            //console.log(response);
            if(response === "DATA NOT FOUND"){
                $('body').loadingModal('destroy');
                alert("DATA NOT FOUND");
            } else {
               $("#defective-model").html(response);   
               $('#defective_part_pending_booking_age').modal('toggle'); 
               $('body').loadingModal('destroy');
            }
            
        }
    });
    
}
</script>