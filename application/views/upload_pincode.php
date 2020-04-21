<!DOCTYPE html>
<html>
    <head>
        <title>Pincode Distance Calculator</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- include jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

        <!-- include bootstrap files -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script>
            $(function () {
                $('#btn').click(function () {
                    $('.myprogress').css('width', '0');
                    $('.msg').text('');
                    var email_id = $('#email_id').val();
                    var file = $('#file').val();
                    
                    if (email_id == '' || file == '') {
                        alert('Please enter email and select file');
                        return;
                    }
                    var formData = new FormData();
                    formData.append('file', $('#file')[0].files[0]);
                    formData.append('email_id', email_id);
                   
                    $('#btn').attr('disabled', 'disabled');
                     $('.msg').text('Uploading in progress...');
                    $.ajax({
                        url: '<?php echo base_url();?>partner/processUploadPincode',
                        data: formData,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        // this part is progress bar
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    $('.myprogress').text(percentComplete + '%');
                                    $('.myprogress').css('width', percentComplete + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (data) {
                            $('.msg').text(data);
                            $('#btn').removeAttr('disabled');
                        }
                    });
                });
            });
        </script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h3>Upload Pincode File To Get Distance<img style='float: right;width:268px;' src="<?php echo base_url();?>images/powered-by-google.png"></h3>
                <br/>
                <br/>
                <form id="myform" method="post">

                    <div class="form-group">
                        <label>Enter Email </label>
                        <input class="form-control" type="text" id="email_id"  /> 
                    </div>
<!--                    <div class="form-group">
                        <label>Enter Password </label>
                        <input class="form-control" type="password" id="password" /> 
                    </div>-->
                    <div class="form-group">
                         <p class="file">
                            <input type="file" name="file" id="file" />
                            <label for="file">Upload your pincode file</label>
                          </p>
<!--                           <label>Select file: </label>
                        <input class="form-control" type="file" id="file" />-->
                    </div>
                    <div class="form-group">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                        </div>

                        <div class="msg"></div>
                    </div>

                    <input type="button" id="btn" class="btn-success" value="Upload" />
                </form>
                <p class="txtcenter">File having distance between pincodes will be sent to the provided email id.</p>
                <p class="txtcenter copy">Created by <a href="http://247around.com">@247around</a><br /></p>
            </div>
        </div>
    </body>
</html>

<style>
    
.file {
  position: relative;
}
.file label {
  background: #39D2B4;
  padding: 5px 20px;
  color: #fff;
  font-weight: bold;
  font-size: .9em;
  transition: all .4s;
}
.file input {
  position: absolute;
  display: inline-block;
  left: 0;
  top: 0;
  opacity: 0.01;
  cursor: pointer;
}
.file input:hover + label,
.file input:focus + label {
  background: #34495E;
  color: #39D2B4;
}



/* Useless styles, just for demo styles */

body {
  font-family: "Open sans", "Segoe UI", "Segoe WP", Helvetica, Arial, sans-serif;
  color: #7F8C9A;
  background: #FCFDFD;
}
h1, h2, h3 {
  margin-bottom: 5px;
  font-weight: normal;
  text-align: center;
  color:#aaa;
}
h2 {
  margin: 5px 0 2em;
  color: #1ABC9C;
}
form {
  width: 225px;
  margin: 0 auto;
  text-align:center;
}
h2 + P {
  text-align: center;
}
.txtcenter {
  margin-top: 4em;
  font-size: .9em;
  text-align: center;
  color: #aaa;
}
.copy {
 margin-top: 2em; 
}
.copy a {
 text-decoration: none;
 color: #1ABC9C;
}
</style>
