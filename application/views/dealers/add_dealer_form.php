 <script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
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
    @media (min-width: 992px){
       .col-md-3 {
           width: 25%;
        }
    }

</style>
<div id="page-wrapper">
    <div class="row">
        
        <div  class = "panel panel-info" ng-app="addDealers">
            <div class="panel-heading">Add Dealer</div>
            <div class="panel-body" ng-controller="addDealersController" >
                <form class="form-horizontal form-label-left" name="dealerForm" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer name">Dealer Name *</label>
                                <input type="text" placeholder="Enter Dealer Name" class="form-control" ng-pattern="/^[a-zA-Z\s]*$/" ng-model="tempData.dealer_name" 
                                       id="dealer_name" name="dealer_name" value = "" required>
                                 <span class="msg"  ng-show="dealerForm.dealer_name.$dirty && dealerForm.dealer_name.$error.required">Dealer Name is required</span>
                                 <span class="msg"  ng-show="dealerForm.dealer_name.$dirty && dealerForm.dealer_name.$error.pattern">Dealer Name is Invalid</span>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer name">Dealer Mobile No *</label>
                                <input type="text" placeholder="Enter Dealer Mobile Number" class="form-control" ng-pattern="/^[6-9]{1}[0-9]{9}$/" ng-minlength="10" ng-maxlength="10" ng-model="tempData.dealer_phone_number_1"
                                       id="dealer_phone_number_1" name="dealer_phone_number_1" value = "" required>
                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$dirty && dealerForm.dealer_phone_number_1.$error.required">Mobile No is required</span>
                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$dirty && dealerForm.dealer_phone_number_1.$error.pattern">Please Enter Valid Mobile No</span>
<!--                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.minlength">Please Enter Valid Mobile</span>
                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.maxlength">Please Enter Valid Mobile</span>-->
                            </div>
                        </div>
                         <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer email">Dealer Email </label>
                                <input type="email" placeholder="Enter Dealer Email" ng-trim="false" class="form-control" ng-model="tempData.dealer_email"
                                       id="dealer_email" name="dealer_email" value = "" >
                                 <span class="msg" ng-if="dealerForm.dealer_email.$invalid" ng-show="dealerForm.dealer_email.$dirty && dealerForm.dealer_email.$invalid">Email ID is not valid</span>
<!--                                 <span class="msg"  ng-show="dealerForm.dealer_email.$dirty && dealerForm.dealer_email.$error.required">Email ID is required</span>-->
<!--                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.minlength">Please Enter Valid Mobile</span>
                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.maxlength">Please Enter Valid Mobile</span>-->
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer name">Owner Name </label>
                                <input type="text" placeholder="Enter Owner Name" ng-trim="false" class="form-control" ng-pattern="/^[a-zA-Z\s]*$/"
                                       ng-model="tempData.owner_name"  id="owner_name" name="owner_name" value = "" >
<!--                                 <span class="msg"  ng-show="dealerForm.owner_name.$dirty && dealerForm.owner_name.$error.required">Owner Name is required</span>-->
                                 <span class="msg"  ng-show="dealerForm.owner_name.$dirty && dealerForm.owner_name.$error.pattern">Owner Name is Invalid</span>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer name">Owner Mobile No </label>
                                <input type="text" placeholder="Enter Owner Mobile Number" class="form-control"
                                       ng-pattern="/^[6-9]{1}[0-9]{9}$/" ng-minlength="10" ng-maxlength="10" ng-model="tempData.owner_phone_number_1"
                                       id="owner_phone_number_1" name="owner_phone_number_1" value = "" >
<!--                                 <span class="msg"  ng-show="dealerForm.owner_phone_number_1.$dirty && dealerForm.owner_phone_number_1.$error.required">Mobile No is required</span>-->
                                 <span class="msg"  ng-show="dealerForm.owner_phone_number_1.$dirty && dealerForm.owner_phone_number_1.$error.pattern">Please Enter Valid Mobile No</span>
<!--                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.minlength">Please Enter Valid Mobile</span>
                                 <span class="msg"  ng-show="dealerForm.dealer_phone_number_1.$error.maxlength">Please Enter Valid Mobile</span>-->
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="owner email">Owner Email </label>
                                <input type="email" placeholder="Enter Dealer Email" ng-trim="false" class="form-control" ng-model="tempData.owner_email"
                                       id="owner_email" name="owner_email" value = "" >
                                 <span class="msg" ng-if="dealerForm.owner_email.$invalid" ng-show="dealerForm.owner_email.$dirty && dealerForm.owner_email.$invalid">Email ID is not valid</span>
<!--                                 <span class="msg"  ng-show="dealerForm.owner_email.$dirty && dealerForm.owner_email.$error.required">Email ID is required</span>-->
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="pincode">State *</label>
                                   <select name="city" id="city" required="required" ui-select2 class="form-control"  ng-model="tempData.state"
                                         ng-options="option1.state as option1.state for option1 in state_list" required
                                         data-placeholder="Select State" >
                                     <option value="" disabled="" ng-show="false"></option>
                                 </select>
                                 <span class="msg"  ng-show="dealerForm.state.$dirty && dealerForm.state.$error.required">State is required</span>
                                
                            </div>
                            
                        </div>  
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="pincode">City *</label>
                                 <select name="city" id="city" required="required" ui-select2 class="form-control"  ng-model="tempData.city"
                                         ng-options="option1.district as option1.district for option1 in city_list" required
                                         data-placeholder="Select City" >
                                     <option value="" disabled="" ng-show="false"></option>
                                 </select>
                                 <span class="msg"  ng-show="dealerForm.city.$dirty && dealerForm.city.$error.required">City is required</span>
                                
                            </div>
                            
                        </div>  
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="pincode">Partner *</label>
                                <select name="partner_id[]" ui-select2  id="partner_id" class="form-control" 
                                       
                                        ng-model="tempData.partner_id" 
                                        ng-options="option.partner_id as option.source for option in partner_list" 
                                       required="" multiple="multiple" required data-placeholder="Select Partner">
                                    <option value="" disabled="" ng-show="false"></option>
                                </select>
                                <span class="msg"  ng-show="dealerForm.partner_id.$dirty && dealerForm.partner_id.$error.required">Partner is required</span>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-md-offset-5">
                            <a href="javascript:void(0);" class="btn btn-success" ng-click="create_dealer()" ng-disabled="dealerForm.$invalid" >Create Dealer</a>
                        </div>
                        
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>
