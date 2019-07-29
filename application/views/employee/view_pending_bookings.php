<script>
    var booking_status = '<?php echo $booking_status;?>';
    var booking_id = '<?php echo $booking_id;?>';
    var datatable1;
    function get_filter_data(){
    
       $.ajax({
         type: 'POST',
     
         data: {},
         url: '<?php echo base_url(); ?>employee/booking/get_booking_filter_view/'+booking_status,
         success: function (data) {
             $('#loader_gif').attr('src',  "");
             $('#loader_gif').css("display", "none");
             $('#table_filter').html(data);
             
         }
       });
    }
    get_filter_data();
</script>
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
        <a href="<?php echo  base_url()?>employee/booking/download_pending_bookings/<?php echo  $booking_status?>" id="download_btn"  name="download_btn" class="col-xs-1 btn btn-primary"  style="float:right;margin-top: 25px;">Download</a>
        <a href="<?php echo  base_url()?>employee/dashboard" id="btn_dashboard"  name="btn_dashboard" class="col-s-1 btn btn-success"  style="float:right;margin-right:10px;margin-top: 25px;"><i class="fa fa-arrow-left"></i>&nbsp;Go To Dashboard</a>
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
        <div class="table_filter" id="table_filter">
            <img id="loader_gif" src="<?php echo base_url(); ?>images/loader.gif" style="width:50px;" class="col-md-offset-6">
        </div>
        <input type="hidden" id="bookingIDString" value="<?php if(isset($bookingIDString)){ echo $bookingIDString; }  ?>" >
        <hr>
        <div class="bookings_table">
            <table id="datatable1" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>User Name / Phone Number</th>
                        <th>Service Name</th>
                        <th>Brand</th>
                        <th>Booking Date</th>
                        <th>Booking Age</th>
                        <th>Status</th>
                        <th>Service Center</th>
                        <th>State</th>
                        <?php if(isset($saas_module) && (!$saas_module)) { ?>
                        <th></th>
                        <?php } ?>
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
                <div class="modal-body" id="relevant_content_model_data">
                </div>
                <center><img id="loader_gif_contact" src="<?php echo base_url(); ?>images/loadring.gif"></center>
            </div>
        </div>
    </div>
    <!-- End Contact Model -->
    <!-- Helper Document Model -->
    <div id="showBrandCollateral" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Brand Collateral</h4>
                </div>
                <div class="modal-body" id="collatral_container">
                    <center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Helper Document Model -->
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    $(document).ready(function(){   
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
            <?php if(isset($bookingIDString)){ ?> 
            paging: false,
            <?php } ?>
            "pageLength": 25,
             dom: 'lBfrtip',
             buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'pending_bookings',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/booking/get_bookings_by_status/"+booking_status,
                "type": "POST",
                "data": function(d){
                    d.booking_status =  booking_status;
                    d.booking_id =  '<?php echo $booking_id;?>';
                    if ($('#partner_id').length){  
                    d.partner_id   =  $('#partner_id').val();
                    }else{
                    d.partner_id = "";
                    }
                    if ($('#sf_id').length){         d.sf_id        =  $('#sf_id').val();}else{ d.sf_id = ""; }
                    if ($('#booking_date').length){  d.booking_date =  $('#booking_date').val();}else{ d.booking_date = ""; }
                    if ($('#city').length){          d.city         =  $('#city').val();}else{ d.city = ""; }
                    if ($('#current_status').length){d.current_status =  $('#current_status').val();}else{ d.current_status = ""; }
                    if ($('#actor').length){         d.actor          =  $('#actor').val();}else{ d.actor = ""; }
                    if ($('#rm_id').length){         d.rm_id          =  $('#rm_id').val();}else{ d.rm_id = ""; }
                    if ($('#is_upcountry').length){  d.is_upcountry   =  $('#is_upcountry').val();}else{ d.rm_id = ""; }
                    if ($('#appliance').length){d.appliance =  $('#appliance').val();}else{ d.appliance = ""; }
                     if ($('#state').length){d.state =  $('#state').val();}else{ d.state = ""; }
                    d.bulk_booking_id =  $('#bookingIDString').val();
                    d.internal_status = getMultipleSelectedValues('internal_status');
                    d.request_type = getMultipleSelectedValues('request_type');
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true,
            "fnInitComplete": function (oSettings, response) {
               $('input[type="search"]').attr("name", "search_value");           
            }        
        });
        
        
    });
    
function filter_changes(){
datatable1.ajax.reload();
}
    
</script>
<script>
    function getMultipleSelectedValues(fieldName){
         var values = [];
        if ($('#'+fieldName).length){
           fieldObj = document.getElementById(fieldName);
    
           var length = fieldObj.length;
           for(var i=0;i<length;i++){
              if (fieldObj[i].selected == true){
                  values.push(fieldObj[i].value);
              }
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
    

    function open_upcountry_model(sc_id, booking_id, amount_due, flat_upcountry){
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due + "/"+ flat_upcountry,
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
       
        if(result.length > 0) {
            var j;
            for(var i=0;i<result.length;i++) {j=i+1;
                data =data +  "<tr><td>"+j+") </td><td>247around Account Manager <br>("+result[i].am_state+")</td><td>"+result[i].am+"</td><td>"+result[i].am_caontact+"</td></tr>";
            }
            data =data +  "<tr><td>"+(++j)+") </td><td>247around Regional Manager</td><td>"+result[0].rm+"</td><td>"+result[0].rm_contact+"</td></tr>";
            data =data +  "<tr><td>"+(++j)+") </td><td>Brand POC</td><td>"+result[0].partner_poc+"</td><td>"+result[0].poc_contact+"</td></tr>";
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
        else{
            $("#relevant_content_model_data").html("Booking contacts not found");
            $("#loader_gif_contact").hide();
        }
    }
    
    function  get_brand_collateral(booking_id){
       $('#collatral_container').html('<center><img id="loader_gif_pending" src="<?php echo base_url(); ?>images/loadring.gif" ></center>');
       $.ajax({
         type: 'POST',
         data: {booking_id: booking_id},
         url: '<?php echo base_url(); ?>employee/service_centers/get_learning_collateral_for_bookings/',
         success: function (data) {
             $('#collatral_container').html(data);
         }
       });
    }
    
    
</script>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php if ($this->session->userdata('failed')) {$this->session->unset_userdata('failed');} ?>