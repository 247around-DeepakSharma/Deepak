<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">

   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
      
         <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Received Defective Parts</h1>
                    </div>
                    <div class="approved col-md-6">
                        <div class="btn btn-info btn-sm send_all_spare pull-right" onclick="process_send_all_spare();">Send spare to partner</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="success_msg_div" style="display:none;">
                    <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><span id="success_msg"></span></strong>
                    </div>
                </div>
                <div class="error_msg_div" style="display:none;">
                    <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><span id="error_msg"></span></strong>
                    </div>
                </div>
               <div class="table-responsive">
                  
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">Defective Parts Shipped</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">
                                Send To Partner
                                <input type="checkbox" id="send_all">
                            </th>
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php  foreach($spare_parts as $key =>$row){?>
                               <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['defective_part_shipped']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['defective_part_shipped_date'])){  echo date("d-m-Y",strtotime($row['defective_part_shipped_date'])); }  ?>
                                    </td>
                                   <td>
                                        <?php echo $row['awb_by_sf']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['courier_name_by_sf']; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['remarks_defective_part_by_sf']; ?>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="check_single_row" data-shipped_inventory_id = "<?php echo $row['shipped_inventory_id']?>" data-booking_id ="<?php echo $row['booking_id']?>" data-partner_id = "<?php echo $row['partner_id']?>" data-spare_id = "<?php echo $row['id']?>">
                                    </td>
                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
                      
                        </div>
                   
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>

<script>
    $('#send_all').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });
    
    function process_send_all_spare(){
        var tmp_arr = {};
        var postData = {};
        var flag = false;
        $(".check_single_row:checked").each(function (key) {
            tmp_arr[key] = {};
            tmp_arr[key]['inventory_id'] = $(this).attr('data-shipped_inventory_id');
            tmp_arr[key]['booking_id'] = $(this).attr('data-booking_id');
            tmp_arr[key]['partner_id'] = $(this).attr('data-partner_id');
            tmp_arr[key]['spare_id'] = $(this).attr('data-spare_id');
            flag = true;
        });
        
        postData['data'] = JSON.stringify(tmp_arr);
        postData['sender_entity_id'] =  '<?php echo $this->session->userdata('service_center_id')?>';
        postData['sender_entity_type'] = '<?php echo _247AROUND_SF_STRING; ?>';
        
        if(flag){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/send_defective_parts_to_partner_from_wh',
                data:postData,
                success:function(response){
                    obj = JSON.parse(response);
                    if(obj.status){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(obj.message);
                        window.location.reload();
                    }else{
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(obj.message);
                    }
                }
            });
        }else{
            alert("Please Select At Least One Checkbox");
        }
    }
</script>