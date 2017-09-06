<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script>
    var unassigned;
</script>
<style>
    .dropdown-menu{
        font-size: 13px;
        left:-60px;
    }
</style>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main" ng-app="viewBBOrder">
<!--        <div class="page-title">
    <div class="title_left">
        <h3>Order Details</h3>
    </div>
    </div>-->
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel" style="height: auto;" ng-controller="assignCP">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title">
                    <h2>
                        <i class="fa fa-bars"></i> Order Details <!--<small>Float left</small>-->
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <div ng-if="showLoader" style="position: absolute;z-index: 99999;width: 100%;height: 100%; background-color: rgba(19, 17, 17, 0.31);">
                    <img src="<?php echo base_url();?>images/loadring.gif" style="position: relative;top: 350px;left: 400px;">
                </div>
                <div class="x_content" >
                    <?php if ($this->session->userdata('error')) {
                            echo '<br><br><div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                           <strong>' . $this->session->userdata('error') . '</strong>
                       </div>';
                        }else if($this->session->userdata('success')){
                                echo '<br><br><div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                               <strong>' . $this->session->userdata('success') . '</strong>
                           </div>';
                        }
                        ?>
                    
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_content1" role="tab" id="intransit-tab" data-toggle="tab" aria-expanded="true">In-Transit( <span style="font-weight: bold;" id="in_tranist_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content2" id="delivered-tab" role="tab" data-toggle="tab" aria-expanded="true">Delivered ( <span style="font-weight: bold;" id="in_delivered_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content3" role="tab" id="unassigned" data-toggle="tab" aria-expanded="true">Un-Assigned Order ( <span style="font-weight: bold;" id="in_unassigned_record">0</span> )</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content4" role="tab" id="others" data-toggle="tab" aria-expanded="true">Others ( <span style="font-weight: bold;" id="in_others_record">0</span> )</a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="intransit-tab">
                                <table id="datatable1" class="table table-striped table-bordered" style="width: 100%; margin-bottom: 100px;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order ID</th>
                                            <th>Service Name</th>
                                            <th>City</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Exchange Value</th>
                                            <th>SF Charge</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="delivered-tab">
                                <table id="datatable2" class="table table-striped table-bordered" style="width: 100%; margin-bottom: 100px;">
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
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="unassigned">
                                <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                                <table id="datatable3" class="table table-striped table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order ID</th>
                                            <th>Services</th>
                                            <th>City</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Exchange Value</th>
                                            <th>Assign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                </table>
                                
                                    <div class="row">
                                        <div class="btn btn-info" ng-click="showDialogueBox()">Assign All Order</div>
                                       
                                         <a href="javascript:void(0);" class="btn btn-md  btn-success" onclick="reAssign()"  >Assign CP</a>
                                        <div id="invoiceDetailsModal"  class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" ng-click="closeModel()">&times;</button>
                                                        <h4 class="modal-title">Not Assigned</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="open_model">
                                                            
                                                            <table class="table table-bordered table-hover table-responsive">
                                                                <thead>
                                                                    <th>S.No.</th>
                                                                    <th>Order ID</th>   
                                                                     <th>Message</th>   
                                                                </thead>
                                                                <tbody>
                                                                    <tr ng-repeat="x in notFoundCity">
                                                                        <td>{{$index + 1}}</td>
                                                                        <td>{{ x.order_id }}</td>
                                                                         <td>{{ x.message }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" ng-click="closeModel()">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="others">
                                <table id="datatable4" class="table table-striped table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order ID</th>
                                            <th>Services</th>
                                            <th>City</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Exchange Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    var in_transit;
    var delivered;
    
    var others;
    
    $(document).ready(function () {
         
        //datatables
        in_transit = $('#datatable1').DataTable({
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
                    "targets": [0,1,6,7,8], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            $("#in_tranist_record").text(response.recordsTotal);
          }
            
        });
    
    
    
        //datatables
        delivered = $('#datatable2').DataTable({
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
                    "targets": [0,1,7,8,9], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $("#in_delivered_record").text(response.recordsTotal);
            }
        });
    
        //datatables
        unassigned = $('#datatable3').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 2}
            },
            "drawCallback": function( settings ) {
                $(".assign_cp_id").select2({
                  allowClear: true
                });
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
           
                $("#in_unassigned_record").text(response.recordsTotal);
            }
        });
    
    
    //datatables
        others = $('#datatable4').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": {"status": 3}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,6], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
           
                $("#in_others_record").text(response.recordsTotal);
            }
        });
    
    
    });
    
    
</script>
<script>

    function showDialogueBox(url){
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
