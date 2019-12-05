<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
        }
        ?>
    <div class="row">
<?php } ?>
        <style>
            .dataTables_length{
                width: 250px;
                float: left
            }
            .dataTables_filter{
                float: right;
            }
            .table.dataTable thead .sorting:after {
              opacity: 1;            
            }
                .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    height: 100%;
    z-index: 9999999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
        </style>
        <div class="loader"></div>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <button class="btn btn-success pull-right" id="revieve_multiple_parts_btn">Recieve Multiple Parts</button>
            <h2>Defective Parts Shipped By SF</h2>
            <div class="clearfix"></div>

            
        </div>
        <hr>
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="defective_spare_shipped_by_sf">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">SF Name</th>
                            <th class="text-center">SF City</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Shipped Quantity</th>
                            <th class="text-center">Parts Code</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Consumption</th>
                            <th class="text-center">Consumption Reason</th>                            
                            <th class="text-center">Received</th>
                            <th class="text-center">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sn_no = 1;
                        foreach ($spare_parts as $key => $row) {

                            $spareStatus = DELIVERED_SPARE_STATUS;
                            if (!$row['defactive_part_received_date_by_courier_api']) {
                                $spareStatus = $row['status'];
                            }
                            ?>
                            <tr style="text-align: center;<?php if($row['defective_part_rejected_by_partner']==1){echo "background-color: #d89e9e !important;font-weight: 900";} ?>">
                                <td>
                                    <?php echo $sn_no; ?>
                                </td>
                                <td>
                                    <a  style="color:black" href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                </td>
                                <td>
                                    <?php echo $row['user_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['sf_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['sf_city']; ?>
                                </td>
    <!--                                    <td>
                                    <?php //echo $row['age_of_booking'];  ?>
                                </td>-->
                                <td style="word-break: break-all;">
                                    <?php echo $row['defective_part_shipped']; ?>
                                </td>

                                <td style="word-break: break-all;">
                                    <?php echo $row['shipped_quantity']; ?>
                                </td>
                                
                                <td style="word-break: break-all;">
                                    <?php echo $row['part_number']; ?>
                                </td>
                                <td>
                                    <?php echo $row['courier_name_by_sf']; ?>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="get_awb_details('<?php echo $row['courier_name_by_sf']; ?>','<?php echo $row['awb_by_sf']; ?>','<?php echo $spareStatus; ?>','<?php echo "awb_loader_".$sn_no; ?>')"><?php echo $row['awb_by_sf']; ?></a> 
                                    <span id="<?php echo "awb_loader_".$sn_no; ?>" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                                </td>
                                <td>
                                    <?php if (!is_null($row['defective_part_shipped_date'])) {
                                        echo date("d-m-Y", strtotime($row['defective_part_shipped_date']));
                                    } ?>
                                </td>
                                    <td style="width: 15% !important;">
                                <?php echo $row['remarks_defective_part_by_sf']; ?>
                                </td>
                                <td><?php if($row['is_consumed'] == 1) { echo 'Yes'; } else { echo 'No';} ?></td>
                                <td><?php echo $row['consumed_status']; ?></td>                                
                                <td>
                                <?php if (!empty($row['defective_part_shipped'])) { ?> 
                                    <a class="btn btn-sm btn-primary recieve_defective" id="defective_parts_<?php echo $row['id']; ?>" onclick="return confirm_received(this.id)" href="<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $row['id']; ?>/<?php echo $row['booking_id']; ?>/<?php echo $row['partner_id']; ?>/0" <?php echo empty($row['defective_part_shipped']) ? 'disabled="disabled"' : '' ?>>Received</a> <input type="checkbox" class="checkbox_revieve_class" name="revieve_checkbox"  data-url="<?php echo base_url(); ?>service_center/acknowledge_received_defective_parts/<?php echo $row['id']; ?>/<?php echo $row['booking_id']; ?>/<?php echo $row['partner_id']; ?>/1"  />
                                <?php } ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['defective_part_shipped'])) { ?>
                                        <div class="dropdown" >
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-danger" type="button" data-toggle="dropdown">Reject
                                                <span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="right: 0px;left: auto;">
                                                <?php foreach ($internal_status as $value) { ?>
                                                    <li><a href="<?php echo base_url(); ?>service_center/reject_defective_part/<?php echo $row['id']; ?>/<?php echo $row['booking_id']; ?>/<?php echo urlencode(base64_encode($row['partner_id'])); ?>/<?php echo urlencode(base64_encode($value->status)); ?>"><?php echo $value->status; ?></a></li>
                                                    <li class="divider"></li>
                                                <?php } ?>

                                            </ul>

                                        </div>
                                    <?php } ?>
                                </td>


                            </tr>
                        <?php $sn_no++;
                    } ?>
                    </tbody>
                </table>
        </div>
    </div>
</div>
        <script>
            $('#defective_spare_shipped_by_sf').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Export',
                        exportOptions: {
                            columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10,11,12,13]
                        },
                        title: 'defective_spare_shipped_by_sf_to_wh'
                    }
                ],
                "bSortClasses": false,
                "pageLength":2000,
            });
        </script>
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
function confirm_received(id){
    $("#"+id).attr('disabled',true);
    var c = confirm("Continue?");
    if(!c){
        $("#"+id).attr('disabled',false);
        return false;
    }
}

$("#revieve_multiple_parts_btn").click(function(){
$("#revieve_multiple_parts_btn").attr('disabled',true);
$(".recieve_defective").attr('disabled',true);
$(".loader").css("display","block !important");
var flag=false;
var url = new Array();
$('.checkbox_revieve_class').each(function () {
    if (this.checked) { 
        url.push($(this).attr("data-url"));
        flag=true;
    }
});

if(flag) {
    $('.checkbox_revieve_class').prop('checked', false);
    for (var index in url)
    {
        $.ajax({
            type: "POST",
            url: url[index],
            async: false,
            success: function(data)
            {
                console.log("Receiving");
            }
        });
    }
 swal("Received!", "Your all selected spares are received !.", "success");
     $(".loader").css("display","none");
     location.reload();
}
else {
    alert("Please Select At Least One Checkbox");
    $("#revieve_multiple_parts_btn").attr('disabled',false);
    $(".recieve_defective").attr('disabled',false);
}


});


function get_awb_details(courier_code,awb_number,status,id){
        if(courier_code && awb_number && status){
            $('#'+id).show();
            $.ajax({
                method:"POST",
                data : {courier_code: courier_code, awb_number: awb_number, status: status},
                url:'<?php echo base_url(); ?>courier_tracking/get_awb_real_time_tracking_details',
                success: function(res){
                    $('#'+id).hide();
                    $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                    $('#gen_model_body').html(res);
                    $('#gen_model').modal('toggle');
                }
            });
        }else{
            alert('Something Wrong. Please Refresh Page...');
        }
    }



</script>
