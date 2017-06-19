<script src="<?php echo base_url() ?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="<?php echo base_url() ?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<style>
    #datatable_length,#datatable2_length{
        display: none;
    }
    #datatable_filter,#datatable2_filter{
        display: none;
    }
</style>

<div class="bb_order_details" style="margin: 20px 20px 10px 10px;">
    <h2>Order Details</h2>
    <hr>
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Delivered</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Pending</a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                <div class="x_content">

                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Service Name</th>

                                <th>City</th>
                                <th>Order Date</th>
                                <th>Delivery date</th>
                                <th>Status</th>
                                <th>SF Charge</th>

                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                <table id="datatable2" class="table table-striped table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Service Name</th>

                            <th>City</th>
                            <th>Order Date</th>
                            <th>Delivery date</th>
                            <th>Status</th>
                            <th>SF Charge</th>

                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var table;
    var table1;

    $(document).ready(function () {

        //datatables
        table = $('#datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_delivered_bb_order_details",
                "type": "POST",
                "data": {"status": 0}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
        });

        //datatables
        table1 = $('#datatable2').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_delivered_bb_order_details",
                "type": "POST",
                "data": {"status": 1}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
        });

    });


</script>