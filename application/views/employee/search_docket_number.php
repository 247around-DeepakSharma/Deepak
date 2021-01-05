<style>
    #docket_number_table_filter{
        text-align: right;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_docket_number" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Search Docket Number</strong></h3>
            <hr>
            <section class="search_docket_number_div">
                <div class="row">
                    <div class="form-inline">
                        <div class="form-group col-md-6">
                            <label class="radio-inline"><input type="radio" name="search_docket_number_by" value="awb_by_partner"> Sent By Partner</label>
                            <label class="radio-inline"><input type="radio" name="search_docket_number_by" value="awb_by_sf"> Sent By Service Center</label>
                            <label class="radio-inline"><input type="radio" name="search_docket_number_by" value="awb_by_wh"> Sent By Warehouse To partner</label>
                            <label class="radio-inline"><input type="radio" name="search_docket_number_by" value="wh"> MSL</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="form-inline">
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="docket_number" placeholder="Search multiple docket number seperated by comma(,)" style="width:100%;">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="shipment_daterange" name="shipment_daterange" style="width:100%;" placeholder="Select Date">
                        </div>
                        <button class="btn btn-success col-md-2" id="get_docket_number_data">Search</button>
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none; margin: 10px;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section id="search_docket_number_details_div">
                <div class="docket_number_details" style="display: none;">
                    <table  class="table table-response table-bordered" style="padding-top: 20px;" width="100%" id="docket_number_table">
                        <thead id="docket_header">
                            <th>Sr No</th>
                            <th>Booking Id</th>
                            <th>SF Name</th>
                            <th>WH Name</th>
                            <th>Partner Challan Number</th>
                            <th>SF Challan Number</th>
                            <th>WH Challan Number</th>
                            <th>Partner AWB</th>
                            <th>SF AWB</th>
                            <th>WH AWB</th>
                            <th>Part Name</th>
                            <th>Part Code</th>
                            <th>Part Type</th>
                            <th>Consumption</th>
                            <th>Consumption Reason</th>
                            <th>Price</th>
                            <th>GST</th>
                        </thead>
                        <tbody id="docket_number_details_body"></tbody>
                    </table>
                </div>
                <div class="docket_number__not_found_div" style="display: none;">
                    <div class="text_center">
                        <div class="alert alert-danger text-center">
                            <span id="error_msg"></span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!--Modal start-->
        <div id="modal_data" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-body">
                      <div id="open_model"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </div>
          </div>
        </div>
        <!-- Modal end -->
    </div>
</div>
<script>
    var time = moment().format('D-MMM-YYYY');
    $(function() {

        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('#shipment_daterange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
        }

        $('#shipment_daterange').daterangepicker({
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        
        $('input[name="shipment_daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('input[name="shipment_daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        cb(start, end);

    });
    
    $(document).ready(function () {
        $('#get_docket_number_data').click(function () {
            var docket_number = $.trim($("#docket_number").val());
            var search_by = $('input[name=search_docket_number_by]:checked').val();
            var shipment_daterange = $('#shipment_daterange').val();
            if (search_by){
                if(docket_number || shipment_daterange){
                    var from_date = shipment_daterange.split('-')[0];
                    var to_date = shipment_daterange.split('-')[1]; 
                    $('#loader').show();
                    $.ajax({
                        method: 'POST',
                        data: {docket_number: docket_number,search_by:search_by,from_date:from_date,to_date:to_date},
                        url: '<?php echo base_url(); ?>employee/inventory/process_search_docket_number',
                        success: function (response) {
                            var obj = JSON.parse(response);
                            if(obj.status){
                                $("#loader").hide();
                                if(search_by == 'wh'){
                                    create_table_search_msl(obj.msg);
                                }else{
                                    create_table(obj.msg, search_by);
                                }
                            }else{
                                $('#loader').hide();
                                $('.docket_number__not_found_div').show();
                                $('#error_msg').html(obj.msg);
                                $('.docket_number_details').hide();
                            }


                        }
                    });
                }else{
                    alert("Please Enter Either Docket Number or Date Range");
                }
            }else{
                alert("Please Select Either Partner Or Service Center Or Warehouse");
            }
        });
    });
    
    function create_table(table_data, searchBy){
        $("#docket_number_table").dataTable().fnDestroy();
        
        // set header
        var table_head = '<tr><th>Sr No</th>'+'<th>Booking Id</th>'+'<th>SF Name</th>'+'<th>WH Name</th>'+'<th>Partner Challan Number</th>'+'<th>SF Challan Number</th>'+'<th>WH Challan Number</th>'+'<th>Partner AWB</th>'+
        '<th>SF AWB</th>'+'<th>WH AWB</th>'+'<th>Part Name</th>'+'<th>Part Code</th>'+'<th>Part Type</th>'+'<th>Consumption</th>'+'<th>Consumption Reason</th>'+'<th>Part Status</th>'+'<th>Spare ID</th>'+'<th>Price</th>'+'<th>GST</th></tr>';
        
        $('#docket_header').empty();
        $('#docket_header').html(table_head);
        
        var table_body = "";
        $.each(table_data, function (index,val) {
            table_body += "<tr>";
            table_body += '<td>' + (Number(index)+1) +'</td>';
            table_body += "<td><a href='<?php echo base_url();?>employee/booking/viewdetails/"+ val['booking_id'] +"' target='_blank'>"+val['booking_id']+" </a></td>";
            
            if(val['sf_name']){
                table_body += '<td>' + val['sf_name'] +'</td>';
            }else{
                 table_body += '<td></td>';
            }
            
             if(val['wh_name']){
                table_body += '<td>' + val['wh_name'] +'</td>';
            }else{
                 table_body += '<td></td>';
            }
           
            //make partner challan file link
            if(val['partner_challan_file']){
                table_body += "<td><a href='<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/"+ val['partner_challan_file'] +"' target='_blank'>"+val['partner_challan_number']+" </a></td>";
            }else if(val['partner_challan_number']){
                table_body += "<td>"+val['partner_challan_number']+"</td>";
            }else if(val['partner_challan_number'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td></td>';
            }
            
            //make sf challan file link
            if(val['sf_challan_file']){
                table_body += "<td><a href='<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/"+ val['sf_challan_file'] +"' target='_blank'>"+val['sf_challan_number']+" </a></td>";
            }else if(val['sf_challan_number']){
                table_body += "<td>"+val['sf_challan_number']+"</td>";
            }else if(val['sf_challan_number'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td></td>';
            }
            
            
            //make sf challan file link
            if(val['wh_challan_number']){
                table_body += "<td><a href='<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/"+ val['wh_challan_file'] +"' target='_blank'>"+val['wh_challan_number']+" </a></td>";
            }else if(val['wh_challan_number']){
                table_body += "<td>"+val['wh_challan_number']+"</td>";
            }else if(val['wh_challan_number'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td></td>';
            }

            if(val['courier_pic_by_partner']){
                table_body += "<td><a href='<?php echo S3_WEBSITE_URL;?>vendor-partner-docs/"+ val['courier_pic_by_partner'] +"' target='_blank'>"+val['awb_by_partner']+" </a></td>";
            }else if(val['awb_by_partner'] === null){
                table_body += '<td></td>';
            }else if(val['awb_by_partner']){
                table_body += "<td>"+val['awb_by_partner']+"</td>";
            }else{
                table_body += '<td></td>';
            }
            
            
            if(val['awb_by_sf'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['awb_by_sf'] +'</td>';
            }
            
            if(val['awb_by_wh'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['awb_by_wh'] +'</td>';
            }

            if(val['parts_shipped'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['parts_shipped'] +'</td>';
            }


            if(val['part_number'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['part_number'] +'</td>';
            }


            if(val['shipped_parts_type'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['shipped_parts_type'] +'</td>';
            }
            
            /**
             * @modifiedBY Ankit Rajvanshi
             */
            // consumption column
            if(val['is_consumed'] === null){
                table_body += '<td></td>';
            } else if(val['is_consumed'] == 1){
                table_body += '<td>Yes</td>';
            } else {
                table_body += '<td>No</td>';
            }
            // consumption reason column
            if(val['consumed_status'] === null){
                table_body += '<td></td>';
            } else {
                table_body += '<td>' + val['consumed_status'] +'</td>';
            }
             // consumption reason column
            if(val['status'] === null){
                table_body += '<td></td>';
            } else {
                table_body += '<td>' + val['status'] +'</td>';
            }
            if(val['id'] === null){
                table_body += '<td></td>';
            } else {
                table_body += '<td>' + val['id'] +'</td>';
            }

            if(val['price'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['price'] +'</td>';
            }

            if(val['gst_rate'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['gst_rate'] +'</td>';
            }
            
            table_body += "</tr>";
        });
        
        $('.docket_number_details').show();
        $('#docket_number_details_body').html(table_body);
        $('.docket_number__not_found_div').hide();

        if(searchBy == 'awb_by_sf') { 
            $("#docket_number_table").dataTable({
                dom: 'Bfrtip',
                pageLength: 50,
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Export',
                        title: 'docket_number' + time
                    }
                ]
            });    
        } else {
            $("#docket_number_table").dataTable({
                dom: 'Bfrtip',
                pageLength: 50,
                 "columnDefs": [
                    {
                        "targets": [ 13 ],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": [ 14 ],
                        "visible": false
                    }
                ],
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Export',
                        title: 'docket_number_' + time,
                        exportOptions: {
                            columns: [ 0, 1, 2,3,4, 5,6,7,8,9,10,11,12,15,16,17,18]
                        },
                    }
                ]
            });    
        }
    }
    
    function create_table_search_msl(table_data){
        $("#docket_number_table").dataTable().fnDestroy();

        var table_head = '<tr><th>Sr No</th><th>Invoice Id</th><th> AWB Number</th><th>Courier company_name</th><th>Weight</th><th>No. Of Boxes</th><th>Price</th></tr>';
        $('#docket_header').empty();
        $('#docket_header').html(table_head);

        var table_body = "";
        $.each(table_data, function (index,val) {
            table_body += "<tr>";
            table_body += '<td>' + (Number(index)+1) +'</td>';
           
            if(val['invoice_id']){
                table_body += '<td>' + val['invoice_id'] +'</td>';
            }else{
                 table_body += '<td></td>';
            }
            
            if(val['awb_number']){
                table_body += '<td>' + val['awb_number'] +'</td>';
            }else{
                 table_body += '<td></td>';
            }
            
            if(val['company_name']){
                table_body += '<td>' + val['company_name'] +'</td>';
            }else{
                 table_body += '<td></td>';
            }
           


            if(val['actual_weight'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['actual_weight'] +'</td>';
            }
            
            if(val['box_count'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['box_count'] +'</td>';
            }
           
            if(val['courier_charge'] === null){
                table_body += '<td></td>';
            }else{
                table_body += '<td>' + val['courier_charge'] +'</td>';
            }
                       
            table_body += "</tr>";
        });
        
        $('.docket_number_details').show();
        $('#docket_number_details_body').html(table_body);
        $('.docket_number__not_found_div').hide();

        $("#docket_number_table").dataTable({
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [
                {
                    extend: 'csv',
                    text: 'Export',
                    title: 'docket_number' + time,
                }
            ]
        });

    }
    
    $('#docket_number').bind('keydown', function (event) {
        switch (event.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
            break;
            default:
            var regex = new RegExp("^[a-zA-Z0-9,]+$");
            var key = event.key;
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
            break;
        }
    });
    
    
</script>

<style>
    #docket_number_table_filter {
        display:none;
    }
</style>