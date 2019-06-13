<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="container" >
                <form method="POST" action="<?php echo base_url();?>employee/booking/download_booking_bulk_search_snapshot">
                    <div class="form-group">
                        <input type="submit" class="btn btn-small btn-success" id="download" style="float: right;margin: 10px 0px;" value="Download CSV" onsubmit="loadData('download')">
                        <select class="form-control" name="select_type" id="select_type">
                            <option value="">Select Type</option>
                            <option value="mobile">Mobile</option>
                            <option value="booking_id">BookingID</option>
                            <option value="order_id">OrderID</option>
                        </select>
                    </div>
                    <div class="form-group" id="partner_holder">
                        <select class="form-control" name="partner" id="partner">
                            <option value="option_holder">Select Partner</option>
                            <?php
                                foreach($partnerArray as $partnerID=>$partnerName){
                                    echo ' <option value="'.$partnerID.'">'.$partnerName.'</option>';
                                }
                                ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" id="bulk_input" name="bulk_input" placeholder="Values"></textarea>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="is_unit_details">With Unit Details</label>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-small btn-success" id="search" onclick="loadData()">Search</button>
                    </div>
                </form>
            </div>
            <div class="x_panel" style="height: auto;">
                <table id="bulk_search_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Booking ID</th>
                            <th>OrderID</th>
                            <th>Contact No.</th>
                            <th>Partner</th>
                            <th>City</th>
                            <th>Service Center</th>
                            <th>Service</th>
                            <th>Current Status</th>
                            <th>Internal Status</th>
                            <th>Purchase Date</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Capacity</th>
                            <th>Request Type</th>
                            <th>Product/Service</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function loadData(is_download = null){
        select_type = document.getElementById("select_type").value;
        bulk_input = document.getElementById("bulk_input").value;
        bulkInputArray = bulk_input.replace( /\n/g, " " ).split( " " );
        if(bulkInputArray.length>100){
            alert("Search Input Should be less then 100");
        }
        else{
        if(select_type && bulk_input){
            if(is_download){
                return true;
            }
            else{
                ad_table.ajax.reload( function ( json ) {} );
           }
        }
        else{
           alert("Please provide Bulk values and Select Type Both");
           return false;
        }
    }
    }
    var ad_table;
        ad_table = $('#bulk_search_table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 100,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "deferLoading": 0,
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'bank_transactions',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9,10],
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
            "ajax": {
                "url": baseUrl+"/employee/booking/get_bulk_search_result_view",
                "type": "POST",
                "data": function(d){
                    d.select_type = $("#select_type option:selected").val();
                    d.bulk_input = $('textarea#bulk_input').val();
                    d.partner_id = $("#partner option:selected").val();
                    d.is_unit_details = $('input[name=is_unit_details]:checked').val(); 
                 }
            }
        });
</script>