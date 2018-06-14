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
                                    <?php if(!$this->session->userdata('is_wh')) { ?> 
                                    <th class="text-center">Action</th>
                                    <?php } ?>
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
                                        <?php if(!$this->session->userdata('is_wh')) { ?> 
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary" type="button" data-toggle="dropdown" style="    border: 1px solid #2a3f54;background: #2a3f54;">Action
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu" style="border: none;background: none;z-index: 100;position: inherit;min-width: 70px;">
                                                    <div class="action_holder" style="background: #fff;border: 1px solid #2c9d9c;padding: 1px;">
                                                    <li style="color: #fff;"><a href="<?php echo base_url() ?>partner/update_spare_parts_form/<?php echo $row['id']; ?>" class="btn btn-sm btn-success" title="Update" style="color:#fff;margin: 0px;padding: 5px 12px;" ></i>Update</a></li>
                                                    <li style="color: #fff;margin-top:5px;"><a href="#" data-toggle="modal" id="<?php echo "spare_parts" . $row['id']; ?>" data-url="<?php echo base_url(); ?>employee/inventory/update_action_on_spare_parts/<?php echo $row['id'] . "/" . $row['booking_id']; ?>/CANCEL_PARTS" data-booking_id="<?php echo $row['booking_id']; ?>" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="color:#fff;margin: 0px;padding: 5px 14.4px;" >Reject</a>
                                           </li>
                                           </div>
                                                </ul>
                                            </div>
                                        </td>
                                        <?php } ?>

<!--                                        <td>
                                            <a href="<?php echo base_url() ?>partner/update_spare_parts_form/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Update" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                        </td>
                                        <td>
                                            <a href="#" data-toggle="modal" id="<?php echo "spare_parts" . $row['id']; ?>" data-url="<?php echo base_url(); ?>employee/inventory/update_action_on_spare_parts/<?php echo $row['id'] . "/" . $row['booking_id']; ?>/CANCEL_PARTS" data-booking_id="<?php echo $row['booking_id']; ?>" data-target="#myModal2" class="btn btn-sm btn-danger open-adminremarks" title="Reject" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class="fa fa-times" aria-hidden='true'></i></a>
                                        </td>-->
                                        <td>
                                            <?php if(!empty($row['is_gst_doc'])){ ?> 
                                                <a class="btn btn-sm btn-success" href="#" title="GST number not available" style="background-color:#2C9D9C; border-color: #2C9D9C; cursor: not-allowed;"><i class="fa fa-check"></i></a>
                                            <?php }else if(empty ($row['signature_file'])) { ?> 
                                                <a class="btn btn-sm btn-success" href="#" title="Signature file is not available" style="background-color:#2C9D9C; border-color: #2C9D9C;cursor: not-allowed;"><i class="fa fa-times"></i></a>
                                            <?php }else{ ?>
                                                <a class="btn btn-sm btn-success" href="<?php echo base_url();?>partner/download_sf_declaration/<?php echo rawurlencode($row['sf_id'])?>" title="Download Declaration" style="background-color:#2C9D9C; border-color: #2C9D9C;" target="_blank"><i class="fa fa-download"></i></a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-control checkbox_address" name="download_address[]" onclick='check_checkbox(1)' value="<?php echo $row['booking_id']; ?>" />
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
                        <div class="custom_pagination" style="margin-left: 16px;" > 
                            <?php
                            if (isset($links)) {
                                echo $links;
                            }
                            ?>
                        </div>
                        <input type= "submit"  class="btn btn-md col-md-offset-4" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" name="download_shippment_address" value ="Print Address/Courier Mainfest" >
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
    </script>
    <style>
        .paging_simple_numbers{
            display: none;
        }
        .dataTables_info{
    display: none;
}
.dropdown-backdrop{
    display: none;
}
        </style>