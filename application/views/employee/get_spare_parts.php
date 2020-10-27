<style>
    a.dt-button {
        position: relative;
        display: inline-block;
        box-sizing: border-box;
        margin-right: 0.333em;
        padding: 6px 21px;
        font-size: inherit;
        border: 1px solid #2e6da4;
        border-radius: 2px;
        cursor: pointer;
        color: #f9f9f9;
        white-space: nowrap;
        overflow: hidden;
        background-color: #337ab7;
        background-image: none;
    }
    a.dt-button:hover:not(.disabled),a.dt-button.active:not(.disabled) {
        border: 1px solid #2e6da4;
        background-color: #143958!important;
        background-image: none!important;
    }
    div.dt-button-background{
        position: inherit;
    }
   
</style>
<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert" style="margin-left: 50px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('error') . '</strong>
                            </div>';
                }

                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-left: 50px; margin-right: 120px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                ?>


                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="pull-right"><h2 class="panel-title">Consolidated Report</h2> </div> 
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="partner_id"  id="partner_id" required=""></select>
                            <p id="partner_err"></p>
                        </div>    
                        <div class="col-md-3">
                            <select class="form-control" name="service_centers_id"  id="service_centers_id" required=""></select>
                            <p id="service_centers_id_err"></p>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="spare_part_status"  id="spare_part_status" required=""></select>
                            <p id="service_centers_id_err"></p>
                        </div>
                        <div class="col-md-4">
                            <div class="pull-right">
                                <a class="btn btn-success"  href="#"  id="download_spare_list">Download</a><span class="badge" title="Download all spare data as per the applied filters"><i class="fa fa-info"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>         
                <div class="col-md-12">
                    <div class="panel panel-default">               
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-md-6">
                                    <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Spare Parts Booking </h2>
                                </div>                       
                                <div class="col-md-12">                                               
                                    <div class="pull-right">
                                        <a class="btn btn-info pickup" id="schedule_pickup" data-request="pickup_schedule" style="margin-right: 15px;">Pickup Schedule</a><span class="badge" title="Pickup Schedule"></span>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-primary pickup" id="request_pickup" data-request="pickup_request" style="margin-right: 20px;">Pickup Request</a><span class="badge" title="Pickup Request"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div role="tabpanel"> 
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs" role="tablist">                           
                                        <li role="presentation"><a href="#estimate_cost_requested" aria-controls="spare_parts_requested" class="spare_parts_tabs" role="tab" data-toggle="tab">Quote Requested<span id="total_req_quote"></span></a></li>
                                        <li role="presentation" ><a href="#estimate_cost_given" aria-controls="spare_parts_requested" class="spare_parts_tabs" role="tab" data-toggle="tab">Quote Given<span id="total_quote_given"></span></a></li>
                                        <li role="presentation" class="active"><a href="#spare_parts_requested" aria-controls="spare_parts_requested" class="spare_parts_tabs" role="tab" data-toggle="tab">Parts Requested <span id="total_unapprove"></span></a></li>
                                        <li role="presentation" ><a href="#spare_parts_requested_approved" aria-controls="spare_parts_requested" class="spare_parts_tabs" role="tab" data-toggle="tab">Parts Requested (Approved)<span id="total_approved_spare"></span></a></li>
                                        <li role="presentation" ><a href="#spare_parts_requested_rejected" aria-controls="spare_parts_requested" class="spare_parts_tabs" role="tab" data-toggle="tab">Parts Requested (Rejected)<span id="total_rejected_spare"></span></a></li>
                                        <li role="presentation"><a href="#oow_part_shipped" aria-controls="shipped" class="spare_parts_tabs" role="tab" data-toggle="tab">Partner Shipped Part(Pending on Approval)<span id="total_oow_shipped_part_pending"></span></a></li>
                                        <li role="presentation"><a href="#partner_shipped_part" aria-controls="shipped" class="spare_parts_tabs" role="tab" data-toggle="tab">Partner Shipped Part<span id="total_partner_shipped_part"></span></a></li>
                                        <li role="presentation"><a href="#sf_received_part" aria-controls="delivered" class="spare_parts_tabs" role="tab" data-toggle="tab">SF Received Part<span id="total_sf_received_part"></span></a></li>
                                        <li role="presentation"><a href="#total_parts_shipped_to_sf" aria-controls="total_partshipped_to_sf" class="spare_parts_tabs" role="tab" data-toggle="tab">Total Part Shipped To SF<span id="total_part_shipped_to_sf"></span></a></li>
                                        <li role="presentation" ><a href="#courier_lost_spare_parts" aria-controls="courier_lost_spare_parts" class="spare_parts_tabs" role="tab" data-toggle="tab">Courier Lost Approval<span id="total_courier_lost"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_pending" aria-controls="defective_part_pending" id="pending_defective_part" class="spare_parts_tabs" role="tab" data-toggle="tab">Defective Pending/Ok Part (All)<span id="total_all_defective"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_pending_oot" aria-controls="defective_part_pending_oot" id="defective_part_oot" class="spare_parts_tabs" role="tab" data-toggle="tab">Defective Pending/Ok Part (OOT)<span id="total_defective_oot"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_shipped_by_SF" aria-controls="defective_part_shipped_by_SF" role="tab" class="spare_parts_tabs" data-toggle="tab">Defective/Ok Part Shipped By SF ( Courier Audit )<span id="total_courier_audit"></span></a></li>
                                        <li role="presentation"><a href="#courier_approved_defective_parts" aria-controls="courier_approved_defective_parts" class="spare_parts_tabs" role="tab" data-toggle="tab"> In Transit (Courier Approved Defective Parts)<span id="total_in_transit"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_shipped_by_SF_approved" aria-controls="defective_part_shipped_by_SF" class="spare_parts_tabs" role="tab" data-toggle="tab">Defective/Ok To Be Received By Warehouse<span id="total_defective_received_by_wh"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_rejected_by_wh" aria-controls="defective_part_rejected_by_wh" class="spare_parts_tabs" role="tab" data-toggle="tab">Defective/Ok Part Rejected By Warehouse<span id="total_defective_rejected_by_wh"></span></a></li>
                                        <li role="presentation"><a href="#return_defective_parts_from_wh_to_partner" aria-controls="return_defective_parts_from_wh_to_partner" class="spare_parts_tabs" role="tab" data-toggle="tab"> Returned Defective Part From Warehouse To Partner<span id="total_defective_return_to_partner"></span></a></li>
                                        <li role="presentation"><a href="#defective_part_rejected_by_partner" aria-controls="defective_part_rejected_by_partner" class="spare_parts_tabs" role="tab" data-toggle="tab">Defective/Ok Part Rejected By Partner<span id="total_defective_rejected_partner"></span></a></li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="tab-content">
                                    <center style="margin-top:30px;"> <img style="width: 60px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                                </div>
                            </div>    
                        </div>
                    </div>

                </div>
            </div>
            <input type="hidden" id="reload_table_id" value="spare_parts_requested_table">
        <!--     <div class="custom_pagination" style="margin-left: 16px;" > <?php //if(isset($links)){ echo $links;}  ?></div>-->    
        </div>
    </div>
</div>
<!-- Pickup Request Modal --->
<div id="pickup_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form name="" id="spare_parts_pick_up">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal_title">Pick Request</h4> 
            </div>
            <div class="modal-body"> 
                <label>  Courier Name *</label>
                <select class="form-control" id="courier_name" name="courier_name" required>
                    <option selected="" disabled="" value="">Select Courier Name</option>
                    <?php foreach ($courier_details as $value1) { ?> 
                        <option value="<?php echo $value1['courier_code'] ?>"><?php echo $value1['courier_name'] ?></option>
                    <?php } ?>
                </select>
                <p id="courier_name_err"></p>
                <label> To Email *</label>
                <input type="text"  class="form-control" id="courier_to_email" name="courier_to_email" placeholder="To Email">
                <p id="courier_email_err"></p>
                 <label> CC Email </label>
                <input type="text"  class="form-control" id="courier_cc_email" name="courier_cc_email" placeholder="CC Email">  
                <p id="cc_email_err"></p>               
                <input type="hidden" id="request_type" name="request_type" value="">
                <input type="hidden" id="spare_parts_ids" name="spare_parts_ids" value=""> 
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-success" id="spare_pick_up"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
       </form>
    </div>
</div>
<!-- Pickup Request Modal --->

<script>
    $(document).ready(function() {
        $("#request_pickup").attr('disabled', true);
        $("#schedule_pickup").attr('disabled', true);        
        spare_booking_on_tab();
        get_partner_list();
        get_service_centers_list();
        get_spare_parts_status_list();
    });
    
    function spare_booking_on_tab(){
        $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/inventory/spare_part_booking_on_tab',
         success: function (data) {
          $("#tab-content").html(data);   
         }
       });
    }
    
    $(document).on('click', ".courier_lost", function () {
        if(confirm('Are you sure you want to mark this spare courier lost?')) 
        {
            $.ajax({
                type : 'POST',
                url : $(this).data('url')
            }).done(function (data) {
                alert('Data has been updated successfully.');
                load_table($("#reload_table_id").val());
            });
        }    
    });
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var booking_request_type = $(this).data('request_type');
        if(typeof booking_request_type === undefined){
            booking_request_type = 'NA';
        }
        var url = $(this).data('url');
        var keys = $(this).data('keys'); 
        var split_url = url.split('/');
        if(split_url[8]=='NOT_REQUIRED_PARTS' || split_url[8]=='NOT_REQUIRED_PARTS_FOR_COMPLETED_BOOKING'){
            button_txt = 'Move To Not Required';
        }else{
            button_txt = 'Move To Required';
        }
         if(!isNaN(keys)){              
             $("#reject_btn").html("Approve");             
             $("#reject_btn").attr("onclick","approve_spare_part()");
             booking_request_type = booking_request_type.toUpperCase();
             booking_request_type = booking_request_type.replace(/\s/g, '');
             var HTML = '<select class="form-control" id="part_warranty_status" name="part_warranty_status" value="">';
                 HTML+= '<option selected="" disabled="">Select warranty status</option>';
                 if(booking_request_type.indexOf("OUTOFWARRANTY") < 0)
                 {
                    HTML+= '<option value="1"> In-Warranty </option>';
                 }
                 HTML+= '<option value="2"> Out Of Warranty </option>';
                 HTML+= '</select>';
             $("#status_label").css({'display':'block'}).html("Spare Part Status By SF");
             $("#part_warranty_option").html(HTML).css({'padding-bottom':'20px','display':'block'});
             $("#part_warranty_status option[value='"+keys+"']").attr('selected','selected');
        }else if(keys == 'spare_parts_cancel'){            
            var HTML = ''; 
            HTML = '<select class="form-control" id="spare_cancel_reason" name="spare_cancel_reason" value=""></select>';
            $("#part_warranty_option").html(HTML).css({'padding-bottom':'20px','display':'block'}); 
            $("#reject_btn").html("Cancel");             
            $("#reject_btn").attr("onclick","reject_parts()");            
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_cancellation_reasons',
                    success: function (data) {
                        $("#status_label").css({'display':'block'}).html("Spare Cancel Reason *");
                        $("#spare_cancel_reason").html(data); 
                    }
                });
        }else{
            $("#reject_btn").html(button_txt);  
            $("#status_label").css({'display':'none'});
            $("#reject_btn").attr("onclick","reject_parts()");   
            var btntext = $(this).attr("data-button");
            console.log(btntext);
            $("#reject_btn").text(btntext);
            $("#part_warranty_option").css({'display':'none'});
        }
        $('#modal-title').text(booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        $button_text = $(this).text();
        if(btntext === "Approve Courier"){
            $("#charges").css("display","block");
             var charge = $(this).data('charge');
            $("#charges").val(charge);
        } else {
            $("#charges").css("display","none");
            $("#charges").val(0);
        }

    });
    
    function approve_spare_part(){
      var remarks =  $('#textarea').val();
      var warranty_status = $('#part_warranty_status').val();
      
      if(warranty_status==''){
          alert('Please Select Part Warranty Status');
          return false;
      }
      
      if(remarks==''){
          alert('Please Enter Remarks');
          return false;
      }
                 
      if(remarks !== "" && warranty_status !=''){
       $('#reject_btn').attr('disabled',true);
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{remarks:remarks,part_warranty_status:warranty_status},
            success: function(data){
                 var obj = JSON.parse(data); 
                $('#reject_btn').attr('disabled',false);
                if(obj['status']){
                  //  $("#"+booking_id+"_1").hide()
                    $("#reject_btn").html("Send");             
                    $("#reject_btn").attr("onclick","reject_parts()");
                    $('#myModal2').modal('hide');
                    alert("Approved Successfully");
                    spare_parts_requested_table.ajax.reload( function ( json ) { 
                      $("#total_unapprove").html('(<i>'+json.unapproved+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
                    },false );
                    
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } 
    }
    
    
    function reject_parts(){
      var remarks =  $('#textarea').val();
      //var booking_id = $('#modal-title').text();
      var courier_charge = $('#charges').val();
      var reason = $.trim($('#spare_cancel_reason option:selected').text());
      var cancel_id = $('#spare_cancel_reason option:selected').val();
      var table_type = $("#reload_table_id").val();

      if(($('#spare_cancel_reason').parent("div").css('display') !== 'none') && ($('#spare_cancel_reason').length === 1) && !($.isNumeric(cancel_id))) {
          alert("Please Enter Spare Cancellation Reason");
          return false;
      }
      
      if(remarks !== ""){
        $('#reject_btn').attr('disabled',true);
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{ remarks:remarks,courier_charge:courier_charge, spare_cancel_reason:reason, spare_cancel_id:cancel_id },
            success: function(data){
                $('#reject_btn').attr('disabled',false);
                if(data === "Success"){
                    $('#myModal2').modal('hide');
                    alert("Updated Successfully");
                    
                    /* Start Unapprove requested spare count */
                    spare_parts_requested_table.ajax.reload( function ( json ) { 
                        $("#total_unapprove").html('(<i>'+json.unapproved+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
                    },false );
                    /* End */
                    
                    load_table(table_type);
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } else {
          alert("Please Enter Remarks");
      }
    }
    
    function load_table(table_type){
        if(table_type=='estimate_cost_requested_table'){
          estimate_cost_requested_table.ajax.reload(null, false);  
        }else if(table_type=='estimate_cost_given_table'){
          estimate_cost_given_table.ajax.reload(null, false);  
        }else if(table_type=='spare_parts_requested_table'){
          spare_parts_requested_table.ajax.reload(null, false);  
        }else if(table_type=='oow_part_shipped_table'){
          oow_part_shipped_table.ajax.reload(null, false);  
        }else if(table_type=='partner_shipped_part_table'){
          partner_shipped_part_table.ajax.reload(null, false);  
        }else if(table_type=='sf_received_part_table'){
          sf_received_part_table.ajax.reload(null, false);  
        }else if(table_type=='defective_part_pending_table'){
          defective_part_pending_table.ajax.reload(null, false);  
        }else if(table_type=='defective_part_rejected_by_partner_table'){
          defective_part_rejected_by_partner_table.ajax.reload(null, false);  
        }else if(table_type=='defective_part_rejected_by_wh_table'){
          defective_part_rejected_by_wh_table.ajax.reload(null, false);  
        }else if(table_type=='defective_part_shipped_by_SF_table'){
          defective_part_shipped_by_sf_table.ajax.reload(null, false);  
        }else if(table_type=='defective_part_shipped_by_SF_approved_table'){
          defective_part_shipped_by_SF_approved_table.ajax.reload(null, false);  
        }else if(table_type=='spare_parts_requested_table_approved'){
          spare_parts_requested_table_approved.ajax.reload(null, false);  
        }else if(table_type=='spare_parts_requested_table_reject'){
           spare_parts_requested_table_reject.ajax.reload(null, false);  
        }else if(table_type=='courier_lost_spare_parts_table'){
           courier_lost_spare_parts_table.ajax.reload(null, false);  
        }
        
    }
    
      $('#download_spare_list').click(function(){
        var partner_id = $("#partner_id").val();
        var service_center_id = $("#service_centers_id").val();
        var spare_part_status = $("#spare_part_status").val();
        if(partner_id!=null && partner_id!='' || service_center_id !=null && service_center_id !=''){
            
            $("#partner_err").html('');
            $("#service_centers_id_err").html('');
            
            $('#download_spare_list').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/inventory/download_spare_consolidated_data',
                data: { partner_id : partner_id, service_center_id : service_center_id, spare_part_status:spare_part_status },
                success: function (data) {
                    $('#download_spare_list').html("Download").attr('disabled',false);
                    var obj = JSON.parse(data); 
                    if(obj['status']){
                        window.location.href = obj['msg'];
                    }else{
                        alert('File Download Failed. Please Refresh Page And Try Again...')
                    }
                }
            });
        }else{
        $("#service_centers_id_err").html("Please select Service Center.").css('color','red');
        $("#partner_err").html("Please select partner.").css('color','red');
        }
    });
    
   /*
    $("#partner_id").change(function(){
      var partner_id = $(this).val();
      $("#download_spare_list").attr("href","<?php //echo base_url(); ?>employee/partner/download_spare_part_shipped_by_partner/1/"+partner_id);

    });
    */
    
    $(".pickup").click(function(){
        $("#spare_pick_up").attr('disabled',false);  
        $("#courier_to_email").val('');
        $("#courier_cc_email").val('');
        $("#remarks").val('');  
        var request_type = $(this).data("request");
            class_name = request_type+':checked';
        var spare_ids_arr = [];
        $("."+class_name).each(function(i){
           spare_ids_arr[i] = $(this).val();
        });

        if(spare_ids_arr.length>0){
          if(request_type == 'pickup_request'){
             $("#modal_title").html('Pickup Request'); 
             $("#spare_pick_up").html("Request");
             $("#request_type").val('2');
          }else{
             $("#modal_title").html('Pickup Schedule'); 
             $("#spare_pick_up").html("Schedule");
             $("#request_type").val('3');
              var pickup_courier = $(".pickup_schedule:checkbox:checked").attr("pickup_courier");
              $('#courier_name option[value="'+pickup_courier+'"]').attr("selected", "selected");
          }
          
            $("#spare_parts_ids").val(spare_ids_arr);
            $("#pickup_modal").modal();
            
         }else{
             alert('At least one checkbox checked');
         }        
    });
    
    $("#pending_defective_part").click(function(){
        $("#request_pickup").attr('disabled', false);
        $("#schedule_pickup").attr('disabled', false); 
    });
    
    $(".spare_parts_tabs").click(function(){
        $("#request_pickup").attr('disabled', true);
        $("#schedule_pickup").attr('disabled', true); 
    });
    
    $("#spare_pick_up").click(function(){        
        var courier_name = $("#courier_name").val();
        var courier_email = $("#courier_to_email").val();
        var cc_email = $("#courier_cc_email").val();
                               
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;  
        if(courier_name =='' || courier_name == null){
            $("#courier_name_err").html('Please Select Courier Name.').css({color:'red'});
            return false;
        }else{
            $("#courier_name_err").html(''); 
        }
          
        if(!emailReg.test(courier_email) || courier_email ==''){
            $("#courier_email_err").html('Please Enter Valid Email.').css({color:'red'});
            return false;
        }else{
            $("#courier_email_err").html(''); 
        } 
        
        if(!emailReg.test(cc_email)){
            $("#cc_email_err").html('Please Enter Valid Email.').css({color:'red'});
            return false;
        }else{
            $("#cc_email_err").html(''); 
        } 
                                     
        if(courier_email !='') { 
           $("#spare_pick_up").attr('disabled',true);        
           $("#spare_pick_up").append(' <i class="fa fa-spinner fa-spin" style="font-size:20px;"></i>');
            $.ajax({
                url: '<?php echo base_url(); ?>employee/spare_parts/pick_up_spare_parts',
                type: 'POST',                
                cache: false,
                data: $('#spare_parts_pick_up').serialize(),
                success: function(data) {                    
                   if(data){
                       $('#pickup_modal').modal('hide');
                       defective_part_pending_table.ajax.reload(null, false);
                   } 
                }
            });
        } 
        
    });
    
    $(".spare_parts_tabs").on('click',function(){
         var href = $(this).attr('href');
             table_id  = href.replace('#','');
             $("#reload_table_id").val(table_id+'_table');
      });
    
    function get_partner_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/partner/get_partner_list',
            data:{is_wh:0,is_all_partner:1},
            success: function (response) {
                $("#partner_id").html(response);                
            }
        });
    }
    
    
    function get_service_centers_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_service_centers_list',
            data:{ is_micro_wh : 1 },
            success: function (response) {
                $("#service_centers_id").html(response);                
            }
        });
    }
    
    
    function get_spare_parts_status_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/spare_parts/get_spare_parts_status',
            data:{ is_active : 1 },
            success: function (response) {
                $("#spare_part_status").html(response);                
            }
        });
    }
    
    $('#partner_id').select2({
        placeholder:'Select Partner'
    });

    $('#service_centers_id').select2({
        placeholder:'Select Service Center'
    });
    
    $('#spare_part_status').select2({
        placeholder:'Select Spare Status'
    });
    function generate_sale_invoice(reverse_sale_id){
        var flag = true;
        var button_id = "btn_sell_invoice_"+reverse_sale_id;
        if(flag){
            var url = "<?php echo base_url(); ?>employee/invoice/generate_oow_parts_invoice/"+reverse_sale_id;
            var dashboard = "";
            $.ajax({
                 method:'POST',
                 dataType: "json",
                 url:url,
                 data: { return_response : true },
                 beforeSend: function(){
                     $("#"+button_id).html("Generate Sale Invoice... <i class='fa fa-spinner fa-spin' aria-hidden='true'></i>");
                 },
                 complete: function(data){
                     //data = data.trim();
                     if(data!=''){
                        alert('Invoice Generated Successfully');
                    }else{
                        alert('Invoice Not Generated');
                    }
                    $("#"+button_id).html("Generate Sale Invoice");
                 }
            });
        }
    }
   
</script>
<?php 
    if ($this->session->userdata('error')) {
        $this->session->unset_userdata('error');
    }
    if ($this->session->userdata('success')) {
        $this->session->unset_userdata('success');
    }
?>