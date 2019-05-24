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
                    <h2>Defective Parts Delivered to Partner</h2>
                    <div class="pull-right"><a style="background: #2a3f54; border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_waiting_defective_parts"  class="btn btn-sm btn-primary">Download</a></div>
                    <div class="right_holder" style="float:right;margin-right:10px;">
                            <select class="form-control " id="state_search_waiting" style="border-radius:3px;" onchange="booking_search_waiting()">
                    <option value="">States</option>
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
        <input type="text" id="booking_id_search_waiting" onchange="booking_search_waiting()" style="float: right;margin-bottom: -32px;border: 1px solid #ccc;padding: 5px;z-index: 100;position: inherit;" placeholder="Search">
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="waiting_defactive_parts">
                    <thead>
                        <tr>
                            <th class="text-center">S.N</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Parts Number</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">SF Challan</th>
                            <th class="text-center">Partner Challan</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Send Email</th>                            
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Receive</th>
                            <th class="text-center">Reject</th>
                        </tr>
                    </thead>
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
 <div id="gen_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="gen_model_title"></h4>
                </div>
                <div class="modal-body" id="gen_model_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
    $(document).ready(function () {
        waiting_defactive_parts = $('#waiting_defactive_parts').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_defactive_part_shipped_by_sf_bookings",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search_waiting').val();
                    d.state =  $('#state_search_waiting').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,5,6,7,8,10,11,12,13], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search_waiting(){
             waiting_defactive_parts.ajax.reload();
        }
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
        function create_email_form_2(booking_id){
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
    <style>
        #waiting_defactive_parts_filter{
      display: none;
}
#waiting_defactive_parts_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>