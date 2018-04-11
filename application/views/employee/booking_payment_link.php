<div class="right_col" role="main" id="page-wrapper">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Create Link to send User For Payment</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <?php
                    if ($this->session->flashdata('err_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->flashdata('err_msg') . '</strong>
                                </div>';
                    };
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success_msg') . '</strong>
                                </div>';
                    };
                    ?>
                    <form method="POST" action="<?php echo base_url(); ?>employee/booking/process_create_booking_payment_link" class="form-inline">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_name">Mobile number</label>
                                        <input class="form-control" type="number" id="phone_number" name="phone_number" placeholder="Enter number at which sms to be sent">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_name">Email</label>
                                        <input class="form-control" type="email" id="email" name="email" placeholder="Enter email at which email to be sent">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="clonedInput" id="clonedInput">
                                    <table class="table  table-striped table-bordered">
                                        <tr>
                                            <th style="width: 40%;">
                                                <div class="form-group">
                                                    <label for="booking-id">Booking ID:</label>
                                                    <input type="text" class="form-control get_required" id="booking-id_1" name="booking_id[]">
                                                </div>
                                            </th>

                                            <th class="text-center">
                                                <button class="clone btn btn-sm btn-success" id="add_1">Add New Row</button>
                                            </th>
                                            <th class="text-center">
                                                <button class="remove btn btn-sm btn-danger" id="delete_1">Delete Row</button>
                                            </th>
                                        </tr>
                                    </table>
                                </div>
                                <div class="cloned"></div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <center><img id="loader_gif" src="" style="display: none;width:40px;"></center>
                                    <center><input type="submit" value="Submit" onclick="return check_validation()" class="btn btn-md btn-primary" /></center>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length + 1;

    function clone() {
        $(this).parents(".clonedInput").clone()
                .appendTo(".cloned")
                .attr("id", "cat" + cloneIndex)
                .find("*")
                .each(function () {
                    var id = this.id || "";
                    var match = id.match(regex) || [];
                    //console.log(match.length);
                    if (match.length === 3) {
                        this.id = match[1] + (cloneIndex);
                    }
                    $('#booking-id_' + cloneIndex).val('');
                })
                .on('click', 'button.clone', clone)
                .on('click', 'button.remove', remove);


        cloneIndex++;
        return false;
    }
    function remove() {
        var length = $(".clonedInput").length;

        if (length === 1) {
            alert("Atleast one row being added");
            return false;
        } else {
            $(this).parents(".clonedInput").remove();
        }


        return false;
    }
    $("button.clone").on("click", clone);

    $("button.remove").on("click", remove);

    function check_validation() {
        var validation = 1;
        
        var mobile_num = $('#phone_number').val();
        var email = $('#email').val();
        
        if(mobile_num === '' && email === ''){
            validation = 0;
            alert('Please Enter Either Phone Number Or Email');
        }
        
        if(mobile_num){
            var regxp = /^[6789]\d{9}$/;
            
            if (!regxp.test(mobile_num)) {
                alert("Please Enter Valid Phone Number");
                $(this).css({'border-color' : 'red'});
                validation = 0;
            }else{
                $(this).css({'border-color' : '#ccc'});
                validation = 1;
            }
        }
        
        
        $('.get_required').each(function (i) {
            var input_field = $("#" + this.id).val();

            switch (input_field) {
                case null:
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case typeof this === "undefined":
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case "":
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case false:
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
            }
        });
        
        if (validation === 0) {
            return false;

        } else if (validation === 1) {
            return true;
        }
    }
</script>