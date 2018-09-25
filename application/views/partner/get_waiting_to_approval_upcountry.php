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
            <h2>Approve/Reject Upcountry Charges</h2>
             <div class="pull-right"><a style="background: #2a3f54; border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_waiting_upcountry_bookings"  class="btn btn-sm btn-primary">Download</a></div>
                    <span style="color:#337ab7" id="messageSpare"></span></div>
                    <div class="right_holder" style="float:right;margin-right:10px;margin-top: -16px;">
                            <select class="form-control " id="state_search" style="border-radius:3px;" onchange="booking_search()">
                    <option value="">States</option>
      <?php
      foreach($states as $state){
          ?>
      <option value="<?php echo $state['state'] ?>"><?php echo $state['state'] ?></option>
      <?php
      }
      ?>
  </select>            
</div>
            <div class="clearfix"></div>
                    
        </div>
    <input type="text" id="booking_id_search" onchange="booking_search()" style="float: right;margin-bottom: -32px;border: 1px solid #ccc;padding: 5px;z-index: 100;position: inherit;">
        <div class="x_content">
            <table class="table table-bordered table-hover table-striped" id="waiting_upcountry_charges_table" style=" z-index: -1;position: static;">
                <thead>
                    <tr>
                        <th class="text-center">S.N</th>
                        <th class="text-center">Booking ID</th>
                        <th class="text-center">Call Type</th>
                        <th class="text-center">Customer Name</th>
                        <th class="text-center">Appliance</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Capacity</th>
                        <th class="text-center">Address</th>
                        <th class="text-center">Age</th>
                        <th class="text-center">Upcountry Distance</th>
                        <th class="text-center">Upcountry Charges</th>
                        <th class="text-center">Action</th>
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
        $('#state_search').select2();
        waiting_upcountry_charges_table = $('#waiting_upcountry_charges_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_waiting_upcountry_charges/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search').val();
                    d.state =  $('#state_search').val();
                 }
            },
            "columnDefs": [
            {
                    "targets": [0,2,3,8,9,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search(){
             waiting_upcountry_charges_table.ajax.reload();
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
        </style>