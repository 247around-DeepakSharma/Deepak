<html>

<head>
    <script>
        function phonevalidate() {
            var ph_no = document.forms["myForm"]["phone_number"].value;
            var booking_id = document.forms["myForm"]["booking_id"].value;
            var user_name = document.forms["myForm"]["userName"].value;
            var order_id = document.forms["myForm"]["order_id"].value;

            var exp1 = /^[6-9]{1}[0-9]{9}$/;
            var exp2 = /^[A-Za-z _]+$/;

            if (ph_no == "" && booking_id == "" && user_name =="" && order_id == "" ) {
                alert("Please enter atleast one detail to search..");
                return false;
            }

            if (ph_no != "" && booking_id != "" && user_name !="" && order_id !="") {
                alert("Please fill only one field...");
                return false;
            }

            if (ph_no != ""  && user_name !="" ) {
                alert("Please fill only one field...");
                return false;
            }

            if (ph_no != ""  && order_id !="" ) {
                alert("Please fill only one field...");
                return false;
            }

            if (booking_id != ""  && order_id !="" ) {
                alert("Please fill only one field...");
                return false;
            }

            if (booking_id != ""  && user_name !="" ) {
                alert("Please fill only one field...");
                return false;
            }

            if (order_id != ""  && user_name !="" ) {
                alert("Please fill only one field...");
                return false;
            }


            if (ph_no != "" && !ph_no.match(exp1)) {
                alert("Enter Valid Phone Number Only");
                return false;
            }

            if (user_name != "" && !user_name.match(exp2)){
                alert("Enter only alphabates in user name");
                return false;
            }

        }

    </script>
</head>

<body>
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <h1 class="page-header">
                       Search User
                   </h1>
                   <form name="myForm" class="form-horizontal" action="<?php echo base_url()?>employee/user/finduser" method="GET" onsubmit="return phonevalidate()" ;>
                    <div class="form-group <?php if( form_error('phone_number') ) { echo 'has-error';} ?>">
                        <label for="phone_number" class="col-md-2">Phone Number</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="phone_number" value="<?php echo set_value('phone_number'); ?>" placeholder="Enter Phone No." onkeypress="return (event.charCode > 47 && event.charCode < 58) || event.charCode == 13">
                            <?php echo form_error('phone_number'); ?>
                        </div>


                    </div>

                    <div>
                        <center><b style="color:blue;padding-right:300px;">OR</b></center>
                    </div>
                    <div class="form-group <?php if( form_error('booking_id') ) { echo 'has-error';} ?>">
                        <label for="booking_id" class="col-md-2">Booking Id</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="booking_id" value="<?php echo set_value('booking_id'); ?>" placeholder="Enter Booking ID" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 45 || event.charCode == 13">
                            <?php echo form_error('booking_id'); ?>
                        </div>
                    </div>
                    <div>
                        <center><b style="color:blue;padding-right:300px;">OR</b></center>
                    </div>
                    <div class="form-group">
                        <label for="booking_id" class="col-md-2">User Name</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="userName" placeholder="Enter User Name" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 32 || event.charCode == 13">
                        </div>

                    </div>


                    <div>
                        <center><b style="color:blue;padding-right:300px;">OR</b></center>
                    </div>

                    <div class="form-group">
                        <label for="booking_id" class="col-md-2">Partner</label>
                        <div class="col-md-4">
                            <select name="partner" id="partner" class="form-control">
                                <option value="">Select Partner</option>
                                <?php foreach ($partner as  $value) { ?>
                                <option value="<?php echo $value['partner_id']?>" <?php if($value['source'] == "Snapdeal") {echo "selected"; } ?>><?php echo $value['source']; ?></option>
                                <?php  } ?>

                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="order id" class="col-md-2">Order ID</label>
                        <div class="col-md-4">
                        <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Enter Order ID" onkeypress="return (event.charCode > 47 && event.charCode < 58) || event.charCode == 13">
                        </div>

                    </div>
                    <div class="form-group ">
                       <div class="col-sm-offset-3">
                          <?php echo "<a href='user/finduser'><input type='submit' value='Find' class='btn btn-primary'></a>"?>
                      </div>

                  </div>


              </form>
          </div>
      </div>
  </div>
</div>
</body>

</html>

<script type="text/javascript">
    $('#partner').select2();
</script>
