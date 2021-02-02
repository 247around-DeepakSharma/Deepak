<div id="page-wrapper" >
        <div role="tabpanel" class="tab-pane" id="oow_estimate_given">
            <div class="container-fluid">
                <div class="row" >
                    <div class="col-md-12">
                        
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                             <div class="panel-heading">
                                <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> OOW Bookings </h2>
                             </div>
                            <div class="panel-body">
                                <table id="datatable1" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Booking ID</th>
                                            <th>Part</th>
                                            <th>Model No</th>
                                            <th>Serial No</th>
                                            <th>Age Of Request</th>
                                            <th>Estimate Cost</th>
                                            <th>Submit</th>
                                            <th>Reject</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                    <h4 class="modal-title" id="modal-title">Reject Spare</h4>
                </div>
                <div class="modal-body">
                    <textarea rows="4" class="form-control" id="textarea"></textarea>
                </div>
                <input type="hidden" id="id_no" />
                <input type="hidden" id="booking_id" />
                <input type="hidden" id="service_center_id" />
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_oow()">Reject</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
          //  $("#datatable1_filter").hide();
            
            <?php $data = array( 'status' => SPARE_OOW_EST_REQUESTED);
        ?>
             <?php $column = array(NULL,NUll,NULL,NULL, NULL, "age_of_request", NULL, NULL);?>
             var column_order = <?php echo json_encode($column);?>;
             var obj = '<?php echo json_encode($data); ?>';
             var select = '<?php echo "spare_parts_details.parts_requested, spare_parts_details.model_number, spare_parts_details.serial_number, spare_parts_details.id,"
        . "booking_details.booking_id, booking_details.partner_id, assigned_vendor_id, amount_due, inventory_master_list.part_number"; ?>';
             oow_spare = $('#datatable1').DataTable({
                "processing": true, //Feature control the processing indicator.
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                 "searchable":true,
                "order": [], //Initial no order.
                "pageLength": 50,
                "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
                dom: 'lBfrtip',
                buttons: [
                   {
                      extend: 'excel',
                       text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                       pageSize: 'LEGAL',
                       title: 'OOW-bookings',
                       exportOptions: {
                         columns: [1,2,3,4,5],
                           modifier : {
                                // DataTables core
                                order : 'index',  // 'current', 'applied', 'index',  'original'
                                page : 'All',      // 'all',     'current' search : 'none'   
                            }
                       }

                   }
               ],
                "ajax": {
                    type: "POST",
              
                  data : {requestType:'<?php echo SPARE_OOW_EST_REQUESTED;?>', 'crmType': 'Admin',
                      'select':select,'where':obj, column_order:column_order},
                  url: "<?php echo base_url();?>apiDataRequest"
        
                },
                
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0,1,2,3,4,6,7,8], //first column / numbering column
                        "orderable": false //set not orderable
                    }
                ],
               "fnInitComplete": function (oSettings, response) {
               //  $("#datatable1_filter").hide();
              }
                
            });
            
        });
        
        function update_spare_estimate_cost(spare_id, booking_id, assigned_vendor_id, amount_due){
           var estimate_cost = $("#estimate_cost_"+spare_id).val();
          
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
                data:{'estimate_cost':estimate_cost, booking_id:booking_id, assigned_vendor_id:assigned_vendor_id,amount_due:amount_due, 
                agent_id:'<?php echo $this->session->userdata('id');?>', 
                partner_id: '<?php echo _247AROUND; ?>', 'sp_id':spare_id,requestType:'UPDATE_OOW_EST', gst_rate: '<?php echo DEFAULT_TAX_RATE;?>'},
                url: "<?php echo base_url() ?>apiDataRequest",
                success: function (data) {
                    if(data.includes('Success')){
                        oow_spare.ajax.reload(null, false);
                        swal("Thanks!", "Booking updated successfully!", "success");
                   
                    } else {
                        swal("Oops", "There is some problem, please contact Admin", "error");                        
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
        
        function reject_oow(){
            var spare_id=  $("#id_no").val();
            var booking_id = $("#booking_id").val();
            var assigned_vendor_id = $("#service_center_id").val();
            var remarks =  $('#textarea').val();
            if(spare_id !== "" && booking_id !=="" && assigned_vendor_id !== ""){
              
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
                data:{remarks:remarks},
                url: "<?php echo base_url() ?>employee/inventory/update_action_on_spare_parts/"+spare_id+"/"+booking_id+"/QUOTE_REQUEST_REJECTED",
                success: function (data) {
                    
                    if(data === 'Success'){
                        oow_spare.ajax.reload(null, false);
                        $("#myModal2").modal('toggle');
                        $("#id_no").val("");
                        $("#booking_id").val("");
                        $("#service_center_id").val("");
                        swal("Thanks!", "Booking updated successfully!", "success");
                   
                    } else {
                        swal("Oops", "There is some problem, please contact backoffice Team", "error");
                        
                    }
                    $('body').loadingModal('destroy');
                }
              });
              
            } else {
            swal("Oops", "Please Refresh Page! Try Again Later", "error");
            }
        }
        
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
        
        function reject_oow_model(spare_id, booking_id, assigned_vendor_id, amount_due){
            $("#id_no").val(spare_id);
            $("#booking_id").val(booking_id);
            $("#service_center_id").val(assigned_vendor_id);
            $("#myModal2").modal('toggle');
        }
        
        $('#myModal2').on('hidden.bs.modal', function(e) { 
             $("#id_no").val("");
             $("#booking_id").val("");
             $("#service_center_id").val("");
        }) ;
       
        
</script>
<style>
    .dataTables_filter{
            float: right;

    }
    </style>