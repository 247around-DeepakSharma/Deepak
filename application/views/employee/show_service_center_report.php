<style type="text/css">
    #noty_topCenter_layout_container{margin-top:40px !important;}
</style>
<div class="container-fluid" style="margin-top: 4%;">
    <div class="col-md-7">
        <h2>Service Center Report</h2>
    </div>

    <div class="col-md-4">
        <div class="col-md-12" style="margin-top: 4%;">
            <div class="input-group input-append date">
                <input id="date" class="form-control date"  name="date" type="text" value = "<?php echo date('Y-m-d'). ' - '. date('Y-m-d'); ?>" autocomplete='off' onkeydown="return false" >
                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
    <div class="col-md-1" >
        <span class="btn btn-primary" style="float:right;margin-top: 20%;" id="sendmail">Send Mail</span>
    </div>
    <div class="clear"></div>
    <div class="row">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loader.gif" style="display: none;"></center>
        </div>
    </div>
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
                            url: '<?php echo base_url() ?>employee/vendor/send_report_to_mail',
                            success: function (data) {
                                //console.log(data);
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
    
    $('#date').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    
    $(function() {
      $('input[name="date"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
      }, function(start, end, label) {
            var startDateObj = new Date(start);
            var endDateObj = new Date(end);
            var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            var date = startDateObj.getFullYear()+'-'+(("0" + (startDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + startDateObj.getDate()).slice(-2))+' - '+endDateObj.getFullYear()+'-'+(("0" + (endDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + endDateObj.getDate()).slice(-2));
            if(diffDays>31) {
                alert("Maximum range allowed is 1 month.");
                return false;
            } else {
                console.log(date);
                $.ajax({
                    method:'POST',
                    url: '<?php echo base_url() ?>employee/vendor/show_service_center_report',
                    data: {date: date},
                    beforeSend: function() {
                        $('#loader_gif_title').show();
                    },
                }).done(function(data) {
                    $('#loader_gif_title').hide();
                    $('body').html($.trim(data));
                    $('#date').val(date);
                });
            }  
      });
    });    
    

</script>

