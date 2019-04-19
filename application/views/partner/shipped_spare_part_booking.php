<?php
if ($this->uri->segment(3)) {
    $sn_no = $this->uri->segment(3) + 1;
} else {
    $sn_no = 1;
}
?>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                     <h2>Spare Parts Shipped By <?php echo $this->session->userdata('partner_name'); ?>, Waiting For Confirmation From SF</h2>
                    <div class="pull-right"><a style="background: #2a3f54;border-color: #2a3f54;" href="<?php echo base_url(); ?>employee/partner/download_spare_part_shipped_by_partner_not_acknowledged"  class="btn btn-sm btn-primary">Download</a></div>
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
                    <table class="table table-bordered table-hover table-striped" id="shipped_spare_part_table">
                        <thead>
                            <tr>
                                <th class="text-center">S.N</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">Customer Name</th>
                                <th class="text-center">Shipped Parts</th>
                                <th class="text-center">Parts Number</th>
                                <th class="text-center">Courier Name</th>
                                <th class="text-center">AWB</th>
                                <th class="text-center">Challan</th>
                                <th class="text-center">Shipped Date</th>
                                <th class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        
                    </table>
                    <div class="custom_pagination" style="margin-left: 16px;" > 
                <?php if(isset($links)) { echo $links; } ?>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#state_search').select2();
        shipped_spare_part_table = $('#shipped_spare_part_table').DataTable({
            "processing": true,
            "language":{ 
                "processing": "<center><img id='loader_gif_title' src='<?php echo base_url(); ?>images/loadring.gif'></center>",
            },
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/partner/get_shipped_spare_waiting_for_confirmation/",
                "type": "POST",
                "data": function(d){
                    d.booking_id =  $('#booking_id_search').val();
                    d.state =  $('#state_search').val();
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,2,6,8,9], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],  
            "deferRender": true 
        });
    });
    function booking_search(){
             shipped_spare_part_table.ajax.reload();
        }
    </script>
    <style>
        #shipped_spare_part_table_filter{
      display: none;
}
#shipped_spare_part_table_processing{
    border:none !important;
    background-color: transparent !important;
}
        </style>
