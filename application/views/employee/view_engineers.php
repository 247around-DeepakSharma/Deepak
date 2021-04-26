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
            <a href="<?php echo base_url();?>employee/engineer/download_all_engineers"><input class="btn btn-primary" type="Button" value="Download All Engineers"></a>
             
            <button type="button" class="btn btn-success pull-right">Installed <span class="badge"><?php echo count($installs); ?></span></button>
            <button type="button" class="btn btn-danger pull-right" style="margin-right: 17px;">UnInstalled <span class="badge"><?php echo count($uninstalls); ?></span></button>

            <button type="button" class="btn btn-warning pull-right" style="margin-right: 17px;">Never Used <span class="badge"><?php echo count($neverinstalled); ?></span></button>

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
                <th>Service Center Name</th>
                <th>State</th>
                <th>City</th>
          	<th>Engineer Name</th>
                <th>Appliances</th>
          	<th>Mobile</th>
          	<th>Alternate Mobile Number</th>
          	<th>ID Proof</th>
                <th>Create Date</th>
                <th>Verified</th> <!-- Remove duplicate coloumn -->
                <th>Enable/Disable</th>  <!-- Change Column Name -->
                <th>Edit</th>
                <th>Status</th> <!-- Show Status Active/Inactive -->
                <th>Install/UnInstall</th> <!-- Show  Install/Uninstall -->
                <th>History</th> <!-- Show  engg history -->
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
        lengthMenu: [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
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
                       columns: [1,2,3,4,5,6,7,9,10,13,14],
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
                "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,10,11,12,13,14,15], //first column / numbering column disable sort in buttons
                "orderable": false //set not orderable
            },
            {
                "targets": [  ],
                "visible": false,
            }
        ]
    });
    
    function verify_engineer(engineer_id, varified_status){
        var confirm_varification = confirm("Are you sure to varify this engineer");
        if(confirm_varification){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url();?>employee/vendor/verify_engineer',
                data: {engineer_id: engineer_id,varified_status: varified_status},
                success: function (response) {
                    if(response){
                        location.reload();
                    }
                    else{
                        alert("Error in varifying engineer, Please contact tech team");
                    }
                }
            });
        }
    }
</script>
