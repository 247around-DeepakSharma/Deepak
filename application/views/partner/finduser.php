<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">

            <div class="panel panel-info clear">
                <div class="panel-heading"><center><strong style="font-size:130%;">Search User</strong></center></div>
            </div>
            <div class="clear search_panel">
                <div class="container" style="margin-left:50px;">
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
                    <form name="my_Search_Form" class="form-horizontal" action="<?php echo base_url() ?>employee/partner/finduser" method="POST" onsubmit="return phonevalidate()">
                        <div class="clear"></div>
                        <div class="form-group <?php
                        if (form_error('phone_number')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="phone_number" class="col-md-2 search_col">Phone Number</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control fa fa-search" name="phone_number" value="<?php echo set_value('phone_number'); ?>" placeholder="Enter Phone No.">
                                <?php echo form_error('phone_number'); ?>
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" style="margin-left:24%;"/>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="order id" class="col-md-2 search_col">Order ID/ Serial No</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control fa fa-search" id="order_id" name="order_id" value="<?php echo set_value('order_id'); ?>" placeholder="Enter Order ID / Serial No">
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" style="margin-left:24%;"/>
                        <div class="clear"></div>
                        <div class="form-group <?php
                        if (form_error('booking_id')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="booking_id" class="col-md-2 search_col">247 Booking ID</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control fa fa-search" id="booking_id" name="booking_id" value="<?php echo set_value('booking_id'); ?>" placeholder="Enter Booking ID">
                                <?php echo form_error('booking_id'); ?>
                            </div>
                        </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-6">
                    <?php echo "<input type='submit' value='Find' class='btn btn-lg btn-primary clear'>" ?>
                </div>
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
        var exp1 = /^[7-9]{1}[0-9]{9}$/;

        if (ph_no == "" && booking_id == "" && order_id == "") {
            alert("Please enter atleast one detail to search.");
            return false;
        }

        if (ph_no != "" && booking_id != "" && order_id != "") {
            alert("Please fill only one field");
            return false;
        }

        if (ph_no != "" && order_id != "") {
            alert("Please fill only one field");
            return false;
        }

        if (booking_id != "" && order_id != "") {
            alert("Please fill only one field");
            return false;
        }

        if (ph_no != "" && !ph_no.match(exp1)) {
            alert("Enter valid Phone Number");
            return false;
        }
    }

</script>
