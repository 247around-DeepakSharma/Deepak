<style>
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    #inventory_part_and_model_mapping_table_filter{
        text-align: right;
    }
    .x_title span {
        color: #333;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            
            <?php if(!empty($inventory_details)) { ?>
            <div class="x_panel">
                <div class="x_title">
                    <h3>Spare Part Details For Model Number <span id="model_name"><strong><?php echo array_unique(array_column($inventory_details, 'model_number'))[0] ;?></strong></span></h3>
                    
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_part_and_model_mapping">
                        <table id="inventory_part_and_model_mapping_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>HSN</th>
                                    <th>Basic Price</th>
                                    <th>GST Rate</th>
                                    <th>Total Price</th>
                                    <th>Vendor Margin</th>
                                    <th>Around Margin</th>
                                     <th>Customer Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach ($inventory_details as $value) { ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td><?php echo $value['services']; ?></td>
                                    <td><?php echo $value['type']; ?></td>
                                    <td><?php echo $value['part_name']; ?></td>
                                    <td><?php echo $value['part_number']; ?></td>
                                    <td><?php echo $value['hsn_code']; ?></td>
                                    <td><?php echo $value['price']; ?></td>
                                    <td><?php echo $value['gst_rate']; ?></td>
                                    <td><?php echo number_format((float)($value['price'] + ($value['price'] * ($value['gst_rate']/100))), 2, '.', ''); ?></td>
                                    <td><?php echo $value['oow_vendor_margin']; ?>%</td>
                                    <td><?php echo $value['oow_around_margin']; ?>%</td>


                    <?php    
                    $customertot = number_format((float)($value['price'] + ($value['price'] * ($value['gst_rate']/100))), 2, '.', '');
                    $customertot = number_format((float)$customertot+($customertot*($value['oow_vendor_margin']+$value['oow_around_margin'])/100), 2, '.', '');

                                      ?>


                                    <td><?php echo $customertot; ?></td>
                                    
                                </tr>
                                <?php $sn++;} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php }else { ?> 
            <div class="alert alert-danger text-center">
                <p>No Details Found</p>
            </div>
            <?php } ?>
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
    var time = moment().format('D-MMM-YYYY-H-mm-A');
    var model_name = $('#model_name').text();
    $('#inventory_part_and_model_mapping_table').DataTable({
        "dom": 'lBfrtip',
        "lengthMenu": [[50,100, -1], [50, 100,"All"]],
        "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0,1,2,3,4,5,6,7,8,9,10,11]
                    },
                    title: 'parts_used_in_model_'+model_name+time,
                         exportOptions: { 
                         columns: [0,1,2,3,4,5,6,7,8,9,10,11],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                },
            ],
    });
</script>
<style>
    .dataTables_length {
    width: 100% !important;
    float: left;
}
</style>