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
                    <h2>Review Cancelled Bookings </h2>
                    <div class="pull-right"><a style="float: right;background: #2a3f54;border-color: #2a3f54;"type="button" class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/partner/download_partner_review_bookings/<?php echo $this->session->userdata('partner_id')?>">Download</a>
                    </div>
                    <div class="right_holder" style="float:right;margin-right:10px;">
                            <select class="form-control " id="state_search_waiting" style="border-radius:3px;" onchange="booking_search_review()">
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
        <input type="text" id="booking_id_search_review" onchange="booking_search_review()" style="float: right;margin-bottom: -32px;border: 1px solid #ccc;padding: 5px;z-index: 100;position: inherit;">
        <div class="x_content">
            <form action="<?php echo base_url(); ?>employee/partner/checked_complete_review_booking" method="post">
            <table class="table table-bordered table-hover table-striped" id="review_booking_table" style=" z-index: -1;position: static;">
                <thead>
                    <tr>
                        <th class="text-center">S.N</th>
                        <th class="text-center">Booking ID</th>
                       <th class="text-center">Call Type</th>
                        <th class="text-center">Cancellation Reason</th>
                        <th class="text-center">Customer Name</th>
                        <th class="text-center">Mobile</th>
                        <th class="text-center">City</th>
                       <th class="text-center">State</th>
                        <th class="text-center">Booking Date</th>
                        <th class="text-center">Age (Days)</th>
                        <th class="text-center" style="padding: 0px 24px 6px;"><input type="checkbox" id="selecctall" /> Approve</th>
                        <th class="text-center">Reject</th>
                    </tr>
                </thead>
               
            </table>
                <center><input  type="submit" value="Approve Bookings" onclick="return checkValidationForBlank_review()" style=" background-color: #2C9D9C;
                     border-color: #2C9D9C;" class="btn btn-md btn-success"></input></center>
                </form>
        </div>
    </div>
</div>
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>
        <script>
            $(document).on("click", ".open-AddBookDialog", function () {
                var myBookId = $(this).data('id');
                $(".modal-body #ec_booking_id").val( myBookId );
            });
        </script>
<div class="clearfix"></div>
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
       var partner_remarksUrl = baseUrl + '/employee/partner/reject_booking_from_review/';
       $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
            $("#selecctall").change(function(){
                $(".checkbox1").prop('checked', $(this).prop("checked"));
            });
            review_booking_table = $('#review_booking_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_review_booking_data/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search_review').val();
                     d.state =  $('#state_search_waiting').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,4,5,10,11], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
            });
        });
        function booking_search_review(){
             review_booking_table.ajax.reload();
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
                $('.modal').modal('hide') // closes all active pop ups.
                if($("button_reject_" +bookingID).length == 0) {
                $("#button_reject_"+bookingID).prop('disabled','true');
                }
                document.getElementById("row_"+bookingID).style.background = "rgb(255, 227, 147)";
            });
        }
function submit_approve_bookings(){
    var bookings = $("input[name=approved_booking]").val();
    alert(bookings);
}
function checkValidationForBlank_review(){
    var is_checked = $('.checkbox1:checkbox:checked');
    if(is_checked.length != 0){
        return true;
    }
    else{
        alert("Please Select any booking");
        return false;
    }
    }
    </script>
    <style>
#a_hover a:hover {
  background: #26b99a !important;
  text-decoration:none;
}
.dropdown-backdrop{
    display: none;
}
#review_booking_table_processing{
    border:none !important;
    background-color: transparent !important;
}
#review_booking_table_filter{
    display: none;
}
        </style>