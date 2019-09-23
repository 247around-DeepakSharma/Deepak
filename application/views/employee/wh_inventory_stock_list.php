<script src="<?php echo base_url();?>js/base_url.js"></script>
<script src="<?php echo base_url();?>js/return_new_parts.js?v=<?=mt_rand()?>"></script>
<style>
    #inventory_stock_table_filter{
        text-align: right;
    }
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
   #total_stock{
        font-size: 14px;
    }
    .pull-right{
        padding: 0 0 0 19px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Warehouse Spare Parts Inventory <span id="total_stock"></span> 
                        <span class="pull-right"><input type="button" id="sellItem" name="sellItem" class="btn btn-primary btn-md" onclick="open_selected_parts_to_return()" value="Return new Parts (0)"></span>
                        <span class="pull-right"><input type="button" id="micro_warehouse" class="download_stock  btn btn-primary btn-md" value="Download Micro-Warehouse Stock"></span>
                        <span class="pull-right"><input type="button" id="warehouse" class="download_stock btn btn-primary btn-md" value="Download Warehouse Stock"></span>
                    </h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>                                    
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="service_id">
                                            <option value="" disabled="">Select Appliance</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">With Out of Stock</label>
                                    </div>
                                    <button class="btn btn-success col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="inventory_stock_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Parts Number</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                    <th>Pending Stock</th>
                                    <th> SF Basic Price</th>
                                    <th>GST Rate</th>
                                    <th>Total Price</th>
                                    <th>  Customer  Total</th>
                                    <?php if($this->session->userdata('userType') == "employee") { ?>
                                        <th>Return Qty</th>
                                        <th>Add</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal start-->
        <div id="modal_data" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="open_model"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal end -->
        
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" style="min-width:1400px;">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Return New Parts</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="courier_model_form" action="javascript:void(0)" method="post" novalidate="novalidate">
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="awb" class="col-md-4">AWB *</label>
                                    <div class="col-md-8">
                                        <input type="text" onblur="check_awb_exist()" class="form-control"  id="awb" name="awb" placeholder="Please Enter AWB" required>
                                        <input type="hidden" class="form-control"  id="agent_type" name="agent_type"  value="247Around">
                                        <input type="hidden"  class="form-control"  id="agent_id" name="agent_id" value="<?php echo $this->session->userdata('id');?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="courier_name_" class="col-md-4">Courier Name *</label>
                                    <div class="col-md-8">
                                        
                                        <select class="form-control"  name="courier_name" name="courier_name" required>
                                            <option selected="" disabled="" value="">Select Courier Name</option>
                                            <?php foreach ($courier_details as $value1) { ?> 
                                                <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="courier" class="col-md-4">Courier Price *</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control"  id="courier_price" name="courier_price" placeholder="Please Enter Courier Price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="shipped_date" class="col-md-4">Courier Shipped Date *</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="shipped_date" name="shipped_date" placeholder="Please enter Shipped Date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="shippped_courier_pic" class="col-md-4">Courier Pic *</label>
                                    <div class="col-md-8">
                                        <input type="hidden" class="form-control"  id="exist_courier_image" name="exist_courier_image" >
                                        <input type="file" class="form-control"  id="shippped_courier_pic" name="shippped_courier_pic" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="from_gst_number" class="col-md-4">To GST Number *</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="from_gst_number" required>
                                            <option selected disabled value="">Select from GST number</option>
                                            <?php
                                            foreach ($from_gst_number as $gst_numbers => $gst_number) {
                                            ?>
                                            <option value="<?php echo $gst_number['id']  ?>"><?php echo $gst_number['state']." - ".$gst_number['gst_number'] ?></option>
                                            <?php    
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
<!--                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="shippped_courier_pic" class="col-md-4">Courier Address </label>
                                    <div class="col-md-8">
                                        <a href="javascript:void(0)" onclick="print_courier_address()">Print Courier Addess</a>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <div class='row' id='receiver_entity' style='display:none;'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="courier" class="col-md-4">Partner/Warehouse *</label>
                                    <div class="col-md-8">
                                        <input type="radio" class='receiver_type' id="radio_partner" name="receiver_type" value='0' checked>&nbsp;&nbsp;Partner
                                        <input type="radio" class='receiver_type' id="radio_wh" name="receiver_type" value='1' style="margin-left:10px;">&nbsp;&nbsp;Warehouse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id='to_wh' style='display:none;'>
                                <div class='form-group'>
                                    <label for="to_wh_id" class="col-md-4">Warehouse *</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="to_wh_id">
                                            <option value="" disabled="">Select Warehouse</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-center">
                            <span id="same_awb" style="display:none">This AWB already used same price will be added</span>
                    </div>
                    <br/>
                  <table id="return_new_parts_data" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Basic Price</th>
                                    <th>GST Rate</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Remove</th>
                                    
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submit_courier_form" onclick="return_new_parts()" >Return New Parts</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal" onclick="sellItem.disabled=false" >Close</button>
                </div>
              </div>

            </div>
         </div>
</div>
<script>
    $("#shipped_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
    var inventory_stock_table;
    var is_admin_crm = false;
    var time = moment().format('D-MMM-YYYY');
    $(document).ready(function () {
        $('#wh_id,#to_wh_id').select2({
            placeholder:"Select Warehouse"
        });
        $('#service_id').select2({
            allowClear: true,
            placeholder: 'Select Appliance'
        });
        
        get_partner();        
        get_inventory_list();
    });
    
    $('#get_inventory_data').on('click',function(){
        var wh_id = $('#wh_id').val();
        var partner_id = $('#partner_id').val();
        if(wh_id && partner_id){
            is_admin_crm = true;
            returnItemArray = [];
            $("#sellItem").val("Return new Parts (0)");
            inventory_stock_table.ajax.reload( function ( json ) { 
            $("#total_stock").html('Total Stock (<i>'+json.stock+'</i>)').css({"font-size": "14px;", "color": "#288004;"});
        } );
        }else{
            alert("Please Select Warehouse and Partner Both");
        }
    });
    
    function get_inventory_list(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2,3,4, 5,6,7,8]
                    },
                    title: 'stock_details_'+time,
                    action: newExportAction
                },
            ],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            
            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_inventory_stocks_details",
                type: "POST",
                data: function(d){
                    
                    var entity_details = get_entity_details();
                    d.receiver_entity_id = entity_details.receiver_entity_id,
                    d.receiver_entity_type = entity_details.receiver_entity_type,
                    d.sender_entity_id = entity_details.sender_entity_id,
                    d.sender_entity_type = entity_details.sender_entity_type,
                    d.service_id = entity_details.service_id,
                    d.is_show_all = entity_details.is_show_all_checked,
                    d.is_admin_crm = is_admin_crm;
                }
            }
        });
    }
    
    function get_entity_details(){
        var data = {
            'receiver_entity_id': $('#wh_id').val(),
            'receiver_entity_type' : '<?php echo _247AROUND_SF_STRING; ?>',
            'sender_entity_id': $('#partner_id').val(),
            'sender_entity_type' : '<?php echo _247AROUND_PARTNER_STRING; ?>',
            'service_id': $('#service_id').val(),
            'is_show_all_checked':$('#show_all_inventory:checked').val()
        };
        
        return data;
    }
    
    function get_partner(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success:function(response){
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    }
    
    function get_vendor(partner_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
            data:{'is_wh' : 1,partner_id:partner_id},
            success: function (response) {
                $('#wh_id').html(response);
            }
        });
    }
    
    function get_wh(partner_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
            data:{is_wh:1,partner_id:partner_id},
            success: function (response) {               
                $('#to_wh_id').html(response);
            }
        });
    }
    
    $('#partner_id').on('change',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            get_appliance(partner_id);
            get_vendor(partner_id);
            get_wh(partner_id);
        }else{
            alert('Please Select Partner');
        }
    });
    
    $('#wh_id').on('change',function(){
        var wh_type = $("#wh_id").find(':selected').attr('data-warehose');
        if(wh_type){
            $('#receiver_entity').css('display','block');
            if(wh_type == 1) {
                $('#receiver_entity').css('display','none');
            }
        }else{
            alert('Please Select Warehouse');
        }
    });
    
    $('.receiver_type').on('change',function(){
        var receiver = $('input[type=radio]:checked').attr('id');
        if(receiver){
            $('#to_wh').css('display','none');
            if(receiver == 'radio_wh') {
                $('#to_wh').css('display','block');
            }
        }else{
            alert('Please Select Partner or Warehouse');
        }
    });
    
    function get_appliance(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
            data:{partner:partner_id},
            success: function (response) {
                $('#service_id').html(response);
            }
        });
    }
    
    var oldExportAction = function (self, e, inventory_stock_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(inventory_stock_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, inventory_stock_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, inventory_stock_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, inventory_stock_table, button, config);
        }
    };

    var newExportAction = function (e, inventory_stock_table, button, config) {
        var self = this;
        var oldStart = inventory_stock_table.settings()[0]._iDisplayStart;

        inventory_stock_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = inventory_stock_table.page.info().recordsTotal;

            inventory_stock_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, inventory_stock_table, button, config);

                inventory_stock_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(inventory_stock_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        inventory_stock_table.ajax.reload( function ( json ) {
           $("#total_stock").html('Total Stock(<i>'+json.stock+'</i>)').css({"font-size": "14px", "color": "#288004;"});
        } );
    };
    
    $(".download_stock").click(function(){
    var req_type = $(this).attr("id");
    if(req_type !=''){
        $("#"+req_type).html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        $.ajax({
              type: 'POST',
              url: '<?php echo base_url(); ?>employee/inventory/download_warehouse_stock_data',
              data: {request_type : req_type , is_access_to_sf_price : 1 },
              success: function (data) {
                  $("#"+req_type).html("Download").attr('disabled',false);
                  var obj = JSON.parse(data); 
                  if(obj['status']){
                      window.location.href = obj['msg'];
                  }else{
                      alert('File Download Failed. Please Refresh Page And Try Again...')
                  }
              }
          });
    }
        
    });

</script>