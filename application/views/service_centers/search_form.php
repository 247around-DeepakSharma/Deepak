<div id="page-wrapper" style="margin-top:40px;">
    <div class="container-fluid">
        <div class="row">

            <div class="search_panel">
                <div class="container com-md-offset-2">
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
                    <form name="my_Search_Form" class="form-horizontal" action="<?php echo base_url() ?>service_center/search" method="POST" onsubmit="return phonevalidate()">
                        <div class="clear"></div>
                        <div class="form-group <?php
                        if (form_error('phone_number')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="phone_number" class="col-md-2">Phone Number</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control fa fa-search" name="phone_number" value="<?php echo set_value('phone_number'); ?>" placeholder="Enter Phone No.">
                                <?php echo form_error('phone_number'); ?>
                            </div>

                        </div>
                        <img src="<?php echo base_url() ?>images/or.png" height="25px" width="25px" class="col-md-offset-3" style="margin-bottom:10px;"/>

                        <div class="form-group <?php
                        if (form_error('booking_id')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label for="booking_id" class="col-md-2">Booking ID</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control fa fa-search" id="booking_id" name="booking_id" value="<?php echo set_value('booking_id'); ?>" placeholder="Enter Booking ID">
                                <?php echo form_error('booking_id'); ?>
                            </div>
                        </div>
                </div>
            </div>
             <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9" style="margin-top: 53px; margin-bottom: 36px;">
                                        <button type="submit" class="login_btn" style="padding: 6px 50px;">Sign in</button>
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
        var exp1 = /^[7-9]{1}[0-9]{9}$/;
        var exp2 = /^[a-zA-Z0-9]{6,}$/;

        if (ph_no === "" && booking_id === "" ) {
                alert("Please enter atleast one detail to search");
                return false;
            }

            if (ph_no !== "" && booking_id !== "") {
                alert("Please fill only one field");
                return false;
            }

            if (ph_no !== "" && !ph_no.match(exp1)) {
                alert("Please enter valid Phone number");
                return false;
            }
            
    }

</script>
<style type="text/css">
    .login_btn{
        background-color: #2C9D9C;
    }
</style>
