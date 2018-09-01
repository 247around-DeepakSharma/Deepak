<?php if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
    <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top: 55px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
        }
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-error alert-dismissible" role="alert" style="margin-top: 55px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('error') . '</strong>
                        </div>';
        }
    ?>
    <div class="row">
<?php } ?>        
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Pending Bookings</h2>
            <?php
            if($this->session->userdata('agent_id') != '980084' && $this->session->userdata('agent_id') != '980083'){
            ?>
            <a style="float: right;background: #2a3f54;border-color: #2a3f54;"type="button" class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/partner/download_partner_pending_bookings/<?php echo $this->session->userdata('partner_id')?>/Pending">Download</a>
            <?php
            }
            ?>
            <div class="right_holder" style="float:right;margin-right:10px;">
                            <lable>State</lable>
                            <select class="form-control " id="serachInput" style="border-radius:3px;">
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
            <table class="table table-bordered table-hover table-striped" id="pending_booking_table" style=" z-index: -1;position: static;">
                <thead>
                    <tr>
                        <th class="text-center">S.N</th>
                        <th class="text-center">Booking ID</th>
                       <th class="text-center">Call Type</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Customer Name</th>
                        <th class="text-center">Mobile</th>
                        <th class="text-center">City</th>
                       <th class="text-center">State</th>
                        <th class="text-center">Booking Date</th>
                        <th class="text-center">Age (Days)</th>
                        <th class="text-center">Send Email</th>
                        <th class="text-center">Action</th>
                        <th class="text-center">JobCard</th>
                        <th class="text-center">Escalate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $key => $row) { ?>
                        <tr>
                            <td class="text-center">
                                   <?php echo $sn_no; ?>
                                <?php if ($row->is_upcountry == 1 && $row->upcountry_paid_by_customer == 0) { ?>
                                    <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row->booking_id; ?>', '<?php echo $row->amount_due; ?>')"
                                       class="fa fa-road" aria-hidden="true"></i><?php } ?>
                            </td>
                            <td class="text-center">
                                <a style="color:blue;" href="<?php echo base_url(); ?>partner/booking_details/<?= $row->booking_id ?>" target='_blank' title='View'> <?php
                                    echo $row->booking_id;
                                    ?></a>
                            </td>

                            <td class="text-center">
                                <?php
                                echo $row->services . "<br/>";
                                switch ($row->request_type) {

                                    case "Installation & Demo":
                                        echo "Installation";
                                        break;
                                    case "Repair - In Warranty":
                                    case REPAIR_OOW_TAG:
                                        echo "Repair";
                                        break;
                                    default:
                                        echo $row->request_type;
                                        break;
                                }
                                ?>
                            </td>
                            <td class="text-center"><?php echo $row->appliance_brand; ?></td>
                            <td class="text-center"><?php if ($row->count_escalation>0) { ?>
                                    <i data-toggle="tooltip" title="Escalation" style="color:red; font-size:13px;" onclick=""
                                       class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></i><?php } ?>
                                   <?php echo $row->partner_internal_status; ?>
                            </td>
                            <td class="text-center"> 
                                <?= $row->customername; ?>
                            </td>
                            <td class="text-center">
                                <?= $row->booking_primary_contact_no; ?>
                            </td>
                            <td class="text-center">
                                <?= $row->city; ?>
                            </td>
                             <td class="text-center">
                                <?= $row->state; ?>
                            </td>
                            <td class="text-center">
                                <?= $row->booking_date; ?>
                            </td>
                             <td class="text-center">
                                <?= $row->aging; ?>
                            </td>
                             <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row->booking_id?>')"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                            <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="padding: 5px 5px 5px 5px;margin: 0px;min-width: 95px;position: inherit;z-index: 100;">
                                                    <li style="color: #fff;"><a class='btn btn-sm btn-primary' href="<?php echo base_url(); ?>partner/update_booking/<?= $row->booking_id ?>"  title='View' style="background-color:#2C9D9C; border-color: #2C9D9C;color:#fff;padding: 5px 0px;
    margin: 0px;">Update</a></li>
                                                    <li style="color: #fff;margin-top:5px;">
                                                        <a id="a_hover" <?php if ($row->type == "Query") { ?> style="background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px;" <?php } else{ echo "style='background-color: #26b99a;border-color:#26b99a;color:#fff;padding: 5px 0px;margin: 0px;'";} ?> href="<?php echo base_url(); ?>partner/get_reschedule_booking_form/<?php echo $row->booking_id; ?>" id="reschedule" class="btn btn-sm btn-success" title ="Reschedule">Reschedule</a>
                                                    </li>
                                                     <li style="color: #fff;margin-top:5px;">
                                                         <a id="a_hover" style="background-color: #d9534f;border-color:#d9534f;color:#fff;padding: 5px 0px;margin: 0px;"href="<?php echo base_url(); ?>partner/get_cancel_form/Pending/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-danger' title='Cancel'>Cancel</a>
                                                     </li>
                                                </ul>
                                            </div>
                            </td>
                            <td class="text-center"><a href="javascript: w=window.open('https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename; ?>'); w.print()" class='btn btn-sm btn-primary btn-sm' target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                            <td class="text-center">
                                <?php
                                $initialBooking = strtotime($row->initial_booking_date);
                                $now = time();
                                $datediff = $now - $initialBooking;
                                $days= round($datediff / (60 * 60 * 24));
                                $futureBookingDateMsg = "Booking has future booking date so you can not escalate the booking";
                                $partnerDependencyMsg = 'Escalation can not be Processed, Because booking in '.$row->partner_internal_status.' state';
                                ?>
                                <a <?php if ($row->type == "Query") { ?> style="pointer-events: none;background: #ccc;border-color:#ccc;" <?php } ?> href="#" 
                                                                         class='btn btn-sm btn-warning open-AddBookDialog' data-id= "<?php echo $row->booking_id; ?>" data-toggle="modal" 
                                                                             <?php if($row->actor != 'Partner' && $days>=0){ echo 'data-target="#myModal"';} else if($days<0)
                                                                                 { ?>  onclick="alert('<?php echo $futureBookingDateMsg; ?>')" <?php }
                                                                             else{ ?> onclick="alert('<?php echo $partnerDependencyMsg;?>')" <?php } ?> 
                                                                         title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
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
            $(document).on("click", ".open-AddBookDialog", function () {
                var myBookId = $(this).data('id');
                $(".modal-body #ec_booking_id").val( myBookId );

            });
        </script>
<?php if(empty($is_ajax)) { ?> 
    </div>
    
    <div id="myModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Escalate Form</h4>
                </div>
                <div class="modal-body">
                    
                    <form class="form-horizontal" action="#" method="POST" target="_blank" >
                         <h4 class="col-md-offset-3" id ="failed_validation" style="color:red;margin-top: 0px;margin-bottom: 35px;"></h4>
                        <h4 class="col-md-offset-3" id ="success_validation" style="color:green;margin-top: 0px;margin-bottom: 35px;"></h4>
                        <div class="form-group">
                            <label for="ec_booking_id" class="col-md-2">Booking Id</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control"  name="booking_id" id="ec_booking_id" placeholder = "Booking Id"  readonly>
                            </div>
                        </div>
                        <div class="form-group  <?php if (form_error('escalation_reason_id')) {
                            echo 'has-error';
                        } ?>">
                            <label for="Service" class="col-md-2">Reason</label>
                            <div class="col-md-6">
                                <select class=" form-control" name ="escalation_reason_id" id="escalation_reason_id">
                                    <option selected="" disabled="">----------- Select Reason ------------</option>
                                    <?php
                                    foreach ($escalation_reason as $reason) {
                                        ?>
                                        <option value = "<?php echo $reason['id'] ?>">
                                        <?php echo $reason['escalation_reason']; ?>
                                        </option>
                                <?php } ?>
                                </select>
                                <?php echo form_error('escalation_reason_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="es_remarks" class="col-md-2">Remarks</label>
                            <div class="col-md-6">
                                <textarea  class="form-control" id="es_remarks" name="escalation_remarks" placeholder = "Remarks" ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type= "submit"  onclick="return form_submit()" class="btn btn-primary" value ="Save" style="background-color:#2C9D9C; border-color:#2C9D9C;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function form_submit(){
            var escalation_id = $("#escalation_reason_id").val();
            var booking_id = $("#ec_booking_id").val();
            var remarks = $("#es_remarks").val();

            if(escalation_id ===  null){
                $("#failed_validation").text("Please Select Any Escalation Reason");

            }  else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>partner/process_escalation/'+booking_id,
                    data: {escalation_reason_id: escalation_id,escalation_remarks:remarks,  booking_id:booking_id},
                    success: function (data) {
                        if(data === "success"){
                            $("#failed_validation").text("");
                            $("#success_validation").text("Booking Escalated");
                            location.reload();
                            $('#myModal').modal('toggle');
                       } else {
                           $("#success_validation").text("");
                           $("#failed_validation").html(data);
                       }
                    }
                  });

            }

            return false;
        }
    </script>
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
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script>
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
    var table = $('#pending_booking_table').DataTable(
                {
                    "pageLength": 50
                }
            );
        $("#serachInput").change(function () {
            if($('#serachInput').val() !== 'all'){
                table
                    .columns( 8 )
                    .search($('#serachInput').val())
                    .draw();
            }
            else{
                location.reload();
            }
} );
$('#serachInput').select2();
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
/*        .dataTables_filter{
            display:none;
        }*/
#a_hover a:hover {
  background: #26b99a !important;
  text-decoration:none;
}
.dropdown-backdrop{
    display: none;
}
        </style>
