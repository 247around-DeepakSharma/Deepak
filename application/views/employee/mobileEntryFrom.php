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
        width: 24%;
        word-break: break-all;
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

    .form-info{
        font-size: 18px;
        font-style: italic;
        color: black;
    }

    span.name-text {
        display: inline-block;
        width: 50%;
    }

    /* brand name informatiom tooltip */ 
    .tooltip-info {
        position: relative;
        display: inline-block;
    }

    .tooltip-info .tooltiptext-info {
        visibility: hidden;
        width: 120px;
        font-size: 10px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 150%;
        left: 50%;
        margin-left: -60px;
    }

    .tooltip-info .tooltiptext-info::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: black transparent transparent transparent;
    }

    .tooltip-info:hover .tooltiptext-info {
        visibility: visible;
    }
    </style>
</head>
<body>
    <div id="page-wrapper">
        <div class="container-fluid">
            <div id="create-error-msg" class="alert alert-danger alert-dismissible hidden" role="alert">
                <center><strong></strong></center>                        
            </div>
            <div class="row">
                <h1 class="page-header">
                    Fill Information<span class="form-info"> (The form to capture the contact details of Brand/Dealer/SF)</span>
                </h1>
                <div class="col-md-6">
                    <form name="mobileEntryForm" id="mobile-entry-form" class="form-horizontal" method="POST";>
                        <div>
                            <input type="hidden" class="form-control" name="entity_id" id="entity_id" value="<?= $user_id ?>">
                            <input type="hidden" class="form-control" name="entity" id="entity" value="<?= $emp_name ?>">
                        </div>
                        <div class="form-group">
                            <label for="fullname" class="col-md-4"></label>
                            <div class="col-md-6">
                                <label class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="brand" checked> Brand</label>
                                <label  class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="dealer"> Dealer</label>
                                <label  class="radio-inline font-weight-bold" style="font-weight: 700;"><input type="radio" name="role" value="sf"> SF</label>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="fullname" class="col-md-4">Name<span style="color:red"> *</span>:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="fullname" id="fullname">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mobile-number" class="col-md-4">Mobile No.<span style="color:red">*</span> :</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="mobile_number" id="mobile-number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="brand-name" class="col-md-4">Brand Name:
                                <div class="tooltip-info">
                                    <span class="tooltiptext-info">Brand name is mandatory for brands only</span>
                                    <li class="fa fa-info-circle" style="font-size:18px;color:#337ab7"></li>
                                </div>
                            </label>
                            <div class="col-md-6">
                                <select name="brand_name" id="brand-name" class="form-control">
                                    <option value="">Select Brand</option>
                                    <?php foreach ($partner as  $value) { ?>
                                    <option value="<?php echo $value['source']?>"><?php echo $value['source']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-6">
                                <input type='submit' value='Submit' class='btn btn-primary' id="submitform">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 from-entry-warpper">
                    <div class="row">
                        <?php foreach($data as $key => $value){
                            $fullname = explode(' ',$value['agent']);
                            $first_name = $fullname[0];
                            ?>
                            <label class="label label-primary tooltip-info">
                                <span class="name-text"><?= (strlen($first_name) > 6) ? substr($first_name,0,6).'...' : $first_name ?></span>
                                <span class="badge"><?= ($key % 5 == 0) ? '100': $value['total'] ?></span>
                                <span class="tooltiptext-info"><?= $value['agent'] ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    $(document).ready(function () {

        $('#brand-name').select2({
            tags: true
        });

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

            var actionUrl_verify = '<?php echo base_url(); ?>mobile_entry/process_mobile_number_validation';
            var actionUrl_save = '<?php echo base_url(); ?>mobile_entry/save_mobile_entry_data';

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
                                    'brand_name': brand_name,
                                    'role': role
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