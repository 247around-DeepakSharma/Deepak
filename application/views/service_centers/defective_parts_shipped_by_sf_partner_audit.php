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
            <button class="btn btn-success pull-right" id="revieve_multiple_parts_btn_partner">Receive Multiple Parts</button>
            <h2>Defective/Ok Parts Received by WH - Partner Audit</h2>
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
                            <th class="text-center">Spare Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">SF Name</th>
                            <th class="text-center">SF City</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Shipped Quantity</th>
                            <th class="text-center">Parts Code</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center" style="width: 15% !important;">Remarks</th>
                            <th class="text-center">Consumption</th>
                            <th class="text-center">Consumption Reason</th>                           
                            <th class="text-center">Received</th>                            
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
                url: "<?php echo base_url(); ?>employee/service_centers/get_defective_parts_shipped_by_sf_list_partner_audit",
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
$("#revieve_multiple_parts_btn_partner").click(function(){
$("#revieve_multiple_parts_btn_partner").attr('disabled',true);
$(".recieve_defective").attr('disabled',true);

var spare_id_array = [];
var flag = false;
$('.checkbox_revieve_class').each(function () {
    if (this.checked) { 
        spare_id_array.push($(this).attr("data-spare-id"));
        flag=true;
    }
});
if(flag) {
    $('.checkbox_revieve_class').prop('checked', false); 
		receive_partner_audit_defective_part(spare_id_array);
}
else {
    alert("Please Select At Least One Checkbox");
    $("#revieve_multiple_parts_btn_partner").attr('disabled',false);
    $(".recieve_defective").attr('disabled',false);
}
});

    function open_spare_consumption_model(id, booking_id, spare_id) {    
		 var spare_id_array = [];
		 spare_id_array.push(spare_id);
		 receive_partner_audit_defective_part(spare_id_array);
    }

	function receive_partner_audit_defective_part(spare_id_array){
		if(confirm('Are you sure to continue?')){
			

			 $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/acknowledge_received_defective_parts_partner_audit',
            data: {spare_id_array:spare_id_array},
			beforeSend: function(){
				$(".loader").css("display","block !important");
			},
            success: function (data) {
				swal("Received!", "Your all selected spares are received !.", "success");
				$(".loader").css("display","none");
				location.reload();
            }
        });

		}else{
			$("#revieve_multiple_parts_btn_partner").attr('disabled',false);
			$(".recieve_defective").attr('disabled',false);
		}
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