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
             //console.log(data);
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
<!--        <a href="<?php echo  base_url()?>employee/booking/download_pending_bookings/<?php echo  $booking_status?>" id="download_btn"  name="download_btn" class="col-xs-1 btn btn-primary"  style="float:right;margin-top: 21px;">Download</a>-->
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
                    if ($this->session->userdata('rating_error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">
            
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('rating_error') . '</strong>
                   </div>';
            }
            ?> 
        <div class="table_filter" id="table_filter">
            <img id="loader_gif" src="<?php echo base_url(); ?>images/loader.gif" style="width:50px;" class="col-md-offset-6">
        </div>
        <hr>
        <div class="bookings_table">
            <table id="datatable1" class="table table-bordered table-responsive" style="display:none">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>User Name / Phone Number</th>
                        <th>Service Name</th>
                        <th>Service Centre</th>
                        <th>Service Centre City</th>
                        <th>Completion Date</th>
                        <?php if($c2c) {?>
                        <th>Call</th>
                        <?php } ?>
                        <?php if(!$saas_module){?>
                        <?php if($booking_status === _247AROUND_COMPLETED) { ?> 
                        <th>Edit</th>
                        <th>Cancel</th>
                        <?php } else if ($booking_status === _247AROUND_CANCELLED) { ?> 
                        <th>Complete</th>
                        <?php } ?>
                        <?php } ?>
                        <?php if(!$saas_module){?>
                        <th>Open</th>
                        <?php } ?>
                        <th>View</th>
                        <?php if($booking_status === _247AROUND_COMPLETED){ ?> 
                        <th>Rate</th>
                        <?php } ?>
                        <th>Penalty</th>
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
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
        
    function load_datatable(){
        datatable1 = $('#datatable1').DataTable({
            "processing": true, 
            "serverSide": true,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 50,
            "ordering": false,
             dom: 'lBfrtip',
             buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: booking_status+'-bookings',
                    exportOptions: {
                       columns: [1,2,3,4,5,6],
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
                    d.c2c = '<?php echo $c2c; ?>';
                    if ($('#partner_id').length){    d.partner_id   =  $('#partner_id').val();}else{ d.partner_id = ""; }
                    if ($('#sf_id').length){         d.sf_id        =  $('#sf_id').val();}else{ d.sf_id = ""; }
                    if ($('#closed_date').length){  d.booking_date_range =  $('#closed_date').val();}else{ d.booking_date_range = ""; }
                    if ($('#city').length){          d.city         =  $('#city').val();}else{ d.city = ""; }
                    if ($('#ratings').length){d.ratings =  $('#ratings').val();}else{ d.ratings = ""; }
                    if ($('#appliance').length){d.appliance =  $('#appliance').val();}else{ d.appliance = ""; }
                    if ($('#request_type_booking').length){d.request_type_booking =  $('#request_type_booking').val();}else{ d.request_type_booking = ""; }
                    if ($('#completed_booking').length){d.completed_booking =  $('#completed_booking').val();}else{ d.completed_booking = ""; }
                    if ($('#state').length){d.state =  $('#state').val();}else{ d.state = ""; }
                 }
            },
            "deferRender": true,
            "fnInitComplete": function (oSettings, response) {
               $('input[type="search"]').attr("name", "search_value");           
            }        
        });
        
      } 
        
 </script>
<script>
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
          url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due +"/"+flat_upcountry,
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
    
    
</script>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php if ($this->session->userdata('failed')) {$this->session->unset_userdata('failed');} ?><?php if ($this->session->userdata('rating_error')) {$this->session->unset_userdata('rating_error');} ?>
