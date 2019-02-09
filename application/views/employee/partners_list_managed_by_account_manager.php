<script src="<?php echo base_url(); ?>js/invoice_tag.js"></script>
<script src="<?php echo base_url();?>assest/DataTables/Buttons-1.5.1/js/dataTables.buttons.min.js"></script>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_invoice_id" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Partners Managed By Account Manager</strong></h3>
            <hr>
            
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section class="show_invoice_id_data">
                <table class="table table-bordered  table-hover table-striped data" id="partners_list_table"  >
                    <thead>
                        <tr >
                            <th>No</th>
                            <th>Company Name</th>
                            <th>Public Name</th>
<!--                            <th>Company Type</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Pin code</th>
                            <th>Primary Contact Name</th>
                            <th>Email</th>
                            <th>Phone No.</th>-->
                            <th>Account Manager Name</th>
<!--                            <th>Email</th>
                            <th>Phone No.</th>-->
                        </tr>
                    </thead>
                </table>
            </section>
        </div>
    </div>
</div>

 
<script>
   
    
    var partners_list_table = null;
    $(document).ready(function () {
        loaddataTable();
    });
    
       
    function loaddataTable(){
        partners_list_table = $('#partners_list_table').DataTable({
         "processing": true, //Feature control the processing indicator.
         "serverSide": true, //Feature control DataTables' server-side processing mode.
         "order": [[ 1, "asc" ]], //Initial no order.
         "pageLength": 25,
          dom: 'lBfrtip',
         "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
          buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Invoice',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16, 17],
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
                       d.request_type = "partners_managed_by_account_manager",
                       d.group_by = "partners.account_manager_id,partners.id";
                 }

            },

            //Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [0,1,2,3], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
            $("#partners_list_table_filter").addClass("pull-right");
          }

     });
    }
  
</script>
