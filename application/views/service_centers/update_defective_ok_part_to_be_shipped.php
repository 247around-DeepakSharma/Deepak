<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
}
?>
<div role="tabpanel" class="tab-pane" id="estimate_cost_given">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <h1><a class="btn btn-primary btn-sm" href="<?php echo base_url().'employee/service_centers/update_defective_parts_pending_bookings_download'; ?>" id="download" style="float:right;font-size:12px;color:white;margin-top:-6px;">Download List</a></h1>
                        <form target="_blank"  action="<?php echo base_url(); ?>employee/service_centers/print_partner_address_challan_file" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                        <table id="estimate_cost_given_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead>
                                    <tr>
                                        <th class="text-center">S.No.</th>
                                        <th class="text-center">Booking Id</th>
                                        <th class="text-center">User Name</th>
                                        <th class="text-center">Age of Pending</th>
                                        <th class="text-center">Part Received</th>
                                        <th class="text-center">Part Code </th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Consumption Reason</th>
                                        <th class="text-center">Consumption Remarks</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                    <?php if(!empty($spare_parts)) { foreach ($spare_parts as $key => $row) { ?>
                                        <tr id="spare_row_<?php echo $row['id']; ?>" style="text-align: center;<?php if (!is_null($row['remarks_defective_part_by_wh'])) {
                                            echo "color:red";
                                        } ?>">
                                            <td>
                                                <?php echo $sn_no; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id'])); ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                            </td>
                                            <td>
                                                <?php echo $row['name']; ?>
                                            </td>
                                            <td>
                                                <?php if (!is_null($row['service_center_closed_date'])) {
                                                    $age_shipped = date_diff(date_create($row['service_center_closed_date']), date_create('today'));
                                                    echo $age_shipped->days . " Days";
                                                } ?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['parts_shipped']; ?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['part_number']; ?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['shipped_quantity']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['challan_approx_value']; ?>
                                            </td>

                                            <td>
                                              
                                                <select style="width:100%;" name="spare_consumption_status_<?php echo $row['id']; ?>" class="spare_consumption_status" id="spare_consumption_status_<?php echo $row['id']; ?>" >
                                                    <option value="" selected disabled>Select Reason</option>
                                                    <?php foreach($spare_consumed_status as $k => $status) { ?>
                                                        <option value="<?php echo $status['id']; ?>" data-spare_id="<?php echo $row['id']; ?>"
                                                        ><?php echo $status['consumed_status']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control" id="consumption_remarks_<?php echo $row['id']; ?>" name="consumption_remarks_<?php echo $row['id']; ?>" rows="2"></textarea>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-primary save-consumption" name="save_<?php echo $row['id']; ?>" id="save_<?php echo $row['id']; ?>" data-booking_id="<?php echo $row['booking_id']; ?>">Save</a>
                                            </td>
                                        </tr>
                                    <?php $sn_no++;
                                    } } else {
                                        echo '<tr><td colspan="11">No Data Found.</td></tr>';
                                    }?>
                                </tbody>
                        </table>
                          
                            </form>
                        
                        <div class="custom_pagination" style="margin-left: 16px;" > <?php if (isset($links)) echo $links; ?></div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="loader hide"></div>
<style>
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('<?php echo base_url(); ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
    }

    .sweet-alert {

        width: 700px !important;
        left: 46% !important;
    }
    .modal-lg {
        /* width: 1300px; */
        width: 95% !important;
    }
    .form-control{
        margin-bottom: 10px;
    }
    .input-group{
        margin-bottom: 10px; 
    }
</style>
<script>
    $(".spare_consumption_status").select2();
     
    $(document).ready(function(){
        $('.save-consumption').on('click', function(){
            var btn_id = $(this).attr('id');
            // disabled button after click
            $('#'+btn_id).text('Please wait...');
            $('#'+btn_id).attr('disabled', true);
            
            var spare_id = $(this).attr('id').split('_')[1];
            var booking_id = $(this).data('booking_id');
            var consumption_reason = $('#spare_consumption_status_'+spare_id).val();
            var consumption_remarks = $('#consumption_remarks_'+spare_id).val();
            
            if(consumption_reason == null || consumption_reason == '') {
                alert('Please select consumption reason');
                $(".loader").addClass('hide');
                // enable button after click
                $('#'+btn_id).text('Save');
                $('#'+btn_id).attr('disabled', false);
                return false;
            }
            $.ajax({
                type:'POST',
                async: false,
                url:"<?php echo base_url(); ?>employee/service_centers/update_spare_consumption_reason",
                data:{'booking_id':booking_id, 'spare_id':spare_id, 'consumption_reason':consumption_reason, 'consumption_remarks':consumption_remarks},
            }).done(function(data) {
                alert('Data has been saved successfully.');
                $('#spare_row_'+spare_id).css('display','none');
            });
        });
    }); 
</script>