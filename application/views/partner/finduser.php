<div class="right_col" role="main">
    <?php
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
        }
        ?>
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('success') . '</strong>
                   </div>';
        }
    ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Advanced Search</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form name="my_Search_Form" class="form-horizontal" action="<?php echo base_url() ?>employee/partner/finduser" method="POST" onsubmit="return phonevalidate()">
                        <div class="form-group">
                            <label class="radio-inline col-md-4" style="margin-left: 10px;">
                                <input type="radio" name="optradio" value="order_id" required> Order Id
                            </label>
                            <label class="radio-inline col-md-4">
                                <input type="radio" name="optradio" value="serial_number"> Serial Number
                            </label>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-md-4 search_label" for="search_value">Order Id</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="serial_number" name="search_value" value="<?php echo set_value('search_value'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Search" style="margin-left: 5px;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    $('input[type="radio"]').click(function(){
        var val = $(this).val();
        if(val === 'order_id'){
            $('.search_label').html("Order Id");
        }else if(val === 'serial_number'){
            $('.search_label').html("Serial Number");
        }
    });
    function phonevalidate() {
        var ph_no = document.forms["my_Search_Form"]["phone_number"].value;
        var booking_id = document.forms["my_Search_Form"]["booking_id"].value;
        var order_id = document.forms["my_Search_Form"]["order_id"].value;
        var serial_no = document.forms["my_Search_Form"]["serial_number"].value;
        var exp1 = /^[6-9]{1}[0-9]{9}$/;
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
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
