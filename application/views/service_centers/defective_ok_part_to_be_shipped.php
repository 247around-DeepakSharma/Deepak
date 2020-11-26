<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
}
?>
<div role="tabpanel" class="tab-pane" id="estimate_cost_given">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" > 
                        <form target="_blank"  action="<?php echo base_url(); ?>employee/service_centers/print_partner_address_challan_file" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                        <table id="estimate_cost_given_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Booking Id</th>
                                        <th class="text-center">User Name</th>
                                        <th class="text-center">Age of Pending</th>
                                        <th class="text-center">Parts Received</th>
                                        <th class="text-center">Parts Code </th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Consumption</th>
                                        <th class="text-center">Consumption Reason</th>
                                        <th class="text-center" >Spare Tag/Address<br><input type="checkbox" id="selectall_spare_tag" > </th>
<!--                                        <th class="text-center" >Address<br><input type="checkbox" id="selectall_address" > </th>-->
                                        <th class="text-center" >Challan<br><input type="checkbox" id="selectall_challan_file" > </th>   
                                        <th class="text-center" >Bulk Send<br><input type="checkbox" id="selectall_send_courier" > </th>                          
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($spare_parts as $key => $row) {
                                    ?>
                                        <tr style="text-align: center;<?php if (!is_null($row['remarks_defective_part_by_wh'])) {
                                            echo "color:red";
                                        } ?>">
                                            <td>
                                                <?php echo $sn_no; ?>
                                            </td>
                                            <td>
                                                <a  href="<?php echo base_url(); ?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id'])); ?>"  title='View'><?php echo $row['booking_id']; ?></a>
                                            </td>
                                            <td>
                                                <?php echo $row['name']; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $remaining_days = '';
                                                    if (!is_null($row['service_center_closed_date'])) {
                                                        $age_shipped = date_diff(date_create($row['service_center_closed_date']), date_create('today'));
                                                        echo $age_shipped->days . " Days";

                                                        if($age_shipped->days <= SF_SPARE_OOT_DAYS) {

                                                        $remaining_days = (int) SF_SPARE_OOT_DAYS - $age_shipped->days;  
                                                ?>
                                                <div class="blink" style="font-size:13px;font-weight:bold;color:#f5a142;"><?php echo PART_TO_BE_BILLED. ' in '.$remaining_days.' Days'; ?></div>
                                                <?php } else { ?>
                                                    <div class="text-danger" style="font-size:13px;font-weight:bold;"><?php ?></div>    
                                                <?php } }?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['parts_shipped']; ?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['part_number']; ?>
                                            </td>
                                            <td style="word-break: break-all;">
                                                <?php echo $row['shipped_quantity']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['challan_approx_value']; ?>
                                            </td>

                                           
                                            <td><?php if ($row['is_consumed'] == 1) {
                                                    echo 'Yes';
                                                } else {
                                                    echo 'No';
                                                } ?></td>
                                            <td><?php echo $row['consumed_status']; ?></td>
                                            <td>
                                                <input type="checkbox" class="form-control checkbox_spare_tag" onclick="remove_all_spare_tag_all()" name="download_spare_tag[]"  value="<?php echo $row['id']; ?>" />
                                            </td>
<!--                                            <td>
                                                <input type="checkbox" class="form-control checkbox_address" onclick="remove_select_all()" name="download_address[]"  value="<?php echo $row['id']; ?>" />
                                            </td>-->
                                            <td>

                                        <?php if (!$partner_on_saas) { ?>
                                                    <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan(this.id)" id="download_challan_<?php echo  $i; ?>" name="download_challan[]"  value="<?php echo $row['challan_file']; ?>" <?php if(empty($row['challan_file'])){echo 'disabled';} ?>/>
                                        <?php } else { ?>

                                                    <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan(this.id)" id="download_challan_<?php echo  $i; ?>" name="download_challan[<?php echo $row['defective_return_to_entity_id']; ?>][]" id="download_challan_<?php echo $i; ?>" value="<?php echo $row['id'] ?>" />

                                        <?php } ?>

                                            </td>  

                                            <td>
                                                <input type="checkbox" data-booking_partner_id="<?php echo $row['booking_partner_id']; ?>"  data-sf_id="<?php echo $row['service_center_id']; ?>" data-mobile="<?php echo $row['mobile']; ?>" data-user_name="<?php echo $row['name']; ?>" class="form-control checkbox_courier" onclick="remove_select_all_courier()" name="send_courier[]"  value="<?php echo $row['id']; ?>" />
                                            </td>                                

                                            <td>
                                                <a href="<?php echo base_url() ?>service_center/update_defective_parts/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                            </td>

                                        </tr>
                                    <?php $sn_no++; $i++;
                                } ?>
                                </tbody>
                        </table>
                             <center> 
                                <input type= "submit" id="button_send" class="btn btn-danger" onclick='return check_checkbox()' style="text-align: center; background-color:#2C9D9C; border-color: #2C9D9C;"  data-toggle="modal" value ="Print Shipment Address / Challan File /  Send Dective Parts" >

                            </center>
                            </form>
                        
                        <div class="custom_pagination" style="margin-left: 16px;" > <?php if (isset($links)) echo $links; ?></div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div id="courier_update" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <form id="idForm"  action="<?php echo base_url(); ?>employee/service_centers/do_multiple_spare_shipping"  method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal-title">Send Bulk Courier</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="awb" class="col-md-4">AWB *</label>
                                <div class="col-md-6">
                                    <input onblur="check_awb_exist()" type="text" class="form-control" id="awb_by_sf" name="awb_by_sf" value="" placeholder="Please Enter AWB" required="">
                                </div>
                            </div>
                            <div id="courier_charges_by_sfrow" class="form-group ">
                                <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                <div class="col-md-6">

                                    <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value="<?php
                                    if ((set_value("courier_charges_by_sf"))) {
                                        echo set_value("courier_charges_by_sf");
                                    }
                                    ?>" onblur="chkPrice($(this), 2000)" placeholder="Please Enter Courier Charges" required="">
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="awb" class="col-md-4">No Of Boxes *</label>
                                <div class="col-md-6">
                                    <select class="form-control" id="defective_parts_shipped_boxes_count" name="defective_parts_shipped_boxes_count" required="">
                                        <option selected="" disabled="" value="">Select Boxes</option>
                                        <?php for ($i = 1; $i < 31; $i++) { ?>
                                            <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group       " id="exist_courier_image_row">
                                <label for="AWS Receipt" class="col-md-4">Courier Invoice *</label>
                                <div class="col-md-6">
                                    <input id="aws_receipt" class="form-control" name="defective_courier_receipt" type="file" required="" value="" style="background-color:#fff;pointer-events:cursor">
                                    <input type="hidden" class="form-control" value="" id="exist_courier_image" name="exist_courier_image">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="courier" class="col-md-4">Courier Name *</label>
                                <div class="col-md-6">
                                    <select class="form-control" id="courier_name_by_sf" name="courier_name_by_sf" required="">
                                        <option selected="" disabled="" value="">Select Courier Name </option>

                                        <?php foreach ($courier_details as $value1) { ?> 
                                            <option <?php
                                            if ((set_value("courier_name_by_sf") == $value1['courier_name'])) {
                                                echo "selected";
                                            }
                                            ?> value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>

                                    </select>
                                </div>

                            </div>
                            <br>
                            <div class="form-group ">
                                <label for="shipment_date" class="col-md-4">Shipment Date *</label>
                                <div class="col-md-6">
                                    <div class="input-group input-append date">
                                        <input id="defective_part_shipped_date" class="form-control" name="defective_part_shipped_date" type="text" value="<?php echo date("Y-m-d", strtotime("+0 day")); ?>" required="" readonly="true" style="background-color:#fff;pointer-events:cursor">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="courier" class="col-md-4">Weight *</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" style="width: 25%; display: inline-block;" id="defective_parts_shipped_weight_in_kg" name="defective_parts_shipped_kg" value="" placeholder="Weight" required=""> <strong> in KG</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="number" class="form-control" style="width: 25%; display: inline-block;" id="defective_parts_shipped_weight_in_gram" value="" name="defective_parts_shipped_gram" placeholder="Weight" required="">&nbsp;<strong>in Gram </strong>                                       
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="remarks_defective_part" class="col-md-4">Remarks *</label>
                                <div class="col-md-6">
                                    <textarea type="text" class="form-control" id="remarks" name="remarks_defective_part" placeholder="Please Enter Remarks" required=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="courier_boxes_weight_flag" id="courier_boxes_weight_flag" value="0">
                    <input type="hidden" name="courier_charges_by_sf_hidden" id="courier_charges_by_sf_hidden" value="0">
                    <input type="hidden" name="sp_ids" id="spareids" value="">
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
                    </center>
                </div>
            </div>
        </form>
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
        background: url('<?php echo base_url(); ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
    }
</style>
<script type="text/javascript">

    $('#myTabs a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr("data-url");

        var href = this.hash;
        $(this).tab('show');
        load_view(url, href);
    });

    function load_view(url, tab) {
        //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");

        $.ajax({
            type: "POST",
            url: url,
            data: {is_ajax: true},
            success: function (data) {
                $(tab).html(data);
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }

    $(document).ready(function () {
        $('#button_send').click(function () {
            $('#courier_charges_by_sf').css("border-color", "#ccc");
        })
        $('#courier_charges_by_sf').on('focus', function () {
            $(this).css("border-color", "#ccc");
        });
    });
    function submitForm() {
        event.preventDefault();
        var courier_price = $('#courier_charges_by_sf').val();
        if (!/^\d+(\.\d+)?$/g.test(courier_price)) {              //should be number only with one decimal 
            alert("Courier price should be numerical and should not contain alphabets and special characters except decimal.")
            $('#courier_charges_by_sf').css("border-color", "red");
            return false;
        }
        var courier_price = parseFloat(courier_price);
        if (courier_price < 0 || courier_price > 2000) {                              //should be in between 0 and 2000
            alert('Courier price should be in between 0 and 2000.');
            $('#courier_charges_by_sf').css("border-color", "red");
            return false;
        }
        $(".loader").removeClass('hide');
        if ($("#courier_charges_by_sf_hidden").val() != 0)
        {
            $("#courier_charges_by_sf").val($("#courier_charges_by_sf_hidden").val())
        }


      let kg = $("#defective_parts_shipped_weight_in_kg").val();
      let gm = $("#defective_parts_shipped_weight_in_gram").val();
      let total = parseInt(kg)+parseInt(gm);
      if(!total){
      swal("Error !", "Sum of weight in KG and GM must be greater than 0");
      return false;
      }


        var form_data = new FormData(document.getElementById("idForm"));

        $.ajax({
            url: "<?php echo base_url() ?>employee/service_centers/do_multiple_spare_shipping",
            type: "POST",
            data: form_data,
            processData: false, // tell jQuery not to process the data
            contentType: false   // tell jQuery not to set contentType
        }).done(function (response) {
            console.log(response);
            $(".loader").addClass('hide');
            var resp = '';
            try {
                resp = JSON.parse(response);
            } catch (err) {
                swal({title: "Error", text: "Response Error: Invalid or malformed response.", type: "error"});
            }
            if (!!resp.error) {
                swal({title: "Failed !", text: resp.errorMessage, type: "error", html: true});
            } else {
                swal({title: "Updated !", text: "Your courier details updated .", type: "success"},
                function () {
                    location.reload();
                }
                );
            }

        });

    }



    $('#courier_name_by_sf').select2({
        width: '100%',
        placeholder: 'Select Courier Name',
        allowClear: true
    });


    $('#defective_part_shipped_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate: function () {
            var today = new Date();
            var yesterday = new Date();
            yesterday.setDate(today.getDate() - 2);
            return yesterday;
        }(),
        maxDate: new Date(),
        setDate: new Date(),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('#defective_part_shipped_date').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    $('#defective_part_shipped_date').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    $("#defective_parts_shipped_weight_in_kg").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }


        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }

        },
        "mouseout": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3 || weight_kg < 0) {
                $(this).val('');
                return false;
            }
        }
    });


    $("#defective_parts_shipped_weight_in_gram").on({
        "click": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }


        },
        "keypress": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 2) {
                $(this).val('');
                return false;
            }
 
        },
        "mouseleave": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3) {
                $(this).val('');
                return false;
            }
        },
        "mouseout": function () {
            var weight_kg = $(this).val();
            if (weight_kg.length > 3 || weight_kg < 0) {
                $(this).val('');
                return false;
            }
        }
    });


    $('#defective_parts_shipped_weight_in_gram,#defective_parts_shipped_weight_in_kg').bind('keydown', function (event) {
        switch (event.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
                break;
            default:
                var regex = new RegExp("^[a-df-zA-DF-Z0-9,]+$");
                var key = event.key;
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
                break;
        }
    });

    function check_checkbox() {

        var flag = 0;
        //$('.checkbox_address').each(function (i) {

        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m_s.length > 0) {
            flag = 1;
        }
        
        var d_m = $('.checkbox_address:checked');
        if (d_m.length > 0) {
            flag = 1;
        }

        if (flag === 0) {
            var d_m = $('.checkbox_challan:checked');
            if (d_m.length > 0) {
                flag = 1;
            }
        }

        if (flag === 0) {
            var c_m_c = $('.checkbox_courier:checked');
            if (c_m_c.length > 0) {
                flag = 1;
            }
        }

        //});

        if (flag === 0) {
            alert("Please Select Atleast One Checkbox");
            return false;
        }
    }

    $("#selectall_address").change(function () {
        var d_m = $('.checkbox_challan:checked');
        var d_mm = $('.checkbox_courier:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_mm.length > 0 || d_m_s.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
        }

        $(".checkbox_address").prop('checked', $(this).prop("checked"));
        $("#button_send").val("Print Shipment Address");
        $("#button_send").attr("type", "submit");
        $("#button_send").removeAttr("data-target");


    });

    $("#selectall_challan_file").change(function () {
        var d_m = $('.checkbox_address:checked');
        var d_mm = $('.checkbox_courier:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_mm.length > 0 || d_m_s.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
        }
        var total_lineItems = $('.checkbox_challan').length;
        $(".checkbox_challan").prop('checked', false); 
        for( i = 1; i <= total_lineItems; i++ ){
            if(i <= 30){
              $("#download_challan_"+i).prop('checked', $(this).prop("checked"));
            }
        }
        $("#button_send").val("Challan File");
        $("#button_send").attr("type", "submit");
        $("#button_send").removeAttr("data-target");
    });



    $("#selectall_send_courier").change(function () {
        var d_m = $('.checkbox_address:checked');
        var d_mm = $('.checkbox_challan:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_mm.length > 0 || d_m_s.length > 0 ) {

            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }
        $(".checkbox_courier").prop('checked', $(this).prop("checked"));
        $("#button_send").val("Send Defective Parts");
        $("#button_send").attr("type", "button");
        $("#button_send").attr("data-target", "#courier_update");
    });
    
    
    $("#selectall_spare_tag").change(function () {
        var d_m = $('.checkbox_challan:checked');
        var d_mm = $('.checkbox_courier:checked');
        var d_ms = $('.checkbox_address:checked');
        if (d_m.length > 0 || d_mm.length > 0 || d_ms.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('.checkbox_address').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }

        $(".checkbox_spare_tag").prop('checked', $(this).prop("checked"));
        $("#button_send").val("Print Spare Tag");
        $("#button_send").attr("type", "submit");
        $("#button_send").removeAttr("data-target");


    });


    function remove_select_all() {
        $('#selectall_address').prop('checked', false);
        $('#selectall_send_courier').prop('checked', false);
        $('#selectall_challan_file').prop('checked', false);
        $('#selectall_spare_tag').prop('checked', false);
        var d_m = $('.checkbox_challan:checked');
        var d_m_d = $('.checkbox_courier:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_s.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
        }
    }
    
    function remove_all_spare_tag_all() {
        $('#selectall_spare_tag').prop('checked', false);
        $('#selectall_address').prop('checked', false);
        $('#selectall_send_courier').prop('checked', false);
        $('#selectall_challan_file').prop('checked', false);
        var d_m = $('.checkbox_challan:checked');
        var d_m_d = $('.checkbox_courier:checked');
        var d_m_addr = $('.checkbox_address:checked');
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_addr.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('.checkbox_address').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }
    }
    

    function remove_select_all_challan(checkBox_id) {
        $('#selectall_challan_file').prop('checked', false);
        $('#selectall_send_courier').prop('checked', false);
        $('#selectall_address').prop('checked', false);
        $('#selectall_spare_tag').prop('checked', false);
        var d_m = $('.checkbox_address:checked');
        var d_m_d = $('.checkbox_courier:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_s.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
        }
        var total_lineItmes = $('.checkbox_challan:checked').length;
            
         if(total_lineItmes > 30){
                $("#"+checkBox_id).prop('checked', false);
                alert('You can not select more than 30.');
         }
    }

    function remove_select_all_courier() {
        $('#selectall_send_courier').prop('checked', false);
        $('#selectall_challan_file').prop('checked', false);
        $('#selectall_address').prop('checked', false);
        $('#selectall_spare_tag').prop('checked', false);
        var d_m = $('.checkbox_address:checked');
        var d_m_d = $('.checkbox_challan:checked');
        var d_m_s = $('.checkbox_spare_tag:checked');
        if (d_m.length > 0 || d_m_d.length > 0 || d_m_s.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_spare_tag').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_spare_tag').prop('checked', false);
        }
    }



    $(".checkbox_challan").click(function () {
        if ($('.checkbox_challan:checkbox:checked').length > 0) {
            $("#button_send").val("Challan File");
            $("#button_send").attr("type", "submit");
            $("#button_send").removeAttr("data-target");
        }
    });
    
    $(".checkbox_spare_tag").click(function () {
        if ($('.checkbox_spare_tag:checkbox:checked').length > 0) {
            $("#button_send").val("Print Spare Tag");
            $("#button_send").attr("type", "submit");
            $("#button_send").removeAttr("data-target");
        }
    });
    
    
    $(".checkbox_address").click(function () {
        if ($('.checkbox_address:checkbox:checked').length > 0) {
            $("#button_send").val("Print Shipment Address");
            $("#button_send").attr("type", "submit");
            $("#button_send").removeAttr("data-target");
        }
    });


    $(".checkbox_courier").click(function () {
        if ($('.checkbox_courier:checkbox:checked').length > 0) {
            $("#button_send").val("Send Defective Parts");
            $("#button_send").attr("type", "button");
            $("#button_send").attr("data-target", "#courier_update");

        }
    });


    $("#button_send").click(function () {
        yourArray = [];

        $(".checkbox_courier:checked").each(function () {
            yourArray.push($(this).val());
        });

        $("#spareids").val(yourArray);


    });




    function check_awb_exist() {
        var awb = $("#awb_by_sf").val();
        var characterReg = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
        if (characterReg.test(awb) && awb != '') {
            awb = '';
            $("#awb_by_sf").val('');
            alert('Special Characters are not allowed in AWB.');
            return false;
        }
        if (awb != '') {
            $.ajax({
                type: 'POST',
                beforeSend: function () {

                    $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });

                },
                url: '<?php echo base_url() ?>employee/service_centers/check_sf_shipped_defective_awb_exist',
                data: {awb: awb},
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if (data.code === 247) {


                        // $("#same_awb").css({"color": "green", "font-weight": "900"});

                        //  $("#same_awb").css("font-wight",900);
                        //alert("This AWB already used same price will be added");
                        //$("#same_awb").css("display", "block");
                        $('body').loadingModal('destroy');



                        $("#defective_part_shipped_date").val(data.message[0].defective_part_shipped_date);

                        $("#courier_name_by_sf").val("");
                        $("#courier_name_by_sf").attr('readonly', "readonly");
                        $("#courier_name_by_sf").css("pointer-events", "none");
                        var courier = data.message[0]['courier_name_by_sf'].toLowerCase();
                        // $('#courier_name_by_sf option[value="'+data.message[0].courier_name_by_sf.toLowerCase()+'"]').attr("selected", "selected");
                        $('#courier_name_by_sf').val(courier).trigger('change');
                        if (data.message[0].courier_charge > 0) {
                            $("#courier_charges_by_sf").val(data.message[0].courier_charge);
                            $("#courier_charges_by_sf_hidden").val(data.message[0].courier_charge);
                            // $("#courier_charges_by_sf").attr('readonly', "readonly");
                        }



                        // $("#courier_charges_by_sf").css("display","none");
                        $('#defective_parts_shipped_boxes_count option[value="' + data.message[0]['box_count'] + '"]').attr("selected", "selected");
                        if (data.message[0]['box_count'] === 0) {
                            $('#defective_parts_shipped_boxes_count').val("");

                        } else {
                            $('#defective_parts_shipped_boxes_count').val(data.message[0]['box_count']).trigger('change');

                        }

                        $("#courier_boxes_weight_flag").val(data.message[0]['partcount']);
                        //$("#aws_receipt").removeAttr("required");
                        /*
                         if (data.message[0].defective_courier_receipt) {
                         
                         
                         $("#exist_courier_image").val(data.message[0].defective_courier_receipt);
                         $("#aws_receipt").css("display", "none");
                         
                         }
                         */

                        //    alert(data.message[0]['partcount'])
                        var wt = Number(data.message[0]['billable_weight']);
                        if (wt > 0) {
                            var wieght = data.message[0]['billable_weight'].split(".");
                            $("#defective_parts_shipped_weight_in_kg").val(wieght[0]).attr('readonly', "readonly");
                            $("#defective_parts_shipped_weight_in_gram").val(wieght[1]).attr('readonly', "readonly");
                        }

                    } else {
                        $('body').loadingModal('destroy');
                        $("#aws_receipt").css("display", "block");
                        $("#courier_charges_by_sf").css("display", "block");
                        $("#same_awb").css("display", "none");
                        $("#exist_courier_image").removeAttr("readonly");
                        $("#courier_name_by_sf").val("");
                        $("#courier_name_by_sf").val("");
                        $("#courier_charges_by_sf").removeAttr('readonly');
                        $("#courier_charges_by_sf").val("");
                        $("#aws_receipt").attr("required", "required");
                        $("#defective_part_shipped_date").val("");
                        $("#defective_parts_shipped_boxes_count").val("");
                        $("#defective_parts_shipped_weight_in_kg").removeAttr('readonly');
                        $("#defective_parts_shipped_weight_in_gram").removeAttr('readonly');
                        $("#defective_parts_shipped_weight_in_kg").val("");
                        $("#defective_parts_shipped_weight_in_gram").val("");
                        $("#remarks").val("");
                        $("#aws_receipt").css("display", "block");
                        $("#courier_boxes_weight_flag").val("0");

                    }

                }
            });
        }

    }

    /*
     * Use to validate the courier charges.
     */
    function chkPrice(curval, maxval) {
        //alert(curval.val());
        let flg = true;
        if (!isNaN(curval.val())) {
            if (parseFloat(curval.val()) < 1) {
                alert('Courier Charges cannot be less than 1.00');
                flg = false;
            } else if (parseFloat(curval.val()) > parseFloat(maxval)) {
                alert('Courier Charges cannot be more than ' + maxval);
                flg = false;
            }
        } else {
            alert('Enter numeric value');
            flg = false;
        }
        if (!flg)
        {
            window.setTimeout(function () {
                curval.focus();
            }, 0);

        }

    }


</script>
<style type="text/css">
    .sweet-alert {

        width: 700px !important;
        left: 46% !important;
    }
    .modal-lg {
        /* width: 1300px; */
        width: 95% !important;
    }
    .form-control{
        margin-bottom: 10px;
    }
    .input-group{
        margin-bottom: 10px; 
    }
</style>
