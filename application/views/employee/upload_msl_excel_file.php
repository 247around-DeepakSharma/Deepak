<style>
    #datatable2_info{
    display: none;
    }
    
    #datatable2_filter{
        text-align: right;
    }
    .select2-container--default .select2-selection--single{
        height: 32px;
    }
    .modal-body{
        padding: 0px;
    }
</style>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
            <?php 
            if(!empty($this->session->userdata('fail'))){ ?>
               
                 <div class="alert alert-danger">
                 <strong>Failed!</strong> Click View Error button to see the details <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal"> View Error</a>
                </div>
            <?php }else if(!empty($this->session->userdata('details'))){ ?>
                   <div class="alert alert-dangers">
                   <strong>Success!</strong> Click See details button to see the details because some data may not be updated
                   <?php if(!empty($this->session->userdata('details'))){ ?>
                             <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal">See Details</a>
                       <?php  } ?>
                  </div>
           <?php  }
            ?>
            <h1 class="page-header">
               MSL Excel Upload
               <a href="<?php echo "https://s3.amazonaws.com/". BITBUCKET_DIRECTORY.'/vendor-partner-docs/Upload-msl-excel-sample.xlsx' ?>" name="download-sample-file" class="btn btn-primary" style="float:right;">Download Sample File</a>
            </h1>
          <div id="show_both">
              <form method="POST" id="msl_bulk_upload_form"  enctype="multipart/form-data"  action="<?php echo base_url(); ?>file_upload/process_upload_file" data-preview="true" >
                  <div role="tabpanel" class="tab-pane active" id="onMsl">
                      <div class="row">
                          <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="x_panel" style="margin-top: 0px;">

                                  <div class="x_content">
                                      <div class="loader"></div>                            
                                      <div class="form-box">
                                          <div class="static-form-box">
                                              <div class="form-group">
                                                  <label class="col-xs-4 col-sm-2 control-label">Partner *</label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <select class="form-control" name="partner_id" id="partner_id" required=""></select>
                                                      <label for="partner_id" class="error"></label>
                                                  </div>
                                                  <label class="col-xs-4 col-sm-2 control-label">247around Warehouses *</label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <select class="form-control" name="wh_id" id="wh_id" required="" >
                                                          <option value="" disabled="">Select Warehouse</option>
                                                      </select>
                                                      <label for="wh_id" class="error"></label>
                                                  </div>
                                              </div>
                                              <div class="form-group">                                            
                                                  <label class="col-xs-4 col-sm-2 control-label">Invoice Date *</label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <input placeholder="Select Date" type="text" class="form-control"  readonly=""  onkeydown="return false;"  name="dated" id="dated" autocomplete="off" required="" />
                                                      <label for="dated" class="error"></label>
                                                      <input type="hidden" name="invoice_tag" value="<?php echo MSL; ?>">
                                                      <input type="hidden" name="transfered_by" value="<?php echo MSL_TRANSFERED_BY_PARTNER; ?>">
                                                  </div>
                                                  <label class="col-xs-2 control-label">Invoice Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Please make sure invoice number does not contain '/'. You can replace '/' with '-' "><i class="fa fa-info"></i></span></label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <input type="text" placeholder="Enter Invoice Number" class="form-control" name="invoice_id" id="invoice_id" required="" onblur="check_invoice_id(this.id)"/>
                                                      <label for="invoice_id" class="error"></label>
                                                  </div>
                                              </div>
                                              <div class="form-group">               
                                                  <label class="col-xs-2 control-label">Invoice Amount * </label>
                                                  <div class="col-xs-4">
                                                      <input placeholder="Enter Invoice Value" type="text" class="form-control allowNumericWithDecimal" name="invoice_amount" id="invoice_amount" required=""/>
                                                      <label for="invoice_amount" class="error"></label>
                                                  </div>
                                                  <label class="col-xs-4 col-sm-2 control-label">Invoice File *  <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Only pdf files are allowed and file size should not be greater than 5 MB."><i class="fa fa-info"></i></span></label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <input type="file" class="form-control" name="invoice_file" id="invoice_file" required="" accept="application/pdf"/>
                                                      <label for="invoice_file" class="error"></label>
                                                  </div>
                                              </div>
                                              <div class="form-group">                                            
                                                  <label class="col-xs-2 control-label">AWB Number *</label>
                                                  <div class="col-xs-4">
                                                      <input placeholder="Enter AWB Number" type="text" class="form-control" name="awb_number" id="despatch_doc_no" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 13" />
                                                      <label for="despatch_doc_no" class="error"></label>
                                                  </div>
                                                  <?php
                                                  if (form_error('courier_name')) {
                                                      echo 'has-error';
                                                  }
                                                  ?>
                                                  <label class="col-xs-2 control-label">Courier Name *</label>
                                                  <div class="col-xs-4">
      <!--                                                <input placeholder="Enter Courier Name" type="text" class="form-control" name="courier_name" id="courier_name" required=""/>-->
                                                      <select class="form-control" id="courier_name" name="courier_name" id="courier_name" required="">
                                                          <option selected="" disabled="" value="">Select Courier Name</option>
                                                          <?php foreach ($courier_details as $value1) { ?> 
                                                              <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                                          <?php } ?>
                                                      </select>
                                                      <label for="courier_name" class="error"></label>
                                                      <?php echo form_error('courier_name'); ?>
                                                  </div>
                                              </div>
                                              <div class="form-group">                                            
                                                  <label class="col-xs-2 control-label">Courier Shipment Date</label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <input placeholder="Select Courier Shipment Date" readonly=""  onkeydown="return false;" type="text" class="form-control" name="courier_shipment_date" id="courier_shipment_date" autocomplete="off"/>
                                                  </div>
                                                  <label class="col-xs-2 control-label">Courier File</label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <input type="file" class="form-control" name="courier_file" id="courier_file"/>
                                                      <br>
                                                  </div>
                                              </div>
                                              <div class="form-group">
                                                  <label class="col-xs-2 control-label">From GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="Your GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                                  <div class="col-xs-4">
                                                      <select class="form-control" name="from_gst_number" id="from_gst_number" required="">
                                                          <option value="" disabled="">Select From GST Number</option>
                                                      </select>
                                                      <label for="from_gst_number" class="error"></label>
                                                  </div>
                                                  <label class="col-xs-2 control-label">To GST Number * <span class="badge badge-info" data-toggle="popover" data-trigger="hover" data-content="247around GST Number print on invoice"><i class="fa fa-info"></i></span></label>
                                                  <div class="col-xs-8 col-sm-4">
                                                      <select class="form-control" name="to_gst_number" id="to_gst_number" required="">
                                                          <option value="" disabled="">Select To GST Number</option>
                                                      </select>
                                                      <label for="to_gst_number" class="error"></label>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-xs-2 control-label">TCS Rate </label>
                                                  <div class="col-xs-4">
                                                      <input type="number" step=".1" name="tcs_rate" class="form-control" id="tcs_rate" value="0.00">
                                                      <label for="from_gst_number" class="error"></label>
                                                  </div>
                                             <label class="col-xs-2 control-label"> Upload MSL File</label>
                                              <div class="col-md-4">
                                                  <div class="form-group">
                                                      <input type="file" class="form-control" required id="msl_excel" name="file"  accept=".xls,.xlsx" />
                                                  </div>
                                              </div>
                                              <input type="hidden" name="file_type" value="<?php echo UPLOAD_MSL_EXCEL_FILE; ?>" />
                                          </div>
                                          <div class="form-group">
                                              <div class="col-md-12">
                                                  <div class="form-group" style="text-align: center; margin-top: 10px;">
                                                      <input type="hidden" class="form-control" id="wh_name"  name="wh_name" value=""/>
                                                      <input type="hidden" class="form-control" id="partner_name"  name="partner_name" value=""/>
                                                      <input type="hidden" class="form-control" id="is_wh_micro"  name="is_wh_micro" value=""/>
                                                      <button type="submit" class="btn btn-small btn-success" id="search">Upload</button> 
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>        
              </form>

              <div id="myModal" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                      <!-- Modal content-->
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title">Excel Entries Not Updated</h4>
                          </div>
                          <div class="modal-body">
                              <?php
                              if (!empty($this->session->userdata('fail'))) {
                                  echo $this->session->userdata('fail');
                              } else {
                                  echo $this->session->userdata('details');
                              }
                              ?>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                      </div>

                  </div>
              </div> 

<!--            <a href="<?php //echo base_url(); ?>BookingSummary/download_latest_file/appliance" class="col-md-2"><button class="btn btn-success btn-sm">Download Latest File</button></a>-->
            <div class="col-md-12" style="margin-top:20px;">
                <h3>File Upload History</h3>
                <table id="datatable2" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Download</th>
                            <th>Uploaded By</th>
                            <th>Uploaded Date</th>
                            <th>Status</th>
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
<div class="modal" id="msl_upload_preview">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Verify MSL Details</h2>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped table-hover" id="msl_preview_table">
                        <thead>
                            <tr>
                                <th>Appliance</th>
                                <th>Part Code</th>
                                <th>Quantity</th>
                                <th>Basic Price</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" id="verify_and_upload_msl" class="btn btn-color" data-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->session->unset_userdata('fail');
    $this->session->unset_userdata('details');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
<script>
/*@desc: Function is used to load partner list */

    $('#partner_id').select2({
        placeholder:'Select Partner',
        width : '100%'
    });
    
    $('#wh_id').select2({
        placeholder:"Select Warehouse"
    });
    
    get_partner_list();
    function get_partner_list(){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>employee/partner/get_partner_list',
                data:{is_wh:true},
                success: function (response) {
                    $("#partner_id").html(response);
                }
            });
     }
     
     $("#partner_id").on('change',function(){
        var partner_id = $("#partner_id").val();
            get_vendor('1',partner_id);
            get_partner_gst_number(partner_id);
            get_247around_wh_gst_number(partner_id);
        var partner_name = $('#partner_id option:selected').text();
            $('#partner_name').val(partner_name);
   });
   
   $("#wh_id").on('change',function(){
        var wh_name = $('#wh_id option:selected').text();
            $('#wh_name').val(wh_name);
        var is_micro = $("#wh_id").find(':selected').attr('data-warehose');
            $("#is_wh_micro").val(is_micro);
    });
    
    function get_vendor(is_wh,partner_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
            data:{is_wh:is_wh,partner_id:partner_id},
            success: function (response) {
                $('#wh_id').html(response);                
            }
        });
    }


    function get_partner_gst_number(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/inventory/get_partner_gst_number',
            data:{partner_id:partner_id},
            success: function (response) {
                $("#from_gst_number").html(response);
            }
        });
    }
    
   function get_247around_wh_gst_number(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/inventory/get_247around_wh_gst_number',
            data:{partner_id:partner_id},
            success: function (response) {
                $("#to_gst_number").html(response);
            }
        });
   }

    $('#msl_excel').change(function () {
        $("#search").prop("disabled", false);
        var ext = this.value.match(/\.(.+)$/)[1];
        switch (ext) {
            case 'xlsx':
                $('#search').attr('disabled', false);
                break;
            default:
                alert('This is not an allowed file type.');
                 $('#search').attr('disabled', true);
                this.value = '';
                $("#msl_bulk_upload_form").data("preview",true);
        }
    });

    $('#dated').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
       minDate: function(){
            var today = new Date();
            var yesterday = new Date();
            yesterday.setDate(today.getDate() - 3);
            return yesterday;
        }(), 
        maxDate:new Date(),//'today',
        setDate: new Date(),
        locale:{
            format: 'DD/MM/YYYY'
        }
    });

    $('#dated').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY'));
    });

    $('#dated').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#courier_shipment_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate: function(){
            var today = new Date();
            var yesterday = new Date();
            yesterday.setDate(today.getDate() - 3);
            return yesterday;
        }(), //date_before_15_days,
        maxDate: false,//'today',
        setDate: new Date(),
        locale:{
            format: 'YYYY-MM-DD'
        }
    });

    $('#courier_shipment_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    $('#courier_shipment_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    $("#invoice_file").change(function(){

            var f = this.files[0];
            var flag=false;
            var ext = this.value.match(/\.(.+)$/)[1];
            switch (ext) {
            case 'pdf':
            case 'PDF':
                 flag=true;
            }
            //here I CHECK if the FILE SIZE is bigger than 5 MB (numbers below are in bytes)
            if (f.size > 5242882 || f.fileSize > 5242882 || flag==false)
            {
               //show an alert to the user
               swal("Error!", "Allowed file size exceeded. (Max. 5 MB) and must be PDF", "error");
               //reset file upload control
               this.value = null;
            }

    });
    
    
    

 
    var table1;
 

    $(document).ready(function () {

        upload_file_history();
 
        //datatables
        $("#msl_bulk_upload_form").submit(function(){
           if($(this).data("preview") == true){
               var file = $("#msl_excel")[0].files[0];
               console.log(file);
               var xl2json = new ExcelToJSON();
               xl2json.parseExcel(file);
               return false;
           }else{
               $("#msl_bulk_upload_form").data("preview",true);
           }
        });
        $("#verify_and_upload_msl").click(function(){
            $("#search").prop("disabled", true);
            $('#search').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $("#msl_bulk_upload_form").submit();
        });
        $("#msl_upload_preview").on('hide.bs.modal', function () {
            $("#msl_bulk_upload_form").data("preview",true);
        });
    });
    
    function upload_file_history(){ 
        table1 = $('#datatable2').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            lengthMenu: [[5,10, 25, 50], [5,10, 25, 50]],
            pageLength: 5,
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function(d){
                    d.file_type = '<?php echo UPLOAD_MSL_EXCEL_FILE; ?>';
                    d.partner_id = $("#partner_id").val();
                }
            },
             //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
    }
    
    var ExcelToJSON = function() {
    
    var invoice_amount = $("#invoice_amount").val();
        this.parseExcel = function(file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var data = e.target.result;
                var workbook = XLSX.read(data, {
                    type: 'binary'
                });
                workbook.SheetNames.forEach(function(sheetName) {
                // Here is your object
                    var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                    //var json_object = JSON.stringify(XL_row_object);
                    //console.log(JSON.parse(json_object));
                    console.log(XL_row_object);
                  var tcs_rate = $("#tcs_rate").val();
                  
                    var html = "";
                    var total_price = 0 ;
                    for(var i in XL_row_object){
                         console.log(XL_row_object[i]['GST Rate']);
                        var parts_total_price = 0;
                        var regexp = /^[0-9]+([,.][0-9]+)?$/g;
                        if ((XL_row_object[i]['Basic Price'].trim() > 0) && (regexp.test(XL_row_object[i]['Basic Price'].trim()))) {
                           
                        }else{
                            alert("Excel cell value basic price is wrong."); 
                            return false;
                        }
                                               
                        var regular_exp = /^[0-9]+$/;
                        if ((XL_row_object[i]['Quantity'].trim() > 0) && (regular_exp.test(XL_row_object[i]['Quantity'].trim()))) {
                           
                        }else{
                            alert("Excel cell value quantity is wrong."); 
                            return false;
                        }
                        
                        if ((XL_row_object[i]['GST Rate'] === '1.5')
                            || (XL_row_object[i]['GST Rate'] === '2')
                            || (XL_row_object[i]['GST Rate'] === '5')
                            || (XL_row_object[i]['GST Rate'] === '12')
                            || (XL_row_object[i]['GST Rate'] === '18')
                            || (XL_row_object[i]['GST Rate'] === '28')) {
                           
                        }else{
                            alert("Excel cell value GST rate is wrong."); 
                            return false;
                        }
                        
                        if ((XL_row_object[i]['HSN Code'].trim()!= '') && (XL_row_object[i]['HSN Code'] != undefined) && XL_row_object[i]['HSN Code'] > 0) {
                           
                        }else{
                            alert("Excel cell value HSN code is wrong."); 
                            return false;
                        }

                        if((XL_row_object[i]['Basic Price'].trim() > 0) && 
                           (XL_row_object[i]['Quantity'].trim() > 0) &&
                           (XL_row_object[i]['GST Rate'].trim() > 0) &&
                           (XL_row_object[i]['Basic Price'] != undefined)  && 
                           (XL_row_object[i]['Quantity'] != undefined) && 
                           (XL_row_object[i]['GST Rate'] != undefined)){
                            var total_part_basic = (Number(XL_row_object[i]['Quantity']) * Number(XL_row_object[i]['Basic Price']));
                            total_basic = total_part_basic;
                            var tax_value = ( total_part_basic * Number(XL_row_object[i]['GST Rate'])/100);
                            total_part_basic = (Number(total_part_basic) + Number(tax_value)).toFixed(2);
                        }else{
                            alert("Excel cell value is going null,empty or wrong."); 
                            return false;
                        }
                                                
                        html += "<tr>";
                        html += "<td>"+ XL_row_object[i]['Appliance'] +"</td>";
                        html += "<td>"+ XL_row_object[i]['Part Code'] +"</td>";
                        html += "<td>"+ XL_row_object[i]['Quantity'] +"</td>";
                        html += "<td>"+ total_basic +"</td>";
                        html += "<td>"+ XL_row_object[i]['HSN Code'] +"</td>";
                        html += "<td>"+ XL_row_object[i]['GST Rate'] +"</td>";
                        html += "</tr>";
                        
                        total_price = (Number(total_price) + Number(total_part_basic)).toFixed(2);   
                    }
                    
                     var tcs_rate_value = ( total_price * Number(tcs_rate)/100);
                        with_tcsrate_price = (Number(total_price) + Number(tcs_rate_value)).toFixed(2);  
                        html += "<tr>";
                        html += "<td colspan='4'></td>";
                        html += "<td><b>Total Price:</b></td>";
                        html += "<td style='flot:right;'><b>"+ with_tcsrate_price +"</b></td>";
                        html += "</tr>";
                     if(parseInt(invoice_amount) != parseInt(with_tcsrate_price)){
                         alert("Amount of invoice does not match with total price "+with_tcsrate_price);
                         return false;
                     }else{   
                    $("#msl_preview_table tbody").empty().html(html);
                    $("#msl_bulk_upload_form").data("preview",false);
                    $("#msl_upload_preview").modal();
                    //jQuery( '#xlx_json' ).val( json_object );
                   }
                })
            };

            reader.onerror = function(ex) {
                console.log(ex);
            };

            reader.readAsBinaryString(file);
        };
    };
    
    function check_invoice_id(id, isOnBooking){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){
            
            if( invoice_id.indexOf('/') !== -1 ){
                $('#'+id).css('border','1px solid red');
                if(isOnBooking){
                     $('#on_submit_btn').attr('disabled',true);
                } else {
                     $('#submit_btn').attr('disabled',true);
                }
                
                alert("Use '-' in place of '/'");
            }
            else{
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>check_invoice_id_exists/'+invoice_id,
                    data:{is_ajax:true},
                    success:function(res){
                        //console.log(res);
                        var obj = JSON.parse(res);
                        if(obj.status === true){
                            $('#'+id).css('border','1px solid red');
                            if(isOnBooking){
                               $('#on_submit_btn').attr('disabled',true);
                            } else {
                               $('#submit_btn').attr('disabled',true);
                            }
                            alert('Invoice number already exists');
                        }else{
                            $('#'+id).css('border','1px solid #ccc');
                            if(isOnBooking){
                              $('#on_submit_btn').attr('disabled',false);
                            } else {
                               $('#submit_btn').attr('disabled',false);
                            }
                            
                        }
                    }
                });
            }
        }
    }
</script>
 