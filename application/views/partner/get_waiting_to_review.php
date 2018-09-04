

<?php if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
    <div class="row">
<?php } ?>        
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Review Bookings</h2>
            <?php
            if($this->session->userdata('agent_id') != '980084' && $this->session->userdata('agent_id') != '980083'){
            ?>
            <a style="float: right;background: #2a3f54;border-color: #2a3f54;"type="button" class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/partner/download_partner_review_bookings/<?php echo $this->session->userdata('partner_id')?>">Download</a>
            <?php
            }
            ?>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="<?php echo base_url(); ?>employee/partner/checked_complete_review_booking" method="post">
            <table class="table table-bordered table-hover table-striped" id="review_booking_table" style=" z-index: -1;position: static;">
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
                        <th class="text-center" style="padding: 0px 24px 6px;"><input type="checkbox" id="selecctall" /> Approve</th>
                        <th class="text-center">Reject</th>
                        <th class="text-center">JobCard</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($booking_details as $bookingID => $row) { ?>
                    <tr id="<?php echo "row_".$bookingID?>">
                            <td class="text-center">
                                   <?php echo $sn_no; ?>
                                <?php if ($row['is_upcountry'] == 1) { ?>
                                    <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $bookingID; ?>', '<?php echo $row['amount_due']; ?>')"
                                       class="fa fa-road" aria-hidden="true"></i><?php } ?>
                            </td>
                            <td class="text-center">
                                <a style="color:blue;" href="<?php echo base_url(); ?>partner/booking_details/<?= $bookingID ?>" target='_blank' title='View'> <?php
                                    echo $bookingID;
                                    ?></a>
                            </td>

                            <td class="text-center">
                                <?php
                                echo $row['services'] . "<br/>";
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
                            <td class="text-center"> <?= $row['appliance_brand']; ?></td>
                            <td class="text-center"> 
                                <?= $row['name']; ?>
                            </td>
                            <td class="text-center">
                                <?= $row['booking_primary_contact_no']; ?>
                            </td>
                            <td class="text-center">
                                <?= $row['internal_status']; ?>
                            </td>
                            <td class="text-center">
                                <?= $row['city']; ?>
                            </td>
                             <td class="text-center">
                                <?= $row['state']; ?>
                            </td>
                            <td class="text-center">
                                <?= $row['initial_booking_date']; ?>
                            </td>
                             <td class="text-center">
                                <?= $row['age']; ?>
                            </td>
                             <td class="text-center">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $bookingID?>')"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                        
                                        <td class="text-center">
                                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $bookingID; ?>]" value = "<?php echo $row['partner_id']; ?>" >
                                            <input id="approved_close" type="checkbox"  class="checkbox1" name="approved_booking[]" value="<?php echo $bookingID ?>">
                                            <input id="approved_by" type="hidden"   name="approved_by" value="<?php echo $row['partner_id']; ?>">
            </td>
                            <td class="text-center">
                                            <button style="min-width: 59px;" type="button" class="btn btn-primary btn-sm open-adminremarks" 
                                                                                     data-toggle="modal" data-target="#myModal2" onclick="create_reject_form('<?php echo $bookingID?>')">Reject</button>
                            </td>
                            <td class="text-center"><a href="javascript: w=window.open('https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row['booking_jobcard_filename']; ?>'); w.print()" class='btn btn-sm btn-primary btn-sm' target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                        </tr>
                            <?php $sn_no++;
                        } ?>
                </tbody>
            </table>
            <center><input  type="submit" value="Approve Bookings"  style=" background-color: #2C9D9C;
                     border-color: #2C9D9C;" class="btn btn-md btn-success"></input></center>
                </form>
        </div>
    </div>
</div>
        <script>
            $(document).on("click", ".open-AddBookDialog", function () {
                var myBookId = $(this).data('id');
                $(".modal-body #ec_booking_id").val( myBookId );

            });
        </script>
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
<!-- Modal -->
   <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="booking_id_container">Modal Header</h4>
            </div>
            <div class="modal-body">
               <textarea rows="8" class="form-control" id="reject_comments"></textarea>
            </div>
            <input type="hidden" value="<?php echo $this->session->userdata('partner_id'); ?>" id="partner_id_cancel">
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks_by_partner()">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script>
       $(document).ready(function(){
     $("#selecctall").change(function(){
       $(".checkbox1").prop('checked', $(this).prop("checked"));
       });
   });
    var partner_remarksUrl = baseUrl + '/employee/partner/reject_booking_from_review/';
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
    var table = $('#review_booking_table').DataTable(
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
        function create_reject_form(booking_id){
            $("#booking_id_container").text(booking_id);
        }
        function send_remarks_by_partner() {
            var bookingID = $('#booking_id_container').text();
            var postData = {};
            postData['booking_id'] = bookingID;
            postData['admin_remarks'] = $('#reject_comments').val();
            postData['rejected_by'] = $('#partner_id_cancel').val();
            sendAjaxRequest(postData, partner_remarksUrl).done(function (data) {
                alert(data);
                document.getElementById("row_"+bookingID).style.background = "rgb(255, 227, 147)";
            });
}
function submit_approve_bookings(){
    var bookings = $("input[name=approved_booking]").val();
    alert(bookings);
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