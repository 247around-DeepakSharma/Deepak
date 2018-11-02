<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading" style="padding-left: 38%;">Generate GSTR2a Report</div>
            <div class="panel-body">
            <div class="row">
                <div id="success_msg_div" class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong id="success_msg"></strong>
                </div>
                <div id="error_msg_div" class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    <strong id="error_msg"></strong>
                </div>
                <form name="myForm" class="form-horizontal" novalidate="novalidate" action="<?php echo base_url(); ?>employee/accounting/generate_taxpro_auth_token"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <div  class="form-group">
                                    <label  for="vendor_partner" class="col-md-2">Enter OTP *</label>
                                    <div class="col-md-10">
                                        <input type="text" id="otp" name="otp" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-3"></div>
                            <div class="col-md-6 form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                <center>
                                    <div><a onclick="generate_otp()">Request OTP</a></div>
                                    <div style="margin-top:5px">
                                        <input type="button" onclick="create_autntoken();" name="submit_btn" class="btn btn-info" value="Submit"/>
                                    </div>
                                </center>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#success_msg_div").hide();
        $("#error_msg_div").hide();
    });
    
    function generate_otp(){
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/generate_taxpro_otp',
                data: {},
                beforeSend: function(){
                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                    });
                },
                success: function (data) {
                    //console.log(data);
                    //data = '{"status_cd":"1"}';
                    data = JSON.parse(data);
                    if(data.status_cd == '1'){
                        $("#success_msg").text("OTP generated successfully.");
                        $("#success_msg_div").show();
                        $("#error_msg_div").hide();
                    }
                    else{
                        $("#error_msg").text("Error in generating OTP. Please contact tech team.");
                        $("#success_msg_div").hide();
                        $("#error_msg_div").show();
                    }
                 $('body').loadingModal('destroy');
            }
        });
    }
    
    function create_autntoken(){ 
        if($("#otp").val()){ 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/generate_taxpro_auth_token',
                data: {otp:$("#otp").val()},
                beforeSend: function(){
                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                    });
                },
                success: function (data) {
                    // console.log(data);
                    if(data == "success"){
                        $("#success_msg").text("GSTR2a data updated successfully.");
                        $("#success_msg_div").show();
                        $("#error_msg_div").hide();
                    }
                    else{
                        $("#error_msg").text("Error in updating GSTR2a data. Please contact tech team.");
                        $("#success_msg_div").hide();
                        $("#error_msg_div").show();
                    }
                    $('body').loadingModal('destroy');
                }
            });
        }
        else{
            alert("Please Enter OTP");
            return false;
        }
    }
</script>
