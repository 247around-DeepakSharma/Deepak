<style>
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 40px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }

    #datatable1_info{
    display: none;
    }
    
    #datatable1_filter{
        text-align: right;
    }
</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/services/services.js"></script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="buyback_file_upload" ng-app="uploadFile">
        <div class="order_details_file">
            <div class="page-title">
                <div class="title_left">
                    <h3>Upload Order Details</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Order Details file</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                                <div ng-controller="uploadOrderDetailsFile">
                                    <div class="form-group" >
                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                            <input type="file" file-model="myFile" id="order_details_file" required="required" class="form-control col-md-7 col-xs-12" enctype="multipart/form-data">
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                            <input type="text" placeholder="File Date" ng-model="file_date.received_date" class="form-control" id="file_date" name="file_date"/>
                                        </div>
                                        
<!--                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                            <input type="text" placeholder="SVC Number" ng-model="file_date.qc_svc" class="form-control" id="qc_svc" name="qc_svc"/>
                                        </div>-->
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <button ng-click="uploadFile()" id="upload_order_file" class="btn btn-success">Upload</button>
                                        </div>
                                        
                                        <div class="col-md-6 col-sm-6 col-xs-12" ng-cloak="">
                                            <div class="spinner" ng-if="ShowSpinnerStatus">
                                                <div class="rect1" style="background-color:#db3236"></div>
                                                <div class="rect2" style="background-color:#4885ed"></div>
                                                <div class="rect3" style="background-color:#f4c20d"></div>
                                                <div class="rect4" style="background-color:#3cba54"></div>
                                            </div>
                                            <div ng-if="successMsg" class="alert alert-success">{{msg}}</div>
                                            <div ng-if="errorMsg" class="alert alert-danger">{{msg}}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div style="margin-top:20px;">
                                    <h3>File Upload History</h3>
                                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Download</th>
                                                <th>Uploaded By</th>
                                                <th>Uploaded Date</th>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var table;

        $(document).ready(function () {
            
            $('input[name="file_date"]').daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                locale:{
                    format: 'DD-MM-YYYY'
                }
            });
            
            $('input[name="file_date"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            });

            $('input[name="file_date"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            //datatables
            table = $('#datatable1').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: '<?php echo _247AROUND_BB_ORDER_LIST; ?>'}
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
        $("#upload_order_file").click(function(){
         $("#upload_order_file").attr('disabled',true);
        })
        $("#order_details_file").change(function(){
           if($("#order_details_file").val() != ''){
               $("#upload_order_file").attr('disabled',false);  
           }
        });  
</script>