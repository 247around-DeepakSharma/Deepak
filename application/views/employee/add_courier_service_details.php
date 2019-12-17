<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->

<style>
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
    
    .dataTables_filter{
        float: right;
        margin-top: 10px;
        margin-right: 5px;
    }
    
    .dt-buttons{
        margin-top: 10px;
    }
        
</style>

<div id="page-wrapper">
    <div class="row">
        <div class="clear" style="margin-top:30px;"></div>
        <p style="text-align: center; font-size: 18px;" id="messages"></p>
        <div id="container-4" style="display:block;padding-top: 0px;" class="form_container panel-body">
            <div  class = "panel panel-info">
                <form name="myForm" class="form-horizontal" id ="courier_service_form" novalidate="novalidate" method="post">   
                    <div class="panel-heading" style="background-color:#ECF0F1"><b id="header_line"> Add Courier Service </b></div>
                    <br/><br/><br/><br/>
                    <section>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-3" style="width: 20%;"> Courier Name <span style="color: red;">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="courier_name" required="" name="courier_name" placeholder="Please Enter Courier Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-3" style="width: 20%;"> Courier Code <span style="color: red;">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="courier_code" required="" name="courier_code" placeholder="Example : GATI Courier, Courier Code is gati-kwe">
                                    <p style="color: red;">Courier code should be match with <strong>TrackingMore</strong> courier services. </p>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="courier_services_id" id="courier_services_id" value="">
                            <input type="Submit" value="Submit" class="btn btn-primary" id="submit_btn">
                        </div>
                    </section>
                    <div class="clear clear_bottom"></div>
                </form>
            </div>
        </div>
    </div>
    <div class="inventory-table">
            <input type="hidden" id="inventory_set_id" value="">
            <table class="table table-bordered table-hover table-striped" id="service_centers_consumption">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Courier Name</th>
                        <th>Courier Code</th>
                        <th>Created Date</th>
                        <th>Edit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
</div>

<script type="text/javascript">
    var courier_service_table;
    var time = moment().format('D-MMM-YYYY');

    $(document).ready(function(){
       get_inventory_list();
    });

    $(document).on('click', '#edit_courier_service', function(){
        var data = $(this).data("id");
        $("#header_line").html("Edit Courier Service");
        $("#courier_services_id").val(data['id']);
        $("#courier_name").val(data['courier_name']);
        $("#courier_code").val(data['courier_code']);
        $("#submit_btn").val("Update")
    });
    
    
    $(document).on('click', '#manage_courier_status', function(){
        var data = $(this).data("id");
            if(confirm("Are you sure?")){
               if(data['id'] !=''){
                  $.ajax({
                       url: "<?php echo base_url(); ?>employee/spare_parts/manage_courier_service_satus",
                       type: "post",
                       data: {data},
                       success: function(result) {
                           courier_service_table.ajax.reload();
                           $("#messages").html(result['message']).css('color','green');
                       }
                   }); 
               }
            }
    });
    
    
    $(function() {
      $("#courier_service_form").validate({
        rules: {
          courier_name: "required",
          courier_code: "required",
        },
        // Specify validation error messages
        messages: {
          courier_name: "<span style='color:red;'>Please enter courier name</span>",
          courier_code: "<span style='color:red;'>Please enter courier code</span>",
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/spare_parts/manage_courier_service',
                    dataType: "json",
                    data: $('#courier_service_form').serialize(),
                    success: function(result) {
                        
                          if(result['message'] == 'Courier service successfuly added.'){
                              courier_service_table.ajax.reload();
                              $("#header_line").html("Add Courier Service");
                              $('#courier_service_form').trigger("reset");
                              $("#messages").html(result['message']).css('color','green');
                          }else{
                              if(result['message'] == 'Courier service successfuly Updated.'){
                                courier_service_table.ajax.reload();
                                $("#header_line").html("Add Courier Service");
                                $('#courier_service_form').trigger("reset");  
                                $("#messages").html(result['message']).css('color','green');
                              }else{
                             $("#messages").html(result['message']).css('color','red');
                             }
                          }
                    }
                });
                return false;
          form.submit();
        }
      });

    });

   

    function get_inventory_list(){
        courier_service_table = $('#service_centers_consumption').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3]
                    },
                    title: 'service_centers_consumption_'+time,
                    action: newExportAction
                },
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [[0, 'asc']], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_courier_service_list",
                "type": "POST",
                data: { courier_flag : 1 }
            },
            "deferRender": true       
        });
    }
    
     
    var oldExportAction = function (self, e, courier_service_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(courier_service_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, courier_service_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, courier_service_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, courier_service_table, button, config);
        }
    };

    var newExportAction = function (e, courier_service_table, button, config) {
        var self = this;
        var oldStart = courier_service_table.settings()[0]._iDisplayStart;

        courier_service_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = courier_service_table.page.info().recordsTotal;

            courier_service_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, courier_service_table, button, config);

                courier_service_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(courier_service_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        courier_service_table.ajax.reload();
    };
    
   

</script>

