<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            Service Center Portal
        </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
         <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <style>
            body{
            text-align:center;
            background-color:#f2f2f2;
            float:none;
            }

        </style>
    </head>
    <body style="background-color: #fff;">

        <div class="container pageWrap">
            <div class="col-xs-4 " style="width:100%;text-align:left">
               
                    <?php if($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                    ?>
                <img src="<?php echo base_url()?>images/logo.jpg" style="display: inline;">
                <p style="display: inline; color: #fff;margin-left:33px;font-size: 22px; ">Welcome to Service Center Portal</p>
                <div class="col-md-offset-3">
                    <form class="form-horizontal" action="<?php echo base_url(); ?>employee/service_centers/service_center_login" style="margin-top:45px;" method="post" id="login_form">
                        <div class="form-group">
                            <div class="col-sm-10">
                                <div class = "input-group">
                                    <span class = "input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                    <input type = "text" class = "form-control" name="user_name" placeholder = "User Name">
                                </div>
                            </div>
                            &nbsp;<span id="errmsg"></span>

                        </div>

                        <div class="form-group">
                            <div class="col-sm-10">
                                <div class = "input-group">
                                    <span class = "input-group-addon"><i class="fa fa-key" aria-hidden="true"></i></span>
                                    <input type = "password" class = "form-control" name="password" placeholder = "Password">
                                </div>
                            </div>
                            &nbsp;<span id="errmsg1"></span>
                        </div>
                        <a href="#" class="pull-right" style="color: #fff;margin-right: 77px;">Forgot password</a>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-10">
                                <button type="submit" class="login_btn">Sign in</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </body>

<script type="text/javascript">

(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#login_form").validate({
                rules: {
                    user_name: {
                         minlength: 3,
                        required: true
                    },
                    password: {
                        required: true,
                        //minlength: 8
                    }
                },
                messages: {
                    name: "Please enter your User Name",
                    password: "Please enter at least 8 digits password"

                },
                errorPlacement: function(error, element) {
                   error.insertAfter(element.parent());
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);


</script>

<?php $this->session->unset_userdata('error'); ?>
