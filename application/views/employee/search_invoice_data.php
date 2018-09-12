<script src="<?php echo base_url();?>assest/DataTables/Buttons-1.5.1/js/dataTables.buttons.min.js"></script>
<!--<script src="<?php echo base_url();?>bower_components/buttons.dataTables/pdfmake.min.js"></script>
<script src="<?php echo base_url();?>bower_components/buttons.dataTables/vfs_fonts.js"></script>-->
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_invoice_id" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Search Invoices</strong></h3>
            <hr>
            <section class="fetch_invoice_id" style="padding-left:5px;">
                <div class="row">
                    <div class="form-inline" style="margin-left: 5px;">
                        <div class="form-group col-md-3">
                            <label>Select Entity Type</label>
                            <select onchange="get_vendor_partner_list()" id="vendor_partner" name="vendor_partner" class="form-control col-md-12" style="width:100%;">
                                 <option value="">Select Entity</option>
                                <option value="partner">Partner</option>
                                <option value="vendor">Vendor</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Select Entity</label>
                            <select name="vendor_partner_id" class="form-control col-md-12" id="vendor_partner_id" style="width:100%;">
                                <option value="">Select Entity</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Select Invoice Date</label>
                            <input name="invoice_date" placeholder="Select invoice date range" class="form-control col-md-12" id="invoice_date" style="width:100%;">
                               
                            </input>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Select Period Date</label>
                            <input name="invoice_period" placeholder="Select invoice period range" class="form-control col-md-12" id="invoice_period_date" style="width:100%;">
                               
                            </input>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Select Invoice Type</label>
                            <select name="invoice_type" class="form-control col-md-12" id="invoice_type" style="width:100%;"> 
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Is Settle Invoice</label>
                            <select name="settle" class="form-control col-md-12" id="settle" style="width:100%;">
                                <option value="2">All</option>
                                <option value="1">Settle</option>
                                <option value="0">Unsettle</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-3">
                             <label>Invoice Remark</label>
                             <input type="text" class="form-control" id="invoice_remarks" placeholder="Invoice Remarks" style="width:100%;">
                        </div>
<!--                        <div class="form-group col-md-3">
                             <label>Invoice Id</label>
                             <input type="text" class="form-control" id="invoice_id" placeholder="Invoice Id" style="width:100%;">
                        </div>-->
                        <div class="form-group col-md-3">
                            <button class="btn btn-success col-md-2" id="get_invoice_id_data" style="width:100%; margin-top: 23px;">Search</button>
                        </div>
                        
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section class="show_invoice_id_data">
                <table class="table table-bordered  table-hover table-striped data" id="datatable"  >
   <thead>
      <tr >
         <th>No</th>
         <th>Party Name</th>
         <th>Invoice Id</th>
         <th>Type</th>
         <th>Bookings/ Parts</th>
         <th>Invoice Period</th>
         <th>Total Invoice</th>
         <th>Service Charges</th>
         <th>Additional Service Charges</th>
         <th>Parts / Stands</th>
         <th>TDS Amount</th>
         <th>Penalty</th>
         <th>GST Amount</th>
         <th>Amount to be Paid By 247Around</th>
         <th>Amount to be Paid By Partner</th>
         <th>Amount Paid</th> 
         <th>Remarks</th> 
         <th>Update</th>
         <th>Resend</th>
         <th>GST CreditNote</th>
         
      </tr>
   </thead>
                </table>
            </section>
        </div>
    </div>
</div>
<script>
    $("#vendor_partner").select2();
    $("#vendor_partner_id").select2();
    var invoice_table = null;
    $(document).ready(function () {
        loaddataTable();
        $('#get_invoice_id_data').click(function () {
            if(invoice_table == null){
                loaddataTable();
            } else {
                invoice_table.ajax.reload(null, false);
            }
            
        });
    });
    
    function loaddataTable(){
        invoice_table = $('#datatable').DataTable({
         "processing": true, //Feature control the processing indicator.
         "serverSide": true, //Feature control DataTables' server-side processing mode.
         "order": [[ 1, "asc" ]], //Initial no order.
         "pageLength": 50,
          dom: 'lBfrtip',
         "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
          buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Invoice',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
         // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url();?>employee/accounting/get_invoice_searched_data",
                type: "POST",
                data: function(d){

                       d.vendor_partner = $("#vendor_partner").val();
                       d.vendor_partner_id = $('#vendor_partner_id').val();
                       d.invoice_date = $("#invoice_date").val();
                       d.invoice_period_date = $("#invoice_period_date").val();
                       d.settle = $("#settle").val();
                       d.invoice_remarks = $("#invoice_remarks").val();
                       d.invoice_type = $("#invoice_type").val();
                       d.invoice_id = $("#invoice_id").val();
                 }

            },

            //Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
            $("#datatable_filter").addClass("pull-right");
            //$("#in_tranist_record").text(response.recordsTotal);
          }

     });
    }
    
    function get_vendor_partner_list(){
        var par_ven = $("#vendor_partner").val();
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                data: {vendor_partner_id: "", invoice_flag: 1},
                success: function (data) {

                    $("#vendor_partner_id").html(data);
                    $("#vendor_partner_id").val("All").change();
                    
            }
        });
    }
    
    $(function() {
        $('#invoice_date').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            autoUpdateInput: false
        });
    });
    
     $(function() {
        $('#invoice_period_date').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            autoUpdateInput: false
        });
    });
    $('#invoice_period_date').on('apply.daterangepicker', function(ev, picker) {

        $('#invoice_period_date').val(picker.startDate.format('YYYY-MM-DD') + '/' + picker.endDate.format('YYYY-MM-DD'));
    });
    
    $('#invoice_date').on('apply.daterangepicker', function(ev, picker) {
        $('#invoice_date').val(picker.startDate.format('YYYY-MM-DD') + '/' + picker.endDate.format('YYYY-MM-DD'));
    });

    get_invoice_type();
    function get_invoice_type(){
        $.ajax({
            method: 'POST',
            data: {},
            url: '<?php echo base_url(); ?>employee/accounting/get_invoice_type',
            success: function (response) {
                //console.log(response);
                $('#invoice_type').html(response);

            }
        });
    }
</script>
