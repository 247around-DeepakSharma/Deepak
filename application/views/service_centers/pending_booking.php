<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<div class="container-fluid">
    <div class="row" >
        <?php 
        if(!$multiple_booking){ 
            $booking_id = ($this->uri->segment(3) != '' ? $this->uri->segment(3) : ''); 
        }
        ?>
        <?php
            if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                 <span aria-hidden="true">&times;</span>
                             </button>
                             <strong>' . $this->session->userdata('success') . '</strong>
                         </div>';
            }
            ?>
        <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('error') . '</strong>
            </div>';
            }
            ?>
        <div class="col-md-10 col-md-offset-2">
            <div style="margin-top:10px; display: flex;font-size: 25px;">
                    <b>Rating:</b> &nbsp;
                    <span class="stars" style="margin-top: 9px;"><?php echo $rating; ?></span> &nbsp;
                    <b><span <?php if($rating > '3.5') { echo "class='text-success'";}else{echo "class='text-danger'";}?>><?php echo $rating; ?> /5</span></b> &nbsp;
                    <div class="sf-escalation">
                        <b> <span style="color:#333;"> | </span> Overall Escalation:</b>
                        <b><span id="sf-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
                    </div>
                    <div class="sf-escalation">
                        <b> <span style="color:#333;"> | </span> Current Month Escalation:</b>
                        <b><span id="sf-cm-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
                    </div>
            </div>
    </div>
        <?php if($this->session->userdata('is_update') == 1){ ?>
        <div class="col-md-12" id="header_summary" style="margin-top:10px;">
            <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
        </div>
        <?php } ?>
        
        <div class="col-md-12 text-center" >
            <div style="margin-top:10px; font-size: 25px; text-align: center">
                    <b>Defective Part Summary</b> &nbsp;
                    

            </div>
    </div>
        <div class="col-md-12" id="defective_header_summary" style="margin-top:10px;">
            <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
        </div>
        <div class="col-md-12">
            <h2 style="display:inline-flex;">Pending Bookings</h2>
            <div class="pull-right" style=' margin-top: 30px;margin-right: 15px;'>
                <a href="<?php echo base_url(); ?>employee/service_centers/download_sf_pending_bookings_list_excel"class="btn btn-primary" style="background-color: #2C9D9C;border-color: #2C9D9C;">Download Pending Bookings List</a>
            </div>
        </div>
        <div class="col-md-10">
            <ul class="nav nav-tabs" role="tablist" >
                <li role="presentation" class="active"><a href="#today_booking" aria-controls="today_booking" role="tab" data-toggle="tab"><?php if($booking_id == ''){ ?>Today Bookings<?php } else { echo "Searched Booking";} ?></a></li>
                <?php if($this->session->userdata('is_update') == 1){ ?>
                <li role="presentation"><a href="#tomorrow_booking" aria-controls="tomorrow_booking" role="tab" data-toggle="tab">Tomorrow Bookings</a></li>
                <li role="presentation"><a href="#rescheduled_booking" aria-controls="rescheduled_booking" role="tab" data-toggle="tab">Rescheduled Bookings</a></li>
                <li role="presentation"><a href="#inprogress_bookings" aria-controls="inprogress_bookings" role="tab" data-toggle="tab">InProcess Bookings</a></li>
                <li role="presentation"><a href="#spare_required" aria-controls="spare_required" role="tab" data-toggle="tab">Spare Required Bookings</a></li>
                <?php if($this->session->userdata('is_engineer_app') == 1){ ?>
                <li role="presentation"><a href="#bookings_on_approval" aria-controls="bookings_on_approval" role="tab" data-toggle="tab">Bookings On Approval</a></li>
                <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<div class="tab-content" id="tab-content">
    <center style="margin-top:30px;"> <img style="width: 60px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Send Reschedule Request</h4>
            </div>
            <div class="modal-body">
                <form name="myForm1" id="reschedule_form" class="form-horizontal" method="POST">
                    <div class="form-group">
                        <label for="name" class="col-sm-3">Booking Id </label>
                        <div class="col-md-6">
                            <input type="text" name="booking_id"  class="form-control "  id="booking_id" readonly></input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-3">Booking Date </label>
                        <div class="col-md-6">
                            <div class="input-group input-append date" >
                                <input type="text" id="datepicker" class="form-control "  style="z-index:9999; background-color:#fff;" name="booking_date" required readonly='true'>
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-3">Reschedule Reason</label>
                        <div class="col-md-6">
                            <textarea name="reason_text" rows="5" class="form-control" id="remarks" placeholer="Plese Enter Reschedule Reason" ></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12" style="margin-top: 5px; margin-bottom: 5px;">
                <p id="error" style="color: red"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="sendRescheduleRequest()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="open_model">
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
<script>
    $(document).ready(function() {
    
        $('#today_datatable').dataTable( {
            "pageLength": 50
           
        } );
       var html = '<div><p style="color:red;">Bookings have to be updated daily else you lose Incentive amount</p></div>\n\
                   <div><p style="color:red;"><i style="font-size:20px;" class="fa fa-road" aria-hidden="true"></i>  is the Upcountry Booking symbol.</p>';
        
        
        $("#today_datatable_filter").html(html);
        $("#today_datatable_filter").css("font-weight", "bold");
        
    
        $('#tomorrow_datatable').dataTable( {
            "pageLength": 50,
            "bFilter": false
        } );
    
        $('#inprogress_datatable').dataTable( {
            "pageLength": 50,
            "bFilter": false
        } );
    
        $('#spare_required_datatable').dataTable({
            "pageLength": 50,
            "bFilter": false
        });
        $('body').popover({
           selector: '[data-popover]',
           trigger: 'click hover',
           placement: 'auto',
           delay: {
               show: 50,
               hide: 100
           }
        });
        
        $.ajax({
            method:'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/get_sf_escalation/<?php echo $this->session->userdata('service_center_id')?>',
            success:function(res){
                if(res === 'empty'){
                    $('#sf-escalation-value').html('0');
                    $('#sf-cm-escalation-value').html('0');
                }else{
                    var data = JSON.parse(res);
                    $('#sf-escalation-value').html(data['total_escalation_per']);
                    $('#sf-cm-escalation-value').html(data['current_month_escalation_per']);
                }
                
                
            }
        });
    } );
    
    
    
     $('.engineers_id').select2();
    
    function edit_engineer(div) {
    // $("#assign_engineer_div"+div).show();
    $("#assign_engineer_div" + div).css("display", "block");
    $("#engineer_name_div" + div).hide();
    $("#engineer"+div).removeAttr("disabled");
    }
    
     function submitForm(form_id) {
        var html = "<img src='<?php echo base_url(); ?>images/loader.gif' />";
        $('.submit_button').hide();
        $('.loading').append(html);
        var fd = new FormData(document.getElementById(form_id));
        fd.append("label", "WEBUPLOAD");
        $.ajax({
           url: "<?php echo base_url() ?>employee/service_centers/assigned_engineers",
          type: "POST",
          data: fd,
          processData: false, // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        }).done(function (data) {
          //console.log(data);
          location.reload();
    
    
        });
        return false;
    }
    
    
    $(".ack_date").datepicker({dateFormat: 'yy-mm-dd'});
    
    //    function submit_spare_form(){
    //        var html = "<img src='<?php echo base_url(); ?>images/loader.gif' />";
    //        $('#submit_button1').hide();
    //        $('#loading1').append(html);
    //        var fd = new FormData(document.getElementById(form_id));
    //        fd.append("label", "WEBUPLOAD");
    //        $.ajax({
    //          url: "<?php echo base_url(); ?>service_center/acknowledge_delivered_spare_parts",
    //          type: "POST",
    //          data: fd,
    //          processData: false, // tell jQuery not to process the data
    //          contentType: false   // tell jQuery not to set contentType
    //        }).done(function (data) {
    //          //console.log(data);
    //          location.reload();
    //
    //
    //        });
    //        return false;
    //    
    //    }
    
</script>
<style>
    .dataTables_filter, .dataTables_paginate{
    float:right;
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
    .esclate{
    width: 115px;
    height: 17px;
    background-color: #F73006;
    color: #fff;
    /* transform: rotate(-26deg); */
    margin-left: -7px;
    font-weight: bold;
    margin-right: -16px;
    /* margin-top: 15px; */
    font-size: 12px;
    /* text-align: center; */
    }
    
    span.stars, span.stars span {
    display: block;
    background: url(/images/stars.png) 0 -16px repeat-x;
    width: 80px;
    height: 16px;
}

span.stars span {
    background-position: 0 0;
}
.notify-alert{
    bottom:0px!important;
}
</style>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
    $(function() { $( "#datepicker" ).datepicker({  minDate: new Date });});
    $(document).ready(function() {
    get_header_summary();
    get_defective_header_summary();
    pending_booking_on_tab();
    });
    
    
    function setbooking_id(booking_id){
    
     $('#booking_id').val(booking_id);
    }
    
    function sendRescheduleRequest(){
       var booking_id = $('#booking_id').val();
       var booking_date = $('#datepicker').val();
       
       var remarks = $('#remarks').val();
       if(booking_date ===""){
         
          $("#error").text('Plese Enter Booking Date');
           return false;
       }
    
       if(remarks ===""){
          $("#error").text('Plese Enter Reschedule Reason');
           return false;
       }
    
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/service_centers/save_reschedule_request',
           data: {booking_id: booking_id, booking_date: booking_date, reason_text: remarks},
           success: function (result) {
    
               //console.log(result);
               location.reload();
              
           }
        });
    }
    
    var date = new Date();
    var end = new Date();
    end.setHours(21);
    end.setMinutes(0);
    end.setMilliseconds(0);
    
    var _second = 1000;
    var _minute = _second * 60;
    var _hour = _minute * 60;
    var _day = _hour * 24;
    var timer;
    
    function showRemaining() {
       var now = new Date();
       var distance = end - now;
       if (distance < 0) {
    
           clearInterval(timer);
           $(".countdown").text('Grace Period');
    
           return;
       }
       //var days = Math.floor(distance / _day);
       var hours = Math.floor((distance % _day) / _hour);
       var minutes = Math.floor((distance % _hour) / _minute);
       //var seconds = Math.floor((distance % _minute) / _second);
       
       var remaining_hr = hours + ":"+ minutes + " hr Left to get incentive";
      
       $(".countdown").text(remaining_hr);
       
    
    }
    
    if(date.getHours() >=9){ // Check the time
       //console.log(date.getHours());
       timer = setInterval(showRemaining, 1000);
    }
    
    function open_upcountry_model(booking_id, is_customer_paid, flat_upcountry){
      
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>service_center/pending_booking_upcountry_price/' + booking_id+"/"+is_customer_paid +"/"+flat_upcountry,
      success: function (data) {
       $("#open_model").html(data);   
       $('#myModal1').modal('toggle');
    
      }
    });
    }
    
    function get_header_summary(){
       $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/service_centers/get_header_summary/',
         success: function (data) {
          $("#header_summary").html(data);   
    
         }
       });
    
    }
    
    function get_defective_header_summary(){
       $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/service_centers/get_defective_part_header_summary/',
         success: function (data) {
          $("#defective_header_summary").html(data);   
    
         }
       });
    
    }
    
    function pending_booking_on_tab(){
    booking_list = "";
     <?php   if($multiple_booking){ ?>
             var booking_list = "<?php echo $booking_id ?>";
            <?php  $booking_id ="";
             ?>
       <?php }
       else{
           if(empty($booking_id)){
               $booking_id ="";
           }
       }
       ?>
       $.ajax({
         type: 'POST',
         data: {booking_list: booking_list},
         url: '<?php echo base_url(); ?>employee/service_centers/pending_booking_on_tab/'+ "<?php echo $booking_id; ?>",
         success: function (data) {
          $("#tab-content").html(data);   
         }
       });
    
    }
    
    
    
    $.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
        var val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}
    $(function() {
    $('span.stars').stars();
});
    function  get_brand_collateral(booking_id){
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

<!-- show alert message if GST is not updated -->
<?php if(!$this->session->userdata('is_gst_exist')) { ?> 
<script src="<?php echo base_url(); ?>js/around_notify.js"></script>
<script>
    
    $(document).ready(function(){
        $.notify({
            message: '<?php echo SF_NOTIFICATION_MSG ?>'

            },{
                type: 'danger',
                url_target: '<?php echo base_url(); ?>service_center/gst_details',
                placement: {
			from: "bottom",
			align: "center"
		}
            }
        );
    }); 
     

</script>
<?php } ?>
<script>
 function load_cancelled_status(booking_id, key, block){
       $.ajax({
           type: 'post',
           url: '<?php echo base_url()  ?>employee/inventory/get_spare_cancelled_status/' + booking_id,
           success: function (response) {
               
               if($.trim(response) === "success"){
                   
                   document.getElementById("spare_"+ block+ key).src="<?php echo base_url();?>images/spare_cancelled.png";
               }  else {
                    
                    $("#spare_"+block + key).css("display", "none");
               }
               //console.log(response);

          }
       });
   }
   
   function load_delivered_status(booking_id, key, block){
     $.ajax({
           type: 'post',
           url: '<?php echo base_url()  ?>employee/inventory/get_spare_delivered_status/' + booking_id,
           success: function (response) {
               var obj  = JSON.parse(response);
               //console.log(obj);
               if(obj[0].is_micro_wh==1){   //SPARE_DELIVERED_TO_SF

                   document.getElementById("spare_delivered_"+ block+ key).src="<?php echo base_url();?>images/msl_available.png";
               }  else if(obj[0].status=='Spare Parts Delivered to SF') {
                    document.getElementById("spare_delivered_"+ block+ key).src="<?php echo base_url();?>images/spare_parts_delivered.png";
               }else{
                $("#spare_delivered_"+block + key).css("display", "none");
               }
               
          }
       });
   }
   
   function load_spare_cost_status(booking_id, key, block){
       $.ajax({
           type: 'post',
           url: '<?php echo base_url()  ?>employee/inventory/get_spare_status/' + booking_id,
           success: function (response) {
               var obj  = JSON.parse(response);
               if($.trim(obj[0].status) == '<?php echo SPARE_OOW_EST_GIVEN; ?>'){
                   
                   document.getElementById("spare_cost_given_"+ block+ key).src="<?php echo base_url();?>images/spare_estimate_arrived.png";
               }  else {
                    
                    $("#spare_cost_given_"+block + key).css("display", "none");
               }
               //console.log(response);

          }
       });
   }
</script>
<!-- end alert message -->
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>