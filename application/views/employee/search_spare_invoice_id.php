<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_invoice_id" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Search Tagged Parts by Spare Invoice</strong></h3>
            <hr>
            <section class="fetch_invoice_id">
                <div class="row">
                    <div class="form-inline">
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="invoice_id" placeholder="Spare Invoice Id" style="width:100%;">
                        </div>
                        <button class="btn btn-success col-md-2" id="get_invoice_id_data">Search</button>
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section id="spare_data">
                <div class="spare_details" style="display: none;">
                    <h3> Spare Part Tagged for Invoice: <span id="spare_invoice_id"></span> <span id="invoice_details_loader" style="display: none;"><i class='fa fa-spinner fa-spin'></i></span></h3>
                    <table  class="table table-response table-bordered" style="padding-top: 20px;">
                        <thead>
                            <th>Sr No</th>
                            <th>Sender Name</th>
                            <th>Receiver Name</th>
                            <th>Part Number</th>
                            <th>Basic Price</th>
                            <th>GST Rate</th>
                            <th>HSN Code</th>
                            <th>Quantity</th>
                            <th>Booking Id</th>
                            <th>Create Date</th>
                        </thead>
                        <tbody id="spare_table_body"></tbody>
                    </table>
                </div>
                <div class="spare_not_found_div" style="display: none;">
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
    invoice_link_html = '';
    $(document).ready(function () {
        $('#get_invoice_id_data').click(function () {
            var invoice_id = $.trim($("#invoice_id").val());
            if (invoice_id){
                $('#loader').show();
                $.ajax({
                    method: 'POST',
                    data: {invoice_id: invoice_id},
                    url: '<?php echo base_url(); ?>employee/inventory/search_spare_tagged_by_invoice_id',
                    success: function (response) {
                        //console.log(response);
                        var obj = JSON.parse(response);
                        if(obj.status){
                            $('#loader').hide();
                            invoice_link_html = "<a href = '#' onclick = get_invoice_data('"+invoice_id+"')>"+invoice_id+"</a>";
                            $('#spare_invoice_id').html(invoice_link_html);
                            create_spare_table(obj.msg);
                        }else{
                            $('#loader').hide();
                            $('.spare_not_found_div').show();
                            $('#error_msg').html(obj.msg);
                            $('.spare_details').hide();
                        }
                        

                    }
                });
            }else{
                alert("Please Enter Invoice");
            }
        });
    });
    
    function create_spare_table(table_data){
        var table_body = "";
        $.each(table_data, function (index,val) {
            table_body += "<tr>";
            table_body += '<td>' + (Number(index)+1) +'</td>';
            table_body += "<td>"+ val['sender'] +"</td>";
            table_body += "<td>"+ val['receiver'] +"</td>";
            table_body += "<td>"+ val['description'] +"</td>";
            table_body += "<td>"+ val['basic_price'] +"</td>";
            table_body += "<td>"+ val['gst_rate'] +"</td>";
            table_body += "<td>"+ val['hsn_code'] +"</td>";
            table_body += "<td>"+ val['qty'] +"</td>";
            table_body += "<td><a href='<?php echo base_url();?>employee/booking/viewdetails/"+ val['booking_id'] +"' target='_blank'>"+val['booking_id']+" </a></td>";
            table_body += "<td>"+ val['create_date'] +"</td>";
            table_body += "</tr>";
        });
        
        $('.spare_details').show();
        $('#spare_table_body').html(table_body);
        $('.spare_not_found_div').hide();
    }
    
    function get_invoice_data(invoice_id){
        if (invoice_id){
            $('#invoice_details_loader').show();
            $.ajax({
                method: 'POST',
                data: {invoice_id: invoice_id},
                url: '<?php echo base_url(); ?>employee/accounting/search_invoice_id',
                success: function (response) {
                    $('#invoice_details_loader').hide();
                    $("#open_model").html(response);   
                    $('#modal_data').modal('toggle');
                }
            });
        }else{
            console.log("Contact Developers For This Issue");
        }
    }
</script>