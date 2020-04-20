 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo GOOGLE_MAPS_API_KEY;?>"></script>
 <script src="<?php echo base_url();?>js/googleScript.js"></script>
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
                                    <input type="text" class="form-control allownumericwithdecimal" id="txtSource1" >
                                    <input type="hidden" class="form-control" id="txtSource" >
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="pincode2">Pincode 2:</label>
                                    <input type="text" class="form-control allownumericwithdecimal" id="txtDestination1">
                                    <input type="hidden" class="form-control" id="txtDestination">
                                </div>
                                <button class="btn btn-success" id="get_distance">Get Distance</button>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
                <hr>
                <section class="view_distance"></section>
                <section id="add_distance" style="display:none">
                    <table class="table table-bordered table-hover table-responsive" id="add_distance_table">
                        <tr>
                            <th>Pincode 1</th>
                            <th>Pincode 2</th>
                            <th>Distance</th>
                            <th>Update</th>
                        </tr>
                        <tr>
                            <td><span class='add_pincode1'></span></td>
                            <td><span class='add_pincode2'></span></td>
                            <td contenteditable="true" id="add_newDistance"><span class="add_newDistance"></span></td>
                            <td>
                                <button class="btnAdd btn btn-primary">Add Distance</button>
                            </td>
                        </tr>
                    </table>
                </section>
                
            </div>
        </div>
        <div class="container-fluid">
            <div class="distance_between_pincode" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
<!--                <h3><strong>Get Distance From Google Map</strong></h3>-->
<!--                <section  style="padding-left:20px;">
                    <div class="row">
                        <div class="row">
                            <div class="form-inline" style="margin-left: 20px;">
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="pincode1">Source :</label>
                                    <input type="text" class="form-control" id="txtSource" >
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <label for="pincode2">Destination:</label>
                                    <input type="text" class="form-control" id="txtDestination">
                                </div>
                                <button class="btn btn-success" onclick="GetRoute()">Get Route</button>
                            </div>
                        </div>
                    </div>
                </section>-->
<!--                 <hr>-->
                <section style="padding-left:20px;"><div id="dvDistance">
                </div></section>
<!--                  <hr>-->
                <section style="padding-left:20px;">
                    <table><tr>
            <td colspan="2">
                <div id="dvDistance">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div id="dvMap" style="width: 600px; height: 500px">
                </div>
            </td>
            <td>
                <div id="dvPanel" style="width: 500px; height: 500px">
                </div>
            </td>
        </tr></table>
                </section>
            </div>
    </div>
    <script type="text/javascript">


        $(".allownumericwithdecimal").on("keypress blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
        
        $(document).on('keyup', '#txtSource1', function (e) {
          var pincode1 = $('#txtSource1').val();
           $('#txtSource').val(pincode1);
        });
         $(document).on('keyup', '#txtDestination1', function (e) {
          var pincode1 = $('#txtDestination1').val();
           $('#txtDestination').val(pincode1);
        });

        $(document).ready(function () {
            $('#get_distance').click(function () {
                GetRoute();
                var pincode1 = $('#txtSource1').val();
                var pincode2 = $('#txtDestination1').val();
                if (pincode1.length === 6 && pincode2.length === 6) {
                    $.ajax({
                        method: 'POST',
                        data: {pincode1: pincode1, pincode2: pincode2},
                        url: '<?php echo base_url(); ?>employee/upcountry/get_distance_between_pincodes',
                        success: function (response) {
                            if (response === 'error') {
                                $('#show_error_msg').html('No Data Found For These Pincode');
                                $('.error').show().delay(5000).fadeOut();
                                $('.view_distance').hide();
                                $('.add_pincode1').html(pincode1);
                                $('.add_pincode2').html(pincode2);
                                $('#add_distance').show();
                            } else {
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
                            $('.view_distance').hide();
                        } else if (response === 'error') {
                            $('#show_error_msg').html('Error in updating distance');
                            $('.error').show().delay(5000).fadeOut();
                        }
                    }
                });
            } else {
                if (distance === prev_distance) {
                    alert("Please Enter New Distance To Update");
                } else {
                    alert("Please Enter Valid Distance");
                }

                return false;
            }
        }

        $(document).ready(function () {

            $("#add_distance_table").on('click', '.btnAdd', function () {
                
                var currentRow = $(this).closest("tr");

                var pincode1 = currentRow.find(".add_pincode1").html(); 
                var pincode2 = currentRow.find(".add_pincode2").html();
                var distance = $("#add_newDistance").text();
                var pattern = /^[0-9]+(\.[0-9]{1,2})?$/;
                if (distance.length > 0 && distance.match(pattern) && distance !== '0' && pincode1.length === 6 && pincode2.length === 6) {
                $.ajax({
                    method: 'POST',
                    data: {pincode1: pincode1, pincode2: pincode2, new_distance: distance},
                    url: '<?php echo base_url(); ?>employee/upcountry/add_new_pincode_distance',
                    success: function (response) {
                        if (response === 'success') {
                            $('#show_success_msg').html('Pincode has been Inserted successfully');
                            $('.success').show().delay(5000).fadeOut();
                            $('#add_distance_table').hide();
                        } else if (response === 'error') {
                            $('#show_error_msg').html('Error in Inserting distance');
                            $('.error').show().delay(5000).fadeOut();
                        }
                    }
                });
            } else {
                alert("Please fill the correct value");

                return false;
            }
                
            });
        });
    </script>
<?php } ?>




