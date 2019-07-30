
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>Upcountry Details </h2>
                </div>
                <div class="panel-body">
                    <div class="success" style="display:none">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button><span id="show_success_msg"></span></div>
                    </div>
                    <div class="error" style="display:none">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button><span id="show_error_msg"></span></div>
                    </div>
                    <div class="table-responsive table-editable" id="table" >
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Pincode</th>
                                    <th>Upcountry Rate (Per KM)</th>
                                    <th colspan="2">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn_no = 1;
                                foreach ($data as $value) { ?>
                                    <tr id="<?php echo "table_tr_" . $sn_no; ?>">
                                        <td><?php echo $sn_no; ?></td>
                                        <td><span id="<?php echo "state" . $sn_no; ?>"><?php echo $value['state']; ?></span></td>
                                        <td><span id="<?php echo "district" . $sn_no; ?>" name="<?php echo "district" . $sn_no; ?>" ><?php echo $value['district']; ?></span></td>
                                        <td><span id="<?php echo "pincode" . $sn_no; ?>" ><?php echo $value['pincode']; ?></span></td>
                                        <td>
                                            <select id="<?php echo "upcountry_rate" . $sn_no; ?>" name="" class='form-control'>
                                                <?php if(!$saas) { for($i=2;$i<=3;$i++) { ?>
                                                    <option value="<?php echo $i;?>" <?php if($i == $value['upcountry_rate']) {echo 'selected';}?>><?php echo $i;?></option>
                                                <?php }} else { for($i=1;$i<=10;$i++) { ?>
                                                     <option value="<?php echo $i;?>" <?php if($i == $value['upcountry_rate']) {echo 'selected';}?>><?php echo $i;?></option>
                                                <?php }} ?>
                                            </select>
                                        </td>
                                        <td><button class="btn btn-primary" 
                                                    onclick="submit_button('<?php echo $value["id"]; ?>',
                                                               '<?php echo $sn_no; ?>', '<?php echo $value["service_center_id"];
                                                ; ?>')">Submit</button></td>
                                        <td>
                                            
                                            <?php $value['active']; if($value['active'] == 1) {?>
                                            <button class="btn btn-danger" 
                                                    onclick="delete_details('<?php echo $value["id"]; ?>',
                                                               '<?php echo $sn_no; ?>', '<?php echo $value["service_center_id"];
                                                ; ?>', '0')">De-Activate</button>
                                            <?php } else  { ?>
                                                 <button class="btn btn-success" 
                                                    onclick="delete_details('<?php echo $value["id"]; ?>',
                                                               '<?php echo $sn_no; ?>', '<?php echo $value["service_center_id"];
                                                ; ?>', '1')">Activate</button>
                                          <?php  }?>
                                        </td>
                                    </tr>

    <?php $sn_no++;
} ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

</div>
<style>

    table {
        word-wrap:break-word;
        table-layout:fixed;
    }

</style>
<script type="text/javascript">
    function submit_button(id, div_no, service_center_id) {

        var upcountry_rate1 = $("#upcountry_rate" + div_no).val();
        
        upcountry_rate = Number(upcountry_rate1);
        <?php if($saas) { ?>
            if (upcountry_rate === 2 || upcountry_rate === 3) {
                update_upcountry_rate(id, service_center_id);
            } else {
                alert("Please Enter Either 2 or 3 in upcountry rate");
                return false;
            }
        <?php } else { ?>
            if (upcountry_rate >= 1 && upcountry_rate <= 10) {
                update_upcountry_rate(id, service_center_id);
            } else {
                alert("Please enter upcountry rate between 1 to 10.");
                return false;
            }
        <?php } ?>
    }
    
    function update_upcountry_rate(id, service_center_id) {
        var event_taget = event.target;
        var event_element = event.srcElement;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/update_sub_service_center_details',
            data: {upcountry_rate: upcountry_rate, id: id, service_center_id: service_center_id},
            success: function (data) {
                if ($.trim(data) === 'success') {
                    $('#show_success_msg').html('Details has been Updated successfully');
                    $('.success').show().delay(5000).fadeOut();
                } else {
                    $('#show_error_msg').html('Error in updating details');
                    $('.error').show().delay(5000).fadeOut();
                }
                location.reload();
            }
        });
        //$(event_taget || event_element).parents('tr').hide();
    }
    function delete_details(id, div_no, service_center_id, active_flag) {

        var event_taget = event.target;
        var event_element = event.srcElement;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/de_activate_sub_service_center_details/'+active_flag,
            data: {id: id, service_center_id: service_center_id},
            success: function (data) {
                
                if (data === 'success') {
                    if(active_flag === 1){
                        $('#show_success_msg').html('HQ Activated successfully');
                        $('.success').show().delay(5000).fadeOut();
                        
                    } else {
                        $('#show_success_msg').html('HQ De-Activated successfully');
                        $('.success').show().delay(5000).fadeOut();
                    }
                } else {
                    $('#show_error_msg').html('Error in deleting details');
                    $('.error').show().delay(5000).fadeOut();
                    ;
                }
                location.reload();
            }
        });
        //$(event_taget || event_element).parents('tr').hide();
    }

    $(".allownumericwithdecimal").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>
