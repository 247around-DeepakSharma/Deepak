<style type="text/css">
    #noty_topCenter_layout_container{margin-top:40px !important;}
</style>
<div class="container-fluid">
    <div class="container">
        <h2>Send Mail</h2><br>
       
        <div class="panel panel-primary" style="margin-top:14px;margin-bottom:-6px;">
            <div class="panel-heading"><span style="margin-left:45%;font-size:120%">Vendors</span></div>
        </div>    
        <table class="table">
            <thead>
                <tr class="info">
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Vendor</th>
                    <th>Attachment</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                    <?php foreach ($email_template as $key => $value) { ?>
                <tr class="success">
                <form id="form_<?php echo $value['id'] ?>" method="POST" enctype="multipart/form-data">
                    <td><?php echo ($key + 1) . '.'; ?></td>
                    <td><?php echo $value['template'] ?></td>
                    <td><?php echo $value['subject'] ?></td>
                    <td>
                        <select name="vendors[]" id="vendors_<?php echo $value['id'] ?>" class="vendors" multiple="multiple">
                            <option value="0">ALL</option>
                            <?php
                            foreach ($vendors as $val) {
                                echo '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="file" name="attachment_<?php echo $value['id'] ?>" style="max-width:200px;"/>
                    </td>
                    <td>
                        <input type="button"  value="Send" id="<?php echo $value['id'] ?>" onClick="send_email(this.id)" class="btn btn-primary btn-sm"/>
                    </td>
                </form>
            </tr>  
            <?php } ?>
            

            </tbody>
        </table>
        
        <br><hr>
      
        <div class="panel panel-primary" style="margin-top:14px;margin-bottom:-6px;">
            <div class="panel-heading"><span style="margin-left:45%;font-size:120%">Partners</span></div>
        </div>
        <table class="table">
            <thead>
                <tr class="info">
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Partner</th>
                    <th>Attachment</th>
                    <th>Action</th>

            </tr>
            </thead>
            <tbody>

                    <?php foreach ($partner_email_template as $key => $value) { ?>
                <tr class="success">
                <form id="partner_form_<?php echo $value['id'] ?>" method="POST" enctype="multipart/form-data">
                    <td><?php echo ($key + 1) . '.'; ?></td>
                    <td><?php echo $value['template'] ?></td>
                    <td><?php echo $value['subject'] ?></td>
                    <td>
                        <select name="partners[]" id="partners_<?php echo $value['id'] ?>" class="partners" multiple="multiple" style="min-width:350px;">
                            <option value="0">ALL</option>
                            <?php
                            foreach ($partners as $val) {
                                echo '<option value="' . $val['id'] . '">' . $val['public_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="file" name="attachment_<?php echo $value['id'] ?>" style="max-width:200px;"/>
                    </td>
                    <td>
                        <input type="button"  value="Send" id="partner_<?php echo $value['id'] ?>" onClick="send_partner_email(this.id)" class="btn btn-primary btn-sm"/>
                    </td>
                </form>
             </tr>
            <?php } ?>
           

            </tbody>
        </table>
        
    </div>


</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".vendors").select2({
            placeholder: "Select Vendors",
            allowClear: true
        });
        $(".partners").select2({
            placeholder: "Select Partners",
            allowClear: true
    });
    });

//This is used to send mail to Vendors
    function send_email(id) {

        if ($('#vendors_' + id).val() == null) {
           noty({
                text: 'Please select any vendor to shoot email.',
                type: 'error',
                layout: 'topCenter',
                theme: 'relax',
                killer:true,
                animation: {
                    open: 'animated shake', // Animate.css class names
                    close: 'animated flipOutX', // Animate.css class names
                    easing: 'swing', // unavailable - no need
                    speed: 500 // unavailable - no need
                }
            });        
            return false;
        } else {
            //Confirm before sending mails
            noty({
                text: 'Do you want to send mails to selected vendors ?',
                buttons: [
        {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    $noty.close();
                        //Setting Notify for information of sending mail when ALL vendors selected
                            var n =noty({
                                text: 'Sending Mails ....',
                                type: 'information',
                                layout: 'bottomRight',
                                theme: 'relax',
                                killer:true,
                                animation: {
                                    open: 'animated bounceInRight', // Animate.css class names
                                    close: 'animated flipOutX', // Animate.css class names
                                    easing: 'swing', // unavailable - no need
                                    speed: 500 // unavailable - no need
                                }
                            });
                            //Show Sending Mails... at each call
                                $('#noty_bottomRight_layout_container').show();
                        //Submittting FORM Data through AJAX
                            $('#form_' + id).ajaxSubmit({
                            type: "POST",
                            data: {data: $('#form_' + id).serialize()},
                            url: '<?php echo base_url() ?>employee/vendor/process_mail_to_vendor/'+id,
                            success: function (data) {
                                //Checking success or error in sending mail
                                if (data) {
                                    $('#noty_bottomRight_layout_container').remove();
                                    noty({
                                        text: 'Mail send successfully.',
                                        type: 'success',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer:true,
                                        animation: {
                                            open: 'animated bounceIn', // Animate.css class names
                                            close: 'animated flipOutX', // Animate.css class names
                                            easing: 'swing', // unavailable - no need
                                            speed: 500 // unavailable - no need
                                        }
                                    });      
                                } else {
                                    $('#noty_bottomRight_layout_container').remove();
                                    noty({
                                        text: 'Error in sending mail to selected vendor. Please check vendor details.',
                                        type: 'error',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer:true,
                                        animation: {
                                            open: 'animated bounceIn', // Animate.css class names
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
        {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    }
        }
            ],
                type: 'confirm',
                killer:true,
                layout: 'topCenter',
                theme: 'relax',
                animation: {
                    open: 'animated pulse', // Animate.css class names
                    close: 'animated flipOutX', // Animate.css class names
                    easing: 'swing', // unavailable - no need
                    speed: 500 // unavailable - no need
                }
            });  
        }
    }

//This is used to send mail to Partners
    function send_partner_email(id){
        var id = id.split("_")[1];
        
        if ($('#partners_' + id).val() == null) {
           noty({
                text: 'Please select any partner to shoot email.',
                type: 'error',
                layout: 'topCenter',
                theme: 'relax',
                killer:true,
                animation: {
                    open: 'animated shake', // Animate.css class names
                    close: 'animated flipOutX', // Animate.css class names
                    easing: 'swing', // unavailable - no need
                    speed: 500 // unavailable - no need
                }
            });        
            return false;
        } else {
            //Confirm before sending mails
            noty({
                text: 'Do you want to send mails to selected partners ?',
                buttons: [
        {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    $noty.close();
                        //Setting Notify for information of sending mail when ALL vendors selected
                            var n =noty({
                                text: 'Sending Mails ....',
                                type: 'information',
                                layout: 'bottomRight',
                                theme: 'relax',
                                killer:true,
                                animation: {
                                    open: 'animated bounceInRight', // Animate.css class names
                                    close: 'animated flipOutX', // Animate.css class names
                                    easing: 'swing', // unavailable - no need
                                    speed: 500 // unavailable - no need
                                }
                            });
                            //Show Sending Mails... at each call
                                $('#noty_bottomRight_layout_container').show();
                        //Submittting FORM Data through AJAX
                            $('#partner_form_' + id).ajaxSubmit({
                            type: "POST",
                            data: {data: $('#form_' + id).serialize()},
                            url: '<?php echo base_url() ?>employee/vendor/process_mail_to_partner/'+id,
                            success: function (data) {
                                //Checking success or error in sending mail
                                if (data) {
                                    $('#noty_bottomRight_layout_container').remove();
                                    noty({
                                        text: 'Mail send successfully.',
                                        type: 'success',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer:true,
                                        animation: {
                                            open: 'animated bounceIn', // Animate.css class names
                                            close: 'animated flipOutX', // Animate.css class names
                                            easing: 'swing', // unavailable - no need
                                            speed: 500 // unavailable - no need
                                        }
                                    });      
                                } else {
                                    $('#noty_bottomRight_layout_container').remove();
                                    noty({
                                        text: 'Error in sending mail to selected partner. Please check partner details.',
                                        type: 'error',
                                        layout: 'topCenter',
                                        theme: 'relax',
                                        killer:true,
                                        animation: {
                                            open: 'animated bounceIn', // Animate.css class names
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
        {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    }
        }
            ],
                type: 'confirm',
                killer:true,
                layout: 'topCenter',
                theme: 'relax',
                animation: {
                    open: 'animated pulse', // Animate.css class names
                    close: 'animated flipOutX', // Animate.css class names
                    easing: 'swing', // unavailable - no need
                    speed: 500 // unavailable - no need
                }
            });  
        }
    }

</script>