<style type="text/css">
    #noty_topCenter_layout_container{margin-top:40px !important;}
</style>
<div class="container-fluid">
    <div class="col-md-6">
        <h2>New Service Center Report</h2></div>
    <div class="col-md-6">
        <center>
            <span class="btn btn-primary" style="margin-top:2%;" id="sendmail">Send Mail</span>
        </center>
    </div>
    <div class="clear"></div>
    <?php echo $html; ?>

</div>
<script type="text/javascript">
    $('#sendmail').click(function () {
        //Confirm before sending mails
        noty({
            text: 'Do you want to send mail ?',
            buttons: [
                {addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                        $noty.close();
                        //Setting Notify for information of sending mail when ALL vendors selected
                        $.ajax({
                            url: '<?php echo base_url() ?>employee/vendor/new_service_center_report_to_mail',
                            success: function (data) {
                                console.log(data);
                                if (data == '1') {
                                    noty({
                                        text: 'Mail Sent',
                                        type: 'success',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer: true,
                                        animation: {
                                            open: 'animated shake', // Animate.css class names
                                            close: 'animated flipOutX', // Animate.css class names
                                            easing: 'swing', // unavailable - no need
                                            speed: 500 // unavailable - no need
                                        }
                                    });
                                } else {
                                    noty({
                                        text: 'Error in Sending Mail. Please check Official Mail value !',
                                        type: 'error',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer: true,
                                        animation: {
                                            open: 'animated shake', // Animate.css class names
                                            close: 'animated flipOutX', // Animate.css class names
                                            easing: 'swing', // unavailable - no need
                                            speed: 500 // unavailable - no need
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                {addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
                        $noty.close();
                    }
                }
            ],
            type: 'confirm',
            killer: true,
            layout: 'topCenter',
            theme: 'relax',
            animation: {
                open: 'animated pulse', // Animate.css class names
                close: 'animated flipOutX', // Animate.css class names
                easing: 'swing', // unavailable - no need
                speed: 500 // unavailable - no need
            }
        });

    });
</script>

