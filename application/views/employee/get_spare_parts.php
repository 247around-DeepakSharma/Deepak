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
       <div class="col-md-12">
           <div class="panel panel-default">
               <div class="panel-heading">
                   <div class="row">
                       <div class="col-md-6">
                           <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Spare Parts Booking </h2>
                       </div>
                       <div class="col-md-6">
                           <div class="pull-right">
                               <a class="btn btn-success" id="download_spare_list">Download</a><span class="badge" title="download all spare data except requested spare"><i class="fa fa-info"></i></span>
                           </div>
                       </div>
                   </div>
               </div>
            <div class="panel-body">
                <div role="tabpanel"> 
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist" >
                            <li role="presentation" class="active"><a href="#parts_requested_on_approval" aria-controls="parts_requested_on_approval" role="tab" data-toggle="tab">Parts Requested On Approval</a></li>
                            <li role="presentation" ><a href="#estimate_cost_requested" aria-controls="spare_parts_requested" role="tab" data-toggle="tab">Quote Requested</a></li>
                            <li role="presentation" ><a href="#estimate_cost_given" aria-controls="spare_parts_requested" role="tab" data-toggle="tab">Quote Given</a></li>
                            <li role="presentation" ><a href="#spare_parts_requested" aria-controls="spare_parts_requested" role="tab" data-toggle="tab">Parts Requested</a></li>
                            <li role="presentation"><a href="#oow_part_shipped" aria-controls="shipped" role="tab" data-toggle="tab">Partner Shipped Part(Pending on Approval)</a></li>
                            <li role="presentation"><a href="#shipped" aria-controls="shipped" role="tab" data-toggle="tab">Partner Shipped Part</a></li>
                            <li role="presentation"><a href="#delivered" aria-controls="delivered" role="tab" data-toggle="tab">SF Received Part</a></li>
                            <li role="presentation"><a href="#defective_part_pending" aria-controls="defective_part_pending" role="tab" data-toggle="tab">Defective Part Pending</a></li>
                            <li role="presentation"><a href="#defective_part_rejected_by_partner" aria-controls="defective_part_rejected_by_partner" role="tab" data-toggle="tab">Defective Part Rejected By Partner</a></li>
                            <li role="presentation"><a href="#defective_part_shipped_by_SF" aria-controls="defective_part_shipped_by_SF" role="tab" data-toggle="tab">Defective Part Shipped By SF</a></li>
                            <li role="presentation"><a href="#defective_part_shipped_by_SF_approved" aria-controls="defective_part_shipped_by_SF" role="tab" data-toggle="tab">Approved Defective Part By Admin</a></li>
                            
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
<!--     <div class="custom_pagination" style="margin-left: 16px;" > <?php //if(isset($links)){ echo $links;} ?></div>-->    
</div>
<script>
    $(document).ready(function() {
        spare_booking_on_tab();
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
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
         url_arr = url.split("/");   
         if(!isNaN(url_arr[8])){
             $("#reject_btn").html("Approve");             
             $("#reject_btn").attr("onclick","approve_spare_part()");
             var HTML = '<select class="form-control" id="part_warranty_status" name="part_warranty_status" value="">';
                 HTML+= '<option selected="" disabled="">Select warranty status</option>';
                 HTML+= '<option value="1"> In-Warranty </option>';
                 HTML+= '<option value="2"> Out-Warranty </option>';
                 HTML+= '</select>';
             $("#part_warranty_option").html(HTML).css({'padding-bottom':'20px'});
             $("#part_warranty_status option[value='"+url_arr[9]+"']").attr('selected','selected');
         }else{
            $("#reject_btn").html("Send");             
             $("#reject_btn").attr("onclick","reject_parts()");            
             $("#part_warranty_option").html('').css({'padding-bottom':'0px'}); 
         }
        $('#modal-title').text(booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        $button_text = $(this).text();
        if($button_text === "Approve Invoice"){
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
                    parts_requested_on_approval_table.ajax.reload(null, false);                   
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
      if(remarks !== ""){
        $('#reject_btn').attr('disabled',true);
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{remarks:remarks,courier_charge:courier_charge},
            success: function(data){
                $('#reject_btn').attr('disabled',false);
                if(data === "Success"){
                  //  $("#"+booking_id+"_1").hide()
                    $('#myModal2').modal('hide');
                    alert("Updated Successfully");
                    defective_part_shipped_by_sf_table.ajax.reload(null, false);
                    //location.reload();
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } else {
          alert("Please Enter Remarks");
      }
    }
    
    $('#download_spare_list').click(function(){
        $('#download_spare_list').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/inventory/download_spare_consolidated_data',
            contentType: false,
            cache: false,
            processData: false,
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
    });
</script>
