<html>
    <head>
        <style> 
            #footer { 
                position: fixed; 
                padding: 10px 10px 0px 10px; 
                bottom: 0; 
                width: 100%; 
                left:0px;
                /* Height of the footer*/  
                height: 50px; 
                background: blue; 
            } 
            #sign_agreement {
                background-color: white;
                border: none;
                color: black;
                padding: 14px 30px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: auto;
                transition-duration: 0.4s;
                cursor: pointer; 
            }
            #sign_agreement:hover{
                background-color: #4CAF50;
                color: white;
            }
        </style> 
    </head>
    <body>
        <div style="width:650px; height:570px;margin: auto;">
            <iframe src="<?php echo $file_path; ?>#toolbar=0" style="width:650px; height:570px;" frameborder="0"></iframe>
        </div>
        <div id="footer">
            <input type="hidden" id="hd_sf_email" value="<?php echo $sf_email; ?>"/>
            <input type="hidden" id="hd_secret_code" value="<?php echo $secret_code; ?>"/>
            <input type="hidden" id="hd_sf_ip" value="<?php echo getHostByName(getHostName()); ?>"/>
            <center><button id="sign_agreement">Sign Agreement</button></center>
        </div>
        <script src= 
                "https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
        </script> 

        <script>
            $('#sign_agreement').on('click',function () {
                var hd_sf_email = $('#hd_sf_email').val();
                var hd_secret_code = $('#hd_secret_code').val();
                var hd_sf_ip = $('#hd_sf_ip').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url('employee/SFAgreement/capture_sf_details'); ?>',
                    data: {sf_ip:hd_sf_ip,secret_code:hd_secret_code,sf_email:hd_sf_email},
                    success: function(response){
                        var result = JSON.parse(response);
                        if(result.success == 1){
                            alert('Agreement has been signed successfully and sent to your email id. Please check and download and if any issue contact your RM/ASM');
                            window.location.href = '<?php echo base_url("employee/login"); ?>';
                        }else{
                            alert('We have found issue in system.Please contact your RM/ASM');
                            window.location.href = '<?php echo base_url("employee/login"); ?>';
                        }
                    },
                    beforeSend: function(){
                        $('#sign_agreement').attr('disabled');
                        $('#sign_agreement').val('processing.....');
                    }
                });
            });

        </script> 
    </body>
</html>