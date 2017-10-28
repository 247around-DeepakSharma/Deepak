<style>
    #stand_allocation_table_filter{
        display:none;
    }
</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<div class="right_col" role="main"  >
    <h3 align="center">Stand allocation for partner and brand</h3>
    <?php
    if($this->session->userdata('stand_msg')){
        echo '<h3 align="center" style="color:green">'.$this->session->userdata('stand_msg').'</h3>';
        $this->session->unset_userdata('stand_msg');
    }
     ?>
    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" style="margin:0px 10px;">Add New Records</button>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                            <table id="stand_allocation_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Brand</th>
                                        <th>Partner</th>
                                        <th>Stand</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                    </div>
                   
                </div>
            
            </div>
        </div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" ng-app="stand_allocation">
      <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" style="text-align:center;">Add New Combination</h4>
      </div>
      <div class="modal-body" ng-controller="standAllocationController">
          <form action='process_stand_combination' method='POST' id="stand_allocation_form">
              <select style="width:100%" name="partner_id" ui-select2 id="partner"  class="form-control data_change" data-placeholder="Select Partner">
                                               <option value="" ng-show="false">Select Partner</option>
                                                <option ng-repeat="y in partner_list" value="{{y.id}}">{{y.public_name}}</option>
                                            </select>
                <select style="width:100%; margin-top:15px;" name="brand" ui-select2 id="brand"  class="form-control data_change" data-placeholder="Select Brand">
                                                <option value="" ng-show="false">Select Brand</option>
                                                <option ng-repeat="y in brand_list" value="{{y.brand_name}}">{{y.brand_name}}</option>
                                            </select>
               <select style="width:100%; margin-top:15px;" name="is_stand" ui-select2 id="is_stand"  class="form-control data_change" data-placeholder="is_stand">
                                                <option value="" ng-show="false">Is_stand</option>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
              <input type="hidden" value="add" id="add_update" name="add_delete">
              <p align='center'> <input type="button" value="Submit" class="btn btn-info" style="margin-top:10px;" onclick="validation()"></p>
              </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<script>
    function createStandEditForm(brand,partner,is_stand){
        document.getElementById("add_update").value = 'update';
        document.getElementById("brand").value = brand;
        document.getElementById("brand").disabled = true;
         document.getElementById("partner").value = partner; 
         document.getElementById("partner").disabled = true; 
          document.getElementById("is_stand").value = is_stand;
    }

            ad_table = $('#stand_allocation_table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            "ajax": {
                "url": baseUrl+"/employee/partner/get_stand_allocation_data",
                "type": "POST"
            }
        });
     $(document).ready(function () {
         ad_table.ajax.reload( function ( json ) {} );
    });
    function validation(){
        brand= document.getElementById("brand").value;
        partner = document.getElementById("partner").value;
        is_stand = document.getElementById("is_stand").value;
        if(!brand || !partner || !is_stand){
            alert("please select all fields");
        }
        else{
          document.getElementById("brand").disabled = false;
          document.getElementById("partner").disabled = false;
          document.getElementById("stand_allocation_form").submit();
    }
    }
    </script>