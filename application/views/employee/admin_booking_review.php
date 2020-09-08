<?php if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="border : 0px;">
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-1" data-id="rescheduled" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_rescheduled_bookings/1">
                                    Rescheduled Bookings
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-2" data-id="completed" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_bookings_by_status/Completed/0">
                                    Completed Bookings By SF 
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-3" data-id="cancelled" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_bookings_by_status/Cancelled/0">
                                    Cancelled Bookings By SF
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-4" data-id="cancelled_review" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_bookings_by_status/Cancelled/0/1">
                                   Cancelled Bookings Under Partner Review
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-5" data-id="service_category_changed" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_bookings_by_status/Completed_By_SF/0">
                                   Service Category Changed By SF(Completed)
                                </a>
                            </li>
                            <!--Added Tab for calls cancelled due to Wrong Area-->  
                            <li role="presentation">
                                <a href="#tabs-6" data-id="wrong_area_cancelled" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>employee/booking/review_bookings_by_status/Cancelled/0/0/0/<?php echo CANCELLATION_REASON_WRONG_AREA_ID ?>">
                                    Wrong Area Bookings
                                </a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="tabs-1"></div>
                            <div class="tab-pane" id="tabs-2"></div>
                            <div class="tab-pane" id="tabs-3"></div>
                            <div class="tab-pane" id="tabs-4"></div>
                            <div class="tab-pane" id="tabs-5"></div>
                            <div class="tab-pane" id="tabs-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
     function load_view(url, tab,link_id, data_id) {
         if(link_id){
             document.getElementById(link_id).style.background_color = "red";
         }
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>" + url,
            data: {is_ajax:true, data_id:data_id},
            success: function (data) {
                $(tab).html(data);
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }
    $(function () {
        load_view('employee/booking/review_rescheduled_bookings/1', '#tabs-1',0, 'rescheduled');
    });
    
     $('#myTabs a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr("data-url");
        var data_id = $(this).attr("data-id");
        var href = this.hash;
        load_view(url,href,0,data_id);
    });
    $("#search").keyup(function () {
    var value = this.value.toLowerCase().trim();

        $("table tr").each(function (index) {
          if (!index) return;
          $(this).find("td").each(function () {
              var id = $(this).text().toLowerCase().trim();
              var not_found = (id.indexOf(value) == -1);
              $(this).closest('tr').toggle(!not_found);
              return not_found;
          });
        });
    });
    
    function outbound_call(phone_number, booking_primary_id = ''){
        var confirm_call = confirm("Call Customer ?");
    
        if (confirm_call == true) {
    
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number + '/' + booking_primary_id,
                success: function(response) {
                }
            });
        } else {
            return false;
        }
    
    }
        </script>
        
        <style type="text/css">
   .marquee {
   height: 60px;
   width: 60px;
   color: red;
   overflow: hidden;
   position: relative;
   }
   .marquee div {
   display: block;
   width: 60px;
   height: 100%;
   position: absolute;
   overflow: hidden;
   animation: marquee 5s linear infinite;
   }
   .marquee span {
   float: left;
   width: 50%;
   }
   @keyframes marquee {
   0% { left: 0; }
   100% { left: -100%; }
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
   
    color: #F73006;
    /* transform: rotate(-26deg); */
    margin-left: 0px;
    font-weight: bold;
    margin-right: 0px;
    font-size: 12px;
}
</style>
 