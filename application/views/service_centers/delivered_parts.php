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
                                <th class="text-center" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($spare_parts)) { foreach($spare_parts as $key => $spare_part) { ?>
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
                                <td>
                                    <?php echo $spare_part['parts_requested']; ?>
                                </td>
                                <td>
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
                                    <?php }elseif(!empty($spare_part['auto_acknowledeged']) && in_array($spare_part['auto_acknowledeged'], [1,2])) { ?>
                                        <a href="<?php echo base_url(); ?>service_center/update_courier_lost/<?php echo $spare_part['id']; ?>" class="btn btn-primary" name="courier_lost_<?php echo $spare_part['id']; ?>" onclick="return is_confirmed(this);">Courier Lost</a>  
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
</script>