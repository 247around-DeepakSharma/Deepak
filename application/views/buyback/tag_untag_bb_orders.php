  
<style>
    .none{
        display: none;
    }
    .alert{
        width: 100%;
        border-radius: 4px;
        margin-top: 10px;
        margin-left: 10px;
    }
    textarea.ng-valid.ng-dirty{
        border:1px solid green;
        border-left: 5px solid green;
    }
    select.ng-valid.ng-dirty{
        border:1px solid green;
        border-left: 5px solid green;
    }
    input.ng-valid.ng-dirty{
        border:1px solid green;
        border-left: 5px solid green;
    }
    span.msg{
        margin-top:15px;
        color:#d9534f;
    }
    .headname{
        font-size:15px;
    }
    .spinner {
        margin: 0px auto;
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
<div class="right_col" role="main">
    <div class="tagUntagBbOrders" ng-app="tagUntagBbOrders">
        <div class="taggingUntagging">
            <div class="page-title">
                <div class="title_left">
                    <h3>Claim Process To Amazon</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel" style="min-height:500px;">
                        <div class="x_content">
                            <div class="formcontainer" ng-controller="tagUntagController">
                                <div class="col-md-6 col-md-offset-3">
                                    <div class=" text-center alert alert-danger none"><p></p></div>
                                    <div class="text-center alert alert-success none"><p></p></div>
                                </div>
                                <form class="form-horizontal form-label-left " name="form" novalidate autocomplete="off" ng-cloak="">
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="actionType">Select Action <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control col-md-7 col-xs-12" id="actionType" name="action_type" ng-model="tempData.action_type" required="">
                                                <option value="" selected="selected" disabled="">Select Action</option>
                                                <option value="tag">Tag</option>
                                            </select>
                                        </div>
                                        <span class="msg" ng-show="form.cp_id.$dirty && form.cp_id.$invalid">This Field Is Required</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="tagUntagType">Select Type <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control col-md-7 col-xs-12" id="tagUntagType" name="tag_untag_type" ng-model="tempData.tag_untag_type" ng-change="showInvoiceIdDiv(tempData.tag_untag_type)" required="">
                                                <option value="" selected="selected" disabled="">Select Type</option>
                                                <option value="claim_submitted_not_delivered">Claim Submitted Not Delivered</option>
                                                <option value="claim_submitted_broken">Claim Submitted Broken</option>
                                                <option value="claim_submitted_tat_breach">Claim Submitted Tat Breach</option>
                                                <option value="claim_approved_by_amazon">Claim Approved By Amazon</option>
                                                <option value="claim_rejected_by_amazon">Claim Rejected By Amazon</option>
                                                <option value="claim_debit_note_raised">Claim Debit Note Raised</option>
                                                <option value="claim_settled_by_amazon">Claim Settled By Amazon</option>
                                            </select>
                                        </div>
                                        <span class="msg"  ng-show="form.tag_untag_type.$dirty && form.tag_untag_type.$error.required">This Field Is Required</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="orderID">Order Id <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <textarea id="orderID" required="required" rows="3" class="form-control col-md-7 col-xs-12" name="order_id" ng-model="tempData.order_id"></textarea>
                                        </div>
                                        <span class="msg" ng-show="form.order_id.$dirty && form.order_id.$invalid">This Field Is Required</span>
                                    </div>
                                    <div class="item form-group" ng-if="IsInvoiceDivToShow">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="invoiceId">
                                                Invoice ID
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" id="invoiceId" required="required" class="form-control col-md-7 col-xs-12" name="invoice_id" ng-model="tempData.invoice_id"></input>
                                        </div>
                                        <span class="msg" ng-show="form.invoice_id.$dirty && form.invoice_id.$invalid">This Field Is Required</span>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="col-md-6 col-md-offset-5">
                                        <div class="item form-group">
                                        <button type="submit" class="btn btn-success col-md-4" ng-click="tagUntagOrderId()" ng-disabled="form.$invalid"> 
                                        {{ buttonText}}
                                        </button>
                                        <div class="col-md-4 spinner" ng-show="buttonText === 'Processing...'">
                                            <div class="rect1" style="background-color:#db3236"></div>
                                            <div class="rect2" style="background-color:#4885ed"></div>
                                            <div class="rect3" style="background-color:#f4c20d"></div>
                                            <div class="rect4" style="background-color:#3cba54"></div>
                                        </div>
                                    </div>
                                    </div>
                
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>   