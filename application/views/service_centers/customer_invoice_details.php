<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

<script src="<?php echo base_url() ?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url() ?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" rel="stylesheet">


<div id="page-wrapper">
    <h2 style="margin-bottom: 25px;">Customer Invoice</h2>
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
    .dt-buttons{
        margin-left:25px;
        margin-top:-1px;
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
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Download Invoice',
                    exportOptions: {
                        columns: [ 0,1,2,3,4,5,6]
                    },
                    title: 'Customer Invoice'
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>apiDataRequest",
                "type": "POST",
                data:{requestType: '<?php echo CUSTOMER_INVOICE_TAG; ?>', crmType:'Vendor'}
                
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