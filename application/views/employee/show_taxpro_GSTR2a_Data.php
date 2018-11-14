<style>
    #GSTR2a_table_filter{
        text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <h1 class="page-header">GSTR2A Report</h1>
            </div>
            <div class="col-md-12 ">
                <table id="GSTR2a_table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <th>S No.</th>
                        <th>Invoice No.</th>
                        <th>Vendor Name</th>
                        <th>GST No.</th>
                        <th>Invoice Date</th>
                        <th>IGST Amt</th>
                        <th>CGST Amt</th>
                        <th>SGST Amt</th>
                        <th>Total Tax</th>
                        <th>Taxable Amt</th>
                        <th>Invoice Amt</th>
                        <th>Related Invoices</th>
                        <th>Generate CN</th>
                        <th>Reject</th>
                    </thead>
                    <tbody>

                    </tbody>  
                </table>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Remark <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
            </div>
            <div class="modal-body">
                   <div class="row">
                       <input type="hidden" id="gstr2a_table_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reject_remarks">Remark *</label>
                                <textarea class="form-control" id="reject_remarks" name="reject_remarks" placeholder="Enter Reject Remark...."></textarea>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="reject_submit()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#GSTR2a_table").bind("DOMSubtreeModified", function() {
         $('#GSTR2a_table tr a.duplicate_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
    });
    var GSTR2a_datatable;
    $(document).ready(function () {
         
        //datatables
       GSTR2a_datatable = $('#GSTR2a_table').DataTable({
            "processing": true, 
            "serverSide": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [],
            "pageLength": 25,
            dom: 'lBfrtip',
             buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'pending_bookings',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9,10,11],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/accounting/get_gst2ra_mapped_data",
                "type": "POST",
                "data": {}
                
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13], 
                    "orderable": false 
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#GSTR2a_table tr a.duplicate_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
            },
            "drawCallback": function (oSettings, response) {
               $(".invoice_select").select2();
            } 
        });
    });
    
    function reject(id){
          $("#gstr2a_table_id").val(id);
    }
    
    function reject_submit(){
        var id =  $("#gstr2a_table_id").val();
        var remarks = $("#reject_remarks").val();
        if(remarks){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/reject_taxpro_gstr2a',
                data: {id:id, remarks:remarks},
                success: function (data) {
                    console.log(data);
                    if(data==true){
                        GSTR2a_datatable.ajax.reload();
                        $('#myModal').modal('hide');
                    }
                    else{
                        alert("Error in rejecting data.");
                    }
                }
            });
        }
        else{
            alert("Please Enter Reject Remarks...");
        }
    }
    
    function generate_credit_note(id, btn){
        var invoice_id = $("#selected_invoice_"+id).val();
        var parent_inv = $("#selected_invoice_"+id).find(':selected').attr('data-parent-inv');
        var checksum = $(btn).attr("data-checksum");
        var id = $(btn).attr("data-id");
        if(invoice_id){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/update_cn_by_taxpro_gstr2a',
                data: {invoice_id:invoice_id, checksum:checksum, id:id, parent_inv:parent_inv},
                success: function (data) {
                    console.log(data);
                    if(data==true){
                        window.open("<?php echo base_url(); ?>employee/invoice/generate_gst_creditnote/"+invoice_id, '_blank');
                        GSTR2a_datatable.ajax.reload();
                    }
                    else{
                        alert("Error in generating credit note.");
                    }
                }
            });
        }
        else{
            alert("Please Select Invoice");
        }
    }
    
    function check_tax_amount(tax_amount, select){
        var inv_tax = $(select).find(':selected').attr('data-tax');
        console.log('inv_tax '+inv_tax);
        console.log('tax_amount '+tax_amount);
        var considarable_tax1 = tax_amount-10;
        var considarable_tax2 = tax_amount+10;
        if(inv_tax >= considarable_tax1 && inv_tax <= considarable_tax2){ 
            $(select).closest("tr").find("button").attr("disabled", false);
        }
        else{ 
            $(select).closest("tr").find("button").attr("disabled", true);
        }
    }
</script>