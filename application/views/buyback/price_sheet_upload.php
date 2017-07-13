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

        <div class="price_charges_file">
            <div class="page-title">
                <div class="title_left">
                    <h3>Upload Price charges file</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Price charges file</h2>
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

                                <div ng-controller="uploadPriceChargesFile">
                                    <div class="form-group" >
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="file" file-model="myFile" id="order_details_file" required="required" class="form-control col-md-7 col-xs-12">
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="spinner" ng-if="ShowSpinnerStatus">
                                                <div class="rect1" style="background-color:#db3236"></div>
                                                <div class="rect2" style="background-color:#4885ed"></div>
                                                <div class="rect3" style="background-color:#f4c20d"></div>
                                                <div class="rect4" style="background-color:#3cba54"></div>
                                            </div>
                                            <div ng-if="successMsg" class="alert alert-success alert-dismissable">{{msg}}</div>
                                            <div ng-if="errorMsg" class="alert alert-danger alert-dismissable">{{msg}}</div>
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button ng-click="uploadFile()" class="btn btn-success">Upload</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div ng-controller="uploadFileHistory" style="margin-top:30px;">
                                    <h2>File History</h2>
                                    <table class="table table-bordered table-hover table-responsive">
                                        <thead>
                                            <th>S.No.</th>
                                            <th>Download</th>
                                            <th>Uploaded By</th>
                                            <th>Uploaded Date</th>    
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="x in uploadFileHistory">
                                                <td>{{$index + 1}}</td>
                                                <td><a href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/{{x.file_name}}'><div class="btn btn-success btn-sm">Download</div></a></td>
                                                <td>{{ x.agent_name }}</td>
                                                <td>{{ x.upload_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--        <div class="shop_address_file">
            <div class="page-title">
                <div class="title_left">
                    <h3>Upload Shop Address File</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel" style="height: auto;">
                        <div class="x_title">
                            <h2>Shop Address File</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                </li>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="display:none;">
                            <br />
                            <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                                <div ng-controller="uploadShopAddressFile">
                                    <div class="form-group" >
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="file" file-model="myFile" id="order_details_file" required="required" class="form-control col-md-7 col-xs-12">
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
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
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button ng-click="uploadFile()" class="btn btn-success">Upload</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->


    </div>
</div>