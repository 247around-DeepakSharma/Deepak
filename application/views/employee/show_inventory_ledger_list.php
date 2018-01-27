<div id="page-wrapper" >
    <div>
        <h3>Inventory Ledger Details</h3>
        <hr>
        <div class="stocks_table">
            <table class="table table-responsive table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Receiver Name</th>
                        <th>Receiver Type</th>
                        <th>Sender Name</th>
                        <th>Sender Type</th>
                        <th>Part Name</th>
                        <th>Part Description</th>
                        <th>Quantity</th>
                        <th>Booking Id</th>
                        <th>Invoice Id</th>
                        <th>Order Id</th>
                        <th>Agent Name</th>
                        <th>Agent Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brackets as $key => $value){?>
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td>
                            <a href="javascript:void(0);" onclick="get_vendor_stocks('<?php echo $value['receiver_entity_id']?>','<?php echo $value['receiver_entity_type']?>')">
                                <?php echo $value['receiver'];?>
                            </a>
                        </td>
                        <td><?php echo $value['receiver_entity_type'];?></td>
                        <td><?php echo $value['sender'];?></td>
                        <td><?php echo $value['sender_entity_type'];?></td>
                        <td><?php echo $value['part_name'];?></td>
                        <td><?php echo $value['description'];?></td>
                        <td><?php echo $value['quantity'];?></td>
                        <td>
                            <a href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>">
                                <?php echo $value['booking_id'];?>
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="get_invoice_data('<?php echo $value['invoice_id']; ?>')">
                                <?php echo $value['invoice_id'];?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo base_url();?>employee/inventory/show_brackets_order_history/<?php echo $value['order_id']; ?>">
                                <?php echo $value['order_id'];?>
                            </a>
                        </td>
                        <td><?php echo $value['agent_name'];?></td>
                        <td><?php echo $value['agent_type'];?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
        </div>
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
    <script>
        
        function get_vendor_stocks(entity_id,entity_type){
            $.ajax({
                type:'POST',
                url: '<?php echo base_url();?>employee/inventory/get_inventory_stock',
                data:{entity_id:entity_id,entity_type:entity_type},
                success:function(response){
                    $("#open_model").html(response);   
                    $('#modal_data').modal('toggle');
                }
            });
        }
        
        function get_invoice_data(invoice_id){
            if (invoice_id){
                    $.ajax({
                        method: 'POST',
                        data: {invoice_id: invoice_id},
                        url: '<?php echo base_url(); ?>employee/accounting/search_invoice_id',
                        success: function (response) {
                            $("#open_model").html(response);   
                            $('#modal_data').modal('toggle');

                        }
                    });
                }else{
                    console.log("Contact Developers For This Issue");
                }
        }
    </script>
</div>