<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<div class="right_col" role="main">
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel" style="height: auto;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title">
                    <h2>Review Order</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <div class="approved">
                                <div class="btn btn-success" class="approved_reject_all_order" onclick="process_approval_rejection('approved');" data-value="approved">Approve Order</div>
                            </div>
                        </li>
                        <li>
                            <div class="approved">
                                <a href="<?php echo base_url()?>buyback/buyback_process/download_review_orders" class="btn btn-success" class="" data-value="Download">Download Review Orders</a>
                            </div>
                        </li>
                        <!--                            <li>
                            <div class="reject">
                                <div class="btn btn-danger" class="approved_reject_all_order" onclick="process_approval_rejection('rejected');" data-value="rejected">Reject Order</div>
                            </div>
                            </li>-->
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="min-height:500px!important;">
                    <div class="review_order">
                        <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Order ID</th>
                                    <th>Tracking ID</th>
                                    <th>CP Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
<!--                                    <th>Physical Condition</th>
                                    <th>Working condition</th>-->
                                    <th>CP Claimed Price</th>
                                    <th>Internal Status</th>
                                    <th>Remarks</th>
                                    <th>View Image</th>
                                    <th>Reject</th>
                                    <th>Approve</th>
                                                                        <th>
                                        Select Order 
                                        <input type="checkbox" id="check_all_row_to_approve_reject">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="reject_approve_order" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span id="model_title"></span></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id ="approve_form" action="#"  method="POST" >
                    <div class="col-md-12" >
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="Claimed Price">Claimed Price *</label>
                                <input type="text" class="form-control"  id="order_cp_claimed_price" placeholder="Enter Claimed Price" name="claimed_price" value = "" >
                                <input type="hidden" value="" id="partner_order_id">
                                <input type="hidden" value="" id="order_status">
                                <input type="hidden" value="" id="action_type">
                            </div>
                        </div>
                         
                       
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Remarks *</label>
                                <textarea class="form-control" id="approve_reject_remarks" placeholder="Enter Remarks"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 " id="amazone_discount_holder" style="display:none;">
                            <div class="form-group col-md-12  ">
                                <label><input type="checkbox" value="" checked="" id="is_amazon_discount"><span>Is Amazon Discount Applicable</span></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" onclick="approve_reject_order_manually()">Submit</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var table;
    
    $(document).ready(function () {
    
        //datatables
        table1 = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "lengthMenu": [[100, 200, 500,-1], [100, 200, 500, "All"]],
            "pageLength": 500,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_review_order_details",
                "type": "POST",
                "data": {"status": 2}
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,8,9,10,11,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ]
        });
    });
    
    
</script>
<script>
    var allVals = [];
    var allCurrentStatus = [];
    var all_cp_claimed_price = [];
    var type = '';
    
    $('#check_all_row_to_approve_reject').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });
    
    function process_approval_rejection(process_type){
         $(".check_single_row:checked").each(function () {
            allVals.push($(this).attr('data-id'));
            allCurrentStatus.push($(this).attr('data-status'));
            all_cp_claimed_price.push($(this).attr('data-cp_claimed_price'));
        });  
        if (allVals.length <= 0)
        {
            showConfirmDialougeBox("Please select At Least One Order",'warning');
        }
        else {
            type = process_type;
            showConfirmDialougeBox("Are you sure you want to approve ?",'ajax_request');
        }
    }
    
    function showConfirmDialougeBox(title,msg_type){
        if(msg_type === 'warning'){
            swal({
                title: title,
                type: "warning"
            });
        }else{
            swal({
                title: title,
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            },
            function(){
                var join_selected_values = allVals.join(",");
                var join_selected_status = allCurrentStatus.join(",");
                var join_selected_cp_claimed_price = all_cp_claimed_price.join(",");
                 ajax_call(join_selected_values,join_selected_status,join_selected_cp_claimed_price,type,'');
            });
        }
    }
    
    function open_reject_approve_model(partner_order_id,internal_status,cp_claimed_price, title, type){
        $('#partner_order_id').val(partner_order_id);
        $('#order_status').val(internal_status);
        $('#order_cp_claimed_price').val(cp_claimed_price);
        $('#action_type').val(type);
        $('#model_title').text(title);
        $('#approve_reject_remarks').val("");
        $('#reject_approve_order').modal('toggle');
        if(type === 'approved'){
            $("#amazone_discount_holder").show();
        }
    }
    
    
    function approve_reject_order_manually(){
        var partner_order_id = $('#partner_order_id').val();
        var cp_claimed_price = $('#order_cp_claimed_price').val();
        var remarks = $('#approve_reject_remarks').val();
        var status = $('#order_status').val();
        var action_type = $('#action_type').val();
        var amazon_discount = $('#is_amazon_discount').is(":checked");
        if(remarks){
            ajax_call(partner_order_id,status,cp_claimed_price,action_type,remarks,amazon_discount);
        }else{
            alert('Please Enter Remarks');
        }
    }
    
    function ajax_call(order_id,status,cp_claimed_price,type,remarks,amazon_discount){
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>buyback/buyback_process/approve_reject_bb_order",
            cache: false,
            data: {'order_ids':order_id,'status':status,'cp_claimed_price':cp_claimed_price,'type':type,'remarks':remarks,'amazon_discount':amazon_discount},
            success: function (response){
                table1.ajax.reload(null, false);
                
                var v = $('#reject_approve_order').is(':visible');
                if(v){
                   $('#reject_approve_order').modal('toggle'); 
                }
                swal("Thanks!", "Order updated successfully!", "success");
                
            }
        });
    }
    
</script>