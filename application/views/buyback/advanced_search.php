<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                          <i class="fa fa-bars"></i> Advanced Search  <!--  (<span id="count_total_order" style="color:#000"></span>)-->
                          
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>  <button class="btn btn-primary" onclick="download_excel()" >Download Current Page</button></li>
                        </ul>
                        <div class="clearfix"></div>
                        
                       
                    </div>
                    <div class="x_content">
                        <div class="x_content"  ng-app="advanced_search">
                            <table class="table table-striped table-bordered" ng-controller="advancedSearchController" >
                                <thead>
                                    <tr>
                                        <td style="width: 22%;"><input type="text" class="form-control" id=""order_date" name="order_date"/></td>
                                        <td>
                                            <select style="width:100%"  name="city" ui-select2 id="city"  class="form-control" ng-model="tempData.city"
                                                ng-options="option.district as option.district for option in city_list" 
                                                data-placeholder="Select City">
                                                <option value="" disabled="" ng-show="false"></option>
                                                
                                            </select>
                                        </td>
                                        <td>
                                            <select style="width:100%"  name="service_id" ui-select2  id="service_id" class="form-control" 
                                                ng-model="tempData.service_id" 
                                               
                                                data-placeholder="Select Alliance">
                                                <option value="" disabled="" ng-show="false"></option>
                                                 <option ng-repeat="y in service_list" value="{{y.id}}">{{y.services}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select style="width:100%"  name="current_status" ui-select2  id="current_status" class="form-control" 
                                                ng-model="tempData.current_status" 
                                                ng-options="option1.current_status as option1.current_status for option1 in current_status_list" 
                                                data-placeholder="Select Status1">
                                            <option value="" disabled="" ng-show="false"></option>
                                        </td>
                                        <td>
                                            <select style="width:100%"  name="internal_status" ui-select2  id="internal_status" class="form-control" 
                                                ng-model="tempData.internal_status" 
                                                ng-options="option2.internal_status as option2.internal_status for option2 in internal_status_list" 
                                                data-placeholder="Select Status2">
                                            <option value="" disabled="" ng-show="false"></option>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                            
                            <table id="datatable1" class="table table-striped table-bordered">
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
<script type="text/javascript">
    var ad_table;
    
    $(document).ready(function () {
        $('input[name="order_date"]').daterangepicker({
            locale: {
                format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01") ?>',
            endDate: '<?php echo date("Y/m/t") ?>'
        });
        $('input[name="order_date"]').on('apply.daterangepicker', function (ev, picker) {
           get_data();
        });
        
        ad_table = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": function(d){
                   
                    d.date_range = $('input[name="order_date"]').val();
                    d.city = $("#city option:selected").text();
                    d.service_id = $("#service_id").val();
                    d.current_status =  $("#current_status option:selected").text();
                    d.internal_status = $("#internal_status option:selected").text();
                    d.status =  10;
                    
                 }
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0, 1, 6, 7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
               //$("#count_total_order").text(response.recordsTotal);
            }
    
        });
        
    });
    

    $('select').on('select2:closing', function (evt) {
  
         ad_table.ajax.reload(null, false);  

    });
    
    function download_excel(){
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('datatable1');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');

        var a = document.createElement('a');
        a.href = data_type + ', ' + table_html;
        a.download =   '<?php echo date("d-m-Y");?>-searchedData.xlsx';
        a.click();
    }
    
    
</script>