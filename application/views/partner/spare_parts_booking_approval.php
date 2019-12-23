<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
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
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('error') . '</strong>
                        </div>';
        }
        ?>
    <div class="row">
<?php } ?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Pending Spares On Approval</h2>
   
                    <div class="clearfix"></div>
                    
                </div>
                <input type="text" id="booking_id_search_spare" onchange="booking_search_spare()" style="float: right;margin-bottom: -32px;border: 1px solid #ccc;padding: 5px;z-index: 100;position: inherit;" placeholder="Search">
                <div class="x_content">
                    <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                        <table class="table table-bordered table-hover table-striped" id="spare_table" style=" z-index: -1;position: static;">
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Appliance</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Part Request Age(Days)</th>
                                    <th class="text-center">Required Parts</th>
                                    <th class="text-center">Parts Number</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">State</th>
                                    <th class="text-center">Problem Description</th>
                                    <th class="text-center">Action</th>
                                    
                                   
                                    <th data-sortable="false" class="text-center">Approve</th>
                                </tr>
                            </thead>
                        </table>
                        
                    </form>
                </div>
            </div>
        </div>
<?php if(empty($is_ajax)) { ?> 
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

                <input type="hidden" name="" value="<?php echo $this->session->userdata('partner_id'); ?>" id="modal_partner_id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_parts()">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>

<div id="myModal77" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width: 55%;">
    <!-- Modal content-->
    <div class="modal-content" >
         
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="appmodal-title">Approve Spare Part</h4>
            </div>
            <br>
                <div class="row">
                            <div class="col-md-6">
                                 <div class="form-group "id="email_ids">
                                    
                                    <label for="" class="col-md-4">Spare Status By SF</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="part_warranty_status" name="part_warranty_status" value=""><option selected="" disabled="">Select warranty status</option><option value="1" selected="selected"> In-Warranty </option><option value="2"> Out Of Warranty </option></select>
                                    </div>
                                </div>
                            <br>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    
                                    <label for="remarks_defective_part" class="col-md-4">Remarks *</label>
                                    <div class="col-md-6">
                                        <textarea type="text" class="form-control" id="apptextarea" name="remarks" placeholder="Please Enter Remarks" required=""></textarea>
                                    </div>
                                   </div>
                            </div>
                            <input type="hidden" id="appurl" value="">
                        </div>
                 <div class="modal-footer">
                <button type="submit" id="uploadButton" onclick="approve_parts();" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
        </div>
        

    </div>

  </div>
</div>
<div class="loader hide"></div>
 <style>
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>


<script>
    $(document).ready(function () {
        $('#state_search').select2();
        $('body').popover({
            selector: '[data-popover]',
            trigger: 'click hover',
            placement: 'auto',
            delay: {
                show: 50,
                hide: 100
            }
        });
        spare_table = $('#spare_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_spare_parts_booking_on_approval_table/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search_spare').val();
                    d.state =  $('#state_search_spare').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,5,6,7,8,9,10,11,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search_spare(){
             spare_table.ajax.reload();
        }
  
    $(document).on("click", ".approve_part", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
        var warranty = $(this).data('warranty');
         $('#apptextarea').val("");
        $('#appmodal-title').text("Approve Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#appurl").val(url);
        $('#part_warranty_status option[value='+warranty+']').attr('selected','selected');
       // alert();
        
    });


      $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
       
        $('#modal-title').text("Approve Part For Booking -" + booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        //$('#part_warranty_status option[value='+warranty+']').attr('selected','selected');
       // alert();
        
    });
    
    function reject_parts(){
         $(".loader").removeClass('hide');
        var remarks =  $('#textarea').val();
        if(remarks !== ""){
            var url =  $('#url').val();
            var partner_id =  $('#modal_partner_id').val();
       
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge:0,partner_id:partner_id},
                success: function(data){
                     $(".loader").addClass('hide');
                    if(data === "Success"){
                        //  $("#"+booking_id+"_1").hide()
                        $('#myModal2').modal('hide');
                        $(".close").click();
                                 swal({title: "Cancelled !", text: "Your Spare  is  Cancelled .", type: "success"},
                                    function(){ 
                                    spare_table.ajax.reload(null, false);
                                    });
                       // location.reload();
                    } else {
                        alert("Spare Parts Cancellation Failed!");
                    }
                }
            });
        } else {
            alert("Please Enter Remarks");
        }
    }
 
 
    function approve_parts(){
        $(".loader").removeClass('hide');
        var remarks =  $('#apptextarea').val();
        if(remarks !== ""){
            var url =  $('#appurl').val();
            var partner_id =  $('#modal_partner_id').val();
            var part_warranty_status =  $('#part_warranty_status').val();
       
            $.ajax({
                type:'POST',
                url:url,
                data:{remarks:remarks,courier_charge:0,partner_id:partner_id,part_warranty_status:part_warranty_status},
                success: function(data){
                 var obj = JSON.parse(data);
                    if(obj['status']){
                        //  $("#"+booking_id+"_1").hide()
                        $('#myModal77').modal('hide');
                        $(".loader").addClass('hide');

                        $(".close").click();
                                 swal({title: "Approved !", text: "Your Spare  is  approved .", type: "success"},
                                    function(){ 
                                    spare_table.ajax.reload(null, false);
                                    });
                         

                       // location.reload();
                    } else {
                        $(".loader").addClass('hide');
                        alert("Spare Parts Cancellation Failed!");
                    }
                }
            });
        } else {
            alert("Please Enter Remarks");
        }
    }
 
 
 

    </script>
    <style>
.dropdown-backdrop{
    display: none;
}
.table tr td:nth-child(10) {
    text-align: center;
}
.table tr td:nth-child(12) {
    text-align: center;
}
.table tr td:nth-child(13) {
    text-align: center;
}
.table tr td:nth-child(14) {
    text-align: center;
}
#spare_table_filter{
      display: none;
}
#spare_table_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>
        
        <?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>