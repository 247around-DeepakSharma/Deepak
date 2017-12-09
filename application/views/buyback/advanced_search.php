<script>
var shop_list_details = [];
</script>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main"  ng-app="advanced_search">
    <div class="clearfix"></div>
    <div class="row" ng-controller="advancedSearchController">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                <form method="post" action="<?php echo base_url();?>buyback/buyback_process/download_order_snapshot" >
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                          <i class="fa fa-bars"></i> Advanced Search  <!--  (<span id="count_total_order" style="color:#000"></span>)-->
                          
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <input type="hidden" value="" name="search_value" id="search_value" value="">
                            <li>  <input type="submit" class="btn btn-primary" value="Download Orders List" ></li>
                        </ul>
                        <div class="clearfix"></div>
                        
                       
                    </div>
                    <div class="x_content">
                        <div class="row" >
                            <div class="col-md-12">
                                <div class="col-md-4 form-group">
                                    <input type="text" placeholder="Order Date" class="form-control" id="order_date" name="order_date"/>
                                </div>
                                <div class="col-md-4 form-group">
                                    <input type="text" placeholder="Delivery Date" class="form-control" id="delivery_date" name="delivery_date"/>
                                </div>
                                <div class="col-md-4 form-group">
                                    <select multiple style="width:100%" name="city[]" ui-select2 id="city"  class="form-control data_change" data-placeholder="Select City">
                                        <option value="" ng-show="false"></option>
                                        <option ng-repeat="y in city_list" value="{{y.district}}">{{y.district}}</option>   
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                
                                <div class="col-md-4 form-group">
                                    <select  style="width:100%" name="cp_id" ui-select2 id="cp_id"  class="form-control data_change" data-placeholder="Select CP">
                                        <option value="" ng-show="false"></option>
                                        <option ng-repeat="y in cp_list" value="{{y.cp_id}}">{{y.cp_name}}</option>   
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <select multiple style="width:100%" name="service_id[]" ui-select2 id="service_id"  class="form-control data_change" data-placeholder="Select Appliance">
                                        <option value="" ng-show="false"></option>
                                        <option ng-repeat="y in service_list" value="{{y.id}}">{{y.services}}</option>   
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <select multiple style="width:100%" name="current_status[]" ui-select2 id="current_status"  class="form-control data_change" data-placeholder="Current Status">
                                        <option value="" ng-show="false"></option>
                                        <option ng-repeat="y in current_status_list" value="{{y.current_status}}">{{y.current_status}}</option>   
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4 form-group">
                                    <select multiple style="width:100%" name="internal_status[]" ui-select2 id="internal_status"  class="form-control data_change" data-placeholder="Internal Status">
                                        <option value="" ng-show="false"></option>
                                        <option ng-repeat="y in internal_status_list" value="{{y.internal_status}}">{{y.internal_status}}</option>   
                                    </select>
                                </div>
                                <div class="col-md-4 form-group" style="margin-top: 8px;">
                                    <label style="margin-right: 10px;"> Is Invoiced:  </label>
                     
                                    <input type="radio" name="invoice" value="Yes"  class="invoice_change" style="margin-right: 10px;"/> Yes
                                    <input type="radio" name="invoice" value="No" class="invoice_change" style="margin-left: 10px;" /> No
                                </div>
                            </div>
                            
                        </div>
                        <hr>
                       </form>
                        <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                            <table id="datatable1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Order ID</th>
                                        <th>Tracking ID</th>
                                        <th>Service Name</th>
                                        <th>Categor/Size</th>
                                        <th>City</th>
                                        <th>Order Date</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th>Exchange Value</th>
                                        <th>CP Charge</th>
                                        <th style="width:200px;">Assign</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="col-md-12 text-center">

                                 <a href="javascript:void(0);" class="btn btn-md  btn-success" onclick="reAssign()"  >ReAssign CP</a>
                            </div>
                        </form>
                    </div>
                   
                </div>
            
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error In Assign</h4>
      </div>
      <div class="modal-body">
         <table class="table table-bordered table-hover table-responsive">
                        <thead>
                        <th>S.No.</th>
                        <th>Order ID</th>   
                        <th>Message</th>   
                        </thead>
                        <tbody  id="error_td">
                           
                        </tbody>
                    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

</div>
<script type="text/javascript">
    var ad_table;
   
    $(document).ready(function () {
        
        $('input[name="order_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear'
            }
        });
        $('input[name="order_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));    
            ad_table.ajax.reload( function ( json ) {
                   create_dropdown();
                 } );

        });
        
        $('input[name="order_date"]').on('cancel.daterangepicker', function (ev, picker) {
             var value = $('input[name="order_date"]').val();
             
             if(value !== ''){
                $(this).val('');
                ad_table.ajax.reload( function ( json ) {
                  create_dropdown();
                } );
             }

        });
        
        $('input[name="delivery_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear'
            }
           
        });
        $('input[name="delivery_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            ad_table.ajax.reload( function ( json ) {
               create_dropdown();
             } );
             
        });

        $('input[name="delivery_date"]').on('cancel.daterangepicker', function(ev, picker) {
             var value1 = $('input[name="delivery_date"]').val();
             
             if(value1 !== ''){
                $(this).val('');

                ad_table.ajax.reload( function ( json ) {
                   create_dropdown();
                 } );
             }
        });
        
        $('#datatable1').on('search.dt', function() {
            var s_value = $('.dataTables_filter input').val();
            $('#search_value').val(s_value);
        }); 
        
       
        $('input:radio[name="invoice"]').change(function() {
  
            ad_table.ajax.reload( function ( json ) {
                create_dropdown();
            });
       });
       
       
        
        ad_table = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_order_details",
                "type": "POST",
                "data": function(d){
                   
                    d.order_date = $('input[name="order_date"]').val();
                    d.delivery_date = $('input[name="delivery_date"]').val();
                    d.city = getMultipleSelectedValues("city");
                    d.service_id = getMultipleSelectedValues("service_id");
                    d.current_status =  getMultipleSelectedValues("current_status");
                    d.internal_status = getMultipleSelectedValues("internal_status");
                    d.invoice = $('input[name="invoice"]:checked').val();
                    d.cp_id = $("#cp_id").val();
                    d.status =  10;
                    
                 }
            },
            "drawCallback": function( settings ) {
               create_dropdown();
            },
                      //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0, 1, 2, 7,9,10,11], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
        
    });
    $('select').on('change', function(){
        
        ad_table.ajax.reload( function ( json ) {
            
                 create_dropdown();
          } );
    });
    
    
    function create_dropdown(){
        
        $(".assign_cp_id").select2({
             allowClear: true
        });
        var option = "<option value='' selected disabled>Select CP</option>";
        
        for(i= 0; i< shop_list_details.length; i++){
            option += "<option value='"+shop_list_details[i]['id']+"' >"+shop_list_details[i]['cp_name']+")</option>";
        }
       // console.log(option);
        $(".assign_cp_id").html(option);
    }
    
    function getMultipleSelectedValues(fieldName){
        fieldObj = document.getElementById(fieldName);
        var values = [];
        var length = fieldObj.length;
        for(var i=0;i<length;i++){
           if (fieldObj[i].selected == true){
               values.push(fieldObj[i].value);
           }
        }
       return values.toString();
    }
    
     
</script>