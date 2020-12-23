<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading" style="padding-left: 42%;">Generate GSTR2a Report</div>
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
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div  class="form-group">
                                    <label  for="otp" class="col-md-4" style="text-align: right;">Enter OTP *</label>
                                    <div class="col-md-6">
                                        <input type="text" id="otp" name="otp" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div  class="form-group">
                                    <label  for="state" class="col-md-4" style="text-align: right;">Select State *</label>
                                    <div class="col-md-6">
                                        <select id="state" class="state form-control" name ="state">
                                            <option value="09AAFCB1281J1ZM">Noida</option>
<!--                                            <?php 
                                          //  foreach($state as $state_data)
                                            {
                                            ?>    
                                            <option value="<?php //echo $state_data["gst_number"]; ?>"><?php //echo $state_data['city']; ?></option>
                                            <?php
                                            }
                                            ?>      -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-4"></div>
                            <div class="col-md-4 form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
<!--                                <center>-->
<!--                                    <div><a style="padding-left: 25px;" onclick="generate_otp()">Request OTP</a></div>-->
                                   
                                         <input type="button" name="generate_otp" id="generate_otp" class="btn btn-info" value="Request OTP"/>
                                         <input type="button" onclick="create_autntoken();" name="submit_btn" class="btn btn-info" value="Submit"/>
                                
<!--                                </center>-->
                                </div>
                            </div>
                            <div class="col-md-4"></div>
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
        $("#state").select2();
    });
    
    $("#generate_otp").click(function(){
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/generate_taxpro_otp',
                data: {state : $("#state").val()},
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
                        var gst_error = '';
                        if(typeof data.error.message != 'undefined'){
                         var gst_error = " ("+data.error.message+")";
                        }
                        $("#error_msg").text("Error in generating OTP. Please contact tech team."+gst_error);
                        $("#success_msg_div").hide();
                        $("#error_msg_div").show();
                    }
                 $('body').loadingModal('destroy');
            }
        });
      });
    
    function create_autntoken(){ 
        if($("#otp").val()){ 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/generate_taxpro_auth_token',
                data: {otp:$("#otp").val(), state:$("#state").val(), return_type:'json'},
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
                    var token_result = JSON.parse(data.trim());
                    data=token_result.status;
                    if(data == "success"){
                        $("#success_msg").text("GSTR2a data updated successfully.");
                        $("#success_msg_div").show();
                        $("#error_msg_div").hide();
                    }
                    else{
                        var gst_error = '';
                        if(token_result.message != ''){
                         var gst_error = " ("+token_result.message+")";
                        }
                        $("#error_msg").text("Error in updating GSTR2a data. Please contact tech team."+gst_error);
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
