<?php if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
} ?>
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
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Defective Parts Shipped By SF</h2>
            <div class="pull-right"><a style="background: #2a3f54; border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_waiting_defective_parts"  class="btn btn-sm btn-primary">Download</a></div>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="waiting_defactive_parts">
                    <thead>
                        <tr>
                            <th class="text-center">S.N</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">SF Challan</th>
                            <th class="text-center">Partner Challan</th>
                            <th class="text-center">Send Email</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Receive</th>
                            <th class="text-center">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spare_parts as $key => $row) { ?>
                            <tr style="text-align: center;">
                                <td>
                                    <?php echo $sn_no; ?>
                                </td>
                                <td>
                                    <a  style="color:blue" href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                </td>
                                <td>
                                    <?php echo $row['name']; ?>
                                </td>
    <!--                                    <td>
                                    <?php //echo $row['age_of_booking'];  ?>
                                </td>-->
                                <td>
                                    <?php echo $row['defective_part_shipped']; ?>
                                </td>
                                <td>
                                    <?php echo $row['courier_name_by_sf']; ?>
                                </td>
                                <td>
                                    <?php echo $row['awb_by_sf']; ?>
                                </td>
                                 <td> 
                                    <?php  if(!empty($row['sf_challan_file'])) { ?> 
                                     <a style="color: blue;" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $row['sf_challan_file']; ?>" target="_blank"><?php echo $row['sf_challan_number']?></a>
                                    <?php } ?>
                                </td>
                                <td> 
                                        <?php  if(!empty($row['partner_challan_file'])) { ?> 
                                            <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $row['partner_challan_file']; ?>" target="_blank"><?php echo $row['partner_challan_number']?></a>
                                        <?php }
                                        else if(!empty($row['partner_challan_number'])) {
                                            echo $row['partner_challan_number'];
                                        }
?>
                                      </td>
                                       <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row["booking_id"]?>')"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                <td>
                                    <?php if (!is_null($row['defective_part_shipped_date'])) {
                                        echo date("d-m-Y", strtotime($row['defective_part_shipped_date']));
                                    } ?>
                                </td>

                                <td>
                                <?php echo $row['remarks_defective_part_by_sf']; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['defective_part_shipped'])) { ?>
                                    <a style="background: #2a3f54; border-color: #2a3f54;" onclick="return confirm_received()" class="btn btn-sm btn-primary" id="defective_parts"
                                               href="<?php echo base_url(); ?>partner/acknowledge_received_defective_parts/<?php echo $row['booking_id']; ?>/<?php echo $this->session->userdata("partner_id"); ?>" 
                                                   <?php echo empty($row['defective_part_shipped']) ? 'disabled="disabled"' : '' ?>>Receive</a>
                                            <?php } ?>
                                    </td>
                                <td>
                                    
                                    <?php if (!empty($row['defective_part_shipped'])) { ?>
                                        <div class="dropdown" >
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-danger" type="button" data-toggle="dropdown">Reject
                                                <span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="right: 0px;left: auto;">
                                                <?php foreach ($internal_status as $value) { ?>
                                                    <li><a href="<?php echo base_url(); ?>partner/reject_defective_part/<?php echo $row['booking_id']; ?>/<?php echo urlencode(base64_encode($value->status)); ?>"><?php echo $value->status; ?></a></li>
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
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<div id="send_email_form" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="background-color:  #2a3f54;border-color: #2a3f54;">
                <button type="button" class="close btn-primary well"  data-dismiss="modal"style="color: white;">&times;</button>
                <p class="modal-title"style="color: white;background-color: #2a3f54;border-color: #2a3f54;border: 0px; text-align: center; font-size:18px;" id="email_title"></p>
            </div>
            <div class="modal-body">
                <div id="form_container">
                <form action="" method="post">
                    <input type="hidden" value="" id="internal_email_booking_id">
                    <div class="form-group">
                    <label for="subject">To : </label>
                    <input type="text" class="form-control" id="internal_email_booking_to">
                    </div>
                    <div class="form-group">
                    <label for="subject">cc: </label>
                    <input type="text" class="form-control" id="internal_email_booking_cc">
                    </div>
                    <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="internal_email_booking_subject">
                    </div>
                    <div class="form-group">
                    <label for="text">Message</label>
                    <textarea class="form-control" rows="5" id="internal_email_booking_msg"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-default" style="color: #fff;background-color: #2a3f54;border-color: #2a3f54;float:right;" onclick="send_booking_internal_conversation_email()">Send Email</button>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                    </form>
                    </div>
                        <div id="msg_container" style="text-align: center;display: none;">
                     <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
            </div>
        </div>


    </div>
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
    var table = $('#waiting_defactive_parts').DataTable(
            {
                 "pageLength": 50
             }
      );
function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
}
     function add_data_in_create_email_form(bookingID){
            $.ajax({
                type: 'post',
                url: '<?php echo base_url()  ?>employee/service_centers/get_booking_contacts/'+bookingID,
                data: {},
                success: function (response) {
                     var result = JSON.parse(response);
                    $("#internal_email_booking_to").val(result[0].am_email+",");
                    $("#internal_email_booking_cc").val(result[0].rm_email+","+result[0].service_center_email);
                    $("#internal_email_booking_subject").val(result[0].partner+"- Query From Partner For - "+bookingID);
               }
            });
        }
        function create_email_form(booking_id){
            $("#internal_email_booking_subject").prop('disabled', true);
            $("#internal_email_booking_cc").prop('disabled', true);
            $("#email_title").html("Send Email For Booking "+booking_id);
            $("#send_email_form").modal("show");
            $("#internal_email_booking_id").val(booking_id);
            add_data_in_create_email_form(booking_id);
        }
        function send_booking_internal_conversation_email(){ 
            var to = $("#internal_email_booking_to").val();
            var cc = $("#internal_email_booking_cc").val();
            var booking_id = $("#internal_email_booking_id").val();
            var subject = $("#internal_email_booking_subject").val();
            var msg = $(" #internal_email_booking_msg").val();
            document.getElementById("msg_container").style.display='block';
            document.getElementById("form_container").style.display='none';
            if(booking_id && subject && msg){
                $.ajax({
                   type: 'post',
                   url: '<?php echo base_url()  ?>employee/partner/process_booking_internal_conversation_email',
                   data: {'booking_id':booking_id,'subject':subject,'msg':msg,'to':to,'cc':cc},
                   success: function (response) {
                        $("#msg_container").html(response);
                        $("#internal_email_booking_to").val("");
                        $("#internal_email_booking_cc").val("");
                        $("#internal_email_booking_id").val("");
                        $("#internal_email_booking_subject").val("");
                        $("#internal_email_booking_msg").val("");
                        location.reload();
                  }
               });
            }
            else{
                alert("Subject Or Message should not be blank ");
                return false;
            }
        }
</script>
    <style>
        </style>