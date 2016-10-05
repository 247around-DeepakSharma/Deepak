<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">

            <div class="panel panel-info" style="margin-top:8px;">
                <div class="panel-heading"><center><strong style="font-size:140%;">Search Booking</strong></center></div>
            </div>
            <div class="search_panel">
                <div class="container" style="margin-left:90px;width:90%;">
                    <?php
                    if ($this->session->flashdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('error') . '</strong>
                    </div>';
                    }
                    ?>
                    <?php
                if ($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
                }
                ?>
                    <form name="my_Search_Form" class="form-horizontal" action="<?php echo base_url() ?>employee/partner/finduser" method="POST" onsubmit="return phonevalidate()">
                        <div class="clear"></div>
                        <div class="form-group <?php
                        if (form_error('phone_number')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="phone_number" class="col-md-4">Phone Number</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control fa fa-search" name="phone_number" value="<?php echo set_value('phone_number'); ?>" placeholder="Enter Phone No.">
                                <?php echo form_error('phone_number'); ?>
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" style="margin-left:45%;margin-top: -10px;margin-bottom: -10px;"/>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="order id" class="col-md-4">Order ID</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control fa fa-search" id="order_id" name="order_id" value="<?php echo set_value('order_id'); ?>" placeholder="Enter Order ID">
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" style="margin-left:45%;margin-top: -10px;margin-bottom: -10px;"/>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="serial_number" class="col-md-4">Serial No</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control fa fa-search" id="serial_number" name="serial_number" value="<?php echo set_value('serial_number'); ?>" placeholder="Enter Serial No">
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" style="margin-left:45%;margin-top: -10px;margin-bottom: -10px;"/>
                        <div class="clear"></div>
                        <div class="form-group <?php
                        if (form_error('booking_id')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="booking_id" class="col-md-4">247 Booking ID</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control fa fa-search" id="booking_id" name="booking_id" value="<?php echo set_value('booking_id'); ?>" placeholder="Enter Booking ID">
                                <?php echo form_error('booking_id'); ?>
                            </div>
                        </div>
                </div>
            </div>
            <div class="form-group">
                <center>
                    <?php echo "<input type='submit' value='Find' class='btn btn-lg btn-primary clear'>" ?>
                </center>
            </div>

            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function phonevalidate() {
        var ph_no = document.forms["my_Search_Form"]["phone_number"].value;
        var booking_id = document.forms["my_Search_Form"]["booking_id"].value;
        var order_id = document.forms["my_Search_Form"]["order_id"].value;
        var serial_no = document.forms["my_Search_Form"]["serial_number"].value;
        var exp1 = /^[7-9]{1}[0-9]{9}$/;
        var exp2 = /^[a-zA-Z0-9]{6,}$/;

        if (ph_no == "" && booking_id == "" && serial_no =="" && order_id == "" ) {
                alert("Please enter atleast one detail to search");
                return false;
            }

            if (ph_no != "" && booking_id != "" && serial_no !="" && order_id !="") {
                alert("Please fill only one field");
                return false;
            }

            if (ph_no != ""  && serial_no !="" ) {
                alert("Please fill only one field");
                return false;
            }

            if (ph_no != ""  && order_id !="" ) {
                alert("Please fill only one field");
                return false;
            }

            if (booking_id != ""  && order_id !="" ) {
                alert("Please fill only one field");
                return false;
            }

            if (booking_id != ""  && serial_no !="" ) {
                alert("Please fill only one field");
                return false;
            }

            if (order_id != ""  && serial_no !="" ) {
                alert("Please fill only one field");
                return false;
            }

            if (ph_no != "" && !ph_no.match(exp1)) {
                alert("Please enter valid Phone number");
                return false;
            }
            
            if (order_id != "" && !order_id.match(exp2)) {
                alert("Please enter valid Order ID");
                return false;
            }
            if (serial_no != "" && !serial_no.match(exp2)) {
                alert("Please enter valid Serial No.");
                return false;
            }
            
    }

</script>
