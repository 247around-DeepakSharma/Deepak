<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                   <h2>Received Spares By <?php echo $this->session->userdata('partner_name') ?></h2>
                   <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>partner/download_received_spare_by_partner"  class="btn btn-sm btn-primary" id="download_excel" onmouseover="rowcount()">Download</a></div>
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
                    <table class="table table-bordered table-hover table-striped" id="approved_defective_parts_table">
                        <thead>
                            <tr>
                                <th class="text-center">S.N</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Received Spare Parts</th>
                                <th class="text-center">Parts Number</th>
                                <th class="text-center">Received Date</th>
                                <th class="text-center">AWB</th>
                                <th class="text-center">Courier Name</th>
                                <th class="text-center">Challan</th>
                                <th class="text-center">SF Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
       $(document).ready(function () {
        $('#state_search').select2();
        approved_defective_parts_table = $('#approved_defective_parts_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/received_defactive_parts_by_partner/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search').val();
                    d.state =  $('#state_search').val();
                 }
            },
            "columnDefs": [
                {
                   "targets": [0,2,7,8,9], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });        
    });
    function booking_search(){
             approved_defective_parts_table.ajax.reload();
        }
        
    function rowcount(){
        var total_rows = $('#approved_defective_parts_table >tbody >tr').length;
        if(total_rows < 2){
             $("#download_excel").attr("disabled", true);
        }
       
    }
    
    
    </script>
        <style>
     #approved_defective_parts_table_filter{
      display: none;
}
#approved_defective_parts_table_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>
