<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>PickUp </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form method="POST" action="<?php echo base_url();?>employee/booking/download_booking_bulk_search_snapshot">
                        <div class="form-group">
                            <label for="model_number">PickUp From *</label>
                            <select class="form-control" name="vendor_partner_type" id="vendor_partner_type">
                                <option value="" selected=" disabled">Select Pick From</option>
                                <option value="<?php echo _247AROUND_PARTNER_STRING?>">Partner</option>
                                <option value="<?php echo _247AROUND_SF_STRING;?>">Vendor</option>
                            </select>
                        </div>
                    </form>
                    
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%; margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Booking ID</th>
                            <th>Courier Name</th>
                            <th>Courier Charge</th>
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

<script>
var table;

$(document).ready(function () {

    //datatables
    table = $('#datatable1').DataTable({
        processing: true, //Feature control the processing indicator.
        serverSide: true, //Feature control DataTables' server-side processing mode.
        order: [], //Initial no order.
        pageLength: 50,
        // Load data for the table's content from an Ajax source
        ajax: {
            url: "<?php echo base_url(); ?>employee/spare_parts/get_upload_file_history",
            type: "POST",
            data: {file_type: '<?php echo _247AROUND_SNAPDEAL_DELIVERED; ?>'}
        },
        //Set column definition initialisation properties.
        columnDefs: [
            {
                "targets": [0,1,2,3,4], //first column / numbering column
                "orderable": false //set not orderable
            }
        ]
    });
});

</script>