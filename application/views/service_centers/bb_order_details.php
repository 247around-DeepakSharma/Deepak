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
            <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Delivered( <span style="font-weight:bold" id="deliverd_record"></span> )</a>
            </li>
            <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Pending( <span style="font-weight:bold" id="pending_record"></span> )</a>
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
                                <th>Physical Condition</th>
                                <th>Workign Condition</th>
                               
                                <th>Charges</th>
                                <th>Status</th>
                                <th>Delivery date</th>
                                <th>Action</th>
<!--                                <th>Delivered</th>
                                <th>Not Delivered</th>-->
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
                            <th>Physical Condition</th>
                            <th>Workign Condition</th>
                            
                            <th>SF Charge</th>
                            <th>Status</th>
                            <th>Order date</th>
                            

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
                    "targets": [0,7,8,9], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {

                $("#deliverd_record").text(response.recordsTotal);
            }
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
                    "targets": [0,7,8], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
            "fnInitComplete": function (oSettings, response) {

                $("#pending_record").text(response.recordsTotal);
            }
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