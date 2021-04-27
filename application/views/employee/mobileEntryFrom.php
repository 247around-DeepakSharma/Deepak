<html>
<head>
    <style>
    .label {
        display: inline;
        padding: 0.9em 0.9em .9em;
        font-size: 129%;
        font-weight: 400;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.5em;
    }
    .label-primery{
        background-color: #337ab7;
    }
    .badge {
        display: inline-block;
        min-width: 10px;
        padding: 10px;
        margin-left: 15px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        background-color: #b5ce83;
        border-radius: 15px;
    }
    .col-md-6.from-entry-warpper .row {
        margin: 0;
        border: #ededed solid;
        border-width: 1px;
        border-radius: 4px;
        padding: 20px 20px 20px;
    }

    label.label.label-primary {
        display: inline-block;
        border-radius: 30px;
        padding: 10px 18px;
        font-size: 14px;
        margin-bottom: 12px;
        color: #272727;
        background: transparent;
        border: #ededed solid 1px;
    }

    span.badge {
        border-radius: 100%;
        color: #fff;
        padding: 0px;
        background-color: #337ab7;
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-weight: 400;
    }

    .col-md-6.from-entry-warpper {
        margin-top: 15px;
    }
    </style>
</head>
<body>
    <div id="page-wrapper">
        <div class="container-fluid">
            <div id="create-error-msg" class="alert alert-danger alert-dismissible hidden" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center><strong></strong></center>                        
            </div>
            <div class="row">
                <h1 class="page-header">
                    Fill Information
                </h1>
                <div class="col-md-6">
                    <form name="mobileEntryForm" id="mobile-entry-form" class="form-horizontal" method="POST";>
                        <div>
                            <input type="hidden" class="form-control" name="entity_id" id="entity_id" value="<?= $user_id ?>">
                            <input type="hidden" class="form-control" name="entity" id="entity" value="<?= $emp_name ?>">
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="brand" checked> Brand</label>
                                <label  class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="dealer"> Dealer</label>
                                <label  class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="sf"> SF</label>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="fullname" class="col-md-2">Name:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="fullname" id="fullname">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mobile-number" class="col-md-2">Mobile No.:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="mobile_number" id="mobile-number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="brand-name" class="col-md-2">Brand Name:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="brand_name" id="brand-name">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4">
                                <input type='submit' value='Submit' class='btn btn-primary' id="submitform">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 from-entry-warpper">
                    <div class="row">
                        <?php foreach($data as $key => $value){?>
                            <label class="label label-primary"><?= $value['entity'] ?> <span class="badge"><?= $value['total'] ?></span></label>
                            <?php if($key % 2 == 0 && $key != 0){
                                echo "<br/>";
                            } ?> 
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    $(document).ready(function () {

        $("#mobile-entry-form").submit(function(event) {

            event.preventDefault();

            var actionReturn = false;
            var regxp_mobile = /^(\+\d{1,3}[- ]?)?\d{10}$/;

            var role = $('input[name=role]:checked' ).val();
            var entity_id = $('#entity_id').val();
            var entity = $('#entity').val();
            var fullname = $('#fullname').val();
            var mobile_number = $('#mobile-number').val();
            var brand_name = $('#brand-name').val();

            var actionUrl_verify = '<?php echo base_url(); ?>employee/user/process_mobile_number_validation';
            var actionUrl_save = '<?php echo base_url(); ?>employee/user/save_mobile_entry_data';

            if(fullname.length == 0){
                $("#create-error-msg").removeClass("hidden").find('strong').html('Name field is mandatory');
                $('#fullname').focus();
            } else if(mobile_number.length == 0){
                $("#create-error-msg").removeClass("hidden").find('strong').html('Mobile number field is mandatory');
                $('#mobile-number').focus();
            } else if(!mobile_number.match(regxp_mobile)){
                $("#create-error-msg").removeClass("hidden").find('strong').html('Please enter valid mobile number');
                $('#mobile-number').focus();
            } else if(brand_name.length == 0 && role == 'brand'){
                $("#create-error-msg").removeClass("hidden").find('strong').html('Brand Name field is mandatory');
                $('#brand-name').focus();
            }else{
                $("#create-error-msg").addClass("hidden");
                $('#submitform').attr('disabled', true);
                $.ajax({
                    type: 'POST',    
                    url: actionUrl_verify,
                    data: { 
                        'mobile_number': mobile_number
                    },
                    success: function(response){
                        if(response == true){
                            $("#create-error-msg").removeClass("hidden").find('strong').html('Mobile number already exist! Please use another one');
                            $('#submitform').attr('disabled', false);
                        } else{
                            
                            $.ajax({
                                type: 'POST',    
                                url: actionUrl_save,
                                data: { 
                                    'entity_id': entity_id,
                                    'entity': entity,
                                    'fullname': fullname,
                                    'mobile_number': mobile_number,
                                    'brand_name': brand_name
                                },
                                success: function(response){
                                    if(response == true){
                                        $("#create-error-msg").removeClass("hidden").removeClass('alert-danger').addClass('alert-success').find('strong').html('Mobile number add succesfully');
                                        setTimeout(function(){
                                            location.reload(); 
                                        }, 3000);
                                    } else{
                                        $("#create-error-msg").removeClass("hidden").find('strong').html('Something went wrong');
                                    }
                                }  
                            });
                        }
                    }  
                });
            }
            $("html, body").animate({scrollTop: 0}, 1000);
            return actionReturn;
        });
    });
</script>
</html>