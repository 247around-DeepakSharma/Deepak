<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
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
                <form name="myForm" class="form-horizontal" id ="courier_serviceable_area_form" novalidate="novalidate" method="post">   
                    <div class="panel-heading" style="background-color:#ECF0F1"><b id="header_line"> Add New Courier Serviceable Area </b></div>
                    <br/><br/><br/><br/>
                    <section>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-3" style="width: 20%;"> Courier Name <span style="color: red;">*</span></label>
                                <div class="col-md-9">
                                    <select class="form-control" id="courier_name" name="courier_name" required="">
                                        <option selected="" disabled="" value="">Select Courier Name</option>
                                        <?php foreach ($courier_details as $value1) { ?> 
                                            <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="excel" class="col-md-5"> Serviceable Area Pin Code <span style="color: red;">*</span></label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" id="serviceble_area_pincode" required="" name="serviceble_area_pincode" placeholder="Pincode">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="courier_serviceable_area_id" id="courier_serviceable_area_id" value="">
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
                    <th>Pin Code</th>
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
    var ourier_serviceble_area_table;
    var time = moment().format('D-MMM-YYYY');

    $(document).ready(function(){
       get_courier_serviceble_area_list();
    });

    $(document).on('click', '#edit_serviceable_area', function(){
        window.scroll(0,0);
        var data = $(this).data("id");
        $("#header_line").html("Edit Courier Service");
        $("#courier_serviceable_area_id").val(data['id']);
        $("#courier_name").val(data['courier_company_name']);
        $("#serviceble_area_pincode").val(data['pincode']);
        $("#submit_btn").val("Update")
    });
    
    
    $(document).on('click', '#serviceable_area_status', function(){
        var data = $(this).data("id");   
            if(confirm("Are you sure?")){
               if(data['id'] !=''){
                  $.ajax({
                       url: "<?php echo base_url(); ?>employee/spare_parts/process_courier_serviceable_area_satus",
                       type: "post",
                       dataType: "json",
                       data: {data},
                       success: function(result) {
                           alert(result['message']);
                           ourier_serviceble_area_table.ajax.reload();
                           $("#messages").html(result['message']).css('color','green');
                       }
                   }); 
               }
            }
    });
    
    
    $(function() {
      $("#courier_serviceable_area_form").validate({
        rules: {
          courier_name: "required",
          serviceble_area_pincode: {
                    required: true,
                    minlength: 6,
                    maxlength: 6,
                    digits: true
                },
        },
        // Specify validation error messages
        messages: {
          courier_name: "<span style='color:red;'>Please enter courier name</span>",
          serviceble_area_pincode:{
                    required: "<span style='color:red;'>Please Enter Your Pin Code!</span>",
                    minlength: "<span style='color:red;'>Your Pin Code Must Be 6 numbers!</span>",
                    maxlength: "<span style='color:red;'>Your Pin Code Must Be 5 numbers!</span>",
                    digits: "<span style='color:red;'>Your Pin Code Must Be 5 numbers!</span>"
                },
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/spare_parts/add_edit_courier_serviceable_area',
                    dataType: "json",
                    data: $('#courier_serviceable_area_form').serialize(),
                    success: function(result) {
                        
                          if(result['message'] == 'Courier serviceable area successfuly added.'){
                              ourier_serviceble_area_table.ajax.reload();
                              $("#header_line").html("Add Courier Service");
                              $('#courier_serviceable_area_form').trigger("reset");
                              $("#courier_name").trigger('change');
                              $("#messages").html(result['message']).css('color','green');
                          }else{
                              if(result['message'] == 'Courier serviceable area successfuly Updated.'){
                                ourier_serviceble_area_table.ajax.reload();
                                $("#header_line").html("Add Courier Service");
                                $('#courier_serviceable_area_form').trigger("reset");  
                                $("#courier_name").trigger('change');
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

   

    function get_courier_serviceble_area_list(){
        ourier_serviceble_area_table = $('#service_centers_consumption').DataTable({
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
                "url": "<?php echo base_url(); ?>employee/inventory/get_courier_serviceable_area_list",
                "type": "POST",
                data: { courier_flag : 1 }
            },
            "deferRender": true       
        });
    }
    
     
    var oldExportAction = function (self, e, ourier_serviceble_area_table, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(ourier_serviceble_area_table, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, ourier_serviceble_area_table, button, config);
            }
            else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, ourier_serviceble_area_table, button, config);
            }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, ourier_serviceble_area_table, button, config);
        }
    };

    var newExportAction = function (e, ourier_serviceble_area_table, button, config) {
        var self = this;
        var oldStart = ourier_serviceble_area_table.settings()[0]._iDisplayStart;

        ourier_serviceble_area_table.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = ourier_serviceble_area_table.page.info().recordsTotal;

            ourier_serviceble_area_table.one('preDraw', function (e, settings) {
                // Call the original action function 
                oldExportAction(self, e, ourier_serviceble_area_table, button, config);

                ourier_serviceble_area_table.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(ourier_serviceble_area_table.ajax.reload, 0);

                // Prevent rendering of the full data to the DOM
                return false;
            });
        });

        // Requery the server with the new one-time export settings
        ourier_serviceble_area_table.ajax.reload();
    };
    
    $('#courier_name').select2({
        placeholder:'Select Courier Name',
        allowClear:true
    });

</script>

