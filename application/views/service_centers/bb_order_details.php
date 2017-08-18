<script src="<?php echo base_url() ?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="<?php echo base_url() ?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<style>
    .dataTables_length{
        margin:10px 0px;
    }
    
    .dataTables_filter{
        margin:10px 0px;
        text-align: right;
    }
    .truncate_text {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="bb_order_details" style="margin: 20px 20px 10px 10px;">
    
      
    <h2>Order Details</h2>
    
    <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
                }
                ?>
    <hr>
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="pending-tab" data-toggle="tab" aria-expanded="true">In-Transit( <span style="font-weight:bold" id="pending_record"></span> )</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content2" role="tab" id="delivered-tab"  data-toggle="tab" aria-expanded="true">Delivered( <span style="font-weight:bold" id="deliverd_record"></span> )</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content3" role="tab" id="acknowledge-tab"  data-toggle="tab" aria-expanded="true">Acknowledge( <span style="font-weight:bold" id="acknowledge_record"></span> )</a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="pending-tab">
                <table id="pending_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>SF Charge</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="delivered-tab">
                <div class="x_content">

                    <table id="delivered_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Charges</th>
                                <th>Order date</th>
                                <th>Delivery date</th>
                                <th>Remarks</th>
                                <th>Action</th>
                                
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="acknowledge-tab">
                <table id="acknowledge_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>Delivery date</th>
                            <th>SF Charge</th>
                            <th>Status</th>
                            
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

    var delivered;
    var pending;

    $(document).ready(function () {

        //delivered datatables
        delivered = $('#delivered_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 0}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7,8], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#deliverd_record").text(response.recordsTotal);
            }
        });
        
        //pending datatables
        pending = $('#pending_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 1}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,6,7], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#pending_record").text(response.recordsTotal);
            }
        });
        
        //acknowledge datatables
        acknowledge = $('#acknowledge_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 2}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#acknowledge_record").text(response.recordsTotal);
            }
        });
    });
    
    $(document).ready(function(){
    $('[data-toggle="popover"]').popover({
        placement : 'top',
        trigger : 'hover'
    });
});

</script>
<script>
    function showConfirmDialougeBox(url){
        swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function(){
                window.location.href = url;
            });
    }
</script>
<?php 
$this->session->unset_userdata('success');
$this->session->unset_userdata('error');
?>