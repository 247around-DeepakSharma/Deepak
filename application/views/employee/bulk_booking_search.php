<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="container" >
            <form >
                <div class="form-group">
  <label for="sel1">Select Type:</label>
  <select class="form-control" name="select_type" id="select_type">
    <option>Select Type</option>
    <option value="mobile">Mobile</option>
    <option value="booking_id">BookingID</option>
    <option value="order_id">OrderID</option>
  </select>
</div>
               <div class="form-group">
  <label for="comment">Bulk Values</label>
  <textarea class="form-control" rows="5" id="bulk_input" name="bulk_input"></textarea>
</div>
                <div class="form-group">
                    <button type="button" class="btn btn-small btn-success" id="search" onclick="loadData()">Search</button>
</div>
            </form>
                </div>
            <div class="x_panel" style="height: auto;">
                        <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                            <table id="bulk_search_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Booking ID</th>
                                        <th>Partner</th>
                                        <th>City</th>
                                        <th>Service Center</th>
                                        <th>Service</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Capacity</th>
                                        <th>Request Type</th>
                                        <th>Product/Service</th>
                                        <th>Current Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </form>
                    </div>
                   
                </div>
            
            </div>
        </div>
<script>
    function loadData(){
        select_type = document.getElementById("select_type").value;
        bulk_input = document.getElementById("bulk_input").value;
        if(select_type && bulk_input){
            ad_table.ajax.reload( function ( json ) {} );
        }
        else{
           alert("Please provide Bulk values and Select Type Both");
           return false;
        }
    }
var ad_table;
        ad_table = $('#bulk_search_table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": baseUrl+"/employee/booking/get_bulk_search_result_view",
                "type": "POST",
                "data": function(d){
                    d.select_type = $("#select_type option:selected").val();
                    d.bulk_input = $('textarea#bulk_input').val();
                 }
            }
        });
    </script>