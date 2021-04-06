<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
}
?>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
            }
            ?> 
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h1 class="panel-title" >Spare Parts Delivered To SF</h1>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                    <table id="delivered_parts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Booking Id</th>
                                <th class="text-center">User Name</th>
                                <th class="text-center">Requested<br>Part Number</th>
                                <th class="text-center">Requested<br>Part Name</th>                                
                                <th class="text-center">Requested<br>Part Type</th>
                                <th class="text-center">Requested<br>Quantity</th>
                                <th class="text-center">Shipped<br>Part Number</th>
                                <th class="text-center">Shipped<br>Part Name</th>                                
                                <th class="text-center">Shipped<br>Part Type</th>
                                <th class="text-center">Shipped<br>Quantity</th>
                                <th class="text-center">Acknowledge<br>Date</th>
                                <th class="text-center">Consumption Reason</th>
                                <th class="text-center">Consumption Remarks</th>
                                <th class="text-center" colspan="3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($spare_parts)) { foreach($spare_parts as $key => $spare_part) {
                             
                             if ($spare_part['auto_acknowledeged'] == AUTO_ACKNOWLEDGED_FROM_API && $spare_part['status'] == SPARE_DELIVERED_TO_SF) {
                                $spare_auto_acknowleged = array("spare_id" => $spare_part['id'], "booking_id" => $spare_part['booking_id'], "is_micro_wh" => $spare_part['is_micro_wh'], "status" => $spare_part['status'], "auto_acknowledeged" => $spare_part['auto_acknowledeged'], "courier_pod_file" => $spare_part['courier_pod_file']);
                            } else {
                                $spare_auto_acknowleged = array();
                            }                                
                                ?>
                            <tr> 
                                <td><?php echo $sn_no; ?></td>
                                <td>
                                    <a href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($spare_part['booking_id'])); ?>"  title='View'><?php echo $spare_part['booking_id']; ?></a>
                                </td>
                                 <td>
                                    <?php echo $spare_part['name']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['part_number']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['parts_requested']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['parts_requested_type']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['quantity']; ?>
                                </td>
                                <td>
                                    <?php echo $spare_part['shipped_part_number']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['parts_shipped']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['shipped_parts_type']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['shipped_quantity']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $this->miscelleneous->get_formatted_date($spare_part['acknowledge_date']); ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['consumed_status']; ?>
                                </td>
                                <td style="word-break: break-all;">
                                    <?php echo $spare_part['consumption_remarks']; ?>
                                </td>
                               
                                <td style="white-space: no-wrap;">
                                    <?php if($spare_part['consumed_part_status_id'] == PART_CONSUMED_STATUS_ID) { ?>
                                            Part Consumed
                                    <?php }elseif(!empty($spare_auto_acknowleged)) { ?>
                                        <a href="javascript:void(0);" class="spare_auto_delivered btn btn-primary" data-courier-auto-delivered='<?php echo json_encode(array($spare_auto_acknowleged)); ?>'>Part Not Received</a>  
                                    <?php } else { ?>
                                        Acknowledged    
                                    <?php } ?>
                               </td>
                               <td style="word-break: break-all;">
                                   <a href="javascript:void(0);" class="btn btn-primary" onclick="change_consumption(<?php echo $spare_part['id']; ?>)" title="Change Consumption Reason"><span class="glyphicon glyphicon-pencil"></span></a>
                               </td>
                            </tr>
                            <?php $sn_no++;} } else { ?>
                            <?php }?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if (isset($links)) echo $links; ?></div>
</div>

<div id="ChangeConsumptionModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="change_consumption_spare_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Consumption Reason</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<div id="partsNotDelivered" class="modal fade in" role="dialog" aria-hidden="false">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">Part Not Received </h3>
            </div>
            <div class="modal-body">
                <div id="html_body_id"></div>
            </div>
        </div>
    </div>
</div>
<script>
    function is_confirmed(obj) {
        if(confirm('Are you sure you want to mark spare part as courier lost?')) {
            return true;
        } else {
            return false;
        }
    }
    
    function change_consumption(spare_id) {
        $.ajax({
            method:'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/change_consumption_by_sf',
            data: {spare_id}
        }).done(function (data){
            $("#change_consumption_spare_model").children('.modal-content').children('.modal-body').html(data);   
            $('#ChangeConsumptionModal').modal({backdrop: 'static', keyboard: false});
        });
    }
    
   /*
    * Desc: Part not received by SF to option madal
    */

    $(document).on('click', '.spare_auto_delivered', function () {
        var spare_parts_json = $(this).attr("data-courier-auto-delivered");
        var spare_parts_obj = JSON.parse(spare_parts_json);
        if(spare_parts_obj.length > 0){
          $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/service_centers/validate_spare_parts_to_marked_not_received_sf',
                data: {spare_parts_data: spare_parts_obj},
                success: function (htmlData) {
                    $("#html_body_id").html(htmlData);
                    $('#partsNotDelivered').modal('toggle'); 
                }
            });
        }
    });

    /*
     * Desc: This function is used to update details that spare not received by sf
     */
     
    function spare_parts_not_received() {
        event.preventDefault();
        var formData = new FormData(document.getElementById("part_not_received"));
        remarks = $("#part_not_received_remarks").val();
        var checkbox_flag = '';
        $(".auto_acknowledge_data").each(function(){
            if($(this).is(":checked") == true){
                checkbox_flag = 1;
            }
        });
        
        if (checkbox_flag == '') {
           swal(messageConstant[0]['247around006']);
           return false; 
        }
        
        if (remarks != '') {
            $("#submit_button_id").prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/service_centers/process_to_part_not_received_by_sf',
                data: formData,
                dataType: "json",
                processData: false,  // tell jQuery not to process the data
                contentType: false, 
                success: function (response) {
                    swal(messageConstant[0][response['error_code']]);
                    if(response['status']){
                        window.location.reload();
                       $('#partsNotDelivered').modal('toggle'); 
                    }
                    $("#submit_button_id").prop('disabled', false);
                }
            });
        } else {
            swal(messageConstant[0]['247around001']);
            return false;
        }
    }
</script>