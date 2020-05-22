<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="row">

        <div  class = "panel panel-info" style="margin:20px;">
            <div class="panel-heading" >
                <b>
                    Defective Spare Dashboard
                </b>
            </div>

            <div class="panel-body">

                <form name="rm_state_mapping" class="form-horizontal" id ="rm_state_mapping"   method="POST">
                    <div class="row">

                        <div class="col-md-6">
                            <div  class="form-group <?php
                            if (form_error('state_name')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label  for="state_name" class="col-md-4">Partner *</label>
                                <div class="col-md-7">
                                    <select id='state_name' name='state_name[]' class="form-control state_name" multiple="multiple" style="min-width:350px;"  required>
                                        <?php foreach ($partner_list as $key => $value) { ?>
                                            <option value ="<?= $value['id']; ?>"  ><?php echo $value['public_name']; ?></option>
                                        <?php } ?>   
                                    </select>
                                    <?php echo form_error('state_name'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="bd-example" data-example-id="">
                                    <div id="accordion" role="tablist" aria-multiselectable="true">
                                        <div class="table-responsive">
                                            <table id="annual_charges_report" class="table  table-striped table-bordered">
                                                <thead>

                                                    <tr>
                                                        <th>S. No.</th>
                                                        <th>Partner</th>
                                                        <th>Pending % as on <?php echo date('d-m-Y'); ?></th>
                                                        <th>Inwards</th>
                                                        <th>Outward</th>
                                                        <th>WH fresh stock</th>
                                                        <th>Micro Fresh Stock</th>
                                                        <th>Sale Out Warranty</th>
                                                        <th>Defective @ wh</th>
                                                        <th>Out TAT - Part Count (Complete & Cancelled)</th>
                                                        <th>Out TAT - Part Amount (Complete & Cancelled)</th>
                                                        <th>Out TAT - Part Count (Pending & Rescheduled)</th>
                                                        <th>Out TAT - Part Amount (Pending & Rescheduled)</th>
                                                        <th>IN Transit Part Count</th>
                                                        <th>IN Transit Amount</th>
                                                        <th>IN TAT- Part Count, Complete & Cancelled</th>
                                                        <th>IN TAT Amount</th>
                                                        <th>IN TAT- Part Count, Rescheduled & Pending</th>
                                                        <th>IN TAT Amount</th>
                                                        <th>Total Part Count (out+in)</th>
                                                        <th>Total Amount (out+in)</th>
                                                        <th>Difference</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="panel-footer" align='center'>
                <input type='button' onclick='search_dashboard()' class="btn btn-primary" value='Search' >               
            </div>
            </form>
        </div>
    </div>




</div>
<style>



    h2{float:left; width:100%; color:#fff; margin-bottom:30px; font-size: 14px;}
    h2 span{font-family: 'Libre Baskerville', serif; display:block; font-size:45px; text-transform:none; margin-bottom:20px; margin-top:30px; font-weight:700}
    h2 a{color:#fff; font-weight:bold;}


    .card {
        -moz-box-direction: normal;
        -moz-box-orient: vertical;
        background-color: #fff;
        border-radius: 0.25rem;
        display: flex;
        flex-direction: column;
        position: relative;
        margin-bottom:1px;
        border:none;
    }
    .card-header:first-child {
        border-radius: 0;
    }
    .card-header {
        background-color: #f7f7f9;
        margin-bottom: 0;
        padding: 20px 1.25rem;
        border:none;

    }
    .card-header a i{
        float:left;
        font-size:25px;
        padding:5px 0;
        margin:0 25px 0 0px;
        color:#195C9D;
    }
    .card-header i{
        float:right;        
        font-size:30px;
        width:1%;
        margin-top:8px;
        margin-right:10px;
    }
    .card-header a{
        width:85%;
        float:left;
        color:#565656;
    }
    .card-header p{
        margin:0;
    }

    .card-header h3{
        margin:0 0 0px;
        font-size:20px;
        font-family: 'Slabo 27px', serif;
        font-weight:bold;
        color:#3fc199;
    }
    .card-block {
        -moz-box-flex: 1;
        flex: 1 1 auto;
        padding: 20px;
        color:#232323;
        box-shadow:inset 0px 4px 5px rgba(0,0,0,0.1);
        border-top:1px soild #000;
        border-radius:0;
    }
</style>
<?php if (!empty($msg)) { ?>
    <script>
        alert('<?php echo $msg; ?>');
    </script>
<?php } ?>
<script type="text/javascript">
    $('#rm_asm').select2();
    //$('#state_name').select2();

    (function ($, W, D)
    {
        var JQUERY4U = {};
        JQUERY4U.UTIL =
                {
                    setupFormValidation: function ()
                    {
                        //form validation rules
                        $("#rm_state_mapping").validate({
                            rules: {

                                rm_asm: "required",
                                state_name: "required",

                            },
                            messages: {

                                rm_asm: "Please select rm/asm",
                                state_name: "Please assign state(s)"

                            },
                            submitHandler: function (form) {
                                if (validateForm())
                                    form.submit();
                            }
                        });
                    }
                };

        //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
            $(".state_name").select2({
                placeholder: "Select Partner",
                allowClear: true
            });
            var error = "<?= $error ?>";
            if (error != '')
                alert(error);
            JQUERY4U.UTIL.setupFormValidation();
        });

    })(jQuery, window, document);


</script>
<style>
    #annual_charges_report_filter label
    {
        float: right !important;
    }
    #annual_charges_report_filter .input-sm
    {
        width: 272px !important;    
    }
    .dataTables_length label
    {
        float:left;
    }
    .dt-buttons
    {
        float:left;
        margin-left:85px;
    }
    .paging_simple_numbers
    {
        width: 45%;
        float: right;
        text-align: right;
    }
    .dataTables_info
    {
        width: 45%;
        float: left;
        padding-top: 30px;
    }
    table tr:last-child td{
        font-weight: bold !important;
        color: red !important;
    }
    .dataTables_filter{
        display:none !important;
    }
</style>
<?php if (!empty($msg)) { ?>
    <script>
        alert('<?php echo $msg; ?>');
    </script>
<?php } ?>
<script>
    $(document).ready(function () {

    });
    var annual_charges_report = $('#annual_charges_report').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "ajax": {
            "url": "<?php echo base_url(); ?>employee/inventory/defective_spare_dashboard_process_record",
            "type": "POST",
            "data": function (d) {
                d.partner_id = $("#state_name").val();

            }
        },
        "dom": 'lBfrtip',
        "buttons": [
            'copy', 'csv'
        ],
        "order": [],
        "ordering": false,
        "deferRender": true,
        //"searching": false,
        //"paging":false
        "pageLength": 50,
        "language": {
            "emptyTable": "No Data Found",
            "searchPlaceholder": "Search by any column."
        },
    });
    function search_dashboard()
    {
        var state_name = $("#state_name").val();
        var agent_ID = $("#rm_asm").val();
        var submit = true;

        if (state_name == '' || state_name == null)
        {
            alert('Please select Partner');
            submit = false;
            return false;
        }


        if (submit)
        {
            //$("#submit_mapping_rm_asm").val('Searching...');
            $('.input-sm').val(state_name);
            annual_charges_report.ajax.reload();
        }
    }
</script>