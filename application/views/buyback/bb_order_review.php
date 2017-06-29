<script src="<?php echo base_url(); ?>js/base_url.js"></script>

<div class="right_col" role="main">

    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2><i class="fa fa-bars"></i> Review Order</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="min-height:500px!important;">
                        <div class="review_order">

                            <table id="datatable1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Order ID</th>
                                        <th>CP Name</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Physical Condition</th>
                                        <th>Working condition</th>
                                        <th>Internal Status</th>
                                        <th>Remarks</th>
                                        <th>View Image</th>
                                        <th>Approved <input type="checkbox" id="check_all_row_to_approve"></th>

                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                        
                        <div class="approved">
                            <div class="btn btn-success" id="approved_all_order">Approve Order</div>
                        </div>

                    </div>

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
                "pageLength": 50,
                // Load data for the table's content from an Ajax source
                "ajax": {
                    "url": "<?php echo base_url(); ?>buyback/buyback_process/get_bb_review_order_details",
                    "type": "POST",
                    "data": {"status": 2}
                },
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0,8,9,10], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
                ],
            });
        });


    </script>
    
<script>
    $('#check_all_row_to_approve').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".check_single_row").prop('checked', true);
        }
        else
        {
            $(".check_single_row").prop('checked', false);
        }
    });

    $('#approved_all_order').on('click', function () {
        var allVals = [];
        var allCurrentStatus = [];
        $(".check_single_row:checked").each(function () {
            allVals.push($(this).attr('data-id'));
            allCurrentStatus.push($(this).attr('data-status'));
        });  
        if (allVals.length <= 0)
        {
            alert("Please select At Least One Order");
        }
        else {
            //$("#loading").show(); 
            WRN = "Are you sure you want to approve this Order ?";
            var check = confirm(WRN);
            if (check === true) {
                var join_selected_values = allVals.join(",");
                var join_selected_status = allCurrentStatus.join(",");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>buyback/buyback_process/approve_all_bb_order",
                    cache: false,
                    data: {'order_ids':join_selected_values,'status':join_selected_status},
                    success: function (response)
                    {
                        location.reload();
                    }
                });
            }
        }
    });
</script>