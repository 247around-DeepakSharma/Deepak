<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<div id="page-wrapper">
    <h2>Customer Invoice</h2>
    <table class="table table-bordered  table-hover table-striped data" id="datatable1" >
   <thead>
      <tr >
         <th>No</th>
         <th>Invoice ID</th>
         <th>Booking ID</th>
         <th>Invoice Date</th>
         <th>Basic Charge</th>
         <th>GST Charge</th>
         <th>Customer Paid Charges</th>
      </tr>
   </thead>
   <tbody>
   </tbody>
    </table>
</div>
<style>
    #datatable1_filter{
        float: right;
    }
</style>
<script>

var invoice;
$(document).ready(function () {
    invoice = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>apiDataRequest",
                "type": "POST",
                data:{requestType: '<?php echo CUSTOMER_INVOICE_TAG; ?>', crmType:'Admin'}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,5,6], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
           
          }
            
        });
});
</script>