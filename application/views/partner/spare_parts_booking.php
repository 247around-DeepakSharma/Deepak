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
    <?php
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
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
                    <h2>Pending Spares On <?php echo $this->session->userdata('partner_name') ?> </h2>
                    <div class="pull-right"><button style="background-color: #2a3f54;border-color:#2a3f54;" id="spareDownload" onclick="downloadSpare()" class="btn btn-sm btn-primary">Download</button>
                        <span style="color:#337ab7" id="messageSpare"></span></div>
                    <div class="right_holder" style="float:right;margin-right:10px;">
                            <select class="form-control " id="state_search_spare" style="border-radius:3px;" onchange="booking_search_spare()">
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
                <input type="text" id="booking_id_search_spare" onchange="booking_search_spare()" style="float: right;margin-bottom: -32px;border: 1px solid #ccc;padding: 5px;z-index: 100;position: inherit;" placeholder="Search">
                <div class="x_content">
                    <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                        <table class="table table-bordered table-hover table-striped" id="spare_table" style=" z-index: -1;position: static;">
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Appliance</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Part Request Age(Days)</th>
                                    <th class="text-center">Required Parts</th>
                                    <th class="text-center">Parts Number</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">State</th>
                                    <th class="text-center">Problem Description</th>
                                    <th class="text-center">Send Email</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">SF GST Declaration</th>
                                    <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                                    <th class="text-center" >Courier Manifest <input type="checkbox" id="selectall_manifest" ></th>
                                    <th data-sortable="false" class="text-center">Approve NRN</th>
                                </tr>
                            </thead>
                        </table>
                        <input onclick="return checkValidationForBlank()"  type= "submit"  class="btn btn-md col-md-offset-4" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" name="download_shippment_address" value ="Print Address/Courier Mainfest" >
                    </form>
                </div>
            </div>
        </div>
<?php if(empty($is_ajax)) { ?> 
    </div>
    
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal-title">Reject Parts</h4>
                </div>
                <div class="modal-body">
                    <textarea rows="3" class="form-control" id="textarea" placeholder="Enter Remarks"></textarea>
                </div>
                <input type="hidden" id="url">
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_parts()">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
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
                    <label for="subject">CC: </label>
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



<div id="myModal77" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width: 55%;">
    <!-- Modal content-->
    <div class="modal-content" >
        <form id="idForm"  action="<?php echo base_url(); ?>employee/partner/de_partner_nrn_approval"  method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Approve NRN Details</h4>
            </div>
            <br>
                <div class="row">
                            <div class="col-md-6">
                          
                        

                               
                                 <div class="form-group "id="email_ids">
                                    
                                    <label for="" class="col-md-4">Email</label>
                                    <div class="col-md-6">
                                        <input id="email" class="form-control" name="email" type="text"  value="" style="background-color:#fff;pointer-events:cursor">

                                        <input type="hidden" name="booking_id" id="booking_id">
                                                                                
                                    </div>
                                </div>


                                        <br>
                                        <div class="form-group       " id="approval_file">
                                            <br>
                                    <label for="AWS Receipt" class="col-md-4">Approval File </label>
                                    <div class="col-md-6">
                                        <input id="aws_recipt" class="form-control" name="approval_file" type="file"  value="" style="background-color:#fff;pointer-events:cursor">
                                                                                
                                    </div>
                                </div>




                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    
                                    <label for="remarks_defective_part" class="col-md-4">Remarks *</label>
                                    <div class="col-md-6">
                                        <textarea type="text" class="form-control" id="remarks" name="remarks" placeholder="Please Enter Remarks" required=""></textarea>
                                    </div>
                                   </div>
                            </div>
                        </div>
                 <div class="modal-footer">
                <button type="submit" id="uploadButton" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
        </div>
        </form>

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
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>


<script>
    $(document).ready(function () {
        $('#state_search').select2();
        $('body').popover({
            selector: '[data-popover]',
            trigger: 'click hover',
            placement: 'auto',
            delay: {
                show: 50,
                hide: 100
            }
        });
        spare_table = $('#spare_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_spare_bookings/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search_spare').val();
                    d.state =  $('#state_search_spare').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,5,6,7,8,9,10,11,12,13,14,15,16], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search_spare(){
             spare_table.ajax.reload();
        }
    function downloadSpare(){
        $("#spareDownload").css("display", "none");
        $("#messageSpare").text("Download In Progress");
         $.ajax({
            type: 'POST',

            url: '<?php echo base_url(); ?>file_process/downloadSpareRequestedParts/' + <?php echo $this->session->userdata("partner_id");?> + '/' + '<?php echo _247AROUND_PARTNER_STRING ; ?>',

            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                var jsondata = JSON.parse(data);
                
                if(jsondata['response'] === "success"){
                    $("#spareDownload").css("display", "block");
                    $("#messageSpare").text("");
                    window.location.href = jsondata['path'];
                } else if(jsondata['response'] === "failed"){
                    alert(jsondata['message']);
                    $("#spareDownload").css("display", "block");
                    $("#messageSpare").text("");
                } else {
                     $("#messageSpare").text("File Download Failed");
                }
            }
        });
    }

    $("#selectall_address").change(function () {
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        if (d_m.length > 0) {
            $('.checkbox_manifest').prop('checked', false);
            $('#selectall_manifest').prop('checked', false);
        }
        $(".checkbox_address").prop('checked', $(this).prop("checked"));
    });
    $("#selectall_manifest").change(function () {
        var d_m = $('input[name="download_address[]"]:checked');
        if (d_m.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }
        $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
    });

    function check_checkbox(number) {

        if (number === 1) {
            var d_m = $('input[name="download_courier_manifest[]"]:checked');
            if (d_m.length > 0) {
                $('.checkbox_manifest').prop('checked', false);
                $('#selectall_manifest').prop('checked', false);
            }

        } else if (number === 0) {
            var d_m = $('input[name="download_address[]"]:checked');
            if (d_m.length > 0) {
                $('.checkbox_address').prop('checked', false);
                $('#selectall_address').prop('checked', false);
            }
        }

    }

    function open_upcountry_model(booking_id, amount_due, flat_upcountry) {

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/' + booking_id + "/" + amount_due + "/" +flat_upcountry,
            success: function (data) {
                // console.log(data);
                $("#modal-content1").html(data);
                $('#myModal1').modal('toggle');

            }
        });
    }
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
        $('#modal-title').text("Reject Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        
    });
    
    function reject_parts(){
        var remarks =  $('#textarea').val();
        if(remarks !== ""){
            var url =  $('#url').val();
            var partner_id =  $('#modal_partner_id').val();
       
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge:0,partner_id:partner_id},
                success: function(data){
              
                    if(data === "Success"){
                        //  $("#"+booking_id+"_1").hide()
                        $('#myModal2').modal('hide');
                        alert("Updated Successfully");
                        location.reload();
                    } else {
                        alert("Spare Parts Cancellation Failed!");
                    }
                }
            });
        } else {
            alert("Please Enter Remarks");
        }
    }
      function add_data_in_create_email_form(bookingID){
            $.ajax({
                type: 'post',
                url: '<?php echo base_url()  ?>employee/service_centers/get_booking_contacts/'+bookingID,
                data: {},
                success: function (response) {
                     var result = JSON.parse(response);
                     var am_email='';
                     for(var i=0;i<result.length;i++) {
                         am_email += result[i].am_email+",";
                     }
                    $("#internal_email_booking_to").val(am_email);
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
        function checkValidationForBlank(){
            var address = $('.checkbox_address:checkbox:checked');
            var manifest = $('.checkbox_manifest:checkbox:checked');
            if(address.length != 0 || manifest.length !=0){
                return true;
            }
            else{
                alert("Please Select any checkbox");
                return false;
            }
        }

$('body').on('click', '.approve_nrn_booking', function() {
var booking = $(this).attr("data-booking_id");
$("#booking_id").val(booking);
$("#idForm")[0].reset();
});




    $('#aws_recipt').change(function () {
    var ext = this.value.match(/\.(.+)$/)[1];
    switch (ext) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'pdf':
        case 'doc':
        case 'jpeg':
        case 'docx':
        case 'gif':
            $('#uploadButton').attr('disabled', false);
            break;
        default:
            alert('This is not an allowed file type. Only PDF,DOC,JPEG,PNG,JPG are allowd');
            this.value = '';
    }
});


$('body').on('click', '.approved_nrn_booking', function() {
     spare_table.ajax.reload(null, false); 
    swal("Already Approved", "Your NRN for this booking already approved.");
});



    function submitForm(){
       event.preventDefault();
       $(".loader").removeClass('hide');
       var form_data = new FormData(document.getElementById("idForm"));
               $.ajax({
                   url: "<?php echo base_url(); ?>employee/partner/do_partner_nrn_approval",
                   type: "POST",
                   data: form_data,
                   processData: false,  // tell jQuery not to process the data
                   contentType: false   // tell jQuery not to set contentType
                   }).done(function(response) {
                          console.log(response);
                          var response = response.trim();
                          spare_table.ajax.reload(null, false); 
                            $(".loader").addClass('hide');
                            if(response=='1'){
                                $(".close").click();
                                 swal({title: "Approved !", text: "Your NRN is  approved .", type: "success"},
                                    function(){ 
                              
                                    }
                            );  
                             }else{
                                swal({title: "Error !", text: "Your approval not processed . Some parts are already shipped !", type: "error"},

                              function(){ 
                              //  location.reload();
                             });
                             }
    
               });
 
    }


    </script>
    <style>
.dropdown-backdrop{
    display: none;
}
.table tr td:nth-child(10) {
    text-align: center;
}
.table tr td:nth-child(12) {
    text-align: center;
}
.table tr td:nth-child(13) {
    text-align: center;
}
.table tr td:nth-child(14) {
    text-align: center;
}
#spare_table_filter{
      display: none;
}
#spare_table_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>
        
        <?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>