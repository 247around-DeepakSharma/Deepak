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
    <div class="row">
<?php } ?>
        <style>
            .dataTables_length{
        width: 250px;
        float: left
    }
    .dataTables_filter{
        float: right;
    }
    .table.dataTable thead .sorting:after {
        opacity: 1;            
    }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        display: none;
        width: 100%;
        height: 100%;
        z-index: 9999999;
        background: url('<?php echo base_url(); ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
    }
    .rejected_by_wh{
        color:red;
    }
        </style>
        <div class="loader"></div>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <button class="btn btn-success pull-right" id="revieve_multiple_parts_btn">Receive Multiple Parts</button>
            <h2>Defective/Ok Parts Shipped By SF</h2>
            <div class="clearfix"></div>

            
        </div>
        <hr>
        <div class="x_content">
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="defective_spare_shipped_by_sf">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">SF Name</th>
                            <th class="text-center">SF City</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Shipped Quantity</th>
                            <th class="text-center">Parts Code</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Consumption</th>
                            <th class="text-center">Consumption Reason</th>                            
                            <th class="text-center">Received</th>
                            <th class="text-center">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                         
                    </tbody>
                </table>
        </div>
    </div>
</div>

<input type="hidden" name="multiple_received_part_consumption_data" id="multiple_received_part_consumption_data" value="">        
        
<!-- Wrong spare parts modal -->
<div id="SpareConsumptionModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="spare_consumption_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Consumption Reason</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<!-- Reject spare parts modal -->
<div id="RejectSpareConsumptionModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="reject_spare_consumption_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reject Defective Part</h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>

    <style>

    @media screen and (min-width: 768px) {

        .modal-dialog {

          width: 700px; /* New width for default modal */
          height: 700px;
        }

        .modal-sm {

          width: 350px; /* New width for small modal */
          height: 400px;
        }

    }

    @media screen and (min-width: 992px) {

        .modal-lg {

          width: 950px; /* New width for large modal */
          height: 420px;
        }

    }
    
    div.dt-buttons {
        float: left;
        margin-top: 35px;
        margin-bottom: 5px;
        margin-left: 5px;
    }

</style>

        <script>
 
    $(document).ready(function () {
        get_defective_spare_shipped_by_sf();
    });



  function get_defective_spare_shipped_by_sf(){
        inventory_spare_table = $('#defective_spare_shipped_by_sf').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found",
                "searchPlaceholder": "Search by BookingID / AWB"
            },
            "order": [],
            "pageLength": 25,
            dom: 'Blfrtip',
            lengthMenu: [[ 25, 50, 100, -1 ],[ '25', '50', '100', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9,10,11]
                    },
                    title: 'defective_parts_shipped_by_sf'
                }
            ],
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/service_centers/get_defective_parts_shipped_by_sf_list",
                type: "POST",
                data: function(d){
                    
                    // var entity_details = get_entity_details();
                    // d.sender_entity_id = entity_details.sender_entity_id,
                    // d.sender_entity_type = entity_details.sender_entity_type,
                    // d.receiver_entity_id = entity_details.receiver_entity_id,
                    // d.receiver_entity_type = entity_details.receiver_entity_type,
                    // d.is_wh_ack = entity_details.is_wh_ack,
                    // d.is_wh_micro = entity_details.is_wh_micro
                }
            },
            "deferRender": true
        });
    }
        </script>
<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script type="text/javascript">
function confirm_received(id){

}


$('.close').on('click', function(data) {
    $("#revieve_multiple_parts_btn").attr('disabled',false);
    $(".recieve_defective").attr('disabled',false);
});

$("#revieve_multiple_parts_btn").click(function(){
    
    
});


$("#revieve_multiple_parts_btn").click(function(){
$("#revieve_multiple_parts_btn").attr('disabled',true);
$(".recieve_defective").attr('disabled',true);
$(".loader").css("display","block !important");
var flag=false;
var url = new Array();
var spare_id_array = [];
var consumption_status = new Array();
$('.checkbox_revieve_class').each(function () {
    if (this.checked) { 
        url.push($(this).attr("data-url"));
        spare_id_array.push($(this).attr("data-spare-id"));
        consumption_status.push($(this).attr("data-consumption_status"));
        flag=true;
    }
});

var docket_uniqu_detail = new Array();
$(".checkbox_revieve_class").each(function() {
    if (this.checked) { 
        docket_uniqu_detail.push($(this).data("docket_number"));
    }
});

if(flag) {
    $('.checkbox_revieve_class').prop('checked', false);
    var count_consumption_status_type = jQuery.unique(consumption_status).length;
    if(count_consumption_status_type > 1) {
        alert('Please select part having same consumption reason.');
        $("#revieve_multiple_parts_btn").attr('disabled',false);
        $(".recieve_defective").attr('disabled',false);
        return false;
    } 
    
//    var dockket_count = jQuery.unique(docket_uniqu_detail).length;
//    if(dockket_count > 1) {
//        alert('Please select unique docket number to acknowledge spare.');
//        $("#revieve_multiple_parts_btn").attr('disabled',false);
//        $(".recieve_defective").attr('disabled',false);
//        return false;
//    } 
    
    var consumption_status_selected = jQuery.unique(consumption_status)[0];
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/service_centers/change_multiple_consumption',
        data: {status_selected:consumption_status_selected},
        success: function (data) {
            $("#spare_consumption_model").children('.modal-content').children('.modal-body').html(data);   
            $('#SpareConsumptionModal').modal({backdrop: 'static', keyboard: false});
        }
    });
    
    /*
     * @js: It's use to received multiple defective send by SF.
     */
    $(document).on('click',".change-consumption-multiple_precheck", function(e) {
        
        var multipleconsumptionremarks = $("#multiple-consumption-remarks").val();
        multipleconsumptionremarks = multipleconsumptionremarks.trim();
        var validation = true;
        if(multipleconsumptionremarks=='' || multipleconsumptionremarks==null){
            alert('Please enter remarks.');
            validation = false;
            return false;
            
        }
        var weight_in_kg = $("#defective_parts_shipped_weight_in_kg").val();
        var weight_in_gram = $("#defective_parts_shipped_weight_in_gram").val();

        if(parseInt(weight_in_kg) < 0){
            $("#defective_parts_shipped_weight_in_kg").val('');
            alert("Please Enter valid Weight in KG.");
            return false;
        }

        if(parseInt(weight_in_gram) < 0){
            $("#defective_parts_shipped_weight_in_gram").val('');
            alert("Please Enter valid Weight in Gram.");
            return false;
        }
        if(validation){
        $(".change-consumption-multiple_precheck").attr('disabled',true);
        $(".change-consumption-multiple_precheck").val('Submitting...');
        $("#multiple_loader_gif").css('display','block');
        $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/service_centers/check_part_alredy_acknowledge',
        data: {spare_ids_to_check:spare_id_array},
        success: function (data) {
           data = JSON.parse(data);
           if(data['status']=='error'){
               alert(data['message']);
               window.location.href = window.location.href;
           }else{
             $('.change-consumption-multiple').trigger('click');
             $(".change-consumption-multiple_precheck").attr('disabled',false);
             $(".change-consumption-multiple_precheck").val('Submitting...');
        }
        }
        });
        }
    });
    $(document).on('click',".change-consumption-multiple", function(e) {
       
        //Declaring new Form Data Instance  
        var formData = new FormData();
        //Getting Files Collection
        var files = $("#received_defective_part_pic_by_wh")[0].files;
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < files.length; i++) {
           formData.append('received_defective_part_pic_by_wh', files[i]);
        }
        
        formData.append('received_defective_part_pic_by_wh_exist', $("#received_defective_part_pic_by_wh_exist").val());
        formData.append('defective_parts_shipped_kg', $("#defective_parts_shipped_weight_in_kg").val());
        formData.append('defective_parts_shipped_gram', $("#defective_parts_shipped_weight_in_gram").val());
        
        $('#multiple_received_part_consumption_data').val(JSON.stringify({consumed_status_id:$('#spare_consumption_status').val(), remarks:$('#multiple-consumption-remarks').val()}));
        
        formData.append('consumption_data', $('#multiple_received_part_consumption_data').val());
        /*
        if($("#received_defective_part_pic_by_wh").val() == '' || $("#received_defective_part_pic_by_wh").val() == null) {
            e.stopImmediatePropagation(); 
            alert('Please choose defective image.');
            return false;
        }
        */
        var weight_in_kg = $("#defective_parts_shipped_weight_in_kg").val();
        var weight_in_gram = $("#defective_parts_shipped_weight_in_gram").val();

        if(parseInt(weight_in_kg) < 0){
            $("#defective_parts_shipped_weight_in_kg").val('');
            alert("Please Enter valid Weight in KG.");
            $("#multiple_received").attr('disabled',false);
            return false;
        }

        if(parseInt(weight_in_gram) < 0){
            $("#defective_parts_shipped_weight_in_gram").val('');
            $("#multiple_received").attr('disabled',false);
            alert("Please Enter valid Weight in Gram.");
            return false;
        }
        
        if($('#multiple-consumption-remarks').val() == '' || $('#multiple-consumption-remarks').val() == null) {
            e.stopImmediatePropagation(); // to prevent multiple alerts
            e.preventDefault();
            alert('Please enter remarks.');
            return false;
        }
        $("#multiple_received").attr('disabled',true);
        $("#multiple_loader_gif").css('display','block');
        $('#multiple_received_part_consumption_data').val(JSON.stringify({consumed_status_id:$('#spare_consumption_status').val(), remarks:$('#multiple-consumption-remarks').val()}))
        $('#SpareConsumptionModal').modal('hide');
    
        var send_email_for_part = 0;
        var from = to = cc = bcc = subject = email_body = template = booking_id = "";
        for (var index in url){
            $.ajax({
                type: "POST",
                url: url[index],
                data:formData,
                async:false,  
                contentType: false,
                processData: false,
                success: function(data){
                    data = JSON.parse(data);
                    var email_data = data[1];
                  $("#multiple_received").attr('disabled',false); 
                  $("#multiple_loader_gif").css('display','none');
                    console.log("Receiving");
                    if(email_data.length > 0){
                        send_email_for_part = 1;
                        from = email_data[0];
                        to = email_data[1];
                        cc = email_data[2];
                        bcc = email_data[3];
                        subject = email_data[4];
                        email_body = email_body + email_data[5] + "</br></br>";
                        template = email_data[7];
                        booking_id = booking_id + email_data[9] + ",";
                        console.log(booking_id);
                    }
                    
                }
            });
        }
        if(send_email_for_part == 1){
            booking_id = booking_id.replace(/,+$/, '');
            //send email after warehouse acknowledges receiving part from SF
            send_email(from, to, cc, bcc, subject, email_body, template, booking_id);
        }
        
        
        function send_email(from, to, cc, bcc, subject, email_body, template, booking_id){
            $.ajax({
                     type:'POST',
                     url: "<?php echo base_url(); ?>employee/service_centers/send_email_acknowledge_received_defective_parts",
                     data:{"from": from, "to": to, "cc": cc, "bcc": bcc, "subject" : subject,"email_body" : email_body, "template" : template,"booking_id" : booking_id},
                     success:function(data){
                     },
                     error: function(data){
                         console.log("error_while_sending_email");
                         console.log(data);
                     }
                 });
         }
    
     swal("Received!", "Your all selected spares are received !.", "success");
     $(".loader").css("display","none");
     location.reload();
    });    


}
else {
    alert("Please Select At Least One Checkbox");
    $("#revieve_multiple_parts_btn").attr('disabled',false);
    $(".recieve_defective").attr('disabled',false);
}


});


function get_awb_details(courier_code,awb_number,status,id){
        if(courier_code && awb_number && status){
            $('#'+id).show();
            $.ajax({
                method:"POST",
                data : {courier_code: courier_code, awb_number: awb_number, status: status},
                url:'<?php echo base_url(); ?>courier_tracking/get_real_time_courier_tracking_using_rapidapi',
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

    function open_spare_consumption_model(id, booking_id, spare_id) {
    
        $("#"+id).attr('disabled',true);
        var c = confirm("Continue?");
        if(!c) {
            $("#"+id).attr('disabled',false);
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/change_consumption',
            data: {spare_part_detail_id:spare_id, booking_id:booking_id},
            success: function (data) {
                $("#spare_consumption_model").children('.modal-content').children('.modal-body').html(data);   
                $('#SpareConsumptionModal').modal({backdrop: 'static', keyboard: false});
            }
        });
        
        $("#"+id).attr('disabled',false);
    }

    function open_reject_spare_consumption_model(id, booking_id, spare_id) {
    
        $("#"+id).attr('disabled',true);
        var c = confirm("Continue?");
        if(!c) {
            $("#"+id).attr('disabled',false);
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/reject_spare_part',
            data: {spare_part_detail_id:spare_id, booking_id:booking_id},
            success: function (data) {
                $("#reject_spare_consumption_model").children('.modal-content').children('.modal-body').html(data);   
                $('#RejectSpareConsumptionModal').modal({backdrop: 'static', keyboard: false});
            }
        });
        
        $("#"+id).attr('disabled',false);
    }

</script>
