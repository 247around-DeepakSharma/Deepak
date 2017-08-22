  
<style>
    .none{
        display: none;
    }
    .alert{
        width: 50%;
        border-radius: 0;
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

</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<div class="right_col" role="main">
    <div class="addCpShopAddress" ng-app="shopAddressAddApp">
        <div class="shopAddress">
            <div class="page-title">
                <div class="title_left">
                    <h3>Add Shop Address</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel" style="min-height:500px;">
                        <div class="x_content">
                            <div class="formcontainer" ng-controller="userController">
                                <div class=" text-center alert alert-danger none"><p></p></div>
                                <div class="text-center alert alert-success none"><p></p></div>
                                <form class="form-horizontal form-label-left " name="userForm" novalidate autocomplete="off" ng-cloak="">
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="cp_id">Select Collection Partner <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control col-md-7 col-xs-12" name="cp_id" ng-model="tempData.cp_id" ng-options="x.name for x in cp_list"required="">
                                                <option disabled="" selected="" value="">Select Collection Partner</option>
                                            </select>
                                        </div>
                                        <span class="msg" ng-if="userForm.cp_id.$invalid" ng-show="userForm.cp_id.$dirty && userForm.cp_id.$invalid">Collection Partner name is required</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">Contact Person <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="contact_person" class="form-control col-md-7 col-xs-12"  name="name" ng-pattern="/^[a-zA-Z\s]*$/" ng-model="tempData.name" required="">
                                        </div>
                                        <span class="msg"  ng-show="userForm.name.$dirty && userForm.name.$error.required">Contact Person name is required</span>
                                        <span class="msg"  ng-show="userForm.name.$dirty && userForm.name.$error.pattern">Contact Person Name is Invalid</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="contact_email">Contact Email 
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="email" id="contact_email" class="form-control col-md-7 col-xs-12"  name="email" ng-model="tempData.email">
                                        </div>
                                        <span class="msg" ng-if="userForm.email.$invalid" ng-show="userForm.email.$dirty && userForm.email.$invalid">Email Address is not valid</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="primary_contact_number">Primary Mobile No. <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-4 col-xs-12">
                                            <input type="tel" id="primary_contact_number" required="required" class="form-control col-md-7 col-xs-12" ng-minlength="10" ng-maxlength="10" name="phone_number" ng-model="tempData.phone_number">
                                        </div>
                                        <span class="msg"  ng-show="userForm.phone_number.$dirty && userForm.phone_number.$error.required">Phone Number is required</span>
                                        <span class="msg"  ng-show="userForm.phone_number.$error.minlength">Enter Valid Phone Number</span>
                                        <span class="msg"  ng-show="userForm.phone_number.$error.maxlength">Enter Valid Phone Number</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="alternate_conatct_number">Alt Mobile No1 
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="tel" id="alternate_conatct_number" class="form-control col-md-7 col-xs-12" name="alt_phone_number_1" ng-minlength="10" ng-maxlength="10" ng-model="tempData.alt_phone_number_1">
                                        </div>
                                        <span class="msg"  ng-show="userForm.alt_phone_number_1.$error.minlength">Enter Valid Phone Number</span>
                                        <span class="msg"  ng-show="userForm.alt_phone_number_1.$error.maxlength">Enter Valid Phone Number</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="alternate_conatct_number2">Alt Mobile No2  
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="tel" id="alternate_conatct_number2" class="form-control col-md-7 col-xs-12" name="alt_phone_number_2" ng-minlength="10" ng-maxlength="10" ng-model="tempData.alt_phone_number_2">
                                        </div>
                                        <span class="msg"  ng-show="userForm.alt_phone_number_2.$error.minlength">Enter Valid Phone Number</span>
                                        <span class="msg"  ng-show="userForm.alt_phone_number_2.$error.maxlength">Enter Valid Phone Number</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="tin_number">Tin Number 
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="tin_number" type="text" class="optional form-control col-md-7 col-xs-12" name="tin_number" ng-model="tempData.tin_number" numbers-only>
                                        </div>
                                        <span class="msg" ng-if="userForm.tin_number.$invalid" ng-show="userForm.tin_number.$dirty && userForm.tin_number.$error.required">Tin Number is required</span>
                                        <span class="msg" ng-if="userForm.tin_number.$invalid" ng-show="userForm.tin_number.$dirty && userForm.tin_number.$Invalid">Enter Valid Tin Number</span>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_line1">Shop Address Line 1 <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <textarea id="shop_address_line1" required="required" rows="3.5" class="form-control col-md-7 col-xs-12" name="shop_address_line1" ng-model="tempData.shop_address_line1"></textarea>
                                        </div>
                                        <span class="msg" ng-if="userForm.shop_address_line1.$invalid" ng-show="userForm.shop_address_line1.$dirty && userForm.shop_address_line1.$invalid">Shop Address is required</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_line2">Shop Address Line 2 
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <textarea id="shop_address_line2" class="form-control col-md-7 col-xs-12" name="shop_address_line2" ng-model="tempData.shop_address_line2"></textarea>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_pincode">Shop Address Pincode
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="shop_address_pincode" type="text" required="required" class="optional form-control col-md-7 col-xs-12" name="shop_address_pincode" ng-minlength="6" ng-maxlength="6" ng-model="tempData.shop_address_pincode" ng-change="(tempData.shop_address_pincode.length === 6) && getCity()">
                                        </div>
                                        <span class="msg" ng-if="userForm.shop_address_pincode.$invalid" ng-show="userForm.shop_address_pincode.$dirty && userForm.shop_address_pincode.$error.required">Pincode is required</span>
                                        <span class="msg" ng-if="userForm.shop_address_pincode.$invalid" ng-show="userForm.shop_address_pincode.$error.minlength">Enter Valid Pincode</span>
                                        <span class="msg" ng-if="userForm.shop_address_pincode.$invalid" ng-show="userForm.shop_address_pincode.$error.maxlength">Enter Valid Pincode</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_city">Shop Address City
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select id="shop_address_city" required="required" class="optional form-control col-md-7 col-xs-12" name="shop_address_city" ng-model="tempData.shop_address_city">
                                                <option value="" selected="" disabled="">Enter Pincode First</option>
                                            </select>
                                        </div>
                                        <span class="msg" ng-if="userForm.shop_address_city.$invalid" ng-show="userForm.shop_address_city.$dirty && userForm.shop_address_city.$invalid">City is required</span>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_region">Shop Region
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select id="shop_address_region" required="required" class="optional form-control col-md-7 col-xs-12" name="shop_address_region" ng-model="tempData.shop_address_region">
                                                <option value="" selected="" disabled="">Select Region</option>
                                            </select>
                                        </div>
                                        <span class="msg" ng-if="userForm.shop_address_region.$invalid" ng-show="userForm.shop_address_region.$dirty && userForm.shop_address_region.$invalid">Region is required</span>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="shop_address_state">Shop Address State
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="shop_address_state" type="text" required="required" class="optional form-control col-md-7 col-xs-12" name="shop_address_state" ng-pattern="/^[a-zA-Z\s]*$/" ng-model="tempData.shop_address_state">
                                        </div>
                                        <span class="msg" ng-show="userForm.shop_address_state.$dirty && userForm.shop_address_state.$error.required">State is required</span>
                                        <span class="msg"  ng-show="userForm.shop_address_state.$dirty && userForm.shop_address_state.$error.pattern">State Name is Invalid</span>
                                    </div>
                                    
                                    <div class="item form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12" for="cp_capacity">CP Capacity
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="cp_capacity" type="text" class="optional form-control col-md-7 col-xs-12" name="cp_capacity" ng-model="tempData.cp_capacity">
                                        </div>
<!--                                        <span class="msg" ng-show="userForm.cp_capacity.$dirty && userForm.cp_capacity.$error.required">State is required</span>-->
<!--                                        <span class="msg"  ng-show="userForm.cp_capacity.$dirty && userForm.cp_capacity.$error.pattern">State Name is Invalid</span>-->
                                    </div>
                                    
                                    <div class="ln_solid"></div>
                                    <a href="javascript:void(0);" class="btn btn-success" ng-click="saveShopAddress()" ng-disabled="userForm.$invalid" >Add Shop Address</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <script>
        $(document).ready(function () {
            $("#cp_id").select2();
        });
    </script>    