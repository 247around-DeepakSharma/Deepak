<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
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
        if ($this->session->userdata('stock_not_exist')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('stock_not_exist') . '</strong>
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
                    <div class="col-md-6">
                        <h2>Spares Assigned To Partner</h2>
                    </div>
                    <div class="col-md-6">
                        <button id="spareDownload" onclick="downloadList()" class="btn btn-sm btn-primary pull-right" style="margin-top: 28px;">Download List</button>
                        <span style="color:#337ab7" id="messageSpare"></span>
                    </div>
                    <div class="clearfix"></div>
                    
                </div>
                <div class="x_content">
                    <form target="_blank"  action="<?php echo base_url(); ?>service_center/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">

                        <table id="on_partner" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Booking  Id</th>
                                    <th class="text-center">Invoice Id</th>
                                    <th class="text-center">SF Name</th>
                                    <th class="text-center">Age of Request(Days)</th>
                                    <th class="text-center">Parts Required</th>
                                    <th class="text-center">Requested Quantity</th>
                                    <th class="text-center">Parts Code</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">Problem Description</th>
                                    <th class="text-center">Stock</th>
                                     <th class="text-center">Update</th>
                                     <th class="text-center">SF GST Declaration</th>
                                     <th class="text-center">Download Challan</th>
                                    <th class="text-center">Couriers Declaration<input type="checkbox" id="selectall_concern_detail" > </th>
                                     
                                     <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                                     <th class="text-center"> Generate Challan<input type="checkbox" id="selectall_challan" ></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($spare_parts as $key => $row) { 
                                    
                                    if ($row['active'] == 0) {
                                        $sc_icon_style = "color:#e10f0fd1;";
                                        $sf_status = "Permanently Off";
                                    } else if ($row['on_off'] == 0) {
                                        $sc_icon_style = "color:#f1bc44;";
                                        $sf_status = "Temporary Off";
                                    } else {
                                        $sc_icon_style = "color:#14d914;";
                                        $sf_status = "On";
                                    }
                                    
                                $vendor_name = "<i class='fa fa-circle' aria-hidden='true' style='margin-right:5px;" . $sc_icon_style . "'></i>" . $row['vendor_name'] . "<p style='font-weight: bold;" . $sc_icon_style . "'> - " . $sf_status . "</p>";
                                    
                                    ?>
                                    <tr style="text-align: center;" >
                                        <td style="<?php if($row['inventory_invoice_on_booking'] == 1){ echo 'background: green;color: #FFFfff;';} ?>">
                                            <?php if ($row['is_upcountry'] == 1 && $row['upcountry_paid_by_customer'] == 0) { ?>
                                                <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row['booking_id']; ?>', '<?php echo $row['amount_due']; ?>',  '<?php echo $row['flat_upcountry']; ?>')"
                                                   class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                            <?php echo $sn_no; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($this->session->userdata('service_center_id'))) { ?>
                                                <a  style="color:blue;" href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                            <?php } else if ($this->session->userdata('id')) { ?>
                                                <a  style="color:blue;" href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                            <?php } ?>	                                            
                                        </td>
                                        <td>
                                            <?php echo $row['purchase_invoice_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $vendor_name; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['age_of_request']; ?>
                                        </td>
                                        <td style="word-break: break-all;">
                                            <?php echo $row['parts_requested']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['quantity']; ?>
                                        </td>
                                         <td style="word-break: break-all;">
                                            <?php echo $row['part_number']; ?>
                                        </td>
                                        
                                        <td>
                                            <?php echo $row['model_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['serial_number']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['remarks_by_sc']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['stock']; ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo base_url() ?>service_center/update_spare_parts_form/<?php echo $row['booking_id']; ?>/<?php echo $row['partner_id']; ?>" class="btn btn-sm btn-info" title="Update" style="" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                        </td>


                                             <td>
                                            <?php if(!empty($row['gst_no']) && empty($row['signature_file'])){ ?> 
                                                <a class="btn btn-sm btn-success nodn" href="#" title="GST number is not available" style="background-color:red; border-color: red; cursor: not-allowed;"><i class="fa fa-close"></i></a>
                                            <?php }else if(empty ($row['signature_file'])) { ?> 
                                                <a class="btn btn-sm btn-success nodn" href="#" title="Signature file is not available" style="background-color:red; border-color: red;cursor: not-allowed;"><i class="fa fa-close"></i></a>
                                            <?php }else{ ?>
                                                <a class="btn btn-sm btn-success" href="<?php echo base_url();?>service_center/download_sf_declaration/<?php echo rawurlencode($row['sf_id'])?>" title="Download Declaration" style="background-color:#0edf0e; border-color: #2C9D9C;" target="_blank"><i class="fa fa-download"></i></a>
                                            <?php } ?>
                                        </td>


                                         <td style="width: 80px;">
                                        <?php if (!empty($row['partner_challan_file'])) { ?>
                                          <a style="background-color:#0edf0e;" title="Download Challan" target="_blank" class="btn btn-sm btn-success" href="<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/<?php echo $row['partner_challan_file']; ?>"><span ><i class="fa fa-download" aria-hidden="true" ></i></span></a>  
                                        <?php }else{ ?>
                                         <a href="#" title="Challan not available" style="background-color:red;border-color: red;" class="challan_not_generated btn btn-sm btn-success"><span style=" "><i class="fa fa-remove" aria-hidden="true"></i></span></a>

                                        <?php } ?>
                                        
                                     </td>

                                      <td>
                                            <input type="checkbox" class="form-control concern_detail" onclick="check_checkbox(3)" name="coueriers_declaration[<?php echo $row['partner_id'].'-'.$row['entity_type'] ;?>][]"  value="<?php echo $row['id']; ?>"/>
                                        </td>

                                       <td>
                                            <input type="checkbox" class="form-control checkbox_address" name="download_address[]" onclick='check_checkbox(1)' value="<?php echo $row['booking_id']; ?>" />
                                        </td>


                                        <td>
                                      <input type="checkbox" class="form-control checkbox_challan" name="generate_challan[<?php echo $row['service_center_id']; ?>][]" id="generate_challan_<?php echo $key; ?>" onclick='check_checkbox(2)' data-service_center_id="<?php echo $row['service_center_id']; ?>" value="<?php echo $row['id']; ?>" />
                                  </td>
                                    </tr>
                                    <?php
                                    $sn_no++;
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
                        <center style="margin-bottom: 10px;"><input type= "submit" onclick="return checkValidationForBlank()"  class="btn btn-md" style="background-color:#2C9D9C; border-color: #2C9D9C; color:#fff;" name="download_shippment_address" value ="Print / Download" > </center>
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
                <input type="hidden" id="modal_partner_id">
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
  

    $(".challan_not_generated").click(function(){

       swal("Not able to download challan", "Challan was not generated. Try to complete booking once again.");

    });

    $(".nodn").click(function(){
        swal("Not able to download GST declaration", "GST/Signature file file not available .");
    })  ;

$("#on_partner").DataTable({
    "pageLength": 100,
    dom: 'Bfrtip',
    // Configure the drop down options.
    "language": {                
                "searchPlaceholder": "Search by Any Column",
     },
    lengthMenu: [
        [ 25, 50,100, -1 ],
        [ '25', '50', '100', 'All' ]
    ],
    // Add to buttons the pageLength option.
    buttons: [
        'pageLength','excel',
    ],

});

    function downloadList(){
        $("#spareDownload").attr("disabled", true).html("Download In Progress");
        //$("#messageSpare").text("");
         $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>file_process/downloadSpareAssignedToPartner/',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                var jsondata = JSON.parse(data);
                
                if(jsondata['response'] === "success"){
                    //$("#spareDownload").css("display", "block");
                    //$("#messageSpare").text("");
                    $("#spareDownload").attr("disabled", false).html("Download List");
                    window.location.href = jsondata['path'];
                } else if(jsondata['response'] === "failed"){
                    alert(jsondata['message']);
                    $("#spareDownload").attr("disabled", false).html("Download List");
                    //$("#messageSpare").text("");
                } else {
                     $("#spareDownload").attr("disabled", false).html("Download List");
                }
            }
        });
    }

    $("#selectall_address").change(function () {
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        var d_m_d = $('.concern_detail:checked');
        var d_m_c = $('.checkbox_challan:checked');
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_c.length > 0 ) {
            $('.checkbox_manifest').prop('checked', false);
            $('#selectall_manifest').prop('checked', false);
            $('.concern_detail').prop('checked', false);
            $('#selectall_concern_detail').prop('checked', false);
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_challan').prop('checked', false);
        }
        $(".checkbox_address").prop('checked', $(this).prop("checked"));
    });
    $("#selectall_manifest").change(function () {
        var d_m = $('input[name="download_address[]"]:checked');
        var d_m_d = $('.concern_detail:checked');
        var d_m_c = $('.checkbox_challan:checked');
        
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_c.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('.concern_detail').prop('checked', false);
            $('#selectall_concern_detail').prop('checked', false);
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_challan').prop('checked', false);
        }
        $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
    });
    
    
    $("#selectall_concern_detail").change(function () {
       var d_m = $('.checkbox_address:checked');
       var d_m_d = $('.checkbox_challan:checked');
       var cb_m = $('.checkbox_manifest:checked');
        
       if (d_m.length > 0 || d_m_d.length > 0 ||cb_m.length > 0 ) {
           $('#selectall_challan_file').prop('checked', false);
           $('.checkbox_address').prop('checked', false);
           $('#selectall_address').prop('checked', false);
           $('.checkbox_manifest').prop('checked', false);
           $('.checkbox_challan').prop('checked', false);
           $('#selectall_challan').prop('checked', false);
           
       }
       $(".concern_detail").prop('checked', $(this).prop("checked"));
    });
    
    
    $("#selectall_challan").change(function () {
       var d_m = $('.checkbox_address:checked');
       var d_m_d = $('.checkbox_challan:checked');
       var cb_m = $('.checkbox_manifest:checked');
       
       if (d_m.length > 0 || d_m_d.length > 0 || cb_m.length > 0) {
           $('#selectall_challan_file').prop('checked', false);
           $('.checkbox_address').prop('checked', false);
           $('#selectall_address').prop('checked', false);
           $('.checkbox_manifest').prop('checked', false);
           $('#selectall_manifest').prop('checked', false);
           $('.checkbox_challan').prop('checked', false);           
       }
       $(".checkbox_challan").prop('checked', $(this).prop("checked"));
       
    
       var sf_id = $("#generate_challan_0").data("service_center_id");
       var flag = false;
       $('.checkbox_challan:checked').each(function(i) {
          var service_center_id = $(this).data("service_center_id");
          if(service_center_id != sf_id){
              flag = true;
          }
        });
        
        if(flag){
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_challan').prop('checked', false);
            alert("Not allow to select all option.");
        }
        
        
    });


    function check_checkbox(number) {
        
        if (number === 0) {
            var d_m = $('input[name="download_address[]"]:checked');
            var d_m_c = $('input[name="generate_challan[]"]:checked');
            var d_m_d = $('.concern_detail:checked');
            if (d_m.length > 0 || d_m_d.length > 0 || d_m_c.length > 0 ) {
                $('.checkbox_address').prop('checked', false);
                $('#selectall_address').prop('checked', false);
                $('.concern_detail').prop('checked', false);
                $('#selectall_concern_detail').prop('checked', false);
                $('.checkbox_challan').prop('checked', false);
                $('#selectall_challan').prop('checked', false);
            }
        }else if (number === 1) {
            var d_m = $('input[name="download_courier_manifest[]"]:checked');
            var d_m_d = $('.concern_detail:checked');
             var d_m_c = $('input[name="generate_challan[]"]:checked');
            if (d_m.length > 0 || d_m_d.length > 0 || d_m_c.length > 0) {
                $('.checkbox_manifest').prop('checked', false);
                $('#selectall_manifest').prop('checked', false);
                $('.concern_detail').prop('checked', false);
                $('#selectall_concern_detail').prop('checked', false);
                $('.checkbox_challan').prop('checked', false);
                $('#selectall_challan').prop('checked', false);
            }

        }else if(number === 2){
            var d_m = $('input[name="download_courier_manifest[]"]:checked');
            var d_m_d = $('.concern_detail:checked');
            var d_m_a = $('input[name="download_address[]"]:checked');
            if (d_m.length > 0 || d_m_d.length > 0 || d_m_a.length > 0 ) {
                $('.checkbox_manifest').prop('checked', false);
                $('#selectall_manifest').prop('checked', false);
                $('.concern_detail').prop('checked', false);
                $('#selectall_concern_detail').prop('checked', false);
                $('.checkbox_address').prop('checked', false);
                $('#selectall_address').prop('checked', false);
            }
            
        }else if(number === 3){
            $('#selectall_concern_detail').prop('checked', false); 
            var d_m = $('.checkbox_manifest:checked');
            var d_m_add = $('.checkbox_address:checked');
            var d_m_c = $('input[name="generate_challan[]"]:checked');
            if (d_m.length > 0 || d_m_add.length > 0 || d_m_c.length > 0) {
                $('.checkbox_address').prop('checked', false);
                $('#selectall_address').prop('checked', false);
                $('.checkbox_manifest').prop('checked', false);
                $('#selectall_manifest').prop('checked', false);
                $('.checkbox_challan').prop('checked', false);
                $('#selectall_challan').prop('checked', false);

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
        var partner_id = $(this).data('partner_id');
        $('#modal-title').text("Reject Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        $("#modal_partner_id").val(partner_id);
        
    });
    
    function reject_parts(){
        var remarks =  $('#textarea').val();
        if(remarks !== ""){
            var url =  $('#url').val();
            var modal_partner_id =  $('#modal_partner_id').val();
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge: 0, partner_id:modal_partner_id},
                success: function(data){
                    console.log(data);
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
    
    function checkValidationForBlank(){
        var address = $('.checkbox_address:checkbox:checked');
        var manifest = $('.checkbox_manifest:checkbox:checked');
        var courier_declaration = $('.concern_detail:checkbox:checked');
        var checkbox_challan = $('.checkbox_challan:checkbox:checked');
                
        if(address.length != 0 || manifest.length !=0 || courier_declaration.length !=0 || checkbox_challan.length !=0){
            return true;
        }
        else{
            alert("Please Select any checkbox");
            return false;
        }
   }
   $(document).on("click", ".checkbox_challan", function (i) {
        var service_center_id_arr = [];
        generate_challan_id = $(this).attr('id');
        $('.checkbox_challan:checked').each(function(i) {
           var service_center_id = $(this).data("service_center_id");
            
            if(i === 0){
                 service_center_id_arr.push(service_center_id);
            } else {
                if ($.inArray(service_center_id, service_center_id_arr) !== -1) {                
                  service_center_id_arr.push(service_center_id);
              } else {                  
                  $("#"+generate_challan_id).prop('checked', false);
                  alert("Do not allow to tick different vendor booking");
                  return false;
              }
            }
        });
   });
</script>
<?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('stock_not_exist')) {
    $this->session->unset_userdata('stock_not_exist');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>

<style>
    .dataTables_filter {
        float:right;
    }
</style>