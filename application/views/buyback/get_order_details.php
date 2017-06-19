<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/services/services.js"></script>

<div class="right_col" role="main">
    <!--        <div class="page-title">
                <div class="title_left">
                    <h3>Order Details</h3>
                </div>
            </div>-->
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_title">
                        <h2><i class="fa fa-bars"></i> Order Details <!--<small>Float left</small>--></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">Settings 1</a>
                                    </li>
                                    <li><a href="#">Settings 2</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">


                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Delivered/Transit</a>
                                </li>
                                <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Cancelled/Rejected</a>
                                </li>
                                <!--                                <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Cancelled/Rejected</a>
                                                                </li>-->
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
                                                    <th>Exchange Value</th>
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
                                                <th>Exchange Value</th>
                                                <th>SF Charge</th>

                                            </tr>
                                        </thead>
                                        <tbody>


                                        </tbody>
                                    </table>
                                </div>
                                <!--                                <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                                                                    <p>xxFood truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo
                                                                        booth letterpress, commodo enim craft beer mlkshk </p>
                                                                </div>-->
                            </div>
                        </div>

                    </div>

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
                    "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                    "type": "POST",
                    "data": {"status": 0}
                },
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0,8], //first column / numbering column
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
                    "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                    "type": "POST",
                    "data": {"status": 1}
                },
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0,8], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
                ],
            });

        });


    </script>