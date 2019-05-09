<style>
    #datatable1_filter{
        text-align: right;
    }
    .col-md-3 {
        width: 18%;
    }
    
    .change_background{
        background-color:rgb(162, 230, 162); 
        color:#000;
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
        height: 40px;
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
        <h1> <?php if($query_status == _247AROUND_CANCELLED){echo ucfirst($query_status);}else{ echo "Pending";}?> Queries</h1>
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
        if($this->session->userdata('pincode_msg')){
            echo "<p style=' text-align: center;color: #15881e; font: bold 20px/24px Century Gothic'>".$this->session->userdata('pincode_msg')."</p>";
        }
        ?> 
        <div class="table_filter">
            <div class="row">
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control filter_table" id="booking_date" name="booking_date" placeholder="Booking Date">
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
                        <th>S No.</th>
                        <th>Booking Id</th>
                        <th>User Name / Phone No.</th>
                        <th>Service Name</th>
                        <th>Booking Date / Time</th>
                        <?php if($query_status != _247AROUND_CANCELLED){?>
                        <th  >Status</th>
                         <?php } ?>
                        <th>City</th>
                        <th>Query Remarks</th>
                        <th>Appliance Description</th>
                        <?php if($query_status != _247AROUND_CANCELLED){ ?>
                            <?php if($pincode_status == PINCODE_NOT_AVAILABLE){ ?>
                                <th>Pincode</th>
                            <?php } else { ?>
                                <th>Vendor Status</th>
                            <?php } 
                        } ?>
                        <?php if($c2c) { ?>
                            <th>Call</th>
                            <th>SMS</th>
                        <?php } ?>
                        
                        <th>View</th>
                        <?php if($query_status != _247AROUND_CANCELLED){?>
                            <th>Update</th>
                        <?php } if($query_status == _247AROUND_CANCELLED){ ?>
                            <th>Un-Cancel</th>
                        <?php } else{ ?>
                            <th>Cancel</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    
    var booking_status = '<?php echo $query_status;?>';
    var booking_id = '<?php echo $booking_id;?>';
    var pincode_status = '<?php echo $pincode_status;?>';
    var datatable1;
    
    $('#partner_id').select2({
        placeholder: "Select Partner",
        allowClear: true
    });
    $('#sf_id').select2({
        placeholder: "Select Service Center",
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
    $(document).ready(function(){
        
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
        
      
       datatable1 = $('#datatable1').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": false,
             dom: 'lBfrtip',
             buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'pending_queries',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7, 8, 9],
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
                "url": "<?php echo base_url(); ?>employee/booking/get_queries_data/"+booking_status,
                "type": "POST",
                "data": function(d){
                    d.booking_status =  booking_status;
                    d.booking_id =  '<?php echo $booking_id;?>';
                    d.partner_id =  $('#partner_id').val();
                    d.booking_date =  $('#booking_date').val();
                    d.appliance =  $('#appliance').val();
                    d.pincode_status = pincode_status;
                    d.city =  $('#city').val();
                    d.c2c = '<?php echo $c2c;?>';
                 }
            },
            "deferRender": true,
            "fnRowCallback": function( nRow , aData) {
                var cellData = aData[5];
                if(booking_status !== 'Cancelled' && cellData.split(" ")[1] === '(Missed_call_confirmed)'){
                    $(nRow).addClass("change_background");
                    return nRow;
                }              
            },
            "fnDrawCallback": function(){
                <?php if($query_status  !== "Cancelled" && $pincode_status !== PINCODE_NOT_AVAILABLE){ ?>
  
                    var info = datatable1.page.info();
                    var start = (info.start)+1;
                    var end = (info.end);    
                    for(var c = start; c <= end; c++  ){
                        var index = c;
                        var service_id = $("#service_id_"+ c).val();
                        var pincode = $("#pincode_"+ c).val();
                        if(pincode !== ""){
                            get_vendor(pincode, service_id, index);
                        } else {
                            $("#av_vendor"+index).css("display","none");
                            $("#av_pincode"+index).css("display","inherit");
                        }
                    }
                <?php } ?> 
            }       
        });
        
        $('.filter_table').on('change', function(){
            datatable1.ajax.reload();
        });
        
        function get_vendor(pincode, service_id, index){
            $.ajax({
                    type:"POST",
                    url:"<?php echo base_url()?>employee/vendor/get_vendor_availability/"+pincode+"/"+service_id,
                    success: function(data){
                        if(data !== ""){
                           $("#av_vendor"+index).html(data); 
                        } else {
                            $("#av_vendor"+index).css("display","none");
                            $("#av_pincode"+index).css("display","inherit");
                        }
                    }
            });
        }    
        
    });

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
    
    function form_submit(booking_id){
        $.ajax({
                type:"POST",
                data:{booking_id:booking_id},
                url:"<?php echo base_url()?>employee/vendor/get_add_vendor_to_pincode_form",
                success: function(data){
                    $("#page-wrapper").html(data);
                }
        });
    }
    
    function send_whtasapp_number(booking_id){
        var confirm_sms = confirm("Send Whatsapp Number ?");
        if (confirm_sms == true) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/send_whatsapp_number/',
                data:{booking_id:booking_id},
                success: function(response) {
                    //console.log(response);
                }
            });
        } else { 
            return false;
        }
    }
</script>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('failed')){$this->session->unset_userdata('failed');} ?>
<?php if($this->session->userdata('pincode_msg')){$this->session->unset_userdata('pincode_msg');} ?>
