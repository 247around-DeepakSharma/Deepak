<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script> 
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>

<script src="<?php echo base_url(); ?>js/buyback_app/services/services.js"></script>
<style>
    .main_container{
        margin: 10px;
        padding: 10px;
        border: 1px solid #ccc;
    }
    .box-header{
        border-bottom: 1px solid #e6e6e6;
        margin-bottom: 10px;
    }
    .box-body{
        box-sizing: content-box;
        box-shadow: 0 0 1px #ccc;
        padding-top: 25px; 
    }
    .loading {
        position: absolute;
        top: 50%;
        left: 50%;
    }
    .loading-bar {
        display: inline-block;
        width: 4px;
        height: 18px;
        border-radius: 4px;
        animation: loading 1s ease-in-out infinite;
    }
    .loading-bar:nth-child(1) {
        background-color: #3498db;
        animation-delay: 0;
    }
    .loading-bar:nth-child(2) {
        background-color: #c0392b;
        animation-delay: 0.09s;
    }
    .loading-bar:nth-child(3) {
        background-color: #f1c40f;
        animation-delay: .18s;
    }
    .loading-bar:nth-child(4) {
        background-color: #27ae60;
        animation-delay: .27s;
    }

    @keyframes loading {
        0% {
            transform: scale(1);
        }
        20% {
            transform: scale(1, 2.2);
        }
        40% {
            transform: scale(1);
        }
    }
</style>
<div class="main_container">
    <div class="box-header">
        <h3>Upload Buyback File</h3>  
    </div>
    <div class="box-body">
        <div class="container" ng-app="fileUploadApp" ng-controller="fileUploadController">  
            <div class="row">
                <div class="col-sx-4">
                    <div class="col-md-4">  
                        <input type="file" class="form-control" file-input="files" />  
                    </div>  
                </div>
                <div class="col-xs-2">
                    <button class="btn btn-info" ng-click="uploadFile()">Upload</button>   
                    <div style="clear:both"></div>  
                    <br /><br />  
                </div>
                <div class="col-xs-6">
                    <div ng-if="ShowSpinnerStatus" class="loader">
                        <div class="loading">
                            <div class="loading-bar"></div>
                            <div class="loading-bar"></div>
                            <div class="loading-bar"></div>
                            <div class="loading-bar"></div>
                        </div>
                    </div>
                    <div ng-if="ErrorResponseState" ng-bind="ErrorResponseMsg" class="alert alert-danger"></div>
                    <div ng-if="SuccessResponseState" ng-bind="SuccessResponseMsg" class="alert alert-success"></div>
                </div>
            </div>
        </div>
    </div>
</div>  