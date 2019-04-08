<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="https://rawgit.com/wasikuss/select2-multi-checkboxes/master/select2.multi-checkboxes.js"></script>-->
<style>
    #dashboard_notification_form .form-group label.error {
        color: #FB3A3A;
        display: inline-block;;
        padding: 0;
        text-align: left;
        width: 250px;
        margin: 0px;
    }
    #notification_table_filter{
        text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><span><i class="fa fa-bell-o" aria-hidden="true" style="margin-right: 5px;"></i></span>Add Dashboard Notification</div>
            <div class="panel-body">
            <div class="row">
                 <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
                <form name="myForm" class="form-horizontal" id="dashboard_notification_form" name="dashboard_notification_form" novalidate="novalidate" action="<?php echo base_url()?>employee/dashboard/process_dashboard_notification"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('entity_type')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="entity_type" class="col-md-4">Entity Type* </label>
                                    <div class="col-md-8">
                                        <select id="entity_type" name="entity_type" class="form-control" onchange="get_entity()">
                                            <option selected disabled>Select Entity Type</option>
                                            <option value="<?php echo _247AROUND_PARTNER_STRING; ?>"><?php echo _247AROUND_PARTNER_STRING; ?></option>
                                            <option value="<?php echo _247AROUND_SF_STRING; ?>"><?php echo _247AROUND_SF_STRING; ?></option>  
                                            <option value="<?php echo _247AROUND_EMPLOYEE_STRING; ?>"><?php echo _247AROUND_EMPLOYEE_STRING; ?></option> 
                                        </select>
                                        <?php echo form_error('entity_type'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('entity')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="entity" class="col-md-4">Entity* </label>
                                    <div class="col-md-8">
                                        <select id="entity" name="entity[]" class="form-control select2-multiple2" multiple>
                                            <option selected disabled>Select Entity </option>
                                        </select>
                                        <?php echo form_error('entity'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('date_range')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="date_range" class="col-md-4">Start and Expiry Date* </label>
                                    <div class="col-md-8">
                                        <input type="text" id="date_range" name="date_range" class="form-control" placeholder="Select Date Range">
                                        <?php echo form_error('date_range'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('notification_type')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="notification_type" class="col-md-4">Notification Type* </label>
                                    <div class="col-md-8">
                                        <select id="notification_type" name="notification_type" class="form-control">
                                            <option selected disabled>Select Notification Type</option>
                                            <?php if(!empty($notification_type)){ 
                                                foreach ($notification_type as $key => $value) {
                                            ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['type'] ?></option>
                                            <?php
                                                }
                                            }  ?>
                                        </select>
                                        <?php echo form_error('notification_type'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div  class="form-group <?php
                                    if (form_error('message')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="message" class="col-md-2">Message* </label>
                                    <div class="col-md-10">
                                        <textarea id="message" name="message" class="form-control" placeholder="Type Notification Message"></textarea>
                                        <?php echo form_error('message'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label  for="is_marquee" class="col-md-4">Marquee</label>
                                    <div class="col-md-8">
                                        <input type="checkbox" id="is_marquee" name="is_marquee">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <center><input type="submit" value="Submit" class="btn btn-primary"></center>  
                        </div>
                    </div>   
                </form>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" id="notification_table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Entity Type</th>
                                <th>Entity Name</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Marquee</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
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
<!--Modal start-->
    <div id="notification_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Edit Notification </h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Start Date*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" placeholder="Start Date" id="edit_start_date" name="edit_start_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="service_id">End Date*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="text" class="form-control" placeholder="End Date" id="edit_end_date" name="edit_end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Message*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <textarea id="edit_message" name="edit_message" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="edit_notification_type">Type*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select id="edit_notification_type" name="edit_notification_type" class="form-control">
                                           
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="entity_id">Marquee*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <input type="checkbox" id="edit_marquee" name="edit_marquee" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="notification_id" name="notification_id">
                            <button type="button" class="btn btn-success" name='submit_type' onclick="update_notification()">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
<?php if($this->session->userdata('error')){ $this->session->unset_userdata('error'); } ?>
<?php if($this->session->userdata('success')){ $this->session->unset_userdata('success');  } ?>
<script type="text/javascript">
    var notification_datatable = "";
    
    $('document').ready(function(){
        $('#entity, #entity_type, #notification_type').select2();
        $("#edit_start_date, #edit_end_date").datepicker({dateFormat: 'yy-mm-dd'});
        $('#date_range').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            autoUpdateInput: false
        });
        
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $('#date_range').val(picker.startDate.format('YYYY-MM-DD') + '/' + picker.endDate.format('YYYY-MM-DD'));
        });
        
        notification_datatable = $('#notification_table').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2 ]
                    },
                    title: 'notifications',
                },
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/dashboard/get_dashboard_notifications",
                "type": "POST",
                data: function(d){ }
            },
            "deferRender": true       
        });
        
    });
    
    
    //form validation
    (function ($, W, D){
        var JQUERY4U = {};
        JQUERY4U.UTIL = { setupFormValidation: function (){
                $("#dashboard_notification_form").validate({
                rules: {
                    entity_type: "required",
                    entity : "required",
                    date_range : "required",
                    message : "required",
                    notification_type : "required",
                },
                messages: {
                    entity_type: "Please select entity type",
                    entity: "Please select entity",
                    date_range: "Please select date",
                    message : "Please Enter Message",
                    notification_type : "Please select notification type",
                },
                submitHandler: function (form) {
                    form.submit();
                }
                });
            }
        };
        $(D).ready(function ($) {
            JQUERY4U.UTIL.setupFormValidation();
        });
    })(jQuery, window, document);
    
    function get_entity(){
        var entity_type = $("#entity_type").val();
        var url = "";
        var data;
        if(entity_type == '<?php echo _247AROUND_EMPLOYEE_STRING; ?>'){
           url = '<?php echo base_url(); ?>employee/login/get_all_employee/';
           data = {};
        }
        else{
           url = '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/'+entity_type;
           data = {vendor_partner_id: "", invoice_flag: 1};
        }
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (data) {
                $("#entity").html(data);
                $("#entity").val("All").change();
            }
        });
    }
    
    function edit_notification(btn){
        var data = $(btn).data('id');
        var type_id = data.id;
        $("#notification_id").val(data.id);
        $("#edit_start_date").val(data.start_date);
        $("#edit_end_date").val(data.end_date);
        $("#edit_message").val(data.message);
        if(data.marquee == 1){
           $("#edit_marquee").attr("checked", true);
        }
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/dashboard/get_dashboard_notification_type',
            data: {},
            success: function (data) {
               if(data){
                   $("#edit_notification_type").html($.trim(data)).select2();
                   $("#edit_notification_type").val($("#type_id_"+type_id).text()).trigger('change');
               }
            }
        });
        
        $('#notification_model').modal('toggle');
    }
    
    function update_notification(){
        var marquee;
        if($("#edit_marquee").is(":checked")){
            marquee = 1;
        }
        else{
            marquee = 0;
        }
        if(!$("#edit_start_date").val()){
            alert("Please select start date");
        }
        else if(!$("#edit_end_date").val()){
            alert("Please select end date");
        }
        else if(!$("#edit_message").val()){
            alert("Please enter your message");
        }
        else{
            $.ajax({
                url : "<?php echo base_url(); ?>employee/dashboard/update_dashboard_notifications",
                method : "POST",
                data : {
                    start_date : $("#edit_start_date").val(),
                    end_date : $("#edit_end_date").val(),
                    marquee : marquee,
                    message : $("#edit_message").val(),
                    notification_id : $("#notification_id").val(),
                    notification_type : $("#edit_notification_type").val(),
                },
                success : function(response){
                    notification_datatable.ajax.reload();
                    $('#notification_model').modal('toggle');
                    alert("Notification Successfully Updated");
                },
            });
        }
    }
</script> 

