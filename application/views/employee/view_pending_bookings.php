<style>
    #datatable1_filter{
        text-align: right;
    }
    .col-md-3 {
        width: 19%;
    }

    @keyframes blink {
        50% { opacity: 0.0; }
    }
    @-webkit-keyframes blink {
        50% { opacity: 0.0; }
    }
    
    .blink {
        animation: blink 1s step-start 0s infinite;
        -webkit-animation: blink 1s step-start 0s infinite;
    }

    .esclate {
        width: auto;
        height: 17px;
        background-color: #F73006;
        color: #fff;
        margin-left: 0px;
        font-weight: bold;
        margin-right: 0px;
        font-size: 12px;
    }
    
    .dialog{
        display: none;
    }
    .select2-container .select2-selection--single{
        height: 34px;
        border: 1px solid #ccc;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 30px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 32px;
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
    
    #datatable1_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
</style>

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<div id="page-wrapper" >
    <div class="row">
        <h1 style="float:left;"> <?php echo ucfirst($booking_status);?> Bookings</h1>
          <a href="<?php echo  base_url()?>employee/booking/download_pending_bookings/<?php echo  $booking_status?>" id="download_btn"  name="download_btn" class="col-xs-1 btn btn-primary"  style="float:right;margin-top: 21px;">Download</a>
          <div class="clear"></div>
        <hr>
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
        }
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
        }
        if ($this->session->userdata('failed')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('failed') . '</strong>
               </div>';
        }
        ?> 
        <div class="table_filter">
            <div class="row">
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="partner_id">
                                <option value="" selected="selected" disabled="">Select Partner</option>
                                <?php foreach($partners as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="sf_id">
                                <option value="" selected="selected" disabled="">Select Service Center</option>
                                <?php foreach($sf as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="appliance">
                                <option value="" selected="selected" disabled="">Select Services</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val->id?>"><?php echo $val->services?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                </div>
            <div class="row" style="margin-top: 12px;">
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="city">
                                <option value="" selected="selected" disabled="">Select City</option>
                                <?php foreach($cities as $val){ ?>
                                <option value="<?php echo $val['city']?>"><?php echo $val['city']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12" >
                            <select class="form-control filter_table" id="internal_status" multiple="" name="internal_status[]">
                                
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                               <input  type="text" placeholder="Booking Date Range" class="form-control" id="booking_date" value="" name="booking_date"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 12px;">
              <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="request_type" multiple="" name="request_type[]">
                                </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="current_status">
                                <option value="" selected="selected" disabled="">Select Current Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Rescheduled">Rescheduled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="actor" onchange="get_internal_status_and_request_type(this.value)">
                                <option value="" selected="selected" disabled="">Select Actor</option>
                                <option value="247Around">247Around</option>
                                <option value="Partner">Partner</option>
                                <option value="Vendor" selected="">Vendor</option>
                                <option value="not_define">not_define</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 12px;">
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="rm_id" style="padding-left: 3px;">
                                <option value="" selected="selected" disabled="">Select Regional Manager</option>
                                <?php foreach($rm as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['full_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="is_upcountry" style="padding-left: 3px;">
                                <option value="yes">Upcountry</option>
                                <option value="no">Non Upcountry</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="bookings_table">
            <table id="datatable1" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>User Name / Phone Number</th>
                        <th>Service Name</th>
                        <th>Booking Date</th>
                        <th>Booking Age</th>
                        <th>Status</th>
                        <th>Service Center</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tbody></tbody>
                </thead>
            </table>
        </div>
    </div>
    
    <!-- start upcountry model -->
    <div id="myModal3" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" id="open_model1">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upcountry Call</h4>
                </div>
                <div class="modal-body" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end upcountry model -->
    
    <!--Cancel Modal-->
    <div id="penaltycancelmodal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg" >
          <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_remove_penalty" method="POST">

            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" style="text-align: center"><b>Remove Penalty</b></h4>
              </div>
              <div class="modal-body">
                  <span id="error_message" style="display:none;color: red;margin-bottom:10px;"><b>Please Select At Least 1 Booking</b></span>
                  <div id="open_model"></div>
              </div>
              <div class="modal-footer">
                 <input type="button" onclick="form_submit()" value="Submit" class="btn btn-info " form="modal-form">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>

          </form>
      </div>
    </div>
    <!-- end cancel model -->
    <!-- Start Contact Model -->
   <div id="relevant_content_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header well" style="background-color:  #2C9D9C;border-color: #2C9D9C;">
                <button type="button" class="close btn-primary well"  data-dismiss="modal"style="color: white;">&times;</button>
                <h4 class="modal-title"style="color: white;background-color: #2c9d9c;border-color: #2c9d9c;border: 0px; text-align: center;">Contacts</h4>
            </div>
            <div class="modal-body">
            </div>
            <center><img id="loader_gif_contact" src="<?php echo base_url(); ?>images/loadring.gif"></center>
        </div>


    </div>
</div>
    <!-- End Contact Model -->
    
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    
    var booking_status = '<?php echo $booking_status;?>';
    var booking_id = '<?php echo $booking_id;?>';
    var datatable1;
    
    $('#partner_id').select2({
        placeholder: "Select Partner",
        allowClear: true
    });
    $('#sf_id').select2({
        placeholder: "Select Service Center",
        allowClear: true
    });
    $('#rm_id').select2({
        placeholder: "Select Regional Manager",
        allowClear: true
    });
    $('#appliance').select2({
        placeholder: "Select Appliance",
        allowClear: true
    });
    $('#city').select2({
        placeholder: "Select City",
        allowClear: true
    });
      $('#internal_status').select2({
        placeholder: "Select Partner Internal Status",
        allowClear: true
    });
    $('#request_type').select2({
        placeholder: "Select Request Type",
        allowClear: true
    });
    $('#current_status').select2({
        placeholder: "Select Current Status",
        allowClear: true
    });
    $('#actor').select2({
        placeholder: "Select Actor",
        allowClear: true
    });
    $('#is_upcountry').select2({
        placeholder: "Select Upcountry Details",
        allowClear: true
    });
    $(document).ready(function(){
        get_internal_status_and_request_type("Vendor");
        datatable1 = $('#datatable1').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "search": "Booking ID"
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 25,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/booking/get_bookings_by_status/"+booking_status,
                "type": "POST",
                "data": function(d){
                    d.booking_status =  booking_status;
                    d.booking_id =  '<?php echo $booking_id;?>';
                    d.partner_id =  $('#partner_id').val();
                    d.sf_id =  $('#sf_id').val();
                    d.appliance =  $('#appliance').val();
                    d.booking_date =  $('#booking_date').val();
                    d.city =  $('#city').val();
                    d.internal_status = getMultipleSelectedValues('internal_status');
                    d.request_type = getMultipleSelectedValues('request_type');
                    d.current_status = $('#current_status').val();
                    d.actor = $('#actor').val();
                    d.rm_id = $('#rm_id').val();
                    d.is_upcountry = $('#is_upcountry').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,6,7,8,9,10,11,12,13,14,15,16], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true,
            "fnInitComplete": function (oSettings, response) {
               $('input[type="search"]').attr("name", "search_value");           
            }        
        });
        
        $('.filter_table').on('change', function(){
            datatable1.ajax.reload();
        });
    });

</script>
<script>
    function getMultipleSelectedValues(fieldName){
    fieldObj = document.getElementById(fieldName);
    var values = [];
    var length = fieldObj.length;
    for(var i=0;i<length;i++){
       if (fieldObj[i].selected == true){
           values.push(fieldObj[i].value);
       }
    }
   return values.toString();
}
    $(function(){

      $('#dynamic_select').bind('change', function () {
          var url = $(this).val();
          if (url) {
              window.location = url;
          }
          return false;
      });
    });

    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
       
        if (confirm_call == true) {
            
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);
                }
            });
        } else {
            return false;
        }

    }
    
    function open_upcountry_model(sc_id, booking_id, amount_due, sf_rate){
     
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due,
          success: function (data) {
              console.log(data);
           $("#open_model1").html(data);   
           $('#myModal3').modal('toggle');
          }
        });
    }
    
    function form_submit() {
        
        var checkbox_val = [];
        $(':checkbox:checked').each(function(i){
          checkbox_val[i] = $(this).val();
        });
        if(checkbox_val.length === 0){
            $('#error_message').css('display','block');
            return false;
        }else{
            $("#cancellation_form").submit();
        }
    }  
    
    function get_penalty_details(booking_id,status,sf_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_penalty_details_data/' + booking_id+"/"+status,
            data: {sf_id:sf_id},
            success: function (data) {
             $("#open_model").html(data);   
             $('#penaltycancelmodal').modal('toggle');

            }
          });
    }
    
    //Function to show the specific popup form
    function show(id)
    {
        var type = id.search("b_notes");
        var count = id.replace( /^\D+/g, '');

        if (type >= 0) {
            $('#bookingMailForm'+count).toggle(500);
        }
        else {
            $('#reminderMailForm'+count).toggle(500);
        }
    }

    //Function to send email to vendor using ajax
    function send_email_to_vendor(i)
    {

        var id = $("#booking_id"+i).val();
        var additional_note = $("#valueFromMyButton"+i).val();

        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/bookingjobcard/send_mail_to_vendor/' + id + "/" + additional_note,
                success: function(response) {
                    var resAlert = response.search("Mail sent to Service Center successfully.");

                    if (resAlert >= 0)
                        alert("Mail sent to Service Center successfully.")
                    else
                        alert("Mail could not be sent, please try again.");
                }
        });

        $("#bookingMailForm"+i).toggle(500);
    }

    //Function to send reminder email to vendor
    function send_reminder_email_to_vendor(i)
    {
        var id = $("#booking_id"+i).val();
        var additional_note = $("#reminderMailButton"+i).val();
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/bookingjobcard/send_reminder_mail_to_vendor/' + id + "/" + additional_note,
                success: function(response) {
                    var resAlert = response.search("Reminder mail sent to Service Center successfully.");

                    if (resAlert >= 0)
                        alert("Reminder mail sent to Service Center successfully.")
                    else
                        alert("Reminder mail could not be sent, please try again.");
                }
        });

        $("#reminderMailForm"+i).toggle(500);
    }
$('input[name="booking_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                 cancelLabel: 'Clear'
            }
        });
        $('input[name="booking_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));  
            datatable1.ajax.reload();
        });
        $('input[name="booking_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $('input[name="booking_date"]').val("");
        });
        function get_internal_status(actor){
            $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/get_internal_status/' + actor,
                    success: function(response) {
                           $("#internal_status").html(response);
                    }
            });
        }
        function get_request_type(actor){
            $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/get_request_type/' + actor,
                    success: function(response) {
                        $("#request_type").html(response);
                    }
            });
        }
        function get_internal_status_and_request_type(actor){
            if(actor === ""){
                actor = "blank";
            }
            get_internal_status(actor);
            get_request_type(actor);
        }
        function show_contacts(bookingID,create_booking_contacts_flag){
            $("#relevant_content_modal .modal-body").html("");
            $("#loader_gif_contact").show();
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url()  ?>employee/service_centers/get_booking_contacts/'+bookingID,
                        data: {},
                        success: function (response) {
                            if(create_booking_contacts_flag){
                              create_booking_contacts(response);
                            }
                       }
                    });
                }
                 function create_booking_contacts(response){
        var data="";
        var result = JSON.parse(response);
        data =data +  "<tr><td>1) </td><td>247around Account Manager</td><td>"+result[0].am+"</td><td>"+result[0].am_caontact+"</td></tr>";
        data =data +  "<tr><td>2) </td><td>247around Regional Manager</td><td>"+result[0].rm+"</td><td>"+result[0].rm_contact+"</td></tr>";
        data =data +  "<tr><td>2) </td><td>Brand POC</td><td>"+result[0].partner_poc+"</td><td>"+result[0].poc_contact+"</td></tr>";
        var tb="<table class='table  table-bordered table-condensed ' >";
        tb+='<thead>';
        tb+='<tr>';
        tb+='<th class="jumbotron col-md-1">SNo.</th> ';
        tb+='<th class="jumbotron col-md-6">Role</th>';
        tb+='<th class="jumbotron  col-md-5">Name</th>';
        tb+='<th class="jumbotron  col-md-5">Contact</th>';
        tb+='</tr>';
        tb+='</thead>';
        tb+='<tbody>';
        tb+=data;
        tb+='</tbody>';
        tb+='</table>';
        $("#loader_gif_contact").hide();
        $("#relevant_content_modal .modal-body").html(tb);
        $('#relevant_content_table').DataTable();
        $('#relevant_content_table  th').css("background-color","#ECEFF1");
        $('#relevant_content_table  tr:nth-child(even)').css("background-color","#FAFAFA");
        $("#relevant_content_modal").modal("show");
    }
</script>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php if ($this->session->userdata('failed')) {$this->session->unset_userdata('failed');} ?>