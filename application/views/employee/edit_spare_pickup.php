<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>Update PickUp </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form method="POST" action="<?php echo base_url();?>employee/booking/download_booking_bulk_search_snapshot">
                        <div class="form-group">
                            <label for="model_number">PickUp From *</label>
                            <select class="form-control col-md-4" name="vendor_partner_type" id="vendor_partner_type">
                                <option value="" selected="" disabled>Select Pick From</option>
                                <option value="<?php echo _247AROUND_PARTNER_STRING?>">Partner</option>
                                <option value="<?php echo _247AROUND_SF_STRING;?>">Vendor</option>
                            </select>
                        </div>
                        <div  class="form-group" style="margin-top: 5%;">
                            <table id="spare_pickup_table" style="display: none" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th data-orderable="false">Booking Id</th>
                                        <th data-orderable="false">AWB Number</th>
                                        <th data-orderable="false">Courier Charges</th>
                                        <th data-orderable="false">Courier Company Name</th>
                                        <th data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
 
                                </tbody>   
                            </table>
                        </div>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   var pickup_table = null;
    $(document).ready(function () {
        $('#vendor_partner_type').change(function () {
            if(pickup_table == null){
                loadData();
            } else {
                pickup_table.ajax.reload(null, false);
            }
            
        });
    });
    function loadData(){
        var table = "";
        var select_type = document.getElementById("vendor_partner_type").value;
        $("#spare_pickup_table").show();
        if(select_type){
          pickup_table = $("#spare_pickup_table").DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: false, //Initial no order.
                pageLength: 50,
//                dom: 'Bfrtip',
//                buttons: [
//                    {
//                        extend: 'excelHtml5',
//                        text: 'Export',
//                        exportOptions: {
//                            columns: [ 1,2,3,4,5]
//                        },
//                        title: 'spare_parts_requested'
//                    }
//                ],
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                    type: "POST",
                    data: function(d){
                       d.type = "7";
                       d.vendor_partner = $("#vendor_partner_type").val();
                    }
                },
                //Set column definition initialisation properties.
                columnDefs: [
                    {
                        "targets": [0,1,2,3,4,5], //first column / numbering column
                        "orderable": false //set not orderable
                    }
                ],
                fnInitComplete: function (oSettings, response) {
                    $("#spare_pickup_table_filter").addClass("pull-right");
                }
            });
        }
        
    }
   
   function update_spare_part_detail(id, button){
        var select_type = document.getElementById("vendor_partner_type").value;
        var column = "";
        if(select_type === '<?php echo _247AROUND_PARTNER_STRING; ?>'){
            column = 'around_pickup_from_partner';
        }
        else{
           column = 'around_pickup_from_service_center'; 
        }
        
        $.ajax({
            url: "<?php echo base_url() ?>employee/spare_parts/process_edit_spare_pickup",
            type: "POST",
            data: {id:id, column: column},
            success: function (response) {
                if(response){
                    alert('Sucessfuly Updated');
                    $(button).closest('tr').remove();
                } else {
                    alert('There is issue to update booking');
                }
            }
        });
   }
   
    
</script>