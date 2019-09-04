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
    <div class="row">
<?php } ?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
                   <h2>Defective Spares Pending on SF </h2>
            <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_sf_needs_to_send_parts"  class="btn btn-sm btn-primary">Download</a></div>
                    <div class="right_holder" style="float:right;margin-right:10px;">
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
            <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-striped" id="sf_needs_to_send_table">
                    <thead>
                        <tr>
                            <th class="text-center">S.N</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Model Number</th>
                            <th class="text-center">Date Of Purchase</th>
                            <th class="text-center">Spare Details</th>
                            <th class="text-center">Parts Number</th>   
                            <th class="text-center">Quantity</th> 
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB</th>
                            <th class="text-center">Challan</th>
                            <th class="text-center">Age</th>
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
<script type="text/javascript">
    
    $(document).ready(function () {
        $('#state_search').select2();
        sf_needs_to_send_table = $('#sf_needs_to_send_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_sf_needs_to_send_spare/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search').val();
                    d.state =  $('#state_search').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,2,3,4,7,9,10], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search(){
             sf_needs_to_send_table.ajax.reload();
        }
 
function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
}

</script>
    <style>
#sf_needs_to_send_table_filter{
      display: none;
}
#sf_needs_to_send_table_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>