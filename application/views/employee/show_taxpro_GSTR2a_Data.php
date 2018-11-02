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
<script>
    var GSTR2a_datatable;
    $(document).ready(function () {
         
        //datatables
       GSTR2a_datatable = $('#GSTR2a_table').DataTable({
            "processing": true, 
            "serverSide": true,
            "order": [],
            "pageLength": 25,
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
            "drawCallback": function (oSettings, response) {
               $(".invoice_select").select2();
            } 
        });
    });
    
    function reject(id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/accounting/reject_taxpro_gstr2a',
            data: {id:id},
            success: function (data) {
                console.log(data);
                if(data==true){
                    GSTR2a_datatable.ajax.reload();
                }
                else{
                    alert("Error in rejecting data.");
                }
            }
        });
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