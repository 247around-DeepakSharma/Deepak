<?php if ($is_ajax) { ?> 
    <table class="table table-bordered table-hover table-responsive">
        <thead>
            <tr>
                <th>Pincode 1</th>
                <th>Pincode 2</th>
                <th>Distance</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $table_data['pincode1']; ?></td>
                <td><?php echo $table_data['pincode2']; ?></td>
                <td contenteditable="true" id="distance"><?php echo $table_data['distance']; ?></td>
                <td><button class="btn btn-primary" onclick="submit_button('<?php echo $table_data['pincode1']; ?>',
                                    '<?php echo $table_data['pincode2']; ?>')">Update</button><input type="hidden" value="<?php echo $table_data['distance']; ?>" id="real_distance"></td>
            </tr>
        </tbody>
    </table>
<?php } else { ?> 
    <div id="page-wrapper" >
        <div class="container-fluid">
            <div class="distance_between_pincode" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
                <h3><strong>Distance Between Pincodes</strong></h3>
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
                <hr>
                <section class="get_pincode_distance" style="padding-left:20px;">
                    <div class="row">
                        <div class="row">
                            <div class="form-inline" style="margin-left: 20px;">
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="pincode1">Pincode 1:</label>
                                    <input type="text" class="form-control allownumericwithdecimal" id="pincode1" >
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="pincode2">Pincode 2:</label>
                                    <input type="text" class="form-control allownumericwithdecimal" id="pincode2">
                                </div>
                                <button class="btn btn-success" id="get_distance">Get Distance</button>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
                <hr>
                <section class="view_distance"></section>
            </div>
        </div>
    </div>
    <script type="text/javascript">


        $(".allownumericwithdecimal").on("keypress blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $(document).ready(function () {
            $('#get_distance').click(function () {
                var pincode1 = $('#pincode1').val();
                var pincode2 = $('#pincode2').val();
                if (pincode1.length === 6 && pincode2.length === 6) {
                    $.ajax({
                        method: 'POST',
                        data: {pincode1: pincode1, pincode2: pincode2},
                        url: '<?php echo base_url(); ?>employee/upcountry/get_distance_between_pincodes',
                        success: function (response) {
                            if(response === 'error'){
                                $('#show_error_msg').html('No Data Found For These Pincode');
                                $('.error').show().delay(5000).fadeOut();
                            }else{
                                $('.view_distance').html(response);
                            }
                            
                        }
                    });
                } else {
                    alert("Pincode Length Must Be 6 digit");
                }
            });
        });

        function submit_button(pincode1, pincode2) {

            var distance = $("#distance").text();
            var pattern = /^[0-9]+(\.[0-9]{1,2})?$/;
            var prev_distance = $('#real_distance').val();
            if (distance.length > 0 && distance.match(pattern) && distance !== '0' && distance !== prev_distance) {
                $.ajax({
                    method: 'POST',
                    data: {pincode1: pincode1, pincode2: pincode2, new_distance: distance},
                    url: '<?php echo base_url(); ?>employee/upcountry/update_pincode_distance',
                    success: function (response) {
                        if (response === 'success') {
                            $('#show_success_msg').html('Distance has been updated successfully');
                            $('.success').show().delay(5000).fadeOut();
                        } else if(response === 'error') {
                            $('#show_error_msg').html('Error in updating distance');
                            $('.error').show().delay(5000).fadeOut();
                        }
                    }
                });
            } else {
                if(distance === prev_distance){
                    alert("Please Enter New Distance To Update");
                }else{
                    alert("Please Enter Valid Distance");
                }
                
                return false;
            }
        }
    </script>
<?php } ?>

