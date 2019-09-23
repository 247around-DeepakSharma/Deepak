<?php if(empty($is_ajax)) { ?>
<div class="right_col" role="main">
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
        }
        ?>
        <?php
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('error') . '</strong>
                   </div>';
        }
        ?>
    <div class="row">
<?php } ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title" style="border-bottom: none;">
            <h2>Approve/Reject NRN Bookings</h2>
            <div class="clearfix"></div>
                    
        </div>
    
        <div class="x_content">
            <table class="table table-bordered table-hover table-striped" id="nrn_table" style=" z-index: -1;position: static;">
                <thead>
                    <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Appliance</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Part Request Age(Days)</th>
                                    <th class="text-center">Required Parts</th>
                                    <th class="text-center">Parts Number</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Model Number</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">State</th>
                                    <th class="text-center">Problem Description</th>
                                    <th class="text-center">NRN Remark</th>
                                    <th class="text-center">Approval File</th>
                                    <th data-sortable="false" class="text-center"> NRN Status</th>

                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>



<?php if(empty($is_ajax)) { ?> 
    </div>
</div>
<?php } ?>        
<div class="clearfix"></div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<script>
    $(document).ready(function () {
        nrn_table = $('#nrn_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
             "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_nrn_approval_table",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search').val();
                    d.state =  $('#state_search').val();
                 }
            },
             "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'NRN Approvals', 
                    exportOptions: { 
                       columns: [0,1,2,3,4,5,6,7,8,9,10,11,12],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "columnDefs": [
            {
                    "targets": [0,2,3,8,9], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search(){
             nrn_table.ajax.reload();
             }




    </script>
    <style>
.dropdown-backdrop{
    display: none;
}
#waiting_upcountry_charges_table_filter{
      display: none;
}
#waiting_upcountry_charges_table_processing{
    border:none !important;
    background-color: transparent !important;
}
.dataTables_length {
    width: 8% !important;
    float: left;
}
        </style>
