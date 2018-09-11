<?php
if ($this->uri->segment(4)) {
    $sn_no = $this->uri->segment(4) + 1;
} else {
    $sn_no = 1;
}
?>
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
                            <lable>State</lable>
                            <select class="form-control " id="serachSpareInput" style="border-radius:3px;">
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
                    <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                        <table class="table table-bordered table-hover table-striped" id="spare_table" style=" z-index: -1;position: static;">
                            <thead>
                                <tr>
                                    <th class="text-center">S.N</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Part Request Age</th>
                                    <th class="text-center">Required Parts</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">State</th>
                                    <th class="text-center">Problem Description</th>
                                    <th class="text-center">Send Email</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">SF GST Declaration</th>
                                    <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                                    <th class="text-center" >Courier Manifest <input type="checkbox" id="selectall_manifest" ></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn_no1 = 1;
                                foreach ($spare_parts as $key => $row) {
                                    ?>
                                    <tr style="text-align: center;">
                                        <td><?php echo $sn_no1; ?>
                                            <?php if ($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) { ?>
                                                <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row['booking_id']; ?>', '<?php echo $row['amount_due']; ?>')"
                                                   class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        </td>
                                        <td>
                                            <a  style="color:blue;" href="<?php echo base_url(); ?>partner/booking_details/<?php echo $row['booking_id']; ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                        </td>
                                        <td>
                                            <?php echo $row['name']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['age_of_request'] . " days"; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['parts_requested']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['model_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['serial_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['state']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['remarks_by_sc']; ?>
                                        </td>
                                           <td style="vertical-align: middle;">
                                            <a style="width: 36px;background: #5cb85c;border: #5cb85c;" class="btn btn-sm btn-primary  relevant_content_button" data-toggle="modal" title="Email"  onclick="create_email_form('<?php echo $row['booking_id']?>')"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="    border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="border: none;background: none;z-index: 100;position: inherit;min-width: 70px;">
                                                    <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                                    <li style="color: #fff;"><a href="<?php echo base_url() ?>partner/update_spare_parts_form/<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-success" title="Update" style="color:#fff;margin: 0px;padding: 5px 12px;" ></i>Update</a></li>
                                                    <?php $explode = explode(",", $row['spare_id']); if(count($explode) == 1){  ?>
                                                    <li style="color: #fff;margin-top:5px;"><a href="#" data-toggle="modal" id="<?php echo "spare_parts" . $row['spare_id']; ?>" data-url="<?php echo base_url(); ?>employee/inventory/update_action_on_spare_parts/<?php echo $row['spare_id'] . "/" . $row['booking_id']; ?>/CANCEL_PARTS" data-booking_id="<?php echo $row['booking_id']; ?>" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="color:#fff;margin: 0px;padding: 5px 14.4px;" >Reject</a>
                                                    <?php } ?>
                                           </li>
                                           </div>
                                                </ul>
                                            </div>
                                        </td>

<!--                                        <td>
                                            <a href="<?php //echo base_url() ?>partner/update_spare_parts_form/<?php //echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Update" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                        </td>
                                        <td>
                                            <a href="#" data-toggle="modal" id="<?php //echo "spare_parts" . $row['id']; ?>" data-url="<?php //echo base_url(); ?>employee/inventory/update_action_on_spare_parts/<?php //echo $row['id'] . "/" . $row['booking_id']; ?>/CANCEL_PARTS" data-booking_id="<?php //echo $row['booking_id']; ?>" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class="fa fa-times" aria-hidden='true'></i></a>
                                        </td>-->
                                        <td>
                                            <?php if(!empty($row['is_gst_doc'])){ ?> 
                                                <a class="btn btn-sm btn-success" href="#" title="GST number not available" style="background-color:#2C9D9C; border-color: #2C9D9C; cursor: not-allowed;"><i class="fa fa-close"></i></a>
                                            <?php }else if(empty ($row['signature_file'])) { ?> 
                                                <a class="btn btn-sm btn-success" href="#" title="Signature file is not available" style="background-color:#2C9D9C; border-color: #2C9D9C;cursor: not-allowed;"><i class="fa fa-times"></i></a>
                                            <?php }else{ ?>
                                                <a class="btn btn-sm btn-success" href="<?php echo base_url();?>partner/download_sf_declaration/<?php echo rawurlencode($row['sf_id'])?>" title="Download Declaration" style="background-color:#2C9D9C; border-color: #2C9D9C;" target="_blank"><i class="fa fa-download"></i></a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-control checkbox_address"  name="download_address[]" onclick='check_checkbox(1)' value="<?php echo $row['booking_id']; ?>" />
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-control checkbox_manifest" name="download_courier_manifest[]" onclick='check_checkbox(0)' value="<?php echo $row['booking_id']; ?>" />
                                        </td>

                                    </tr>
                                    <?php
                                    $sn_no1++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <input onclick="return checkValidationForBlank()" type= "submit"  class="btn btn-md col-md-offset-4" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" name="download_shippment_address" value ="Print Address/Courier Mainfest" >
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
<script>
    $(document).ready(function () {
        $('body').popover({
            selector: '[data-popover]',
            trigger: 'click hover',
            placement: 'auto',
            delay: {
                show: 50,
                hide: 100
            }
        });
    });
    
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

    function open_upcountry_model(booking_id, amount_due) {

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/' + booking_id + "/" + amount_due,
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
</script>
<?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>
<script>
    var table = $('#spare_table').DataTable(
            {
                 "pageLength": 50
             }
      );
        $("#serachSpareInput").change(function () {
            if($('#serachSpareInput').val() !== 'all'){
    table
        .columns( 7 )
        .search($('#serachSpareInput').val())
        .draw();
            }
 else{
                location.reload();
            }
} );
$('#serachSpareInput').select2();
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
    </script>
    <style>
.dropdown-backdrop{
    display: none;
}
        </style>