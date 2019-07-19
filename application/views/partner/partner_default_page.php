<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main">
    <?php
    if($this->session->userdata('agent_id') != '980084' && $this->session->userdata('agent_id') != '980083'){
    ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-12" id="booking_summary" style="margin-top:10px;">
                <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
            </div>
        </div>
    </div>
    <?php
    }
    if($this->session->flashdata('inProcessBookings')){
        echo "Following Bookings has been updated by someone else, Please Check the update and try again";
        echo implode(",",$this->session->flashdata('inProcessBookings'));
    }
    ?>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-1" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>partner/pending_booking/0/1">
                                    Pending Bookings 
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>partner/get_spare_parts_booking/0/1">
                                    Pending Spares
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-3" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>partner/get_waiting_defective_parts/0/1">
                                    Shipped Spares by SF
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-4" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>partner/get_waiting_for_approval_upcountry_charges/0/1">
                                    Waiting Approval Upcountry Charges
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-5" role="tab" data-toggle="tab" aria-expanded="true">
                                    Pending Spare Quotes
                                </a>
                            </li>
                            <?php
                            if($this->session->userdata('review') == 1){
                            ?>
                            <li role="presentation">
                                <a href="#tabs-6" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php //echo base_url();?>partner/review_bookings/0/1">
                                    Waiting For Review
                                </a>
                            </li>
                            <?php }
                           ?>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="tabs-1"></div>
                            <div class="tab-pane" id="tabs-2"></div>
                            <div class="tab-pane" id="tabs-3"></div>
                            <div class="tab-pane" id="tabs-4"></div>
                            <div class="tab-pane" id="tabs-5">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                         <h2>Pending Spares Quotes </h2>
                                            <div class="clearfix"></div>
                                            </div>
                                        <div class="x_content">
                                            <table id="datatable1" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">S.N</th>
                                                        <th class="text-center">Booking ID</th>
                                                        <th class="text-center">Appliance</th>
                                                        <th class="text-center">Spare Part</th>
                                                        <th class="text-center">Parts Number</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">Age Of Requested</th>
                                                        <th class="text-center">Model No</th>
                                                        <th class="text-center">Serial No</th>
                                                        <th class="text-center">Spare Part Pic</th>
                                                        <th class="text-center">Serial Number Pic</th>
                                                        <th class="text-center">Estimate Cost</th>
                                                        <th class="text-center">Submit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="tab-pane" id="tabs-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal1" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="modal-content1">
            </div>
        </div>
    </div>
    
    <div id="myModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Escalate Form</h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" action="#" method="POST" target="_blank" >
                        <h5  class="col-md-offset-3" id ="failed_validation" style="color:red;margin-top: 0px;margin-bottom: 35px;"></h5>
                        <h5 class="col-md-offset-3"  id ="success_validation" style="color:green;margin-top: 0px;margin-bottom: 35px;"></h5>
                       
                        
                        <div class="form-group">
                            <label for="ec_booking_id" class="col-md-2">Booking Id</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control"  name="booking_id" id="ec_booking_id" placeholder = "Booking Id"  readonly>
                            </div>
                        </div>
                        <div class="form-group  <?php if (form_error('escalation_reason_id')) {
                            echo 'has-error';
                        } ?>">
                            <label for="Service" class="col-md-2">Reason</label>
                            <div class="col-md-6">
                                <select class=" form-control" name ="escalation_reason_id" id="escalation_reason_id">
                                    <option selected="" disabled="">----------- Select Reason ------------</option>
                                    <?php
                                    foreach ($escalation_reason as $reason) {
                                        ?>
                                        <option value = "<?php echo $reason['id'] ?>">
                                        <?php echo $reason['escalation_reason']; ?>
                                        </option>
                                <?php } ?>
                                </select>
                                <?php echo form_error('escalation_reason_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="es_remarks" class="col-md-2">Remarks</label>
                            <div class="col-md-6">
                                <textarea  class="form-control" id="es_remarks" name="escalation_remarks" placeholder = "Remarks" ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type= "submit" id="escal_submit_button" onclick="return form_submit()" class="btn btn-primary" value ="Save" style="background-color:#2C9D9C; border-color:#2C9D9C;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal-title">Reject Parts</h4>
                </div>
                <div class="modal-body">
                    <textarea rows="3" class="form-control" id="textarea" placeholder="Enter Remarks"></textarea>
                </div>
                <input type="hidden" id="url">
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_parts()">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
    </div>
    
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
    
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>

<script type="text/javascript">
    
    var spare_parts = '<?php echo $spare_parts?>';
    
    $(function () {
        if(parseInt(spare_parts) > 0 ){
            $("#myTabs li:eq(1) a").tab('show');
            //Loading Pending Spare Parts if Spare Parts Present
            load_view('employee/partner/get_spare_parts_booking/0/1', '#tabs-2');
        }else{
            //Loading Pending Bookings in Else case
            load_view('employee/partner/pending_booking/0/1', '#tabs-1');
        }
    
    });
    
    
    $('#myTabs a').click(function (e) {
        e.preventDefault();

        var url = $(this).attr("data-url");
        var href = this.hash;
        if(href === '#tabs-5'){
            $(this).tab('show');
        }else{
             //Loading view with Ajax data
            load_view(url,href);
        }
       
    });
    
    function open_upcountry_model(booking_id, amount_due, flat_upcountry){
      
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/'+ booking_id+"/"+amount_due +"/"+ flat_upcountry,
            success: function (data) {
             $("#modal-content1").html(data);   
             $('#myModal1').modal('toggle');

            }
        });
    }
    
    var oow_spare;
    
    $(document).on("click", ".open-AddBookDialog", function () {
        var myBookId = $(this).data('id');
        $(".modal-body #ec_booking_id").val( myBookId );
        
    });
    
    window.onload = function(){
     //Adding Validation   
        $("#selectall_address").change(function(){
            var d_m = $('input[name="download_courier_manifest[]"]:checked');
            if(d_m.length > 0){
                $('.checkbox_manifest').prop('checked', false); 
                $('#selectall_manifest').prop('checked', false); 
            }
        $(".checkbox_address").prop('checked', $(this).prop("checked"));
        });
    
        $("#selectall_manifest").change(function(){
            var d_m = $('input[name="download_address[]"]:checked');
            if(d_m.length > 0){
                $('.checkbox_address').prop('checked', false); 
                $('#selectall_address').prop('checked', false); 
            }
        $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
        }); 
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_booking_summary_data/'+<?php echo $this->session->userdata('partner_id')?>,
            success: function (data) {
                $("#booking_summary").html(data);   
            }
        });
    }
    
    function load_view(url, tab) {
        //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>" + url,
            data: {is_ajax:true},
            success: function (data) {
                $(tab).html(data);
                if(tab === '#tabs-2'){
                    //Adding Validation   
                    $("#selectall_address").change(function(){
                        var d_m = $('input[name="download_courier_manifest[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_manifest').prop('checked', false); 
                            $('#selectall_manifest').prop('checked', false); 
                        }
                    $(".checkbox_address").prop('checked', $(this).prop("checked"));
                    });
    
                    $("#selectall_manifest").change(function(){
                        var d_m = $('input[name="download_address[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_address').prop('checked', false); 
                            $('#selectall_address').prop('checked', false); 
                        }
                    $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
                    }); 
                }
            },
            complete: function () {
                $('#loading_image').hide();
            }
        });
    }
    
    function form_submit(){
        var escalation_id = $("#escalation_reason_id").val();
        var booking_id = $("#ec_booking_id").val();
        var remarks = $("#es_remarks").val();
        
        if(escalation_id ===  null){
            $("#failed_validation").text("Please Select Any Escalation Reason");
           
        }  else {
            $("#escal_submit_button").button('loading');
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>partner/process_escalation/'+booking_id,
                data: {escalation_reason_id: escalation_id,escalation_remarks:remarks, booking_id:booking_id},
                success: function (data) {
                  
                  if(data === "success"){
                        $("#failed_validation").text("");
                        $("#success_validation").text("Booking Escalated");
                        location.reload();
                        $('#myModal').modal('toggle');
                         
                   } else {
                       $("#success_validation").text("");
                       $("#failed_validation").html(data);
                   }
                }
                
              });
            
        }
        
        return false;
    }
    
    function check_checkbox(number){
      
      if(number === 1){
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        if(d_m.length > 0){
            $('.checkbox_manifest').prop('checked', false); 
            $('#selectall_manifest').prop('checked', false); 
        }
          
      } else if(number === 0){
         var d_m = $('input[name="download_address[]"]:checked');
        if(d_m.length > 0){
             $('.checkbox_address').prop('checked', false); 
             $('#selectall_address').prop('checked', false); 
         }
      }
      
    }
    
    function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
    }
    
    $(document).ready(function () {
        $("#datatable1_filter").hide();
        
        <?php $data = array('booking_details.partner_id' => $this->session->userdata('partner_id'), 'status' => SPARE_OOW_EST_REQUESTED);
        ?>
         <?php $column = array(NULL,NUll,NUll,"age_of_request", NULL,NULL, NULL, NULL, NULL);?>
         var column_order = <?php echo json_encode($column);?>;
         var obj = '<?php echo json_encode($data); ?>';
         var select = '<?php echo "services.services, spare_parts_details.parts_requested, spare_parts_details.model_number, spare_parts_details.serial_number, assigned_vendor_id, "
         . "amount_due, spare_parts_details.id, spare_parts_details.quantity,spare_parts_details.booking_id, defective_parts_pic, serial_number_pic,booking_details.partner_id ,inventory_master_list.part_number"; ?>';
         oow_spare = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
           
            // Load data for the table's content from an Ajax source
            "ajax": {
                type: "POST",
          
              data : {requestType:'<?php echo SPARE_OOW_EST_REQUESTED;?>', 'crmType': 'Partner',
                  'select':select,'where':obj, column_order:column_order},
              url: "<?php echo base_url();?>apiDataRequest"
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
          }
            
        });
        
    });
    
    function update_spare_estimate_cost(spare_id, booking_id, assigned_vendor_id, amount_due){
        var estimate_cost = $("#estimate_cost_" + spare_id).val();
      
        if(Number(estimate_cost) > 1){
            swal({
                title: "Do You Want To Continue with amount "+estimate_cost+ "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: true
                
            },
            
            function(){
                $.ajax({
                    type: "POST",
                    beforeSend: function(){
                        swal("Thanks!", "Please Wait..", "success");
                        $('body').loadingModal({
                            position: 'auto',
                            text: 'Loading Please Wait...',
                            color: '#fff',
                            opacity: '0.7',
                            backgroundColor: 'rgb(0,0,0)',
                            animation: 'wave'
                        });

                    },
                    data:{'estimate_cost':estimate_cost, booking_id:booking_id,assigned_vendor_id:assigned_vendor_id,amount_due:amount_due, 
                        agent_id:'<?php echo $this->session->userdata('agent_id');?>', 
                        partner_id: '<?php echo $this->session->userdata('partner_id');?>', 'sp_id':spare_id, requestType:'UPDATE_OOW_EST', gst_rate: '<?php echo DEFAULT_TAX_RATE;?>'},
                    url: "<?php echo base_url() ?>apiDataRequest",
                    success: function (data) {

                        if(data === 'Success'){
                            oow_spare.ajax.reload(null, false);
                            swal("Thanks!", "Booking updated successfully!", "success");

                        } else {
                            swal("Oops", "There is some problem, please contact 247Around Team", "error");

                        }
                        $('body').loadingModal('destroy');
                    }
                });
            });
        } else {
           swal("Oops", "Please Provide Estimate Cost", "error");
           //alert("Please Provide Estimate Cost");
        }
      
       return false;
    }
    
    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
        $('#modal-title').text("Reject Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        
    });
    
    function reject_parts(){
        var remarks =  $('#textarea').val();
        if(remarks !== ""){
            var url =  $('#url').val();
       
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge:0},
                success: function(data){
              
                    if(data === "Success"){
                        //  $("#"+booking_id+"_1").hide()
                        $('#myModal2').modal('hide');
                        alert("Updated Successfully");
                        location.reload();
                    } else {
                        alert("Spare Parts Cancellation Failed!");
                    }
                }
            });
        } else {
            alert("Please Enter Remarks");
        }
    }
    
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
<style>
    #datatable1 td{
        text-align: center !important;
    }
   #datatable1 td:nth-child(2){
            color: blue !important;
    }
    #datatable1 td:nth-child(8){
            padding-left: 82px !important;
    }   
    </style>
