<style>
    #engineer_datatable_filter{
        text-align: right;
    }
</style>
<script>
    function outbound_call(phone_number){
        var confirm_call = confirm("Call Vendor ?");
       
        if (confirm_call == true) {
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                }
            });
        } else {
            return false;
        }

    }
</script>
<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h1>Engineer Details</h1>
        <br>
        <div style="margin-bottom: 20px;">
            <a href="<?php echo base_url();?>employee/vendor/add_engineer"><input class="btn btn-primary" type="Button" value="Add Engineer"></a>
        </div>
        <?php if($this->session->userdata('update_success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('update_success') . '</strong>
                    </div>';
                    }
        ?>
        <table  class="table table-striped table-bordered" id="engineer_datatable">
            <thead>
            <tr>
          	<th>S.No</th>
                <th>Service Center</th>
          	<th>Name</th>
                <th>Appliances</th>
          	<th>Mobile</th>
          	<th>Alternate Mobile Number</th>
          	<th>ID Proof</th>
                <th>Status</th>
                <th>Edit</th>
                <!--<th>Delete</th>-->
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
    engineer_datatable = $('#engineer_datatable').DataTable({
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
                    title: 'engineers',
                    exportOptions: {
                       columns: [1,2,3,4,5,6],
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
            url: "<?php echo base_url(); ?>employee/engineer/get_engineer_details",
            type: "POST",
            data: {}
        },
        //Set column definition initialisation properties.
        columnDefs: [
            {
                "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8], //first column / numbering column
                "orderable": false //set not orderable
            }
        ]
    });
    
 
</script>