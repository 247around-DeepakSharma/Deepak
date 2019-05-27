<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">

    <div class="row">
<?php } ?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2> Spares Send to SF   </h2>
              
                    <div class="clearfix"></div>
                    
                </div>
               
                <div class="x_content">
   
                        <table id="inventory_spare_table" class="table table-bordered table-responsive" style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Booking ID</th>
                                                                    <th>Appliance</th>
                                                                    <th>Invoice ID</th>
                                                                    <th>Service Center</th>
                                                                    <th>Spare Type</th>
                                                                    <th>Spare Part Name</th>
                                                                    <th>Spare Part Number</th>
                                                                    <th>Spare Quantity</th>
                                                                    <th>Desciption</th>
                                                                    <th>Courier Name</th>
                                                                    <th>Courier AWB Number</th>
                                                                    <th>
                                                                        Acknowledge
                                                                       
                                                                    </th>
                                                                   
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>

                 
                </div>
            </div>
        </div>
    </div>

</div>

<div class="clearfix"></div>
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
<script>

    var oow_spare;
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
    


</script>
<script>

    var inventory_spare_table;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        get_inventory_list();
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
            "pageLength": 50,
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_send_by_partner_to_wh",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    console.log(entity_details);
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.receiver_entity_type = entity_details.receiver_entity_type
                    //d.is_wh_ack = entity_details.is_wh_ack
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details(){
        var data = {
            'sender_entity_id': '<?php echo $this->session->userdata('partner_id')?>',
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>'
        };
        
        return data;
    }
    



    function get_msl_awb_details(courier_code,awb_number,status='pick-up',id){

            if(courier_code && awb_number){
               // $('#'+id).show();
                $.ajax({
                    method:"POST",
                    data : {courier_code: courier_code, awb_number: awb_number, status: status},
                    url:'<?php echo base_url(); ?>courier_tracking/get_msl_awb_real_time_tracking_details',
                    success: function(res){
                     //   $('#'+id).hide();
                        $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                        $('#gen_model_body').html(res);
                        $('#gen_model').modal('toggle');
                    }
                });
            }else{
                alert('Something Wrong. Please Refresh Page...');
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