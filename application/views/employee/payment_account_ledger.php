<style>
    #invoice_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #invoice_list_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Account Ledger Statement</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="service_centre_id">
                            
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="daterange" name="daterange">
                    </div>
                    <button class="btn btn-success col-md-2" id="download_invoice_data">Submit</button>
                </div>
            </div>
        <hr>
    </div>
    
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    var invoice_table;
    var time = moment().format('D-MMM-YYYY HH:mm:ss');
    $(function () {
        $('#daterange').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")); ?>',
            endDate: '<?php echo date('Y/m/d') ?>',
            minDate: '2015/01/01',
            maxDate: '<?php echo date('Y/m/d') ?>',
            showDropdowns: true,
            dateLimit: 60
        });
    });
    
    $('#service_centre_id').select2({
    placeholder: "Select Sf Name",
    });

    $(document).ready(function(){
        get_service_centre_list();

    });
    
    $('#download_invoice_data').on('click',function(){
        var service_centre_id = $("#service_centre_id").val();
        console.log(service_centre_id);
        var date_range = $("#daterange").val();
        $('#download_invoice_data').html("Download In Progress..").attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/accounting/download_payment_account_ledger',
            data: {vendor_id : service_centre_id, date_range : date_range},
            success: function (data) {
                $('#download_invoice_data').html("Submit..").attr('disabled',false);
                var obj = JSON.parse(data); 
                console.log(data);
                if(obj['status']){
                    
                    window.location.href = obj['path'];
                }else{
                    alert(obj.message)
                }
            }
        });
    });
    
    
    
    //function to get service centre list in dropdown
    function get_service_centre_list()
    {
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/invoice/get_service_centre_list_dropdown',
           data: {}
         })
         .done (function(data) {
             $('#service_centre_id').append(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             alert("Something went wrong while loading service centre list!");
          })
    }
</script>