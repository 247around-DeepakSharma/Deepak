<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>
    #datatable1_wrapper{
        margin-top: 20px;
    }
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }
</style>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-6" role="tab" data-toggle="tab" aria-expanded="true">
                                    Acknowledge From Partner
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_center/spare_parts/0/1">
                                <!--  Pending Spares--> Send To SF
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-3" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_center/defective_spare_parts/0/1">
                                    Acknowledge From SF
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-5" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_center/send_to_partner_list">
                                    Send To Partner
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-7" role="tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo base_url();?>service_centers/spare_assigned_to_partner">
                                    Spare Assigned To Partner
                                </a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane" id="tabs-2"></div>
                            <div class="tab-pane" id="tabs-5"></div>
                            <div class="tab-pane" id="tabs-3"></div>     
                            <div class="tab-pane" id="tabs-7"></div>
                            <div class="tab-pane active" id="tabs-6">
                                <div class="right_col" role="main">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
                                            <div class="x_panel">
                                                <div class="x_title">
                                                    <h3>Spare need to acknowledge</h3>
                                                    <hr>
                                                    <p id="reject_err"></p>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <div class="success_msg_div" style="display:none;">
                                                        <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            <strong><span id="success_msg"></span></strong>
                                                        </div>
                                                    </div>
                                                    <div class="error_msg_div" style="display:none;">
                                                        <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            <strong><span id="error_msg"></span></strong>
                                                        </div>
                                                    </div>
                                                    <div class="x_content_header">
                                                        <section class="fetch_inventory_data">
                                                            <div class="row">
                                                                <div class="form-inline">
                                                                    <div class="form-group col-md-4">
                                                                        <select class="form-control" id="partner_id">
                                                                            <option value="" disabled="">Select Partner</option>
                                                                        </select>
                                                                    </div>
                                                                    <button class="btn btn-success btn-sm col-md-2" id="get_inventory_data">Submit</button>
                                                                </div>
                                                                 <div class="approved pull-right" style="margin-left: 20px;">
                                                                    <div class="btn btn-info btn-sm acknowledge_all_spare" onclick="process_to_reject_spare();">Reject spare</div>
                                                                </div>
                                                                <div class="approved pull-right">
                                                                    <div class="btn btn-info btn-sm acknowledge_all_spare" onclick="process_acknowledge_spare();" id="ack_spare">Acknowledge spare received</div>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <hr>
                                                    <div class="inventory_spare_list">
                                                        <table id="inventory_spare_table" class="table table-bordered table-responsive" style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Booking ID</th>
                                                                    <th>Appliance</th>
                                                                    <th>Invoice ID</th>
                                                                    <th>Spare Type</th>
                                                                    <th>Spare Part Name</th>
                                                                    <th>Spare Part Number</th>
                                                                    <th>Spare Quantity</th>
                                                                    <th>Courier Name</th>
                                                                    <th>Courier AWB Number</th>
                                                                    <th>
                                                                        Acknowledge
                                                                        <input type="checkbox" id="ack_all">
                                                                    </th>
                                                                    <th>Rejected
                                                                        <input type="checkbox" id="reject_all">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--Modal start-->
                                        <div id="modal_data" class="modal fade" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div id="open_model"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal end -->
                                    </div>
                                </div>
                            </div>
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
    
     <!-- model -->
     <!-- Start MSL Real time Tracking model -->
    <div id="gen_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="gen_model_title"></h4>
                </div>
                <div class="modal-body" id="gen_model_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
   <!-- End MSL Real time Tracking model -->
     
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
                                    //<?php
//                                    foreach ($escalation_reason as $reason) {
//                                        ?>
                                        //<option value = "//<?php //echo $reason['id'] ?>">
                                        //<?php //echo $reason['escalation_reason']; ?>
                                        //</option>
                                //<?php //} ?>
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
                <input type="hidden" id="modal_partner_id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_parts()">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>

<script type="text/javascript">
    
    $('#myTabs a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr("data-url");
        var href = this.hash;   
        if(href === '#tabs-5'){
            load_view_send_to_partner(url,href);
        }else if(href === '#tabs-6'){
            $(this).tab('show');
        }else{
             //Loading view with Ajax data
            load_view(url,href);
        }
       
    });
    
    function open_upcountry_model(booking_id, amount_due, flat_upcountry){      
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/booking_upcountry_details/'+ booking_id+"/"+amount_due +"/"+flat_upcountry,
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
    }
    
    function load_view(url, tab) {       
        //Enabling loader
        $('#loading_image').show();
        //Loading view with Ajax data
        $(tab).html("<center>  <img style='width: 46px;' src='<?php echo base_url(); ?>images/loader.gif'/> </center>");
       
        $.ajax({
            type: "POST",
            url: url,
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
    
    
    function load_view_send_to_partner(url, tab){
    
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
        
       // load_view('service_center/spare_parts/0/1', '#tabs-2');
        
        $("#datatable1_filter").hide();
        
        <?php $data = array('spare_parts_details.partner_id' => $this->session->userdata('service_center_id'), 'status' => SPARE_OOW_EST_REQUESTED ,'entity_type' => _247AROUND_SF_STRING);
        ?>
         <?php $column = array(NULL,NUll,NUll,"age_of_request", NULL,NULL, NULL, NULL, NULL);?>
         var column_order = <?php echo json_encode($column);?>;
         var obj = '<?php echo json_encode($data); ?>';
         var select = '<?php echo "spare_parts_details.parts_requested, spare_parts_details.model_number, spare_parts_details.serial_number, assigned_vendor_id, "
         . "amount_due, spare_parts_details.id, spare_parts_details.booking_id, defective_parts_pic, serial_number_pic,booking_details.partner_id ,inventory_master_list.part_number"; ?>';
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
                    "targets": [0,1,2,4,5,6,7,8,9], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
             $("#datatable1_filter").hide();
          }
            
        });
        
    });
    
    function update_spare_estimate_cost(spare_id, booking_id, assigned_vendor_id, amount_due,partner_id){
        var estimate_cost = $("#estimate_cost_" + spare_id).val();
      
        if(Number(estimate_cost) > 1){
            swal({
                title: "Do You Want To Continue?",
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
                        agent_id:'<?php echo $this->session->userdata('service_center_agent_id');?>', 
                        partner_id: partner_id, 'sp_id':spare_id, requestType:'UPDATE_OOW_EST', gst_rate: '<?php echo DEFAULT_TAX_RATE;?>'},
                    url: "<?php echo base_url() ?>apiDataRequest",
                    success: function (data) {

                        if(data === 'Success'){
                            oow_spare.ajax.reload(null, false);
                            swal("Thanks!", "Booking updated successfully!", "success");

                        } else {
                            swal("Oops", "There is something issues, Please Conatct 247Around Team", "error");

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
        var partner_id = $(this).data('partner_id');
        $('#modal-title').text("Reject Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#modal_partner_id").val(partner_id);
        
    });
    
    function reject_parts(){
        var remarks =  $('#textarea').val();
        if(remarks !== ""){
            var url =  $('#url').val();
            var modal_partner_id =  $('#modal_partner_id').val();
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge:0,partner_id:modal_partner_id},
                success: function(data){
                    console.log(data);
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
</script>
<script>

    var inventory_spare_table;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        get_partner();
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            inventory_spare_table.ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_inventory_list(){
        inventory_spare_table = $('#inventory_spare_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10 ]
                    },
                    title: 'inventory_spare_table_'+time                    
                },
            ],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            "order": [],
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_spare_send_by_partner_to_wh",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.is_wh_ack = entity_details.is_wh_ack
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'sender_entity_id': $('#partner_id').val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'receiver_entity_id': '<?php echo $this->session->userdata('service_center_id')?>',
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'is_wh_ack':0
        };
        
        return data;
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    }
    
    $('#ack_all').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
            $(".check_reject_single_row").prop('checked', false);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });
    
    function process_acknowledge_spare(){
        var tmp_arr = {};
        var postData = {};
        var flag = false;
        $(".check_single_row:checked").each(function (key) {
            tmp_arr[key] = {};
            tmp_arr[key]['inventory_id'] = $(this).attr('data-inventory_id');
            tmp_arr[key]['quantity'] = $(this).attr('data-quantity');
            tmp_arr[key]['ledger_id'] = $(this).attr('data-ledger_id');
            tmp_arr[key]['part_name'] = $(this).attr('data-part_name');
            tmp_arr[key]['part_number'] = $(this).attr('data-part_number');
            tmp_arr[key]['booking_id'] = $(this).attr('data-booking_id');
            tmp_arr[key]['invoice_id'] = $(this).attr('data-invoice_id');
            tmp_arr[key]['is_wh_micro'] = $(this).attr('data-is_wh_micro');
            flag = true;
        });
        
        postData['data'] = JSON.stringify(tmp_arr);
        postData['sender_entity_id'] =  $('#partner_id').val();
        postData['sender_entity_type'] = '<?php echo _247AROUND_PARTNER_STRING; ?>';
        postData['receiver_entity_id'] = '<?php echo $this->session->userdata('service_center_id')?>';
        postData['receiver_entity_type'] = '<?php echo _247AROUND_SF_STRING; ?>';
        postData['sender_entity_name'] = $('#partner_id option:selected').text();
        postData['receiver_entity_name'] = '<?php echo $this->session->userdata('wh_name')?>';
        
        if(flag){
            $('#ack_spare').html("<i class='fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/process_acknowledge_spare_send_by_partner_to_wh',
                data:postData,
                success:function(response){
                    $('#ack_spare').html("Acknowledge spare received").attr('disabled',false);
                    obj = JSON.parse(response);
                    if(obj.status){
                        $('.success_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                        $('#success_msg').html(obj.message);
                        inventory_spare_table.ajax.reload();
                    }else{
                        $('.error_msg_div').fadeTo(2000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                        $('#error_msg').html(obj.message);
                    }
                }
            });
        }else{
            alert("Please Select At Least One Checkbox");
        }
    }

    function get_msl_awb_details(courier_code,awb_number,status,id){

            if(courier_code && awb_number && status){
                $('#'+id).show();
                $.ajax({
                    method:"POST",
                    data : {courier_code: courier_code, awb_number: awb_number, status: status},
                    url:'<?php echo base_url(); ?>courier_tracking/get_msl_awb_real_time_tracking_details',
                    success: function(res){
                        $('#'+id).hide();
                        $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                        $('#gen_model_body').html(res);
                        $('#gen_model').modal('toggle');
                    }
                });
            }else{
                alert('Something Wrong. Please Refresh Page...');
            }
    }
    
              
    $('#reject_all').on('click', function () {
         if($(this).is(':checked', true)){
             $(".check_reject_single_row").prop('checked', true);
             $(".check_single_row").prop('checked', false);
         }else{
             $(".check_reject_single_row").prop('checked', false);
         }
     });
     
     function process_to_reject_spare(){
         var reject_spare_arr = {};
          var flag = false;
        $(".check_reject_single_row:checked").each(function(indexId){
            reject_spare_arr[indexId] = {};
            reject_spare_arr[indexId]['inventory_id'] = $(this).attr('data-inventory_id');
            reject_spare_arr[indexId]['quantity'] = $(this).attr('data-quantity');
            reject_spare_arr[indexId]['ledger_id'] = $(this).attr('data-ledger_id');
            reject_spare_arr[indexId]['part_name'] = $(this).attr('data-part_name');
            reject_spare_arr[indexId]['part_number'] = $(this).attr('data-part_number');
            reject_spare_arr[indexId]['booking_id'] = $(this).attr('data-booking_id');
            reject_spare_arr[indexId]['invoice_id'] = $(this).attr('data-invoice_id');
            reject_spare_arr[indexId]['is_wh_micro'] = $(this).attr('data-is_wh_micro');
            flag = true;
        });
        
        var formData = new FormData(); 
            formData.append('spares_data', JSON.stringify(reject_spare_arr));
            formData.append('sender_entity_id', $('#partner_id').val());
            formData.append('sender_entity_type', '<?php echo _247AROUND_PARTNER_STRING; ?>');
            
            
            
        if(flag){     
             
            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/spare_parts/send_rejected_spare_to_partner',
                data:formData,
                contentType: false,
                processData: false,
                success:function(response){
                    inventory_spare_table.ajax.reload();
                    obj = JSON.parse(response);                    
                    if(obj.status){                          
                        $('#reject_err').html("Spart Parts Successfully Rejected").css({'color':'#fff','background':'#85c35e','font-weight':'bold','padding': '5px'});
                    }else{
                        $('#reject_err').html("Spart Parts Not Rejected").css({'color':'#fff','background':'#e06464','font-weight':'bold','padding': '5px'});
                    }
                }
            });
            
           // $("#reject_spare_modal").modal(); 
        } else {
           alert("Please Select At Least One Checkbox");  
        }
        
     }
</script>