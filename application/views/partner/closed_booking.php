<?php if ($this->uri->segment(4)) {
    $count = $this->uri->segment(4) + 1;
} else {
    $count = 1;
} ?>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php echo $status." Bookings" ?></h2>
                     <div class="right_holder" style="float:right;margin-right:10px;">
                         <a style="float: right;background: #2a3f54;border-color: #2a3f54;margin-left: 10px;height: 32px;"type="button" class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/partner/download_partner_pending_bookings/<?php echo $this->session->userdata('partner_id')?>/<?php echo $status ?>">Download</a>
                            <lable>State</lable>
                            <select class="form-control " id="serachInputCompleted" style="border-radius:3px;">
                    <option value="all">All</option>
      <?php
      foreach($states as $state){
          ?>
      <option value="<?php echo $state['state'] ?>"><?php echo $state['state'] ?></option>
      <?php
      }
      ?>
  </select>            
</div>
                                        <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped" id="complete_booking_table">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Booking ID</th>
                                <th>Order ID</th>
                                <th>Call Type</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Booking Date</th>
                                <th><?php if ($status == "Cancelled") { echo 'Cancelled Date'; } else { echo 'Completion Date'; }?></th>
                                <?php if ($status != "Cancelled") { ?>
                                <th>TAT (Days)</th>

                                <?php } ?>
                                <?php if ($status == "Cancelled") { ?>
                                    <th>Cancellation Reason</th>
<!--                                    <th>Open</th>-->
                                <?php } ?>


                            </tr>
                        </thead>
                        <tbody>
                                       <?php foreach ($bookings as $key => $row) { ?>
                                <tr>
                                    <td><?php echo $count; ?>
                                        <?php if ($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) { ?>
                                            <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row['booking_id']; ?>', '<?php echo $row['amount_due']; ?>', '<?php echo $row['amount_due']; ?>', '<?php echo $row['flat_upcountry']; ?>')"
                                               class="fa fa-road" aria-hidden="true"></i>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a style="color:blue;"  href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id'] ?>" target='_blank' title='View'> 
                                        <?php echo $row['booking_id']; ?></a>
                                    </td>
                                    <td>
                                            <?php echo $row['order_id']; ?>
                                    </td>
                                    <td>
                                        <?php
                                        switch ($row['request_type']) {
                                            case "Installation & Demo":
                                                echo "Installation";
                                                break;
                                            case "Repair - In Warranty":
                                            case REPAIR_OOW_TAG:
                                                echo "Repair";
                                                break;
                                            default:
                                                echo $row['request_type'];
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $row['customername']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['booking_primary_contact_no']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['city']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['state']; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['booking_date'])){ echo date('d-m-y', strtotime($row['booking_date'])); } ?> 
                                    </td>
                                    <td>
                                        <?php if(!empty($row['service_center_closed_date'])){ echo date('d-m-y', strtotime($row['service_center_closed_date'])); } ?> 
                                    </td>
                                    <?php if ($status != "Cancelled") { ?>
                                <td><?php echo $row['tat']; ?></td>
                                <?php } ?>
                                    <?php if ($status == "Cancelled") { ?>        
                                        <td>
                                        <?php echo $row['cancellation_reason']; ?>
                                        </td>
<!--                                        <td>
                                           <?php //if($status === _247AROUND_CANCELLED && strtotime($row['closed_date']) <= strtotime("-1 Months")){ ?>
                                            <a disabled style="background-color: #EEEEEE; border-color: #EEEEEE" class='btn btn-sm btn-info' href="javascript:void(0)" target='_blank' title='Open Booking'><i style="color:#000000" class='fa fa-envelope-o' aria-hidden='true'></i></a>
                                           <?php  //}else{ ?>
                                               <a style="background-color: #2a3f54; border-color: #2a3f54;" class='btn btn-sm btn-info' href="<?php //echo base_url(); ?>partner/update_booking/<?php //echo $row['booking_id'] ?>" target='_blank' title='Open Booking'><i class='fa fa-envelope-o' aria-hidden='true'></i></a>
                                            <?php //} ?>
                                            
                                        </td>-->
                                    <?php } ?>
                                </tr>
                                <?php $count++;
                            } ?>
                        </tbody>
                    </table>
                    <div class="custom_pagination" style="margin-left: 16px;" > 
                <?php if(isset($links)) { echo $links;} ?>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" id="modal-content1">

        </div>
    </div>
</div>
<script>
    var table = $('#complete_booking_table').DataTable(
            {
                "pageLength": 50
            });
    $("#serachInputCompleted").change(function () {
         if($('#serachInputCompleted').val() !== 'all'){
    table
        .columns(7)
        .search($('#serachInputCompleted').val())
        .draw();
         }
          else{
                location.reload();
            }
} );
$('#serachInputCompleted').select2();           
    function open_upcountry_model(booking_id, amount_due, flat_upcountry) {

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/' + booking_id + "/" + amount_due +"/" + flat_upcountry,
            success: function (data) {
                // console.log(data);
                $("#modal-content1").html(data);
                $('#myModal1').modal('toggle');

            }
        });
    }
    </script>
    <style>
        .pagination{
            display: none;
        }
                .dataTables_info{
            display: none;
        }
        </style>
