<script src="<?php echo base_url() ?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="<?php echo base_url() ?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script> 
<script src="<?php echo base_url(); ?>js/jquery.counterup.min.js"></script>
<link href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" rel="stylesheet">
<script src='https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js'></script>
<script src='//cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js'></script>
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
    .dropdown-menu{
        left: -60px;
    }
            .blinking{
    animation:blinkingText 0.5s infinite;
}
@keyframes blinkingText{
    0%{     color: red;    }
    49%{   color: red; }
    50%{    color: green; }
    99%{    color:green;  }
    100%{   color: 008000;    }
}
</style>
<div class="col-md-12" id="bb_charges_summary" style="margin-top:10px;">
    <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
</div>
<div class="bb_order_details right_col" style="margin: 20px 20px 10px 10px;">
    
    <div class="row">
        <div class="col-md-6">
            <h3>Order Details</h3>
        </div>
        <div class="col-md-6 text-right">
            <?php
            if(!empty($otp)){
            ?>
            <h3>Last OTP - <b><?php echo $otp; ?></b></h3>
            <a href='<?php echo base_url(); ?>service_center/buyback/bb_otp_list' target='_blanck'>Show Previous OTP</a>
            <?php } ?>
        </div>
    </div>
    
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
            <li role="presentation" class=""><a href="#tab_content5" role="tab" id="inprocess-tab"  data-toggle="tab" aria-expanded="true">InProcess Order( <span style="font-weight:bold" id="inprocess_record"></span> )</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content3" role="tab" id="acknowledge-tab"  data-toggle="tab" aria-expanded="true">Manual Acknowledge( <span style="font-weight:bold" id="acknowledge_record"></span> )</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content4" role="tab" id="auto_acknowledge-tab"  data-toggle="tab" aria-expanded="true">Auto Acknowledge( <span style="font-weight:bold" id="auto_acknowledge_record"></span> )</a>
            </li>
            
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="pending-tab">
                <table id="pending_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Tracking ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>SF Charge</th>
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
                                <th>Tracking ID</th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>SF Charge</th>
                                <th>CP Claimed Price</th>
                                <th>Order date</th>
                                <th>Delivery date</th>
                                <th>Days left <br> to confirm</th>
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
                            <th>Tracking ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>Delivery date</th>
                            <th>SF Charge</th>
                            <th>CP Claimed Price</th>
                            <th>Status</th>
                            <th>Acknowledge Date</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="auto_acknowledge-tab">
                <table id="auto_acknowledge_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Tracking ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>Delivery date</th>
                            <th>SF Charge</th>
                            <th>CP Claimed Price</th>
                            <th>Status</th>
                            <th>Acknowledge Date</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_content5" aria-labelledby="inProcess-tab">
                <table id="inprocess_datatable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Order ID</th>
                            <th>Tracking ID</th>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Order date</th>
                            <th>Delivery date</th>
                            <th>SF Charge</th>
                            <th>CP Claimed Price</th>
                            <th>Status</th>
                            <th>Action</th>
                            
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
    var auto_acknowledge;
    var inprocess;

    $(document).ready(function () {
        
         $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centers/get_bb_cp_charges/'+<?php echo $this->session->userdata('service_center_id')?>,
            success: function (data) {
             $("#bb_charges_summary").html(data);   
             $('.bb_counter').counterUp({
                delay: 10, // the delay time in ms
                time: 1000 // the speed time in ms
             });

            }
        });

        //delivered datatables
        delivered = $('#delivered_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Delivered Order',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 0}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,8,9,10,11], //first column / numbering column
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
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'In-Transit Order',
                    exportOptions: {
                       columns: [1,2,3,4,5,6],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
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
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Manual_Acknowledge',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,10],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 2,auto_acknowledge:0}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7,8], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#acknowledge_record").text(response.recordsTotal);
            }
        });
        
        auto_acknowledge = $('#auto_acknowledge_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Auto_Acknowledge',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,10],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 2, auto_acknowledge: 1}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7,8], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#auto_acknowledge_record").text(response.recordsTotal);
            }
        });
        
        inprocess = $('#inprocess_datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Auto_Acknowledge',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_bb_order_details",
                "type": "POST",
                "data": {"status": 3}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,7,8,10], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#inprocess_record").text(response.recordsTotal);
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

<?php 
if($this->session->userdata('success')){$this->session->unset_userdata('success');}
if($this->session->userdata('error')){$this->session->unset_userdata('error');}
?>