<style>
    #engineer_history_datatable_filter{
        text-align: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h1>Engineers History</h1>
        <br>
        <div style="margin-bottom: 20px;">
 

        </div>


        <table  class="table table-striped table-bordered" id="engineer_history_datatable">
            <thead>
            <tr>
          	<th>S.No</th>
            <th>Service Center Name</th>
          	<th>Engineer Name</th>
            <th>Booking ID</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>


        
      </div>
    </div>
</div>      
<?php if($this->session->userdata('update_success')) {$this->session->unset_userdata('update_success');} ?>

<script>
    var engineer_datatable = "";
    engineer_datatable = $('#engineer_history_datatable').DataTable({
        processing: true, //Feature control the processing indicator.
        serverSide: true, //Feature control DataTables' server-side processing mode.
        order: [], //Initial no order.
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        // Load data for the table's content from an Ajax source
        dom: 'lBfrtip',
        buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'engineers_history',
                    exportOptions: {
                       columns: [0,1,2,3],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
        ajax: {
            url: "<?php echo base_url(); ?>employee/engineer/get_engineer_history/<?php echo $engineer; ?>",
            type: "POST",
            data: {}
        },
        //Set column definition initialisation properties.
        columnDefs: [
            {
                "targets": [0, 1, 2, 3], //first column / numbering column disable sort in buttons
                "orderable": false //set not orderable
            },
            {
                "targets": [  ],
                "visible": false,
            }
        ]
    });
    
 
</script>