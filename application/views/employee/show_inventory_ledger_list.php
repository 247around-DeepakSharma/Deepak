<div id="page-wrapper" >
    <div>
        <h3>Inventory Ledger Details </h3>
        <hr>
        <div class="stocks_table">
            <table class="table table-responsive table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>                        
                        <th>Sender Name</th>
                        <th>Receiver Name</th>
                        <th>Spare Part Name</th>
                        <th>Spare Part Number</th>
                        <th>Spare Quantity</th>
                        <th>Status</th>
                        <th>Booking Id</th>
                        <th>Invoice Id</th>
                        <th>Order Id</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brackets as $key => $value){?>
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo $value['sender'];?></td>
                        <td>
<!--                            <a href="javascript:void(0);" onclick="get_vendor_stocks('<?php echo $value['receiver_entity_id']?>','<?php echo $value['receiver_entity_type']?>')"> </a>-->
                            <?php echo $value['receiver'];?>
                        </td>                        
                        <td><?php echo $value['part_name'];?></td>
                        <td><?php echo $value['part_number'];?></td>
                        <td><?php echo $value['quantity'];?></td>
                       <td><?php  echo ($value['is_wh_ack']==1)  ? "Acknowledged" : "Not Acknowledged"; ?></td>
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
                        <td><?php echo date('d F Y H:i:s', strtotime($value['create_date'])) ; ?></td>
                    </tr>
                    <?php }?>
<!--                    <tr>
                        <th><b>Total Count <span class="badge"><i class="fa fa-info" title="Spare count calculated only for spare shipped by partner to wh and wh to sf only"></i></span></b></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><b><?php if(isset($total_spare) && !empty($total_spare)) { echo $total_spare[0]['total_spare_from_ledger']; }?></b></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>-->
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